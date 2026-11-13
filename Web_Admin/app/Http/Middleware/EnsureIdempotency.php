<?php

namespace App\Http\Middleware;

use App\Models\IdempotencyKey;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class EnsureIdempotency
{
    public function handle(Request $request, Closure $next): Response
    {
        $idempotencyKey = (string) ($request->header('Idempotency-Key') ?: $request->header('X-Idempotency-Key'));
        $apiKey = $request->attributes->get('api_key');

        if ($idempotencyKey === '' || !$apiKey) {
            return $next($request);
        }

        if (strlen($idempotencyKey) > 191) {
            return response()->json(['message' => 'Idempotency-Key is too long.'], 422);
        }

        $requestHash = hash('sha256', $request->getMethod() . '|' . $request->path() . '|' . $request->getContent());
        $lock = Cache::lock('idempotency:' . $apiKey->id . ':' . sha1($idempotencyKey), 30);

        if (!$lock->get()) {
            return response()->json(['message' => 'A request with this Idempotency-Key is already in progress.'], 409);
        }

        try {
            $existing = IdempotencyKey::where('api_key_id', $apiKey->id)
                ->where('idempotency_key', $idempotencyKey)
                ->first();

            if ($existing && $existing->expires_at !== null && $existing->expires_at->isPast()) {
                $existing->delete();
                $existing = null;
            }

            if ($existing) {
                if (!hash_equals($existing->request_hash, $requestHash)) {
                    return response()->json(['message' => 'Idempotency-Key was already used with a different request.'], 422);
                }

                if ($existing->response_code === null) {
                    return response()->json(['message' => 'The original request is still being processed.'], 409);
                }

                return response($existing->response_body, $existing->response_code, [
                    'Content-Type' => 'application/json',
                    'Idempotent-Replayed' => 'true',
                ]);
            }

            $response = $next($request);

            if ($response->getStatusCode() < 500) {
                $ttl = max(60, (int) app(\App\Services\IntegrationSettingsService::class)->get('api.idempotency_ttl', 86400));

                IdempotencyKey::create([
                    'api_key_id' => $apiKey->id,
                    'idempotency_key' => $idempotencyKey,
                    'request_hash' => $requestHash,
                    'response_code' => $response->getStatusCode(),
                    'response_body' => $response->getContent(),
                    'expires_at' => now()->addSeconds($ttl),
                ]);
            }

            return $response;
        } finally {
            $lock->release();
        }
    }
}
