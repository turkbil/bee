<?php
namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Stancl\Tenancy\Tenancy;
use Illuminate\Support\Facades\Cache;

class TenantHelpers
{
    /**
     * Central veritabanında işlem yap
     * 
     * @param callable $callback Çalıştırılacak fonksiyon
     * @return mixed
     */
    public static function central(callable $callback)
    {
        // Mevcut bağlantı ve tenant durumunu sakla
        $previousConnection = DB::getDefaultConnection();
        $tenancy = app(Tenancy::class);
        $wasTenantInitialized = $tenancy->initialized;
        $previousTenant = $wasTenantInitialized ? tenant() : null;
        
        try {
            // Eğer tenant başlatılmışsa, geçici olarak sonlandır
            if ($wasTenantInitialized) {
                $tenancy->end();
            }
            
            // Central veritabanına geçiş yap (ana veritabanı bağlantısı)
            $centralConnection = Config::get('database.default');
            DB::setDefaultConnection($centralConnection);
            
            // Bağlantıyı yenile
            DB::purge($centralConnection);
            
            // Callback'i çalıştır
            $result = $callback();
            
            return $result;
        } finally {
            // Önceki bağlantıya geri dön
            DB::setDefaultConnection($previousConnection);
            
            // Eğer daha önce tenant başlatılmışsa, tekrar başlat
            if ($wasTenantInitialized && $previousTenant) {
                $tenancy->initialize($previousTenant);
            }
        }
    }
    
    /**
     * Tenant veritabanında işlem yap
     * 
     * @param callable $callback Çalıştırılacak fonksiyon
     * @param int|string|null $tenantId Belirli bir tenant ID (opsiyonel)
     * @return mixed
     */
    public static function tenant(callable $callback, $tenantId = null)
    {
        $tenancy = app(Tenancy::class);
        $previousTenant = $tenancy->initialized ? tenant() : null;
        
        try {
            // Belirli bir tenant ID belirtilmişse, o tenant'a geçiş yap
            if ($tenantId !== null && (!$previousTenant || $previousTenant->id != $tenantId)) {
                // Tenant bilgisini önbellekten kontrol et
                $tenantCacheKey = 'tenant_' . $tenantId;
                $tenant = Cache::remember($tenantCacheKey, now()->addDays(7), function () use ($tenantId) {
                    return \App\Models\Tenant::find($tenantId);
                });
                
                if (!$tenant) {
                    throw new \Exception("Tenant bulunamadı: {$tenantId}");
                }
                
                // Eğer şu anda başka bir tenant'daysa, önce sonlandır
                if ($tenancy->initialized) {
                    $tenancy->end();
                }
                
                // Belirtilen tenant'ı başlat
                $tenancy->initialize($tenant);
            }
            
            // Eğer tenant başlatılmamışsa hata ver
            if (!$tenancy->initialized) {
                throw new \Exception("Tenant başlatılmadan tenant veritabanına erişilemez");
            }

            // Tenant için bağlantı ayarını değiştir
            $currentConnection = DB::getDefaultConnection();
            Config::set('database.connections.' . $currentConnection . '.driver', 'mysql');
            DB::purge($currentConnection);
            
            // Callback'i çalıştır
            $result = $callback();
            
            return $result;
        } finally {
            // Eğer tenant değiştirilmişse, önceki tenant'a dön
            if ($tenantId !== null && $previousTenant && $previousTenant->id != $tenantId) {
                $tenancy->end();
                $tenancy->initialize($previousTenant);
            }
        }
    }
    
    /**
     * Aktif tenant'ın ID'sini döndürür, tenant yoksa null döner
     * 
     * @return int|null
     */
    public static function getCurrentTenantId()
    {
        $tenancy = app(Tenancy::class);
        
        if ($tenancy->initialized) {
            return tenant()->id;
        }
        
        return null;
    }
    
