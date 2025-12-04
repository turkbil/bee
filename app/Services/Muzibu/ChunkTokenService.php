<?php

namespace App\Services\Muzibu;

use Illuminate\Support\Facades\Log;

/**
 * Chunk Token Service
 *
 * HLS chunk'lar1 için signed token olu_turur ve dorular
 * Her chunk istei için unique token (song_id + chunk_name + user_id + expiry)
 */
class ChunkTokenService
{
    /**
     * Chunk token olu_tur
     *
     * @param int $songId
     * @param string $chunkName segment-000.ts format
     * @param \App\Models\User|null $user
     * @param int $expiryMinutes Token geçerlilik süresi (dakika)
     * @return string Base64 encoded token
     */
    public function generateChunkToken(
        int $songId,
        string $chunkName,
        $user,
        int $expiryMinutes = 15
    ): string {
        $payload = [
            'song_id' => $songId,
            'chunk_name' => $chunkName,
            'user_id' => $user ? $user->id : null,
            'is_premium' => $user ? $user->isPremium() : false,
            'expires_at' => now()->addMinutes($expiryMinutes)->timestamp,
            'issued_at' => now()->timestamp,
        ];

        // HMAC-SHA256 ile imzala
        $signature = hash_hmac(
            'sha256',
            json_encode($payload),
            config('app.key')
        );

        // Payload + Signature birle_tir
        $token = base64_encode(
            json_encode($payload) . '|' . $signature
        );

        return $token;
    }

    /**
     * Chunk token dorula
     *
     * @param string $token
     * @return array|null Token payload (null = geçersiz)
     */
    public function validateChunkToken(string $token): ?array
    {
        try {
            // Base64 decode
            $decoded = base64_decode($token);

            if (!$decoded) {
                return null;
            }

            // Payload ve signature ay1r
            $parts = explode('|', $decoded, 2);

            if (count($parts) !== 2) {
                return null;
            }

            [$payloadJson, $signature] = $parts;

            // 0mza dorulama
            $expectedSignature = hash_hmac(
                'sha256',
                $payloadJson,
                config('app.key')
            );

            if (!hash_equals($expectedSignature, $signature)) {
                Log::warning('Chunk token: Invalid signature');
                return null;
            }

            // Payload parse
            $payload = json_decode($payloadJson, true);

            if (!$payload) {
                return null;
            }

            // Expiry check
            if ($payload['expires_at'] < time()) {
                Log::info('Chunk token: Expired', [
                    'song_id' => $payload['song_id'] ?? null,
                    'chunk_name' => $payload['chunk_name'] ?? null
                ]);
                return null;
            }

            return $payload;

        } catch (\Exception $e) {
            Log::error('Chunk token validation error', [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Chunk index'i user için izin verilen max index ile kar_1la_t1r
     *
     * @param string $chunkName segment-005.ts
     * @param bool $isPremium
     * @return bool 0zin var m1?
     */
    public function isChunkAllowed(string $chunkName, bool $isPremium): bool
    {
        // Premium user -> tüm chunk'lara eri_im var
        if ($isPremium) {
            return true;
        }

        // Guest/Normal user -> sadece ilk 4 chunk (0, 1, 2, 3)
        $chunkIndex = $this->extractChunkIndex($chunkName);

        // 0-3 aras1 izinli (4 chunk: 3 çalacak + 1 buffer)
        return $chunkIndex <= 3;
    }

    /**
     * Chunk index'i chunk dosya ad1ndan ç1kar
     * segment-005.ts -> 5
     *
     * @param string $chunkName
     * @return int
     */
    protected function extractChunkIndex(string $chunkName): int
    {
        if (preg_match('/segment-(\d+)\.ts/', $chunkName, $matches)) {
            return (int) $matches[1];
        }

        return 0;
    }
}
