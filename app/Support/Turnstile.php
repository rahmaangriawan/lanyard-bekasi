<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use Throwable;

final class Turnstile
{
    public static function verify(string $token, ?string $ipAddress, string $secretKey): bool
    {
        if ($token === '' || $secretKey === '') {
            return false;
        }

        try {
            $response = Http::asForm()
                ->timeout(8)
                ->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                    'secret' => $secretKey,
                    'response' => $token,
                    'remoteip' => $ipAddress,
                ]);

            return $response->successful() && $response->json('success') === true;
        } catch (Throwable $exception) {
            report($exception);

            return false;
        }
    }
}
