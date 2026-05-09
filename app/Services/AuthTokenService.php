<?php

namespace App\Services;

use App\Models\User;

class AuthTokenService
{
    public function issue(User $user): string
    {
        $header = $this->base64UrlEncode(json_encode(['alg' => 'HS256', 'typ' => 'JWT'], JSON_THROW_ON_ERROR));
        $payload = $this->base64UrlEncode(json_encode([
            'sub' => $user->id,
            'role' => $user->role,
            'iat' => time(),
            'exp' => time() + 86400,
        ], JSON_THROW_ON_ERROR));

        return $header.'.'.$payload.'.'.$this->sign($header.'.'.$payload);
    }

    public function userFromToken(?string $token): ?User
    {
        if (! $token || substr_count($token, '.') !== 2) {
            return null;
        }

        [$header, $payload, $signature] = explode('.', $token);

        if (! hash_equals($this->sign($header.'.'.$payload), $signature)) {
            return null;
        }

        $claims = json_decode($this->base64UrlDecode($payload), true);

        if (! is_array($claims) || ($claims['exp'] ?? 0) < time()) {
            return null;
        }

        return User::find($claims['sub'] ?? null);
    }

    private function sign(string $value): string
    {
        return $this->base64UrlEncode(hash_hmac('sha256', $value, (string) config('app.key'), true));
    }

    private function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    private function base64UrlDecode(string $value): string
    {
        return base64_decode(strtr($value, '-_', '+/')) ?: '';
    }
}
