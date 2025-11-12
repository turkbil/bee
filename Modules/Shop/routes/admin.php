<?php
// Modules/Shop/routes/admin.php
use Illuminate\Support\Facades\Route;
use Modules\Shop\App\Http\Livewire\Admin\ShopProductComponent;
use Modules\Shop\App\Http\Livewire\Admin\ShopProductManageComponent;
use Modules\Shop\App\Http\Livewire\Admin\ShopCategoryComponent;
use Modules\Shop\App\Http\Livewire\Admin\ShopCategoryManageComponent;
use Modules\Shop\App\Http\Livewire\Admin\ShopBrandComponent;
use Modules\Shop\App\Http\Livewire\Admin\ShopBrandManageComponent;
use Modules\Shop\App\Http\Livewire\Admin\HomepageProductsComponent;
use Modules\Shop\App\Http\Livewire\Admin\ShopFieldTemplateComponent;
use Modules\Shop\App\Http\Livewire\Admin\ShopFieldTemplateManageComponent;
use Modules\Shop\App\Http\Livewire\Admin\ShopCurrencyComponent;
use Modules\Shop\App\Http\Livewire\Admin\ShopCurrencyManageComponent;

// Admin rotaları
Route::middleware(['admin', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::prefix('shop')
            ->name('shop.')
            ->group(function () {
                // Ana Ürün Listesi
                Route::get('/', ShopProductComponent::class)
                    ->middleware('module.permission:shop,view')
                    ->name('index');

                // Ürün Yönetimi
                Route::get('/manage/{id?}', ShopProductManageComponent::class)
                    ->middleware('module.permission:shop,update')
                    ->name('manage');

                // Anasayfa Ürünleri Sıralama
                Route::get('/homepage-products', HomepageProductsComponent::class)
                    ->middleware('module.permission:shop,update')
                    ->name('homepage-products');

                // Kategori Yönetimi
                Route::prefix('categories')
                    ->name('categories.')
                    ->group(function () {
                        Route::get('/', ShopCategoryComponent::class)
                            ->middleware('module.permission:shop,view')
                            ->name('index');

                        Route::get('/manage/{id?}', ShopCategoryManageComponent::class)
                            ->middleware('module.permission:shop,update')
                            ->name('manage');
                    });

                // Marka Yönetimi
                Route::prefix('brands')
                    ->name('brands.')
                    ->group(function () {
                        Route::get('/', ShopBrandComponent::class)
                            ->middleware('module.permission:shop,view')
                            ->name('index');

                        Route::get('/manage/{id?}', ShopBrandManageComponent::class)
                            ->middleware('module.permission:shop,update')
                            ->name('manage');
                    });

                // Field Templates
                Route::prefix('field-templates')
                    ->name('field-templates.')
                    ->group(function () {
                        Route::get('/', ShopFieldTemplateComponent::class)
                            ->middleware('module.permission:shop,view')
                            ->name('index');

                        Route::get('/manage/{id?}', ShopFieldTemplateManageComponent::class)
                            ->middleware('module.permission:shop,update')
                            ->name('manage');
                    });

                // Currency Management
                Route::prefix('currencies')
                    ->name('currencies.')
                    ->group(function () {
                        Route::get('/', ShopCurrencyComponent::class)
                            ->middleware('module.permission:shop,view')
                            ->name('index');

                        Route::get('/manage/{id?}', ShopCurrencyManageComponent::class)
                            ->middleware('module.permission:shop,update')
                            ->name('manage');
                    });
            });
    });
