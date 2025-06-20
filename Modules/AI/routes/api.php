<?php

use Illuminate\Support\Facades\Route;
// use Modules\AI\App\Http\Controllers\AIController;

// API routes commented out until controller is ready
// Route::middleware(['auth:sanctum'])
//     ->prefix('v1')
//     ->name('api.')
//     ->group(function () {
//         Route::prefix('ai')
//             ->name('ai.')
//             ->group(function () {
//                 Route::post('/generate', [AIController::class, 'generate'])->name('generate');
//                 Route::get('/conversations', [AIController::class, 'getConversations'])->name('conversations');
//                 Route::post('/conversation', [AIController::class, 'createConversation'])->name('conversation.create');
//                 Route::get('/conversation/{id}', [AIController::class, 'getConversation'])->name('conversation.get');
//                 Route::delete('/conversation/{id}', [AIController::class, 'deleteConversation'])->name('conversation.delete');
//             });
//     });