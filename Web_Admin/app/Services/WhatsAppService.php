<?php

namespace App\Services;

use App\Models\IntegrationWebhookEvent;
use App\Models\MessageLog;
use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    public function __construct(
        private IntegrationSettingsService $settings,
        private MessageLogger $logger,
        private TemplateRenderer $templates
    ) {
    }

    public function isEnabled(): bool
    {
        return $this->settings->isEnabled('whatsapp');
    }

    public function isConfigured(): bool
    {
        return $this->settings->isConfigured('whatsapp');
    }

    public function isWebhookConfigured(): bool
    {
        return $this->settings->has('whatsapp.app_secret')
            && $this->settings->has('whatsapp.webhook_verify_token');
    }

    public function sendText(string $to, string $body, ?string $templateKey = null): array
    {
        return $this->sendMessage($to, [
            'type' => 'text',
            'text' => ['preview_url' => false, 'body' => $body],
        ], $templateKey);
    }

    public function sendTemplate(string $to, string $templateName, string $language, array $components = [], ?string $templateKey = null): array
    {
        $template = [
            'name' => $templateName,
            'language' => ['code' => $language],
        ];
        if (!empty($components)) {
            $template['components'] = $components;
        }

        return $this->sendMessage($to, [
            'type' => 'template',
            'template' => $template,
        ], $templateKey);
    }

    public function sendLocalTemplate(string $to, string $templateKey, array $variables): array
    {
        $rendered = $this->templates->render('whatsapp', $templateKey, $variables);

        if ($rendered['template']?->provider_template_name) {
            return $this->sendTemplate(
                $to,
                $rendered['template']->provider_template_name,
                $rendered['template']->language ?: 'ar',
                [],
                $templateKey
            );
        }

        return $this->sendText($to, $rendered['body'], $templateKey);
    }

    public function test(string $to): array
    {
        return $this->sendLocalTemplate($to, 'test', [
            'app' => config('app.name'),
            'time' => now()->toDateTimeString(),
        ]);
    }

    /**
     * Non-destructive credential check: read the business phone number
     * metadata from Graph API. No message is sent.
     */
    public function testConnection(): array
    {
        if (!$this->isEnabled()) {
            return ['status' => 'skipped', 'message' => 'WhatsApp integration is disabled.'];
        }

        if (!$this->isConfigured()) {
            return ['status' => 'not_configured', 'message' => 'WhatsApp credentials are not configured.'];
        }

        $version = (string) $this->settings->get('whatsapp.api_version', 'v20.0');
        $phoneNumberId = (string) $this->settings->get('whatsapp.phone_number_id');
        $accessToken = (string) $this->settings->get('whatsapp.access_token');
        $url = 'https://graph.facebook.com/' . trim($version, '/') . '/' . $phoneNumberId;

        try {
            $response = Http::timeout(15)
                ->withToken($accessToken)
                ->get($url, ['fields' => 'display_phone_number,verified_name,quality_rating']);

            if ($response->successful()) {
                $json = $response->json();
                $name = trim((string) ($json['verified_name'] ?? ''));

                return [
                    'status' => 'ok',
                    'message' => $name !== ''
                        ? 'WhatsApp credentials accepted for ' . $name . ' (no message sent).'
                        : 'WhatsApp credentials accepted (no message sent).',
                    'verified_name' => $name !== '' ? $name : null,
                    'display_phone_number' => $json['display_phone_number'] ?? null,
                ];
            }

            $error = (string) ($response->json('error.message') ?? ('WhatsApp API returned HTTP ' . $response->status()));
            $error = $accessToken !== '' ? str_replace($accessToken, '[redacted]', $error) : $error;

            return ['status' => 'failed', 'message' => $this->settings->sanitizeMessage($error)];
        } catch (\Throwable) {
            return ['status' => 'failed', 'message' => 'WhatsApp API connection failed.'];
        }
    }

    public function verifySignature(string $rawPayload, ?string $signatureHeader): bool
    {
        $secret = (string) $this->settings->get('whatsapp.app_secret', '');
        if ($secret === '' || !$signatureHeader) {
            return false;
        }

        $provided = str_starts_with($signatureHeader, 'sha256=')
            ? substr($signatureHeader, 7)
            : $signatureHeader;

        if ($provided === '' || !ctype_xdigit($provided)) {
            return false;
        }

        return hash_equals(hash_hmac('sha256', $rawPayload, $secret), strtolower($provided));
    }

    public function processWebhook(array $payload, bool $signatureValid): ?IntegrationWebhookEvent
    {
        $entry = $payload['entry'][0] ?? [];
        $change = $entry['changes'][0] ?? [];
        $value = $change['value'] ?? [];
        $statuses = $value['statuses'][0] ?? null;
        $eventType = $statuses ? 'message_status' : (isset($value['messages'][0]) ? 'message_received' : 'unknown');
        $externalId = $statuses['id'] ?? ($value['messages'][0]['id'] ?? null);

        $event = IntegrationWebhookEvent::create([
            'provider' => 'whatsapp',
            'event_type' => $eventType,
            'external_id' => $externalId ? substr((string) $externalId, 0, 190) : null,
            'payload' => $payload,
            'signature_valid' => $signatureValid,
            'processed_at' => now(),
        ]);

        if ($statuses && !empty($statuses['id'])) {
            MessageLog::where('provider_message_id', $statuses['id'])
                ->update(['status' => $statuses['status'] ?? 'unknown']);
        }

        return $event;
    }

    public function status(): array
    {
        $status = $this->settings->status('whatsapp');
        $status['webhook_configured'] = $this->isWebhookConfigured();
        $status['webhook_url'] = url('/api/webhooks/whatsapp');

        return $status;
    }

    private function sendMessage(string $to, array $message, ?string $templateKey = null): array
    {
        if (!$this->isEnabled()) {
            return ['status' => 'skipped', 'message' => 'WhatsApp integration is disabled.'];
        }

        if (!$this->isConfigured()) {
            $this->logger->record('whatsapp', $to, 'not_configured', 'whatsapp_cloud', $templateKey);

            return ['status' => 'not_configured', 'message' => 'WhatsApp credentials are not configured.'];
        }

        $version = (string) $this->settings->get('whatsapp.api_version', 'v20.0');
        $phoneNumberId = (string) $this->settings->get('whatsapp.phone_number_id');
        $accessToken = (string) $this->settings->get('whatsapp.access_token');
        $url = 'https://graph.facebook.com/' . trim($version, '/') . '/' . $phoneNumberId . '/messages';

        try {
            $response = Http::timeout(20)
                ->withToken($accessToken)
                ->post($url, array_merge([
                    'messaging_product' => 'whatsapp',
                    'recipient_type' => 'individual',
                    'to' => $this->normalizePhone($to),
                ], $message));

            $json = $response->json();

            if ($response->successful()) {
                $providerId = $json['messages'][0]['id'] ?? null;
                $this->logger->record('whatsapp', $to, 'sent', 'whatsapp_cloud', $templateKey, $providerId);

                return ['status' => 'sent', 'message' => 'WhatsApp message sent.', 'provider_id' => $providerId];
            }

            $error = $json['error']['message'] ?? ('WhatsApp API returned HTTP ' . $response->status());
            $error = $accessToken !== '' ? str_replace($accessToken, '[redacted]', $error) : $error;
            $errorCode = isset($json['error']['code']) ? (string) $json['error']['code'] : 'http_' . $response->status();
            $this->logger->record('whatsapp', $to, 'failed', 'whatsapp_cloud', $templateKey, null, $errorCode, $error);

            return ['status' => 'failed', 'message' => $this->settings->sanitizeMessage($error)];
        } catch (\Throwable $e) {
            $this->logger->record('whatsapp', $to, 'failed', 'whatsapp_cloud', $templateKey, null, 'connection_error', 'WhatsApp API connection failed.');
            $this->logger->safeLog('warning', 'WhatsApp delivery failed', ['exception' => get_class($e)]);

            return ['status' => 'failed', 'message' => 'WhatsApp API connection failed.'];
        }
    }

    private function normalizePhone(string $to): string
    {
        return preg_replace('/\D+/', '', $to) ?: $to;
    }
}
