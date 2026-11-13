<?php

namespace App\Repositories;

use App\Services\SmsService;

/**
 * Backwards-compatible facade. New code should depend on SmsService.
 */
class SMS
{
    public function sendSms($mobile, $message)
    {
        $result = app(SmsService::class)->send((string) $mobile, (string) $message, 'legacy');

        return $result['status'] === 'sent';
    }

    public function testConnection(string $to, string $message = 'E-Clean SMS test'): bool
    {
        $result = app(SmsService::class)->send($to, $message, 'test');

        return $result['status'] === 'sent';
    }
}
