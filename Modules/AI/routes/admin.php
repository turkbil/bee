<?php
// Modules/AI/routes/admin.php
use Illuminate\Support\Facades\Route;
use Modules\AI\App\Http\Livewire\Admin\ChatPanel;
use Modules\AI\App\Http\Livewire\Admin\SettingsPanel;
use Modules\AI\App\Http\Controllers\Admin\AIChatController;
use Modules\AI\App\Http\Controllers\Admin\SettingsController;
use Modules\AI\App\Http\Controllers\Admin\ConversationController;
use Modules\AI\App\Http\Controllers\Admin\TokenManagementController;
use Modules\AI\App\Http\Controllers\Admin\TokenPackageController;
use Modules\AI\App\Http\Controllers\Admin\TokenController;
use Modules\AI\App\Http\Livewire\Admin\TokenManagement;
use Modules\AI\App\Http\Livewire\Admin\TokenPackageManagement;

// Admin rotaları
Route::middleware(['admin', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::prefix('ai')
            ->name('ai.')
            ->group(function () {
                Route::get('/', [AIChatController::class, 'index'])
                    ->middleware('module.permission:ai,view')
                    ->name('index');
                
                // Settings ana sayfası (API)
                Route::get('/settings', [SettingsController::class, 'api'])
                    ->middleware('module.permission:ai,update')
                    ->name('settings');
                    
                // Settings alt sayfaları
                Route::get('/settings/api', [SettingsController::class, 'api'])
                    ->middleware('module.permission:ai,update')
                    ->name('settings.api');
                    
                Route::get('/settings/limits', [SettingsController::class, 'limits'])
                    ->middleware('module.permission:ai,update')
                    ->name('settings.limits');
                    
                Route::get('/settings/prompts', [SettingsController::class, 'prompts'])
                    ->middleware('module.permission:ai,update')
                    ->name('settings.prompts');
                    
                Route::get('/settings/prompts/manage/{id?}', [SettingsController::class, 'managePrompt'])
                    ->middleware('module.permission:ai,update')
                    ->name('settings.prompts.manage');
                    
                Route::get('/settings/general', [SettingsController::class, 'general'])
                    ->middleware('module.permission:ai,update')
                    ->name('settings.general');
                
                Route::get('/conversations', [ConversationController::class, 'index'])
                    ->middleware('module.permission:ai,view')
                    ->name('conversations.index');
                
                Route::get('/conversations/archived', [ConversationController::class, 'archived'])
                    ->middleware('module.permission:ai,view')
                    ->name('conversations.archived');
                
                Route::get('/conversations/{id}', [ConversationController::class, 'show'])
                    ->middleware('module.permission:ai,view')
                    ->name('conversations.show');
                
                Route::delete('/conversations/{id}', [ConversationController::class, 'delete'])
                    ->middleware('module.permission:ai,delete')
                    ->name('conversations.delete');

                Route::post('/conversations/{id}/archive', [ConversationController::class, 'archive'])
                    ->middleware('module.permission:ai,update')
                    ->name('conversations.archive');

                Route::post('/conversations/{id}/unarchive', [ConversationController::class, 'unarchive'])
                    ->middleware('module.permission:ai,update')
                    ->name('conversations.unarchive');

                Route::post('/conversations/bulk-action', [ConversationController::class, 'bulkAction'])
                    ->middleware('module.permission:ai,delete')
                    ->name('conversations.bulk-action');
                
                // API Endpoints
                Route::post('/generate', [AIChatController::class, 'sendMessage'])
                    ->middleware('module.permission:ai,view')
                    ->name('generate');
                
                // Settings POST routes
                Route::post('/settings/api/update', [SettingsController::class, 'updateApi'])
                    ->middleware('module.permission:ai,update')
                    ->name('settings.api.update');
                    
                Route::post('/settings/limits/update', [SettingsController::class, 'updateLimits'])
                    ->middleware('module.permission:ai,update')
                    ->name('settings.limits.update');
                    
                Route::post('/settings/general/update', [SettingsController::class, 'updateGeneral'])
                    ->middleware('module.permission:ai,update')
                    ->name('settings.general.update');
                
                // Prompt routes
                Route::post('/settings/prompts/store', [SettingsController::class, 'storePrompt'])
                    ->middleware('module.permission:ai,create')
                    ->name('settings.prompts.store');
                    
                Route::get('/prompts/{id}', [SettingsController::class, 'getPrompt'])
                    ->middleware('module.permission:ai,view')
                    ->name('prompts.get');
                    
                Route::put('/settings/prompts/{id}', [SettingsController::class, 'updatePrompt'])
                    ->middleware('module.permission:ai,update')
                    ->name('settings.prompts.update');
                    
                Route::delete('/prompts/{id}', [SettingsController::class, 'deletePrompt'])
                    ->middleware('module.permission:ai,delete')
                    ->name('prompts.delete');
                    
                Route::post('/prompts/{id}/default', [SettingsController::class, 'makeDefaultPrompt'])
                    ->middleware('module.permission:ai,update')
                    ->name('prompts.make-default');
                    
                Route::post('/settings/prompts/update-common', [SettingsController::class, 'updateCommonPrompt'])
                    ->middleware('module.permission:ai,update')
                    ->name('settings.prompts.update-common');
                
                Route::post('/settings/update', [SettingsController::class, 'update'])
                    ->middleware('module.permission:ai,update')
                    ->name('settings.update');
                
                Route::post('/settings/test-connection', [SettingsController::class, 'testConnection'])
                    ->middleware('module.permission:ai,update')
                    ->name('settings.test-connection');
                
                // Prompt güncelleme için özel API endpoint
                Route::post('/update-conversation-prompt', [AIChatController::class, 'updateConversationPrompt'])
                    ->middleware('module.permission:ai,view')
                    ->name('update-conversation-prompt');
                    
                // AI Features Management System
                Route::get('/features', [\Modules\AI\App\Http\Controllers\Admin\AIFeaturesController::class, 'index'])
                    ->middleware('module.permission:ai,view')
                    ->name('features.index');
                    
                Route::get('/features/manage/{id?}', \Modules\AI\App\Http\Livewire\Admin\AIFeatureManageComponent::class)
                    ->middleware('module.permission:ai,view')
                    ->name('features.manage');
                    
                Route::get('/features/{feature}', [\Modules\AI\App\Http\Controllers\Admin\AIFeaturesController::class, 'show'])
                    ->middleware('module.permission:ai,view')
                    ->name('features.show');
                
                // AI Features ek route'lar
                Route::post('/features/bulk-status', [\Modules\AI\App\Http\Controllers\Admin\AIFeaturesController::class, 'bulkStatusUpdate'])
                    ->middleware('module.permission:ai,update')
                    ->name('features.bulk-status');
                Route::post('/features/update-order', [\Modules\AI\App\Http\Controllers\Admin\AIFeaturesController::class, 'updateOrder'])
                    ->middleware('module.permission:ai,update')
                    ->name('features.update-order');
                Route::post('/features/{feature}/duplicate', [\Modules\AI\App\Http\Controllers\Admin\AIFeaturesController::class, 'duplicate'])
                    ->middleware('module.permission:ai,create')
                    ->name('features.duplicate');
                    
                // AI Kullanım Örnekleri Test Sayfası (Yazılımcılar için)
                Route::get('/examples', [\Modules\AI\App\Http\Controllers\Admin\AIFeaturesController::class, 'examples'])
                    ->middleware('module.permission:ai,view')
                    ->name('examples');
                    
                // AI Skills Showcase (Adminler için)
                Route::get('/prowess', [\Modules\AI\App\Http\Controllers\Admin\AIFeaturesController::class, 'prowess'])
                    ->middleware('module.permission:ai,view')
                    ->name('prowess');
                
                // AI Features Test API
                Route::post('/test-feature', function(\Illuminate\Http\Request $request) {
                    \Log::info('AI Feature Test API çağrıldı', $request->all());
                    $controller = app()->make(\Modules\AI\App\Http\Controllers\Admin\AIFeaturesController::class);
                    return $controller->testFeature($request);
                })
                    ->middleware('module.permission:ai,view')
                    ->name('test-feature');
                
                // Token Management Routes (Root Admin Only)
                Route::prefix('tokens')
                    ->name('tokens.')
                    ->middleware('role:root')
                    ->group(function () {
                        Route::get('/', TokenManagement::class)
                            ->name('index');
                        
                        Route::get('/tenant/{tenant}', [TokenManagementController::class, 'show'])
                            ->name('show');
                        
                        Route::put('/tenant/{tenant}/settings', [TokenManagementController::class, 'updateTenantSettings'])
                            ->name('update-settings');
                        
                        Route::post('/tenant/{tenant}/toggle-ai', [TokenManagementController::class, 'toggleAI'])
                            ->name('toggle-ai');
                        
                        Route::post('/tenant/{tenant}/adjust', [TokenManagementController::class, 'adjustTokens'])
                            ->name('adjust');
                        
                        // Livewire Token Paket Yönetimi  
                        Route::get('/packages', TokenPackageManagement::class)
                            ->name('packages');
                        
                        Route::get('/purchases', [TokenManagementController::class, 'allPurchases'])
                            ->name('purchases');
                        
                        Route::get('/usage-stats', [TokenManagementController::class, 'allUsageStats'])
                            ->name('usage-stats');
                        
                        Route::get('/statistics/overview', [TokenManagementController::class, 'statisticsOverview'])
                            ->name('statistics.overview');
                        
                        Route::get('/tenant/{tenantId}/statistics', [TokenManagementController::class, 'tenantStatistics'])
                            ->name('tenant-statistics');
                        
                        // Package management routes
                        Route::post('/packages', [TokenManagementController::class, 'storePackage'])
                            ->name('packages.store');
                        
                        Route::get('/packages/{package}/edit', [TokenManagementController::class, 'editPackage'])
                            ->name('packages.edit');
                        
                        Route::put('/packages/{package}', [TokenManagementController::class, 'updatePackage'])
                            ->name('packages.update');
                        
                        Route::delete('/packages/{package}', [TokenManagementController::class, 'destroyPackage'])
                            ->name('packages.destroy');
                        
                        Route::post('/packages/update-order', [TokenManagementController::class, 'updatePackageOrder'])
                            ->name('packages.update-order');
                    });
            });
    });