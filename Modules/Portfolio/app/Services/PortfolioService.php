<?php

declare(strict_types=1);

namespace Modules\Portfolio\App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Modules\Portfolio\App\Contracts\PortfolioRepositoryInterface;
use App\Contracts\GlobalSeoRepositoryInterface;
use App\Services\GlobalTabService;
use Modules\Portfolio\App\Models\Portfolio;
use Modules\Portfolio\App\DataTransferObjects\{PortfolioOperationResult, BulkOperationResult};
use Modules\Portfolio\App\Exceptions\{PortfolioNotFoundException, PortfolioCreationException, PortfolioProtectionException};
use App\Services\BaseService;
use Throwable;

readonly class PortfolioService extends BaseService
{
    public function __construct(
        private PortfolioRepositoryInterface $portfolioRepository,
        private GlobalSeoRepositoryInterface $seoRepository
    ) {}

    public function getPortfolio(int $id): Portfolio
    {
        return $this->portfolioRepository->findById($id)
            ?? throw PortfolioNotFoundException::withId($id);
    }

    public function getPortfolioBySlug(string $slug, string $locale = 'tr'): Portfolio
    {
        return $this->portfolioRepository->findBySlug($slug, $locale)
            ?? throw PortfolioNotFoundException::withSlug($slug, $locale);
    }

    public function getActivePortfolios(): Collection
    {
        return $this->portfolioRepository->getActive();
    }

    public function getPaginatedPortfolios(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->portfolioRepository->getPaginated($filters, $perPage);
    }

    public function searchPortfolios(string $term, array $locales = []): Collection
    {
        return $this->portfolioRepository->search($term, $locales);
    }

    public function createPortfolio(array $data): PortfolioOperationResult
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

            $portfolio = $this->portfolioRepository->create($data);

            Log::info('Portfolio created', [
                'portfolio_id' => $portfolio->portfolio_id,
                'title' => $portfolio->title,
                'user_id' => auth()->id()
            ]);

            return PortfolioOperationResult::success(
                message: __('portfolio::admin.portfolio_created_successfully'),
                data: $portfolio
            );
        } catch (Throwable $e) {
            Log::error('Portfolio creation failed', [
                'error' => $e->getMessage(),
                'data' => $data,
                'user_id' => auth()->id()
            ]);

            throw PortfolioCreationException::withDatabaseError($e->getMessage());
        }
    }

    public function updatePortfolio(int $id, array $data): PortfolioOperationResult
    {
        try {
            $portfolio = $this->portfolioRepository->findById($id)
                ?? throw PortfolioNotFoundException::withId($id);

            // Slug güncelleme
            if (isset($data['title']) && is_array($data['title'])) {
                $data['slug'] = $this->generateSlugsFromTitles($data['title'], $portfolio->slug ?? []);
            }

            // SEO verileri hazırlama
            if (isset($data['seo']) && is_array($data['seo'])) {
                $data['seo'] = $this->prepareSeoData($data['seo'], $portfolio->seo ?? []);
            }

            $this->portfolioRepository->update($id, $data);

            Log::info('Portfolio updated', [
                'portfolio_id' => $id,
                'title' => $data['title'] ?? 'unchanged',
                'user_id' => auth()->id()
            ]);

            return PortfolioOperationResult::success(
                message: __('portfolio::admin.portfolio_updated_successfully'),
                data: $portfolio->refresh()
            );
        } catch (PortfolioNotFoundException $e) {
            throw $e;
        } catch (Throwable $e) {
            Log::error('Portfolio update failed', [
                'portfolio_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return PortfolioOperationResult::error(
                message: __('portfolio::admin.update_failed'),
                type: 'error'
            );
        }
    }

    public function deletePortfolio(int $id): PortfolioOperationResult
    {
        try {
            $portfolio = $this->portfolioRepository->findById($id)
                ?? throw PortfolioNotFoundException::withId($id);

            $this->portfolioRepository->delete($id);

            Log::info('Portfolio deleted', [
                'portfolio_id' => $id,
                'title' => $portfolio->title,
                'user_id' => auth()->id()
            ]);

            return PortfolioOperationResult::success(
                message: __('portfolio::admin.portfolio_deleted_successfully')
            );
        } catch (PortfolioNotFoundException | PortfolioProtectionException $e) {
            throw $e;
        } catch (Throwable $e) {
            Log::error('Portfolio deletion failed', [
                'portfolio_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return PortfolioOperationResult::error(
                message: __('portfolio::admin.deletion_failed'),
                type: 'error'
            );
        }
    }

    public function togglePortfolioStatus(int $id): PortfolioOperationResult
    {
        try {
            $portfolio = $this->portfolioRepository->findById($id)
                ?? throw PortfolioNotFoundException::withId($id);

            $this->portfolioRepository->toggleActive($id);
            $portfolio->refresh();

            Log::info('Portfolio status toggled', [
                'portfolio_id' => $id,
                'new_status' => $portfolio->is_active,
                'user_id' => auth()->id()
            ]);

            return PortfolioOperationResult::success(
                message: __($portfolio->is_active ? 'admin.activated' : 'admin.deactivated'),
                data: $portfolio,
                meta: ['new_status' => $portfolio->is_active]
            );
        } catch (PortfolioNotFoundException $e) {
            return PortfolioOperationResult::error(
                message: __('portfolio::admin.portfolio_not_found'),
                type: 'error'
            );
        } catch (PortfolioProtectionException $e) {
            return PortfolioOperationResult::warning(
                message: $e->getMessage()
            );
        } catch (Throwable $e) {
            Log::error('Portfolio status toggle failed', [
                'portfolio_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return PortfolioOperationResult::error(
                message: __('admin.operation_failed'),
                type: 'error'
            );
        }
    }

    public function bulkDeletePortfolios(array $ids): BulkOperationResult
    {
        try {
            if (empty($ids)) {
                return BulkOperationResult::failure(
                    message: __('admin.no_portfolios_selected')
                );
            }

            $deletedCount = $this->portfolioRepository->bulkDelete($ids);

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

    public function bulkTogglePortfolioStatus(array $ids): BulkOperationResult
    {
        try {
            $affectedCount = $this->portfolioRepository->bulkToggleActive($ids);

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
    public function preparePortfolioForForm(int $id, string $language): array
    {
        // 🚨 PERFORMANCE FIX: Eager loading ile bir seferde çek
        $portfolio = $this->portfolioRepository->findByIdWithSeo($id);

        if (!$portfolio) {
            return $this->getEmptyFormData($language);
        }

        // 🚨 PERFORMANCE FIX: SEO data'yı sadece bir kez çek
        $seoData = $this->seoRepository->getSeoData($portfolio, $language);

        // Tab completion durumunu hesapla
        $allData = array_merge($portfolio->toArray(), $seoData);
        $tabCompletion = GlobalTabService::getTabCompletionStatus($allData, 'portfolio');

        return [
            'portfolio' => $portfolio,
            'seoData' => $seoData, // Tekrar çekme!
            'tabCompletion' => $tabCompletion,
            'tabConfig' => GlobalTabService::getJavaScriptConfig('portfolio'),
            'seoLimits' => $this->seoRepository->getFieldLimits('portfolio')
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
            'portfolio' => null,
            'seoData' => $emptyData,
            'tabCompletion' => GlobalTabService::getTabCompletionStatus($emptyData, 'portfolio'),
            'tabConfig' => GlobalTabService::getJavaScriptConfig('portfolio'),
            'seoLimits' => $this->seoRepository->getFieldLimits('portfolio')
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
        $seoRules = $this->seoRepository->getValidationRules('portfolio');

        return array_merge($rules, $seoRules);
    }

    /**
     * SEO skorunu hesapla
     */
    public function calculateSeoScore(array $seoData): array
    {
        return $this->seoRepository->calculateSeoScore($seoData, 'portfolio');
    }

    public function updateSeoField(int $id, string $locale, string $field, mixed $value): bool
    {
        $result = $this->portfolioRepository->updateSeoField($id, $locale, $field, $value);

        if ($result) {
            Log::info('SEO field updated', [
                'portfolio_id' => $id,
                'locale' => $locale,
                'field' => $field,
                'user_id' => auth()->id()
            ]);
        }

        return $result;
    }

    public function clearCache(): void
    {
        $this->portfolioRepository->clearCache();

        Log::info('Portfolio cache cleared', [
            'user_id' => auth()->id()
        ]);
    }
}
