<?php
// Modules/AI/routes/admin.php
use Illuminate\Support\Facades\Route;
use Modules\AI\App\Http\Livewire\Admin\ChatPanel;
use Modules\AI\App\Http\Livewire\Admin\SettingsPanel;
use Modules\AI\App\Http\Livewire\Admin\ModalChat;
use Modules\AI\App\Http\Controllers\Admin\AIController;
use Modules\AI\App\Http\Controllers\Admin\SettingsController;
use Modules\AI\App\Http\Controllers\Admin\ConversationController;

// Admin rotaları
Route::middleware(['web', 'auth', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::prefix('ai')
            ->name('ai.')
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
                
                // API Endpoints
                Route::post('/generate', [AIController::class, 'generate'])
                    ->middleware('module.permission:ai,view')
                    ->name('generate');
                
                Route::post('/settings/update', [SettingsController::class, 'update'])
                    ->middleware('module.permission:ai,update')
                    ->name('settings.update');
            });
    });