<?php
// Modules/Shop/routes/admin.php
use Illuminate\Support\Facades\Route;
use Modules\Shop\App\Http\Livewire\Admin\ShopComponent;
use Modules\Shop\App\Http\Livewire\Admin\ShopProductComponent;
use Modules\Shop\App\Http\Livewire\Admin\ShopProductManageComponent;
use Modules\Shop\App\Http\Livewire\Admin\ShopCategoryComponent;
use Modules\Shop\App\Http\Livewire\Admin\ShopCategoryManageComponent;
use Modules\Shop\App\Http\Livewire\Admin\ShopBrandComponent;
use Modules\Shop\App\Http\Livewire\Admin\ShopBrandManageComponent;
use Modules\Shop\App\Http\Livewire\Admin\HomepageProductsComponent;
use Modules\Shop\App\Http\Controllers\Admin\ShopFieldTemplateController;

// Admin rotaları
Route::middleware(['admin', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Shop Ana Dashboard
        Route::get('/shop', ShopComponent::class)
            ->middleware('module.permission:shop,view')
            ->name('shop.index');

        // Anasayfa Ürünleri Sıralama
        Route::get('/shop/homepage-products', HomepageProductsComponent::class)
            ->middleware('module.permission:shop,update')
            ->name('shop.homepage-products');

        Route::prefix('shop/products')
            ->name('shop.products.')
            ->group(function () {
                Route::get('/', ShopProductComponent::class)
                    ->middleware('module.permission:shop,view')
                    ->name('index');

                Route::get('/manage/{id?}', ShopProductManageComponent::class)
                    ->middleware('module.permission:shop,update')
                    ->name('manage');
            });

        Route::prefix('shop/categories')
            ->name('shop.categories.')
            ->group(function () {
                Route::get('/', ShopCategoryComponent::class)
                    ->middleware('module.permission:shop,view')
                    ->name('index');

                Route::get('/manage/{id?}', ShopCategoryManageComponent::class)
                    ->middleware('module.permission:shop,update')
                    ->name('manage');
            });

        Route::prefix('shop/brands')
            ->name('shop.brands.')
            ->group(function () {
                Route::get('/', ShopBrandComponent::class)
                    ->middleware('module.permission:shop,view')
                    ->name('index');

                Route::get('/manage/{id?}', ShopBrandManageComponent::class)
                    ->middleware('module.permission:shop,update')
                    ->name('manage');
            });

        // Field Templates
        Route::prefix('shop/field-templates')
            ->name('shop.field-templates.')
            ->middleware('module.permission:shop,update')
            ->group(function () {
                Route::get('/', [ShopFieldTemplateController::class, 'index'])->name('index');
                Route::get('/create', [ShopFieldTemplateController::class, 'create'])->name('create');
                Route::post('/', [ShopFieldTemplateController::class, 'store'])->name('store');
                Route::get('/{id}/edit', [ShopFieldTemplateController::class, 'edit'])->name('edit');
                Route::put('/{id}', [ShopFieldTemplateController::class, 'update'])->name('update');
                Route::delete('/{id}', [ShopFieldTemplateController::class, 'destroy'])->name('destroy');
                Route::post('/{id}/toggle-active', [ShopFieldTemplateController::class, 'toggleActive'])->name('toggle-active');
                Route::post('/update-order', [ShopFieldTemplateController::class, 'updateOrder'])->name('update-order');
            });
    });
