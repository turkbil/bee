<?php

declare(strict_types=1);

namespace Modules\AI\App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;
use App\Http\Controllers\Controller;
use Modules\AI\App\Services\CentralFallbackService;
use Modules\AI\App\Models\AIProvider;
use Illuminate\Support\Facades\Log;

/**
 * Central Fallback Configuration Controller
 * 
 * Admin panelinde merkezi fallback sistemi yönetimi
 */
class CentralFallbackController extends Controller
{
    public function __construct(
        private CentralFallbackService $fallbackService
    ) {}

    /**
     * Central fallback configuration dashboard
     */
    public function index(): View
    {
        $tenant = tenant();
        $tenantId = $tenant?->id;
        
        // Fallback tenant ID'si - admin.tenant.select middleware'ından al
        if (!$tenantId) {
            $tenantId = session('admin_selected_tenant_id', 1);
            $tenant = (object) ['id' => $tenantId];
        }
        
        // Get current configuration
        $config = $this->fallbackService->getCentralFallbackConfig();
        
        // Get provider order
        $providerOrder = $this->fallbackService->getFallbackProviderOrder();
        
        // Get all available providers
        $allProviders = AIProvider::where('is_active', true)
                                 ->orderBy('priority')
                                 ->get();
        
        // Get fallback statistics
        $statistics = $this->fallbackService->getFallbackStatistics();
        
        // Get provider health status
        $providerHealth = $this->fallbackService->getGlobalProviderHealth();
        
        // Get model recommendations examples
        $modelRecommendations = [
            'economy' => $this->fallbackService->getFallbackModelRecommendations('gpt-3.5-turbo', 'general'),
            'balanced' => $this->fallbackService->getFallbackModelRecommendations('gpt-4o', 'general'),
            'premium' => $this->fallbackService->getFallbackModelRecommendations('gpt-5', 'general')
        ];

        return view('ai::admin.central-fallback.index', [
            'config' => $config,
            'providerOrder' => $providerOrder,
            'allProviders' => $allProviders,
            'statistics' => $statistics,
            'providerHealth' => $providerHealth,
            'modelRecommendations' => $modelRecommendations
        ]);
    }

    /**
     * Configuration settings page
     */
    public function configuration(): View
    {
        $config = $this->fallbackService->getCentralFallbackConfig();
        $allProviders = AIProvider::where('is_active', true)->get();

        return view('ai::admin.central-fallback.configuration', [
            'config' => $config,
            'all_providers' => $allProviders
        ]);
    }

