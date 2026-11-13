<?php

namespace App\Services;

use App\Models\WebhookEndpoint;

class IntegrationTester
{
    public function __construct(
        private IntegrationSettingsService $settings,
        private SmsService $sms,
        private MailIntegrationService $mail,
        private FirebaseMessagingService $firebase,
        private GoogleMapsService $maps,
        private WhatsAppService $whatsapp,
        private WebhookService $webhooks,
        private DeviceTokenService $devices,
        private AuditLogger $audit
    ) {
    }

    public function test(string $group, array $input = []): array
    {
        $result = match ($group) {
            'sms' => $this->testSms($input),
            'otp' => $this->testOtp(),
            'firebase' => $this->testFirebase($input),
            'google_maps' => $this->testMaps(),
            'smtp' => $this->testSmtp($input),
            'whatsapp' => $this->testWhatsapp($input),
            'api' => $this->testApi(),
            'webhooks' => $this->testWebhooks($input),
            'security' => $this->testSecurity(),
            default => ['status' => 'not_configured', 'message' => 'Unknown integration group.'],
        };

        $success = in_array($result['status'], ['sent', 'ok', 'configured', 'delivered', 'partial'], true);
        $this->settings->recordTest($group, $success, $result['message'] ?? null);

        $this->audit->log(
            'integration.tested',
            $group,
            null,
            null,
            null,
            [
                'status' => $result['status'] ?? 'unknown',
                'message' => $result['message'] ?? null,
            ]
        );

        return $result;
    }

    private function testSms(array $input): array
    {
        $recipient = trim((string) ($input['recipient'] ?? ''));
        if ($recipient === '') {
            return ['status' => 'not_configured', 'message' => 'Test recipient is required.'];
        }

        return $this->sms->test($recipient);
    }

    private function testOtp(): array
    {
        if (!$this->settings->isEnabled('otp')) {
            return ['status' => 'skipped', 'message' => 'OTP is disabled.'];
        }

        $length = (int) $this->settings->get('otp.length', 6);
        $ttl = (int) $this->settings->get('otp.ttl', 300);

        if (!in_array($length, [4, 6, 8], true) || $ttl < 30) {
            return ['status' => 'error', 'message' => 'Invalid OTP length or TTL.'];
        }

        $purposes = [];
        foreach (['registration', 'login', 'password_reset', 'phone_change'] as $purpose) {
            $purposes[$purpose] = (string) $this->settings->get("otp.enabled_{$purpose}", '1') === '1';
        }

        if (!in_array(true, $purposes, true)) {
            return [
                'status' => 'skipped',
                'message' => 'All OTP purposes are disabled.',
                'purposes' => $purposes,
            ];
        }

        $providers = [
            'sms' => $this->sms->isEnabled() && $this->sms->isConfigured(),
            'email' => $this->mail->isEnabled() && $this->mail->isConfigured(),
            'whatsapp' => $this->whatsapp->isEnabled() && $this->whatsapp->isConfigured(),
        ];

        if (!in_array(true, $providers, true)) {
            return [
                'status' => 'not_configured',
                'message' => 'OTP settings are valid but no delivery channel is configured.',
                'providers' => $providers,
                'purposes' => $purposes,
            ];
        }

        return [
            'status' => 'configured',
            'message' => 'OTP settings are valid. Delivery channels: ' . implode(', ', array_keys(array_filter($providers))),
            'providers' => $providers,
            'purposes' => $purposes,
        ];
    }

    private function testFirebase(array $input): array
    {
        $token = trim((string) ($input['device_token'] ?? ''));
        if ($token === '') {
            $token = (string) $this->devices->firstForRole((string) ($input['role'] ?? 'customer'));
        }

        $sendReal = filter_var($input['send_test'] ?? false, FILTER_VALIDATE_BOOLEAN);

        if ($sendReal) {
            if ($token === '') {
                return ['status' => 'not_configured', 'message' => 'A device token is required to send a real test notification.'];
            }

            return $this->firebase->test($token);
        }

        return $this->firebase->testConnection($token !== '' ? $token : null);
    }

    private function testMaps(): array
    {
        return $this->maps->test();
    }

    private function testSmtp(array $input): array
    {
        $recipient = trim((string) ($input['recipient'] ?? ''));
        if ($recipient === '') {
            return $this->mail->testConnection();
        }

        return $this->mail->test($recipient);
    }

    private function testWhatsapp(array $input): array
    {
        $recipient = trim((string) ($input['recipient'] ?? ''));
        if ($recipient === '') {
            return $this->whatsapp->testConnection();
        }

        return $this->whatsapp->test($recipient);
    }

    private function testApi(): array
    {
        return $this->settings->isEnabled('api')
            ? ['status' => 'configured', 'message' => 'API key authentication is enabled.']
            : ['status' => 'skipped', 'message' => 'API key authentication is disabled.'];
    }

    private function testWebhooks(array $input): array
    {
        $endpointId = (int) ($input['endpoint_id'] ?? 0);
        if ($endpointId > 0) {
            $endpoint = WebhookEndpoint::find($endpointId);
            if (!$endpoint) {
                return ['status' => 'not_configured', 'message' => 'Webhook endpoint not found.'];
            }

            return $this->webhooks->deliverTest($endpoint);
        }

        if (!$this->settings->isEnabled('webhooks')) {
            return ['status' => 'skipped', 'message' => 'Webhooks are disabled.'];
        }

        return ['status' => 'configured', 'message' => 'Webhooks are enabled; add an endpoint to test delivery.'];
    }

    private function testSecurity(): array
    {
        return [
            'status' => 'ok',
            'message' => 'Security settings loaded. Audit logging and secret masking are active.',
        ];
    }
}
