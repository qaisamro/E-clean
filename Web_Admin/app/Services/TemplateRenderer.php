<?php

namespace App\Services;

use App\Models\NotificationTemplate;

class TemplateRenderer
{
    public function find(string $channel, string $key): ?NotificationTemplate
    {
        return NotificationTemplate::query()
            ->where('channel', $channel)
            ->where('key', $key)
            ->where('active', true)
            ->first();
    }

    public function render(string $channel, string $key, array $variables, string $fallbackSubject = '', string $fallbackBody = ''): array
    {
        $template = $this->find($channel, $key);

        $subject = $template?->subject ?: $fallbackSubject;
        $body = $template?->body ?: $fallbackBody;

        return [
            'subject' => $this->replace($subject, $variables),
            'body' => $this->replace($body, $variables),
            'template' => $template,
        ];
    }

    public function replace(string $text, array $variables): string
    {
        foreach ($variables as $key => $value) {
            if (is_array($value) || is_object($value)) {
                continue;
            }
            $text = str_replace(['{{' . $key . '}}', '{' . $key . '}', '{{ ' . $key . ' }}'], (string) $value, $text);
        }

        // Remove unresolved placeholders so an OTP can never be sent as a
        // literal "{{code}}" because of a typo in a template.
        return preg_replace('/\{\{\s*[\w.]+\s*\}\}/', '', $text) ?? $text;
    }
}
