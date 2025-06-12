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
        
        Log::info('ModuleSlugService: Getting slug', [
            'module' => $moduleName,
            'slug_key' => $slugKey,
            'domain' => $currentDomain,
            'database' => $currentDatabase
        ]);

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
            Log::info('ModuleSlugService: Using database slug', [
                'module' => $moduleName,
                'slug_key' => $slugKey,
                'slug' => $tenantSetting->settings['slugs'][$slugKey],
                'source' => 'database',
                'domain' => $currentDomain,
                'database' => $currentDatabase
            ]);
            return $tenantSetting->settings['slugs'][$slugKey];
        }

        // 3. Config'den varsayılanı al
        $configPath = "Modules/{$moduleName}/config/config.php";
        $fullPath = base_path($configPath);
        
        if (file_exists($fullPath)) {
            $config = include $fullPath;
            if (isset($config['slugs'][$slugKey])) {
                Log::info('ModuleSlugService: Using config slug', [
                    'module' => $moduleName,
                    'slug_key' => $slugKey,
                    'slug' => $config['slugs'][$slugKey],
                    'source' => 'config_file',
                    'domain' => $currentDomain,
                    'database' => $currentDatabase
                ]);
                return $config['slugs'][$slugKey];
            }
        }

        // 4. Son çare fallback
        Log::warning('ModuleSlugService: No slug found, using fallback', [
            'module' => $moduleName,
            'slug_key' => $slugKey,
            'fallback' => $slugKey,
            'source' => 'fallback',
            'domain' => $currentDomain,
            'database' => $currentDatabase
        ]);

        return $slugKey;
    }
}