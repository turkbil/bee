<?php
// Modules/AI/routes/web.php
use Illuminate\Support\Facades\Route;
use Modules\AI\App\Http\Controllers\Front\AIController;
use Modules\AI\App\Http\Controllers\AIController as MainAIController;

// Ön yüz rotaları
Route::middleware(['web', 'auth'])
    ->group(function () {
        Route::get('/ai', [AIController::class, 'index'])->name('ai.index');
        Route::get('/ai/chat/{id?}', [AIController::class, 'chat'])->name('ai.chat');
        Route::post('/ai/ask', [AIController::class, 'ask'])->name('ai.ask');
        Route::post('/ai/chat/create', [AIController::class, 'createConversation'])->name('ai.chat.create');
        Route::delete('/ai/chat/{id}', [AIController::class, 'deleteConversation'])->name('ai.chat.delete');
        
        // Stream ve reset endpointleri - bunlar hem public hem admin tarafında kullanılabilir
        Route::get('/ai/stream', [MainAIController::class, 'streamResponse'])->name('ai.stream');
        Route::post('/ai/reset', [MainAIController::class, 'resetConversation'])->name('ai.reset');
    });