<?php
// Modules/MenuManagement/routes/admin.php
use Illuminate\Support\Facades\Route;
use Modules\MenuManagement\App\Http\Livewire\Admin\MenuComponent;
use Modules\MenuManagement\App\Http\Livewire\Admin\MenuManageComponent;
use Modules\MenuManagement\App\Http\Livewire\Admin\MenuItemManageComponent;

// Admin rotaları
Route::middleware(['admin', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::prefix('menumanagement')
            ->name('menumanagement.')
            ->group(function () {
                // Ana sayfa - header menü yönetimi (menü öğesi ekleme/düzenleme)
                Route::get('/', MenuItemManageComponent::class)
                    ->middleware('module.permission:menumanagement,view')
                    ->name('index');
                    
                // Menü listesi sayfası
                Route::get('/menu', MenuComponent::class)
                    ->middleware('module.permission:menumanagement,view')
                    ->name('menu.index');
                    
                // Menü oluşturma/düzenleme sayfası (Menü ayarları)
                Route::get('/menu/manage/{id?}', MenuManageComponent::class)
                    ->middleware('module.permission:menumanagement,update')
                    ->name('menu.manage');
                    
                // Belirli menünün öğelerini düzenle (ID=1 dışındaki menüler için)
                Route::get('/menu/{id}', MenuItemManageComponent::class)
                    ->middleware('module.permission:menumanagement,view')
                    ->name('menu.edit')
                    ->where('id', '[0-9]+');
                    
                Route::post('/set-editing-language', function () {
                    return response()->json(['status' => 'success']);
                })->name('set-editing-language');
                
                // Menu item reordering API endpoint
                Route::post('/reorder-items', function () {
                    return response()->json(['status' => 'success']);
                })->name('reorder-items');
                
                // Menu duplication endpoint
                Route::post('/duplicate/{id}', function ($id) {
                    return response()->json(['status' => 'success']);
                })->name('duplicate');
                
            });
    });