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

// Admin rotaları
Route::middleware(['admin', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Shop Ana Dashboard
        Route::get('/shop', ShopComponent::class)
            ->middleware('module.permission:shop,view')
            ->name('shop.index');

        Route::prefix('shop/products')
            ->name('shop.products.')
            ->group(function () {
                Route::get('/', ShopProductComponent::class)
                    ->middleware('module.permission:shop,view')
                    ->name('index');

                Route::get('/create', ShopProductManageComponent::class)
                    ->middleware('module.permission:shop,create')
                    ->name('create');

                Route::get('/{id}/edit', ShopProductManageComponent::class)
                    ->middleware('module.permission:shop,update')
                    ->name('edit');
            });

        Route::prefix('shop/categories')
            ->name('shop.categories.')
            ->group(function () {
                Route::get('/', ShopCategoryComponent::class)
                    ->middleware('module.permission:shop,view')
                    ->name('index');

                Route::get('/create', ShopCategoryManageComponent::class)
                    ->middleware('module.permission:shop,create')
                    ->name('create');

                Route::get('/{id}/edit', ShopCategoryManageComponent::class)
                    ->middleware('module.permission:shop,update')
                    ->name('edit');
            });

        Route::prefix('shop/brands')
            ->name('shop.brands.')
            ->group(function () {
                Route::get('/', ShopBrandComponent::class)
                    ->middleware('module.permission:shop,view')
                    ->name('index');

                Route::get('/create', ShopBrandManageComponent::class)
                    ->middleware('module.permission:shop,create')
                    ->name('create');

                Route::get('/{id}/edit', ShopBrandManageComponent::class)
                    ->middleware('module.permission:shop,update')
                    ->name('edit');
            });
    });
