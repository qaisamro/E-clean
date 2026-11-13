<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\IntegrationSetting;
use App\Models\MessageLog;
use App\Models\NotificationTemplate;
use App\Services\AuditLogger;
use App\Services\FirebaseCredentialManager;
use App\Services\FirebaseMessagingService;
use App\Services\GoogleMapsService;
use App\Services\IntegrationSettingsService;
use App\Services\IntegrationTester;
use App\Services\WebhookService;
use App\Support\IntegrationSchema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class IntegrationCenterController extends Controller
{
    public function __construct(
        private IntegrationSettingsService $settings,
        private AuditLogger $audit,
        private IntegrationTester $tester,
        private GoogleMapsService $maps,
        private FirebaseCredentialManager $firebaseCredentials,
        private FirebaseMessagingService $firebase
    ) {
    }

    public function index()
    {
        $this->authorizeSuperAdmin();

        $groups = array_keys(IntegrationSchema::groups());
        $data = [];
        $statuses = [];

        foreach ($groups as $group) {
            $data[$group] = $this->settings->groupWithMeta($group);
            $statuses[$group] = $this->settings->status($group);
        }

        $templates = NotificationTemplate::orderBy('channel')->orderBy('key')->get();
        $recentLogs = MessageLog::latest()->limit(10)->get();
        $webhookDeliveries = \App\Models\WebhookDelivery::latest()->limit(10)->get();
        $firebaseInfo = $this->firebaseCredentials->safeServiceAccountInfo();
        $firebaseAndroidInfo = $this->firebaseCredentials->safeAndroidConfigInfo();

        return view('integrations.index', compact(
            'data',
            'statuses',
            'templates',
            'recentLogs',
            'webhookDeliveries',
            'firebaseInfo',
            'firebaseAndroidInfo'
        ));
    }

    public function uploadFirebaseCredentials(Request $request)
    {
        $this->authorizeSuperAdmin();

        $request->validate([
            'service_account' => ['required', 'file', 'max:2048', 'mimes:json,txt'],
            'device_token' => ['nullable', 'string', 'max:4096'],
        ]);

        $file = $request->file('service_account');
        $raw = (string) file_get_contents($file->getRealPath());
        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            return back()->with('error', 'ملف JSON غير صالح.');
        }

        $validation = $this->firebaseCredentials->validateServiceAccount($decoded);
        if (!$validation['ok']) {
            return back()->withErrors($validation['errors'])->with('error', 'بيانات Firebase غير صالحة.');
        }

        $stagedPath = $this->firebaseCredentials->stageServiceAccount($file);
        $test = $this->firebaseCredentials->testServiceAccount($decoded, $request->input('device_token'));

        if (($test['status'] ?? '') !== 'ok') {
            @unlink($stagedPath);
            return back()->with('error', 'فشل اختبار بيانات الاعتماد: ' . ($test['message'] ?? ''));
        }

        $result = $this->firebaseCredentials->activateServiceAccount($stagedPath, $decoded);
        if (!($result['ok'] ?? false)) {
            @unlink($stagedPath);
            return back()->with('error', $result['message'] ?? 'تعذّر تفعيل الملف.');
        }

        return back()->with('success', $result['message'] . ' (المفتاح القديم مكشوف — ألغِه من Google Cloud).');
    }

    public function rollbackFirebaseCredentials()
    {
        $this->authorizeSuperAdmin();

        $result = $this->firebaseCredentials->rollbackServiceAccount();
        if (!($result['ok'] ?? false)) {
            return back()->with('error', $result['message'] ?? 'تعذّر التراجع.');
        }

        return back()->with('success', $result['message']);
    }

    public function uploadFirebaseAndroidConfig(Request $request)
    {
        $this->authorizeSuperAdmin();

        $request->validate([
            'android_config' => ['required', 'file', 'max:2048', 'mimes:json,txt'],
        ]);

        $file = $request->file('android_config');
        $raw = (string) file_get_contents($file->getRealPath());
        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            return back()->with('error', 'ملف JSON غير صالح.');
        }

        $result = $this->firebaseCredentials->saveAndroidConfig($file);
        if (!($result['ok'] ?? false)) {
            return back()->withErrors($result['errors'] ?? [])->with('error', $result['message'] ?? 'تعذّر حفظ الملف.');
        }

        return back()->with('success', $result['message'] . ' Project: ' . ($result['project_id'] ?? '') . ', Package: ' . ($result['package_name'] ?? ''));
    }

    public function update(Request $request, string $group)
    {
        $this->authorizeSuperAdmin();

        $schema = IntegrationSchema::group($group);
        abort_if($schema === null, 404);

        $fields = $schema['fields'];
        $incoming = $request->only(array_keys($fields));

        // An unchecked multi-select sends no key at all. Treat that as an
        // explicit empty selection so the admin can disable all channels.
        foreach ($fields as $key => $field) {
            if (($field['type'] ?? null) === 'multiselect' && !array_key_exists($key, $incoming)) {
                $incoming[$key] = [];
            }
        }

        $this->validateSensitiveChange($request, $group, $fields, $incoming);

        foreach ($fields as $key => $field) {
            if (!array_key_exists($key, $incoming)) {
                continue;
            }

            $value = $incoming[$key];
            $isSecret = (bool) ($field['secret'] ?? false);

            if ($isSecret && ($value === null || $value === '' || $value === IntegrationSettingsService::MASK)) {
                continue;
            }

            $rules = $field['rules'] ?? [];
            if ($field['type'] === 'bool') {
                $value = (string) $value === '1' ? '1' : '0';
            } elseif ($field['type'] === 'multiselect') {
                $selected = is_array($value) ? $value : explode(',', (string) $value);
                $selected = array_map(fn ($item) => strtolower(trim((string) $item)), $selected);
                $value = implode(',', array_values(array_intersect($selected, array_keys($field['options'] ?? []))));
            } elseif (in_array($field['type'], ['number', 'select'], true) && $value === '') {
                $value = $field['default'] ?? null;
            }

            if (!empty($rules)) {
                $validator = Validator::make(['value' => $value], ['value' => $rules]);
                if ($validator->fails()) {
                    return back()->withErrors([
                        "{$group}.{$key}" => $validator->errors()->first('value'),
                    ])->withInput();
                }
            }

            $old = $this->settings->get("{$group}.{$key}");
            if ((string) $old === (string) $value && !$isSecret && $field['type'] !== 'multiselect') {
                continue;
            }

            $this->settings->set("{$group}.{$key}", $value, [
                'group' => $group,
                'is_secret' => $isSecret,
                'type' => $field['type'] === 'bool' ? 'boolean' : $field['type'],
            ]);

            $this->audit->changedSetting($group, "{$group}.{$key}", $old, $value, $isSecret);
        }

        return back()->with('success', 'تم حفظ إعدادات ' . $schema['label'] . ' بنجاح.');
    }

    public function test(Request $request, string $group)
    {
        $this->authorizeSuperAdmin();

        if (!IntegrationSchema::group($group)) {
            abort(404);
        }

        $result = $this->tester->test($group, $request->all());

        if ($request->expectsJson()) {
            return response()->json($result);
        }

        $success = in_array($result['status'], ['sent', 'ok', 'configured', 'delivered', 'partial'], true);

        return back()->with($success ? 'success' : 'error', $result['message'] ?? 'Test completed.');
    }

    public function reverseGeocode(Request $request)
    {
        $this->authorizeSuperAdmin();

        $validated = $request->validate([
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $result = $this->maps->reverseGeocode((float) $validated['lat'], (float) $validated['lng']);

        return response()->json($result);
    }

    public function templates()
    {
        $this->authorizeSuperAdmin();

        $templates = NotificationTemplate::orderBy('channel')->orderBy('key')->get();

        return view('integrations.templates', compact('templates'));
    }

    public function storeTemplate(Request $request)
    {
        $this->authorizeSuperAdmin();

        $validated = $this->validateTemplate($request);
        $template = NotificationTemplate::create($validated);

        $this->audit->log('notification_template.created', 'templates', 'template:' . $template->id, null, [
            'channel' => $template->channel,
            'key' => $template->key,
        ]);

        return back()->with('success', 'تم إنشاء القالب.');
    }

    public function updateTemplate(Request $request, NotificationTemplate $template)
    {
        $this->authorizeSuperAdmin();

        $validated = $this->validateTemplate($request, $template);
        $template->update($validated);

        $this->audit->log('notification_template.updated', 'templates', 'template:' . $template->id, null, [
            'channel' => $template->channel,
            'key' => $template->key,
        ]);

        return back()->with('success', 'تم تحديث القالب.');
    }

    public function destroyTemplate(NotificationTemplate $template)
    {
        $this->authorizeSuperAdmin();

        $this->audit->log('notification_template.deleted', 'templates', 'template:' . $template->id, [
            'channel' => $template->channel,
            'key' => $template->key,
        ]);
        $template->delete();

        return back()->with('success', 'تم حذف القالب.');
    }

    public function messageLogs(Request $request)
    {
        $this->authorizeSuperAdmin();

        $query = MessageLog::query()->latest();

        if ($channel = $request->input('channel')) {
            $query->where('channel', $channel);
        }
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $logs = $query->paginate(30)->withQueryString();

        return view('integrations.message-logs', compact('logs'));
    }

    public function webhooks()
    {
        $this->authorizeSuperAdmin();

        $endpoints = \App\Models\WebhookEndpoint::withCount('deliveries')->latest()->get();
        $deliveries = \App\Models\WebhookDelivery::with('endpoint')->latest()->paginate(30);
        $events = WebhookService::EVENTS;

        return view('integrations.webhooks', compact('endpoints', 'deliveries', 'events'));
    }

    public function storeWebhook(Request $request)
    {
        $this->authorizeSuperAdmin();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'url' => ['required', 'url', 'max:2048'],
            'events' => ['required', 'array', 'min:1'],
            'events.*' => ['string', Rule::in(array_merge(array_keys(WebhookService::EVENTS), ['*']))],
            'description' => ['nullable', 'string', 'max:1000'],
            'active' => ['nullable', 'boolean'],
        ]);

        $secret = 'whsec_' . bin2hex(random_bytes(24));

        $endpoint = \App\Models\WebhookEndpoint::create([
            'name' => $validated['name'],
            'url' => $validated['url'],
            'secret' => $secret,
            'events' => $validated['events'],
            'description' => $validated['description'] ?? null,
            'active' => $request->boolean('active', true),
            'created_by' => auth()->id(),
        ]);

        $this->audit->log('webhook.created', 'webhooks', 'endpoint:' . $endpoint->id, null, [
            'url' => $endpoint->url,
            'events' => $endpoint->events,
        ]);

        return back()->with('success', 'تم إنشاء Webhook.')->with('webhook_secret', $secret);
    }

    public function updateWebhook(Request $request, \App\Models\WebhookEndpoint $endpoint)
    {
        $this->authorizeSuperAdmin();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'url' => ['required', 'url', 'max:2048'],
            'events' => ['required', 'array', 'min:1'],
            'events.*' => ['string', Rule::in(array_merge(array_keys(WebhookService::EVENTS), ['*']))],
            'description' => ['nullable', 'string', 'max:1000'],
            'active' => ['nullable', 'boolean'],
        ]);

        $endpoint->update([
            'name' => $validated['name'],
            'url' => $validated['url'],
            'events' => $validated['events'],
            'description' => $validated['description'] ?? null,
            'active' => $request->boolean('active'),
        ]);

        $this->audit->log('webhook.updated', 'webhooks', 'endpoint:' . $endpoint->id, null, [
            'url' => $endpoint->url,
            'events' => $endpoint->events,
            'active' => $endpoint->active,
        ]);

        return back()->with('success', 'تم تحديث Webhook.');
    }

    public function deleteWebhook(\App\Models\WebhookEndpoint $endpoint)
    {
        $this->authorizeSuperAdmin();

        $this->audit->log('webhook.deleted', 'webhooks', 'endpoint:' . $endpoint->id, ['url' => $endpoint->url]);
        $endpoint->delete();

        return back()->with('success', 'تم حذف Webhook.');
    }

    public function testWebhook(\App\Models\WebhookEndpoint $endpoint)
    {
        $this->authorizeSuperAdmin();

        $result = $this->tester->test('webhooks', ['endpoint_id' => $endpoint->id]);

        return back()->with(
            in_array($result['status'], ['delivered', 'partial', 'ok', 'configured'], true) ? 'success' : 'error',
            $result['message'] ?? 'Test completed.'
        );
    }

    public function retryWebhook(\App\Models\WebhookDelivery $delivery)
    {
        $this->authorizeSuperAdmin();

        app(WebhookService::class)->retry($delivery);

        return back()->with('success', 'تمت محاولة إعادة الإرسال.');
    }

    private function validateSensitiveChange(Request $request, string $group, array $fields, array $incoming): void
    {
        $changingSecret = false;
        foreach ($fields as $key => $field) {
            if (($field['secret'] ?? false) && !empty($incoming[$key]) && $incoming[$key] !== IntegrationSettingsService::MASK) {
                $changingSecret = true;
                break;
            }
        }

        $reauthRequired = (string) $this->settings->get('security.reauth_required', '1') === '1';
        if (!$changingSecret || !$reauthRequired) {
            return;
        }

        $user = auth()->user();
        $password = (string) $request->input('current_password', '');

        if (!$user || $password === '' || !Hash::check($password, (string) $user->password)) {
            abort(423, 'Current password confirmation is required to change secrets.');
        }
    }

    private function validateTemplate(Request $request, ?NotificationTemplate $template = null): array
    {
        $validated = $request->validate([
            'channel' => ['required', Rule::in(['sms', 'email', 'whatsapp', 'push'])],
            'key' => [
                'required',
                'string',
                'max:64',
                'regex:/^[a-z0-9_\-]+$/',
                Rule::unique('notification_templates', 'key')->where('channel', $request->input('channel'))->ignore($template?->id),
            ],
            'name' => ['required', 'string', 'max:120'],
            'subject' => ['nullable', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:10000'],
            'provider_template_name' => ['nullable', 'string', 'max:160'],
            'language' => ['required', 'string', 'max:12'],
            'variables' => ['nullable', 'string', 'max:1000'],
            'active' => ['nullable', 'boolean'],
        ]);

        $variables = array_values(array_filter(array_map('trim', explode(',', (string) ($validated['variables'] ?? '')))));
        $validated['variables'] = $variables ?: null;
        $validated['active'] = $request->boolean('active');

        return $validated;
    }

    private function authorizeSuperAdmin(): void
    {
        abort_unless(auth()->check() && auth()->user()->hasAnyRole(['root', 'admin']), 403);
    }
}
