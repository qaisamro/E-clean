<?php

namespace App\Services;

/**
 * Backwards-compatible facade used by older controllers.
 * New code should depend on FirebaseMessagingService directly.
 */
class NotificationServices
{
    public function __construct()
    {
    }

    public function send(string $deviceToken, string $title, string $message): void
    {
        try {
            app(FirebaseMessagingService::class)->send($deviceToken, $title, $message);
        } catch (\Throwable $e) {
            app(MessageLogger::class)->safeLog('warning', 'Push notification failed', ['exception' => get_class($e)]);
        }
    }

    /**
     * Send a push notification to many devices.
     *
     * Returns the delivery result from FirebaseMessagingService so callers can
     * tell the admin exactly what happened (sent / failed / skipped / reason).
     */
    public function sendNotification(string $message, array $deviceTokens, string $title): array
    {
        try {
            return app(FirebaseMessagingService::class)->sendMany($deviceTokens, $title, $message);
        } catch (\Throwable $e) {
            app(MessageLogger::class)->safeLog('warning', 'Push notification failed', ['exception' => get_class($e)]);

            return [
                'status' => 'error',
                'message' => 'Push notification failed: ' . $e->getMessage(),
                'provider_id' => null,
                'success' => 0,
                'failed' => count($deviceTokens),
            ];
        }
    }
}
