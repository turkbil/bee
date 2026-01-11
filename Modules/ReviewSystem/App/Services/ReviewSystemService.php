<?php

declare(strict_types=1);

namespace Modules\ReviewSystem\App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Modules\ReviewSystem\App\Contracts\ReviewSystemRepositoryInterface;
use App\Contracts\GlobalSeoRepositoryInterface;
use App\Services\GlobalTabService;
use Modules\ReviewSystem\App\Models\ReviewSystem;
use Modules\ReviewSystem\App\DataTransferObjects\{ReviewSystemOperationResult, BulkOperationResult};
use Modules\ReviewSystem\App\Exceptions\{ReviewSystemNotFoundException, ReviewSystemCreationException, ReviewSystemProtectionException};
use App\Services\BaseService;
use Throwable;

readonly class ReviewSystemService extends BaseService
{
    public function __construct(
        private ReviewSystemRepositoryInterface $reviewsystemRepository,
        private GlobalSeoRepositoryInterface $seoRepository
    ) {}

    public function getReviewSystem(int $id): ReviewSystem
    {
        return $this->reviewsystemRepository->findById($id)
            ?? throw ReviewSystemNotFoundException::withId($id);
    }

    public function getReviewSystemBySlug(string $slug, string $locale = 'tr'): ReviewSystem
    {
        return $this->reviewsystemRepository->findBySlug($slug, $locale)
            ?? throw ReviewSystemNotFoundException::withSlug($slug, $locale);
    }

    public function getActiveReviewSystems(): Collection
    {
        return $this->reviewsystemRepository->getActive();
    }

    public function getPaginatedReviewSystems(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->reviewsystemRepository->getPaginated($filters, $perPage);
    }

    public function searchReviewSystems(string $term, array $locales = []): Collection
    {
        return $this->reviewsystemRepository->search($term, $locales);
    }

    public function createReviewSystem(array $data): ReviewSystemOperationResult
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

            $reviewsystem = $this->reviewsystemRepository->create($data);

            Log::info('ReviewSystem created', [
                'reviewsystem_id' => $reviewsystem->reviewsystem_id,
                'title' => $reviewsystem->title,
                'user_id' => auth()->id()
            ]);

            return ReviewSystemOperationResult::success(
                message: __('reviewsystem::admin.reviewsystem_created_successfully'),
                data: $reviewsystem
            );
        } catch (Throwable $e) {
            Log::error('ReviewSystem creation failed', [
                'error' => $e->getMessage(),
                'data' => $data,
                'user_id' => auth()->id()
            ]);

            throw ReviewSystemCreationException::withDatabaseError($e->getMessage());
        }
    }

    public function updateReviewSystem(int $id, array $data): ReviewSystemOperationResult
    {
        try {
            $reviewsystem = $this->reviewsystemRepository->findById($id)
                ?? throw ReviewSystemNotFoundException::withId($id);

            // Slug güncelleme
            if (isset($data['title']) && is_array($data['title'])) {
                $data['slug'] = $this->generateSlugsFromTitles($data['title'], $reviewsystem->slug ?? []);
            }

            // SEO verileri hazırlama
            if (isset($data['seo']) && is_array($data['seo'])) {
                $data['seo'] = $this->prepareSeoData($data['seo'], $reviewsystem->seo ?? []);
            }

            $this->reviewsystemRepository->update($id, $data);

            Log::info('ReviewSystem updated', [
                'reviewsystem_id' => $id,
                'title' => $data['title'] ?? 'unchanged',
                'user_id' => auth()->id()
            ]);

            return ReviewSystemOperationResult::success(
                message: __('reviewsystem::admin.reviewsystem_updated_successfully'),
                data: $reviewsystem->refresh()
            );
        } catch (ReviewSystemNotFoundException $e) {
            throw $e;
        } catch (Throwable $e) {
            Log::error('ReviewSystem update failed', [
                'reviewsystem_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return ReviewSystemOperationResult::error(
                message: __('reviewsystem::admin.update_failed'),
                type: 'error'
            );
        }
    }

    public function deleteReviewSystem(int $id): ReviewSystemOperationResult
    {
        try {
            $reviewsystem = $this->reviewsystemRepository->findById($id)
                ?? throw ReviewSystemNotFoundException::withId($id);

            $this->reviewsystemRepository->delete($id);

            Log::info('ReviewSystem deleted', [
                'reviewsystem_id' => $id,
                'title' => $reviewsystem->title,
                'user_id' => auth()->id()
            ]);

            return ReviewSystemOperationResult::success(
                message: __('reviewsystem::admin.reviewsystem_deleted_successfully')
            );
        } catch (ReviewSystemNotFoundException | ReviewSystemProtectionException $e) {
            throw $e;
        } catch (Throwable $e) {
            Log::error('ReviewSystem deletion failed', [
                'reviewsystem_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return ReviewSystemOperationResult::error(
                message: __('reviewsystem::admin.deletion_failed'),
                type: 'error'
            );
        }
    }

    public function toggleReviewSystemStatus(int $id): ReviewSystemOperationResult
    {
        try {
            $reviewsystem = $this->reviewsystemRepository->findById($id)
                ?? throw ReviewSystemNotFoundException::withId($id);

            $this->reviewsystemRepository->toggleActive($id);
            $reviewsystem->refresh();

            Log::info('ReviewSystem status toggled', [
                'reviewsystem_id' => $id,
                'new_status' => $reviewsystem->is_active,
                'user_id' => auth()->id()
            ]);

            return ReviewSystemOperationResult::success(
                message: __($reviewsystem->is_active ? 'admin.activated' : 'admin.deactivated'),
                data: $reviewsystem,
                meta: ['new_status' => $reviewsystem->is_active]
            );
        } catch (ReviewSystemNotFoundException $e) {
            return ReviewSystemOperationResult::error(
                message: __('reviewsystem::admin.reviewsystem_not_found'),
                type: 'error'
            );
        } catch (ReviewSystemProtectionException $e) {
            return ReviewSystemOperationResult::warning(
                message: $e->getMessage()
            );
        } catch (Throwable $e) {
            Log::error('ReviewSystem status toggle failed', [
                'reviewsystem_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return ReviewSystemOperationResult::error(
                message: __('admin.operation_failed'),
                type: 'error'
            );
        }
    }

    public function bulkDeleteReviewSystems(array $ids): BulkOperationResult
    {
        try {
            if (empty($ids)) {
                return BulkOperationResult::failure(
                    message: __('admin.no_reviewsystems_selected')
                );
            }

            $deletedCount = $this->reviewsystemRepository->bulkDelete($ids);

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

    public function bulkToggleReviewSystemStatus(array $ids): BulkOperationResult
    {
        try {
            $affectedCount = $this->reviewsystemRepository->bulkToggleActive($ids);

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
    public function prepareReviewSystemForForm(int $id, string $language): array
    {
        // 🚨 PERFORMANCE FIX: Eager loading ile bir seferde çek
        $reviewsystem = $this->reviewsystemRepository->findByIdWithSeo($id);

        if (!$reviewsystem) {
            return $this->getEmptyFormData($language);
        }

        // 🚨 PERFORMANCE FIX: SEO data'yı sadece bir kez çek
        $seoData = $this->seoRepository->getSeoData($reviewsystem, $language);

        // Tab completion durumunu hesapla
        $allData = array_merge($reviewsystem->toArray(), $seoData);
        $tabCompletion = GlobalTabService::getTabCompletionStatus($allData, 'reviewsystem');

        return [
            'reviewsystem' => $reviewsystem,
            'seoData' => $seoData, // Tekrar çekme!
            'tabCompletion' => $tabCompletion,
            'tabConfig' => GlobalTabService::getJavaScriptConfig('reviewsystem'),
            'seoLimits' => $this->seoRepository->getFieldLimits('reviewsystem')
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
            'reviewsystem' => null,
            'seoData' => $emptyData,
            'tabCompletion' => GlobalTabService::getTabCompletionStatus($emptyData, 'reviewsystem'),
            'tabConfig' => GlobalTabService::getJavaScriptConfig('reviewsystem'),
            'seoLimits' => $this->seoRepository->getFieldLimits('reviewsystem')
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
        $seoRules = $this->seoRepository->getValidationRules('reviewsystem');

        return array_merge($rules, $seoRules);
    }

    /**
     * SEO skorunu hesapla
     */
    public function calculateSeoScore(array $seoData): array
    {
        return $this->seoRepository->calculateSeoScore($seoData, 'reviewsystem');
    }

    public function updateSeoField(int $id, string $locale, string $field, mixed $value): bool
    {
        $result = $this->reviewsystemRepository->updateSeoField($id, $locale, $field, $value);

        if ($result) {
            Log::info('SEO field updated', [
                'reviewsystem_id' => $id,
                'locale' => $locale,
                'field' => $field,
                'user_id' => auth()->id()
            ]);
        }

        return $result;
    }

    public function clearCache(): void
    {
        $this->reviewsystemRepository->clearCache();

        Log::info('ReviewSystem cache cleared', [
            'user_id' => auth()->id()
        ]);
    }
}
