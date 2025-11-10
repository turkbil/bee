<?php

declare(strict_types=1);

namespace Modules\Muzibu\App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\Muzibu\App\Repositories\SectorRepository;
use Modules\Muzibu\App\DataTransferObjects\MuzibuOperationResult;
use Modules\Muzibu\App\Models\Sector;
use Illuminate\Support\Facades\Log;

class SectorService
{
    public function __construct(
        private SectorRepository $repository
    ) {
    }

    public function getPaginatedSectors(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $filters);
    }

    public function getActiveSectors(): Collection
    {
        return $this->repository->getActive();
    }

    public function getSectorById(int $id): ?Sector
    {
        return $this->repository->findByIdWithRelations($id);
    }

    public function prepareSectorForForm(int $id, string $currentLanguage): array
    {
        $sector = $this->repository->findByIdWithRelations($id);

        if (!$sector) {
            return ['sector' => null, 'tabCompletion' => []];
        }

        $tabCompletion = [];
        $tabCompletion[0] = !empty($sector->getTranslated('title', $currentLanguage));
        $tabCompletion[1] = true; // SEO tab her zaman tamamlanmış sayılır

        return ['sector' => $sector, 'tabCompletion' => $tabCompletion];
    }

    public function createSector(array $data): MuzibuOperationResult
    {
        try {
            $sector = $this->repository->create($data);
            Log::info('Sector created', ['sector_id' => $sector->sector_id]);
            return new MuzibuOperationResult(true, __('muzibu::admin.sector_created'), 'success', $sector);
        } catch (\Exception $e) {
            Log::error('Sector creation failed', ['error' => $e->getMessage()]);
            return new MuzibuOperationResult(false, __('muzibu::admin.sector_creation_failed'), 'error');
        }
    }

    public function updateSector(int $id, array $data): MuzibuOperationResult
    {
        try {
            $updated = $this->repository->update($id, $data);
            if (!$updated) {
                return new MuzibuOperationResult(false, __('muzibu::admin.sector_not_found'), 'warning');
            }
            Log::info('Sector updated', ['sector_id' => $id]);
            return new MuzibuOperationResult(true, __('muzibu::admin.sector_updated'), 'success', $this->repository->findById($id));
        } catch (\Exception $e) {
            Log::error('Sector update failed', ['error' => $e->getMessage()]);
            return new MuzibuOperationResult(false, __('muzibu::admin.sector_update_failed'), 'error');
        }
    }

    public function toggleSectorStatus(int $id): MuzibuOperationResult
    {
        try {
            $sector = $this->repository->findById($id);
            if (!$sector) {
                return new MuzibuOperationResult(false, __('muzibu::admin.sector_not_found'), 'warning');
            }
            $newStatus = !$sector->is_active;
            $this->repository->update($id, ['is_active' => $newStatus]);
            return new MuzibuOperationResult(true, $newStatus ? __('muzibu::admin.sector_activated') : __('muzibu::admin.sector_deactivated'), 'success', $sector, ['new_status' => $newStatus]);
        } catch (\Exception $e) {
            Log::error('Sector status toggle failed', ['error' => $e->getMessage()]);
            return new MuzibuOperationResult(false, __('muzibu::admin.status_toggle_failed'), 'error');
        }
    }

    public function deleteSector(int $id): MuzibuOperationResult
    {
        try {
            $sector = $this->repository->findById($id);
            if (!$sector) {
                return new MuzibuOperationResult(false, __('muzibu::admin.sector_not_found'), 'warning');
            }
            $this->repository->delete($id);
            Log::info('Sector deleted', ['sector_id' => $id]);
            return new MuzibuOperationResult(true, __('muzibu::admin.sector_deleted'), 'success');
        } catch (\Exception $e) {
            Log::error('Sector deletion failed', ['error' => $e->getMessage()]);
            return new MuzibuOperationResult(false, __('muzibu::admin.sector_deletion_failed'), 'error');
        }
    }

    public function clearCache(): void
    {
        $this->repository->clearCache();
    }
}
