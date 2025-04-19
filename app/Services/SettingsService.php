<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Modules\SettingManagement\App\Models\Setting;

class SettingsService
{
    protected $cacheTtl = 1440; // 24 saat cache süresi

    public function get($key, $default = null)
    {
        $isTenant = function_exists('is_tenant') ? is_tenant() : false;
        $tenantId = function_exists('tenant_id') ? tenant_id() : null;
        
        // Cache anahtarını oluştur
        $cacheKey = $isTenant ? "tenant_{$tenantId}_settings_{$key}" : "settings_{$key}";
        
        try {
            // Önce setting ID'sini bul
            $setting = Setting::where('key', $key)
                ->where('is_active', true)
                ->first();
            
            if (!$setting) {
                return $default;
            }
            
            // Her durumda önce settings_values tablosuna bak
            try {
                if (Schema::hasTable('settings_values')) {
                    // Önce settings_values tablosunu sorgula
                    $customValue = DB::table('settings_values')
                        ->where('setting_id', $setting->id)
                        ->first();
                    
                    // Özel değer varsa döndür
                    if ($customValue && $customValue->value !== null && $customValue->value !== '') {
                        return $customValue->value;
                    }
                }
            } catch (\Exception $e) {
                Log::error("Settings values okuma hatası: " . $e->getMessage(), [
                    'key' => $key, 
                    'setting_id' => $setting->id,
                    'isTenant' => $isTenant,
                    'tenant_id' => $tenantId
                ]);
            }
            
            // Özel değer yoksa varsayılan değeri döndür
            return $setting->default_value ?? $default;
        } catch (\Exception $e) {
            Log::error("Settings hatası: " . $e->getMessage(), ['key' => $key]);
            return $default;
        }
    }

    public function getById($id, $default = null)
    {
        $isTenant = function_exists('is_tenant') ? is_tenant() : false;
        $tenantId = function_exists('tenant_id') ? tenant_id() : null;
        
        try {
            // Central veritabanından settings kaydını bul
            $setting = Setting::find($id);
            
            if (!$setting || !$setting->is_active) {
                return $default;
            }
            
            // Her durumda önce settings_values tablosuna bak
            try {
                if (Schema::hasTable('settings_values')) {
                    // Önce settings_values tablosunu sorgula
                    $customValue = DB::table('settings_values')
                        ->where('setting_id', $id)
                        ->first();
                    
                    // Özel değer varsa döndür
                    if ($customValue && $customValue->value !== null && $customValue->value !== '') {
                        return $customValue->value;
                    }
                }
            } catch (\Exception $e) {
                Log::error("Settings values ID ile okuma hatası: " . $e->getMessage(), [
                    'id' => $id,
                    'isTenant' => $isTenant,
                    'tenant_id' => $tenantId
                ]);
            }
            
            // Özel değer yoksa varsayılan değeri döndür
            return $setting->default_value ?? $default;
        } catch (\Exception $e) {
            Log::error("Settings ID hatası: " . $e->getMessage(), ['id' => $id]);
            return $default;
        }
    }

    public function clearCache($key = null)
    {
        $isTenant = function_exists('is_tenant') ? is_tenant() : false;
        $tenantId = function_exists('tenant_id') ? tenant_id() : null;
        
        if ($key) {
            // Belirli bir anahtarın önbelleğini temizle
            $cacheKey = $isTenant ? "tenant_{$tenantId}_settings_{$key}" : "settings_{$key}";
            Cache::forget($cacheKey);
        } else {
            // Tüm önbelleği temizle
            Cache::flush();
        }
    }
}