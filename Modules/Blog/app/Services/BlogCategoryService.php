<?php

declare(strict_types=1);

namespace Modules\Blog\App\Services;

use Modules\Blog\App\Models\BlogCategory;
use Modules\Blog\App\Repositories\BlogCategoryRepository;
use Illuminate\Support\Facades\Log;

class BlogCategoryService
{
    public function __construct(
        private readonly BlogCategoryRepository $repository
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
    public function findCategory(int $id): ?BlogCategory
    {
        return $this->repository->findById($id);
    }

    /**
     * Find category by ID with SEO data
     */
    public function findCategoryWithSeo(int $id): ?BlogCategory
    {
        return $this->repository->findByIdWithSeo($id);
    }

    /**
     * Find category by slug
     */
    public function findBySlug(string $slug, string $locale = 'tr'): ?BlogCategory
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

            Log::info('Blog Category created', [
                'category_id' => $category->category_id,
                'title' => $category->title,
                'user_id' => auth()->id()
            ]);

            return [
                'success' => true,
                'message' => __('blog::admin.category_created'),
                'data' => $category
            ];
        } catch (\Exception $e) {
            Log::error('Blog Category creation failed', [
                'error' => $e->getMessage(),
                'data' => $data,
                'user_id' => auth()->id()
            ]);

            return [
                'success' => false,
                'message' => __('blog::admin.category_create_failed'),
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
            Log::info('Blog Category updated', [
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
                    'message' => __('blog::admin.category_not_found')
                ];
            }

            // Check if category has blogs
            if ($category->blogs()->count() > 0) {
                Log::warning('Cannot delete category with blogs', [
                    'category_id' => $id,
                    'blog_count' => $category->blogs()->count()
                ]);

                return [
                    'success' => false,
                    'message' => __('blog::admin.category_has_blogs')
                ];
            }

            $result = $this->repository->delete($id);

            if ($result) {
                Log::info('Blog Category deleted', [
                    'category_id' => $id,
                    'user_id' => auth()->id()
                ]);

                return [
                    'success' => true,
                    'message' => __('blog::admin.category_deleted')
                ];
            }

            return [
                'success' => false,
                'message' => __('blog::admin.category_delete_failed')
            ];
        } catch (\Exception $e) {
            Log::error('Blog Category deletion failed', [
                'category_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return [
                'success' => false,
                'message' => __('blog::admin.category_delete_failed'),
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
                    'message' => __('blog::admin.category_not_found')
                ];
            }

            $newStatus = !$category->is_active;
            $result = $this->repository->toggleActive($id);

            if ($result) {
                Log::info('Blog Category status toggled', [
                    'category_id' => $id,
                    'new_status' => $newStatus,
                    'user_id' => auth()->id()
                ]);

                return [
                    'success' => true,
                    'type' => 'success',
                    'message' => __('blog::admin.status_updated'),
                    'data' => $category,
                    'meta' => ['new_status' => $newStatus]
                ];
            }

            return [
                'success' => false,
                'type' => 'error',
                'message' => __('blog::admin.status_update_failed')
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
