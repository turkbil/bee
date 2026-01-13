<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\ModuleEnabled;
use App\Events\ModuleDisabled;
use App\Listeners\LoadModuleRoutes;
use App\Listeners\ClearModuleRouteCache;
use App\Listeners\SendEmailVerificationNotificationWithSettingCheck;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotificationWithSettingCheck::class,
        ],

        // Remember Me cookie duration (tenant-aware)
        \Illuminate\Auth\Events\Login::class => [
            \App\Listeners\SetRememberMeCookieDuration::class,
        ],

        // Process pending favorite after login
        \Illuminate\Auth\Events\Authenticated::class => [
            \App\Listeners\ProcessPendingFavorite::class,
        ],

        // Module management events
        ModuleEnabled::class => [
            LoadModuleRoutes::class,
            ClearModuleRouteCache::class . '@handleModuleEnabled',
        ],
        
        ModuleDisabled::class => [
            ClearModuleRouteCache::class . '@handleModuleDisabled',
        ],
        
        // Module tenant permission events
        \App\Events\ModuleAddedToTenant::class => [
            \App\Listeners\HandleModuleTenantPermissions::class . '@handleModuleAdded',
        ],
        
        \App\Events\ModuleRemovedFromTenant::class => [
            \App\Listeners\HandleModuleTenantPermissions::class . '@handleModuleRemoved',
        ],

        // Tenant database events - TenancyServiceProvider'da tanımlı, buradan kaldırıldı

        // 🔧 Media Library - Otomatik ownership fix (psacln → psaserv)
        \Spatie\MediaLibrary\MediaCollections\Events\MediaHasBeenAdded::class => [
            \App\Listeners\MediaUploadedListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        // 🔥 GLOBAL RESPONSE CACHE TEMIZLEME
        // Herhangi bir Eloquent model kaydedildiginde/silindiginde response cache temizlenir
        // Bu sayede admin panelden yapilan tum degisiklikler aninda yansir
        \Illuminate\Database\Eloquent\Model::saved(function ($model) {
            $this->clearResponseCacheOnModelChange($model, 'saved');
        });

        \Illuminate\Database\Eloquent\Model::deleted(function ($model) {
            $this->clearResponseCacheOnModelChange($model, 'deleted');
        });
    }

    /**
     * Response cache'i model degisikliginde temizle
     * Sadece onemli modeller icin calisir (gereksiz temizlemeyi onler)
     */
    protected function clearResponseCacheOnModelChange($model, string $event): void
    {
        // Response cache aktif degilse cik
        if (!setting('response_cache_enabled', true)) {
            return;
        }

        // Cache temizleme gerektirmeyen modeller (log, session, cache, analytics vs.)
        $excludedModels = [
            // Logging & Monitoring
            'Spatie\\Activitylog\\Models\\Activity',
            'Laravel\\Telescope\\Storage\\EntryModel',
            'Laravel\\Pulse\\',
            'Spatie\\ResponseCache\\',

            // Player Analytics (çok sık güncellenir, cache temizlememeli!)
            'SongPlay',           // Dinleme kayıtları
            'ListeningHistory',   // Dinleme geçmişi
            'PlayHistory',        // Play history

            // Usage & Analytics
            'UsageLog',           // Kullanım logları
            'ApiLog',             // API logları
            'SearchQuery',        // Arama sorguları (analytics)

            // Session & Cart (kullanıcıya özel, cache'i etkilememeli)
            'Session',
            'Cart',
            'CartItem',

            // Queue & Jobs
            'Job',
            'FailedJob',
        ];

        $modelClass = get_class($model);
        foreach ($excludedModels as $excluded) {
            if (str_contains($modelClass, $excluded)) {
                return;
            }
        }

        // Analytics field'ları değişmişse cache temizleme (play_count, view_count vb.)
        $analyticsFields = ['play_count', 'view_count', 'download_count', 'click_count', 'last_played_at', 'updated_at'];
        $dirty = $model->getDirty();

        // Sadece analytics field'ları değiştiyse cache temizleme
        if (!empty($dirty)) {
            $nonAnalyticsChanges = array_diff(array_keys($dirty), $analyticsFields);
            if (empty($nonAnalyticsChanges)) {
                // Sadece analytics değişti, cache temizleme
                return;
            }
        }

        try {
            // ResponseCache paketini kontrol et
            if (class_exists(\Spatie\ResponseCache\Facades\ResponseCache::class)) {
                \Spatie\ResponseCache\Facades\ResponseCache::clear();

                if (config('app.debug')) {
                    \Log::debug("ResponseCache auto-cleared", [
                        'model' => $modelClass,
                        'event' => $event,
                        'tenant' => tenant()?->id ?? 'central',
                    ]);
                }
            }
        } catch (\Exception $e) {
            \Log::warning("ResponseCache auto-clear failed", [
                'model' => $modelClass,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }

    /**
     * Override to prevent Laravel's default email verification listener.
     * We use our own SendEmailVerificationNotificationWithSettingCheck.
     */
    protected function configureEmailVerification(): void
    {
        // Do nothing - we handle email verification in $listen array
        // with SendEmailVerificationNotificationWithSettingCheck
    }
}