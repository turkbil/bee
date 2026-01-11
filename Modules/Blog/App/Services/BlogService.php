<?php

declare(strict_types=1);

namespace Modules\Blog\App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Modules\Blog\App\Contracts\BlogRepositoryInterface;
use App\Contracts\GlobalSeoRepositoryInterface;
use App\Services\GlobalTabService;
use Modules\Blog\App\Models\Blog;
use Modules\Blog\App\DataTransferObjects\{BlogOperationResult, BulkOperationResult};
use Modules\Blog\App\Exceptions\{BlogNotFoundException, BlogCreationException, BlogProtectionException};
use App\Services\BaseService;
use Throwable;

readonly class BlogService extends BaseService
{
    public function __construct(
        private BlogRepositoryInterface $blogRepository,
        private GlobalSeoRepositoryInterface $seoRepository
    ) {}

    public function getBlog(int $id): Blog
    {
        return $this->blogRepository->findById($id)
            ?? throw BlogNotFoundException::withId($id);
    }

    public function getBlogBySlug(string $slug, string $locale = 'tr'): Blog
    {
        return $this->blogRepository->findBySlug($slug, $locale)
            ?? throw BlogNotFoundException::withSlug($slug, $locale);
    }

    public function getActiveBlogs(): Collection
    {
        return $this->blogRepository->getActive();
    }

    public function getPaginatedBlogs(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->blogRepository->getPaginated($filters, $perPage);
    }

    public function searchBlogs(string $term, array $locales = []): Collection
    {
        return $this->blogRepository->search($term, $locales);
    }

    public function createBlog(array $data): BlogOperationResult
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

            $blog = $this->blogRepository->create($data);

            Log::info('Blog created', [
                'blog_id' => $blog->blog_id,
                'title' => $blog->title,
                'user_id' => auth()->id()
            ]);

            return BlogOperationResult::success(
                message: __('blog::admin.blog_created_successfully'),
                data: $blog
            );
        } catch (Throwable $e) {
            Log::error('Blog creation failed', [
                'error' => $e->getMessage(),
                'data' => $data,
                'user_id' => auth()->id()
            ]);

            throw BlogCreationException::withDatabaseError($e->getMessage());
        }
    }

    public function updateBlog(int $id, array $data): BlogOperationResult
    {
        try {
            $blog = $this->blogRepository->findById($id)
                ?? throw BlogNotFoundException::withId($id);

            // Slug güncelleme
            if (isset($data['title']) && is_array($data['title'])) {
                $data['slug'] = $this->generateSlugsFromTitles($data['title'], $blog->slug ?? []);
            }

            // SEO verileri hazırlama
            if (isset($data['seo']) && is_array($data['seo'])) {
                $data['seo'] = $this->prepareSeoData($data['seo'], $blog->seo ?? []);
            }

            $this->blogRepository->update($id, $data);

            Log::info('Blog updated', [
                'blog_id' => $id,
                'title' => $data['title'] ?? 'unchanged',
                'user_id' => auth()->id()
            ]);

            return BlogOperationResult::success(
                message: __('blog::admin.blog_updated_successfully'),
                data: $blog->refresh()
            );
        } catch (BlogNotFoundException $e) {
            throw $e;
        } catch (Throwable $e) {
            Log::error('Blog update failed', [
                'blog_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return BlogOperationResult::error(
                message: __('blog::admin.update_failed'),
                type: 'error'
            );
        }
    }

    public function deleteBlog(int $id): BlogOperationResult
    {
        try {
            $blog = $this->blogRepository->findById($id)
                ?? throw BlogNotFoundException::withId($id);

            $this->blogRepository->delete($id);

            Log::info('Blog deleted', [
                'blog_id' => $id,
                'title' => $blog->title,
                'user_id' => auth()->id()
            ]);

            return BlogOperationResult::success(
                message: __('blog::admin.blog_deleted_successfully')
            );
        } catch (BlogNotFoundException | BlogProtectionException $e) {
            throw $e;
        } catch (Throwable $e) {
            Log::error('Blog deletion failed', [
                'blog_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return BlogOperationResult::error(
                message: __('blog::admin.deletion_failed'),
                type: 'error'
            );
        }
    }

    public function toggleBlogStatus(int $id): BlogOperationResult
    {
        try {
            $blog = $this->blogRepository->findById($id)
                ?? throw BlogNotFoundException::withId($id);

            $this->blogRepository->toggleActive($id);
            $blog->refresh();

            Log::info('Blog status toggled', [
                'blog_id' => $id,
                'new_status' => $blog->is_active,
                'user_id' => auth()->id()
            ]);

            return BlogOperationResult::success(
                message: __($blog->is_active ? 'admin.activated' : 'admin.deactivated'),
                data: $blog,
                meta: ['new_status' => $blog->is_active]
            );
        } catch (BlogNotFoundException $e) {
            return BlogOperationResult::error(
                message: __('blog::admin.blog_not_found'),
                type: 'error'
            );
        } catch (BlogProtectionException $e) {
            return BlogOperationResult::warning(
                message: $e->getMessage()
            );
        } catch (Throwable $e) {
            Log::error('Blog status toggle failed', [
                'blog_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return BlogOperationResult::error(
                message: __('admin.operation_failed'),
                type: 'error'
            );
        }
    }

    public function bulkDeleteBlogs(array $ids): BulkOperationResult
    {
        try {
            if (empty($ids)) {
                return BulkOperationResult::failure(
                    message: __('admin.no_blogs_selected')
                );
            }

            $deletedCount = $this->blogRepository->bulkDelete($ids);

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

    public function bulkToggleBlogStatus(array $ids): BulkOperationResult
    {
        try {
            $affectedCount = $this->blogRepository->bulkToggleActive($ids);

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
    public function prepareBlogForForm(int $id, string $language): array
    {
        // 🚨 PERFORMANCE FIX: Eager loading ile bir seferde çek
        $blog = $this->blogRepository->findByIdWithSeo($id);

        if (!$blog) {
            return $this->getEmptyFormData($language);
        }

        // 🚨 PERFORMANCE FIX: SEO data'yı sadece bir kez çek
        $seoData = $this->seoRepository->getSeoData($blog, $language);

        // Tab completion durumunu hesapla
        $allData = array_merge($blog->toArray(), $seoData);
        $tabCompletion = GlobalTabService::getTabCompletionStatus($allData, 'blog');

        return [
            'blog' => $blog,
            'seoData' => $seoData, // Tekrar çekme!
            'tabCompletion' => $tabCompletion,
            'tabConfig' => GlobalTabService::getJavaScriptConfig('blog'),
            'seoLimits' => $this->seoRepository->getFieldLimits('blog')
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
            'blog' => null,
            'seoData' => $emptyData,
            'tabCompletion' => GlobalTabService::getTabCompletionStatus($emptyData, 'blog'),
            'tabConfig' => GlobalTabService::getJavaScriptConfig('blog'),
            'seoLimits' => $this->seoRepository->getFieldLimits('blog')
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
        $seoRules = $this->seoRepository->getValidationRules('blog');

        return array_merge($rules, $seoRules);
    }

    /**
     * SEO skorunu hesapla
     */
    public function calculateSeoScore(array $seoData): array
    {
        return $this->seoRepository->calculateSeoScore($seoData, 'blog');
    }

    public function updateSeoField(int $id, string $locale, string $field, mixed $value): bool
    {
        $result = $this->blogRepository->updateSeoField($id, $locale, $field, $value);

        if ($result) {
            Log::info('SEO field updated', [
                'blog_id' => $id,
                'locale' => $locale,
                'field' => $field,
                'user_id' => auth()->id()
            ]);
        }

        return $result;
    }

    public function clearCache(): void
    {
        $this->blogRepository->clearCache();

        Log::info('Blog cache cleared', [
            'user_id' => auth()->id()
        ]);
    }
}
