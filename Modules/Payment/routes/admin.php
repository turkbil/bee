<?php
// Modules/Payment/routes/admin.php
use Illuminate\Support\Facades\Route;
use Modules\Payment\App\Http\Livewire\Admin\PaymentMethodsComponent;
use Modules\Payment\App\Http\Livewire\Admin\PaymentMethodManageComponent;
use Modules\Payment\App\Http\Livewire\Admin\PaymentsComponent;
use Modules\Payment\App\Http\Livewire\Admin\PaymentDetailComponent;

// Admin rotaları - Payment Management
Route::middleware(['admin', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::prefix('payment')
            ->name('payment.')
            ->group(function () {
                // Payment Methods (PayTR, Stripe vb.)
                Route::prefix('methods')
                    ->name('methods.')
                    ->group(function () {
                        Route::get('/', PaymentMethodsComponent::class)
                            ->middleware('module.permission:payment,view')
                            ->name('index');

                        Route::get('/manage/{id?}', PaymentMethodManageComponent::class)
                            ->middleware('module.permission:payment,update')
                            ->name('manage');
                    });

                // Payments (Ödeme kayıtları)
                Route::get('/', PaymentsComponent::class)
                    ->middleware('module.permission:payment,view')
                    ->name('index');

                Route::get('/{id}', PaymentDetailComponent::class)
                    ->middleware('module.permission:payment,view')
                    ->name('detail');
            });
    });
