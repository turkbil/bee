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
        
        // Disk yapılandırmasını önbellekte kontrol et
        $diskCacheKey = 'tenant_disk_' . $tenantId;
        $diskConfig = Cache::remember($diskCacheKey, now()->addDays(7), function () use ($tenantId) {
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
            
            return $tenantDisk;
        });
        
        return $diskConfig;
    }
}