    /**
     * İşlemin central veritabanında olup olmadığını kontrol eder
     * 
     * @return bool
     */
    public static function isCentral()
    {
        return !app(Tenancy::class)->initialized;
    }
    
    /**
     * İşlemin tenant veritabanında olup olmadığını kontrol eder
     * 
     * @return bool
     */
    public static function isTenant()
    {
        return app(Tenancy::class)->initialized;
    }
    
    /**
     * Tenant ID'ye göre dosya depolama diskin ayarlar
     *
     * ⚠️ CRITICAL FIX: Cache kaldırıldı çünkü storage_path() tenant context'ine bağlı
     *
     * @param int|null $tenantId
     * @return string
     */
    public static function getTenantDiskConfig($tenantId = null)
    {
        if ($tenantId === null) {
            $tenantId = self::getCurrentTenantId();
        }

        // Eğer tenant ID yoksa veya 1 ise (central) normal public disk kullan
        if (!$tenantId || $tenantId == 1) {
            return 'public';
        }

        // Özel tenant disk yapılandırması
        $tenantDisk = 'tenant';

        // ⚠️ CRITICAL FIX: Tenant context var mı kontrol et
        // Eğer tenant() helper çalışıyorsa, storage_path() otomatik prefix ekler
        // Manuel eklememeliyiz!
        $isInitialized = function_exists('tenant') && tenant();
        $root = $isInitialized ? storage_path("app/public") : base_path("storage/tenant{$tenantId}/app/public");

        // Disk yapılandırmasını ayarla
        Config::set('filesystems.disks.tenant', [
            'driver' => 'local',
            'root' => $root,
            'url' => env('APP_URL').'/storage/tenant'.$tenantId,
            'visibility' => 'public',
            'throw' => false,
        ]);

        return $tenantDisk;
    }

    /**
     * Hızlı tenant ID alma (admin paneli desteği ile)
     * Öncelik sırası: tenant() -> admin_selected_tenant -> auth()->user()->tenant_id -> default tenant 1
     * 
     * @param bool $fallbackToLatest En son tenant'a fallback yap (default: false)
     * @return int|null
     */
    public static function resolveCurrentTenantId(bool $fallbackToLatest = false): ?int
    {
        // Önce mevcut tenant context'ini kontrol et
        $tenantId = tenant('id');
        if ($tenantId) {
            return $tenantId;
        }

        // Admin panelinde session'da seçilen tenant'ı kontrol et (EN ÖNCELİKLİ)
        $adminSelectedTenant = session('admin_selected_tenant_id');
        if ($adminSelectedTenant) {
            return (int) $adminSelectedTenant;
        }

        // Admin panelinde logged-in user'ın tenant'ını kontrol et (eğer varsa)
        $user = auth()->user();
        if ($user && isset($user->tenant_id)) {
            return $user->tenant_id;
        }

        // Admin panelinde veya default durumda tenant 1 kullan (Nurullah'ın tenant'ı)
        if (request()->is('admin/*') || request()->is('admin')) {
            return 1;
        }

        // Manuel default: Nurullah tenant 1 kullanıyor
        if (auth()->check()) {
            return 1; // Nurullah'ın tenant'ı
        }

        // Default tenant 1 (artık latest tenant'a fallback yapmıyoruz)
        return 1;
    }

    /**
     * Tenant bilgilerini hızlı al (cache'li)
     * 
     * @param int|null $tenantId
     * @return \App\Models\Tenant|null
     */
    public static function getTenantById(?int $tenantId = null): ?\App\Models\Tenant
    {
        if (!$tenantId) {
            $tenantId = self::resolveCurrentTenantId();
        }

        if (!$tenantId) {
            return null;
        }

        return Cache::remember("tenant_info_{$tenantId}", now()->addHour(), function () use ($tenantId) {
            return \App\Models\Tenant::find($tenantId);
        });
    }

