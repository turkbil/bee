<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// API v1 routes
Route::prefix('v1')->group(function () {
    
    // API v1 base endpoint
    Route::get('/', function () {
        return response()->json([
            'message' => 'API v1 is running',
            'version' => '1.0.0',
            'timestamp' => now()->toISOString(),
            'endpoints' => [
                'auth' => '/api/v1/auth/*',
                'profile' => '/api/v1/profile',
                'tokens' => '/api/v1/tokens',
                'tenant' => '/api/v1/tenant'
            ]
        ]);
    });
    
    // Auth routes (public)
    Route::post('/auth/login', [App\Http\Controllers\Api\AuthController::class, 'login']);
    Route::post('/auth/register', [App\Http\Controllers\Api\AuthController::class, 'register']);
    
    // Protected routes
    Route::middleware(['auth:sanctum'])->group(function () {
        
        // Auth management
        Route::get('/auth/me', [App\Http\Controllers\Api\AuthController::class, 'me']);
        Route::post('/auth/logout', [App\Http\Controllers\Api\AuthController::class, 'logout']);
        
        // User profile management
        Route::get('/profile', [App\Http\Controllers\Api\UserProfileController::class, 'getProfile']);
        Route::put('/profile', [App\Http\Controllers\Api\UserProfileController::class, 'updateProfile']);
        Route::post('/profile/change-password', [App\Http\Controllers\Api\UserProfileController::class, 'changePassword']);
        Route::post('/profile/avatar', [App\Http\Controllers\Api\UserProfileController::class, 'uploadAvatar']);
        Route::delete('/profile/avatar', [App\Http\Controllers\Api\UserProfileController::class, 'deleteAvatar']);
        
        // Token management
        Route::get('/tokens', [App\Http\Controllers\Api\TokenController::class, 'getTokens']);
        Route::post('/tokens/refresh', [App\Http\Controllers\Api\TokenController::class, 'refreshToken']);
        Route::delete('/tokens/{token_id}', [App\Http\Controllers\Api\TokenController::class, 'revokeToken']);
        Route::delete('/tokens', [App\Http\Controllers\Api\TokenController::class, 'revokeAllTokens']);
        Route::get('/tokens/current', [App\Http\Controllers\Api\TokenController::class, 'getTokenInfo']);
        Route::get('/tokens/validate', [App\Http\Controllers\Api\TokenController::class, 'validateToken']);
        
        // Tenant info
        Route::get('/tenant', [App\Http\Controllers\Api\TenantController::class, 'getCurrentTenant']);
        Route::get('/tenant/details', [App\Http\Controllers\Api\TenantController::class, 'getTenantDetails']);
        
    });
    
});