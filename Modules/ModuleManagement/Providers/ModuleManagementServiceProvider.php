<?php
namespace Modules\ModuleManagement\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Modules\ModuleManagement\App\Http\Livewire\ModuleComponent;
use Modules\ModuleManagement\App\Http\Livewire\ModuleManageComponent;
use Modules\ModuleManagement\App\Http\Livewire\ModuleSlugSettingsComponent;
use Modules\ModuleManagement\App\Http\Livewire\Modals\DeleteModal;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ModuleManagementServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'ModuleManagement';

    protected string $nameLower = 'modulemanagement';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));

        $this->loadRoutesFrom(module_path('ModuleManagement', 'routes/web.php'));
        $this->loadRoutesFrom(module_path('ModuleManagement', 'routes/admin.php'));
        $this->loadViewsFrom(module_path('ModuleManagement', 'resources/views'), 'modulemanagement');
        $this->loadMigrationsFrom(module_path('ModuleManagement', 'database/migrations'));

        // Livewire bileşenlerini kaydet
        Livewire::component('module-component', ModuleComponent::class);
        Livewire::component('module-manage-component', ModuleManageComponent::class);
        Livewire::component('module-slug-settings-component', ModuleSlugSettingsComponent::class);
        Livewire::component('modals.delete-modal', DeleteModal::class);
        
        // Yeni modül eklendiğinde ve güncellendiğinde izinlerini otomatik oluştur ve admin'e ata
        \Modules\ModuleManagement\App\Models\Module::created(function($module) {
            $this->createModulePermissions($module);
        });
        
        \Modules\ModuleManagement\App\Models\Module::updated(function($module) {
            $this->updateModulePermissions($module);
        });
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Yeni modül için izinleri otomatik oluşturur ve admin rolüne ekler
     */
    protected function createModulePermissions($module): void
    {
        $permissionTypes = ['view', 'create', 'update', 'delete'];
        $createdPermissions = [];
        
        foreach ($permissionTypes as $type) {
            $permissionName = "{$module->name}.{$type}";
            
            // İzin yoksa oluştur
            $permission = Permission::firstOrCreate(
                [
                    'name' => $permissionName,
                    'guard_name' => 'web',
                ],
                [
                    'description' => "{$module->display_name} - " . ucfirst($type)
                ]
            );
            
            $createdPermissions[] = $permission;
        }
        
        // Root ve Admin rollerine izinleri ata
        $this->assignPermissionsToRoles($createdPermissions);

        // Modül cache temizleme
        \Illuminate\Support\Facades\Cache::forget("modules_tenant_central");
        app(\App\Services\ModuleAccessService::class)->clearModuleAccessCache();
    }

    /**
     * Modül güncellendiğinde izinleri günceller
     */
    protected function updateModulePermissions($module): void
    {
        // Modül aktif değilse işlem yapma
        if (!$module->is_active) {
            return;
        }
        
        $this->createModulePermissions($module);

        // Modül cache temizleme
        \Illuminate\Support\Facades\Cache::forget("modules_tenant_" . $module->tenant_id);
        app(\App\Services\ModuleAccessService::class)->clearModuleAccessCache();
    }
    
    /**
     * Root ve Admin rollerine izinleri atar
     */
    protected function assignPermissionsToRoles($permissions): void
    {
        try {
            // Root rolü her zaman tüm izinlere sahip olur
            $rootRole = Role::where('name', 'root')->first();
            if ($rootRole) {
                $rootRole->givePermissionTo($permissions);
            }
            
            // Admin rolüne izinleri ata (TenantManagement hariç)
            $adminRole = Role::where('name', 'admin')->first();
            if ($adminRole) {
                foreach ($permissions as $permission) {
                    // TenantManagement modülüne izin verme
                    if (strpos($permission->name, 'tenantmanagement.') !== 0) {
                        // SettingManagement için create ve delete hariç
                        if (strpos($permission->name, 'settingmanagement.create') !== 0 && 
                            strpos($permission->name, 'settingmanagement.delete') !== 0) {
                            $adminRole->givePermissionTo($permission);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            // Hata durumunda logla
            \Illuminate\Support\Facades\Log::error('Rol izin atama hatası: ' . $e->getMessage());
        }
    }


    /**
     * Register commands in the format of Command::class
     */
    protected function registerCommands(): void
    {
        // $this->commands([]);
    }

    /**
     * Register command Schedules.
     */
    protected function registerCommandSchedules(): void
    {
        // $this->app->booted(function () {
        //     $schedule = $this->app->make(Schedule::class);
        //     $schedule->command('inspire')->hourly();
        // });
    }

    /**
     * Register translations.
     */
    public function registerTranslations(): void
    {
        // Ana dil dosyaları - modül klasöründen yükle
        $moduleLangPath = module_path($this->name, 'lang');
        if (is_dir($moduleLangPath)) {
            $this->loadTranslationsFrom($moduleLangPath, $this->nameLower);
            $this->loadJsonTranslationsFrom($moduleLangPath);
        }
        
        // Resource'daki dil dosyaları (varsa)
        $resourceLangPath = resource_path('lang/modules/' . $this->nameLower);
        if (is_dir($resourceLangPath)) {
            $this->loadTranslationsFrom($resourceLangPath, $this->nameLower);
            $this->loadJsonTranslationsFrom($resourceLangPath);
        }
    }

    /**
     * Register config.
     */
    protected function registerConfig(): void
    {
        $relativeConfigPath = config('modules.paths.generator.config.path');
        $configPath         = module_path($this->name, $relativeConfigPath);

        if (is_dir($configPath)) {
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($configPath));

            foreach ($iterator as $file) {
                if ($file->isFile() && $file->getExtension() === 'php') {
                    $relativePath = str_replace($configPath . DIRECTORY_SEPARATOR, '', $file->getPathname());
                    $configKey    = $this->nameLower . '.' . str_replace([DIRECTORY_SEPARATOR, '.php'], ['.', ''], $relativePath);
                    $key          = ($relativePath === 'config.php') ? $this->nameLower : $configKey;

                    $this->publishes([$file->getPathname() => config_path($relativePath)], $configPath);
                    $this->mergeConfigFrom($file->getPathname(), $key);
                }
            }
        }
    }

    public function registerViews(): void
    {
        $viewPath   = resource_path('views/modules/modulemanagement');
        $sourcePath = module_path('ModuleManagement', 'resources/views');

        $this->publishes([
            $sourcePath => $viewPath,
        ], ['views', 'modulemanagement-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), 'modulemanagement');
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (config('view.paths') as $path) {
            if (is_dir($path . '/modules/modulemanagement')) {
                $paths[] = $path . '/modules/modulemanagement';
            }
        }

        return $paths;
    }

    /**
     * Tenant'a modül eklendiğinde veya aktifleştirildiğinde izinleri oluştur
     * 
     * @param int $moduleId Modül ID 
     * @param string $tenantId Tenant ID
     * @return void
     */
    public function handleModuleAddedToTenant($moduleId, $tenantId): void
    {
        try {
            $module = \Modules\ModuleManagement\App\Models\Module::find($moduleId);
            if (!$module) {
                \Illuminate\Support\Facades\Log::error("Module not found: {$moduleId}");
                return;
            }

            $this->createTenantModulePermissions($module, $tenantId);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Error creating tenant module permissions: " . $e->getMessage());
        }
    }

    /**
     * Tenant'tan modül kaldırıldığında izinleri de kaldır
     * 
     * @param int $moduleId Modül ID
     * @param string $tenantId Tenant ID
     * @return void 
     */
    public function handleModuleRemovedFromTenant($moduleId, $tenantId): void
    {
        try {
            $module = \Modules\ModuleManagement\App\Models\Module::find($moduleId);
            if (!$module) {
                \Illuminate\Support\Facades\Log::error("Module not found: {$moduleId}");
                return;
            }

            $this->removeTenantModulePermissions($module, $tenantId);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Error removing tenant module permissions: " . $e->getMessage());
        }
    }

    /**
     * Tenant için modül izinlerini oluştur
     * 
     * @param \Modules\ModuleManagement\App\Models\Module $module
     * @param string $tenantId
     * @return void
     */
    protected function createTenantModulePermissions($module, $tenantId): void
    {
        try {
            // Tenant'a geçiş yapalım
            tenancy()->initialize($tenantId);

            // Modül adı ve slug'ını hazırla
            $moduleSlug = \Illuminate\Support\Str::slug(\Illuminate\Support\Str::snake($module->name), '');
            $displayName = $module->display_name;

            // SettingManagement modülü kontrolü 
            $isSettingManagement = (
                $moduleSlug === 'settingmanagement' || 
                strtolower($module->name) === 'settingmanagement'
            );

            // Temel izin tipleri 
            if ($isSettingManagement) {
                // Setting Management için sadece görüntüleme ve güncelleme izinleri
                $permissionTypes = [
                    'view' => 'Görüntüleme',
                    'update' => 'Güncelleme',
                ];
            } else {
                // Diğer modüller için tüm izinler
                $permissionTypes = [
                    'view' => 'Görüntüleme',
                    'create' => 'Oluşturma',
                    'update' => 'Güncelleme',
                    'delete' => 'Silme',
                ];
            }

            // Her izin tipi için oluştur
            $createdPermissions = [];
            foreach ($permissionTypes as $type => $label) {
                $permissionName = "{$moduleSlug}.{$type}";
                $description = "{$displayName} - {$label}";
                
                // İzin yoksa oluştur
                $permission = \Spatie\Permission\Models\Permission::firstOrCreate(
                    ['name' => $permissionName, 'guard_name' => 'web'],
                    ['description' => $description]
                );
                
                $createdPermissions[] = $permission;
            }
            
            // Admin rolüne bu izinleri ata
            $adminRole = \Spatie\Permission\Models\Role::where('name', 'admin')->first();
            if ($adminRole) {
                $adminRole->givePermissionTo($createdPermissions);
            }

            // Tenant'dan çıkış yapalım
            tenancy()->end();
            
            \Illuminate\Support\Facades\Log::info("Permissions created for module {$module->name} in tenant {$tenantId}");
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Permission creation failed for tenant {$tenantId}, module {$module->name}: " . $e->getMessage());
        }
    }

    /**
     * Tenant'tan modül izinlerini kaldır
     * 
     * @param \Modules\ModuleManagement\App\Models\Module $module
     * @param string $tenantId
     * @return void
     */
    protected function removeTenantModulePermissions($module, $tenantId): void
    {
        try {
            // Tenant'a geçiş yapalım
            tenancy()->initialize($tenantId);

            // Modül adı ve slug'ını hazırla
            $moduleSlug = \Illuminate\Support\Str::slug(\Illuminate\Support\Str::snake($module->name), '');
            
            // SettingManagement modülü kontrolü 
            $isSettingManagement = (
                $moduleSlug === 'settingmanagement' || 
                strtolower($module->name) === 'settingmanagement'
            );

            // Temel izin tipleri
            if ($isSettingManagement) {
                $permissionTypes = ['view', 'update'];
            } else {
                $permissionTypes = ['view', 'create', 'update', 'delete'];
            }

            // Modüle ait izinleri bul ve sil
            foreach ($permissionTypes as $type) {
                $permissionName = "{$moduleSlug}.{$type}";
                
                // İzni bul
                $permission = \Spatie\Permission\Models\Permission::where('name', $permissionName)->first();
                
                if ($permission) {
                    // Roller ile ilişkisini kaldır
                    $permission->roles()->detach();
                    
                    // Kullanıcı izinlerini kaldır
                    $permission->users()->detach();
                    
                    // İzni sil
                    $permission->delete();
                }
            }
            
            // Kullanıcı-modül izinleri tablosundan ilgili izinleri temizle
            if (Schema::hasTable('user_module_permissions')) {
                DB::table('user_module_permissions')
                    ->where('module_name', $module->name)
                    ->delete();
                
                \Illuminate\Support\Facades\Log::info("User module permissions for module {$module->name} removed in tenant {$tenantId}");
            }

            // Tenant'dan çıkış yapalım
            tenancy()->end();
            
            \Illuminate\Support\Facades\Log::info("Permissions removed for module {$module->name} in tenant {$tenantId}");
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Permission removal failed for tenant {$tenantId}, module {$module->name}: " . $e->getMessage());
            
            // Tenant'dan çıkışı garantiye al
            if (tenancy()->initialized) {
                tenancy()->end();
            }
        }
    }
}