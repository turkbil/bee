<?php
// Modules/ThemeManagement/routes/admin.php
use Illuminate\Support\Facades\Route;
use Modules\ThemeManagement\App\Http\Livewire\ThemeManagementComponent;
use Modules\ThemeManagement\App\Http\Livewire\ThemeManagementManageComponent;

Route::middleware(['web', 'auth', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::prefix('thememanagement')
            ->name('thememanagement.')
            ->group(function () {
                Route::get('/', ThemeManagementComponent::class)
                    ->middleware('module.permission:thememanagement,view')
                    ->name('index');
                Route::get('/manage/{id?}', ThemeManagementManageComponent::class)
                    ->middleware('module.permission:thememanagement,update')
                    ->name('manage');
            });
    });