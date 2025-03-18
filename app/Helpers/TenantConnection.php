<?php
namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Stancl\Tenancy\Tenancy;

class TenantConnection
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
}