<?php

declare(strict_types=1);

namespace Modules\Muzibu\App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\Muzibu\App\Repositories\PlaylistRepository;
use Modules\Muzibu\App\DataTransferObjects\MuzibuOperationResult;
use Modules\Muzibu\App\Models\Playlist;
use Modules\Muzibu\App\Models\Song;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PlaylistService
{
    public function __construct(
        private PlaylistRepository $repository
    ) {
    }

    public function getPaginatedPlaylists(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $filters);
    }

    public function getActivePlaylists(): Collection
    {
        return $this->repository->getActive();
    }

    public function getPlaylistById(int $id): ?Playlist
    {
        return $this->repository->findByIdWithRelations($id);
    }

    public function preparePlaylistForForm(int $id, string $currentLanguage): array
    {
        $playlist = $this->repository->findByIdWithRelations($id);

        if (!$playlist) {
            return ['playlist' => null, 'tabCompletion' => []];
        }

        $tabCompletion = [];
        $tabCompletion[0] = !empty($playlist->getTranslated('title', $currentLanguage));
        $tabCompletion[1] = $playlist->hasSeoSettings() && !empty($playlist->getSeoFallbackTitle());

        return ['playlist' => $playlist, 'tabCompletion' => $tabCompletion];
    }

    public function createPlaylist(array $data): MuzibuOperationResult
    {
        try {
            $playlist = $this->repository->create($data);
            Log::info('Playlist created', ['playlist_id' => $playlist->playlist_id]);
            return new MuzibuOperationResult(true, __('muzibu::admin.playlist_created'), 'success', $playlist);
        } catch (\Exception $e) {
            Log::error('Playlist creation failed', ['error' => $e->getMessage()]);
            return new MuzibuOperationResult(false, __('muzibu::admin.playlist_creation_failed'), 'error');
        }
    }

    public function updatePlaylist(int $id, array $data): MuzibuOperationResult
    {
        try {
            $updated = $this->repository->update($id, $data);
            if (!$updated) {
                return new MuzibuOperationResult(false, __('muzibu::admin.playlist_not_found'), 'warning');
            }
            Log::info('Playlist updated', ['playlist_id' => $id]);
            return new MuzibuOperationResult(true, __('muzibu::admin.playlist_updated'), 'success', $this->repository->findById($id));
        } catch (\Exception $e) {
            Log::error('Playlist update failed', ['error' => $e->getMessage()]);
            return new MuzibuOperationResult(false, __('muzibu::admin.playlist_update_failed'), 'error');
        }
    }

    public function togglePlaylistStatus(int $id): MuzibuOperationResult
    {
        try {
            $playlist = $this->repository->findById($id);
            if (!$playlist) {
                return new MuzibuOperationResult(false, __('muzibu::admin.playlist_not_found'), 'warning');
            }
            $newStatus = !$playlist->is_active;
            $this->repository->update($id, ['is_active' => $newStatus]);
            return new MuzibuOperationResult(true, $newStatus ? __('muzibu::admin.playlist_activated') : __('muzibu::admin.playlist_deactivated'), 'success', $playlist, ['new_status' => $newStatus]);
        } catch (\Exception $e) {
            Log::error('Playlist status toggle failed', ['error' => $e->getMessage()]);
            return new MuzibuOperationResult(false, __('muzibu::admin.status_toggle_failed'), 'error');
        }
    }

    public function deletePlaylist(int $id): MuzibuOperationResult
    {
        try {
            $playlist = $this->repository->findById($id);
            if (!$playlist) {
                return new MuzibuOperationResult(false, __('muzibu::admin.playlist_not_found'), 'warning');
            }
            $this->repository->delete($id);
            Log::info('Playlist deleted', ['playlist_id' => $id]);
            return new MuzibuOperationResult(true, __('muzibu::admin.playlist_deleted'), 'success');
        } catch (\Exception $e) {
            Log::error('Playlist deletion failed', ['error' => $e->getMessage()]);
            return new MuzibuOperationResult(false, __('muzibu::admin.playlist_deletion_failed'), 'error');
        }
    }

    public function clearCache(): void
    {
        $this->repository->clearCache();
    }

    /**
     * Clone system playlist to user playlist
     */
    public function clonePlaylist(int $playlistId, int $userId): array
    {
        // 🔒 Günlük limit kontrolü (max 20 playlist/gün)
        if (!$this->checkDailyPlaylistLimit($userId)) {
            return [
                'success' => false,
                'message' => 'Günlük playlist oluşturma limitine ulaştınız. Maksimum 20 playlist/gün oluşturabilirsiniz.',
            ];
        }

        try {
            // Sistem playlist'i bul
            $sourcePlaylist = Playlist::with('songs')->find($playlistId);

            if (!$sourcePlaylist) {
                return [
                    'success' => false,
                    'message' => 'Playlist bulunamadı',
                ];
            }

            // Sadece sistem playlistleri kopyalanabilir
            if (!$sourcePlaylist->is_system) {
                return [
                    'success' => false,
                    'message' => 'Sadece sistem playlistleri kopyalanabilir',
                ];
            }

            DB::beginTransaction();

            // Yeni user playlist oluştur
            $newPlaylist = new Playlist();
            $newPlaylist->user_id = $userId;
            $newPlaylist->title = $sourcePlaylist->title;
            $newPlaylist->slug = $sourcePlaylist->slug;
            $newPlaylist->description = $sourcePlaylist->description;
            $newPlaylist->media_id = $sourcePlaylist->media_id;
            $newPlaylist->is_system = false;
            $newPlaylist->is_public = true;
            $newPlaylist->is_radio = false;
            $newPlaylist->is_active = true;
            $newPlaylist->save();

            // Şarkıları kopyala (position ile) - cache count'ları da güncelle
            foreach ($sourcePlaylist->songs as $song) {
                $newPlaylist->attachSongWithCache($song, [
                    'position' => $song->pivot->position,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            Log::info('Playlist cloned', [
                'source_playlist_id' => $playlistId,
                'new_playlist_id' => $newPlaylist->playlist_id,
                'user_id' => $userId,
            ]);

            return [
                'success' => true,
                'message' => 'Playlist kopyalandı',
                'data' => [
                    'playlist_id' => $newPlaylist->playlist_id,
                    'title' => $newPlaylist->title,
                    'song_count' => $newPlaylist->songs()->count(),
                ],
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Playlist clone failed', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Playlist kopyalama başarısız: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Check daily playlist creation limit (max 20 per day)
     */
    private function checkDailyPlaylistLimit(int $userId): bool
    {
        $todayCount = Playlist::where('user_id', $userId)
            ->where('is_system', false)
            ->whereDate('created_at', today())
            ->count();

        return $todayCount < 20;
    }

    /**
     * Create playlist with songs
     */
    public function createPlaylistWithSongs(array $data, int $userId): array
    {
        // 🔒 Günlük limit kontrolü (max 20 playlist/gün)
        if (!$this->checkDailyPlaylistLimit($userId)) {
            return [
                'success' => false,
                'message' => 'Günlük playlist oluşturma limitine ulaştınız. Maksimum 20 playlist/gün oluşturabilirsiniz.',
            ];
        }

        try {
            DB::beginTransaction();

            // Tenant locale
            $locale = app()->getLocale() ?: config('app.locale', 'tr');

            // JSON formatında hazırla
            $titleJson = json_encode([$locale => $data['title']], JSON_UNESCAPED_UNICODE);
            $descriptionJson = isset($data['description']) && $data['description']
                ? json_encode([$locale => $data['description']], JSON_UNESCAPED_UNICODE)
                : json_encode([$locale => ''], JSON_UNESCAPED_UNICODE);

            $slugBase = isset($data['slug'])
                ? $data['slug']
                : \Illuminate\Support\Str::slug($data['title']);
            $slugJson = json_encode([$locale => $slugBase], JSON_UNESCAPED_UNICODE);

            // 🔥 ZORUNLU: DB::insert ile RAW SQL kullan (HasTranslations trait override etmesin!)
            $playlistId = DB::connection('tenant')->table('muzibu_playlists')->insertGetId([
                'user_id' => $userId,
                'title' => $titleJson,
                'slug' => $slugJson,
                'description' => $descriptionJson,
                'is_system' => false,
                'is_public' => $data['is_public'] ?? false,
                'is_radio' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Model instance oluştur (ilişkiler için)
            $playlist = Playlist::find($playlistId);

            // Şarkıları ekle (varsa) - cache count'ları da güncelle
            if (!empty($data['song_ids']) && is_array($data['song_ids'])) {
                foreach ($data['song_ids'] as $index => $songId) {
                    // Şarkının var olduğunu kontrol et
                    $song = Song::find($songId);
                    if ($song) {
                        $playlist->attachSongWithCache($song, [
                            'position' => $index + 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }

            DB::commit();

            // 🔥 KRİTİK: Response Cache'i temizle (my-playlists sayfasına yeni playlist eklensin!)
            try {
                \Spatie\ResponseCache\Facades\ResponseCache::clear();
            } catch (\Exception $e) {
                Log::warning('Response cache clear failed', ['error' => $e->getMessage()]);
            }

            // 🎨 MUZIBU: Otomatik playlist kapağı oluştur (Muzibu'ya özel job - insansız görseller)
            // İlk şarkı başlığını da ekle (AI için daha iyi context)
            $firstSong = $playlist->songs()->first();
            $titleContext = $data['title'];

            if ($firstSong) {
                $titleContext .= " featuring " . $firstSong->title;
            }

            // Muzibu'ya özel job kullan (GenerateGenericMuzibyCover - insansız prompt kuralları)
            \Modules\Muzibu\App\Jobs\GenerateGenericMuzibyCover::dispatch(
                'playlist',
                $playlist->playlist_id,
                $titleContext,
                $userId,
                tenant('id')
            );

            Log::info('Playlist created with songs', [
                'playlist_id' => $playlist->playlist_id,
                'user_id' => $userId,
                'song_count' => count($data['song_ids'] ?? []),
            ]);

            return [
                'success' => true,
                'message' => 'Playlist oluşturuldu',
                'playlist' => [
                    'playlist_id' => $playlist->playlist_id,
                    'title' => $playlist->title,
                    'slug' => $playlist->slug,
                    'song_count' => $playlist->songs()->count(),
                ],
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Create playlist with songs failed', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Playlist oluşturma başarısız: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Add song to existing playlist
     */
    public function addSongToPlaylist(int $playlistId, int $songId, int $userId): array
    {
        try {
            $playlist = Playlist::find($playlistId);

            if (!$playlist) {
                return [
                    'success' => false,
                    'message' => 'Playlist bulunamadı',
                ];
            }

            // Sadece playlist sahibi ekleyebilir
            if ($playlist->user_id !== $userId) {
                \Log::warning('[PlaylistService] Owner mismatch', [
                    'playlist_id' => $playlistId,
                    'playlist_user_id' => $playlist->user_id,
                    'auth_user_id' => $userId,
                    'playlist_title' => $playlist->title,
                ]);
                return [
                    'success' => false,
                    'message' => 'Bu playlist\'e şarkı ekleyemezsiniz (Sadece kendi playlistlerinize ekleyebilirsiniz)',
                ];
            }

            // Şarkının var olduğunu kontrol et
            $song = Song::find($songId);
            if (!$song) {
                return [
                    'success' => false,
                    'message' => 'Şarkı bulunamadı',
                ];
            }

            // Şarkı zaten playlist'te mi?
            if ($playlist->songs()->where('muzibu_playlist_song.song_id', $songId)->exists()) {
                return [
                    'success' => false,
                    'message' => 'Şarkı zaten playlist\'te',
                ];
            }

            // Son position'ı bul
            $maxPosition = $playlist->songs()->max('muzibu_playlist_song.position') ?? 0;

            // Şarkıyı ekle (cache count'ları da güncelle)
            $playlist->attachSongWithCache($song, [
                'position' => $maxPosition + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 🔥 KRİTİK: Redis cache'i temizle (yeni şarkı eklendi!)
            $tenantId = tenant() ? tenant()->id : 'default';
            $cacheKey = "muzibu:playlist:{$tenantId}:{$playlistId}";
            \Cache::forget($cacheKey);

            // 🔥 KRİTİK: Response Cache'i temizle (my-playlists sayfasında şarkı sayısı güncel görsün!)
            try {
                \Spatie\ResponseCache\Facades\ResponseCache::clear();
            } catch (\Exception $e) {
                Log::warning('Response cache clear failed', ['error' => $e->getMessage()]);
            }

            Log::info('Song added to playlist', [
                'playlist_id' => $playlistId,
                'song_id' => $songId,
                'position' => $maxPosition + 1,
                'cache_cleared' => $cacheKey,
            ]);

            return [
                'success' => true,
                'message' => 'Şarkı playlist\'e eklendi',
                'data' => [
                    'song_id' => $songId,
                    'position' => $maxPosition + 1,
                ],
            ];
        } catch (\Exception $e) {
            Log::error('Add song to playlist failed', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Şarkı ekleme başarısız: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Remove song from playlist
     */
    public function removeSongFromPlaylist(int $playlistId, int $songId, int $userId): array
    {
        try {
            $playlist = Playlist::find($playlistId);

            if (!$playlist) {
                return [
                    'success' => false,
                    'message' => 'Playlist bulunamadı',
                ];
            }

            // Sadece playlist sahibi çıkarabilir
            if ($playlist->user_id !== $userId) {
                return [
                    'success' => false,
                    'message' => 'Bu playlist\'ten şarkı çıkaramazsınız',
                ];
            }

            // Şarkıyı çıkar (cache count'ları da güncelle)
            $playlist->detachSongWithCache($songId);

            // 🔥 KRİTİK: Redis cache'i temizle (yoksa preview'de eski data görünür!)
            $tenantId = tenant() ? tenant()->id : 'default';
            $cacheKey = "muzibu:playlist:{$tenantId}:{$playlistId}";
            \Cache::forget($cacheKey);

            // 🔥 KRİTİK: Response Cache'i temizle (my-playlists sayfasında şarkı sayısı güncel görsün!)
            try {
                \Spatie\ResponseCache\Facades\ResponseCache::clear();
            } catch (\Exception $e) {
                Log::warning('Response cache clear failed', ['error' => $e->getMessage()]);
            }

            Log::info('Song removed from playlist', [
                'playlist_id' => $playlistId,
                'song_id' => $songId,
                'cache_cleared' => $cacheKey,
            ]);

            return [
                'success' => true,
                'message' => 'Şarkı playlist\'ten çıkarıldı',
            ];
        } catch (\Exception $e) {
            Log::error('Remove song from playlist failed', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Şarkı çıkarma başarısız: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Reorder songs in playlist
     */
    public function reorderSongs(int $playlistId, array $songPositions, int $userId): array
    {
        try {
            $playlist = Playlist::find($playlistId);

            if (!$playlist) {
                return [
                    'success' => false,
                    'message' => 'Playlist bulunamadı',
                ];
            }

            // Sadece playlist sahibi sıralayabilir
            if ($playlist->user_id !== $userId) {
                return [
                    'success' => false,
                    'message' => 'Bu playlist\'i düzenleyemezsiniz',
                ];
            }

            DB::beginTransaction();

            // Her şarkının position'ını güncelle
            foreach ($songPositions as $item) {
                DB::table('muzibu_playlist_song')
                    ->where('playlist_id', $playlistId)
                    ->where('song_id', $item['song_id'])
                    ->update([
                        'position' => $item['position'],
                        'updated_at' => now(),
                    ]);
            }

            DB::commit();

            // 🔥 KRİTİK: Redis cache'i temizle (sıralama değişti!)
            $tenantId = tenant() ? tenant()->id : 'default';
            $cacheKey = "muzibu:playlist:{$tenantId}:{$playlistId}";
            \Cache::forget($cacheKey);

            // 🔥 KRİTİK: Response Cache'i temizle (tutarlılık için)
            try {
                \Spatie\ResponseCache\Facades\ResponseCache::clear();
            } catch (\Exception $e) {
                Log::warning('Response cache clear failed', ['error' => $e->getMessage()]);
            }

            Log::info('Playlist songs reordered', [
                'playlist_id' => $playlistId,
                'song_count' => count($songPositions),
                'cache_cleared' => $cacheKey,
            ]);

            return [
                'success' => true,
                'message' => 'Sıralama güncellendi',
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Reorder songs failed', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Sıralama güncellenemedi: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get user playlists
     */
    public function getUserPlaylists(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return Playlist::where('user_id', $userId)
            ->where('is_system', false)
            ->withCount('songs')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}
