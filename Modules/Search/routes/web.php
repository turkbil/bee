<?php

use Illuminate\Support\Facades\Route;
use Modules\Search\App\Http\Controllers\SearchPageController;
use Modules\Search\App\Http\Livewire\Admin\SearchAnalyticsComponent;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Frontend Search Routes - MOVED TO routes/web.php (priority over catch-all)
// Search routes must be defined before Page module's catch-all routes
// See: routes/web.php line 43-56

// Admin Routes (Tenant-aware + Auth)
Route::middleware(['web', 'auth', 'tenant'])->prefix('admin')->group(function () {
    // Main index route (navigation için)
    Route::get('/search', function () {
        return redirect()->route('admin.search.analytics');
    })->name('admin.search.index');

    // Analytics - Livewire component view'dan render edilecek
    Route::get('/search/analytics', function () {
        return view('search::admin.analytics');
    })->name('admin.search.analytics')->middleware('permission:search.view_analytics');
});
