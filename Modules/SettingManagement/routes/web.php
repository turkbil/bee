<?php
// Modules/SettingManagement/routes/web.php
use Illuminate\Support\Facades\Route;
use Modules\SettingManagement\App\Http\Livewire\GroupListComponent;
use Modules\SettingManagement\App\Http\Livewire\ItemListComponent;
use Modules\SettingManagement\App\Http\Livewire\ManageComponent;
use Modules\SettingManagement\App\Http\Livewire\TenantValueComponent;
use Modules\SettingManagement\App\Http\Livewire\GroupManageComponent;
use Modules\SettingManagement\App\Http\Livewire\ValuesComponent;
use Modules\SettingManagement\App\Http\Livewire\TenantSettingsComponent;

Route::middleware(['web', 'auth', 'tenant'])
    ->prefix('admin/settingmanagement')
    ->name('admin.settingmanagement.')
    ->group(function () {
        Route::get('/', GroupListComponent::class)
            ->middleware('module.permission:settingmanagement,view')
            ->name('index');
            
        // Grup yönetimi
        Route::get('/group/manage', GroupManageComponent::class)
            ->middleware('module.permission:settingmanagement,create')
            ->name('group.create');
            
        Route::get('/group/manage/{id}', GroupManageComponent::class)
            ->middleware('module.permission:settingmanagement,update')
            ->where('id', '[0-9]+')
            ->name('group.edit');
            
        Route::get('/group/list', GroupListComponent::class)
            ->middleware('module.permission:settingmanagement,view')
            ->name('group.list');
            
        // Öğe yönetimi
        Route::get('/items/{group}', ItemListComponent::class)
            ->middleware('module.permission:settingmanagement,view')
            ->name('items');
            
        Route::get('/item/manage', ManageComponent::class)
            ->middleware('module.permission:settingmanagement,create')
            ->name('manage');
            
        Route::get('/item/manage/{id}', ManageComponent::class)
            ->middleware('module.permission:settingmanagement,update')
            ->where('id', '[0-9]+')
            ->name('manage.edit');
            
        // Değer yönetimi
        Route::get('/value/{id}', TenantValueComponent::class)
            ->middleware('module.permission:settingmanagement,update')
            ->name('value');
            
        Route::get('/values/{group}', ValuesComponent::class)
            ->middleware('module.permission:settingmanagement,view')
            ->name('values');
            
        Route::get('/tenant/settings', TenantSettingsComponent::class)
            ->middleware('module.permission:settingmanagement,update')
            ->name('tenant.settings');
    });