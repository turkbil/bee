<?php

use Modules\SettingManagement\App\Models\Setting;
use Modules\SettingManagement\App\Models\SettingValue;
use Illuminate\Support\Facades\Cache;

if (!function_exists('setting')) {
    /**
     * Get setting value by ID or KEY
     * 
     * Mantık:
     * 1. Tenant'ta settings_values tablosunda değer varsa onu kullan
     * 2. Yoksa central'daki settings tablosundan default_value kullan
     * 3. O da yoksa fonksiyona verilen $default parametresini kullan
     * 
     * Usage:
     * setting(1) - ID ile değer al
     * setting('site_name') - KEY ile değer al
     * setting('site_name', 'Default Site') - Fallback default değerli
     * 
     * @param int|string $identifier Setting ID veya KEY
     * @param mixed $default Fallback default değer (opsiyonel)
     * @return mixed
     */
    function setting($identifier, $default = null)
    {
        try {
            // Cache key oluştur
            $cacheKey = 'setting_' . (is_numeric($identifier) ? 'id_' : 'key_') . $identifier;

            // Tenant ID'yi şimdi capture et (closure içinde kullanmak için)
            $tenantId = tenant() ? tenant()->id : null;

            // Tenant varsa tenant prefix ekle
            if ($tenantId) {
                $cacheKey = 'tenant_' . $tenantId . '_' . $cacheKey;
            }

            // Cache'den kontrol et
            return Cache::remember($cacheKey, 3600, function () use ($identifier, $default, $tenantId) {
                // ✅ FIX: Closure içinde tenant context'i yeniden başlat
                // Redis cache callback'i sırasında tenant context kayboluyor
                if ($tenantId && (!tenant() || tenant()->id !== $tenantId)) {
                    $tenant = \App\Models\Tenant::find($tenantId);
                    if ($tenant) {
                        tenancy()->initialize($tenant);
                    }
                }

                // ID veya KEY ile setting'i central'dan bul
                if (is_numeric($identifier)) {
                    $setting = Setting::find($identifier);
                } else {
                    $setting = Setting::where('key', $identifier)->first();
                }

                // Setting yoksa fallback default döndür
                if (!$setting) {
                    return $default;
                }

                // Model'in getValue() metodunu kullan
                // Bu metod otomatik olarak:
                // 1. Tenant'ta değer varsa onu döndürür
                // 2. Yoksa settings tablosundaki default_value döndürür
                $value = $setting->getValue();

                // Eğer default_value da null ise, fallback default kullan
                return $value ?? $default;
            });
        } catch (\Exception $e) {
            // Hata durumunda fallback default döndür
            return $default;
        }
    }
}

if (!function_exists('settings')) {
    /**
     * Get multiple settings at once
     * 
     * Usage:
     * settings(['site_title', 'site_email'])
     * settings(['site_title', 'site_email'], 'Default Value')
     * 
     * @param array $identifiers Setting KEY'leri
     * @param mixed $default Fallback default değer (opsiyonel)
     * @return array
     */
    function settings(array $identifiers, $default = null)
    {
        $result = [];
        
        foreach ($identifiers as $identifier) {
            $result[$identifier] = setting($identifier, $default);
        }
        
        return $result;
    }
}

if (!function_exists('setting_update')) {
    /**
     * Update setting value (only for tenant)
     * 
     * Usage:
     * setting_update(1, 'New Value')
     * setting_update('site_name', 'My Website')
     * 
     * @param int|string $identifier Setting ID veya KEY
     * @param mixed $value Yeni değer
     * @return bool
     */
    function setting_update($identifier, $value)
    {
        try {
            // Sadece tenant içinde çalışsın
            if (!tenant()) {
                return false;
            }
            
            // Setting'i bul
            if (is_numeric($identifier)) {
                $setting = Setting::find($identifier);
            } else {
                $setting = Setting::where('key', $identifier)->first();
            }
            
            if (!$setting) {
                return false;
            }
            
            // Değeri güncelle veya oluştur
            SettingValue::updateOrCreate(
                ['setting_id' => $setting->id],
                ['value' => $value]
            );
            
            // Cache temizleme artık SettingValue model'inde otomatik yapılıyor
            // Ek manuel temizleme gerekmez
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}

if (!function_exists('setting_clear_cache')) {
    /**
     * Clear all settings cache
     * 
     * Usage:
     * setting_clear_cache()
     * 
     * @return void
     */
    function setting_clear_cache()
    {
        // Tüm setting cache'lerini temizle
        Cache::flush();
    }
}