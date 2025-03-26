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
                    
                // Yeni kullanıcı oluşturma
                Route::get('/manage', UserManageComponent::class)
                    ->middleware('module.permission:usermanagement,create')
                    ->name('create');
                    
                // Mevcut kullanıcı düzenleme
                Route::get('/manage/{id}', UserManageComponent::class)
                    ->middleware('module.permission:usermanagement,update')
                    ->where('id', '[0-9]+')
                    ->name('edit');
                
                // Modül bazlı izinler
                Route::get('/module-permissions', ModulePermissionComponent::class)
                    ->middleware('module.permission:usermanagement,update')
                    ->name('module.permissions');
                    
                // Kullanıcı modül izinleri
                Route::get('/user-module-permissions/{id}', UserModulePermissionComponent::class)
                    ->middleware('module.permission:usermanagement,update')
                    ->where('id', '[0-9]+')
                    ->name('user.module.permissions');
            });

        // Role Routes    
        Route::prefix('usermanagement/role')
            ->name('usermanagement.role.')
            ->group(function () {
                Route::get('/', RoleComponent::class)
                    ->middleware('module.permission:usermanagement,view')
                    ->name('index');
                    
                // Yeni rol oluşturma
                Route::get('/manage', RoleManageComponent::class)
                    ->middleware('module.permission:usermanagement,create')
                    ->name('create');
                    
                // Mevcut rol düzenleme
                Route::get('/manage/{id}', RoleManageComponent::class)
                    ->middleware('module.permission:usermanagement,update')
                    ->where('id', '[0-9]+')
                    ->name('edit');
            });

        // Permission Routes    
        Route::prefix('usermanagement/permission')
            ->name('usermanagement.permission.')
            ->group(function () {
                Route::get('/', PermissionComponent::class)
                    ->middleware('module.permission:usermanagement,view')
                    ->name('index');
                    
                // Yeni izin oluşturma
                Route::get('/manage', PermissionManageComponent::class)
                    ->middleware('module.permission:usermanagement,create')
                    ->name('create');
                    
                // Mevcut izin düzenleme
                Route::get('/manage/{id}', PermissionManageComponent::class)
                    ->middleware('module.permission:usermanagement,update')
                    ->where('id', '[0-9]+')
                    ->name('edit');
            });
    });