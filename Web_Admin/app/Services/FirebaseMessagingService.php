<?php

namespace App\Services;

use App\Services\Firebase\ServiceAccountTokenProvider;
use App\Support\FirebaseCredentials;
use Illuminate\Support\Facades\Http;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\ApnsConfig;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FirebaseMessagingService
{
    private mixed $messaging = null;

    public function __construct(
        private IntegrationSettingsService $settings,
        private MessageLogger $logger,
        private ServiceAccountTokenProvider $tokenProvider
    ) {
    }

    public function isEnabled(): bool
    {
        return $this->settings->isEnabled('firebase');
    }

    public function isConfigured(): bool
    {
        return $this->settings->isConfigured('firebase');
    }

    public function credentialsSource(): string
    {
        if ($this->credentialsFromSettings()) {
            return 'integration_settings';
        }

        if ($this->credentialsFromFile()) {
            return 'server_file';
        }

        if ($this->settings->has('firebase.legacy_server_key')) {
            return 'legacy_server_key';
        }

        return 'none';
    }

    public function send(string $deviceToken, string $title, string $body, array $data = []): array
    {
        return $this->sendMany([$deviceToken], $title, $body, $data);
    }

    public function sendMany(array $deviceTokens, string $title, string $body, array $data = []): array
    {
        $deviceTokens = array_values(array_unique(array_filter($deviceTokens)));
        if (empty($deviceTokens)) {
            return $this->result('skipped', 'No device tokens.');
        }

        if (!$this->isEnabled()) {
            return $this->result('skipped', 'Firebase push is disabled.');
        }

        if (!$this->isConfigured()) {
            foreach ($deviceTokens as $token) {
                $this->logger->record('push', $token, 'not_configured', 'fcm');
            }

            return $this->result('not_configured', 'Firebase credentials are not configured.');
        }

        $credentials = $this->credentialsFromSettings() ?? $this->credentialsFromFile();

        if ($credentials) {
            return $this->sendWithServiceAccount($deviceTokens, $title, $body, $data, $credentials);
        }

        return $this->sendLegacy($deviceTokens, $title, $body, $data);
    }

    public function test(string $deviceToken, string $title = 'Elite Clean test', string $body = 'Firebase test notification'): array
    {
        return $this->send($deviceToken, $title, $body, ['type' => 'test']);
    }

    /**
     * Non-destructive connection test: authenticate with the service account
     * and send a validate_only request. When a real device token is provided
     * it is validated without delivering anything; otherwise an intentionally
     * invalid probe token is used and an UNREGISTERED response is accepted as
     * proof that authentication works.
     */
    public function testConnection(?string $deviceToken = null): array
    {
        if (!$this->isEnabled()) {
            return $this->result('skipped', 'Firebase push is disabled.');
        }

        $credentials = $this->credentialsFromSettings() ?? $this->credentialsFromFile();

        if (!$credentials) {
            if ($this->hasLegacyKey()) {
                return $this->testLegacyConnection();
            }

            return $this->result('not_configured', 'Firebase credentials are not configured.');
        }

        $accessToken = $this->tokenProvider->fetch($credentials);
        if ($accessToken === null) {
            return $this->result('failed', 'Firebase OAuth token request failed. Check the service account JSON.');
        }

        $projectId = (string) $this->settings->get('firebase.project_id', '');
        if ($projectId === '') {
            $projectId = (string) ($credentials['project_id'] ?? '');
        }

        if ($projectId === '') {
            return $this->result('not_configured', 'Firebase project ID is missing.');
        }

        $target = $deviceToken !== null && trim($deviceToken) !== ''
            ? trim($deviceToken)
            : 'ec-probe-invalid-token';

        try {
            $response = Http::withToken($accessToken)
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
                return $this->result('ok', 'Firebase credentials accepted (validate-only; no notification sent).');
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
                return $this->result('ok', 'Firebase credentials accepted (validate-only; probe token is not registered).');
            }

            return $this->result('failed', 'Firebase rejected the request (HTTP ' . $httpStatus . ').');
        } catch (\Throwable) {
            return $this->result('failed', 'Firebase connection failed.');
        }
    }

    public function status(): array
    {
        $status = $this->settings->status('firebase');
        $status['credentials_source'] = $this->credentialsSource();
        $status['credentials_present'] = $this->credentialsSource() !== 'none';
        $status['credentials_in_public'] = FirebaseCredentials::publicFileExists();

        return $status;
    }

    private function testLegacyConnection(): array
    {
        $serverKey = $this->legacyKey();

        if ($serverKey === '') {
            return $this->result('not_configured', 'Firebase server key is not configured.');
        }

        try {
            $response = Http::timeout(15)
                ->withHeaders(['Authorization' => 'key=' . $serverKey])
                ->post('https://fcm.googleapis.com/fcm/send', [
                    'registration_ids' => ['ec-probe-invalid-token'],
                    'dry_run' => true,
                ]);

            if ($response->successful()) {
                return $this->result('ok', 'Legacy FCM key accepted (dry run; no notification sent).');
            }

            if (in_array($response->status(), [401, 403], true)) {
                return $this->result('failed', 'Legacy FCM rejected the server key.');
            }

            if (in_array($response->status(), [404, 410], true)) {
                return $this->result('failed', 'Legacy FCM endpoint is unavailable; migrate to a service account.');
            }

            return $this->result('failed', 'Legacy FCM returned HTTP ' . $response->status() . '.');
        } catch (\Throwable) {
            return $this->result('failed', 'Legacy FCM connection failed.');
        }
    }

    private function hasLegacyKey(): bool
    {
        return $this->legacyKey() !== '';
    }

    private function legacyKey(): string
    {
        return (string) $this->settings->get('firebase.legacy_server_key', config('app.fcm_server_key'));
    }

    private function sendWithServiceAccount(array $tokens, string $title, string $body, array $data, array $credentials): array
    {
        try {
            $message = CloudMessage::new()
                ->withNotification(Notification::create($title, $body))
                ->withData($this->stringifyData($data))
                ->withAndroidConfig(AndroidConfig::fromArray([
                    'priority' => 'high',
                    'notification' => [
                        'sound' => (string) $this->settings->get('firebase.default_sound', 'default'),
                        'channel_id' => (string) $this->settings->get('firebase.default_channel_id', 'high_importance_channel'),
                        'notification_priority' => 'PRIORITY_HIGH',
                        'visibility' => 'PUBLIC',
                        'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    ],
                ]))
                ->withApnsConfig(ApnsConfig::fromArray([
                    'payload' => ['aps' => ['sound' => 'default']],
                ]));

            $messaging = $this->messaging($credentials);

            if (count($tokens) === 1) {
                $messaging->send($message->withChangedTarget('token', $tokens[0]));
                $this->logger->record('push', $tokens[0], 'sent', 'fcm_v1', null, null, null, null, ['count' => 1]);

                return $this->result('sent', 'Notification sent.', null, 1);
            }

            $report = $messaging->sendMulticast($message, $tokens);
            $success = $report->successes()->count();
            $failures = $report->failures()->count();

            foreach ($tokens as $index => $token) {
                $this->logger->record('push', $token, $index < $success ? 'sent' : 'failed', 'fcm_v1');
            }

            return $this->result(
                $failures > 0 ? 'partial' : 'sent',
                "Sent {$success} of " . count($tokens) . ' notifications.',
                null,
                $success,
                $failures
            );
        } catch (\Throwable $e) {
            foreach ($tokens as $token) {
                $this->logger->record('push', $token, 'failed', 'fcm_v1', null, null, 'fcm_error', 'FCM delivery failed.');
            }
            $this->logger->safeLog('warning', 'FCM delivery failed', ['exception' => get_class($e)]);

            return $this->result('failed', 'FCM delivery failed.');
        }
    }

    /**
     * Transitional path for the legacy server key. Google has deprecated this
     * endpoint; it is kept so existing installations do not break while they
     * migrate to a service account.
     */
    private function sendLegacy(array $tokens, string $title, string $body, array $data): array
    {
        $serverKey = (string) $this->settings->get('firebase.legacy_server_key', config('app.fcm_server_key'));

        if ($serverKey === '') {
            foreach ($tokens as $token) {
                $this->logger->record('push', $token, 'not_configured', 'fcm_legacy');
            }

            return $this->result('not_configured', 'Firebase service account is not configured.');
        }

        try {
            $payload = [
                'registration_ids' => $tokens,
                'notification' => ['title' => $title, 'body' => $body, 'sound' => 'default'],
                'data' => $this->stringifyData($data),
                'priority' => 'high',
            ];

            $response = Http::timeout(15)
                ->withHeaders(['Authorization' => 'key=' . $serverKey])
                ->post('https://fcm.googleapis.com/fcm/send', $payload);

            if ($response->successful()) {
                foreach ($tokens as $token) {
                    $this->logger->record('push', $token, 'sent', 'fcm_legacy');
                }

                return $this->result('sent', 'Notification sent through legacy FCM.', null, count($tokens));
            }

            foreach ($tokens as $token) {
                $this->logger->record('push', $token, 'failed', 'fcm_legacy', null, null, 'http_' . $response->status(), 'Legacy FCM rejected the request.');
            }

            return $this->result('failed', 'Legacy FCM returned HTTP ' . $response->status());
        } catch (\Throwable $e) {
            foreach ($tokens as $token) {
                $this->logger->record('push', $token, 'failed', 'fcm_legacy', null, null, 'connection_error', 'Legacy FCM connection failed.');
            }
            $this->logger->safeLog('warning', 'Legacy FCM delivery failed', ['exception' => get_class($e)]);

            return $this->result('failed', 'Legacy FCM connection failed.');
        }
    }

    private function messaging(array $credentials)
    {
        if ($this->messaging === null) {
            $factory = (new Factory)->withServiceAccount($credentials);
            $projectId = (string) $this->settings->get('firebase.project_id', '');
            if ($projectId === '') {
                $projectId = (string) ($credentials['project_id'] ?? '');
            }
            if ($projectId !== '') {
                $factory = $factory->withProjectId($projectId);
            }
            $this->messaging = $factory->createMessaging();
        }

        return $this->messaging;
    }

    private function credentialsFromSettings(): ?array
    {
        $raw = (string) $this->settings->get('firebase.credentials_json', '');
        if (str_starts_with($raw, '********')) {
            return null;
        }

        return $this->decodeCredentials($raw);
    }

    private function credentialsFromFile(): ?array
    {
        foreach (FirebaseCredentials::paths() as $path) {
            if (!is_file($path) || !is_readable($path)) {
                continue;
            }
            $credentials = $this->decodeCredentials((string) file_get_contents($path));
            if ($credentials) {
                return $credentials;
            }
        }

        return null;
    }

    private function decodeCredentials(string $raw): ?array
    {
        $raw = trim($raw);
        if ($raw === '') {
            return null;
        }

        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            return null;
        }

        foreach (['project_id', 'client_email', 'private_key'] as $required) {
            if (empty($decoded[$required])) {
                return null;
            }
        }

        return $decoded;
    }

    private function stringifyData(array $data): array
    {
        $result = [];
        foreach ($data as $key => $value) {
            if (is_scalar($value) || $value === null) {
                $result[(string) $key] = (string) $value;
            }
        }

        return $result;
    }

    private function result(string $status, string $message, ?string $providerId = null, int $success = 0, int $failed = 0): array
    {
        return [
            'status' => $status,
            'message' => $message,
            'provider_id' => $providerId,
            'success' => $success,
            'failed' => $failed,
        ];
    }
}
