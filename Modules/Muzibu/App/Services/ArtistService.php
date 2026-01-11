<?php

declare(strict_types=1);

namespace Modules\Muzibu\App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\Muzibu\App\Repositories\ArtistRepository;
use Modules\Muzibu\App\DataTransferObjects\MuzibuOperationResult;
use Modules\Muzibu\App\Models\Artist;
use Illuminate\Support\Facades\Log;

class ArtistService
{
    public function __construct(
        private ArtistRepository $repository
    ) {
    }

    /**
     * Get paginated artists with filters
     */
    public function getPaginatedArtists(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $filters);
    }

    /**
     * Get all active artists
     */
    public function getActiveArtists(): Collection
    {
        return $this->repository->getActive();
    }

    /**
     * Get artist by ID
     */
    public function getArtistById(int $id): ?Artist
    {
        return $this->repository->findByIdWithRelations($id);
    }

    /**
     * Prepare artist for form
     */
    public function prepareArtistForForm(int $id, string $currentLanguage): array
    {
        $artist = $this->repository->findByIdWithRelations($id);

        if (!$artist) {
            return [
                'artist' => null,
                'tabCompletion' => []
            ];
        }

        // Tab completion kontrolü
        $tabCompletion = [];

        // Content tab (0) kontrolü
        $hasTitle = !empty($artist->getTranslated('title', $currentLanguage));
        $tabCompletion[0] = $hasTitle;

        // SEO tab (1) kontrolü - SEO trait'inden
        $hasSeo = $artist->hasSeoSettings() &&
                  !empty($artist->getSeoFallbackTitle());
        $tabCompletion[1] = $hasSeo;

        return [
            'artist' => $artist,
            'tabCompletion' => $tabCompletion
        ];
    }

    /**
     * Create new artist
     */
    public function createArtist(array $data): MuzibuOperationResult
    {
        try {
            $artist = $this->repository->create($data);

            Log::info('Artist created', ['artist_id' => $artist->artist_id]);

            return new MuzibuOperationResult(
                success: true,
                message: __('muzibu::admin.artist_created'),
                type: 'success',
                data: $artist
            );
        } catch (\Exception $e) {
            Log::error('Artist creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return new MuzibuOperationResult(
                success: false,
                message: __('muzibu::admin.artist_creation_failed'),
                type: 'error'
            );
        }
    }

    /**
     * Update artist
     */
    public function updateArtist(int $id, array $data): MuzibuOperationResult
    {
        try {
            $updated = $this->repository->update($id, $data);

            if (!$updated) {
                return new MuzibuOperationResult(
                    success: false,
                    message: __('muzibu::admin.artist_not_found'),
                    type: 'warning'
                );
            }

            Log::info('Artist updated', ['artist_id' => $id]);

            return new MuzibuOperationResult(
                success: true,
                message: __('muzibu::admin.artist_updated'),
                type: 'success',
                data: $this->repository->findById($id)
            );
        } catch (\Exception $e) {
            Log::error('Artist update failed', [
                'artist_id' => $id,
                'error' => $e->getMessage()
            ]);

            return new MuzibuOperationResult(
                success: false,
                message: __('muzibu::admin.artist_update_failed'),
                type: 'error'
            );
        }
    }

    /**
     * Toggle artist status
     */
    public function toggleArtistStatus(int $id): MuzibuOperationResult
    {
        try {
            $artist = $this->repository->findById($id);

            if (!$artist) {
                return new MuzibuOperationResult(
                    success: false,
                    message: __('muzibu::admin.artist_not_found'),
                    type: 'warning'
                );
            }

            $newStatus = !$artist->is_active;
            $this->repository->update($id, ['is_active' => $newStatus]);

            return new MuzibuOperationResult(
                success: true,
                message: $newStatus ? __('muzibu::admin.artist_activated') : __('muzibu::admin.artist_deactivated'),
                type: 'success',
                data: $artist,
                meta: ['new_status' => $newStatus]
            );
        } catch (\Exception $e) {
            Log::error('Artist status toggle failed', [
                'artist_id' => $id,
                'error' => $e->getMessage()
            ]);

            return new MuzibuOperationResult(
                success: false,
                message: __('muzibu::admin.status_toggle_failed'),
                type: 'error'
            );
        }
    }

    /**
     * Delete artist
     */
    public function deleteArtist(int $id): MuzibuOperationResult
    {
        try {
            $artist = $this->repository->findById($id);

            if (!$artist) {
                return new MuzibuOperationResult(
                    success: false,
                    message: __('muzibu::admin.artist_not_found'),
                    type: 'warning'
                );
            }

            $this->repository->delete($id);

            Log::info('Artist deleted', ['artist_id' => $id]);

            return new MuzibuOperationResult(
                success: true,
                message: __('muzibu::admin.artist_deleted'),
                type: 'success'
            );
        } catch (\Exception $e) {
            Log::error('Artist deletion failed', [
                'artist_id' => $id,
                'error' => $e->getMessage()
            ]);

            return new MuzibuOperationResult(
                success: false,
                message: __('muzibu::admin.artist_deletion_failed'),
                type: 'error'
            );
        }
    }

    /**
     * Clear cache
     */
    public function clearCache(): void
    {
        $this->repository->clearCache();
    }
}
