<?php

declare(strict_types=1);

namespace Modules\Portfolio\App\Services;

use Modules\Portfolio\App\Models\PortfolioCategory;
use Modules\Portfolio\App\Repositories\PortfolioCategoryRepository;
use Illuminate\Support\Facades\Log;

class PortfolioCategoryService
{
    public function __construct(
        private readonly PortfolioCategoryRepository $repository
    ) {}

    /**
     * Get paginated categories with filters
     */
    public function getPaginatedCategories(array $filters, int $perPage = 15)
    {
        return $this->repository->getPaginated($filters, $perPage);
    }

    /**
     * Find category by ID
     */
    public function findCategory(int $id): ?PortfolioCategory
    {
        return $this->repository->findById($id);
    }

    /**
     * Find category by ID with SEO data
     */
    public function findCategoryWithSeo(int $id): ?PortfolioCategory
    {
        return $this->repository->findByIdWithSeo($id);
    }

    /**
     * Find category by slug
     */
    public function findBySlug(string $slug, string $locale = 'tr'): ?PortfolioCategory
    {
        return $this->repository->findBySlug($slug, $locale);
    }

    /**
     * Get active categories
     */
    public function getActiveCategories()
    {
        return $this->repository->getActive();
    }

    /**
     * Create new category
     */
    public function createCategory(array $data): PortfolioCategory
    {
        $category = $this->repository->create($data);

        Log::info('Portfolio Category created', [
            'category_id' => $category->category_id,
            'title' => $category->title,
            'user_id' => auth()->id()
        ]);

        return $category;
    }

    /**
     * Update category
     */
    public function updateCategory(int $id, array $data): bool
    {
        $result = $this->repository->update($id, $data);

        if ($result) {
            Log::info('Portfolio Category updated', [
                'category_id' => $id,
                'user_id' => auth()->id()
            ]);
        }

        return $result;
    }

    /**
     * Delete category
     */
    public function deleteCategory(int $id): bool
    {
        $category = $this->repository->findById($id);

        if (!$category) {
            return false;
        }

        // Check if category has portfolios
        if ($category->portfolios()->count() > 0) {
            Log::warning('Cannot delete category with portfolios', [
                'category_id' => $id,
                'portfolio_count' => $category->portfolios()->count()
            ]);
            return false;
        }

        $result = $this->repository->delete($id);

        if ($result) {
            Log::info('Portfolio Category deleted', [
                'category_id' => $id,
                'user_id' => auth()->id()
            ]);
        }

        return $result;
    }

    /**
     * Toggle category active status
     */
    public function toggleCategoryStatus(int $id): array
    {
        try {
            $category = $this->repository->findById($id);

            if (!$category) {
                return [
                    'success' => false,
                    'type' => 'error',
                    'message' => __('portfolio::admin.category_not_found')
                ];
            }

            $newStatus = !$category->is_active;
            $result = $this->repository->toggleActive($id);

            if ($result) {
                Log::info('Portfolio Category status toggled', [
                    'category_id' => $id,
                    'new_status' => $newStatus,
                    'user_id' => auth()->id()
                ]);

                return [
                    'success' => true,
                    'type' => 'success',
                    'message' => __('portfolio::admin.status_updated'),
                    'data' => $category,
                    'meta' => ['new_status' => $newStatus]
                ];
            }

            return [
                'success' => false,
                'type' => 'error',
                'message' => __('portfolio::admin.status_update_failed')
            ];
        } catch (\Exception $e) {
            Log::error('Category status toggle error', [
                'category_id' => $id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'type' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Search categories
     */
    public function searchCategories(string $term, array $locales = [])
    {
        return $this->repository->search($term, $locales);
    }

    /**
     * Bulk delete categories
     */
    public function bulkDeleteCategories(array $ids): int
    {
        return $this->repository->bulkDelete($ids);
    }

    /**
     * Bulk toggle active status
     */
    public function bulkToggleActive(array $ids): int
    {
        return $this->repository->bulkToggleActive($ids);
    }

    /**
     * Clear cache
     */
    public function clearCache(): void
    {
        $this->repository->clearCache();
    }

    /**
     * Prepare category data for form
     */
    public function prepareCategoryForForm(int $id, string $currentLanguage): array
    {
        $category = $this->findCategoryWithSeo($id);

        if (!$category) {
            return [
                'category' => null,
                'tabCompletion' => []
            ];
        }

        // Tab completion status hesapla
        $tabCompletion = [
            'general' => !empty($category->getTranslated('title', $currentLanguage)),
            'seo' => $category->seoSetting !== null
        ];

        return [
            'category' => $category,
            'tabCompletion' => $tabCompletion
        ];
    }
}
