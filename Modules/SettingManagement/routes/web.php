<?php

use Illuminate\Support\Facades\Route;
use Modules\SettingManagement\App\Http\Livewire\GroupListComponent;
use Modules\SettingManagement\App\Http\Livewire\ItemListComponent;
use Modules\SettingManagement\App\Http\Livewire\ManageComponent;
use Modules\SettingManagement\App\Http\Livewire\TenantValueComponent;
use Modules\SettingManagement\App\Http\Livewire\GroupManageComponent;
use Modules\SettingManagement\App\Http\Livewire\ValuesComponent;
use Modules\SettingManagement\App\Http\Livewire\TenantSettingsComponent;

// Central Routes - Ana veritabanı için
Route::middleware(['web', 'auth'])
    ->prefix('admin/settingmanagement')
    ->name('admin.settingmanagement.')
    ->group(function () {
        Route::get('/', GroupListComponent::class)->name('index');
        Route::get('/group/manage/{id?}', GroupManageComponent::class)->name('group.manage');
        Route::get('/group/list', GroupListComponent::class)->name('group.list');
        Route::get('/items/{group}', ItemListComponent::class)->name('items');
        Route::get('/item/manage', ManageComponent::class)->name('manage');
        Route::get('/item/manage/{id}', ManageComponent::class)->name('manage.edit');
        Route::get('/value/{id}', TenantValueComponent::class)->name('value');
        Route::get('/values/{group}', ValuesComponent::class)->name('values');
        Route::get('/tenant/settings', TenantSettingsComponent::class)->name('tenant.settings');
    });