<?php

namespace App\Services;

use App\Services\Firebase\ServiceAccountTokenProvider;
use App\Support\FirebaseCredentials;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class FirebaseCredentialManager
{
    public function __construct(
        private IntegrationSettingsService $settings,
        private ServiceAccountTokenProvider $tokenProvider,
        private AuditLogger $audit
    ) {
    }

    public function activePath(): string
    {
        return FirebaseCredentials::storagePath();
    }

    public function backupDir(): string
    {
        return storage_path('app/private/firebase/backup');
    }

    public function androidConfigPath(): string
    {
        return storage_path('app/private/firebase/google-services.json');
    }

    public function validateServiceAccount(array $data, bool $enforceProjectMatch = true): array
    {
        $errors = [];

        foreach (['project_id', 'client_email', 'private_key'] as $required) {
            if (empty($data[$required])) {
                $errors[$required] = 'الحقل مطلوب.';
            }
        }

        if (empty($errors['client_email'] ?? null) && !filter_var($data['client_email'] ?? '', FILTER_VALIDATE_EMAIL)) {
            $errors['client_email'] = 'البريد الإلكتروني غير صالح.';
        }

        if (empty($errors['private_key'] ?? null)) {
            $key = (string) $data['private_key'];
            if (!str_contains($key, 'BEGIN PRIVATE KEY') || !str_contains($key, 'END PRIVATE KEY')) {
                $errors['private_key'] = 'المفتاح الخاص غير صالح.';
            }
        }

        if (empty($errors['project_id'] ?? null)) {
            $projectId = (string) $data['project_id'];
            if (!preg_match('/^[a-z0-9-]{6,30}$/', $projectId)) {
                $errors['project_id'] = 'معرّف المشروع غير صالح.';
            }
        }

        $configuredProject = (string) $this->settings->get('firebase.project_id', '');
        if ($enforceProjectMatch && $configuredProject !== '' && empty($errors['project_id']) && $configuredProject !== (string) $data['project_id']) {
            $errors['project_id'] = 'معرّف المشروع لا يطابق المشروع المعتمد (' . $configuredProject . ').';
        }

        return [
            'ok' => empty($errors),
            'errors' => $errors,
            'project_id' => (string) ($data['project_id'] ?? ''),
            'client_email' => (string) ($data['client_email'] ?? ''),
        ];
    }

    public function validateAndroidConfig(array $data): array
    {
        $errors = [];
        $projectId = (string) ($data['project_id'] ?? '');
        $client = $data['client'][0] ?? null;
        $packageName = (string) (data_get($client, 'client_info.android_client_info.package_name') ?? '');
        $appId = (string) (data_get($client, 'client_info.mobilesdk_app_id') ?? '');

        if ($projectId === '') {
            $errors['project_id'] = 'معرّف المشروع مفقود.';
        }
        if ($packageName === '') {
            $errors['package_name'] = 'اسم الحزمة (package_name) مفقود.';
        }
        if ($appId === '') {
            $errors['mobilesdk_app_id'] = 'معرّف التطبيق (mobilesdk_app_id) مفقود.';
        }

        $configuredProject = (string) $this->settings->get('firebase.project_id', '');
        if ($configuredProject !== '' && $projectId !== '' && $configuredProject !== $projectId) {
            $errors['project_id'] = 'معرّف المشروع لا يطابق المشروع المعتمد (' . $configuredProject . ').';
        }

        return [
            'ok' => empty($errors),
            'errors' => $errors,
            'project_id' => $projectId,
            'package_name' => $packageName,
            'mobilesdk_app_id' => $appId,
        ];
    }

    public function stageServiceAccount(UploadedFile $file): string
    {
        $dir = storage_path('app/private/firebase/staging');
        File::ensureDirectoryExists($dir, 0750, true);

        $path = $dir . '/service-account-' . Str::random(32) . '.json';
        File::put($path, (string) file_get_contents($file->getRealPath()));
        @chmod($path, 0600);

        return $path;
    }

    public function testServiceAccount(array $credentials, ?string $deviceToken = null): array
    {
        $accessToken = $this->tokenProvider->fetch($credentials);
        if ($accessToken === null) {
            return ['status' => 'failed', 'message' => 'فشل الحصول على OAuth Token. تحقق من ملف الخدمة.'];
        }

        $projectId = (string) $this->settings->get('firebase.project_id', '');
        if ($projectId === '') {
            $projectId = (string) ($credentials['project_id'] ?? '');
        }
        if ($projectId === '') {
            return ['status' => 'failed', 'message' => 'معرّف المشروع (project_id) مفقود.'];
        }

        $target = $deviceToken !== null && trim($deviceToken) !== ''
            ? trim($deviceToken)
            : 'ec-probe-invalid-token';

        try {
            $response = \Illuminate\Support\Facades\Http::withToken($accessToken)
                ->timeout(15)
                ->post('https://fcm.googleapis.com/v1/projects/' . rawurlencode($projectId) . '/messages:send', [
                    'validate_only' => true,
                    'message' => [
                        'token' => $target,
                        'notification' => [
                            'title' => 'Elite Clean connection test',
                            'body' => 'Validate only; no notification is delivered.',
                        ],
                    ],
                ]);

            if ($response->successful()) {
                return ['status' => 'ok', 'message' => 'بيانات الاعتماد مقبولة (validate-only؛ لم يُرسل أي إشعار).'];
            }

            $httpStatus = $response->status();
            $errorCode = (string) (
                data_get($response->json(), 'error.details.0.errorCode')
                ?? data_get($response->json(), 'error.status')
                ?? ''
            );

            $authAccepted = in_array($httpStatus, [400, 404], true)
                && in_array($errorCode, ['UNREGISTERED', 'INVALID_ARGUMENT', 'NOT_FOUND', 'INVALID_REGISTRATION'], true);

            if ($authAccepted) {
                return ['status' => 'ok', 'message' => 'بيانات الاعتماد مقبولة (validate-only؛ جهاز الاختبار غير مسجل، وهذا لا يدل على فشل المصادقة).'];
            }

            return ['status' => 'failed', 'message' => 'FCM رفض الطلب (HTTP ' . $httpStatus . ').'];
        } catch (\Throwable) {
            return ['status' => 'failed', 'message' => 'فشل الاتصال بـ FCM.'];
        }
    }

    public function activateServiceAccount(string $stagedPath, array $credentials): array
    {
        $activePath = $this->activePath();
        $dir = dirname($activePath);
        File::ensureDirectoryExists($dir, 0750, true);

        // Backup the current file (if any) for a short-lived rollback, unless
        // the current key is the known compromised one.
        $backupPath = null;
        if (is_file($activePath) && !$this->isCompromisedKey($activePath)) {
            $backupDir = $this->backupDir();
            File::ensureDirectoryExists($backupDir, 0750, true);
            $backupPath = $backupDir . '/credentials-' . date('YmdHis') . '-' . Str::random(8) . '.json';
            if (!@copy($activePath, $backupPath)) {
                return ['ok' => false, 'message' => 'تعذّر إنشاء نسخة احتياطية من المفتاح الحالي.'];
            }
            @chmod($backupPath, 0600);
        }

        // Atomic replace via temp file in the same directory.
        $tempPath = $activePath . '.tmp-' . Str::random(16);
        if (File::put($tempPath, (string) file_get_contents($stagedPath)) === false) {
            @unlink($tempPath);
            return ['ok' => false, 'message' => 'تعذّر كتابة الملف الجديد.'];
        }
        @chmod($tempPath, 0600);

        if (!@rename($tempPath, $activePath)) {
            @unlink($tempPath);
            return ['ok' => false, 'message' => 'تعذّر تفعيل الملف الجديد (rename فشل).'];
        }

        @unlink($stagedPath);

        // Clear the inline JSON setting so the file takes precedence.
        $this->settings->set('firebase.credentials_json', '', ['group' => 'firebase', 'is_secret' => true]);
        if (empty($this->settings->get('firebase.project_id', ''))) {
            $this->settings->set('firebase.project_id', $credentials['project_id'], ['group' => 'firebase', 'is_secret' => false]);
        }

        $this->cleanupOldBackups();

        $this->audit->log(
            'firebase.credentials.uploaded',
            'firebase',
            'credentials.json',
            null,
            '[redacted]',
            [
                'project_id' => $credentials['project_id'],
                'client_email' => $credentials['client_email'],
                'backup' => $backupPath ? basename($backupPath) : null,
            ]
        );

        return [
            'ok' => true,
            'message' => 'تم تفعيل بيانات Firebase بنجاح.',
            'backup' => $backupPath ? basename($backupPath) : null,
        ];
    }

    public function rollbackServiceAccount(): array
    {
        $backupDir = $this->backupDir();
        if (!is_dir($backupDir)) {
            return ['ok' => false, 'message' => 'لا توجد نسخة احتياطية للتراجع.'];
        }

        $backups = glob($backupDir . '/credentials-*.json') ?: [];
        if (empty($backups)) {
            return ['ok' => false, 'message' => 'لا توجد نسخة احتياطية للتراجع.'];
        }
        rsort($backups);
        $latest = $backups[0];

        if ($this->isCompromisedKey($latest)) {
            @unlink($latest);
            return ['ok' => false, 'message' => 'التراجع إلى المفتاح المكشوف غير مسموح. ألغِه من Google Cloud.'];
        }

        $raw = (string) @file_get_contents($latest);
        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            return ['ok' => false, 'message' => 'النسخة الاحتياطية تالفة.'];
        }
        $validation = $this->validateServiceAccount($decoded, false);
        if (!$validation['ok']) {
            return ['ok' => false, 'message' => 'النسخة الاحتياطية غير صالحة.'];
        }

        $activePath = $this->activePath();
        $tempPath = $activePath . '.tmp-' . Str::random(16);
        if (File::put($tempPath, $raw) === false) {
            return ['ok' => false, 'message' => 'تعذّر كتابة الملف.'];
        }
        @chmod($tempPath, 0600);
        @unlink($activePath);
        if (!@rename($tempPath, $activePath)) {
            @unlink($tempPath);
            return ['ok' => false, 'message' => 'تعذّر التراجع.'];
        }

        @unlink($latest);

        $this->audit->log(
            'firebase.credentials.rollback',
            'firebase',
            'credentials.json',
            null,
            '[redacted]',
            ['restored_from' => basename($latest)]
        );

        return ['ok' => true, 'message' => 'تم التراجع إلى النسخة الاحتياطية (المفتاح القديم مكشوف — ألغِه من Google Cloud).'];
    }

    public function hasServiceAccountBackup(): bool
    {
        $backups = glob($this->backupDir() . '/credentials-*.json') ?: [];

        return !empty($backups);
    }

    public function saveAndroidConfig(UploadedFile $file): array
    {
        $raw = (string) file_get_contents($file->getRealPath());
        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            return ['ok' => false, 'message' => 'ملف JSON غير صالح.'];
        }

        $validation = $this->validateAndroidConfig($decoded);
        if (!$validation['ok']) {
            return ['ok' => false, 'message' => 'بيانات Android غير صالحة.', 'errors' => $validation['errors']];
        }

        $dir = dirname($this->androidConfigPath());
        File::ensureDirectoryExists($dir, 0750, true);

        // Backup previous (optional, keep one).
        $backupPath = $this->androidConfigPath() . '.bak';
        if (is_file($this->androidConfigPath())) {
            @copy($this->androidConfigPath(), $backupPath);
            @chmod($backupPath, 0600);
        }

        $tempPath = $this->androidConfigPath() . '.tmp-' . Str::random(16);
        if (File::put($tempPath, $raw) === false) {
            @unlink($tempPath);
            return ['ok' => false, 'message' => 'تعذّر كتابة الملف.'];
        }
        @chmod($tempPath, 0600);
        if (!@rename($tempPath, $this->androidConfigPath())) {
            @unlink($tempPath);
            return ['ok' => false, 'message' => 'تعذّر حفظ ملف Android.'];
        }

        $this->audit->log(
            'firebase.android_config.uploaded',
            'firebase',
            'google-services.json',
            null,
            '[redacted]',
            [
                'project_id' => $validation['project_id'],
                'package_name' => $validation['package_name'],
                'mobilesdk_app_id' => $validation['mobilesdk_app_id'],
            ]
        );

        return [
            'ok' => true,
            'message' => 'تم حفظ إعدادات Android Firebase (لا تؤثر على التطبيقات المثبتة — تتطلب إعادة بناء).',
            'project_id' => $validation['project_id'],
            'package_name' => $validation['package_name'],
            'mobilesdk_app_id' => $validation['mobilesdk_app_id'],
        ];
    }

    public function safeServiceAccountInfo(): array
    {
        $credentials = null;
        if (is_file($this->activePath()) && is_readable($this->activePath())) {
            $decoded = json_decode((string) file_get_contents($this->activePath()), true);
            if (is_array($decoded)) {
                $credentials = $decoded;
            }
        }

        if (!$credentials) {
            $credentials = $this->settings->get('firebase.credentials_json', '');
            if (is_string($credentials)) {
                $decoded = json_decode($credentials, true);
                if (is_array($decoded)) {
                    $credentials = $decoded;
                } else {
                    $credentials = null;
                }
            }
        }

        return [
            'project_id' => (string) ($credentials['project_id'] ?? $this->settings->get('firebase.project_id', '')),
            'client_email' => (string) ($credentials['client_email'] ?? ''),
            'source' => $this->sourceLabel(),
            'has_backup' => $this->hasServiceAccountBackup(),
        ];
    }

    public function safeAndroidConfigInfo(): ?array
    {
        $path = $this->androidConfigPath();
        if (!is_file($path) || !is_readable($path)) {
            return null;
        }

        $decoded = json_decode((string) file_get_contents($path), true);
        if (!is_array($decoded)) {
            return null;
        }

        $client = $decoded['client'][0] ?? null;

        return [
            'project_id' => (string) ($decoded['project_id'] ?? ''),
            'package_name' => (string) (data_get($client, 'client_info.android_client_info.package_name') ?? ''),
            'mobilesdk_app_id' => (string) (data_get($client, 'client_info.mobilesdk_app_id') ?? ''),
            'uploaded_at' => date('Y-m-d H:i:s', (int) filemtime($path)),
        ];
    }

    private function sourceLabel(): string
    {
        if (is_file($this->activePath()) && is_readable($this->activePath())) {
            return 'ملف خاص (storage)';
        }

        $raw = (string) $this->settings->get('firebase.credentials_json', '');
        if ($raw !== '' && !str_starts_with($raw, '********')) {
            return 'إعدادات اللوحة';
        }

        if ($this->settings->has('firebase.legacy_server_key')) {
            return 'Legacy Server Key';
        }

        return 'غير مهيأ';
    }

    public function isCompromisedKey(string $path): bool
    {
        $hash = (string) $this->settings->get('firebase.compromised_key_hash', '');
        if ($hash === '' || !is_file($path)) {
            return false;
        }

        $fileHash = @hash_file('sha256', $path);
        if ($fileHash === false) {
            return false;
        }

        return hash_equals($hash, $fileHash);
    }

    private function cleanupOldBackups(): void
    {
        $backupDir = $this->backupDir();
        if (!is_dir($backupDir)) {
            return;
        }

        foreach (glob($backupDir . '/credentials-*.json') ?: [] as $file) {
            if (is_file($file) && (time() - (int) filemtime($file)) > 86400) {
                @unlink($file);
            }
        }
    }
}
