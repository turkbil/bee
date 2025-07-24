<?php

namespace Modules\Page\App\Repositories;

use Modules\Page\App\Contracts\PageSeoRepositoryInterface;
use Modules\Page\App\Services\PageSeoService;
use Modules\Page\App\Models\Page;
use Illuminate\Support\Facades\Cache;

class PageSeoRepository implements PageSeoRepositoryInterface
{
    protected string $cachePrefix = 'page_seo';
    protected int $cacheTtl = 1800; // 30 dakika

    /**
     * SEO verilerini getir
     */
    public function getSeoData(Page $page, string $language): array
    {
        // Admin panelinde cache kullanma - Fresh data
        if (request()->is('admin*')) {
            return $this->getFreshSeoData($page, $language);
        }
        
        // Public sayfalarda cache kullan
        $cacheKey = $this->getCacheKey("seo_data.{$page->page_id}.{$language}");
        
        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($page, $language) {
            return $this->getFreshSeoData($page, $language);
        });
    }
    
    /**
     * Fresh SEO data çek (cache'siz)
     */
    protected function getFreshSeoData(Page $page, string $language): array
    {
        $seoSettings = $page->seoSetting;
        
        if (!$seoSettings) {
            return $this->getEmptySeoData();
        }

        return [
            'seo_title' => $seoSettings->getTitle($language) ?? '',
            'seo_description' => $seoSettings->getDescription($language) ?? '',
            'seo_keywords' => $seoSettings->getKeywords($language) ?? '',
            'canonical_url' => $seoSettings->getCanonicalUrl($language) ?? '',
            'robots' => $seoSettings->getRobots($language) ?? 'index,follow',
            'og_title' => $seoSettings->getOgTitle($language) ?? '',
            'og_description' => $seoSettings->getOgDescription($language) ?? '',
            'og_image' => $seoSettings->getOgImage($language) ?? '',
        ];
    }

    /**
     * SEO verilerini kaydet
     */
    public function saveSeoData(Page $page, string $language, array $seoData): bool
    {
        try {
            $seoSettings = $page->seoSetting;
            
            if (!$seoSettings) {
                $seoSettings = $page->seoSetting()->create([
                    'model_type' => Page::class,
                    'model_id' => $page->page_id,
                ]);
            }

            // Her alanı ayrı ayrı kaydet
            foreach ($seoData as $field => $value) {
                $this->updateSeoField($page, $language, $field, $value);
            }

            $this->clearSeoCache($page);
            return true;
            
        } catch (\Exception $e) {
            \Log::error('SEO data save failed', [
                'page_id' => $page->page_id,
                'language' => $language,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Belirli bir SEO alanını güncelle
     */
    public function updateSeoField(Page $page, string $language, string $field, mixed $value): bool
    {
        try {
            $seoSettings = $page->seoSetting;
            
            if (!$seoSettings) {
                return false;
            }

            $methodMap = [
                'seo_title' => 'setTitle',
                'seo_description' => 'setDescription', 
                'seo_keywords' => 'setKeywords',
                'canonical_url' => 'setCanonicalUrl',
                'robots' => 'setRobots',
                'og_title' => 'setOgTitle',
                'og_description' => 'setOgDescription',
                'og_image' => 'setOgImage'
            ];

            if (isset($methodMap[$field])) {
                $method = $methodMap[$field];
                $seoSettings->$method($language, $value);
                $seoSettings->save();
                
                $this->clearSeoCache($page);
                return true;
            }

            return false;
            
        } catch (\Exception $e) {
            \Log::error('SEO field update failed', [
                'page_id' => $page->page_id,
                'language' => $language,
                'field' => $field,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * SEO skorunu hesapla
     */
    public function calculateSeoScore(array $seoData): array
    {
        return PageSeoService::calculateSeoScore($seoData);
    }

    /**
     * SEO validation kurallarını getir
     */
    public function getValidationRules(): array
    {
        return PageSeoService::getSeoValidationRules();
    }

    /**
     * SEO alanlarının limitlerini getir
     */
    public function getFieldLimits(): array
    {
        return PageSeoService::getSeoLimits();
    }

    /**
     * Keyword'leri parse et
     */
    public function parseKeywords(?string $keywords): array
    {
        return PageSeoService::parseKeywords($keywords);
    }

    /**
     * SEO verilerini temizle/cache'den sil
     */
    public function clearSeoCache(Page $page): void
    {
        $tenantId = tenant() ? tenant()->id : 'landlord';
        $pattern = "{$this->cachePrefix}.tenant.{$tenantId}.seo_data.{$page->page_id}.*";
        
        // Laravel cache'de pattern-based clear yoksa manuel clear
        $languages = ['tr', 'en', 'ar']; // Aktif dilleri al
        
        foreach ($languages as $lang) {
            $cacheKey = $this->getCacheKey("seo_data.{$page->page_id}.{$lang}");
            Cache::forget($cacheKey);
        }
    }

    /**
     * Boş SEO data template'i
     */
    private function getEmptySeoData(): array
    {
        return [
            'seo_title' => '',
            'seo_description' => '',
            'seo_keywords' => '',
            'canonical_url' => '',
            'robots' => 'index,follow',
            'og_title' => '',
            'og_description' => '',
            'og_image' => '',
        ];
    }

    /**
     * Cache key oluştur
     */
    private function getCacheKey(string $key): string
    {
        $tenantId = tenant() ? tenant()->id : 'landlord';
        return "{$this->cachePrefix}.tenant.{$tenantId}.{$key}";
    }
}