<?php

declare(strict_types=1);

namespace Modules\Muzibu\App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\Muzibu\App\Repositories\RadioRepository;
use Modules\Muzibu\App\DataTransferObjects\MuzibuOperationResult;
use Modules\Muzibu\App\Models\Radio;
use Illuminate\Support\Facades\Log;

class RadioService
{
    public function __construct(
        private RadioRepository $repository
    ) {
    }

    public function getPaginatedRadios(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $filters);
    }

    public function getActiveRadios(): Collection
    {
        return $this->repository->getActive();
    }

    public function getRadioById(int $id): ?Radio
    {
        return $this->repository->findByIdWithRelations($id);
    }

    public function prepareRadioForForm(int $id, string $currentLanguage): array
    {
        $radio = $this->repository->findByIdWithRelations($id);

        if (!$radio) {
            return ['radio' => null, 'tabCompletion' => []];
        }

        $tabCompletion = [];
        $tabCompletion[0] = !empty($radio->getTranslated('title', $currentLanguage));
        $tabCompletion[1] = $radio->hasSeoSettings() && !empty($radio->getSeoFallbackTitle());

        return ['radio' => $radio, 'tabCompletion' => $tabCompletion];
    }

    public function createRadio(array $data): MuzibuOperationResult
    {
        try {
            $radio = $this->repository->create($data);
            Log::info('Radio created', ['radio_id' => $radio->radio_id]);
            return new MuzibuOperationResult(true, __('muzibu::admin.radio_created'), 'success', $radio);
        } catch (\Exception $e) {
            Log::error('Radio creation failed', ['error' => $e->getMessage()]);
            return new MuzibuOperationResult(false, __('muzibu::admin.radio_creation_failed'), 'error');
        }
    }

    public function updateRadio(int $id, array $data): MuzibuOperationResult
    {
        try {
            $updated = $this->repository->update($id, $data);
            if (!$updated) {
                return new MuzibuOperationResult(false, __('muzibu::admin.radio_not_found'), 'warning');
            }
            Log::info('Radio updated', ['radio_id' => $id]);
            return new MuzibuOperationResult(true, __('muzibu::admin.radio_updated'), 'success', $this->repository->findById($id));
        } catch (\Exception $e) {
            Log::error('Radio update failed', ['error' => $e->getMessage()]);
            return new MuzibuOperationResult(false, __('muzibu::admin.radio_update_failed'), 'error');
        }
    }

    public function toggleRadioStatus(int $id): MuzibuOperationResult
    {
        try {
            $radio = $this->repository->findById($id);
            if (!$radio) {
                return new MuzibuOperationResult(false, __('muzibu::admin.radio_not_found'), 'warning');
            }
            $newStatus = !$radio->is_active;
            $this->repository->update($id, ['is_active' => $newStatus]);
            return new MuzibuOperationResult(true, $newStatus ? __('muzibu::admin.radio_activated') : __('muzibu::admin.radio_deactivated'), 'success', $radio, ['new_status' => $newStatus]);
        } catch (\Exception $e) {
            Log::error('Radio status toggle failed', ['error' => $e->getMessage()]);
            return new MuzibuOperationResult(false, __('muzibu::admin.status_toggle_failed'), 'error');
        }
    }

    public function deleteRadio(int $id): MuzibuOperationResult
    {
        try {
            $radio = $this->repository->findById($id);
            if (!$radio) {
                return new MuzibuOperationResult(false, __('muzibu::admin.radio_not_found'), 'warning');
            }
            $this->repository->delete($id);
            Log::info('Radio deleted', ['radio_id' => $id]);
            return new MuzibuOperationResult(true, __('muzibu::admin.radio_deleted'), 'success');
        } catch (\Exception $e) {
            Log::error('Radio deletion failed', ['error' => $e->getMessage()]);
            return new MuzibuOperationResult(false, __('muzibu::admin.radio_deletion_failed'), 'error');
        }
    }

    public function clearCache(): void
    {
        $this->repository->clearCache();
    }
}
