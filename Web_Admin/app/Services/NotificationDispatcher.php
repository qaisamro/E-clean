<?php

namespace App\Services;

use App\Events\UserMailEvent;
use Illuminate\Support\Facades\Event;

class NotificationDispatcher
{
    public function __construct(
        private IntegrationSettingsService $settings,
        private SmsService $sms,
        private MailIntegrationService $mail,
        private WhatsAppService $whatsapp,
        private TemplateRenderer $templates
    ) {
    }

    public function sendOtp($user, string $contact, string $purpose, string $code): array
    {
        $channels = $this->channelsFor($purpose, $contact);
        $results = [];
        $isEmail = filter_var($contact, FILTER_VALIDATE_EMAIL);
        $ttl = max(1, (int) $this->settings->get('otp.ttl', 300));
        $variables = [
            'code' => $code,
            'otp' => $code,
            'name' => $user->name ?? $user->first_name ?? '',
            'app' => config('app.name'),
            'minutes' => (string) ceil($ttl / 60),
        ];

        foreach ($channels as $channel) {
            if ($channel === 'email' && !$isEmail) {
                continue;
            }

            if ($channel === 'sms' && $isEmail) {
                continue;
            }

            if ($channel === 'whatsapp' && $isEmail) {
                continue;
            }

            $results[$channel] = match ($channel) {
                'sms' => $this->sms->sendTemplate($contact, 'otp_' . $purpose, $variables, $this->smsFallback($purpose, $variables)),
                'whatsapp' => $this->whatsapp->sendLocalTemplate($contact, 'otp_' . $purpose, $variables),
                'email' => $this->mail->sendTemplate(
                    $contact,
                    'otp_' . $purpose,
                    $variables,
                    'Elite Clean verification code',
                    $this->emailFallback($variables)
                ),
                default => ['status' => 'skipped', 'message' => 'Unknown channel.'],
            };
        }

        // Preserve the existing application email flow when the integration
        // center has no SMTP configuration yet. The listener itself checks
        // whether env mail credentials exist.
        $emailResult = $results['email'] ?? null;
        if ($user && !empty($user->email) && $isEmail && ($emailResult === null || in_array($emailResult['status'], ['skipped', 'not_configured'], true))) {
            try {
                Event::dispatch(new UserMailEvent($user, $code));
                $results['email'] = ['status' => 'queued', 'message' => 'Sent through the legacy mail event.'];
            } catch (\Throwable $e) {
                $results['email'] = ['status' => 'failed', 'message' => 'Email delivery failed.'];
            }
        }

        return $results;
    }

    public function channelsFor(string $purpose, string $contact): array
    {
        $key = 'otp.channels_' . $purpose;
        $sentinel = '__ec_missing__';
        $configured = $this->settings->get($key, $sentinel);

        // An explicitly empty selection means "no channel". A missing row
        // uses the schema default so existing installations keep working.
        $explicitEmpty = $configured === '' || $configured === [];
        if ($configured === $sentinel) {
            $configured = \App\Support\IntegrationSchema::defaults('otp')['channels_' . $purpose] ?? 'email';
        }

        if (is_array($configured)) {
            $channels = $configured;
        } else {
            $configured = (string) $configured;
            // Older installations stored a JSON array; current UI stores CSV.
            if (str_starts_with(trim($configured), '[')) {
                $decoded = json_decode($configured, true);
                $channels = is_array($decoded) ? $decoded : [];
            } else {
                $channels = explode(',', $configured);
            }
        }

        $channels = array_values(array_filter(array_map(
            fn ($channel) => strtolower(trim((string) $channel)),
            $channels
        )));
        $allowed = ['email', 'sms', 'whatsapp'];
        $channels = array_values(array_intersect($channels, $allowed));

        if (empty($channels) && !$explicitEmpty) {
            $channels = [filter_var($contact, FILTER_VALIDATE_EMAIL) ? 'email' : 'sms'];
        }

        return $channels;
    }

    public function shouldShowInApp(string $contact): bool
    {
        if (!in_array(app()->environment(), ['local', 'staging', 'testing'], true)) {
            return false;
        }

        if ((string) $this->settings->get('otp.show_in_app', '0') !== '1') {
            return false;
        }

        $allowed = array_filter(array_map('trim', explode(',', (string) $this->settings->get('otp.test_contacts', ''))));

        return in_array($contact, $allowed, true);
    }

    private function smsFallback(string $purpose, array $variables): string
    {
        return 'Elite Clean: your verification code is ' . $variables['code'] . '. Valid for ' . $variables['minutes'] . ' minutes.';
    }

    private function emailFallback(array $variables): string
    {
        return "Elite Clean verification code\n\n"
            . 'Your verification code is: ' . $variables['code'] . "\n"
            . 'It is valid for ' . $variables['minutes'] . " minutes.\n\n"
            . 'If you did not request this code, ignore this email.';
    }
}
