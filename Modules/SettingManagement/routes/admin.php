<?php
// Modules/SettingManagement/routes/admin.php
use Illuminate\Support\Facades\Route;
use Modules\SettingManagement\App\Http\Livewire\GroupListComponent;
use Modules\SettingManagement\App\Http\Livewire\ManageComponent;
use Modules\SettingManagement\App\Http\Livewire\GroupManageComponent;
use Modules\SettingManagement\App\Http\Livewire\ValuesComponent;
use Modules\SettingManagement\App\Http\Livewire\TenantSettingsComponent;
use Modules\SettingManagement\App\Http\Livewire\FormBuilderComponent;
use Modules\SettingManagement\App\Http\Controllers\FormBuilderController;

Route::middleware(['admin', 'tenant'])
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
                    ->middleware(['module.permission:settingmanagement,update', 'root.access'])
                    ->name('group.manage');
                    
                Route::get('/group/list', GroupListComponent::class)
                    ->middleware('module.permission:settingmanagement,view')
                    ->name('group.list');
                    
                // items/{group} rotası kaldırıldı
                    
                // item/manage rotaları kaldırıldı
                    
                // value/{id} rotası kaldırıldı
                    
                Route::get('/values/{group}', ValuesComponent::class)
                    ->middleware('module.permission:settingmanagement,view')
                    ->name('values');
                    
                Route::get('/tenant/settings', TenantSettingsComponent::class)
                    ->middleware(['module.permission:settingmanagement,update', 'root.access'])
                    ->name('tenant.settings');

                // Form Builder routes - Livewire yaklaşımıyla
                // İndex rotası kaldırıldı

                Route::get('/form-builder/{groupId}', FormBuilderComponent::class)
                    ->middleware('module.permission:settingmanagement,update')
                    ->name('form-builder.edit');

                // Form Builder form kayıt işlemi için POST route
                Route::post('/form-builder/{groupId}/save', [FormBuilderController::class, 'save'])
                    ->middleware('module.permission:settingmanagement,update')
                    ->name('form-builder.save');

                // Form Builder API endpoints
                Route::get('/form-builder/{groupId}/load', [FormBuilderController::class, 'load'])
                    ->middleware('module.permission:settingmanagement,view')
                    ->name('form-builder.load');
                    
                Route::get('/api/settings', [FormBuilderController::class, 'getSettings'])
                    ->middleware('module.permission:settingmanagement,view')
                    ->name('api.settings');
                    
                // Grup bilgisi endpoint'i
                Route::get('/api/groups/{groupId}', function ($groupId) {
                    $group = \Modules\SettingManagement\App\Models\SettingGroup::find($groupId);
                    if (!$group) {
                        return response()->json(['error' => 'Grup bulunamadı'], 404);
                    }
                    
                    return response()->json([
                        'id' => $group->id,
                        'name' => $group->name,
                        'prefix' => $group->prefix,
                        'slug' => $group->slug
                    ]);
                })->middleware('module.permission:settingmanagement,view')
                  ->name('api.groups.show');
            });
    });