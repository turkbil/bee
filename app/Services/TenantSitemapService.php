<?php

namespace App\Services;

use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use Illuminate\Support\Facades\DB;
use App\Helpers\TenantHelpers;

class TenantSitemapService
{
    /**
     * Tenant için çok dilli dinamik sitemap oluştur
     */
    public static function generate(): Sitemap
    {
        $sitemap = Sitemap::create();
        
        // Aktif dilleri al
        $languages = self::getActiveLanguages();
        $defaultLanguage = get_tenant_default_locale();

        // Ana sayfa - tüm diller için
        foreach ($languages as $language) {
            // Object veya array olabilir, type check yap
            $languageCode = is_object($language) ? $language->code : $language['code'];
            $url = $languageCode === $defaultLanguage ? '/' : '/' . $languageCode;
            $sitemap->add(
                Url::create($url)
                    ->setLastModificationDate(now())
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                    ->setPriority(1.0)
            );
        }

        // Dinamik modül içerikleri - tenant'a atanmış content modüllerini işle
        self::addDynamicModuleContent($sitemap, $languages, $defaultLanguage);

        return $sitemap;
    }
    
    /**
     * Aktif dilleri al
     */
    private static function getActiveLanguages(): array
    {
        try {
            return available_tenant_languages();
        } catch (\Exception $e) {
            return [
                ['code' => 'tr', 'name' => 'Türkçe'],
                ['code' => 'en', 'name' => 'English']
            ];
        }
    }
    
    /**
     * Tenant'a atanmış aktif content modüllerini al
     */
    private static function getAssignedContentModules(): array
    {
        try {
            $tenantId = tenant()?->id ?? 1;
            
            return TenantHelpers::central(function () use ($tenantId) {
                return DB::table('modules')
                    ->join('module_tenants', 'modules.module_id', '=', 'module_tenants.module_id')
                    ->where('modules.is_active', true)
                    ->where('modules.type', 'content')
                    ->where('module_tenants.tenant_id', $tenantId)
                    ->where('module_tenants.is_active', true)
                    ->select('modules.name', 'modules.display_name')
                    ->get()
                    ->toArray();
            });
        } catch (\Exception $e) {
            return [];
        }
    }
    
    /**
     * Dinamik modül içeriklerini ekle
     */
    private static function addDynamicModuleContent(Sitemap $sitemap, array $languages, string $defaultLanguage): void
    {
        $modules = self::getAssignedContentModules();
        
        foreach ($modules as $module) {
            $moduleName = $module->name;
            
            try {
                // Modül model sınıfını oluştur
                $modelClass = "Modules\\{$moduleName}\\App\\Models\\{$moduleName}";
                
                if (!class_exists($modelClass)) {
                    continue;
                }
                
                // Model instance'ı oluştur ve aktif kayıtları al
                $model = new $modelClass();
                $records = $model->where('is_active', true)->get();
                
                foreach ($records as $record) {
                    // HasTranslations trait'i var mı kontrol et
                    $hasTranslations = method_exists($record, 'getTranslated');
                    
                    foreach ($languages as $language) {
                        $languageCode = is_object($language) ? $language->code : $language['code'];
                        
                        if ($hasTranslations) {
                            // Çok dilli modül
                            $slug = $record->getTranslated('slug', $languageCode);
                            if (empty($slug)) {
                                continue; // Bu dilde içerik yoksa atla
                            }
                        } else {
                            // Tek dilli modül
                            $slug = $record->slug ?? null;
                            if (empty($slug)) {
                                continue;
                            }
                        }
                        
                        // URL pattern'ini belirle (modül yapısına göre)
                        $url = self::buildModuleUrl($moduleName, $slug, $languageCode, $defaultLanguage, $record);
                        
                        if ($url) {
                            $sitemap->add(
                                Url::create($url)
                                    ->setLastModificationDate($record->updated_at ?? now())
                                    ->setChangeFrequency(self::getModuleChangeFrequency($moduleName))
                                    ->setPriority(self::getModulePriority($moduleName))
                            );
                        }
                    }
                }
            } catch (\Exception $e) {
                // Modül yüklenemiyorsa skip
                continue;
            }
        }
    }
    
    /**
     * Modül URL'ini oluştur
     */
    private static function buildModuleUrl(string $moduleName, string $slug, string $languageCode, string $defaultLanguage, $record): ?string
    {
        $baseUrl = '';
        
        // Dil prefix'i ekle (gerekirse)
        if ($languageCode !== $defaultLanguage) {
            $baseUrl = '/' . $languageCode;
        }
        
        // Modül tipine göre URL pattern'i
        switch (strtolower($moduleName)) {
            case 'page':
                return $baseUrl . '/' . $slug;
                
            case 'portfolio':
                // Portfolio kategori ile birlikte
                if (isset($record->category) && $record->category) {
                    $categorySlug = method_exists($record->category, 'getTranslated') 
                        ? $record->category->getTranslated('slug', $languageCode)
                        : $record->category->slug;
                    return $baseUrl . '/portfolio/' . $categorySlug . '/' . $slug;
                }
                return $baseUrl . '/portfolio/' . $slug;
                
            case 'announcement':
                return $baseUrl . '/announcements/' . $slug;
                
            default:
                // Varsayılan pattern: /module-name/slug
                $moduleSlug = strtolower($moduleName);
                return $baseUrl . '/' . $moduleSlug . '/' . $slug;
        }
    }
    
    /**
     * Modül için değişiklik sıklığını belirle
     */
    private static function getModuleChangeFrequency(string $moduleName): string
    {
        switch (strtolower($moduleName)) {
            case 'page':
                return Url::CHANGE_FREQUENCY_WEEKLY;
            case 'announcement':
                return Url::CHANGE_FREQUENCY_MONTHLY;
            case 'portfolio':
                return Url::CHANGE_FREQUENCY_MONTHLY;
            default:
                return Url::CHANGE_FREQUENCY_MONTHLY;
        }
    }
    
    /**
     * Modül için öncelik belirle
     */
    private static function getModulePriority(string $moduleName): float
    {
        switch (strtolower($moduleName)) {
            case 'page':
                return 0.8;
            case 'portfolio':
                return 0.6;
            case 'announcement':
                return 0.5;
            default:
                return 0.5;
        }
    }


    /**
     * Sitemap'i dosyaya kaydet
     */
    public static function generateAndSave(): string
    {
        $sitemap = self::generate();
        $filename = 'sitemap.xml';
        $path = public_path($filename);
        
        $sitemap->writeToFile($path);
        
        return $filename;
    }
}