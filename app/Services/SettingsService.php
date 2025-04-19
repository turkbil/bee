<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Modules\SettingManagement\App\Models\Setting;
use Modules\SettingManagement\App\Models\SettingValue;

class SettingsService
{
    protected $cacheTtl = 60;

    public function get($key, $default = null)
    {
        $isTenant = function_exists('is_tenant') ? is_tenant() : false;
        $tenantId = function_exists('tenant_id') ? tenant_id() : null;
        
        $cacheKey = $isTenant ? "tenant_{$tenantId}_settings_{$key}" : "settings_{$key}";
        
        return Cache::remember($cacheKey, $this->cacheTtl * 60, function () use ($key, $default, $isTenant, $tenantId) {
            // Önce settings tablosundan ilgili kaydı bul
            $setting = Setting::where('key', $key)->first();
            
            if (!$setting) {
                return $default;
            }
            
            // Tenant'da çalışıyorsak, önce tenant_values tablosuna bak
            if ($isTenant && $tenantId) {
                $tenantValue = SettingValue::where('setting_id', $setting->id)->first();
                
                if ($tenantValue && !empty($tenantValue->value)) {
                    return $tenantValue->value;
                }
            }
            
            // Tenant değeri yoksa veya Central'daysa varsayılan değeri döndür
            return $setting->default_value ?? $default;
        });
    }

    public function getById($id, $default = null)
    {
        $isTenant = function_exists('is_tenant') ? is_tenant() : false;
        $tenantId = function_exists('tenant_id') ? tenant_id() : null;
        
        $cacheKey = $isTenant ? "tenant_{$tenantId}_settings_id_{$id}" : "settings_id_{$id}";
        
        return Cache::remember($cacheKey, $this->cacheTtl * 60, function () use ($id, $default, $isTenant, $tenantId) {
            // Önce settings tablosundan ilgili kaydı bul
            $setting = Setting::find($id);
            
            if (!$setting) {
                return $default;
            }
            
            // Tenant'da çalışıyorsak, önce tenant_values tablosuna bak
            if ($isTenant && $tenantId) {
                $tenantValue = SettingValue::where('setting_id', $setting->id)->first();
                
                if ($tenantValue && !empty($tenantValue->value)) {
                    return $tenantValue->value;
                }
            }
            
            // Tenant değeri yoksa veya Central'daysa varsayılan değeri döndür
            return $setting->default_value ?? $default;
        });
    }

    public function clearCache($key = null)
    {
        $isTenant = function_exists('is_tenant') ? is_tenant() : false;
        $tenantId = function_exists('tenant_id') ? tenant_id() : null;
        
        if ($key) {
            $cacheKey = $isTenant ? "tenant_{$tenantId}_settings_{$key}" : "settings_{$key}";
            Cache::forget($cacheKey);
        } else {
            if ($isTenant && $tenantId) {
                Cache::flush("tenant_{$tenantId}_settings_*");
            }
            Cache::flush("settings_*");
        }
    }
}