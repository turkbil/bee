<?php

namespace Modules\UserManagement\Providers;

use Illuminate\Support\ServiceProvider;

class ModulePermissionHelperServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('module-permission', function () {
            return new \Modules\UserManagement\App\Models\ModulePermission();
        });
    }

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        // Helper dosyasını dahil et
        $this->loadHelpers();
    }

    /**
     * Helper dosyasını yükle.
     *
     * @return void
     */
    protected function loadHelpers()
    {
        $helperPath = module_path('UserManagement', 'Helpers/ModulePermissionHelperFunctions.php');
        
        if (file_exists($helperPath)) {
            require_once $helperPath;
        }
    }
}