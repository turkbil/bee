<?php

namespace App\Tenancy;

use Stancl\Tenancy\Contracts\TenancyBootstrapper;
use Stancl\Tenancy\Contracts\Tenant;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;

class SessionTenancyBootstrapper implements TenancyBootstrapper
{
    public function bootstrap(Tenant $tenant)
    {
        $domain = Request::getHost();
        
        // Tenant domain'i için session ayarları
        Config::set([
            'session.cookie' => 'laravel_session_' . str_replace('.', '_', $domain),
            'session.domain' => $domain, // Tam domain kullan (subdomain desteği için)
            'session.path' => '/',
            'session.secure' => Request::isSecure(), // Otomatik HTTPS kontrolü
            'session.same_site' => 'lax',
            'session.table' => 'sessions', // Tenant database'inde sessions tablosu
        ]);
        
        // Session sürücüsünü tenant database'ine yönlendir
        if (config('session.driver') === 'database') {
            Config::set('session.connection', 'tenant');
        }
        
        // File driver kullanılıyorsa tenant'a özel dizin
        if (config('session.driver') === 'file') {
            Config::set('session.files', storage_path('framework/sessions/' . $tenant->getTenantKey()));
        }
    }

    public function revert()
    {
        // Orijinal session ayarlarına geri dön
        Config::set([
            'session.cookie' => config('session.cookie'),
            'session.domain' => env('SESSION_DOMAIN'),
            'session.connection' => env('SESSION_CONNECTION'),
        ]);
    }
}