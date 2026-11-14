<?php

namespace App\Services;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

class GmaspeCaptcha
{
    /** @return array{question: string, token: string, expires_in: int} */
    public function issue(): array
    {
        $left = random_int(2, 9);
        $right = random_int(1, 9);
        $ttl = max(60, (int) config('gmaspe.captcha_ttl_seconds', 600));

        $payload = [
            'answer' => $left + $right,
            'issued_at' => now()->timestamp,
            'nonce' => bin2hex(random_bytes(16)),
        ];

        return [
            'question' => "¿Cuánto es {$left} + {$right}?",
            'token' => Crypt::encryptString(json_encode($payload, JSON_THROW_ON_ERROR)),
            'expires_in' => $ttl,
        ];
    }

    public function verify(string $token, int $answer): bool
    {
        try {
            $payload = json_decode(Crypt::decryptString($token), true, flags: JSON_THROW_ON_ERROR);
        } catch (DecryptException|\JsonException) {
            return false;
        }

        if (! is_array($payload)
            || ! isset($payload['answer'], $payload['issued_at'], $payload['nonce'])
            || ! is_int($payload['answer'])
            || ! is_int($payload['issued_at'])
            || ! is_string($payload['nonce'])) {
            return false;
        }

        $age = now()->timestamp - $payload['issued_at'];
        $ttl = max(60, (int) config('gmaspe.captcha_ttl_seconds', 600));
        $minimumAge = max(0, (int) config('gmaspe.captcha_minimum_seconds', 2));

        if ($age < $minimumAge || $age > $ttl || $answer !== $payload['answer']) {
            return false;
        }

        $cacheKey = 'gmaspe-captcha:'.hash('sha256', $payload['nonce']);

        return Cache::add($cacheKey, true, now()->addSeconds($ttl));
    }
}
