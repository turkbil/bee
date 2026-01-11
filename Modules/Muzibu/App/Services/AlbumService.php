<?php

declare(strict_types=1);

namespace Modules\Muzibu\App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\Muzibu\App\Repositories\AlbumRepository;
use Modules\Muzibu\App\DataTransferObjects\MuzibuOperationResult;
use Modules\Muzibu\App\Models\Album;
use Illuminate\Support\Facades\Log;

class AlbumService
{
    public function __construct(
        private AlbumRepository $repository
    ) {
    }

    public function getPaginatedAlbums(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $filters);
    }

    public function getActiveAlbums(): Collection
    {
        return $this->repository->getActive();
    }

    public function getAlbumById(int $id): ?Album
    {
        return $this->repository->findByIdWithRelations($id);
    }

    public function prepareAlbumForForm(int $id, string $currentLanguage): array
    {
        $album = $this->repository->findByIdWithRelations($id);

        if (!$album) {
            return ['album' => null, 'tabCompletion' => []];
        }

        $tabCompletion = [];
        $tabCompletion[0] = !empty($album->getTranslated('title', $currentLanguage));
        $tabCompletion[1] = $album->hasSeoSettings() && !empty($album->getSeoFallbackTitle());

        return ['album' => $album, 'tabCompletion' => $tabCompletion];
    }

    public function createAlbum(array $data): MuzibuOperationResult
    {
        try {
            $album = $this->repository->create($data);
            Log::info('Album created', ['album_id' => $album->album_id]);
            return new MuzibuOperationResult(true, __('muzibu::admin.album_created'), 'success', $album);
        } catch (\Exception $e) {
            Log::error('Album creation failed', ['error' => $e->getMessage()]);
            return new MuzibuOperationResult(false, __('muzibu::admin.album_creation_failed'), 'error');
        }
    }

    public function updateAlbum(int $id, array $data): MuzibuOperationResult
    {
        try {
            $updated = $this->repository->update($id, $data);
            if (!$updated) {
                return new MuzibuOperationResult(false, __('muzibu::admin.album_not_found'), 'warning');
            }
            Log::info('Album updated', ['album_id' => $id]);
            return new MuzibuOperationResult(true, __('muzibu::admin.album_updated'), 'success', $this->repository->findById($id));
        } catch (\Exception $e) {
            Log::error('Album update failed', ['error' => $e->getMessage()]);
            return new MuzibuOperationResult(false, __('muzibu::admin.album_update_failed'), 'error');
        }
    }

    public function toggleAlbumStatus(int $id): MuzibuOperationResult
    {
        try {
            $album = $this->repository->findById($id);
            if (!$album) {
                return new MuzibuOperationResult(false, __('muzibu::admin.album_not_found'), 'warning');
            }
            $newStatus = !$album->is_active;
            $this->repository->update($id, ['is_active' => $newStatus]);
            return new MuzibuOperationResult(true, $newStatus ? __('muzibu::admin.album_activated') : __('muzibu::admin.album_deactivated'), 'success', $album, ['new_status' => $newStatus]);
        } catch (\Exception $e) {
            Log::error('Album status toggle failed', ['error' => $e->getMessage()]);
            return new MuzibuOperationResult(false, __('muzibu::admin.status_toggle_failed'), 'error');
        }
    }

    public function deleteAlbum(int $id): MuzibuOperationResult
    {
        try {
            $album = $this->repository->findById($id);
            if (!$album) {
                return new MuzibuOperationResult(false, __('muzibu::admin.album_not_found'), 'warning');
            }

            // Şarkı kontrolü (Observer'da da kontrol var, ama service seviyesinde de yapalım)
            if ($album->songs()->count() > 0) {
                Log::warning('Cannot delete album with songs', [
                    'album_id' => $id,
                    'song_count' => $album->songs()->count()
                ]);

                return new MuzibuOperationResult(
                    false,
                    'Bu albüme ait ' . $album->songs()->count() . ' şarkı var. Önce şarkıları silmelisiniz veya başka albüme taşımalısınız.',
                    'warning'
                );
            }

            $this->repository->delete($id);
            Log::info('Album deleted', ['album_id' => $id]);
            return new MuzibuOperationResult(true, __('muzibu::admin.album_deleted'), 'success');
        } catch (\Exception $e) {
            Log::error('Album deletion failed', [
                'album_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            // Observer'dan gelen exception mesajını kullanıcıya dön
            return new MuzibuOperationResult(false, $e->getMessage(), 'error');
        }
    }

    public function clearCache(): void
    {
        $this->repository->clearCache();
    }
}
