<?php
// Modules/UserManagement/routes/web.php
use Illuminate\Support\Facades\Route;
use Modules\UserManagement\App\Http\Livewire\UserComponent;
use Modules\UserManagement\App\Http\Livewire\UserManageComponent;
use Modules\UserManagement\App\Http\Livewire\RoleComponent;
use Modules\UserManagement\App\Http\Livewire\RoleManageComponent;
use Modules\UserManagement\App\Http\Livewire\PermissionComponent;
use Modules\UserManagement\App\Http\Livewire\PermissionManageComponent;
use Modules\UserManagement\App\Http\Livewire\ModulePermissionComponent;
use Modules\UserManagement\App\Http\Livewire\UserModulePermissionComponent;
use Modules\UserManagement\App\Http\Livewire\ActivityLogComponent;
use Modules\UserManagement\App\Http\Livewire\UserActivityLogComponent;

Route::middleware(['web', 'auth', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // User Routes
        Route::prefix('usermanagement')
            ->name('usermanagement.')
            ->group(function () {
                Route::get('/', UserComponent::class)
                    ->middleware('module.permission:usermanagement,view')
                    ->name('index');
                    
                // Kullanıcı yönetimi - id parametresi opsiyonel
                Route::get('/manage/{id?}', UserManageComponent::class)
                    ->middleware('module.permission:usermanagement,update')
                    ->name('manage');
                
                // Modül bazlı izinler
                Route::get('/module-permissions', ModulePermissionComponent::class)
                    ->middleware('module.permission:usermanagement,update')
                    ->name('module.permissions');
                    
                // Kullanıcı modül izinleri
                Route::get('/user-module-permissions/{id}', UserModulePermissionComponent::class)
                    ->middleware('module.permission:usermanagement,update')
                    ->where('id', '[0-9]+')
                    ->name('user.module.permissions');
                    
                // Aktivite log kayıtları
                Route::get('/activity-logs', ActivityLogComponent::class)
                    ->middleware('module.permission:usermanagement,view')
                    ->name('activity.logs');
                    
                // Kullanıcı aktivite log kayıtları
                Route::get('/user-activity-logs/{id}', UserActivityLogComponent::class)
                    ->middleware('module.permission:usermanagement,view')
                    ->where('id', '[0-9]+')
                    ->name('user.activity.logs');
            });

        // Role Routes    
        Route::prefix('usermanagement/role')
            ->name('usermanagement.role.')
            ->group(function () {
                Route::get('/', RoleComponent::class)
                    ->middleware('module.permission:usermanagement,view')
                    ->name('index');
                    
                // Rol yönetimi - id parametresi opsiyonel
                Route::get('/manage/{id?}', RoleManageComponent::class)
                    ->middleware('module.permission:usermanagement,update')
                    ->name('manage');
            });

        // Permission Routes    
        Route::prefix('usermanagement/permission')
            ->name('usermanagement.permission.')
            ->group(function () {
                Route::get('/', PermissionComponent::class)
                    ->middleware('module.permission:usermanagement,view')
                    ->name('index');
                    
                // İzin yönetimi - id parametresi opsiyonel
                Route::get('/manage/{id?}', PermissionManageComponent::class)
                    ->middleware('module.permission:usermanagement,update')
                    ->name('manage');
            });
    });