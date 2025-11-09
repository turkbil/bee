<?php

declare(strict_types=1);

namespace Modules\Muzibu\App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Modules\Muzibu\App\Contracts\MuzibuRepositoryInterface;
use App\Contracts\GlobalSeoRepositoryInterface;
use App\Services\GlobalTabService;
use Modules\Muzibu\App\Models\Muzibu;
use Modules\Muzibu\App\DataTransferObjects\{MuzibuOperationResult, BulkOperationResult};
use Modules\Muzibu\App\Exceptions\{MuzibuNotFoundException, MuzibuCreationException, MuzibuProtectionException};
use App\Services\BaseService;
use Throwable;

readonly class MuzibuService extends BaseService
{
    public function __construct(
        private MuzibuRepositoryInterface $muzibuRepository,
        private GlobalSeoRepositoryInterface $seoRepository
    ) {}

    public function getMuzibu(int $id): Muzibu
    {
        return $this->muzibuRepository->findById($id)
            ?? throw MuzibuNotFoundException::withId($id);
    }

    public function getMuzibuBySlug(string $slug, string $locale = 'tr'): Muzibu
    {
        return $this->muzibuRepository->findBySlug($slug, $locale)
            ?? throw MuzibuNotFoundException::withSlug($slug, $locale);
    }

    public function getActiveMuzibus(): Collection
    {
        return $this->muzibuRepository->getActive();
    }

    public function getPaginatedMuzibus(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->muzibuRepository->getPaginated($filters, $perPage);
    }

    public function searchMuzibus(string $term, array $locales = []): Collection
    {
        return $this->muzibuRepository->search($term, $locales);
    }

    public function createMuzibu(array $data): MuzibuOperationResult
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

            $muzibu = $this->muzibuRepository->create($data);

            Log::info('Muzibu created', [
                'muzibu_id' => $muzibu->muzibu_id,
                'title' => $muzibu->title,
                'user_id' => auth()->id()
            ]);

            return MuzibuOperationResult::success(
                message: __('muzibu::admin.muzibu_created_successfully'),
                data: $muzibu
            );
        } catch (Throwable $e) {
            Log::error('Muzibu creation failed', [
                'error' => $e->getMessage(),
                'data' => $data,
                'user_id' => auth()->id()
            ]);

            throw MuzibuCreationException::withDatabaseError($e->getMessage());
        }
    }

    public function updateMuzibu(int $id, array $data): MuzibuOperationResult
    {
        try {
            $muzibu = $this->muzibuRepository->findById($id)
                ?? throw MuzibuNotFoundException::withId($id);

            // Slug güncelleme
            if (isset($data['title']) && is_array($data['title'])) {
                $data['slug'] = $this->generateSlugsFromTitles($data['title'], $muzibu->slug ?? []);
            }

            // SEO verileri hazırlama
            if (isset($data['seo']) && is_array($data['seo'])) {
                $data['seo'] = $this->prepareSeoData($data['seo'], $muzibu->seo ?? []);
            }

            $this->muzibuRepository->update($id, $data);

            Log::info('Muzibu updated', [
                'muzibu_id' => $id,
                'title' => $data['title'] ?? 'unchanged',
                'user_id' => auth()->id()
            ]);

            return MuzibuOperationResult::success(
                message: __('muzibu::admin.muzibu_updated_successfully'),
                data: $muzibu->refresh()
            );
        } catch (MuzibuNotFoundException $e) {
            throw $e;
        } catch (Throwable $e) {
            Log::error('Muzibu update failed', [
                'muzibu_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return MuzibuOperationResult::error(
                message: __('muzibu::admin.update_failed'),
                type: 'error'
            );
        }
    }

    public function deleteMuzibu(int $id): MuzibuOperationResult
    {
        try {
            $muzibu = $this->muzibuRepository->findById($id)
                ?? throw MuzibuNotFoundException::withId($id);

            $this->muzibuRepository->delete($id);

            Log::info('Muzibu deleted', [
                'muzibu_id' => $id,
                'title' => $muzibu->title,
                'user_id' => auth()->id()
            ]);

            return MuzibuOperationResult::success(
                message: __('muzibu::admin.muzibu_deleted_successfully')
            );
        } catch (MuzibuNotFoundException | MuzibuProtectionException $e) {
            throw $e;
        } catch (Throwable $e) {
            Log::error('Muzibu deletion failed', [
                'muzibu_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return MuzibuOperationResult::error(
                message: __('muzibu::admin.deletion_failed'),
                type: 'error'
            );
        }
    }

    public function toggleMuzibuStatus(int $id): MuzibuOperationResult
    {
        try {
            $muzibu = $this->muzibuRepository->findById($id)
                ?? throw MuzibuNotFoundException::withId($id);

            $this->muzibuRepository->toggleActive($id);
            $muzibu->refresh();

            Log::info('Muzibu status toggled', [
                'muzibu_id' => $id,
                'new_status' => $muzibu->is_active,
                'user_id' => auth()->id()
            ]);

            return MuzibuOperationResult::success(
                message: __($muzibu->is_active ? 'admin.activated' : 'admin.deactivated'),
                data: $muzibu,
                meta: ['new_status' => $muzibu->is_active]
            );
        } catch (MuzibuNotFoundException $e) {
            return MuzibuOperationResult::error(
                message: __('muzibu::admin.muzibu_not_found'),
                type: 'error'
            );
        } catch (MuzibuProtectionException $e) {
            return MuzibuOperationResult::warning(
                message: $e->getMessage()
            );
        } catch (Throwable $e) {
            Log::error('Muzibu status toggle failed', [
                'muzibu_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return MuzibuOperationResult::error(
                message: __('admin.operation_failed'),
                type: 'error'
            );
        }
    }

    public function bulkDeleteMuzibus(array $ids): BulkOperationResult
    {
        try {
            if (empty($ids)) {
                return BulkOperationResult::failure(
                    message: __('admin.no_muzibus_selected')
                );
            }

            $deletedCount = $this->muzibuRepository->bulkDelete($ids);

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

    public function bulkToggleMuzibuStatus(array $ids): BulkOperationResult
    {
        try {
            $affectedCount = $this->muzibuRepository->bulkToggleActive($ids);

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
    public function prepareMuzibuForForm(int $id, string $language): array
    {
        // 🚨 PERFORMANCE FIX: Eager loading ile bir seferde çek
        $muzibu = $this->muzibuRepository->findByIdWithSeo($id);

        if (!$muzibu) {
            return $this->getEmptyFormData($language);
        }

        // 🚨 PERFORMANCE FIX: SEO data'yı sadece bir kez çek
        $seoData = $this->seoRepository->getSeoData($muzibu, $language);

        // Tab completion durumunu hesapla
        $allData = array_merge($muzibu->toArray(), $seoData);
        $tabCompletion = GlobalTabService::getTabCompletionStatus($allData, 'muzibu');

        return [
            'muzibu' => $muzibu,
            'seoData' => $seoData, // Tekrar çekme!
            'tabCompletion' => $tabCompletion,
            'tabConfig' => GlobalTabService::getJavaScriptConfig('muzibu'),
            'seoLimits' => $this->seoRepository->getFieldLimits('muzibu')
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
            'muzibu' => null,
            'seoData' => $emptyData,
            'tabCompletion' => GlobalTabService::getTabCompletionStatus($emptyData, 'muzibu'),
            'tabConfig' => GlobalTabService::getJavaScriptConfig('muzibu'),
            'seoLimits' => $this->seoRepository->getFieldLimits('muzibu')
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
        $seoRules = $this->seoRepository->getValidationRules('muzibu');

        return array_merge($rules, $seoRules);
    }

    /**
     * SEO skorunu hesapla
     */
    public function calculateSeoScore(array $seoData): array
    {
        return $this->seoRepository->calculateSeoScore($seoData, 'muzibu');
    }

    public function updateSeoField(int $id, string $locale, string $field, mixed $value): bool
    {
        $result = $this->muzibuRepository->updateSeoField($id, $locale, $field, $value);

        if ($result) {
            Log::info('SEO field updated', [
                'muzibu_id' => $id,
                'locale' => $locale,
                'field' => $field,
                'user_id' => auth()->id()
            ]);
        }

        return $result;
    }

    public function clearCache(): void
    {
        $this->muzibuRepository->clearCache();

        Log::info('Muzibu cache cleared', [
            'user_id' => auth()->id()
        ]);
    }
}
