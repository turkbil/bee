<?php

declare(strict_types=1);

namespace Modules\Shop\App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Shop\App\Contracts\ShopCategoryRepositoryInterface;
use Modules\Shop\App\Models\ShopProductCategory;
use Illuminate\Support\Facades\Log;

class ShopCategoryService
{
    public function __construct(
        private readonly ShopCategoryRepositoryInterface $repository
    ) {}

    /**
     * Get paginated categories with filters
     */
    public function getPaginatedCategories(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getPaginated($filters, $perPage);
    }

    /**
     * Find category by ID
     */
    public function findCategory(int $id): ?ShopCategory
    {
        return $this->repository->findById($id);
    }

    /**
     * Find category by ID with SEO data
     */
    public function findCategoryWithSeo(int $id): ?ShopCategory
    {
        return $this->repository->findByIdWithSeo($id);
    }

    /**
     * Find category by slug
     */
    public function findBySlug(string $slug, string $locale = 'tr'): ?ShopCategory
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
    public function createCategory(array $data): array
    {
        try {
            $category = $this->repository->create($data);

            Log::info('Shop Category created', [
                'category_id' => $category->category_id,
                'title' => $category->title,
                'user_id' => auth()->id()
            ]);

            return [
                'success' => true,
                'message' => __('shop::admin.category_created'),
                'data' => $category
            ];
        } catch (\Exception $e) {
            Log::error('Shop Category creation failed', [
                'error' => $e->getMessage(),
                'data' => $data,
                'user_id' => auth()->id()
            ]);

            return [
                'success' => false,
                'message' => __('shop::admin.category_create_failed'),
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Update category
     */
    public function updateCategory(int $id, array $data): bool
    {
        $result = $this->repository->update($id, $data);

        if ($result) {
            Log::info('Shop Category updated', [
                'category_id' => $id,
                'user_id' => auth()->id()
            ]);
        }

        return $result;
    }

    /**
     * Delete category
     */
    public function deleteCategory(int $id): array
    {
        try {
            $category = $this->repository->findById($id);

            if (!$category) {
                return [
                    'success' => false,
                    'message' => __('shop::admin.category_not_found')
                ];
            }

            // Check if category has products
            if ($category->products()->count() > 0) {
                Log::warning('Cannot delete category with products', [
                    'category_id' => $id,
                    'product_count' => $category->products()->count()
                ]);

                return [
                    'success' => false,
                    'message' => __('shop::admin.category_has_products')
                ];
            }

            $result = $this->repository->delete($id);

            if ($result) {
                Log::info('Shop category deleted', [
                    'category_id' => $id,
                    'user_id' => auth()->id()
                ]);

                return [
                    'success' => true,
                    'message' => __('shop::admin.category_deleted')
                ];
            }

            return [
                'success' => false,
                'message' => __('shop::admin.category_delete_failed')
            ];
        } catch (\Exception $e) {
            Log::error('Shop category deletion failed', [
                'category_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return [
                'success' => false,
                'message' => __('shop::admin.category_delete_failed'),
                'error' => $e->getMessage()
            ];
        }
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
                    'message' => __('shop::admin.category_not_found')
                ];
            }

            $newStatus = !$category->is_active;
            $result = $this->repository->toggleActive($id);

            if ($result) {
                Log::info('Shop Category status toggled', [
                    'category_id' => $id,
                    'new_status' => $newStatus,
                    'user_id' => auth()->id()
                ]);

                return [
                    'success' => true,
                    'type' => 'success',
                    'message' => __('shop::admin.status_updated'),
                    'data' => $category,
                    'meta' => ['new_status' => $newStatus]
                ];
            }

            return [
                'success' => false,
                'type' => 'error',
                'message' => __('shop::admin.status_update_failed')
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
