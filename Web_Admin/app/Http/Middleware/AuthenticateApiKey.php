<?php

namespace App\Http\Middleware;

use App\Services\ApiKeyService;
use App\Services\IntegrationSettingsService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateApiKey
{
    public function __construct(
        private ApiKeyService $keys,
        private IntegrationSettingsService $settings
    ) {
    }

    public function handle(Request $request, Closure $next, string ...$requiredScopes): Response
    {
        if (!$this->settings->isEnabled('api')) {
            return $this->error('API access is disabled.', 503);
        }

        $token = $this->tokenFrom($request);
        if ($token === '') {
            return $this->error('API key required.', 401);
        }

        $apiKey = $this->keys->findByToken($token);
        if (!$apiKey) {
            return $this->error('Invalid or expired API key.', 401);
        }

        foreach ($requiredScopes as $scope) {
            $scope = trim($scope);
            if ($scope !== '' && !$apiKey->hasScope($scope)) {
                return $this->error('This API key does not have the required scope: ' . $scope, 403);
            }
        }

        $max = max(1, (int) $this->settings->get('api.rate_limit_per_minute', 60));
        $bucket = 'api-key:' . $apiKey->id;

        if (RateLimiter::tooManyAttempts($bucket, $max)) {
            return $this->error('Too many requests.', 429, [
                'Retry-After' => (string) RateLimiter::availableIn($bucket),
            ]);
        }

        RateLimiter::hit($bucket, 60);
        $apiKey->forceFill(['last_used_at' => now()])->saveQuietly();

        $request->attributes->set('api_key', $apiKey);

        return $next($request);
    }

    private function tokenFrom(Request $request): string
    {
        $header = (string) $request->header('X-API-Key', '');
        if ($header !== '') {
            return $header;
        }

        $bearer = $request->bearerToken();

        return $bearer ?: '';
    }

    private function error(string $message, int $status, array $headers = []): Response
    {
        return response()->json(['message' => $message], $status, $headers);
    }
}
