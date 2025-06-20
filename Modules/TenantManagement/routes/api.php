<?php

use Illuminate\Support\Facades\Route;
use Modules\TenantManagement\App\Http\Controllers\TenantManagementController;

/*
 *--------------------------------------------------------------------------
 * API Routes
 *--------------------------------------------------------------------------
 *
 * Here is where you can register API routes for your application. These
 * routes are loaded by the RouteServiceProvider within a group which
 * is assigned the "api" middleware group. Enjoy building your API!
 *
*/

// API routes geçici olarak deaktif - controller eksik olduğu için
// Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
//     Route::apiResource('tenantmanagement', TenantManagementController::class)->names('tenantmanagement');
//     
//     // Ek tenant yönetim route'ları
//     Route::patch('tenantmanagement/{id}/toggle-status', [TenantManagementController::class, 'toggleStatus'])
//          ->name('tenantmanagement.toggle-status');
// });