    /**
     * Update central fallback configuration
     */
    public function updateConfiguration(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'fallback_enabled' => 'boolean',
                'max_fallback_attempts' => 'integer|min:1|max:5',
                'fallback_timeout' => 'integer|min:10|max:120',
                'preferred_provider_order' => 'array',
                'preferred_provider_order.*' => 'string|exists:ai_providers,name',
                'cost_preference' => 'string|in:cheap,balanced,premium',
                'retry_failed_providers' => 'boolean',
                'log_fallback_decisions' => 'boolean'
            ]);

            // Save configuration (for demonstration, using cache)
            // In production, this should be saved to database
            $tenant = tenant();
            $cacheKey = $tenant ? "fallback_config_{$tenant->id}" : 'fallback_config_central';
            
            $existingConfig = $this->fallbackService->getCentralFallbackConfig();
            $newConfig = array_merge($existingConfig, $validated);
            
            cache()->put($cacheKey, $newConfig, 86400); // 24 hours
            
            // Clear config cache to force reload
            $this->fallbackService->clearConfigCache();

            Log::info('✅ Central fallback configuration updated', [
                'tenant_id' => $tenant?->id,
                'updated_config' => $validated,
                'updated_by' => auth()->user()?->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Fallback konfigürasyonu başarıyla güncellendi',
                'config' => $newConfig
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update central fallback configuration', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Konfigürasyon güncellenirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test fallback configuration
     */
    public function test(Request $request): JsonResponse
    {
        try {
            $testResult = $this->fallbackService->testFallbackConfiguration();

            Log::info('🧪 Central fallback configuration tested', [
                'test_result' => $testResult,
                'tested_by' => auth()->user()?->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Fallback konfigürasyon testi başarılı',
                'test_result' => $testResult
            ]);

        } catch (\Exception $e) {
            Log::error('Central fallback configuration test failed', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Test başarısız: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get fallback statistics data for charts
     */
    public function statistics(Request $request): JsonResponse
    {
        try {
            $days = (int) $request->input('days', 30);
            $statistics = $this->fallbackService->getFallbackStatistics();

            // Process daily stats for chart
            $dailyData = [];
            for ($i = $days - 1; $i >= 0; $i--) {
                $date = now()->subDays($i)->format('Y-m-d');
                $dayStats = $statistics['daily_stats'][$date] ?? ['total' => 0, 'fallbacks' => 0, 'successes' => 0];
                
                $dailyData[] = [
                    'date' => $date,
                    'total_requests' => $dayStats['total'],
                    'fallback_requests' => $dayStats['fallbacks'],
                    'successful_fallbacks' => $dayStats['successes'],
                    'fallback_rate' => $dayStats['total'] > 0 ? round(($dayStats['fallbacks'] / $dayStats['total']) * 100, 1) : 0
                ];
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'overview' => [
                        'total_requests' => $statistics['total_requests'],
                        'fallback_requests' => $statistics['fallback_requests'],
                        'successful_fallbacks' => $statistics['successful_fallbacks'],
                        'failed_fallbacks' => $statistics['failed_fallbacks'],
                        'fallback_rate' => $statistics['fallback_rate'],
                        'fallback_success_rate' => $statistics['fallback_success_rate']
                    ],
                    'daily_data' => $dailyData,
                    'provider_usage' => $statistics['provider_usage'],
                    'model_usage' => $statistics['model_usage']
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'İstatistik verileri alınamadı: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get model recommendations for specific scenario
     */
    public function modelRecommendations(Request $request): JsonResponse
    {
        try {
            $originalModel = $request->input('original_model');
            $requestType = $request->input('request_type', 'general');

            $recommendations = $this->fallbackService->getFallbackModelRecommendations(
                $originalModel,
                $requestType
            );

            return response()->json([
                'success' => true,
                'recommendations' => $recommendations,
                'original_model' => $originalModel,
                'request_type' => $requestType
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Model önerileri alınamadı: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reset provider failure history
     */
    public function resetProviderFailures(Request $request): JsonResponse
    {
        try {
            $providerName = $request->input('provider_name');
            
            if ($providerName) {
                // Reset specific provider
                $cacheKey = "provider_failure_{$providerName}";
                cache()->forget($cacheKey);
                
                Log::info('🔄 Provider failure history reset', [
                    'provider' => $providerName,
                    'reset_by' => auth()->user()?->id
                ]);
                
                $message = "{$providerName} provider hata geçmişi sıfırlandı";
            } else {
                // Reset all providers
                $providers = AIProvider::where('is_active', true)->get();
                
                foreach ($providers as $provider) {
                    $cacheKey = "provider_failure_{$provider->name}";
                    cache()->forget($cacheKey);
                }
                
                Log::info('🔄 All provider failure histories reset', [
                    'reset_by' => auth()->user()?->id
                ]);
                
                $message = 'Tüm provider hata geçmişleri sıfırlandı';
            }

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hata geçmişi sıfırlanamadı: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear fallback statistics
     */
    public function clearStatistics(Request $request): JsonResponse
    {
        try {
            $tenant = tenant();
            $tenantId = $tenant?->id ?? 'central';
            $cacheKey = "fallback_stats_{$tenantId}";
            
            cache()->forget($cacheKey);

            Log::info('🗑️ Fallback statistics cleared', [
                'tenant_id' => $tenantId,
                'cleared_by' => auth()->user()?->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Fallback istatistikleri temizlendi'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'İstatistikler temizlenemedi: ' . $e->getMessage()
            ], 500);
        }
    }
}