<?php
// Modules/AI/routes/admin.php
use Illuminate\Support\Facades\Route;
use Modules\AI\App\Http\Livewire\Admin\ChatPanel;
use Modules\AI\App\Http\Livewire\Admin\SettingsPanel;
use Modules\AI\App\Http\Controllers\Admin\AIController;
use Modules\AI\App\Http\Controllers\Admin\SettingsController;
use Modules\AI\App\Http\Controllers\Admin\ConversationController;

// Admin rotaları
Route::middleware(['web', 'auth'])
    ->group(function () {
        Route::prefix('admin/ai')
            ->name('admin.ai.')
            ->group(function () {
                Route::get('/', ChatPanel::class)
                    ->middleware('module.permission:ai,view')
                    ->name('index');
                
                Route::get('/settings', SettingsPanel::class)
                    ->middleware('module.permission:ai,update')
                    ->name('settings');
                
                Route::get('/conversations', [ConversationController::class, 'index'])
                    ->middleware('module.permission:ai,view')
                    ->name('conversations.index');
                
                Route::get('/conversations/{id}', [ConversationController::class, 'show'])
                    ->middleware('module.permission:ai,view')
                    ->name('conversations.show');
                
                Route::delete('/conversations/{id}', [ConversationController::class, 'delete'])
                    ->middleware('module.permission:ai,delete')
                    ->name('conversations.delete');
                
                // API Endpoints
                Route::post('/generate', [AIController::class, 'generate'])
                    ->middleware('module.permission:ai,view')
                    ->name('generate');
                
                Route::post('/settings/update', [SettingsController::class, 'update'])
                    ->middleware('module.permission:ai,update')
                    ->name('settings.update');
                
                Route::post('/settings/test-connection', [SettingsController::class, 'testConnection'])
                    ->middleware('module.permission:ai,update')
                    ->name('settings.test-connection');
                
                // Prompt güncelleme için özel API endpoint
                Route::post('/update-conversation-prompt', [AIController::class, 'updateConversationPrompt'])
                    ->middleware('module.permission:ai,view')
                    ->name('update-conversation-prompt');
            });
    });