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

// Admin rotaları - middleware RouteServiceProvider'da uygulanıyor
Route::middleware(['admin', 'tenant', 'admin.tenant.select'])
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
                
                // AI Translation Routes - Global Translation System
                Route::prefix('translation')->name('translation.')->group(function () {
                    // Token estimation
                    Route::post('/estimate-tokens', [\Modules\AI\App\Http\Controllers\Admin\Translation\GlobalTranslationController::class, 'estimateTokens'])
                        ->name('estimate-tokens');
                    
                    // Start translation
                    Route::post('/start', [\Modules\AI\App\Http\Controllers\Admin\Translation\GlobalTranslationController::class, 'startTranslation'])
                        ->name('start');
                    
                    // Get progress
                    Route::get('/progress/{operationId}', [\Modules\AI\App\Http\Controllers\Admin\Translation\GlobalTranslationController::class, 'getProgress'])
                        ->name('progress');
                });
                    
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
                Route::get('/features/categories', [\Modules\AI\App\Http\Controllers\Admin\Features\AIFeatureCategoriesController::class, 'index'])
                    ->middleware('module.permission:ai,view')
                    ->name('features.categories');
                    
                // Categories Order Update
                Route::post('/features/categories/update-order', [\Modules\AI\App\Http\Controllers\Admin\Features\AIFeatureCategoriesController::class, 'updateOrder'])
                    ->middleware('module.permission:ai,update')
                    ->name('features.categories.update-order');
                    
                // Categories Status Toggle
                Route::post('/features/categories/{id}/toggle-status', [\Modules\AI\App\Http\Controllers\Admin\Features\AIFeatureCategoriesController::class, 'toggleStatus'])
                    ->middleware('module.permission:ai,update')
                    ->name('features.categories.toggle-status');
                    
                // Input Management Routes - Universal Input System
                Route::prefix('features/{feature}/inputs')
                    ->middleware('module.permission:ai,view')
                    ->name('features.inputs.')
                    ->group(function() {
                        Route::get('/', [\Modules\AI\App\Http\Controllers\Admin\Features\AIFeatureInputController::class, 'manage'])
                            ->name('manage');
                        Route::post('/', [\Modules\AI\App\Http\Controllers\Admin\Features\AIFeatureInputController::class, 'store'])
                            ->middleware('module.permission:ai,create')
                            ->name('store');
                        Route::put('/{input}', [\Modules\AI\App\Http\Controllers\Admin\Features\AIFeatureInputController::class, 'update'])
                            ->middleware('module.permission:ai,update')
                            ->name('update');
                        Route::delete('/{input}', [\Modules\AI\App\Http\Controllers\Admin\Features\AIFeatureInputController::class, 'destroy'])
                            ->middleware('module.permission:ai,delete')
                            ->name('destroy');
                    });
                    
                // Feature Manage Route'ları (Page pattern - create ve edit kaldırıldı)
                Route::get('/features/manage/{id?}', \Modules\AI\App\Http\Livewire\Admin\Features\AIFeatureManageComponent::class)
                    ->middleware('module.permission:ai,view')
                    ->name('features.manage');
                    
                // Feature tek başına görüntüleme sayfası (slug veya ID ile)
                Route::get('/features/{feature}', [\Modules\AI\App\Http\Controllers\Admin\Features\AIFeaturesController::class, 'show'])
                    ->middleware('module.permission:ai,view')
                    ->name('features.show');
                    
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
                
                // AI Providers Management
                Route::get('/providers', function() {
                    $providers = \Modules\AI\App\Models\AIProvider::orderBy('priority', 'asc')->get();
                    return view('ai::admin.providers.index', compact('providers'));
                })
                    ->middleware('module.permission:ai,view')
                    ->name('providers');
                    

                // AI Features Test API
                Route::post('/test-feature', function(\Illuminate\Http\Request $request) {
                    \Log::info('AI Feature Test API çağrıldı', $request->all());
                    $controller = app()->make(\Modules\AI\App\Http\Controllers\Admin\Features\AIFeaturesController::class);
                    return $controller->testFeature($request);
                })
                    ->middleware('module.permission:ai,view')
                    ->name('test-feature');
                    
                // Credit Stats API (Real-time güncellemeler için)
                Route::get('/credit-stats', function() {
                    $tenantId = tenant('id') ?: '1';
                    
                    // Mevcut credit verilerini al
                    $remainingCredits = ai_get_credit_balance($tenantId);
                    $totalUsed = ai_get_total_credits_used($tenantId);
                    $totalPurchased = ai_get_total_credits_purchased($tenantId);
                    $monthlyUsage = ai_get_monthly_credits_used($tenantId);
                    $dailyUsage = ai_get_daily_credits_used($tenantId);
                    
                    // Formatlanmış değerlerle birlikte döndür
                    $creditStats = [
                        'remaining_credits' => $remainingCredits,
                        'remaining_credits_formatted' => format_credit($remainingCredits),
                        'used_credits' => $totalUsed,
                        'total_credits' => $totalPurchased,
                        'monthly_usage' => $monthlyUsage,
                        'monthly_usage_formatted' => format_credit($monthlyUsage),
                        'daily_usage' => $dailyUsage,
                        'daily_usage_formatted' => format_credit($dailyUsage),
                        'monthly_limit' => 0, // TODO: Limit sistemi eklenecek
                        'monthly_limit_formatted' => '0',
                        'usage_percentage' => 0
                    ];
                    
                    return response()->json($creditStats);
                })
                    ->middleware('module.permission:ai,view')
                    ->name('credit-stats');
                
                // YENİ KREDİ SİSTEMİ - Credit Management Routes (Root Admin Only)
                Route::prefix('credits')
                    ->name('credits.')
                    ->middleware('role:root')
                    ->group(function () {
                        // Genel kredi yönetimi ana sayfa
                        Route::get('/', function() {
                            $packages = \Modules\AI\App\Models\AICreditPackage::getActivePackages();
                            return view('ai::admin.credits.index', compact('packages'));
                        })
                            ->name('index');
                        
                        // Kredi paket yönetimi
                        Route::get('/packages', function() {
                            $packages = \Modules\AI\App\Models\AICreditPackage::orderBy('sort_order')->get();
                            return view('ai::admin.credits.packages', compact('packages'));
                        })
                            ->name('packages');
                        
                        // Kredi kullanım raporları
                        Route::get('/usage', function() {
                            return view('ai::admin.credits.usage');
                        })
                            ->name('usage');
                        
                        // API: İstatistikler
                        Route::get('/api/statistics', function() {
                            $stats = [
                                'total_credits_used' => format_credit_short(\Modules\AI\App\Models\AICreditUsage::sum('credits_used')),
                                'monthly_credits_used' => format_credit_short(\Modules\AI\App\Models\AICreditUsage::whereMonth('used_at', now()->month)->sum('credits_used')),
                                'daily_credits_used' => format_credit_short(\Modules\AI\App\Models\AICreditUsage::whereDate('used_at', today())->sum('credits_used')),
                                'avg_daily_usage' => format_credit_short(\Modules\AI\App\Models\AICreditUsage::where('used_at', '>=', now()->subDays(30))->sum('credits_used') / 30)
                            ];
                            return response()->json($stats);
                        })->name('api.statistics');
                        
                        // API: Kullanım verisi
                        Route::get('/api/usage-data', function() {
                            $providerUsage = \Modules\AI\App\Models\AICreditUsage::selectRaw('provider_name as provider, SUM(credits_used) as credits')
                                ->groupBy('provider_name')
                                ->whereNotNull('provider_name')
                                ->get();
                                
                            $featureUsage = \Modules\AI\App\Models\AICreditUsage::selectRaw('feature_slug as feature, SUM(credits_used) as credits')
                                ->groupBy('feature_slug')
                                ->whereNotNull('feature_slug')
                                ->orderByDesc('credits')
                                ->limit(10)
                                ->get();
                                
                            $detailedUsage = \Modules\AI\App\Models\AICreditUsage::with(['tenant'])
                                ->latest('used_at')
                                ->limit(20)
                                ->get()
                                ->map(function($usage) {
                                    return [
                                        'date' => $usage->used_at->format('d.m.Y H:i'),
                                        'tenant' => $usage->tenant->title ?? 'N/A',
                                        'provider' => $usage->provider_name ?? 'N/A',
                                        'feature' => $usage->feature_slug ?? 'N/A',
                                        'input_tokens' => number_format($usage->input_tokens),
                                        'output_tokens' => number_format($usage->output_tokens),
                                        'total_credits' => format_credit($usage->credits_used, false),
                                        'cost' => format_credit_detailed($usage->credit_cost)
                                    ];
                                });
                            
                            return response()->json([
                                'provider_usage' => $providerUsage,
                                'feature_usage' => $featureUsage,
                                'detailed_usage' => $detailedUsage
                            ]);
                        })->name('api.usage-data');
                        
                        // API: Kullanım trendi
                        Route::get('/api/usage-trend', function() {
                            $period = request('period', 7);
                            $data = \Modules\AI\App\Models\AICreditUsage::selectRaw('DATE(used_at) as date, SUM(credits_used) as credits')
                                ->where('used_at', '>=', now()->subDays($period))
                                ->groupBy('date')
                                ->orderBy('date')
                                ->get();
                                
                            $labels = [];
                            $values = [];
                            
                            for ($i = $period - 1; $i >= 0; $i--) {
                                $date = now()->subDays($i)->format('Y-m-d');
                                $labels[] = now()->subDays($i)->format('d.m');
                                $usage = $data->where('date', $date)->first();
                                $values[] = $usage ? round($usage->credits, 2) : 0;
                            }
                            
                            return response()->json([
                                'labels' => $labels,
                                'values' => $values
                            ]);
                        })->name('api.usage-trend');
                        
                        // API: Kullanım filtreleme
                        Route::get('/api/usage-filter', function() {
                            $startDate = request('start');
                            $endDate = request('end');
                            
                            $usage = \Modules\AI\App\Models\AICreditUsage::with(['tenant'])
                                ->when($startDate, fn($q) => $q->whereDate('used_at', '>=', $startDate))
                                ->when($endDate, fn($q) => $q->whereDate('used_at', '<=', $endDate))
                                ->latest('used_at')
                                ->limit(100)
                                ->get()
                                ->map(function($usage) {
                                    return [
                                        'date' => $usage->used_at->format('d.m.Y H:i'),
                                        'tenant' => $usage->tenant->title ?? 'N/A',
                                        'provider' => $usage->provider_name ?? 'N/A',
                                        'feature' => $usage->feature_slug ?? 'N/A',
                                        'input_tokens' => number_format($usage->input_tokens),
                                        'output_tokens' => number_format($usage->output_tokens),
                                        'total_credits' => format_credit($usage->credits_used, false),
                                        'cost' => format_credit_detailed($usage->credit_cost)
                                    ];
                                });
                            
                            return response()->json(['usage' => $usage]);
                        })->name('api.usage-filter');
                        
                        // Kredi işlemleri
                        Route::get('/transactions', function() {
                            return view('ai::admin.credits.transactions');
                        })
                            ->name('transactions');
                        
                        // Kredi satın alımları
                        Route::get('/purchases', function() {
                            return view('ai::admin.credits.purchases');
                        })
                            ->name('purchases');
                        
                        // Kredi kullanım istatistikleri - HTML sayfa
                        Route::get('/usage-stats', [TokenManagementController::class, 'usageStats'])
                            ->name('usage-stats');
                            
                        // Kredi kullanım istatistikleri - JSON API
                        Route::get('/usage-stats-api', function() {
                            $tenantId = tenant('id') ?: '1';
                            
                            $stats = [
                                'current_balance' => ai_get_credit_balance($tenantId),
                                'total_used' => ai_get_total_credits_used($tenantId),
                                'total_purchased' => ai_get_total_credits_purchased($tenantId),
                                'monthly_used' => ai_get_monthly_credits_used($tenantId),
                                'daily_used' => ai_get_daily_credits_used($tenantId)
                            ];
                            
                            return response()->json($stats);
                        })
                            ->name('usage-stats-api');
                        
                        // Kredi detay sayfası
                        Route::get('/show', function() {
                            return view('ai::admin.credits.show');
                        })
                            ->name('show');
                    });
                    
                // ESKİ TOKEN SİSTEMİ - DEPRECATED (Backward compatibility için korundu)
                Route::prefix('tokens')
                    ->name('tokens.')
                    ->middleware('role:root')
                    ->group(function () {
                        Route::get('/', function() {
                            return redirect()->route('admin.ai.credits.index')
                                ->with('info', 'Token sistemi kredi sistemine dönüştürüldü. Lütfen kredi yönetimini kullanın.');
                        })
                            ->name('index');
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

                // Global AI Widget System
                Route::post('/execute-widget-feature', [AIChatController::class, 'executeWidgetFeature'])
                    ->middleware('module.permission:ai,view')
                    ->name('execute-widget-feature');

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
                
                // GLOBAL AI MONITORING DASHBOARD (YENİLENMİŞ SİSTEM)
                Route::prefix('monitoring')
                    ->name('monitoring.')
                    ->middleware('module.permission:ai,view')
                    ->group(function () {
                        
                        // Ana monitoring dashboard sayfası
                        Route::get('/', [\Modules\AI\App\Http\Controllers\Admin\MonitoringController::class, 'index'])
                            ->name('index');
                        
                        // API Endpoints - Real-time veriler
                        Route::get('/api/realtime-metrics', [\Modules\AI\App\Http\Controllers\Admin\MonitoringController::class, 'globalRealTimeMetrics'])
                            ->name('api.realtime-metrics');
                            
                        Route::get('/api/analytics', [\Modules\AI\App\Http\Controllers\Admin\MonitoringController::class, 'globalAnalytics'])
                            ->name('api.analytics');
                            
                        Route::get('/api/debug-data', [\Modules\AI\App\Http\Controllers\Admin\MonitoringController::class, 'globalDebugData'])
                            ->name('api.debug-data');
                            
                        Route::get('/api/live-stream', [\Modules\AI\App\Http\Controllers\Admin\MonitoringController::class, 'liveStream'])
                            ->name('api.live-stream');
                            
                        Route::get('/api/credit-status', [\Modules\AI\App\Http\Controllers\Admin\MonitoringController::class, 'creditStatus'])
                            ->name('api.credit-status');
                        
                        // Legacy API endpoints (eski sistem uyumluluğu)
                        Route::get('/api/dashboard-data', [\Modules\AI\App\Http\Controllers\Admin\MonitoringController::class, 'getDashboardData'])
                            ->name('api.dashboard-data');
                            
                        Route::get('/api/system-health', [\Modules\AI\App\Http\Controllers\Admin\MonitoringController::class, 'getSystemHealth'])
                            ->name('api.system-health');
                            
                        Route::get('/api/performance-metrics', [\Modules\AI\App\Http\Controllers\Admin\MonitoringController::class, 'getPerformanceMetrics'])
                            ->name('api.performance-metrics');
                            
                        Route::get('/api/usage-analytics', [\Modules\AI\App\Http\Controllers\Admin\MonitoringController::class, 'getUsageAnalytics'])
                            ->name('api.usage-analytics');
                            
                        Route::get('/api/provider-health', [\Modules\AI\App\Http\Controllers\Admin\MonitoringController::class, 'getProviderHealth'])
                            ->name('api.provider-health');
                            
                        Route::get('/api/real-time-stats', [\Modules\AI\App\Http\Controllers\Admin\MonitoringController::class, 'getRealTimeStats'])
                            ->name('api.real-time-stats');
                            
                        Route::get('/api/alerts', [\Modules\AI\App\Http\Controllers\Admin\MonitoringController::class, 'getAlerts'])
                            ->name('api.alerts');
                            
                        Route::get('/api/cost-report', [\Modules\AI\App\Http\Controllers\Admin\MonitoringController::class, 'getCostReport'])
                            ->name('api.cost-report');
                        
                        // Export functionality
                        Route::get('/export/{format}', [\Modules\AI\App\Http\Controllers\Admin\MonitoringController::class, 'exportData'])
                            ->where('format', 'json|csv')
                            ->name('export');
                    });
                    
                // Universal Input System Demo Route
                Route::get('/demo/blog-writer', function() {
                    return view('ai::demo.blog-writer-form');
                })->name('demo.blog-writer');
                
                // Universal Input System API Routes
                Route::prefix('api/features')
                    ->middleware('module.permission:ai,view')
                    ->name('api.features.')
                    ->group(function() {
                        Route::get('/{feature}/form-structure', [\Modules\AI\App\Http\Controllers\Admin\Features\AIFeatureInputController::class, 'getFormStructure'])
                            ->name('form-structure');
                        Route::post('/{feature}/validate-inputs', [\Modules\AI\App\Http\Controllers\Admin\Features\AIFeatureInputController::class, 'validateInputs'])
                            ->name('validate-inputs');
                        Route::post('/{feature}/process-form', [\Modules\AI\App\Http\Controllers\Admin\Features\AIFeatureInputController::class, 'processForm'])
                            ->name('process-form');
                    });
                    
                // AI Profiles API Routes  
                Route::prefix('api/profiles')
                    ->middleware('module.permission:ai,view')
                    ->name('api.profiles.')
                    ->group(function() {
                        Route::get('/company-info', function() {
                            $tenantId = tenant('id') ?: '1';
                            
                            $profile = \Modules\AI\App\Models\AITenantProfile::where('tenant_id', $tenantId)
                                ->where('is_active', true)
                                ->first();
                            
                            if (!$profile) {
                                return response()->json([
                                    'available' => false,
                                    'message' => 'Şirket profili bulunamadı. Lütfen AI Profiles bölümünden şirket bilgilerinizi tamamlayın.',
                                    'setup_url' => route('admin.ai.profile.show')
                                ]);
                            }
                            
                            // Company info'yu organize et
                            $companyInfo = [];
                            
                            if ($profile->company_info) {
                                $companyData = is_string($profile->company_info) ? json_decode($profile->company_info, true) : $profile->company_info;
                                if ($companyData) {
                                    $companyInfo['company_name'] = $companyData['company_name'] ?? '';
                                    $companyInfo['industry'] = $companyData['industry'] ?? '';
                                    $companyInfo['business_model'] = $companyData['business_model'] ?? '';
                                    $companyInfo['target_audience'] = $companyData['target_audience'] ?? '';
                                }
                            }
                            
                            if ($profile->sector_details) {
                                $sectorData = is_string($profile->sector_details) ? json_decode($profile->sector_details, true) : $profile->sector_details;
                                if ($sectorData) {
                                    $companyInfo['products_services'] = $sectorData['products_services'] ?? '';
                                    $companyInfo['competitive_advantage'] = $sectorData['competitive_advantage'] ?? '';
                                }
                            }
                            
                            if ($profile->ai_behavior_rules) {
                                $behaviorData = is_string($profile->ai_behavior_rules) ? json_decode($profile->ai_behavior_rules, true) : $profile->ai_behavior_rules;
                                if ($behaviorData) {
                                    $companyInfo['brand_voice'] = $behaviorData['brand_voice'] ?? '';
                                    $companyInfo['communication_style'] = $behaviorData['communication_style'] ?? '';
                                }
                            }
                            
                            return response()->json([
                                'available' => true,
                                'profile_data' => $companyInfo,
                                'company_info' => $companyInfo, // Backward compatibility
                                'profile_completeness' => $profile->profile_completeness_score ?? 0,
                                'last_updated' => $profile->updated_at->diffForHumans()
                            ]);
                        })->name('company-info');
                    });

                // Universal Input System V3 Routes
                Route::prefix('universal')->name('universal.')->group(function() {
                    // Admin Panel Pages
                    Route::get('/', [\Modules\AI\App\Http\Controllers\Admin\Universal\UniversalInputController::class, 'index'])->name('index');
                    Route::get('/input-management', [\Modules\AI\App\Http\Controllers\Admin\Universal\UniversalInputController::class, 'inputManagement'])->name('input-management');
                    
                    Route::get('/context-dashboard', function() {
                        return view('ai::admin.universal.context-dashboard');
                    })->middleware('module.permission:ai,view')->name('universal.context-dashboard');
                    
                    Route::get('/bulk-operations', function() {
                        return view('ai::admin.universal.bulk-operations');
                    })->middleware('module.permission:ai,view')->name('universal.bulk-operations');
                    
                    Route::get('/analytics-dashboard', function() {
                        return view('ai::admin.universal.analytics-dashboard');
                    })->middleware('module.permission:ai,view')->name('universal.analytics-dashboard');
                    
                    Route::get('/integration-settings', function() {
                        return view('ai::admin.universal.integration-settings');
                    })->middleware('module.permission:ai,view')->name('universal.integration-settings');
                    
                    // API Routes
                    Route::get('/form-structure/{featureId}', [\Modules\AI\App\Http\Controllers\Admin\Universal\UniversalInputController::class, 'getFormStructure']);
                    Route::post('/submit/{featureId}', [\Modules\AI\App\Http\Controllers\Admin\Universal\UniversalInputController::class, 'submitForm']);
                    Route::get('/defaults/{featureId}', [\Modules\AI\App\Http\Controllers\Admin\Universal\UniversalInputController::class, 'getSmartDefaults']);
                    Route::post('/preferences', [\Modules\AI\App\Http\Controllers\Admin\Universal\UniversalInputController::class, 'savePreferences']);
                    Route::post('/validate', [\Modules\AI\App\Http\Controllers\Admin\Universal\UniversalInputController::class, 'validateInputs']);
                    Route::post('/suggestions', [\Modules\AI\App\Http\Controllers\Admin\Universal\UniversalInputController::class, 'getFieldSuggestions']);
                });
                // Bulk Operations Routes
                Route::prefix('bulk')->group(function() {
                    Route::post('/create', [\Modules\AI\App\Http\Controllers\Admin\Bulk\BulkOperationController::class, 'createBulkOperation']);
                    Route::get('/status/{operationId}', [\Modules\AI\App\Http\Controllers\Admin\Bulk\BulkOperationController::class, 'getOperationStatus']);
                    Route::post('/cancel/{operationId}', [\Modules\AI\App\Http\Controllers\Admin\Bulk\BulkOperationController::class, 'cancelOperation']);
                    Route::get('/history', [\Modules\AI\App\Http\Controllers\Admin\Bulk\BulkOperationController::class, 'getOperationHistory']);
                    Route::post('/retry/{operationId}', [\Modules\AI\App\Http\Controllers\Admin\Bulk\BulkOperationController::class, 'retryFailedItems']);
                });

                // Module Integration Routes
                Route::prefix('integration')->group(function() {
                    Route::get('/module/{moduleName}', [\Modules\AI\App\Http\Controllers\Admin\Integration\ModuleIntegrationController::class, 'getModuleConfig']);
                    Route::put('/module/{moduleName}', [\Modules\AI\App\Http\Controllers\Admin\Integration\ModuleIntegrationController::class, 'updateModuleConfig']);
                    Route::get('/actions/{moduleName}/{fieldName}', [\Modules\AI\App\Http\Controllers\Admin\Integration\ModuleIntegrationController::class, 'getAvailableActions']);
                    Route::post('/execute', [\Modules\AI\App\Http\Controllers\Admin\Integration\ModuleIntegrationController::class, 'executeAction']);
                    Route::post('/suggestions', [\Modules\AI\App\Http\Controllers\Admin\Integration\ModuleIntegrationController::class, 'getFieldSuggestions']);
                    Route::get('/health/{moduleName}', [\Modules\AI\App\Http\Controllers\Admin\Integration\ModuleIntegrationController::class, 'getModuleHealth']);
                });

                // Template Routes
                Route::prefix('templates')->group(function() {
                    Route::get('/list', [\Modules\AI\App\Http\Controllers\Admin\Template\TemplateController::class, 'listTemplates']);
                    Route::get('/preview/{templateId}', [\Modules\AI\App\Http\Controllers\Admin\Template\TemplateController::class, 'previewTemplate']);
                    Route::post('/generate/{templateId}', [\Modules\AI\App\Http\Controllers\Admin\Template\TemplateController::class, 'generateFromTemplate']);
                    Route::post('/create', [\Modules\AI\App\Http\Controllers\Admin\Template\TemplateController::class, 'createCustomTemplate']);
                    Route::put('/{templateId}', [\Modules\AI\App\Http\Controllers\Admin\Template\TemplateController::class, 'updateTemplate']);
                    Route::delete('/{templateId}', [\Modules\AI\App\Http\Controllers\Admin\Template\TemplateController::class, 'deleteTemplate']);
                });

                // Translation Routes  
                Route::prefix('translation')->group(function() {
                    Route::post('/translate', [\Modules\AI\App\Http\Controllers\Admin\Translation\TranslationController::class, 'translateContent']);
                    Route::post('/bulk-translate', [\Modules\AI\App\Http\Controllers\Admin\Translation\TranslationController::class, 'bulkTranslate']);
                    Route::get('/languages', [\Modules\AI\App\Http\Controllers\Admin\Translation\GlobalTranslationController::class, 'getAvailableLanguages']);
                    Route::get('/fields/{module}', [\Modules\AI\App\Http\Controllers\Admin\Translation\TranslationController::class, 'getTranslatableFields']);
                    Route::get('/mappings/{module}', [\Modules\AI\App\Http\Controllers\Admin\Translation\TranslationController::class, 'getFieldMappings']);
                    
                    // Global translation routes (used by JavaScript)
                    Route::post('/estimate', [\Modules\AI\App\Http\Controllers\Admin\Translation\GlobalTranslationController::class, 'estimateTokens']);
                    Route::post('/estimate-tokens', [\Modules\AI\App\Http\Controllers\Admin\Translation\GlobalTranslationController::class, 'estimateTokens']);
                    Route::post('/start', [\Modules\AI\App\Http\Controllers\Admin\Translation\GlobalTranslationController::class, 'startTranslation']);
                    Route::get('/progress/{operationId}', [\Modules\AI\App\Http\Controllers\Admin\Translation\GlobalTranslationController::class, 'getProgress']);
                });

                // Analytics Routes
                Route::prefix('analytics')->group(function() {
                    Route::get('/usage/{featureId}', [\Modules\AI\App\Http\Controllers\Admin\Analytics\AnalyticsController::class, 'getUsageStats']);
                    Route::get('/performance', [\Modules\AI\App\Http\Controllers\Admin\Analytics\AnalyticsController::class, 'getPerformanceMetrics']);
                    Route::get('/popular-features', [\Modules\AI\App\Http\Controllers\Admin\Analytics\AnalyticsController::class, 'getPopularFeatures']);
                    Route::get('/user-preferences/{userId}', [\Modules\AI\App\Http\Controllers\Admin\Analytics\AnalyticsController::class, 'getUserPreferences']);
                    Route::get('/system-health', [\Modules\AI\App\Http\Controllers\Admin\Analytics\AnalyticsController::class, 'getSystemHealth']);
                });

                // Context & Rules Routes
                Route::prefix('context')->group(function() {
                    Route::get('/dashboard', [\Modules\AI\App\Http\Controllers\Admin\Context\ContextController::class, 'index'])->name('context.dashboard');
                    Route::get('/rules', [\Modules\AI\App\Http\Controllers\Admin\Context\ContextController::class, 'listRules']);
                    Route::post('/rules', [\Modules\AI\App\Http\Controllers\Admin\Context\ContextController::class, 'createRule']);
                    Route::put('/rules/{ruleId}', [\Modules\AI\App\Http\Controllers\Admin\Context\ContextController::class, 'updateRule']);
                    Route::delete('/rules/{ruleId}', [\Modules\AI\App\Http\Controllers\Admin\Context\ContextController::class, 'deleteRule']);
                    Route::post('/detect', [\Modules\AI\App\Http\Controllers\Admin\Context\ContextController::class, 'detectContext']);
                });

                // ÇAKIŞAN ROUTE GRUBU SİLİNDİ - ÜSTTEKİ GRUP AKTİF

                // Bulk Operations Admin Pages
                Route::prefix('bulk')->name('bulk.')->group(function() {
                    Route::get('/operations', [\Modules\AI\App\Http\Controllers\Admin\Bulk\BulkOperationController::class, 'index'])->name('operations');
                });

                // Module Integration Admin Pages  
                Route::prefix('integration')->name('integration.')->group(function() {
                    Route::get('/settings', [\Modules\AI\App\Http\Controllers\Admin\Integration\ModuleIntegrationController::class, 'index'])->name('settings');
                });

                // Analytics Admin Pages
                Route::prefix('analytics')->name('analytics.')->group(function() {
                    Route::get('/dashboard', [\Modules\AI\App\Http\Controllers\Admin\Analytics\AnalyticsController::class, 'index'])->name('dashboard');
                });
                
                // PHASE 4: Model Credit Rate Management Pages
                Route::prefix("credit-rates")->name("credit-rates.")->group(function() {
                    // Ana model credit rate yönetim sayfası
                    Route::get("/", [\Modules\AI\App\Http\Controllers\Admin\ModelCreditRateController::class, "index"])->name("index");
                    
                    // Credit rate düzenleme sayfası  
                    Route::get("/manage/{providerId}/{modelName?}", [\Modules\AI\App\Http\Controllers\Admin\ModelCreditRateController::class, "manage"])->name("manage");
                    
                    // Bulk credit rate import/export
                    Route::get("/import", [\Modules\AI\App\Http\Controllers\Admin\ModelCreditRateController::class, "importPage"])->name("import");
                    Route::post("/import", [\Modules\AI\App\Http\Controllers\Admin\ModelCreditRateController::class, "processImport"])->name("import.process");
                    Route::get("/export", [\Modules\AI\App\Http\Controllers\Admin\ModelCreditRateController::class, "export"])->name("export");
                    
                    // Credit calculator dashboard
                    Route::get("/calculator", [\Modules\AI\App\Http\Controllers\Admin\ModelCreditRateController::class, "calculator"])->name("calculator");
                    
                    // Model performance analytics
                    Route::get("/analytics", [\Modules\AI\App\Http\Controllers\Admin\ModelCreditRateController::class, "analytics"])->name("analytics");
                    
                    // API Endpoints for AJAX calls
                    Route::prefix("api")->name("api.")->group(function() {
                        // Get credit rates list for DataTable (manage sayfası için)
                        Route::get("/index", [\Modules\AI\App\Http\Controllers\Admin\ModelCreditRateController::class, "apiIndex"])->name("index");
                        
                        // Get providers with models
                        Route::get("/providers-models", [\Modules\AI\App\Http\Controllers\Admin\ModelCreditRateController::class, "getProvidersWithModels"])->name("providers-models");
                        
                        // Get models for specific provider
                        Route::get("/provider/{providerId}/models", [\Modules\AI\App\Http\Controllers\Admin\ModelCreditRateController::class, "getProviderModels"])->name("provider-models");
                        
                        // Calculate credit cost
                        Route::get("/calculate-cost", [\Modules\AI\App\Http\Controllers\Admin\ModelCreditRateController::class, "calculateCreditCost"])->name("calculate-cost");
                        
                        // CRUD operations
                        Route::post("/store", [\Modules\AI\App\Http\Controllers\Admin\ModelCreditRateController::class, "store"])->name("store");
                        Route::put("/{id}", [\Modules\AI\App\Http\Controllers\Admin\ModelCreditRateController::class, "update"])->name("update");
                        Route::delete("/{id}", [\Modules\AI\App\Http\Controllers\Admin\ModelCreditRateController::class, "destroy"])->name("destroy");
                    });
                    
                // Admin Compare Models API endpoint (calculator sayfası için)
                Route::post('/compare-models', [\Modules\AI\App\Http\Controllers\Admin\ModelCreditRateController::class, 'compareModels'])
                    ->middleware('module.permission:ai,view')
                    ->name('compare.models');
                });
                
                // PHASE 5: Credit Warning Management Pages
                Route::prefix("credit-warnings")->name("credit-warnings.")->group(function() {
                    // Ana credit warning yönetim sayfası
                    Route::get("/", [\Modules\AI\App\Http\Controllers\Admin\CreditWarningController::class, "index"])->name("index");
                    
                    // Credit warning konfigürasyon sayfası  
                    Route::get("/configuration", [\Modules\AI\App\Http\Controllers\Admin\CreditWarningController::class, "configuration"])->name("configuration");
                    Route::post("/configuration", [\Modules\AI\App\Http\Controllers\Admin\CreditWarningController::class, "updateConfiguration"])->name("configuration.update");
                    
                    // Credit warning test sayfası
                    Route::get("/test", [\Modules\AI\App\Http\Controllers\Admin\CreditWarningController::class, "test"])->name("test");
                    Route::post("/test", [\Modules\AI\App\Http\Controllers\Admin\CreditWarningController::class, "runTest"])->name("test.run");
                    
                    // Credit warning analytics
                    Route::get("/analytics", [\Modules\AI\App\Http\Controllers\Admin\CreditWarningController::class, "analytics"])->name("analytics");
                });
                
                // PHASE 6: Silent Fallback Management Pages
                Route::prefix("silent-fallback")->name("silent-fallback.")->group(function() {
                    // Ana silent fallback yönetim sayfası
                    Route::get("/", [\Modules\AI\App\Http\Controllers\Admin\SilentFallbackController::class, "index"])->name("index");
                    
                    // Silent fallback konfigürasyon sayfası  
                    Route::get("/configuration", [\Modules\AI\App\Http\Controllers\Admin\SilentFallbackController::class, "configuration"])->name("configuration");
                    
                    // Silent fallback test sayfası
                    Route::post("/test", [\Modules\AI\App\Http\Controllers\Admin\SilentFallbackController::class, "test"])->name("test");
                    
                    // Silent fallback analytics
                    Route::get("/analytics", [\Modules\AI\App\Http\Controllers\Admin\SilentFallbackController::class, "analytics"])->name("analytics");
                    
                    // Clear statistics
                    Route::post("/clear-stats", [\Modules\AI\App\Http\Controllers\Admin\SilentFallbackController::class, "clearStatistics"])->name("clear-stats");
                });
                
                // PHASE 7: Central Fallback Management Pages
                Route::prefix("central-fallback")->name("central-fallback.")->group(function() {
                    // Ana central fallback yönetim sayfası
                    Route::get("/", [\Modules\AI\App\Http\Controllers\Admin\CentralFallbackController::class, "index"])->name("index");
                    
                    // Central fallback konfigürasyon sayfası  
                    Route::get("/configuration", [\Modules\AI\App\Http\Controllers\Admin\CentralFallbackController::class, "configuration"])->name("configuration");
                    Route::post("/configuration", [\Modules\AI\App\Http\Controllers\Admin\CentralFallbackController::class, "updateConfiguration"])->name("configuration.update");
                    
                    // Central fallback test sayfası
                    Route::post("/test", [\Modules\AI\App\Http\Controllers\Admin\CentralFallbackController::class, "test"])->name("test");
                    
                    // Central fallback statistics
                    Route::get("/statistics", [\Modules\AI\App\Http\Controllers\Admin\CentralFallbackController::class, "statistics"])->name("statistics");
                    
                    // Model recommendations
                    Route::post("/model-recommendations", [\Modules\AI\App\Http\Controllers\Admin\CentralFallbackController::class, "getModelRecommendations"])->name("model-recommendations");
                    
                    // Reset failures
                    Route::post("/reset-failures", [\Modules\AI\App\Http\Controllers\Admin\CentralFallbackController::class, "resetFailures"])->name("reset-failures");
                    
                    // Clear statistics
                    Route::post("/clear-statistics", [\Modules\AI\App\Http\Controllers\Admin\CentralFallbackController::class, "clearStatistics"])->name("clear-statistics");
                });
                
                // PHASE 8: Model Credit Rate Management Pages
                Route::prefix("model-credit-rates")->name("model-credit-rates.")->group(function() {
                    // Ana model credit rate yönetim sayfası
                    Route::get("/", [\Modules\AI\App\Http\Controllers\Admin\ModelCreditRateController::class, "index"])->name("index");
                    
                    // Model credit rate oluşturma/düzenleme sayfası
                    Route::get("/create", [\Modules\AI\App\Http\Controllers\Admin\ModelCreditRateController::class, "create"])->name("create");
                    Route::post("/", [\Modules\AI\App\Http\Controllers\Admin\ModelCreditRateController::class, "store"])->name("store");
                    Route::get("/{id}/edit", [\Modules\AI\App\Http\Controllers\Admin\ModelCreditRateController::class, "edit"])->name("edit");
                    Route::put("/{id}", [\Modules\AI\App\Http\Controllers\Admin\ModelCreditRateController::class, "update"])->name("update");
                    Route::delete("/{id}", [\Modules\AI\App\Http\Controllers\Admin\ModelCreditRateController::class, "destroy"])->name("destroy");
                    
                    // Bulk operations
                    Route::post("/bulk-update", [\Modules\AI\App\Http\Controllers\Admin\ModelCreditRateController::class, "bulkUpdate"])->name("bulk-update");
                    Route::post("/bulk-delete", [\Modules\AI\App\Http\Controllers\Admin\ModelCreditRateController::class, "bulkDelete"])->name("bulk-delete");
                    
                    // Import/Export
                    Route::get("/export", [\Modules\AI\App\Http\Controllers\Admin\ModelCreditRateController::class, "export"])->name("export");
                    Route::post("/import", [\Modules\AI\App\Http\Controllers\Admin\ModelCreditRateController::class, "import"])->name("import");
                    
                    // Model specific operations
                    Route::post("/sync-models", [\Modules\AI\App\Http\Controllers\Admin\ModelCreditRateController::class, "syncModels"])->name("sync-models");
                    Route::get("/model-info/{model}", [\Modules\AI\App\Http\Controllers\Admin\ModelCreditRateController::class, "getModelInfo"])->name("model-info");
                });
            });
    });