    /**
     * Mevcut tenant'ın domain'ini al
     * 
     * @param int|null $tenantId
     * @return string|null
     */
    public static function getTenantDomain(?int $tenantId = null): ?string
    {
        $tenant = self::getTenantById($tenantId);
        return $tenant?->domains()->first()?->domain;
    }

    /**
     * Tenant'ın central database'de mi, tenant database'de mi olduğunu kontrol et
     * 
     * @param int|null $tenantId
     * @return string 'central'|'tenant'
     */
    public static function getTenantDatabaseType(?int $tenantId = null): string
    {
        if (!$tenantId) {
            $tenantId = self::resolveCurrentTenantId();
        }

        // Tenant ID 1 veya yoksa central
        return (!$tenantId || $tenantId == 1) ? 'central' : 'tenant';
    }

    /**
     * Mevcut tenant ID'yi al
     * claude_ai.md sistemi için
     */
    public static function getTenantId(): int
    {
        try {
            $tenant = tenant();
            return $tenant ? $tenant->id : 1; // Varsayılan olarak 1 (central)
        } catch (\Exception $e) {
            return 1; // Hata durumunda central
        }
    }

    /**
     * Tenant dillerini al (cache'li)
     *
     * @param bool $activeOnly Sadece aktif dilleri getir (default: true)
     * @param bool $asArray Array olarak döndür (default: false)
     * @return \Illuminate\Support\Collection|array
     */
    public static function getTenantLanguages(bool $activeOnly = true, bool $asArray = false)
    {
        $cacheKey = 'tenant_languages_' . ($activeOnly ? 'active' : 'all');

        $languages = Cache::remember($cacheKey, now()->addHours(24), function () use ($activeOnly) {
            $query = DB::table('tenant_languages')->orderBy('sort_order');

            if ($activeOnly) {
                $query->where('is_active', 1);
            }

            return $query->get(['code', 'name', 'native_name', 'is_default', 'is_main_language']);
        });

        // Eğer hiç dil yoksa, fallback olarak 'tr' ekle
        if ($languages->isEmpty()) {
            $languages = collect([
                (object)['code' => 'tr', 'name' => 'Türkçe', 'native_name' => 'Türkçe', 'is_default' => 1, 'is_main_language' => 1]
            ]);
        }

        return $asArray ? $languages->toArray() : $languages;
    }

    /**
     * Tenant dil kodlarını al
     *
     * @param bool $activeOnly Sadece aktif dilleri getir (default: true)
     * @return array ['tr', 'en', 'ar']
     */
    public static function getTenantLanguageCodes(bool $activeOnly = true): array
    {
        return self::getTenantLanguages($activeOnly)->pluck('code')->toArray();
    }

    /**
     * Default tenant dilini al
     *
     * @return string Default dil kodu (örn: 'tr')
     */
    public static function getDefaultTenantLanguage(): string
    {
        $defaultLang = self::getTenantLanguages(true)
            ->where('is_default', 1)
            ->first();

        return $defaultLang ? $defaultLang->code : 'tr';
    }

    /**
     * Çoklu dil JSON objesi oluştur
     *
     * @param mixed $value Değer (string veya array)
     * @param array|null $languageCodes Dil kodları (null ise aktif diller kullanılır)
     * @return array ['tr' => '...', 'en' => '...', ...]
     */
    public static function createMultilingualJson($value, ?array $languageCodes = null): array
    {
        if (is_null($languageCodes)) {
            $languageCodes = self::getTenantLanguageCodes();
        }

        $result = [];

        // Eğer value zaten array ise, direkt kullan
        if (is_array($value)) {
            foreach ($languageCodes as $code) {
                $result[$code] = $value[$code] ?? $value[self::getDefaultTenantLanguage()] ?? '';
            }
        } else {
            // String ise, tüm dillere aynı değeri ata
            foreach ($languageCodes as $code) {
                $result[$code] = $value;
            }
        }

        return $result;
    }
}