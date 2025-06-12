<?php

namespace App\Services;

use App\Models\ModuleTenantSetting;
use Illuminate\Support\Facades\Log;

class ModuleSlugService
{
    public static function getSlug(string $moduleName, string $slugKey): string
    {
        $currentDatabase = \DB::connection()->getDatabaseName();
        $currentDomain = request()->getHost();
        
        // Log kaldırıldı

        // 1. Mevcut veritabanından ayarları al (hangi veritabanında isek)
        try {
            $tenantSetting = ModuleTenantSetting::where('module_name', $moduleName)->first();
        } catch (\Exception $e) {
            Log::warning('ModuleSlugService: Database query failed, using config fallback', [
                'module' => $moduleName,
                'slug_key' => $slugKey,
                'domain' => $currentDomain,
                'database' => $currentDatabase,
                'error' => $e->getMessage()
            ]);
            $tenantSetting = null;
        }
        
        // 2. Database'de ayar varsa onu kullan
        if ($tenantSetting && isset($tenantSetting->settings['slugs'][$slugKey])) {
            return $tenantSetting->settings['slugs'][$slugKey];
        }

        // 3. Config'den varsayılanı al
        $configPath = "Modules/{$moduleName}/config/config.php";
        $fullPath = base_path($configPath);
        
        if (file_exists($fullPath)) {
            $config = include $fullPath;
            if (isset($config['slugs'][$slugKey])) {
                return $config['slugs'][$slugKey];
            }
        }

        // 4. Son çare fallback
        return $slugKey;
    }
}