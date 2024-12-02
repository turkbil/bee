<?php // Modules/TenantManagement/routes/web.php

use Illuminate\Support\Facades\Route;
use Modules\TenantManagement\App\Http\Controllers\TenantManagementController;

Route::middleware(['web', 'auth'])
    ->prefix('admin/tenant')
    ->name('admin.tenant.')
    ->group(function () {
        Route::get('/', [TenantManagementController::class, 'index'])->name('index');
        Route::post('/manage', [TenantManagementController::class, 'manage'])->name('manage');
        Route::delete('/{id}', [TenantManagementController::class, 'destroy'])->name('destroy');
        Route::post('/add-domain', [TenantManagementController::class, 'addDomain'])->name('addDomain');
        Route::delete('/delete-domain/{id}', [TenantManagementController::class, 'deleteDomain'])->name('deleteDomain');
        Route::post('/update-domain/{id}', [TenantManagementController::class, 'updateDomain'])->name('updateDomain');
        Route::get('/get-domains/{tenantId}', [TenantManagementController::class, 'getDomains'])->name('getDomains');
    });
