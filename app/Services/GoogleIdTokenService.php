<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleIdTokenService
{
    private const CERTS_URL = 'https://www.googleapis.com/oauth2/v3/certs';
    private const TOKEN_INFO = 'https://oauth2.googleapis.com/tokeninfo';

    protected string $clientId;

    public function __construct()
    {
        $this->clientId = config('services.google.client_id', '');
    }

    /**
     * Verify a Google ID token.
     *
     * Returns:
     *   ['ok' => true,  'payload' => [...]]
     *   ['ok' => false, 'error'   => '...']
     *
     * Uses Google's tokeninfo endpoint — simple and reliable.
     * For high-volume production use, swap to local JWT verification.
     */
    public function verify(string $idToken): array
    {
        try {
            $response = Http::timeout(10)
                ->get(self::TOKEN_INFO, ['id_token' => $idToken]);

            if (!$response->successful()) {
                return ['ok' => false, 'error' => 'Invalid Google ID token.'];
            }

            $payload = $response->json();

            // Validate audience matches our app's client ID
            if (!empty($this->clientId) && ($payload['aud'] ?? '') !== $this->clientId) {
                return ['ok' => false, 'error' => 'Token audience mismatch.'];
            }

            // Check token has not expired
            if (isset($payload['exp']) && $payload['exp'] < time()) {
                return ['ok' => false, 'error' => 'Google token has expired.'];
            }

            // Email must be present and verified
            if (empty($payload['email'])) {
                return ['ok' => false, 'error' => 'Google token missing email.'];
            }

            if (($payload['email_verified'] ?? 'false') === 'false') {
                return ['ok' => false, 'error' => 'Google email not verified.'];
            }

            return ['ok' => true, 'payload' => $payload];

        } catch (\Throwable $e) {
            Log::error('Google token verification failed', ['error' => $e->getMessage()]);
            return ['ok' => false, 'error' => 'Google verification request failed.'];
        }
    }
}
