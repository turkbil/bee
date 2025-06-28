<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\InitializeTenancy;

// Cache test sayfası - bu sayfa cache'lenecek
Route::middleware([InitializeTenancy::class, \Modules\LanguageManagement\app\Http\Middleware\SetLocaleMiddleware::class . ':site'])
    ->get('/cache-test-page', function () {
        $currentTime = now()->format('Y-m-d H:i:s');
        
        return view('debug.cache-test-page', [
            'timestamp' => $currentTime,
            'auth_status' => auth()->check() ? 'AUTHENTICATED' : 'GUEST',
            'user_name' => auth()->user()?->name ?? 'N/A',
            'locale' => app()->getLocale(),
            'random_number' => rand(1000, 9999)
        ]);
    })->name('cache.test.page');