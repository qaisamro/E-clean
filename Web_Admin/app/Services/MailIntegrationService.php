<?php

namespace App\Services;

use App\Services\Smtp\SmtpProbe;
use Illuminate\Support\Facades\Mail;

class MailIntegrationService
{
    public function __construct(
        private IntegrationSettingsService $settings,
        private MessageLogger $logger,
        private TemplateRenderer $templates,
        private SmtpProbe $smtpProbe
    ) {
    }

    public function isEnabled(): bool
    {
        return $this->settings->isEnabled('smtp');
    }

    public function isConfigured(): bool
    {
        return $this->settings->isConfigured('smtp');
    }

    /**
     * Apply the integration-center SMTP settings to Laravel's mail config.
     * Returns true when the runtime mailer was configured.
     */
    public function applyConfiguration(bool $force = false): bool
    {
        if (!$force && !$this->isEnabled()) {
            return false;
        }

        if (!$this->isConfigured()) {
            return false;
        }

        $encryption = (string) $this->settings->get('smtp.encryption', 'tls');

        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.host' => (string) $this->settings->get('smtp.host'),
            'mail.mailers.smtp.port' => (int) $this->settings->get('smtp.port', 587),
            'mail.mailers.smtp.encryption' => $encryption === 'none' ? null : $encryption,
            'mail.mailers.smtp.username' => (string) $this->settings->get('smtp.username'),
            'mail.mailers.smtp.password' => (string) $this->settings->get('smtp.password'),
            'mail.mailers.smtp.timeout' => (int) $this->settings->get('smtp.timeout', 15),
            'mail.from.address' => (string) $this->settings->get('smtp.from_address'),
            'mail.from.name' => (string) $this->settings->get('smtp.from_name', config('app.name')),
        ]);

        Mail::purge('smtp');

        return true;
    }

    public function send(string $to, string $subject, string $body, ?string $templateKey = null): array
    {
        if (!$this->isEnabled()) {
            return ['status' => 'skipped', 'message' => 'SMTP integration is disabled.'];
        }

        if (!$this->isConfigured()) {
            $this->logger->record('email', $to, 'not_configured', 'smtp', $templateKey);

            return ['status' => 'not_configured', 'message' => 'SMTP is not configured.'];
        }

        $this->applyConfiguration(true);

        try {
            Mail::raw($body, function ($message) use ($to, $subject) {
                $message->to($to)->subject($subject);
            });

            $this->logger->record('email', $to, 'sent', 'smtp', $templateKey);

            return ['status' => 'sent', 'message' => 'Email sent.'];
        } catch (\Throwable $e) {
            $this->logger->record(
                'email',
                $to,
                'failed',
                'smtp',
                $templateKey,
                null,
                'smtp_error',
                'SMTP delivery failed.'
            );
            $this->logger->safeLog('warning', 'SMTP delivery failed', ['exception' => get_class($e)]);

            return ['status' => 'failed', 'message' => 'SMTP delivery failed.'];
        }
    }

    public function sendTemplate(string $to, string $templateKey, array $variables, string $fallbackSubject = '', string $fallbackBody = ''): array
    {
        $rendered = $this->templates->render('email', $templateKey, $variables, $fallbackSubject, $fallbackBody);

        return $this->send($to, $rendered['subject'], $rendered['body'], $templateKey);
    }

    public function test(string $to): array
    {
        return $this->sendTemplate(
            $to,
            'test',
            ['app' => config('app.name'), 'time' => now()->toDateTimeString()],
            'Elite Clean SMTP test',
            "This is a test email from Elite Clean.\nTime: {{time}}"
        );
    }

    /**
     * Non-destructive SMTP check: connect, negotiate TLS and authenticate,
     * but never issue MAIL FROM / RCPT TO / DATA.
     */
    public function testConnection(): array
    {
        if (!$this->isEnabled()) {
            return ['status' => 'skipped', 'message' => 'SMTP integration is disabled.'];
        }

        if (!$this->isConfigured()) {
            return ['status' => 'not_configured', 'message' => 'SMTP is not configured.'];
        }

        return $this->smtpProbe->test([
            'host' => (string) $this->settings->get('smtp.host'),
            'port' => (int) $this->settings->get('smtp.port', 587),
            'encryption' => (string) $this->settings->get('smtp.encryption', 'tls'),
            'username' => (string) $this->settings->get('smtp.username'),
            'password' => (string) $this->settings->get('smtp.password'),
            'timeout' => (int) $this->settings->get('smtp.timeout', 15),
        ]);
    }

    public function status(): array
    {
        return $this->settings->status('smtp');
    }
}
