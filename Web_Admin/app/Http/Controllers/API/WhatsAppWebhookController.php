<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\IntegrationSettingsService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class WhatsAppWebhookController extends Controller
{
    public function __construct(
        private WhatsAppService $whatsapp,
        private IntegrationSettingsService $settings
    ) {
    }

    public function verify(Request $request)
    {
        $mode = (string) $request->query('hub_mode', $request->query('hub.mode', ''));
        $token = (string) $request->query('hub_verify_token', $request->query('hub.verify_token', ''));
        $challenge = (string) $request->query('hub_challenge', $request->query('hub.challenge', ''));

        $expected = (string) $this->settings->get('whatsapp.webhook_verify_token', '');

        if ($mode === 'subscribe' && $expected !== '' && hash_equals($expected, $token)) {
            return response($challenge, 200)->header('Content-Type', 'text/plain');
        }

        return response()->json(['message' => 'Forbidden'], 403);
    }

    public function handle(Request $request)
    {
        if (!$this->whatsapp->isEnabled() || !$this->settings->get('whatsapp.webhook_enabled', '1')) {
            return response()->json(['message' => 'WhatsApp webhooks are disabled.'], 503);
        }

        if (!$this->whatsapp->isWebhookConfigured()) {
            return response()->json(['message' => 'Webhook signature secret is not configured.'], 503);
        }

        $raw = $request->getContent();
        $signature = $request->header('X-Hub-Signature-256');
        $valid = $this->whatsapp->verifySignature($raw, $signature);

        if (!$valid) {
            return response()->json(['message' => 'Invalid signature.'], 403);
        }

        $payload = json_decode($raw, true);
        if (!is_array($payload)) {
            return response()->json(['message' => 'Invalid payload.'], 400);
        }

        $this->whatsapp->processWebhook($payload, true);

        return response()->json(['message' => 'ok']);
    }
}
