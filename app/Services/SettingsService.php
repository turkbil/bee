<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\SettingManagement\App\Models\Setting;

class SettingsService
{
    protected $cacheTtl = 1440; // 24 saat cache süresini uzattık

    public function get($key, $default = null)
    {
        $isTenant = function_exists('is_tenant') ? is_tenant() : false;
        $tenantId = function_exists('tenant_id') ? tenant_id() : null;
        
        // Cache anahtarını oluştur
        $cacheKey = $isTenant ? "tenant_{$tenantId}_settings_{$key}" : "settings_{$key}";
        
        // Önbelleği kapat - doğrudan veritabanından oku
        // Hata çözümü için önbelleği devre dışı bıraktık
        try {
            // Central veritabanından settings kaydını bul
            $setting = Setting::where('key', $key)
                ->where('is_active', true)
                ->first();
            
            if (!$setting) {
                return $default;
            }
            
            // Eğer tenant ise, doğrudan tenant veritabanına bak
            if ($isTenant && $tenantId) {
                try {
                    // SQL sorgusu ile tenant_values tablosunu kontrol et
                    $tenantValue = DB::select("SELECT value FROM settings_values WHERE setting_id = ? LIMIT 1", [$setting->id]);
                    
                    if (!empty($tenantValue) && isset($tenantValue[0]->value) && $tenantValue[0]->value !== null && $tenantValue[0]->value !== '') {
                        return $tenantValue[0]->value;
                    }
                } catch (\Exception $e) {
                    Log::error("Tenant değeri okuma hatası: " . $e->getMessage(), ['key' => $key]);
                }
            }
            
            // Tenant değeri yoksa varsayılan değeri döndür
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
            
            // Eğer tenant ise, tenant veritabanına bak
            if ($isTenant && $tenantId) {
                try {
                    // Direkt SQL sorgusu çalıştır
                    $tenantValue = DB::select("SELECT value FROM settings_values WHERE setting_id = ? LIMIT 1", [$id]);
                    
                    if (!empty($tenantValue) && isset($tenantValue[0]->value) && $tenantValue[0]->value !== null && $tenantValue[0]->value !== '') {
                        return $tenantValue[0]->value;
                    }
                } catch (\Exception $e) {
                    Log::error("Tenant değeri okuma hatası: " . $e->getMessage(), ['id' => $id]);
                }
            }
            
            // Tenant değeri yoksa varsayılan değeri döndür
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
        
        // Tüm önbelleği temizle
        Cache::flush();
    }
}