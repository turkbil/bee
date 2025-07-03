<?php
// Modules/AI/routes/web.php
use Illuminate\Support\Facades\Route;
use Modules\AI\App\Http\Controllers\Front\AIController;
use Modules\AI\App\Http\Controllers\AIController as MainAIController;

// Ön yüz rotaları - Sadece authenticated kullanıcılar için

Route::prefix('ai')
    ->middleware(['web', 'auth'])
    ->group(function () {
        Route::get('/', [AIController::class, 'index'])->name('ai.index');
        Route::get('/chat/{id?}', [AIController::class, 'chat'])->name('ai.chat');
        Route::post('/ask', [AIController::class, 'ask'])->name('ai.ask');
        Route::post('/chat/create', [AIController::class, 'createConversation'])->name('ai.chat.create');
        Route::delete('/chat/{id}', [AIController::class, 'deleteConversation'])->name('ai.chat.delete');
        
        // Stream ve reset endpointleri - bunlar hem public hem admin tarafında kullanılabilir
        Route::get('/stream', [MainAIController::class, 'streamResponse'])->name('ai.stream');
        Route::post('/reset', [MainAIController::class, 'resetConversation'])->name('ai.reset');
    });