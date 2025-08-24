<?php

namespace App\Repositories;

use App\Contracts\GlobalSeoRepositoryInterface;
use App\Services\GlobalSeoService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class GlobalSeoRepository implements GlobalSeoRepositoryInterface
{
    protected string $cachePrefix = 'global_seo';
    protected int $cacheTtl = 1800; // 30 dakika

    /**
     * SEO verilerini getir
     */
    public function getSeoData(Model $model, string $language): array
    {
        // Admin panelinde cache kullanma - Fresh data
        if (request()->is('admin*')) {
            return $this->getFreshSeoData($model, $language);
        }
        
        // Public sayfalarda cache kullan
        $cacheKey = $this->getCacheKey($model, "seo_data.{$language}");
        
        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($model, $language) {
            return $this->getFreshSeoData($model, $language);
        });
    }
    
    /**
     * Fresh SEO data çek (cache'siz)
     */
    protected function getFreshSeoData(Model $model, string $language): array
    {
        // SEO relationship'i kontrol et
        if (!$model->relationLoaded('seoSetting')) {
            $model->load('seoSetting');
        }
        
        $seoSettings = $model->seoSetting;
        
        if (!$seoSettings) {
            return $this->getEmptySeoData();
        }

        return [
            'seo_title' => $seoSettings->getTitle($language) ?? '',
            'seo_description' => $seoSettings->getDescription($language) ?? '',
            'seo_keywords' => '', // Keywords kaldırıldı - AI tarafından doldurulacak
            'canonical_url' => $seoSettings->canonical_url ?? '',
            'robots' => $seoSettings->getRobotsMetaString() ?? 'index, follow',
            'og_titles' => $seoSettings->getOgTitle($language) ?? '',
            'og_descriptions' => $seoSettings->getOgDescription($language) ?? '',
            'og_image' => $seoSettings->og_image ?? '',
        ];
    }

    /**
     * SEO verilerini kaydet
     */
    public function saveSeoData(Model $model, string $language, array $seoData): bool
    {
        try {
            $seoSettings = $model->seoSetting;
            
            if (!$seoSettings) {
                $seoSettings = $model->seoSetting()->create([
                    'model_type' => get_class($model),
                    'model_id' => $model->getKey(),
                ]);
            }

            // Her alanı ayrı ayrı kaydet
            foreach ($seoData as $field => $value) {
                $this->updateSeoField($model, $language, $field, $value);
            }

            $this->clearSeoCache($model);
            return true;
            
        } catch (\Exception $e) {
            \Log::error('SEO data save failed', [
                'model_type' => get_class($model),
                'model_id' => $model->getKey(),
                'language' => $language,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Belirli bir SEO alanını güncelle
     */
    public function updateSeoField(Model $model, string $language, string $field, mixed $value): bool
    {
        try {
            $seoSettings = $model->seoSetting;
            
            if (!$seoSettings) {
                return false;
            }

            $methodMap = [
                'seo_title' => 'updateLanguageData',
                'seo_description' => 'updateLanguageData', 
                'canonical_url' => 'updateLanguageData',
                'og_titles' => 'updateLanguageData',
                'og_descriptions' => 'updateLanguageData',
                'og_image' => 'updateLanguageData'
                // Keywords ve robots artık kaldırıldı - AI ve model içinde yönetiliyor
            ];

            if (isset($methodMap[$field])) {
                // updateLanguageData metodunu kullan
                $fieldMap = [
                    'seo_title' => 'title',
                    'seo_description' => 'description',
                    'og_titles' => 'og_title',
                    'og_descriptions' => 'og_description'
                ];
                
                if (isset($fieldMap[$field])) {
                    $seoSettings->updateLanguageData($language, [$fieldMap[$field] => $value]);
                } else {
                    // Direct field update (canonical_url, og_image)
                    $seoSettings->updateLanguageData($language, [$field => $value]);
                }
                
                $this->clearSeoCache($model);
                return true;
            }

            return false;
            
        } catch (\Exception $e) {
            \Log::error('SEO field update failed', [
                'model_type' => get_class($model),
                'model_id' => $model->getKey(),
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
    public function calculateSeoScore(array $seoData, string $module = 'default'): array
    {
        return GlobalSeoService::calculateSeoScore($seoData, $module);
    }

    /**
     * SEO validation kurallarını getir
     */
    public function getValidationRules(string $module = 'default'): array
    {
        return GlobalSeoService::getSeoValidationRules($module);
    }

    /**
     * SEO alanlarının limitlerini getir
     */
    public function getFieldLimits(string $module = 'default'): array
    {
        return GlobalSeoService::getSeoLimits($module);
    }

    /**
     * Keyword'leri parse et
     */
    public function parseKeywords(?string $keywords): array
    {
        return GlobalSeoService::parseKeywords($keywords);
    }

    /**
     * SEO verilerini temizle/cache'den sil
     */
    public function clearSeoCache(Model $model): void
    {
        $tenantId = tenant() ? tenant()->id : 'landlord';
        $modelKey = $model->getKey();
        $modelType = class_basename($model);
        
        // Aktif dilleri al
        $languages = \Modules\LanguageManagement\app\Models\TenantLanguage::where('is_active', true)
            ->pluck('code')
            ->toArray();
        
        foreach ($languages as $lang) {
            $cacheKey = $this->getCacheKey($model, "seo_data.{$lang}");
            Cache::forget($cacheKey);
        }
    }

    /**
     * Model için SEO ayarlarını al
     */
    public function getSeoSetting(Model $model): mixed
    {
        return $model->seoSetting;
    }

    /**
     * Model için SEO ayarlarını oluştur/güncelle
     */
    public function createOrUpdateSeoSetting(Model $model, array $data): mixed
    {
        $seoSettings = $model->seoSetting;
        
        if (!$seoSettings) {
            return $model->seoSetting()->create(array_merge($data, [
                'model_type' => get_class($model),
                'model_id' => $model->getKey(),
            ]));
        }
        
        $seoSettings->update($data);
        return $seoSettings;
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
            'og_titles' => '',
            'og_descriptions' => '',
            'og_image' => '',
        ];
    }

    /**
     * Modül-based SEO ayarlarını getir (Page/Module index SEO'su için)
     */
    public function getSeoSettings(string $moduleName, string $pageType = 'index'): array
    {
        try {
            // SEO settings tablosundan modül bazlı veri al (seoable_type ve seoable_id kullan)
            $seoSetting = \Modules\SeoManagement\app\Models\SeoSetting::where('seoable_type', $moduleName)
                ->where('seoable_id', 0) // Module index için 0 kullanıyoruz
                ->first();
            
            if (!$seoSetting) {
                // Boş template döndür
                return [
                    'titles' => [],
                    'descriptions' => [],
                    'keywords' => [],
                    'canonical_url' => ''
                ];
            }
            
            return [
                'titles' => $seoSetting->titles ?? [],
                'descriptions' => $seoSetting->descriptions ?? [],
                'keywords' => $seoSetting->keywords ?? [],
                'canonical_url' => $seoSetting->canonical_url ?? ''
            ];
            
        } catch (\Exception $e) {
            \Log::error('Module SEO settings retrieval failed', [
                'module' => $moduleName,
                'page_type' => $pageType,
                'error' => $e->getMessage()
            ]);
            
            return [
                'titles' => [],
                'descriptions' => [],
                'keywords' => [],
                'canonical_url' => ''
            ];
        }
    }
    
    /**
     * Modül-based SEO ayarlarını kaydet (Page/Module index SEO'su için)
     */
    public function saveSeoSettings(string $moduleName, string $pageType, array $seoData): bool
    {
        try {
            // SEO settings tablosuna modül bazlı veri kaydet
            $seoSetting = \Modules\SeoManagement\app\Models\SeoSetting::updateOrCreate(
                [
                    'seoable_type' => $moduleName,
                    'seoable_id' => 0  // Module index için 0 kullanıyoruz
                ],
                [
                    'titles' => $seoData['titles'] ?? [],
                    'descriptions' => $seoData['descriptions'] ?? [],
                    'keywords' => $seoData['keywords'] ?? [],
                    'canonical_url' => $seoData['canonical_url'] ?? ''
                ]
            );
            
            // Cache temizle
            $this->clearModuleSeoCache($moduleName, $pageType);
            
            return true;
            
        } catch (\Exception $e) {
            \Log::error('Module SEO settings save failed', [
                'module' => $moduleName,
                'page_type' => $pageType,
                'data' => $seoData,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }
    
    /**
     * Modül SEO cache'ini temizle
     */
    private function clearModuleSeoCache(string $moduleName, string $pageType): void
    {
        try {
            $tenantId = tenant() ? tenant()->id : 'landlord'; 
            $cacheKey = "{$this->cachePrefix}.module.{$tenantId}.{$moduleName}.{$pageType}";
            Cache::forget($cacheKey);
        } catch (\Exception $e) {
            \Log::warning('Module SEO cache clear failed', [
                'module' => $moduleName,
                'page_type' => $pageType,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Cache key oluştur
     */
    private function getCacheKey(Model $model, string $key): string
    {
        $tenantId = tenant() ? tenant()->id : 'landlord';
        $modelType = class_basename($model);
        $modelId = $model->getKey();
        
        return "{$this->cachePrefix}.tenant.{$tenantId}.{$modelType}.{$modelId}.{$key}";
    }
}