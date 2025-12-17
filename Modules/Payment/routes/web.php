<?php
// Modules/Payment/routes/web.php

use Illuminate\Support\Facades\Route;
use Modules\Payment\App\Http\Controllers\PayTRCallbackController;
use Modules\Payment\App\Http\Controllers\PaymentPageController;
use Modules\Payment\App\Http\Controllers\BankTransferPageController;

// ⚠️  FRONTEND ROUTES DynamicRouteService TARAFINDAN YÖNETİLİYOR
// Bu dosyada sadece özel route'lar (homepayment gibi) tanımlanmalı
// Normal content route'ları (index, show) DynamicRouteService'den geliyor

// Ana sayfa route'u routes/web.php'de tanımlı

// PayTR Callback Routes (public, CSRF exempt, no auth required)
// NOT: Bu route'lar için VerifyCsrfToken middleware'de exception eklenmeli!
Route::prefix('payment/callback')->name('payment.callback.')->group(function () {
    Route::post('paytr', [PayTRCallbackController::class, 'handle'])->name('paytr');

    // GET isteğini sessizce reddet (güvenlik - stack trace gösterme)
    Route::get('paytr', function () {
        return response('Not Found', 404);
    });
});

// Payment Page & Bank Transfer Routes - 'web' middleware tenant context için ZORUNLU!
Route::middleware(['web'])->group(function () {
    // Payment Page (Frontend - Controller based)
    Route::get('/payment/{orderNumber}', [PaymentPageController::class, 'show'])->name('payment.page');

    // Bank Transfer (Havale/EFT) Routes
    Route::get('/payment/{orderNumber}/bank-transfer', [BankTransferPageController::class, 'show'])->name('payment.bank-transfer');
    Route::post('/payment/{orderNumber}/bank-transfer', [BankTransferPageController::class, 'confirm'])->name('payment.bank-transfer.confirm');
    Route::get('/payment/{orderNumber}/bank-transfer/success', [BankTransferPageController::class, 'success'])->name('payment.bank-transfer.success');
});
