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

            // Şarkıları kopyala (position ile)
            foreach ($sourcePlaylist->songs as $song) {
                $newPlaylist->songs()->attach($song->song_id, [
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
     * Create playlist with songs
     */
    public function createPlaylistWithSongs(array $data, int $userId): array
    {
        try {
            DB::beginTransaction();

            // Playlist oluştur
            $playlist = new Playlist();
            $playlist->user_id = $userId;
            $playlist->title = $data['title'];

            // 🔧 FIX: Auto-generate slug from title
            if (isset($data['slug'])) {
                $playlist->slug = $data['slug'];
            } else {
                // Title'dan slug oluştur (multilang support)
                $slugBase = \Illuminate\Support\Str::slug($data['title']);
                $playlist->slug = [
                    'tr' => $slugBase,
                    'en' => $slugBase,
                ];
            }

            $playlist->description = $data['description'] ?? null;
            $playlist->is_system = false;
            $playlist->is_public = $data['is_public'] ?? true;
            $playlist->is_radio = false;
            $playlist->is_active = true;
            $playlist->save();

            // Şarkıları ekle (varsa)
            if (!empty($data['song_ids']) && is_array($data['song_ids'])) {
                foreach ($data['song_ids'] as $index => $songId) {
                    // Şarkının var olduğunu kontrol et
                    if (Song::find($songId)) {
                        $playlist->songs()->attach($songId, [
                            'position' => $index + 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }

            DB::commit();

            Log::info('Playlist created with songs', [
                'playlist_id' => $playlist->playlist_id,
                'user_id' => $userId,
                'song_count' => count($data['song_ids'] ?? []),
            ]);

            return [
                'success' => true,
                'message' => 'Playlist oluşturuldu',
                'data' => [
                    'playlist_id' => $playlist->playlist_id,
                    'title' => $playlist->title,
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
                return [
                    'success' => false,
                    'message' => 'Bu playlist\'e şarkı ekleyemezsiniz',
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
            if ($playlist->songs()->where('song_id', $songId)->exists()) {
                return [
                    'success' => false,
                    'message' => 'Şarkı zaten playlist\'te',
                ];
            }

            // Son position'ı bul
            $maxPosition = $playlist->songs()->max('muzibu_playlist_song.position') ?? 0;

            // Şarkıyı ekle
            $playlist->songs()->attach($songId, [
                'position' => $maxPosition + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Log::info('Song added to playlist', [
                'playlist_id' => $playlistId,
                'song_id' => $songId,
                'position' => $maxPosition + 1,
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

            // Şarkıyı çıkar
            $playlist->songs()->detach($songId);

            Log::info('Song removed from playlist', [
                'playlist_id' => $playlistId,
                'song_id' => $songId,
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

            Log::info('Playlist songs reordered', [
                'playlist_id' => $playlistId,
                'song_count' => count($songPositions),
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
