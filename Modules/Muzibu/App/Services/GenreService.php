<?php

declare(strict_types=1);

namespace Modules\Muzibu\App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\Muzibu\App\Repositories\GenreRepository;
use Modules\Muzibu\App\DataTransferObjects\MuzibuOperationResult;
use Modules\Muzibu\App\Models\Genre;
use Illuminate\Support\Facades\Log;

class GenreService
{
    public function __construct(
        private GenreRepository $repository
    ) {
    }

    public function getPaginatedGenres(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $filters);
    }

    public function getActiveGenres(): Collection
    {
        return $this->repository->getActive();
    }

    public function getGenreById(int $id): ?Genre
    {
        return $this->repository->findByIdWithRelations($id);
    }

    public function prepareGenreForForm(int $id, string $currentLanguage): array
    {
        $genre = $this->repository->findByIdWithRelations($id);

        if (!$genre) {
            return ['genre' => null, 'tabCompletion' => []];
        }

        $tabCompletion = [];
        $tabCompletion[0] = !empty($genre->getTranslated('title', $currentLanguage));
        $tabCompletion[1] = $genre->hasSeoSettings() && !empty($genre->getSeoFallbackTitle());

        return ['genre' => $genre, 'tabCompletion' => $tabCompletion];
    }

    public function createGenre(array $data): MuzibuOperationResult
    {
        try {
            $genre = $this->repository->create($data);
            Log::info('Genre created', ['genre_id' => $genre->genre_id]);
            return new MuzibuOperationResult(true, __('muzibu::admin.genre_created'), 'success', $genre);
        } catch (\Exception $e) {
            Log::error('Genre creation failed', ['error' => $e->getMessage()]);
            return new MuzibuOperationResult(false, __('muzibu::admin.genre_creation_failed'), 'error');
        }
    }

    public function updateGenre(int $id, array $data): MuzibuOperationResult
    {
        try {
            $updated = $this->repository->update($id, $data);
            if (!$updated) {
                return new MuzibuOperationResult(false, __('muzibu::admin.genre_not_found'), 'warning');
            }
            Log::info('Genre updated', ['genre_id' => $id]);
            return new MuzibuOperationResult(true, __('muzibu::admin.genre_updated'), 'success', $this->repository->findById($id));
        } catch (\Exception $e) {
            Log::error('Genre update failed', ['error' => $e->getMessage()]);
            return new MuzibuOperationResult(false, __('muzibu::admin.genre_update_failed'), 'error');
        }
    }

    public function toggleGenreStatus(int $id): MuzibuOperationResult
    {
        try {
            $genre = $this->repository->findById($id);
            if (!$genre) {
                return new MuzibuOperationResult(false, __('muzibu::admin.genre_not_found'), 'warning');
            }
            $newStatus = !$genre->is_active;
            $this->repository->update($id, ['is_active' => $newStatus]);
            return new MuzibuOperationResult(true, $newStatus ? __('muzibu::admin.genre_activated') : __('muzibu::admin.genre_deactivated'), 'success', $genre, ['new_status' => $newStatus]);
        } catch (\Exception $e) {
            Log::error('Genre status toggle failed', ['error' => $e->getMessage()]);
            return new MuzibuOperationResult(false, __('muzibu::admin.status_toggle_failed'), 'error');
        }
    }

    public function deleteGenre(int $id): MuzibuOperationResult
    {
        try {
            $genre = $this->repository->findById($id);
            if (!$genre) {
                return new MuzibuOperationResult(false, __('muzibu::admin.genre_not_found'), 'warning');
            }
            $this->repository->delete($id);
            Log::info('Genre deleted', ['genre_id' => $id]);
            return new MuzibuOperationResult(true, __('muzibu::admin.genre_deleted'), 'success');
        } catch (\Exception $e) {
            Log::error('Genre deletion failed', ['error' => $e->getMessage()]);
            return new MuzibuOperationResult(false, __('muzibu::admin.genre_deletion_failed'), 'error');
        }
    }

    public function clearCache(): void
    {
        $this->repository->clearCache();
    }
}
