<?php

declare(strict_types=1);

namespace Modules\Muzibu\App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\Muzibu\App\Repositories\PlaylistRepository;
use Modules\Muzibu\App\DataTransferObjects\MuzibuOperationResult;
use Modules\Muzibu\App\Models\Playlist;
use Illuminate\Support\Facades\Log;

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
}
