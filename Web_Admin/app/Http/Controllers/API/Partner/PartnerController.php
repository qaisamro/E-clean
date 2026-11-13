<?php

namespace App\Http\Controllers\API\Partner;

use App\Http\Controllers\Controller;
use App\Services\WebhookService;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    public function ping(Request $request)
    {
        $apiKey = $request->attributes->get('api_key');

        return response()->json([
            'data' => [
                'ok' => true,
                'key' => $apiKey?->name,
                'scopes' => $apiKey?->scopes,
                'time' => now()->toIso8601String(),
            ],
        ]);
    }

    public function echo(Request $request)
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
        ]);

        return response()->json([
            'data' => [
                'message' => $validated['message'],
                'received_at' => now()->toIso8601String(),
            ],
        ]);
    }

    public function testWebhook(Request $request, WebhookService $webhooks)
    {
        $result = $webhooks->dispatchEvent('integration.test', [
            'source' => 'api-key',
            'key' => $request->attributes->get('api_key')?->name,
            'timestamp' => now()->toIso8601String(),
        ]);

        return response()->json(['data' => $result]);
    }
}
