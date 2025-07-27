<?php

declare(strict_types=1);

namespace Modules\Page\App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Modules\Page\App\Contracts\PageRepositoryInterface;
use App\Contracts\GlobalSeoRepositoryInterface;
use App\Services\GlobalTabService;
use Modules\Page\App\Models\Page;
use Modules\Page\App\DataTransferObjects\{PageOperationResult, BulkOperationResult};
use Modules\Page\App\Exceptions\{PageNotFoundException, PageCreationException, HomepageProtectionException};
use Throwable;

readonly class PageService
{
    public function __construct(
        private PageRepositoryInterface $pageRepository,
        private GlobalSeoRepositoryInterface $seoRepository
    ) {}
    
    public function getPage(int $id): Page
    {
        return $this->pageRepository->findById($id) 
            ?? throw PageNotFoundException::withId($id);
    }
    
    public function getPageBySlug(string $slug, string $locale = 'tr'): Page
    {
        return $this->pageRepository->findBySlug($slug, $locale)
            ?? throw PageNotFoundException::withSlug($slug, $locale);
    }
    
    public function getActivePages(): Collection
    {
        return $this->pageRepository->getActive();
    }
    
    public function getHomepage(): ?Page
    {
        return $this->pageRepository->getHomepage();
    }
    
    public function getPaginatedPages(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->pageRepository->getPaginated($filters, $perPage);
    }
    
    public function searchPages(string $term, array $locales = []): Collection
    {
        return $this->pageRepository->search($term, $locales);
    }
    
    public function createPage(array $data): PageOperationResult
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
            
            $page = $this->pageRepository->create($data);
            
            Log::info('Page created', [
                'page_id' => $page->page_id,
                'title' => $page->title,
                'user_id' => auth()->id()
            ]);
            
            return PageOperationResult::success(
                message: __('page::admin.page_created_successfully'),
                data: $page
            );
            
        } catch (Throwable $e) {
            Log::error('Page creation failed', [
                'error' => $e->getMessage(),
                'data' => $data,
                'user_id' => auth()->id()
            ]);
            
            throw PageCreationException::withDatabaseError($e->getMessage());
        }
    }
    
    public function updatePage(int $id, array $data): PageOperationResult
    {
        try {
            $page = $this->pageRepository->findById($id)
                ?? throw PageNotFoundException::withId($id);
            
            // Slug güncelleme
            if (isset($data['title']) && is_array($data['title'])) {
                $data['slug'] = $this->generateSlugsFromTitles($data['title'], $page->slug ?? []);
            }
            
            // SEO verileri hazırlama
            if (isset($data['seo']) && is_array($data['seo'])) {
                $data['seo'] = $this->prepareSeoData($data['seo'], $page->seo ?? []);
            }
            
            $this->pageRepository->update($id, $data);
            
            Log::info('Page updated', [
                'page_id' => $id,
                'title' => $data['title'] ?? 'unchanged',
                'user_id' => auth()->id()
            ]);
            
            return PageOperationResult::success(
                message: __('page::admin.page_updated_successfully'),
                data: $page->refresh()
            );
            
        } catch (PageNotFoundException $e) {
            throw $e;
        } catch (Throwable $e) {
            Log::error('Page update failed', [
                'page_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
            
            return PageOperationResult::error(
                message: __('page::admin.update_failed'),
                type: 'error'
            );
        }
    }
    
    public function deletePage(int $id): PageOperationResult
    {
        try {
            $page = $this->pageRepository->findById($id)
                ?? throw PageNotFoundException::withId($id);
            
            // Ana sayfa silinmesine izin verme
            if ($page->is_homepage) {
                Log::warning('Attempted to delete homepage', [
                    'page_id' => $id,
                    'user_id' => auth()->id()
                ]);
                
                throw HomepageProtectionException::cannotDelete($id);
            }
            
            $this->pageRepository->delete($id);
            
            Log::info('Page deleted', [
                'page_id' => $id,
                'title' => $page->title,
                'user_id' => auth()->id()
            ]);
            
            return PageOperationResult::success(
                message: __('page::admin.page_deleted_successfully')
            );
            
        } catch (PageNotFoundException|HomepageProtectionException $e) {
            throw $e;
        } catch (Throwable $e) {
            Log::error('Page deletion failed', [
                'page_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
            
            return PageOperationResult::error(
                message: __('page::admin.deletion_failed'),
                type: 'error'
            );
        }
    }
    
    public function togglePageStatus(int $id): PageOperationResult
    {
        try {
            $page = $this->pageRepository->findById($id)
                ?? throw PageNotFoundException::withId($id);
            
            // Ana sayfa kontrolü
            if ($page->is_homepage && $page->is_active) {
                throw HomepageProtectionException::cannotDeactivate($id);
            }
            
            $this->pageRepository->toggleActive($id);
            $page->refresh();
            
            Log::info('Page status toggled', [
                'page_id' => $id,
                'new_status' => $page->is_active,
                'user_id' => auth()->id()
            ]);
            
            return PageOperationResult::success(
                message: __($page->is_active ? 'admin.activated' : 'admin.deactivated'),
                data: $page,
                meta: ['new_status' => $page->is_active]
            );
            
        } catch (PageNotFoundException $e) {
            return PageOperationResult::error(
                message: __('admin.page_not_found'),
                type: 'error'
            );
        } catch (HomepageProtectionException $e) {
            return PageOperationResult::warning(
                message: __('admin.homepage_cannot_be_deactivated')
            );
        } catch (Throwable $e) {
            Log::error('Page status toggle failed', [
                'page_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
            
            return PageOperationResult::error(
                message: __('admin.operation_failed'),
                type: 'error'
            );
        }
    }
    
    public function bulkDeletePages(array $ids): BulkOperationResult
    {
        try {
            // Ana sayfaları çıkar
            $homepageIds = [];
            foreach ($ids as $id) {
                $page = $this->pageRepository->findById($id);
                if ($page?->is_homepage) {
                    $homepageIds[] = $id;
                }
            }
            
            $allowedIds = array_diff($ids, $homepageIds);
            
            if (empty($allowedIds)) {
                return BulkOperationResult::failure(
                    message: __('admin.no_pages_can_be_deleted')
                );
            }
            
            $deletedCount = $this->pageRepository->bulkDelete($allowedIds);
            
            Log::info('Bulk delete performed', [
                'deleted_count' => $deletedCount,
                'skipped_homepages' => count($homepageIds),
                'user_id' => auth()->id()
            ]);
            
            return count($homepageIds) > 0
                ? BulkOperationResult::partial(
                    message: __('admin.bulk_delete_partial'),
                    affectedCount: $deletedCount,
                    skippedCount: count($homepageIds)
                )
                : BulkOperationResult::success(
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
    
    public function bulkToggleStatus(array $ids): BulkOperationResult
    {
        try {
            $affectedCount = $this->pageRepository->bulkToggleActive($ids);
            
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
    
    public function updateSeoField(int $id, string $locale, string $field, mixed $value): bool
    {
        $result = $this->pageRepository->updateSeoField($id, $locale, $field, $value);
        
        if ($result) {
            Log::info('SEO field updated', [
                'page_id' => $id,
                'locale' => $locale,
                'field' => $field,
                'user_id' => auth()->id()
            ]);
        }
        
        return $result;
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
                // Boş değerleri temizle
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
    public function preparePageForForm(int $id, string $language): array
    {
        // 🚨 PERFORMANCE FIX: Eager loading ile bir seferde çek
        $page = $this->pageRepository->findByIdWithSeo($id);
        
        if (!$page) {
            return $this->getEmptyFormData($language);
        }

        // 🚨 PERFORMANCE FIX: SEO data'yı sadece bir kez çek
        $seoData = $this->seoRepository->getSeoData($page, $language);
        
        // Tab completion durumunu hesapla
        $allData = array_merge($page->toArray(), $seoData);
        $tabCompletion = GlobalTabService::getTabCompletionStatus($allData, 'page');

        return [
            'page' => $page,
            'seoData' => $seoData, // Tekrar çekme!
            'tabCompletion' => $tabCompletion,
            'tabConfig' => GlobalTabService::getJavaScriptConfig('page'),
            'seoLimits' => $this->seoRepository->getFieldLimits('page')
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
            'page' => null,
            'seoData' => $emptyData,
            'tabCompletion' => GlobalTabService::getTabCompletionStatus($emptyData, 'page'),
            'tabConfig' => GlobalTabService::getJavaScriptConfig('page'),
            'seoLimits' => $this->seoRepository->getFieldLimits('page')
        ];
    }

    /**
     * Form validation kurallarını getir
     */
    public function getValidationRules(array $availableLanguages): array
    {
        $rules = [
            'inputs.css' => 'nullable|string',
            'inputs.js' => 'nullable|string',
            'inputs.is_active' => 'boolean',
            'inputs.is_homepage' => 'boolean',
        ];
        
        // Çoklu dil alanları
        foreach ($availableLanguages as $lang) {
            $rules["multiLangInputs.{$lang}.title"] = $lang === 'tr' ? 'required|min:3|max:255' : 'nullable|min:3|max:255';
            $rules["multiLangInputs.{$lang}.slug"] = 'nullable|string|max:255';
            $rules["multiLangInputs.{$lang}.body"] = 'nullable|string';
        }
        
        // SEO validation kuralları
        $seoRules = $this->seoRepository->getValidationRules('page');
        
        return array_merge($rules, $seoRules);
    }

    /**
     * SEO skorunu hesapla
     */
    public function calculateSeoScore(array $seoData): array
    {
        return $this->seoRepository->calculateSeoScore($seoData, 'page');
    }

    public function clearCache(): void
    {
        $this->pageRepository->clearCache();
        
        Log::info('Page cache cleared', [
            'user_id' => auth()->id()
        ]);
    }
}