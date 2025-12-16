<?php

declare(strict_types=1);

namespace Modules\Muzibu\App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Muzibu\App\Models\Song;
use Modules\Muzibu\App\Models\Playlist;
use Modules\Muzibu\App\Models\Album;
use Modules\Muzibu\App\Models\Radio;

/**
 * 🎵 Play Controller - AI Assistant Play Actions
 *
 * Handles play requests for songs, playlists, albums, and radios from AI Assistant
 */
class PlayController extends Controller
{
    /**
     * 🎵 Play Content (Song, Playlist, Album, Radio)
     *
     * @param Request $request
     * @param string $type
     * @param int $id
     * @return JsonResponse
     */
    public function play(Request $request, string $type, int $id): JsonResponse
    {
        try {
            // Type validation (already validated in routes, but double-check)
            $validTypes = ['song', 'playlist', 'album', 'radio'];
            if (!in_array($type, $validTypes)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Geçersiz içerik tipi',
                ], 400);
            }

            // Get content based on type
            $content = match($type) {
                'song' => Song::findOrFail($id),
                'playlist' => Playlist::with('songs')->findOrFail($id),
                'album' => Album::with('songs')->findOrFail($id),
                'radio' => Radio::with('sectors')->findOrFail($id), // ✅ Load sectors relationship
            };

            // Build play data
            $playData = $this->buildPlayData($type, $content);

            \Log::info('🎵 AI Play Content', [
                'type' => $type,
                'id' => $id,
                'title' => $playData['title'] ?? 'Unknown',
                'user_id' => auth('sanctum')->id() ?? auth('web')->id(),
            ]);

            return response()->json([
                'success' => true,
                'data' => $playData
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => ucfirst($type) . ' bulunamadı',
            ], 404);
        } catch (\Exception $e) {
            \Log::error('AI Play Content Error', [
                'error' => $e->getMessage(),
                'type' => $type,
                'id' => $id,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'İçerik çalınamadı: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * 🎵 Queue'ya Şarkı Ekle (Toplu)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function addToQueue(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'song_ids' => 'required|array|min:1',
                'song_ids.*' => 'required|integer|exists:songs,id',
                'play_now' => 'nullable|boolean', // İlk şarkıyı hemen çal
                'clear_queue' => 'nullable|boolean', // Mevcut queue'yu temizle
            ]);

            // Session'a queue kaydet (frontend'de işlenecek)
            $userId = auth('sanctum')->id() ?? auth('web')->id();
            $sessionKey = 'ai_queue_' . ($userId ?? session()->getId());

            $currentQueue = $validated['clear_queue'] ?? false
                ? []
                : session()->get($sessionKey, []);

            $newQueue = array_merge($currentQueue, $validated['song_ids']);
            session()->put($sessionKey, array_unique($newQueue));

            // Şarkı bilgilerini al
            $songs = Song::whereIn('id', $validated['song_ids'])
                ->get(['id', 'song_title', 'artist_title', 'album_title', 'duration'])
                ->map(function ($song) {
                    return [
                        'id' => $song->id,
                        'title' => $song->title,
                        'artist' => $song->artist_name,
                        'album' => $song->album_name,
                        'duration' => $song->duration,
                    ];
                });

            \Log::info('🎵 AI Songs Added to Queue', [
                'added_count' => count($validated['song_ids']),
                'total_queue' => count($newQueue),
                'play_now' => $validated['play_now'] ?? false,
                'user_id' => $userId,
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'queue_count' => count($newQueue),
                    'added_count' => count($validated['song_ids']),
                    'songs' => $songs,
                    'play_now' => $validated['play_now'] ?? false,
                    'message' => "✅ " . count($validated['song_ids']) . " şarkı sıraya eklendi!"
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('AI Add to Queue Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Sıraya eklenirken hata oluştu: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Build play data based on content type
     *
     * @param string $type
     * @param mixed $content
     * @return array
     */
    private function buildPlayData(string $type, $content): array
    {
        return match($type) {
            'song' => [
                'type' => 'song',
                'song_id' => $content->id,
                'title' => $content->title,
                'artist' => $content->artist_name,
                'album' => $content->album_name,
                'duration' => $content->duration,
                'stream_url' => route('api.muzibu.songs.stream', $content->id),
                'play_url' => route('muzibu.song.play', $content->slug),
            ],
            'playlist' => [
                'type' => 'playlist',
                'playlist_id' => $content->id,
                'title' => $content->title,
                'description' => $content->description,
                'song_count' => $content->songs->count(),
                'song_ids' => $content->songs->pluck('id')->toArray(),
                'songs' => $content->songs->map(fn($song) => [
                    'id' => $song->id,
                    'title' => $song->title,
                    'artist' => $song->artist_name,
                    'duration' => $song->duration,
                ])->toArray(),
                'play_url' => route('muzibu.playlist.show', $content->slug),
            ],
            'album' => [
                'type' => 'album',
                'album_id' => $content->id,
                'title' => $content->title,
                'artist' => $content->artist_name,
                'song_count' => $content->songs->count(),
                'song_ids' => $content->songs->pluck('id')->toArray(),
                'songs' => $content->songs->map(fn($song) => [
                    'id' => $song->id,
                    'title' => $song->title,
                    'duration' => $song->duration,
                ])->toArray(),
                'play_url' => route('muzibu.album.show', $content->slug),
            ],
            'radio' => [
                'type' => 'radio',
                'radio_id' => $content->id,
                'title' => $content->title,
                'description' => $content->description,
                'sectors' => $content->sectors->pluck('title')->toArray(), // ✅ Radio doesn't have genre, only sectors (belongsToMany)
                'play_url' => route('muzibu.radio.show', $content->slug),
            ],
        };
    }
}
