<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Services\ModuleSlugService;
use App\Models\ModuleTenantSetting;
use Illuminate\Support\Facades\Schema;

class DebugController extends Controller
{
    public function portfolioDebug()
    {
        $debugData = [];
        
        // 1. Tenant Bilgileri
        $debugData['tenant_info'] = [
            'is_tenant' => app()->bound('tenant'),
            'tenant_id' => app()->bound('tenant') ? tenant('id') : null,
            'tenant_domain' => app()->bound('tenant') ? tenant('domains')->first()?->domain : null,
            'current_domain' => request()->getHost(),
            'database_name' => DB::connection()->getDatabaseName(),
            'connection_type' => app()->bound('tenant') ? 'tenant' : 'central',
            'tenant_context' => tenancy()->initialized ? 'INITIALIZED' : 'NOT_INITIALIZED',
            'actual_connection' => DB::getDefaultConnection(),
            'tenant_aware' => config('tenancy.tenant_aware_kernel', false)
        ];
        
        // 2. Veritabanı Durumu
        try {
            $debugData['database_status'] = [
                'connection' => 'OK',
                'current_db' => DB::connection()->getDatabaseName(),
                'tables_exist' => Schema::hasTable('module_tenant_settings'),
                'total_settings' => ModuleTenantSetting::count()
            ];
        } catch (\Exception $e) {
            $debugData['database_status'] = [
                'connection' => 'FAILED',
                'error' => $e->getMessage()
            ];
        }
        
        // 3. Portfolio Config Dosyası
        $configPath = base_path('Modules/Portfolio/config/config.php');
        if (file_exists($configPath)) {
            $config = include $configPath;
            $debugData['config_file'] = [
                'exists' => true,
                'path' => $configPath,
                'slugs' => $config['slugs'] ?? 'YOK'
            ];
        } else {
            $debugData['config_file'] = [
                'exists' => false,
                'path' => $configPath
            ];
        }
        
        // 4. Veritabanından Portfolio Ayarları
        try {
            $portfolioSetting = ModuleTenantSetting::where('module_name', 'Portfolio')->first();
            if ($portfolioSetting) {
                $debugData['database_settings'] = [
                    'exists' => true,
                    'id' => $portfolioSetting->id,
                    'module_name' => $portfolioSetting->module_name,
                    'settings' => $portfolioSetting->settings,
                    'slugs' => $portfolioSetting->settings['slugs'] ?? 'YOK',
                    'created_at' => $portfolioSetting->created_at,
                    'updated_at' => $portfolioSetting->updated_at
                ];
            } else {
                $debugData['database_settings'] = [
                    'exists' => false,
                    'message' => 'Portfolio modülü için ayar bulunamadı'
                ];
            }
        } catch (\Exception $e) {
            $debugData['database_settings'] = [
                'exists' => false,
                'error' => $e->getMessage()
            ];
        }
        
        // 5. ModuleSlugService Test
        try {
            $debugData['slug_service'] = [
                'index_slug' => ModuleSlugService::getSlug('Portfolio', 'index'),
                'show_slug' => ModuleSlugService::getSlug('Portfolio', 'show'),
                'category_slug' => ModuleSlugService::getSlug('Portfolio', 'category'),
            ];
        } catch (\Exception $e) {
            $debugData['slug_service'] = [
                'error' => $e->getMessage()
            ];
        }
        
        // 6. Cache Bilgileri
        $debugData['cache_info'] = [
            'global_cache_key' => 'module_slug_service_global_settings',
            'global_cache_exists' => Cache::has('module_slug_service_global_settings'),
            'table_empty_cache_key' => 'module_tenant_settings_table_empty',
            'table_empty_cache_exists' => Cache::has('module_tenant_settings_table_empty'),
            'config_cache_key' => 'module_config_Portfolio',
            'config_cache_exists' => Cache::has('module_config_Portfolio')
        ];
        
        // 7. Tüm Module Settings (Debug için)
        try {
            $allSettings = ModuleTenantSetting::all();
            $debugData['all_module_settings'] = $allSettings->map(function($setting) {
                return [
                    'id' => $setting->id,
                    'module_name' => $setting->module_name,
                    'settings' => $setting->settings,
                    'updated_at' => $setting->updated_at
                ];
            });
        } catch (\Exception $e) {
            $debugData['all_module_settings'] = ['error' => $e->getMessage()];
        }
        
        // 8. Route Bilgileri
        $debugData['route_info'] = [
            'current_url' => request()->url(),
            'current_path' => request()->path(),
            'method' => request()->method(),
            'domain' => request()->getHost()
        ];
        
        return view('debug.portfolio', compact('debugData'));
    }
}