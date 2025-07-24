<?php
namespace Modules\Page\App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Modules\Page\App\Contracts\PageRepositoryInterface;
use Modules\Page\App\Contracts\PageSeoRepositoryInterface;
use Modules\Page\App\Services\PageTabService;
use Modules\Page\App\Models\Page;

class PageService
{
    public function __construct(
        protected PageRepositoryInterface $pageRepository,
        protected PageSeoRepositoryInterface $seoRepository
    ) {}
    
    public function getPage(int $id): ?Page
    {
        return $this->pageRepository->findById($id);
    }
    
    public function getPageBySlug(string $slug, string $locale = 'tr'): ?Page
    {
        return $this->pageRepository->findBySlug($slug, $locale);
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
    
    public function createPage(array $data): Page
    {
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
        
        return $page;
    }
    
    public function updatePage(int $id, array $data): bool
    {
        $page = $this->pageRepository->findById($id);
        
        if (!$page) {
            return false;
        }
        
        // Slug güncelleme
        if (isset($data['title']) && is_array($data['title'])) {
            $data['slug'] = $this->generateSlugsFromTitles($data['title'], $page->slug ?? []);
        }
        
        // SEO verileri hazırlama
        if (isset($data['seo']) && is_array($data['seo'])) {
            $data['seo'] = $this->prepareSeoData($data['seo'], $page->seo ?? []);
        }
        
        $result = $this->pageRepository->update($id, $data);
        
        if ($result) {
            Log::info('Page updated', [
                'page_id' => $id,
                'title' => $data['title'] ?? 'unchanged',
                'user_id' => auth()->id()
            ]);
        }
        
        return $result;
    }
    
    public function deletePage(int $id): bool
    {
        $page = $this->pageRepository->findById($id);
        
        if (!$page) {
            return false;
        }
        
        // Ana sayfa silinmesine izin verme
        if ($page->is_homepage) {
            Log::warning('Attempted to delete homepage', [
                'page_id' => $id,
                'user_id' => auth()->id()
            ]);
            return false;
        }
        
        $result = $this->pageRepository->delete($id);
        
        if ($result) {
            Log::info('Page deleted', [
                'page_id' => $id,
                'title' => $page->title,
                'user_id' => auth()->id()
            ]);
        }
        
        return $result;
    }
    
    public function togglePageStatus(int $id): array
    {
        $page = $this->pageRepository->findById($id);
        
        if (!$page) {
            return [
                'success' => false,
                'message' => __('page::admin.page_not_found'),
                'type' => 'error'
            ];
        }
        
        // Ana sayfa kontrolü
        if ($page->is_homepage && $page->is_active) {
            return [
                'success' => false,
                'message' => __('page::admin.homepage_cannot_be_deactivated'),
                'type' => 'warning'
            ];
        }
        
        $result = $this->pageRepository->toggleActive($id);
        
        if ($result) {
            $page->refresh();
            
            Log::info('Page status toggled', [
                'page_id' => $id,
                'new_status' => $page->is_active,
                'user_id' => auth()->id()
            ]);
            
            return [
                'success' => true,
                'message' => __($page->is_active ? 'page::admin.page_activated' : 'page::admin.page_deactivated'),
                'type' => $page->is_active ? 'success' : 'warning',
                'new_status' => $page->is_active
            ];
        }
        
        return [
            'success' => false,
            'message' => __('page::admin.operation_failed'),
            'type' => 'error'
        ];
    }
    
    public function bulkDeletePages(array $ids): array
    {
        // Ana sayfaları çıkar
        $homepageIds = [];
        foreach ($ids as $id) {
            $page = $this->pageRepository->findById($id);
            if ($page && $page->is_homepage) {
                $homepageIds[] = $id;
            }
        }
        
        $allowedIds = array_diff($ids, $homepageIds);
        
        if (empty($allowedIds)) {
            return [
                'success' => false,
                'message' => __('page::admin.no_pages_can_be_deleted'),
                'deleted_count' => 0
            ];
        }
        
        $deletedCount = $this->pageRepository->bulkDelete($allowedIds);
        
        Log::info('Bulk delete performed', [
            'deleted_count' => $deletedCount,
            'skipped_homepages' => count($homepageIds),
            'user_id' => auth()->id()
        ]);
        
        return [
            'success' => true,
            'message' => __('page::admin.pages_deleted_successfully', ['count' => $deletedCount]),
            'deleted_count' => $deletedCount,
            'skipped_count' => count($homepageIds)
        ];
    }
    
    public function bulkToggleStatus(array $ids): array
    {
        $affectedCount = $this->pageRepository->bulkToggleActive($ids);
        
        Log::info('Bulk status toggle performed', [
            'affected_count' => $affectedCount,
            'user_id' => auth()->id()
        ]);
        
        return [
            'success' => true,
            'message' => __('page::admin.pages_status_updated', ['count' => $affectedCount]),
            'affected_count' => $affectedCount
        ];
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
        $page = $this->pageRepository->findById($id);
        
        if (!$page) {
            return $this->getEmptyFormData($language);
        }

        // Tab completion durumunu hesapla
        $allData = array_merge(
            $page->toArray(),
            $this->seoRepository->getSeoData($page, $language)
        );
        
        $tabCompletion = PageTabService::getTabCompletionStatus($allData);

        return [
            'page' => $page,
            'seoData' => $this->seoRepository->getSeoData($page, $language),
            'tabCompletion' => $tabCompletion,
            'tabConfig' => PageTabService::getJavaScriptConfig(),
            'seoLimits' => $this->seoRepository->getFieldLimits()
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
            'tabCompletion' => PageTabService::getTabCompletionStatus($emptyData),
            'tabConfig' => PageTabService::getJavaScriptConfig(),
            'seoLimits' => $this->seoRepository->getFieldLimits()
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
        $seoRules = $this->seoRepository->getValidationRules();
        
        return array_merge($rules, $seoRules);
    }

    /**
     * SEO skorunu hesapla
     */
    public function calculateSeoScore(array $seoData): array
    {
        return $this->seoRepository->calculateSeoScore($seoData);
    }

    public function clearCache(): void
    {
        $this->pageRepository->clearCache();
        
        Log::info('Page cache cleared', [
            'user_id' => auth()->id()
        ]);
    }
}