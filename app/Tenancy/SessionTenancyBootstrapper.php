<?php

namespace App\Tenancy;

use Stancl\Tenancy\Contracts\TenancyBootstrapper;
use Stancl\Tenancy\Contracts\Tenant;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\File;

class SessionTenancyBootstrapper implements TenancyBootstrapper
{
    public function bootstrap(Tenant $tenant)
    {
        $domain = Request::getHost();
        $tenantKey = $tenant->getTenantKey();
        
        // Tenant domain'i için session ayarları
        Config::set([
            'session.cookie' => 'laravel_session_tenant_' . $tenantKey,
            'session.domain' => $domain,
            'session.path' => '/',
            'session.secure' => Request::isSecure(),
            'session.same_site' => 'lax',
            'session.table' => 'sessions', // Tenant database'inde sessions tablosu
        ]);
        
        // Session sürücüsünü tenant database'ine yönlendir
        if (config('session.driver') === 'database') {
            Config::set('session.connection', 'tenant');
        }
        
        // File driver kullanılıyorsa tenant'a özel dizin oluştur
        if (config('session.driver') === 'file') {
            $sessionPath = storage_path('framework/sessions/tenant_' . $tenantKey);
            
            // Dizin yoksa oluştur
            if (!File::exists($sessionPath)) {
                File::makeDirectory($sessionPath, 0755, true);
            }
            
            Config::set('session.files', $sessionPath);
        }
        
        // Redis driver kullanılıyorsa tenant prefix
        if (config('session.driver') === 'redis') {
            Config::set([
                'session.connection' => 'default',
                'database.redis.options.prefix' => 'tenant_' . $tenantKey . ':session:',
            ]);
        }
    }

    public function revert()
    {
        // Orijinal session ayarlarına geri dön
        Config::set([
            'session.cookie' => env('SESSION_COOKIE', 'laravel_session'),
            'session.domain' => env('SESSION_DOMAIN'),
            'session.path' => env('SESSION_PATH', '/'),
            'session.secure' => env('SESSION_SECURE_COOKIE', false),
            'session.same_site' => env('SESSION_SAME_SITE', 'lax'),
            'session.connection' => env('SESSION_CONNECTION'),
            'session.files' => storage_path('framework/sessions'),
            'session.table' => env('SESSION_TABLE', 'sessions'),
        ]);
        
        // Redis prefix'i temizle
        if (config('session.driver') === 'redis') {
            Config::set('database.redis.options.prefix', env('REDIS_PREFIX', ''));
        }
    }
}