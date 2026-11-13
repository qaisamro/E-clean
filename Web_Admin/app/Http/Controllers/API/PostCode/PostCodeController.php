<?php

namespace App\Http\Controllers\API\PostCode;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

class PostCodeController extends Controller
{
    public function index()
    {
        $postcode = request()->query('postcode');
        if (!is_string($postcode) || strlen($postcode) > 64) {
            return $this->json('Invalid postcode', [], Response::HTTP_BAD_REQUEST);
        }

        $postcode = trim($postcode);
        if ($postcode === '' || !preg_match('/^[A-Za-z0-9][A-Za-z0-9 -]*$/', $postcode)) {
            return $this->json('Invalid postcode', [], Response::HTTP_BAD_REQUEST);
        }

        $prefixes = (array) config('enums.post_code', []);
        $matchedPrefix = '';
        foreach ([substr($postcode, 0, 2), substr($postcode, 0, 3), substr($postcode, 0, 4)] as $prefix) {
            if (in_array($prefix, $prefixes, true)) {
                $matchedPrefix = $prefix;
            }
        }

        if ($matchedPrefix === '') {
            return $this->json('Invalid postcode', [], Response::HTTP_BAD_REQUEST);
        }

        $apiKey = config('services.ideal_postcodes.api_key');
        if (!is_string($apiKey) || trim($apiKey) === '') {
            return $this->json('Postcode lookup is unavailable', [], Response::HTTP_SERVICE_UNAVAILABLE);
        }

        try {
            $response = Http::timeout(8)->get(
                'https://api.ideal-postcodes.co.uk/v1/postcodes/' . rawurlencode($postcode),
                ['api_key' => $apiKey]
            );
        } catch (\Throwable $exception) {
            return $this->json('Postcode lookup is unavailable', [], Response::HTTP_BAD_GATEWAY);
        }

        if ($response->successful()) {
            return $this->json('Post Code List', [$response->json()]);
        }

        return $this->json('Postcode lookup failed', [], Response::HTTP_BAD_GATEWAY);
    }
}
