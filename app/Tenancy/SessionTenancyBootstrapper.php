<?php

namespace App\Tenancy;

use Stancl\Tenancy\Contracts\TenancyBootstrapper;
use Stancl\Tenancy\Contracts\Tenant;
use App\Services\TenantSessionService;

class SessionTenancyBootstrapper implements TenancyBootstrapper
{
    protected TenantSessionService $sessionService;

    public function __construct()
    {
        $this->sessionService = app(TenantSessionService::class);
    }

    public function bootstrap(Tenant $tenant)
    {
        // TenantSessionService ile session yapılandırması
        $this->sessionService->configureTenantSession();
    }

    public function revert()
    {
        // Orijinal session ayarlarına geri dön
        config([
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
            config(['database.redis.options.prefix' => env('REDIS_PREFIX', '')]);
        }
    }
}