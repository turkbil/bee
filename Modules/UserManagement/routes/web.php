<?php
use Illuminate\Support\Facades\Route;
use Modules\UserManagement\App\Http\Livewire\UserComponent;
use Modules\UserManagement\App\Http\Livewire\UserManageComponent;
use Modules\UserManagement\App\Http\Livewire\RoleComponent;
use Modules\UserManagement\App\Http\Livewire\RoleManageComponent;
use Modules\UserManagement\App\Http\Livewire\PermissionComponent;
use Modules\UserManagement\App\Http\Livewire\PermissionManageComponent;
use Modules\UserManagement\App\Http\Livewire\ModulePermissionComponent;

Route::middleware(['web', 'auth', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // User Routes
        Route::prefix('usermanagement')
            ->name('usermanagement.')
            ->middleware('module.permission:usermanagement,view')
            ->group(function () {
                Route::get('/', UserComponent::class)->name('index');
                Route::get('/manage/{id?}', UserManageComponent::class)
                    ->middleware('module.permission:usermanagement,update')
                    ->name('manage');
                
                // Modül bazlı izinler
                Route::get('/module-permissions', ModulePermissionComponent::class)
                    ->middleware('module.permission:usermanagement,update')
                    ->name('module.permissions');
            });

        // Role Routes    
        Route::prefix('usermanagement/role')
            ->name('usermanagement.role.')
            ->middleware('module.permission:usermanagement,update')
            ->group(function () {
                Route::get('/', RoleComponent::class)->name('index');
                Route::get('/manage/{id?}', RoleManageComponent::class)->name('manage');
            });

        // Permission Routes    
        Route::prefix('usermanagement/permission')
            ->name('usermanagement.permission.')
            ->middleware('module.permission:usermanagement,update')
            ->group(function () {
                Route::get('/', PermissionComponent::class)->name('index');
                Route::get('/manage/{id?}', PermissionManageComponent::class)->name('manage');
            });
    });