<?php
use Illuminate\Support\Facades\Route;
use Modules\SettingManagement\App\Http\Livewire\GroupListComponent;
use Modules\SettingManagement\App\Http\Livewire\ItemListComponent;
use Modules\SettingManagement\App\Http\Livewire\ManageComponent;
use Modules\SettingManagement\App\Http\Livewire\TenantValueComponent;
use Modules\SettingManagement\App\Http\Livewire\GroupManageComponent;
use Modules\SettingManagement\App\Http\Livewire\ValuesComponent;
use Modules\SettingManagement\App\Http\Livewire\TenantSettingsComponent;

Route::middleware(['web', 'auth', 'tenant', 'module.permission:settingmanagement,view'])
    ->prefix('admin/settingmanagement')
    ->name('admin.settingmanagement.')
    ->group(function () {
        Route::get('/', GroupListComponent::class)->name('index');
        Route::get('/group/manage/{id?}', GroupManageComponent::class)
            ->middleware('module.permission:settingmanagement,update')
            ->name('group.manage');
        Route::get('/group/list', GroupListComponent::class)->name('group.list');
        Route::get('/items/{group}', ItemListComponent::class)->name('items');
        Route::get('/item/manage', ManageComponent::class)
            ->middleware('module.permission:settingmanagement,update')
            ->name('manage');
        Route::get('/item/manage/{id}', ManageComponent::class)
            ->middleware('module.permission:settingmanagement,update')
            ->name('manage.edit');
        Route::get('/value/{id}', TenantValueComponent::class)
            ->middleware('module.permission:settingmanagement,update')
            ->name('value');
        Route::get('/values/{group}', ValuesComponent::class)->name('values');
        Route::get('/tenant/settings', TenantSettingsComponent::class)
            ->middleware('module.permission:settingmanagement,update')
            ->name('tenant.settings');
    });