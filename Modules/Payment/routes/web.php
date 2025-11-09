<?php
// Modules/Payment/routes/web.php

use Illuminate\Support\Facades\Route;
use Modules\Payment\App\Http\Controllers\PaymentCallbackController;

// ⚠️  FRONTEND ROUTES DynamicRouteService TARAFINDAN YÖNETİLİYOR
// Bu dosyada sadece özel route'lar (homepayment gibi) tanımlanmalı
// Normal content route'ları (index, show) DynamicRouteService'den geliyor

// Ana sayfa route'u routes/web.php'de tanımlı

// PayTR Callback Routes (public, CSRF exempt)
Route::prefix('payment/callback')->name('payment.callback.')->group(function () {
    Route::post('paytr', [PaymentCallbackController::class, 'paytr'])->name('paytr');
    Route::get('success/{payment}', [PaymentCallbackController::class, 'success'])->name('success');
    Route::get('fail/{payment}', [PaymentCallbackController::class, 'fail'])->name('fail');
});
