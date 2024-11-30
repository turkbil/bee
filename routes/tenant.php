<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    Route::get('/', function () {
        return view('welcome', ['tenant_id' => tenant('id')]); // Tenant ID'yi görselle gönderin
    });

    Route::get('/{any}', function ($any) {
        return view('welcome'); // Alt rotalar için de aynı görünümü döndür
    })->where('any', '.*');
});
