<?php
// Modules/AI/routes/admin.php
use Illuminate\Support\Facades\Route;
use Modules\AI\App\Http\Livewire\Admin\Chat\ChatPanel;
use Modules\AI\App\Http\Livewire\Admin\Settings\SettingsPanel;
use Modules\AI\App\Http\Controllers\Admin\Chat\AIChatController;
use Modules\AI\App\Http\Controllers\Admin\Settings\SettingsController;
use Modules\AI\App\Http\Controllers\Admin\Conversations\ConversationController;
use Modules\AI\App\Http\Controllers\Admin\Tokens\TokenManagementController;
use Modules\AI\App\Http\Controllers\Admin\Tokens\TokenController;
use Modules\AI\App\Http\Livewire\Admin\Tokens\TokenManagement;
use Modules\AI\App\Http\Livewire\Admin\Tokens\TokenPackageManagement;

// Admin rotaları
Route::middleware(['admin', 'admin.tenant.select'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::prefix('ai')
            ->name('ai.')
            ->group(function () {
                Route::get('/', ChatPanel::class)
                    ->middleware('module.permission:ai,view')
                    ->name('index');
                
                Route::post('/send-message', [AIChatController::class, 'sendMessage'])
                    ->middleware('module.permission:ai,view')
                    ->name('send-message');
                    
                Route::get('/stream', [AIChatController::class, 'streamResponse'])
                    ->middleware('module.permission:ai,view')
                    ->name('stream');
                
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
                
                Route::get('/settings/providers', [SettingsController::class, 'providers'])
                    ->middleware('module.permission:ai,update')
                    ->name('settings.providers');
                
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
                    
                Route::post('/settings/api/update-priorities', [SettingsController::class, 'updateProviderPriorities'])
                    ->middleware('module.permission:ai,update')
                    ->name('settings.api.update-priorities');
                    
                Route::post('/settings/limits/update', [SettingsController::class, 'updateLimits'])
                    ->middleware('module.permission:ai,update')
                    ->name('settings.limits.update');
                    
                Route::post('/settings/general/update', [SettingsController::class, 'updateGeneral'])
                    ->middleware('module.permission:ai,update')
                    ->name('settings.general.update');
                    
                // Provider routes
                Route::put('/providers/{id}', [SettingsController::class, 'updateProvider'])
                    ->middleware('module.permission:ai,update')
                    ->name('providers.update');
                    
                Route::post('/providers/{id}/test', [SettingsController::class, 'testProvider'])
                    ->middleware('module.permission:ai,update')
                    ->name('providers.test');
                    
                Route::post('/providers/{id}/make-default', [SettingsController::class, 'makeDefaultProvider'])
                    ->middleware('module.permission:ai,update')
                    ->name('providers.make-default');
                
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
                    
                // AI Features Management System - Page Pattern
                Route::get('/features', \Modules\AI\App\Http\Livewire\Admin\Features\AIFeaturesManagement::class)
                    ->middleware('module.permission:ai,view')
                    ->name('features.index');
                    
                // AI Features Dashboard Route
                Route::get('/features/dashboard', [\Modules\AI\App\Http\Controllers\Admin\Settings\SettingsController::class, 'features'])
                    ->middleware('module.permission:ai,view')
                    ->name('features.dashboard');
                    
                // AI Feature Categories Management
                Route::get('/features/categories', \Modules\AI\App\Http\Livewire\Admin\Features\AIFeatureCategoryComponent::class)
                    ->middleware('module.permission:ai,view')
                    ->name('features.categories');
                    
                // Feature Manage Route'ları (Page pattern - create ve edit kaldırıldı)
                Route::get('/features/manage/{id?}', \Modules\AI\App\Http\Livewire\Admin\Features\AIFeatureManageComponent::class)
                    ->middleware('module.permission:ai,view')
                    ->name('features.manage');
                
                // Sıralama güncelleme (AJAX)
                Route::post('/features/update-sort', [\Modules\AI\App\Http\Controllers\Admin\Features\AIFeaturesController::class, 'updateSort'])
                    ->middleware('module.permission:ai,update')
                    ->name('features.update-sort');
                    
                // Durum değiştirme (AJAX)
                Route::post('/features/{id}/toggle-status', [\Modules\AI\App\Http\Controllers\Admin\Features\AIFeaturesController::class, 'toggleStatus'])
                    ->middleware('module.permission:ai,update')
                    ->name('features.toggle-status');
                    
                
                // AI Features ek route'lar (eski sistem uyumluluğu)
                Route::post('/features/bulk-status', [\Modules\AI\App\Http\Controllers\Admin\Features\AIFeaturesController::class, 'bulkStatusUpdate'])
                    ->middleware('module.permission:ai,update')
                    ->name('features.bulk-status');
                Route::post('/features/update-order', [\Modules\AI\App\Http\Controllers\Admin\Features\AIFeaturesController::class, 'updateOrder'])
                    ->middleware('module.permission:ai,update')
                    ->name('features.update-order');
                Route::post('/features/{feature}/duplicate', [\Modules\AI\App\Http\Controllers\Admin\Features\AIFeaturesController::class, 'duplicate'])
                    ->middleware('module.permission:ai,create')
                    ->name('features.duplicate');
                    
                    
                // AI Skills Showcase (Adminler için)
                Route::get('/prowess', [\Modules\AI\App\Http\Controllers\Admin\Features\AIFeaturesController::class, 'prowess'])
                    ->middleware('module.permission:ai,view')
                    ->name('prowess');
                
                // AI Features Test API
                Route::post('/test-feature', function(\Illuminate\Http\Request $request) {
                    \Log::info('AI Feature Test API çağrıldı', $request->all());
                    $controller = app()->make(\Modules\AI\App\Http\Controllers\Admin\Features\AIFeaturesController::class);
                    return $controller->testFeature($request);
                })
                    ->middleware('module.permission:ai,view')
                    ->name('test-feature');
                    
                // Token Stats API (Real-time güncellemeler için)
                Route::get('/token-stats', function() {
                    $tenantId = tenant('id') ?: '1';
                    
                    // Mevcut token verilerini al
                    $remainingTokens = ai_get_token_balance($tenantId);
                    $totalUsed = ai_get_total_used($tenantId);
                    $totalPurchased = ai_get_total_purchased($tenantId);
                    $monthlyUsage = ai_get_monthly_usage($tenantId);
                    $dailyUsage = ai_get_daily_usage($tenantId);
                    
                    // Formatlanmış değerlerle birlikte döndür
                    $tokenStats = [
                        'remaining_tokens' => $remainingTokens,
                        'remaining_tokens_formatted' => ai_format_token_count($remainingTokens),
                        'used_tokens' => $totalUsed,
                        'total_tokens' => $totalPurchased,
                        'monthly_usage' => $monthlyUsage,
                        'monthly_usage_formatted' => ai_format_token_count($monthlyUsage),
                        'daily_usage' => $dailyUsage,
                        'daily_usage_formatted' => ai_format_token_count($dailyUsage),
                        'monthly_limit' => 0, // TODO: Limit sistemi eklenecek
                        'monthly_limit_formatted' => '0',
                        'usage_percentage' => 0
                    ];
                    
                    return response()->json($tokenStats);
                })
                    ->middleware('module.permission:ai,view')
                    ->name('token-stats');
                
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
                    
                // AI Profile Management Routes
                Route::prefix('profile')
                    ->name('profile.')
                    ->middleware('module.permission:ai,view')
                    ->group(function () {
                        Route::get('/', [\Modules\AI\App\Http\Controllers\Admin\Profile\AIProfileController::class, 'show'])
                            ->name('show');
                        
                        // Step-based URL routing
                        
                        // jQuery-based simple edit
                        Route::get('/edit/{step?}', [\Modules\AI\App\Http\Controllers\Admin\Profile\AIProfileController::class, 'jqueryEdit'])
                            ->where('step', '[1-5]')
                            ->name('edit');
                            
                        Route::post('/edit/{step?}', [\Modules\AI\App\Http\Controllers\Admin\Profile\AIProfileController::class, 'update'])
                            ->where('step', '[1-5]')
                            ->middleware('module.permission:ai,update')
                            ->name('edit.update');
                        
                        Route::post('/generate-story', [\Modules\AI\App\Http\Controllers\Admin\Profile\AIProfileController::class, 'generateStory'])
                            ->middleware('module.permission:ai,create')
                            ->name('generate-story');
                        
                        Route::get('/generate-story-stream', [\Modules\AI\App\Http\Controllers\Admin\Profile\AIProfileController::class, 'generateStoryStream'])
                            ->middleware('module.permission:ai,create')
                            ->name('generate-story-stream');
                        
                        Route::post('/save-field', [\Modules\AI\App\Http\Controllers\Admin\Profile\AIProfileController::class, 'saveField'])
                            ->middleware('module.permission:ai,update')
                            ->name('save-field');
                        
                        Route::get('/questions/{step}', [\Modules\AI\App\Http\Controllers\Admin\Profile\AIProfileController::class, 'getQuestions'])
                            ->middleware('module.permission:ai,view')
                            ->name('get-questions');
                        
                        Route::get('/profile-data', [\Modules\AI\App\Http\Controllers\Admin\Profile\AIProfileController::class, 'getProfileData'])
                            ->middleware('module.permission:ai,view')
                            ->name('get-profile-data');
                        
                        Route::post('/chat', [\Modules\AI\App\Http\Controllers\Admin\Profile\AIProfileController::class, 'chat'])
                            ->middleware('module.permission:ai,view')
                            ->name('chat');
                        
                        Route::post('/reset', [\Modules\AI\App\Http\Controllers\Admin\Profile\AIProfileController::class, 'reset'])
                            ->middleware('module.permission:ai,delete')
                            ->name('reset');
                    });

                // AI Debug Dashboard Routes
                Route::prefix('debug')
                    ->name('debug.')
                    ->middleware('role:root') // Sadece root admin erişebilir
                    ->group(function () {
                        
                        // Debug Dashboard Ana Sayfa
                        Route::get('/dashboard', [\Modules\AI\App\Http\Controllers\Admin\DebugDashboardController::class, 'index'])
                            ->name('dashboard');
                        
                        // Real-time Prompt Tester
                        Route::post('/test-prompt', [\Modules\AI\App\Http\Controllers\Admin\DebugDashboardController::class, 'testPrompt'])
                            ->name('test-prompt');
                        
                        // Prompt Details Modal
                        Route::post('/prompt-details', [\Modules\AI\App\Http\Controllers\Admin\DebugDashboardController::class, 'getPromptDetails'])
                            ->name('prompt-details');
                        
                        // Live Log Stream (AJAX)
                        Route::get('/live-stream', [\Modules\AI\App\Http\Controllers\Admin\DebugDashboardController::class, 'liveLogStream'])
                            ->name('live-stream');
                        
                        // Tenant Analytics
                        Route::get('/tenant/{tenantId}', [\Modules\AI\App\Http\Controllers\Admin\DebugDashboardController::class, 'tenantAnalytics'])
                            ->name('tenant-analytics');
                        
                        // Export/Download Routes
                        Route::get('/export/{type}', [\Modules\AI\App\Http\Controllers\Admin\DebugDashboardController::class, 'exportData'])
                            ->name('export');
                            
                        // Performance Analytics
                        Route::get('/performance', [\Modules\AI\App\Http\Controllers\Admin\DebugDashboardController::class, 'performanceAnalytics'])
                            ->name('performance');
                            
                        // Prompt Usage Heatmap
                        Route::get('/heatmap', [\Modules\AI\App\Http\Controllers\Admin\DebugDashboardController::class, 'promptHeatmap'])
                            ->name('heatmap');
                            
                        // Error Analysis
                        Route::get('/errors', [\Modules\AI\App\Http\Controllers\Admin\DebugDashboardController::class, 'errorAnalysis'])
                            ->name('errors');
                    });
            });
    });