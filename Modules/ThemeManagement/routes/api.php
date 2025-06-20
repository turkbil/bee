<?php

use Illuminate\Support\Facades\Route;
use Modules\ThemeManagement\App\Http\Controllers\ThemeManagementController;

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
//     Route::apiResource('thememanagement', ThemeManagementController::class)->names('thememanagement');
// });
