<?php  
namespace App\Providers;  

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;
use App\Helpers\TenantHelpers;
use App\Services\ModuleTenantPermissionService;
use App\Services\SettingsService;
use App\Services\TenantCacheManager;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Contracts binding
        $this->app->bind(
            \App\Contracts\ModuleAccessServiceInterface::class,
            \App\Services\ModuleAccessService::class
        );
        
        $this->app->bind(
            \App\Contracts\ThemeRepositoryInterface::class,
            \App\Repositories\ThemeRepository::class
        );
        
        $this->app->bind(
            \App\Contracts\DynamicRouteResolverInterface::class,
            \App\Services\DynamicRouteResolver::class
        );
        
        $this->app->bind(
            \App\Contracts\ModuleRepositoryInterface::class,
            \App\Repositories\ModuleRepository::class
        );
        
        // URL Builder Interface binding - YENİ
        $this->app->bind(
            \App\Contracts\UrlBuilderInterface::class,
            \App\Services\UnifiedUrlBuilderService::class
        );
        
        // ModuleService binding
        $this->app->singleton(\App\Services\ModuleService::class);
        
        // URL ve Locale servisleri - YENİ
        $this->app->singleton(\App\Services\UnifiedUrlBuilderService::class);
        $this->app->singleton(\App\Services\LocaleValidationService::class);
        $this->app->singleton(\App\Services\HomepageRouteService::class);
        
        // Service singletons
        $this->app->singleton(ModuleTenantPermissionService::class, function ($app) {
            return new ModuleTenantPermissionService();
        });
        
        $this->app->singleton('settings', function ($app) {
            return new SettingsService();
        });
        
        // ThemeService singleton - simplified for emergency fix
        $this->app->singleton(\App\Services\ThemeService::class, function ($app) {
            return new \App\Services\ThemeService();
        });
        
        // Module permission helper services
        $this->app->singleton(\App\Services\ModulePermissionChecker::class);
        $this->app->singleton(\App\Services\ModuleAccessCache::class);
        
        // Dynamic route services
        $this->app->singleton(\App\Services\DynamicRouteRegistrar::class);
        
        // Tenant Cache Manager
        $this->app->singleton(TenantCacheManager::class, function ($app) {
            return new TenantCacheManager();
        });
        
        $this->loadHelperFiles();
    }

    public function boot(): void
    {
        // Manual module translations registration
        $this->loadModuleTranslations();
        
        // Register Livewire Components
        $this->registerLivewireComponents();
        
        // Register Blade Components
        $this->registerBladeComponents();
        
        // HTTPS kullanıyorsanız bu ayarı aktif edin
        if(env('APP_ENV') !== 'local') {
            URL::forceScheme('https');
        }
        
        // Tenant için Redis önbellek yapılandırması
        if (TenantHelpers::isTenant()) {
            $tenantId = tenant_id();
            
            // ResponseCache için tenant bazlı tag ayarlaması
            config([
                'responsecache.cache_tag' => 'tenant_' . $tenantId . '_response_cache',
                'responsecache.cache_lifetime_in_seconds' => 86400, // 24 saat
            ]);
        }
        
        // Performance: View Composers for commonly used data
        $this->registerViewComposers();
    }
    
    protected function registerViewComposers(): void
    {
        // Tenant Languages View Composer - Cache için optimize edilmiş
        view()->composer([
            'page::admin.livewire.page-manage-component',
            'portfolio::admin.livewire.portfolio-manage-component',
            'portfolio::admin.livewire.portfolio-category-manage-component',
            'announcement::admin.livewire.announcement-manage-component'
        ], function ($view) {
            // Admin panelinde cache kullanma - Her zaman fresh data
            if (request()->is('admin*')) {
                $tenantLanguages = \Modules\LanguageManagement\app\Models\TenantLanguage::orderBy('is_active', 'desc')
                    ->orderBy('sort_order', 'asc')
                    ->orderBy('id', 'asc')
                    ->get();
            } else {
                // Sadece public sayfalarda cache kullan
                $tenantLanguages = \Illuminate\Support\Facades\Cache::remember(
                    'tenant_languages_for_forms', 
                    3600, // 1 saat
                    function () {
                        return \Modules\LanguageManagement\app\Models\TenantLanguage::orderBy('is_active', 'desc')
                            ->orderBy('sort_order', 'asc')
                            ->orderBy('id', 'asc')
                            ->get();
                    }
                );
            }
            
            $view->with('cachedTenantLanguages', $tenantLanguages);
        });
    }
    
    protected function loadHelperFiles(): void
    {
        $helpersPath = app_path('Helpers');
        if (is_dir($helpersPath)) {
            $files = glob($helpersPath . '/*.php');
            foreach ($files as $file) {
                require_once $file;
            }
        }
    }
    
    protected function loadModuleTranslations(): void
    {
        $modulesPath = base_path('Modules');
        if (is_dir($modulesPath)) {
            $modules = array_diff(scandir($modulesPath), ['.', '..']);
            foreach ($modules as $module) {
                $moduleLangPath = $modulesPath . '/' . $module . '/lang';
                if (is_dir($moduleLangPath)) {
                    $moduleNameLower = strtolower($module);
                    $this->loadTranslationsFrom($moduleLangPath, $moduleNameLower);
                }
            }
        }
    }
    
    protected function registerLivewireComponents(): void
    {
        // AI Token Management Components - admin prefix ile kayıt
        Livewire::component('admin.ai-token-packages-component', \App\Http\Livewire\Admin\AITokenPackagesComponent::class);
        Livewire::component('admin.ai-token-purchases-component', \App\Http\Livewire\Admin\AITokenPurchasesComponent::class);
        Livewire::component('admin.ai-token-usage-stats-component', \App\Http\Livewire\Admin\AITokenUsageStatsComponent::class);
        
        // Cache Clear Button Component
        Livewire::component('admin.cache-clear-buttons', \App\Http\Livewire\Admin\CacheClearButtons::class);
        
        // Eski kayıtlar da korunacak (backward compatibility)
        Livewire::component('ai-token-packages', \App\Http\Livewire\Admin\AITokenPackagesComponent::class);
        Livewire::component('ai-token-purchases', \App\Http\Livewire\Admin\AITokenPurchasesComponent::class);
        Livewire::component('ai-token-usage-stats', \App\Http\Livewire\Admin\AITokenUsageStatsComponent::class);
        Livewire::component('ai-token-package-management', \App\Http\Livewire\Admin\AITokenPackageManagementComponent::class);
        Livewire::component('ai-token-purchase-management', \App\Http\Livewire\Admin\AITokenPurchaseManagementComponent::class);
        Livewire::component('ai-token-usage-management', \App\Http\Livewire\Admin\AITokenUsageManagementComponent::class);
    }
    
    protected function registerBladeComponents(): void
    {
        // Register Progress Circle Component
        Blade::component('progress-circle', \App\View\Components\ProgressCircle::class);
        
        // Register SEO Meta Component
        Blade::component('seo-meta', \App\View\Components\SeoMeta::class);
    }
}