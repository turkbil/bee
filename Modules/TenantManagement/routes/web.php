<?php
// Modules/TenantManagement/routes/web.php

use Illuminate\Support\Facades\Route;
use Modules\TenantManagement\App\Http\Controllers\TenantManagementController;

Route::middleware(['web', 'auth'])
    ->prefix('admin/tenant')
    ->name('admin.tenant.')
    ->group(function () {
        Route::get('/', [TenantManagementController::class, 'index'])->name('index');
        Route::post('/manage', [TenantManagementController::class, 'manage'])->name('manage');
        Route::delete('/{id}', [TenantManagementController::class, 'destroy'])->name('destroy');
    });
