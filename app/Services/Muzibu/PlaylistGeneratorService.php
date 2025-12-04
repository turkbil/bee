<?php

namespace App\Services\Muzibu;

use Illuminate\Support\Facades\Log;

/**
 * Playlist Generator Service
 *
 * User tipine göre dynamic M3U8 playlist olu_turur
 * Guest/Normal User: 4 chunk (3 çal + 1 buffer)
 * Premium User: Tüm chunk'lar
 */
class PlaylistGeneratorService
{
    /**
     * Dynamic playlist olu_tur
     *
     * @param \Modules\Muzibu\App\Models\Song $song
     * @param \App\Models\User|null $user
     * @return string M3U8 playlist content
     */
    public function generatePlaylist($song, $user = null): string
    {
        // Orijinal playlist path
        $playlistPath = storage_path('app/public/' . $song->hls_path);

        if (!file_exists($playlistPath)) {
            Log::error('Playlist file not found', [
                'song_id' => $song->song_id,
                'hls_path' => $song->hls_path
            ]);
            throw new \Exception('Playlist not found');
        }

        // Orijinal playlist oku
        $originalPlaylist = file_get_contents($playlistPath);

        // User tipine göre chunk limiti belirle
        $chunkLimit = $this->getChunkLimit($user);

        // Playlist'i limit'e göre filter et
        $filteredPlaylist = $this->limitChunks($originalPlaylist, $chunkLimit);

        // Chunk URL'lerini signed token ile dei_tir
        $signedPlaylist = $this->signChunkUrls($filteredPlaylist, $song->song_id, $user);

        return $signedPlaylist;
    }

    /**
     * User tipine göre chunk limiti
     *
     * @param \App\Models\User|null $user
     * @return int|null Chunk limit (null = s1n1rs1z)
     */
    protected function getChunkLimit($user): ?int
    {
        // Guest veya Normal User (Premium deil)
        if (!$user || !$user->isPremium()) {
            // 4 chunk = 3 çalacak (30 saniye) + 1 buffer (10 saniye)
            return 4;
        }

        // Premium User - S1n1rs1z
        return null;
    }

    /**
     * Playlist'i chunk limitine göre filtrele
     *
     * @param string $playlist M3U8 content
     * @param int|null $limit Chunk limit (null = limit yok)
     * @return string Filtered playlist
     */
    protected function limitChunks(string $playlist, ?int $limit): string
    {
        // Limit yoksa olduu gibi döndür
        if ($limit === null) {
            return $playlist;
        }

        $lines = explode("\n", $playlist);
        $output = [];
        $chunkCount = 0;
        $inChunk = false;

        foreach ($lines as $line) {
            $line = trim($line);

            // Header sat1rlar1 (playlist ba_1)
            if (str_starts_with($line, '#EXTM3U') ||
                str_starts_with($line, '#EXT-X-VERSION') ||
                str_starts_with($line, '#EXT-X-TARGETDURATION') ||
                str_starts_with($line, '#EXT-X-MEDIA-SEQUENCE') ||
                str_starts_with($line, '#EXT-X-KEY')) {
                $output[] = $line;
                continue;
            }

            // Chunk ba_lang1c1 (#EXTINF)
            if (str_starts_with($line, '#EXTINF')) {
                if ($chunkCount >= $limit) {
                    break; // Limit a_1ld1, dur
                }
                $inChunk = true;
                $chunkCount++;
                $output[] = $line;
                continue;
            }

            // Chunk dosya sat1r1 (segment-XXX.ts)
            if ($inChunk && (str_ends_with($line, '.ts') || str_contains($line, 'segment'))) {
                $output[] = $line;
                $inChunk = false;
                continue;
            }

            // Dier sat1rlar
            if (!empty($line)) {
                $output[] = $line;
            }
        }

        // Playlist sonu ekle
        $output[] = '#EXT-X-ENDLIST';

        Log::info('Playlist filtered', [
            'total_chunks' => $chunkCount,
            'limit' => $limit
        ]);

        return implode("\n", $output);
    }

    /**
     * Chunk URL'lerini signed token ile dei_tir
     *
     * @param string $playlist M3U8 content
     * @param int $songId Song ID
     * @param \App\Models\User|null $user
     * @return string Playlist with signed URLs
     */
    protected function signChunkUrls(string $playlist, int $songId, $user): string
    {
        $chunkTokenService = app(\App\Services\Muzibu\ChunkTokenService::class);

        // segment-000.ts, segment-001.ts vb. tüm chunk dosyalar1n1 bul
        $playlist = preg_replace_callback(
            '/(segment-\d+\.ts)/',
            function($matches) use ($songId, $user, $chunkTokenService) {
                $chunkName = $matches[1];

                // Signed token olu_tur
                $token = $chunkTokenService->generateChunkToken($songId, $chunkName, $user);

                // Tenant domain al
                $domain = tenant() && tenant()->domains->count() > 0
                    ? tenant()->domains->first()->domain
                    : 'muzibu.com.tr';

                // Signed chunk URL
                return "https://{$domain}/api/muzibu/songs/{$songId}/chunk/{$chunkName}?token={$token}";
            },
            $playlist
        );

        return $playlist;
    }

    /**
     * Chunk index'i chunk dosya ad1ndan ç1kar
     * segment-005.ts ’ 5
     *
     * @param string $chunkName
     * @return int
     */
    public function extractChunkIndex(string $chunkName): int
    {
        if (preg_match('/segment-(\d+)\.ts/', $chunkName, $matches)) {
            return (int) $matches[1];
        }

        return 0;
    }
}
