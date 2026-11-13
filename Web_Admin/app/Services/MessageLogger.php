<?php

namespace App\Services;

use App\Models\MessageLog;
use Illuminate\Support\Facades\Log;

class MessageLogger
{
    public function recipientHash(string $recipient): string
    {
        return hash('sha256', $this->normalizeRecipient($recipient));
    }

    public function maskRecipient(string $recipient): string
    {
        $recipient = trim($recipient);

        if (str_contains($recipient, '@')) {
            [$local, $domain] = explode('@', $recipient, 2);
            $local = strlen($local) <= 2 ? substr($local, 0, 1) . '*' : substr($local, 0, 2) . '***';

            return $local . '@' . $domain;
        }

        $digits = preg_replace('/\D+/', '', $recipient) ?: $recipient;

        return str_repeat('*', max(0, strlen($digits) - 4)) . substr($digits, -4);
    }

    public function record(
        string $channel,
        string $recipient,
        string $status,
        ?string $provider = null,
        ?string $templateKey = null,
        ?string $providerMessageId = null,
        ?string $errorCode = null,
        ?string $errorMessage = null,
        array $meta = []
    ): MessageLog {
        return MessageLog::create([
            'channel' => $channel,
            'provider' => $provider,
            'template_key' => $templateKey,
            'recipient_masked' => $this->maskRecipient($recipient),
            'recipient_hash' => $this->recipientHash($recipient),
            'status' => $status,
            'provider_message_id' => $providerMessageId ? substr((string) $providerMessageId, 0, 255) : null,
            'error_code' => $errorCode ? substr((string) $errorCode, 0, 64) : null,
            'error_message' => $errorMessage ? substr(app(IntegrationSettingsService::class)->sanitizeMessage($errorMessage), 0, 1000) : null,
            'meta' => $meta,
        ]);
    }

    public function safeLog(string $level, string $message, array $context = []): void
    {
        // Never pass credentials, recipient addresses or message bodies to logs.
        Log::{$level}($message, app(AuditLogger::class)->redact($context));
    }

    private function normalizeRecipient(string $recipient): string
    {
        $recipient = trim($recipient);

        if (str_contains($recipient, '@')) {
            return strtolower($recipient);
        }

        $digits = preg_replace('/\D+/', '', $recipient) ?? '';

        return ltrim($digits, '0') ?: $digits;
    }
}
