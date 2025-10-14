<?php

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

// Tenant'a özel route'lar olduğunda buraya ekleyebilirsiniz
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