<?php

use Illuminate\Support\Facades\Route;
use Modules\SettingManagement\App\Http\Livewire\Settings\GroupList;
use Modules\SettingManagement\App\Http\Livewire\Settings\ItemList;
use Modules\SettingManagement\App\Http\Livewire\Settings\Manage;
use Modules\SettingManagement\App\Http\Livewire\Settings\TenantValue;
use Modules\SettingManagement\App\Http\Livewire\Settings\GroupManage;
use Modules\SettingManagement\App\Http\Livewire\Settings\Values;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

// Central Routes - Ana veritabanı için
Route::middleware(['web', 'auth'])
    ->prefix('admin/settingmanagement')
    ->name('admin.settingmanagement.')
    ->group(function () {
        Route::get('/', GroupList::class)->name('index');
        Route::get('/group/manage/{id?}', GroupManage::class)->name('group.manage');
        Route::get('/group/list', GroupList::class)->name('group.list');
        Route::get('/items/{group}', ItemList::class)->name('items');
        Route::get('/item/manage', Manage::class)->name('manage');
        Route::get('/item/manage/{id}', Manage::class)->name('manage.edit');
        Route::get('/value/{id}', TenantValue::class)->name('value');
        Route::get('/values/{group}', Values::class)->name('values');
    });