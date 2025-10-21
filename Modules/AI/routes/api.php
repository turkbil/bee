<?php

use Illuminate\Support\Facades\Route;
use Modules\AI\App\Http\Controllers\Api\PublicAIController;
use App\Http\Middleware\InitializeTenancy;

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
| ⚠️ TENANT CONTEXT: Tüm route'lar InitializeTenancy middleware kullanır
|
*/

Route::prefix('ai/v1')
    ->name('ai.api.v1.')
    ->middleware([InitializeTenancy::class]) // ✅ Tenant context for tenant('id') - Models use central DB explicitly
    ->group(function () {
    
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

    // 🛍️ Shop Assistant Endpoints (NO rate limiting, NO credit cost)
    Route::post('/shop-assistant/chat', [PublicAIController::class, 'shopAssistantChat'])
        ->name('shop-assistant.chat');

    Route::get('/shop-assistant/history', [PublicAIController::class, 'getConversationHistory'])
        ->name('shop-assistant.history');

    // 🎨 Product Placeholder Endpoint (Cached AI-generated conversations)
    Route::get('/product-placeholder/{productId}', [PublicAIController::class, 'getProductPlaceholder'])
        ->name('product-placeholder');

    // 🔗 Link Resolver Endpoint (Convert [LINK:module:type:id] to URL)
    Route::get('/resolve-link/{module}/{type}/{id}', [PublicAIController::class, 'resolveLink'])
        ->name('resolve-link')
        ->middleware(['throttle:120,1']); // 120 requests per minute

    // 🗑️ Delete Conversation (Admin/Testing - NO AUTH for now)
    Route::delete('/conversation/{conversationId}', [PublicAIController::class, 'deleteConversation'])
        ->name('conversation.delete')
        ->middleware(['throttle:10,1']); // 10 deletions per minute

    // 📚 Knowledge Base Endpoints (Public access, cached)
    Route::get('/knowledge-base', [PublicAIController::class, 'getKnowledgeBase'])
        ->name('knowledge-base.list')
        ->middleware(['throttle:60,1']); // 60 requests per minute

    Route::get('/knowledge-base/{questionId}', [PublicAIController::class, 'answerKnowledgeQuestion'])
        ->name('knowledge-base.answer')
        ->middleware(['throttle:30,1']); // 30 requests per minute

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

// ✨ PHASE 3: Model Credit Rate Management API - MOVED TO web.php for CSRF support