<?php

use Illuminate\Support\Facades\Route;
use Modules\UserManagement\App\Http\Livewire\UserComponent;
use Modules\UserManagement\App\Http\Livewire\UserManageComponent;
use Modules\UserManagement\App\Http\Livewire\RoleComponent;
use Modules\UserManagement\App\Http\Livewire\RoleManageComponent;
use Modules\UserManagement\App\Http\Livewire\PermissionComponent;
use Modules\UserManagement\App\Http\Livewire\PermissionManageComponent;
use Modules\UserManagement\App\Http\Livewire\ModulePermissionComponent;
use Modules\UserManagement\App\Http\Livewire\UserModulePermissionComponent;

Route::middleware(['web', 'auth'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // User Routes
        Route::prefix('usermanagement')
            ->name('usermanagement.')
            ->group(function () {
                Route::get('/', UserComponent::class)->name('index');
                Route::get('/manage/{id?}', UserManageComponent::class)->name('manage');
                
                // Yeni eklenen rotalar
                Route::get('/module-permissions', ModulePermissionComponent::class)->name('module.permissions');
                Route::get('/user/{id}/module-permissions', UserModulePermissionComponent::class)->name('user.module.permissions');
            });

        // Role Routes    
        Route::prefix('usermanagement/role')
            ->name('usermanagement.role.')
            ->group(function () {
                Route::get('/', RoleComponent::class)->name('index');
                Route::get('/manage/{id?}', RoleManageComponent::class)->name('manage');
            });

        // Permission Routes    
        Route::prefix('usermanagement/permission')
            ->name('usermanagement.permission.')
            ->group(function () {
                Route::get('/', PermissionComponent::class)->name('index');
                Route::get('/manage/{id?}', PermissionManageComponent::class)->name('manage');
            });
    });