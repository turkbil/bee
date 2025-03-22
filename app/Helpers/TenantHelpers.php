<?php
namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Stancl\Tenancy\Tenancy;

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
                $tenant = \App\Models\Tenant::find($tenantId);
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
        
        // Disk yapılandırmasını ayarla
        Config::set('filesystems.disks.tenant', [
            'driver' => 'local',
            'root' => storage_path("tenant{$tenantId}/app/public"),
            'url' => env('APP_URL').'/storage/tenant'.$tenantId,
            'visibility' => 'public',
            'throw' => false,
        ]);
        
        // Tenant disk'ini döndür
        return $tenantDisk;
    }
}

// Global helper fonksiyonları

if (!function_exists('cdn')) {
    /**
     * Central domain üzerinden asset URL'i oluşturur
     * 
     * @param string $path
     * @return string
     */
    function cdn($path)
    {
        // URL'in başındaki ve sonundaki slash'leri düzelt
        $path = ltrim($path, '/');
        
        // APP_URL'i kullanarak central domain üzerinden URL oluştur
        return rtrim(env('APP_URL'), '/') . '/' . $path;
    }
}

if (!function_exists('tenant_id')) {
    /**
     * Aktif tenant ID'sini döndürür, tenant yoksa null döner
     * 
     * @return int|null
     */
    function tenant_id()
    {
        return \App\Helpers\TenantHelpers::getCurrentTenantId();
    }
}

if (!function_exists('is_tenant')) {
    /**
     * İşlemin tenant veritabanında olup olmadığını kontrol eder
     * 
     * @return bool
     */
    function is_tenant()
    {
        return \App\Helpers\TenantHelpers::isTenant();
    }
}

if (!function_exists('is_central')) {
    /**
     * İşlemin central veritabanında olup olmadığını kontrol eder
     * 
     * @return bool
     */
    function is_central()
    {
        return \App\Helpers\TenantHelpers::isCentral();
    }
}

if (!function_exists('tenant_name')) {
    /**
     * Aktif tenant'ın adını döndürür, tenant yoksa null döner
     * 
     * @return string|null
     */
    function tenant_name()
    {
        if (is_tenant()) {
            return tenant()->name ?? tenant()->id;
        }
        
        return null;
    }
}

if (!function_exists('tenant_domain')) {
    /**
     * Aktif tenant'ın domain adresini döndürür, tenant yoksa null döner
     * 
     * @return string|null
     */
    function tenant_domain()
    {
        if (is_tenant()) {
            return tenant()->domains->first()->domain ?? null;
        }
        
        return null;
    }
}

if (!function_exists('tenant_disk')) {
    /**
     * Aktif tenant için disk adını döndürür
     * Central için 'public', tenant için 'tenant' döndürür ve yapılandırır
     *
     * @return string
     */
    function tenant_disk()
    {
        return \App\Helpers\TenantHelpers::getTenantDiskConfig();
    }
}

if (!function_exists('tenant_storage_path')) {
    /**
     * Tenant için depolama yolunu oluşturur
     * Tenant1 için normal storage yolu, diğer tenant'lar için tenant{id} yolu kullanır
     *
     * @param int|null $tenantId
     * @param string $path
     * @return string
     */
    function tenant_storage_path($path = '', $tenantId = null)
    {
        if ($tenantId === null) {
            $tenantId = tenant_id() ?? 1;
        }
        
        // Central için normal storage yolu
        if ($tenantId == 1) {
            return storage_path('app/public/' . ltrim($path, '/'));
        }
        
        // Tenant için tenant{id} yolu
        return storage_path('tenant' . $tenantId . '/app/public/' . ltrim($path, '/'));
    }
}

if (!function_exists('tenant_storage_url')) {
    /**
     * Tenant için depolama URL'ini oluşturur
     * tenant{id} formatında URL döndürür
     *
     * @param string $path
     * @param int|null $tenantId
     * @return string
     */
    function tenant_storage_url($path = '', $tenantId = null)
    {
        if ($tenantId === null) {
            $tenantId = tenant_id() ?? 1;
        }
        
        // Tenant ID'li URL formatı
        return url('/storage/tenant' . $tenantId . '/' . ltrim($path, '/'));
    }
}