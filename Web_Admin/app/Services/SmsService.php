<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SmsService
{
    public function __construct(
        private IntegrationSettingsService $settings,
        private MessageLogger $logger,
        private TemplateRenderer $templates
    ) {
    }

    public function isEnabled(): bool
    {
        return $this->settings->isEnabled('sms');
    }

    public function isConfigured(): bool
    {
        if ((string) $this->settings->get('sms.provider', 'generic_http') === 'log') {
            return true;
        }

        foreach (['api_url', 'user_name', 'password', 'sender_id'] as $key) {
            if (trim((string) $this->value('sms.' . $key)) === '') {
                return false;
            }
        }

        return true;
    }

    public function send(string $mobile, string $message, ?string $templateKey = null): array
    {
        if (!$this->isEnabled()) {
            return $this->result('skipped', 'SMS is disabled.');
        }

        $provider = (string) $this->settings->get('sms.provider', 'generic_http');

        if ($provider === 'log') {
            $this->logger->record('sms', $mobile, 'logged', 'log', $templateKey, null, null, null, ['mode' => 'test']);

            return $this->result('sent', 'SMS accepted by the log driver (no real delivery).');
        }

        if (!$this->isConfigured()) {
            $this->logger->record('sms', $mobile, 'not_configured', $provider, $templateKey);

            return $this->result('not_configured', 'SMS provider is not configured.');
        }

        $recipient = $this->normalizePhone($mobile);
        $timeout = max(1, (int) $this->value('sms.timeout', 15));

        try {
            $response = Http::timeout($timeout)->get((string) $this->value('sms.api_url'), [
                'USER_NAME' => (string) $this->value('sms.user_name'),
                'PASSWORD' => (string) $this->value('sms.password'),
                'ORIGINATOR' => (string) $this->value('sms.sender_id'),
                'RECIPIENT' => $recipient,
                'ROUTE' => (string) $this->value('sms.route'),
                'MESSAGE_TEXT' => $message,
            ]);

            if ($response->successful()) {
                $providerId = $this->extractProviderId($response->body());
                $this->logger->record('sms', $mobile, 'sent', 'generic_http', $templateKey, $providerId);

                return $this->result('sent', 'SMS sent.', $providerId);
            }

            $this->logger->record(
                'sms',
                $mobile,
                'failed',
                'generic_http',
                $templateKey,
                null,
                'http_' . $response->status(),
                'SMS provider returned HTTP ' . $response->status()
            );

            return $this->result('failed', 'SMS provider returned HTTP ' . $response->status());
        } catch (\Throwable $e) {
            // Never include the request URL/query (it contains the password).
            $this->logger->record(
                'sms',
                $mobile,
                'failed',
                'generic_http',
                $templateKey,
                null,
                'connection_error',
                'SMS provider connection failed.'
            );
            $this->logger->safeLog('warning', 'SMS delivery failed', ['exception' => get_class($e)]);

            return $this->result('failed', 'SMS provider connection failed.');
        }
    }

    public function sendTemplate(string $mobile, string $templateKey, array $variables, string $fallbackBody = ''): array
    {
        $rendered = $this->templates->render('sms', $templateKey, $variables, '', $fallbackBody);

        return $this->send($mobile, $rendered['body'], $templateKey);
    }

    public function test(string $mobile): array
    {
        return $this->sendTemplate($mobile, 'test', [
            'name' => 'Elite Clean',
            'time' => now()->toDateTimeString(),
        ], 'Elite Clean test message at {{time}}');
    }

    public function normalizePhone(string $mobile): string
    {
        $digits = preg_replace('/\D+/', '', $mobile) ?? '';
        $countryCode = preg_replace('/\D+/', '', (string) $this->value('sms.country_code', '44')) ?: '44';

        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        }

        if (str_starts_with($digits, $countryCode)) {
            return $digits;
        }

        if (str_starts_with($digits, '0')) {
            $digits = ltrim($digits, '0');
        }

        return $countryCode . $digits;
    }

    public function status(): array
    {
        return $this->settings->status('sms');
    }

    /**
     * Integration settings win; the legacy env keys remain a fallback during
     * the transition so existing installations keep sending SMS.
     */
    private function value(string $key, $default = null)
    {
        $stored = $this->settings->get($key);
        if ($stored !== null && $stored !== '') {
            return $stored;
        }

        return match ($key) {
            'sms.api_url' => config('app.sms_base_url') ?: $default,
            'sms.user_name' => config('app.sms_user_name') ?: $default,
            'sms.password' => config('app.sms_password') ?: $default,
            'sms.sender_id' => config('app.sms_originator') ?: $default,
            'sms.route' => config('app.sms_route') ?: $default,
            default => $default,
        };
    }

    private function extractProviderId(string $body): ?string
    {
        $body = trim($body);
        if ($body === '') {
            return null;
        }

        if (Str::startsWith($body, '{')) {
            $json = json_decode($body, true);
            if (is_array($json)) {
                foreach (['message_id', 'id', 'MessageID', 'messageId'] as $key) {
                    if (!empty($json[$key])) {
                        return (string) $json[$key];
                    }
                }
            }
        }

        return Str::limit($body, 120, '');
    }

    private function result(string $status, string $message, ?string $providerId = null): array
    {
        return ['status' => $status, 'message' => $message, 'provider_id' => $providerId];
    }
}
