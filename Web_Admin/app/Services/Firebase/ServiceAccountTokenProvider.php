<?php

namespace App\Services\Firebase;

use Illuminate\Support\Facades\Http;

class ServiceAccountTokenProvider
{
    /**
     * Exchange a Google service-account JSON for a short-lived OAuth token.
     * Returns null when the credentials cannot be signed or Google rejects
     * the request. The private key is never included in an exception message.
     */
    public function fetch(array $credentials): ?string
    {
        $clientEmail = (string) ($credentials['client_email'] ?? '');
        $privateKey = (string) ($credentials['private_key'] ?? '');

        if ($clientEmail === '' || $privateKey === '') {
            return null;
        }

        $now = time();
        $header = $this->base64Url(json_encode([
            'alg' => 'RS256',
            'typ' => 'JWT',
        ], JSON_UNESCAPED_SLASHES));

        $claims = $this->base64Url(json_encode([
            'iss' => $clientEmail,
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => $now,
            'exp' => $now + 3600,
        ], JSON_UNESCAPED_SLASHES));

        $input = $header . '.' . $claims;
        $signature = '';

        if (!@openssl_sign($input, $signature, $privateKey, OPENSSL_ALGO_SHA256)) {
            return null;
        }

        $jwt = $input . '.' . $this->base64Url($signature);

        try {
            $response = Http::asForm()
                ->timeout(15)
                ->post('https://oauth2.googleapis.com/token', [
                    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                    'assertion' => $jwt,
                ]);

            if (!$response->successful()) {
                return null;
            }

            $token = (string) $response->json('access_token', '');

            return $token !== '' ? $token : null;
        } catch (\Throwable) {
            return null;
        }
    }

    private function base64Url(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
