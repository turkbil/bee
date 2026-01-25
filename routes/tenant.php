<?php

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

// Tenant Storage Serving - OUTSIDE web middleware group (no session needed)
// Storage dosyaları için session/cookie gereksiz - sadece tenancy initialize yeterli
Route::middleware([
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->get('/storage/tenant1001/{path}', [\App\Http\Controllers\TenantStorageController::class, 'serve'])
    ->where('path', '.*')
    ->name('tenant.storage.serve');

// Contact Form API - Outside web middleware (no CSRF needed)
Route::middleware([
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->post('/api/contact', [\App\Http\Controllers\ContactController::class, 'submit'])
    ->name('api.contact.submit');

// Tenant'a özel route'lar (session gerektiren)
Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    // Tenant-spesifik route'lar (genellikle API endpoints gibi)

    // Auto SEO Fill API (Premium tenant'lar için)
    Route::middleware(['throttle:1,1'])->prefix('api')->group(function () {
        Route::post('/auto-seo-fill', [\App\Http\Controllers\Api\AutoSeoFillController::class, 'fill'])
            ->name('api.auto-seo-fill');

        Route::post('/auto-seo-fill/bulk', [\App\Http\Controllers\Api\AutoSeoFillController::class, 'bulkFill'])
            ->name('api.auto-seo-fill.bulk');
    });
});