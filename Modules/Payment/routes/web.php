<?php
// Modules/Payment/routes/web.php

use Illuminate\Support\Facades\Route;
use Modules\Payment\App\Http\Controllers\PayTRCallbackController;

// ⚠️  FRONTEND ROUTES DynamicRouteService TARAFINDAN YÖNETİLİYOR
// Bu dosyada sadece özel route'lar (homepayment gibi) tanımlanmalı
// Normal content route'ları (index, show) DynamicRouteService'den geliyor

// Ana sayfa route'u routes/web.php'de tanımlı

// PayTR Callback Routes (public, CSRF exempt, no auth required)
// NOT: Bu route'lar için VerifyCsrfToken middleware'de exception eklenmeli!
Route::prefix('payment/callback')->name('payment.callback.')->group(function () {
    Route::post('paytr', [PayTRCallbackController::class, 'handle'])->name('paytr');
});
