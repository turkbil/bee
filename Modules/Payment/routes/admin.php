<?php
// Modules/Payment/routes/admin.php
use Illuminate\Support\Facades\Route;
use Modules\Payment\App\Http\Livewire\Admin\PaymentComponent;
use Modules\Payment\App\Http\Livewire\Admin\PaymentManageComponent;
use Modules\Payment\App\Http\Livewire\Admin\PaymentCategoryComponent;
use Modules\Payment\App\Http\Livewire\Admin\PaymentCategoryManageComponent;

// Admin rotaları
Route::middleware(['admin', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::prefix('payment')
            ->name('payment.')
            ->group(function () {
                Route::get('/', PaymentComponent::class)
                    ->middleware('module.permission:payment,view')
                    ->name('index');

                Route::get('/manage/{id?}', PaymentManageComponent::class)
                    ->middleware('module.permission:payment,update')
                    ->name('manage');

                // Kategori route'ları
                Route::prefix('category')
                    ->name('category.')
                    ->group(function () {
                        Route::get('/', PaymentCategoryComponent::class)
                            ->middleware('module.permission:payment,view')
                            ->name('index');

                        Route::get('/manage/{id?}', PaymentCategoryManageComponent::class)
                            ->middleware('module.permission:payment,update')
                            ->name('manage');
                    });
            });
    });
