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
            'session.cookie' => env('SESSION_COOKIE', 'laravel_session'),
            'session.domain' => env('SESSION_DOMAIN'),
            'session.path' => env('SESSION_PATH', '/'),
            'session.secure' => env('SESSION_SECURE_COOKIE', false),
            'session.same_site' => env('SESSION_SAME_SITE', 'lax'),
            'session.connection' => env('SESSION_CONNECTION'),
            'session.files' => env('SESSION_FILES', storage_path('framework/sessions')),
        ]);
    }
}