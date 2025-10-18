<?php

use Illuminate\Support\Facades\Route;
use Modules\Search\App\Http\Controllers\SearchPageController;
use Modules\Search\App\Http\Livewire\Admin\SearchAnalyticsComponent;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Frontend Search Routes (Tenant-aware)
Route::middleware(['web', 'tenant'])->group(function () {
    // Main search page
    Route::get('/search/{query?}', [SearchPageController::class, 'show'])
        ->name('search.show');

    // Popular searches (SEO)
    Route::get('/populer-aramalar', [SearchPageController::class, 'tags'])
        ->name('search.tags');
});

// Admin Routes (Tenant-aware + Auth)
Route::middleware(['web', 'auth', 'tenant'])->prefix('admin')->group(function () {
    // Analytics - Livewire component view'dan render edilecek
    Route::get('/search/analytics', function () {
        return view('search::admin.analytics');
    })->name('admin.search.analytics')->middleware('permission:search.view_analytics');
});
