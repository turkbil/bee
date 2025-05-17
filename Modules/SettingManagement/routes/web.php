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
use Modules\SettingManagement\App\Http\Controllers\FormBuilderController;

Route::middleware(['web', 'auth', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::prefix('settingmanagement')
            ->name('settingmanagement.')
            ->group(function () {
                Route::get('/', GroupListComponent::class)
                    ->middleware('module.permission:settingmanagement,view')
                    ->name('index');
                    
                Route::get('/group/manage/{id?}', GroupManageComponent::class)
                    ->middleware('module.permission:settingmanagement,update')
                    ->name('group.manage');
                    
                Route::get('/group/list', GroupListComponent::class)
                    ->middleware('module.permission:settingmanagement,view')
                    ->name('group.list');
                    
                Route::get('/items/{group}', ItemListComponent::class)
                    ->middleware('module.permission:settingmanagement,view')
                    ->name('items');
                    
                Route::get('/item/manage', ManageComponent::class)
                    ->middleware('module.permission:settingmanagement,create')
                    ->name('manage');
                    
                Route::get('/item/manage/{id}', ManageComponent::class)
                    ->middleware('module.permission:settingmanagement,update')
                    ->name('manage.edit');
                    
                Route::get('/value/{id}', TenantValueComponent::class)
                    ->middleware('module.permission:settingmanagement,update')
                    ->name('value');
                    
                Route::get('/values/{group}', ValuesComponent::class)
                    ->middleware('module.permission:settingmanagement,view')
                    ->name('values');
                    
                Route::get('/tenant/settings', TenantSettingsComponent::class)
                    ->middleware('module.permission:settingmanagement,update')
                    ->name('tenant.settings');
                
                // Form Builder routes
                Route::get('/form-builder', [FormBuilderController::class, 'index'])
                    ->middleware('module.permission:settingmanagement,view')
                    ->name('form-builder.index');
                
                Route::get('/form-builder/{groupId}/edit', [FormBuilderController::class, 'edit'])
                ->middleware('module.permission:settingmanagement,update')
                ->name('form-builder.edit');
                
                Route::post('/form-builder/{groupId}/save', [FormBuilderController::class, 'save'])
                    ->middleware('module.permission:settingmanagement,update')
                    ->name('form-builder.save');
                
                Route::get('/form-builder/{groupId}/load', [FormBuilderController::class, 'load'])
                    ->middleware('module.permission:settingmanagement,view')
                    ->name('form-builder.load');
            });
    });