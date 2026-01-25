<?php

declare(strict_types=1);

namespace Modules\Service\App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Modules\Service\App\Contracts\ServiceRepositoryInterface;
use App\Contracts\GlobalSeoRepositoryInterface;
use App\Services\GlobalTabService;
use Modules\Service\App\Models\Service;
use Modules\Service\App\DataTransferObjects\{ServiceOperationResult, BulkOperationResult};
use Modules\Service\App\Exceptions\{ServiceNotFoundException, ServiceCreationException, ServiceProtectionException};
use App\Services\BaseService;
use Throwable;

readonly class ServiceService extends BaseService
{
    public function __construct(
        private ServiceRepositoryInterface $serviceRepository,
        private GlobalSeoRepositoryInterface $seoRepository
    ) {}

    public function getService(int $id): Service
    {
        return $this->serviceRepository->findById($id)
            ?? throw ServiceNotFoundException::withId($id);
    }

    public function getServiceBySlug(string $slug, string $locale = 'tr'): Service
    {
        return $this->serviceRepository->findBySlug($slug, $locale)
            ?? throw ServiceNotFoundException::withSlug($slug, $locale);
    }

    public function getActiveServices(): Collection
    {
        return $this->serviceRepository->getActive();
    }

    public function getPaginatedServices(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->serviceRepository->getPaginated($filters, $perPage);
    }

    public function searchServices(string $term, array $locales = []): Collection
    {
        return $this->serviceRepository->search($term, $locales);
    }

    public function createService(array $data): ServiceOperationResult
    {
        try {
            // Slug otomatik oluşturma
            if (isset($data['title']) && is_array($data['title'])) {
                $data['slug'] = $this->generateSlugsFromTitles($data['title']);
            }

            // SEO verileri hazırlama
            if (isset($data['seo']) && is_array($data['seo'])) {
                $data['seo'] = $this->prepareSeoData($data['seo']);
            }

            $service = $this->serviceRepository->create($data);

            Log::info('Service created', [
                'service_id' => $service->service_id,
                'title' => $service->title,
                'user_id' => auth()->id()
            ]);

            return ServiceOperationResult::success(
                message: __('service::admin.service_created_successfully'),
                data: $service
            );
        } catch (Throwable $e) {
            Log::error('Service creation failed', [
                'error' => $e->getMessage(),
                'data' => $data,
                'user_id' => auth()->id()
            ]);

            throw ServiceCreationException::withDatabaseError($e->getMessage());
        }
    }

    public function updateService(int $id, array $data): ServiceOperationResult
    {
        try {
            $service = $this->serviceRepository->findById($id)
                ?? throw ServiceNotFoundException::withId($id);

            // Slug güncelleme
            if (isset($data['title']) && is_array($data['title'])) {
                $data['slug'] = $this->generateSlugsFromTitles($data['title'], $service->slug ?? []);
            }

            // SEO verileri hazırlama
            if (isset($data['seo']) && is_array($data['seo'])) {
                $data['seo'] = $this->prepareSeoData($data['seo'], $service->seo ?? []);
            }

            $this->serviceRepository->update($id, $data);

            Log::info('Service updated', [
                'service_id' => $id,
                'title' => $data['title'] ?? 'unchanged',
                'user_id' => auth()->id()
            ]);

            return ServiceOperationResult::success(
                message: __('service::admin.service_updated_successfully'),
                data: $service->refresh()
            );
        } catch (ServiceNotFoundException $e) {
            throw $e;
        } catch (Throwable $e) {
            Log::error('Service update failed', [
                'service_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return ServiceOperationResult::error(
                message: __('service::admin.update_failed'),
                type: 'error'
            );
        }
    }

    public function deleteService(int $id): ServiceOperationResult
    {
        try {
            $service = $this->serviceRepository->findById($id)
                ?? throw ServiceNotFoundException::withId($id);

            $this->serviceRepository->delete($id);

            Log::info('Service deleted', [
                'service_id' => $id,
                'title' => $service->title,
                'user_id' => auth()->id()
            ]);

            return ServiceOperationResult::success(
                message: __('service::admin.service_deleted_successfully')
            );
        } catch (ServiceNotFoundException | ServiceProtectionException $e) {
            throw $e;
        } catch (Throwable $e) {
            Log::error('Service deletion failed', [
                'service_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return ServiceOperationResult::error(
                message: __('service::admin.deletion_failed'),
                type: 'error'
            );
        }
    }

    public function toggleServiceStatus(int $id): ServiceOperationResult
    {
        try {
            $service = $this->serviceRepository->findById($id)
                ?? throw ServiceNotFoundException::withId($id);

            $this->serviceRepository->toggleActive($id);
            $service->refresh();

            Log::info('Service status toggled', [
                'service_id' => $id,
                'new_status' => $service->is_active,
                'user_id' => auth()->id()
            ]);

            return ServiceOperationResult::success(
                message: __($service->is_active ? 'admin.activated' : 'admin.deactivated'),
                data: $service,
                meta: ['new_status' => $service->is_active]
            );
        } catch (ServiceNotFoundException $e) {
            return ServiceOperationResult::error(
                message: __('service::admin.service_not_found'),
                type: 'error'
            );
        } catch (ServiceProtectionException $e) {
            return ServiceOperationResult::warning(
                message: $e->getMessage()
            );
        } catch (Throwable $e) {
            Log::error('Service status toggle failed', [
                'service_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return ServiceOperationResult::error(
                message: __('admin.operation_failed'),
                type: 'error'
            );
        }
    }

    public function bulkDeleteServices(array $ids): BulkOperationResult
    {
        try {
            if (empty($ids)) {
                return BulkOperationResult::failure(
                    message: __('admin.no_services_selected')
                );
            }

            $deletedCount = $this->serviceRepository->bulkDelete($ids);

            Log::info('Bulk delete performed', [
                'deleted_count' => $deletedCount,
                'user_id' => auth()->id()
            ]);

            return BulkOperationResult::success(
                message: __('admin.deleted_successfully'),
                affectedCount: $deletedCount
            );
        } catch (Throwable $e) {
            Log::error('Bulk delete failed', [
                'error' => $e->getMessage(),
                'ids' => $ids,
                'user_id' => auth()->id()
            ]);

            return BulkOperationResult::failure(
                message: __('admin.bulk_operation_failed'),
                errors: [$e->getMessage()]
            );
        }
    }

    public function bulkToggleServiceStatus(array $ids): BulkOperationResult
    {
        try {
            $affectedCount = $this->serviceRepository->bulkToggleActive($ids);

            Log::info('Bulk status toggle performed', [
                'affected_count' => $affectedCount,
                'user_id' => auth()->id()
            ]);

            return BulkOperationResult::success(
                message: __('admin.updated_successfully'),
                affectedCount: $affectedCount
            );
        } catch (Throwable $e) {
            Log::error('Bulk status toggle failed', [
                'error' => $e->getMessage(),
                'ids' => $ids,
                'user_id' => auth()->id()
            ]);

            return BulkOperationResult::failure(
                message: __('admin.bulk_operation_failed'),
                errors: [$e->getMessage()]
            );
        }
    }

    protected function generateSlugsFromTitles(array $titles, array $existingSlugs = []): array
    {
        $slugs = $existingSlugs;

        foreach ($titles as $locale => $title) {
            if (!empty($title) && empty($slugs[$locale])) {
                $slugs[$locale] = \Str::slug($title);
            }
        }

        return $slugs;
    }

    protected function prepareSeoData(array $seoData, array $existingSeo = []): array
    {
        $prepared = $existingSeo;

        foreach ($seoData as $locale => $data) {
            if (is_array($data)) {
                $cleanData = array_filter($data, function($value) {
                    return !is_null($value) && $value !== '' && $value !== [];
                });

                if (!empty($cleanData)) {
                    $prepared[$locale] = array_merge($prepared[$locale] ?? [], $cleanData);
                }
            }
        }

        return $prepared;
    }

    /**
     * Sayfa verilerini form için hazırla
     */
    public function prepareServiceForForm(int $id, string $language): array
    {
        // 🚨 PERFORMANCE FIX: Eager loading ile bir seferde çek
        $service = $this->serviceRepository->findByIdWithSeo($id);

        if (!$service) {
            return $this->getEmptyFormData($language);
        }

        // 🚨 PERFORMANCE FIX: SEO data'yı sadece bir kez çek
        $seoData = $this->seoRepository->getSeoData($service, $language);

        // Tab completion durumunu hesapla
        $allData = array_merge($service->toArray(), $seoData);
        $tabCompletion = GlobalTabService::getTabCompletionStatus($allData, 'service');

        return [
            'service' => $service,
            'seoData' => $seoData, // Tekrar çekme!
            'tabCompletion' => $tabCompletion,
            'tabConfig' => GlobalTabService::getJavaScriptConfig('service'),
            'seoLimits' => $this->seoRepository->getFieldLimits('service')
        ];
    }

    /**
     * Yeni sayfa için boş form verisi
     */
    public function getEmptyFormData(string $language): array
    {
        $emptyData = [
            'title' => '',
            'body' => '',
            'slug' => '',
            'seo_title' => '',
            'seo_description' => '',
            'seo_keywords' => '',
            'canonical_url' => ''
        ];

        return [
            'service' => null,
            'seoData' => $emptyData,
            'tabCompletion' => GlobalTabService::getTabCompletionStatus($emptyData, 'service'),
            'tabConfig' => GlobalTabService::getJavaScriptConfig('service'),
            'seoLimits' => $this->seoRepository->getFieldLimits('service')
        ];
    }

    /**
     * Form validation kurallarını getir
     */
    public function getValidationRules(array $availableLanguages): array
    {
        $rules = [
            'inputs.is_active' => 'boolean',
        ];

        // Default locale'i al
        $defaultLocale = get_tenant_default_locale();

        // Çoklu dil alanları
        foreach ($availableLanguages as $lang) {
            $rules["multiLangInputs.{$lang}.title"] = $lang === $defaultLocale ? 'required|min:3|max:255' : 'nullable|min:3|max:255';
            $rules["multiLangInputs.{$lang}.slug"] = 'nullable|string|max:255';
            $rules["multiLangInputs.{$lang}.body"] = 'nullable|string';
        }

        // SEO validation kuralları
        $seoRules = $this->seoRepository->getValidationRules('service');

        return array_merge($rules, $seoRules);
    }

    /**
     * SEO skorunu hesapla
     */
    public function calculateSeoScore(array $seoData): array
    {
        return $this->seoRepository->calculateSeoScore($seoData, 'service');
    }

    public function updateSeoField(int $id, string $locale, string $field, mixed $value): bool
    {
        $result = $this->serviceRepository->updateSeoField($id, $locale, $field, $value);

        if ($result) {
            Log::info('SEO field updated', [
                'service_id' => $id,
                'locale' => $locale,
                'field' => $field,
                'user_id' => auth()->id()
            ]);
        }

        return $result;
    }

    public function clearCache(): void
    {
        $this->serviceRepository->clearCache();

        Log::info('Service cache cleared', [
            'user_id' => auth()->id()
        ]);
    }
}
