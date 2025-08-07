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
                
                // AI Providers Management
                Route::get('/providers', function() {
                    $providers = \Modules\AI\App\Models\AIProvider::orderBy('priority', 'asc')->get();
                    return view('ai::admin.providers.index', compact('providers'));
                })
                    ->middleware('module.permission:ai,view')
                    ->name('providers');

                // SEO AI Center
                Route::get('/seo', function() {
                    // SEO kategorisindeki feature'ları getir
                    $seoFeatures = \Modules\AI\App\Models\AIFeature::with(['category'])
                        ->whereHas('category', function($query) {
                            $query->where('ai_feature_categories.title', 'SEO & Marketing')
                                  ->where('ai_feature_categories.is_active', true);
                        })
                        ->where('ai_features.status', 'active')
                        ->orderBy('complexity_level')
                        ->get()
                        ->groupBy(function($feature) {
                            return $feature->category->title ?? 'other';
                        });

                    $categoryNames = [
                        'SEO & Marketing' => 'SEO & Pazarlama Araçları'
                    ];

                    // Token durumunu al
                    $tokenStatus = [
                        'remaining' => ai_get_credit_balance() ?? 1000,
                        'provider' => ai_get_active_provider_name() ?? 'DeepSeek',
                        'provider_active' => true
                    ];

                    return view('ai::admin.seo.prowess', compact('seoFeatures', 'categoryNames', 'tokenStatus'));
                })
                    ->middleware('module.permission:ai,view')
                    ->name('seo.prowess');
                    
                // AI Provider Update
                Route::post('/providers/{provider}/update', function(\Modules\AI\App\Models\AIProvider $provider, \Illuminate\Http\Request $request) {
                    $data = $request->only(['is_active', 'is_default', 'priority', 'api_key']);
                    
                    // Boolean değerleri düzelt
                    $data['is_active'] = $request->has('is_active') || $request->boolean('is_active');
                    $data['is_default'] = $request->has('is_default') || $request->boolean('is_default');
                    
                    $provider->update($data);
                    
                    // AJAX mi normal request mi kontrol et
                    if ($request->expectsJson()) {
                        return response()->json(['success' => true, 'message' => 'Provider güncellendi!']);
                    }
                    
                    return redirect()->route('admin.ai.providers')->with('success', 'Provider güncellendi!');
                })
                    ->middleware('module.permission:ai,update')
                    ->name('providers.update');

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
            });
    });