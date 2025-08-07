<?php

use Illuminate\Support\Facades\Route;
use Modules\AI\App\Http\Controllers\Api\PublicAIController;

/*
|--------------------------------------------------------------------------
| 🌐 AI V2 Public API Routes - Frontend Integration
|--------------------------------------------------------------------------
|
| Bu dosya AI modülünün public API endpoint'lerini tanımlar:
| - Guest user access (rate limited)
| - Authenticated user access (credit system)
| - Public chat widget support
| - Feature-specific API endpoints
|
*/

Route::prefix('ai/v1')->name('ai.api.v1.')->group(function () {
    
    // 📋 Public Information Endpoints (No authentication required)
    Route::get('/features/public', [PublicAIController::class, 'getPublicFeatures'])
        ->name('features.public')
        ->middleware(['throttle:60,1']); // 60 requests per minute
    
    // 💬 Public Chat Endpoints (Rate limited)
    Route::post('/chat', [PublicAIController::class, 'publicChat'])
        ->name('chat.public');
    
    // 🎯 Public Feature Access (Rate limited)
    Route::post('/feature/{slug}', [PublicAIController::class, 'publicFeature'])
        ->name('feature.public');
    
    // 👤 Authenticated User Endpoints (Requires authentication)
    Route::middleware(['auth:sanctum'])->group(function () {
        
        // 💬 Authenticated User Chat (Full features)
        Route::post('/chat/user', [PublicAIController::class, 'userChat'])
            ->name('chat.user');
        
        // 💰 Credit Management
        Route::get('/credits/balance', [PublicAIController::class, 'getCreditBalance'])
            ->name('credits.balance');
    });
    
    // 🔧 System Status Endpoints (Public but limited)
    Route::get('/status', function () {
        return response()->json([
            'success' => true,
            'data' => [
                'status' => 'operational',
                'version' => '2.0',
                'features_available' => true,
                'public_access' => true,
                'timestamp' => now()->toISOString(),
            ]
        ]);
    })->name('status')->middleware(['throttle:120,1']); // 120 requests per minute
    
});