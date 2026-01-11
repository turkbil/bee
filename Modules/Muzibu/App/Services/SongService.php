<?php

declare(strict_types=1);

namespace Modules\Muzibu\App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\Muzibu\App\Repositories\SongRepository;
use Modules\Muzibu\App\DataTransferObjects\MuzibuOperationResult;
use Modules\Muzibu\App\Models\Song;
use Illuminate\Support\Facades\Log;

class SongService
{
    public function __construct(
        private SongRepository $repository
    ) {
    }

    public function getPaginatedSongs(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $filters);
    }

    public function getActiveSongs(): Collection
    {
        return $this->repository->getActive();
    }

    public function getSongById(int $id): ?Song
    {
        return $this->repository->findByIdWithRelations($id);
    }

    public function prepareSongForForm(int $id, string $currentLanguage): array
    {
        $song = $this->repository->findByIdWithRelations($id);

        if (!$song) {
            return ['song' => null, 'tabCompletion' => []];
        }

        $tabCompletion = [];
        $tabCompletion[0] = !empty($song->getTranslated('title', $currentLanguage));
        $tabCompletion[1] = $song->hasSeoSettings() && !empty($song->getSeoFallbackTitle());

        return ['song' => $song, 'tabCompletion' => $tabCompletion];
    }

    public function createSong(array $data): MuzibuOperationResult
    {
        try {
            $song = $this->repository->create($data);
            Log::info('Song created', ['song_id' => $song->song_id]);
            return new MuzibuOperationResult(true, __('muzibu::admin.song_created'), 'success', $song);
        } catch (\Exception $e) {
            Log::error('Song creation failed', ['error' => $e->getMessage()]);
            return new MuzibuOperationResult(false, __('muzibu::admin.song_creation_failed'), 'error');
        }
    }

    public function updateSong(int $id, array $data): MuzibuOperationResult
    {
        try {
            $updated = $this->repository->update($id, $data);
            if (!$updated) {
                return new MuzibuOperationResult(false, __('muzibu::admin.song_not_found'), 'warning');
            }
            Log::info('Song updated', ['song_id' => $id]);
            return new MuzibuOperationResult(true, __('muzibu::admin.song_updated'), 'success', $this->repository->findById($id));
        } catch (\Exception $e) {
            Log::error('Song update failed', ['error' => $e->getMessage()]);
            return new MuzibuOperationResult(false, __('muzibu::admin.song_update_failed'), 'error');
        }
    }

    public function toggleSongStatus(int $id): MuzibuOperationResult
    {
        try {
            $song = $this->repository->findById($id);
            if (!$song) {
                return new MuzibuOperationResult(false, __('muzibu::admin.song_not_found'), 'warning');
            }
            $newStatus = !$song->is_active;
            $this->repository->update($id, ['is_active' => $newStatus]);
            return new MuzibuOperationResult(true, $newStatus ? __('muzibu::admin.song_activated') : __('muzibu::admin.song_deactivated'), 'success', $song, ['new_status' => $newStatus]);
        } catch (\Exception $e) {
            Log::error('Song status toggle failed', ['error' => $e->getMessage()]);
            return new MuzibuOperationResult(false, __('muzibu::admin.status_toggle_failed'), 'error');
        }
    }

    public function deleteSong(int $id): MuzibuOperationResult
    {
        try {
            $song = $this->repository->findById($id);
            if (!$song) {
                return new MuzibuOperationResult(false, __('muzibu::admin.song_not_found'), 'warning');
            }
            $this->repository->delete($id);
            Log::info('Song deleted', ['song_id' => $id]);
            return new MuzibuOperationResult(true, __('muzibu::admin.song_deleted'), 'success');
        } catch (\Exception $e) {
            Log::error('Song deletion failed', ['error' => $e->getMessage()]);
            return new MuzibuOperationResult(false, __('muzibu::admin.song_deletion_failed'), 'error');
        }
    }

    public function clearCache(): void
    {
        $this->repository->clearCache();
    }
}
