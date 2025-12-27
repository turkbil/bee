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

    /**
     * 🚀 BULK CACHE: Request boyunca memory'de tut
     * Tüm settings tek seferde yüklenir, sonraki çağrılar array lookup
     */
    protected static $loadedSettings = [];
    protected static $settingsLoaded = [];

    /**
     * Tenant bazlı cache key oluştur
     */
    protected function getCacheKey(): string
    {
        $isTenant = function_exists('is_tenant') ? is_tenant() : false;
        $tenantId = function_exists('tenant_id') ? tenant_id() : 'central';

        return $isTenant ? "tenant_{$tenantId}_all_settings" : "central_all_settings";
    }

    /**
     * Tenant için tüm settings'i yükle (BULK)
     * İlk çağrıda DB'den çeker, sonrakiler memory'den
     */
    protected function loadAllSettings(): array
    {
        $cacheKey = $this->getCacheKey();

        // Memory'de varsa direkt dön (aynı request içinde)
        if (isset(self::$settingsLoaded[$cacheKey]) && self::$settingsLoaded[$cacheKey]) {
            return self::$loadedSettings[$cacheKey] ?? [];
        }

        try {
            // Cache'den al veya DB'den yükle
            $settings = Cache::remember($cacheKey, $this->cacheTtl * 60, function() {
                return $this->fetchAllSettingsFromDB();
            });

            // Memory'ye kaydet (request boyunca)
            self::$loadedSettings[$cacheKey] = $settings;
            self::$settingsLoaded[$cacheKey] = true;

            return $settings;
        } catch (\Exception $e) {
            Log::error("Settings bulk load hatası: " . $e->getMessage());
            return [];
        }
    }

    /**
     * DB'den tüm settings + values'ı çek
     * 2 query ile TÜM veriler gelir
     */
    protected function fetchAllSettingsFromDB(): array
    {
        $result = [];

        // 1. Tüm aktif settings'i çek (central DB'den)
        $settings = Setting::where('is_active', true)->get();

        if ($settings->isEmpty()) {
            return $result;
        }

        // Settings'i key => [id, default_value] olarak hazırla
        $settingsMap = [];
        foreach ($settings as $setting) {
            $settingsMap[$setting->id] = [
                'key' => $setting->key,
                'default_value' => $setting->default_value,
            ];
            // Default değeri ata
            $result[$setting->key] = $setting->default_value;
        }

        // 2. settings_values tablosundan tenant değerlerini çek
        try {
            $tableExists = cache()->remember('settings_values_table_exists', 3600, function() {
                return Schema::hasTable('settings_values');
            });

            if ($tableExists) {
                $settingIds = array_keys($settingsMap);
                $customValues = DB::table('settings_values')
                    ->whereIn('setting_id', $settingIds)
                    ->get();

                // Custom değerleri override et
                foreach ($customValues as $custom) {
                    if ($custom->value !== null && $custom->value !== '') {
                        $settingKey = $settingsMap[$custom->setting_id]['key'] ?? null;
                        if ($settingKey) {
                            $result[$settingKey] = $custom->value;
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error("Settings values bulk okuma hatası: " . $e->getMessage());
        }

        return $result;
    }

    /**
     * Setting değeri al - BULK cache'den
     */
    public function get($key, $default = null)
    {
        try {
            $settings = $this->loadAllSettings();
            return $settings[$key] ?? $default;
        } catch (\Exception $e) {
            Log::error("Settings get hatası: " . $e->getMessage(), ['key' => $key]);
            return $default;
        }
    }

    /**
     * ID ile setting al
     */
    public function getById($id, $default = null)
    {
        $cacheKey = $this->getCacheKey() . "_id_map";

        try {
            // ID → Key map'i cache'le
            $idMap = Cache::remember($cacheKey, $this->cacheTtl * 60, function() {
                $map = [];
                $settings = Setting::where('is_active', true)->get(['id', 'key']);
                foreach ($settings as $setting) {
                    $map[$setting->id] = $setting->key;
                }
                return $map;
            });

            $key = $idMap[$id] ?? null;
            if (!$key) {
                return $default;
            }

            return $this->get($key, $default);
        } catch (\Exception $e) {
            Log::error("Settings getById hatası: " . $e->getMessage(), ['id' => $id]);
            return $default;
        }
    }

    /**
     * Tüm settings'i al (array olarak)
     */
    public function all(): array
    {
        return $this->loadAllSettings();
    }

    /**
     * Birden fazla key'i tek seferde al
     */
    public function getMany(array $keys, $default = null): array
    {
        $settings = $this->loadAllSettings();
        $result = [];

        foreach ($keys as $key) {
            $result[$key] = $settings[$key] ?? $default;
        }

        return $result;
    }

    /**
     * Cache temizle - tenant bazlı
     */
    public function clearCache($key = null)
    {
        $cacheKey = $this->getCacheKey();

        // Bulk cache'i temizle
        Cache::forget($cacheKey);
        Cache::forget($cacheKey . "_id_map");

        // Memory'yi de temizle
        unset(self::$loadedSettings[$cacheKey]);
        unset(self::$settingsLoaded[$cacheKey]);

        Log::info("Settings cache temizlendi", ['cache_key' => $cacheKey]);
    }

    /**
     * Tüm tenant'ların cache'ini temizle (admin için)
     */
    public function clearAllTenantsCache()
    {
        // Pattern ile tüm tenant cache'lerini bul ve sil
        // Redis kullanılıyorsa pattern delete yapılabilir
        Cache::flush(); // Fallback: tüm cache temizle

        // Memory temizle
        self::$loadedSettings = [];
        self::$settingsLoaded = [];

        Log::info("Tüm settings cache temizlendi");
    }

    /**
     * Cache'i yenile (setting güncellendikten sonra çağır)
     */
    public function refreshCache()
    {
        $cacheKey = $this->getCacheKey();

        // Önce sil
        Cache::forget($cacheKey);
        Cache::forget($cacheKey . "_id_map");
        unset(self::$loadedSettings[$cacheKey]);
        unset(self::$settingsLoaded[$cacheKey]);

        // Yeniden yükle
        $this->loadAllSettings();

        Log::info("Settings cache yenilendi", ['cache_key' => $cacheKey]);
    }
}