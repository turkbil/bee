<?php

use Illuminate\Support\Facades\Route;
use Modules\TenantManagement\Http\Livewire\QueueMonitoringComponent;

/*
|--------------------------------------------------------------------------
| TenantManagement Web Routes
|--------------------------------------------------------------------------
|
| Tenant yönetimi için web rotaları
|
*/

// Admin rotaları (middleware grubu admin.php'de tanımlı)
// Queue Monitoring Routes
Route::middleware(['web', 'auth'])->group(function () {
    Route::prefix('admin/tenantmanagement')->name('admin.tenantmanagement.')->group(function () {
        
        // Queue Monitoring
        Route::get('/queue-monitoring', function () {
            return view('tenantmanagement::queue.monitoring');
        })->name('queue-monitoring');
            
        // Failed Jobs Management
        Route::get('/failed-jobs', function () {
            return view('tenantmanagement::queue.failed-jobs');
        })->name('failed-jobs');
        
        // Job Statistics
        Route::get('/job-statistics', function () {
            return view('tenantmanagement::queue.job-statistics');
        })->name('job-statistics');
        
    });
});