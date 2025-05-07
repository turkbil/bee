<?php

use Illuminate\Support\Facades\Route;
use Modules\AI\App\Http\Controllers\ApiController;

Route::middleware(['auth:sanctum'])
    ->prefix('v1')
    ->name('api.')
    ->group(function () {
        Route::prefix('ai')
            ->name('ai.')
            ->group(function () {
                Route::post('/generate', [ApiController::class, 'generate'])->name('generate');
                Route::get('/conversations', [ApiController::class, 'getConversations'])->name('conversations');
                Route::post('/conversation', [ApiController::class, 'createConversation'])->name('conversation.create');
                Route::get('/conversation/{id}', [ApiController::class, 'getConversation'])->name('conversation.get');
                Route::delete('/conversation/{id}', [ApiController::class, 'deleteConversation'])->name('conversation.delete');
            });
    });