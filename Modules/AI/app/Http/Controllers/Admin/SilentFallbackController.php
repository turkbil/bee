<?php

declare(strict_types=1);

namespace Modules\AI\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Modules\AI\App\Services\SilentFallbackService;
use Modules\AI\App\Models\AIProvider;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

/**
 * Silent Fallback Admin Controller
 * 
 * Silent fallback sisteminin yönetimi ve istatistikleri
 */
class SilentFallbackController extends Controller
{
    public function __construct(
        private SilentFallbackService $fallbackService
    ) {}

    /**
     * Silent Fallback Dashboard
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $tenant = tenant();
        $tenantId = $tenant?->id;
        
        // Fallback tenant ID'si - admin.tenant.select middleware'ından al
        if (!$tenantId) {
            $tenantId = session('admin_selected_tenant_id', 1);
            $tenant = (object) ['id' => $tenantId];
        }
        
        // Fallback istatistikleri
        $stats = $this->fallbackService->getFallbackStats($tenantId);
        
        // Provider durumları
        $providers = AIProvider::where('is_active', true)
                              ->with(['modelCreditRates' => function($query) {
                                  $query->where('is_active', true);
                              }])
                              ->get();
        
        $providerStatus = [];
        foreach ($providers as $provider) {
            try {
                $isAvailable = method_exists($provider, 'isAvailable') ? $provider->isAvailable() : true;
            } catch (\Exception $e) {
                $isAvailable = false;
            }
            
            $providerStatus[] = [
                'id' => $provider->id,
                'name' => $provider->name,
                'is_available' => $isAvailable,
                'default_model' => $provider->default_model ?? 'N/A',
                'model_count' => $provider->modelCreditRates->count(),
                'priority' => $provider->priority ?? 1,
                'last_error' => $this->getLastProviderError($provider->name),
                'fallback_priority' => $this->getFallbackPriority($provider->name)
            ];
        }
        
        // Son 7 günün fallback geçmişi
        $recentHistory = $this->getRecentFallbackHistory($tenantId);
        
        return view('ai::admin.silent-fallback.index', [
            'stats' => $stats,
            'providers' => $providerStatus,
            'recent_fallbacks' => $recentHistory, // View'da bu değişken kullanılıyor
            'recentHistory' => $recentHistory,
            'tenantId' => $tenantId
        ]);
    }

    /**
     * Fallback Konfigürasyon Sayfası
     * 
     * @return \Illuminate\View\View
     */
    public function configuration()
    {
        $tenant = tenant();
        $tenantId = $tenant?->id;
        
        // Mevcut fallback ayarları
        $config = $this->getFallbackConfiguration($tenantId);
        
        // Provider'lar ve modeller
        $providers = AIProvider::where('is_active', true)
                              ->with(['modelCreditRates' => function($query) {
                                  $query->where('is_active', true)
                                        ->orderBy('credit_per_1k_input_tokens', 'asc');
                              }])
                              ->get();
        
        return view('ai::admin.silent-fallback.configuration', [
            'config' => $config,
            'providers' => $providers,
            'tenantId' => $tenantId
        ]);
    }

    /**
     * Fallback Test Paneli
     * 
     * @return \Illuminate\View\View
     */
    public function testing()
    {
        $providers = AIProvider::where('is_active', true)->get();
        
        return view('ai::admin.silent-fallback.testing', [
            'providers' => $providers
        ]);
    }

    /**
     * Fallback Test Çalıştır
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function test(Request $request): JsonResponse
    {
        $request->validate([
            'provider' => 'required|string',
            'model' => 'required|string',
            'test_prompt' => 'required|string|min:10|max:1000'
        ]);

        try {
            $startTime = microtime(true);
            
            // Test fallback'i çalıştır
            $result = $this->fallbackService->attemptSilentFallback(
                $request->provider,
                $request->model,
                $request->test_prompt,
                ['test_mode' => true],
                'Manual test simulation'
            );
            
            $executionTime = microtime(true) - $startTime;
            
            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Fallback test successful',
                    'data' => [
                        'fallback_provider' => $result['provider']->name,
                        'fallback_model' => $result['model'],
                        'execution_time' => round($executionTime * 1000, 2) . 'ms',
                        'original_provider' => $request->provider,
                        'original_model' => $request->model
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'All fallback attempts failed',
                    'data' => [
                        'execution_time' => round($executionTime * 1000, 2) . 'ms',
                        'original_provider' => $request->provider,
                        'original_model' => $request->model
                    ]
                ]);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Test failed: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Fallback İstatistiklerini Temizle
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function clearStats(Request $request): JsonResponse
    {
        try {
            $tenant = tenant();
            $tenantId = $tenant?->id;
            
            $this->fallbackService->clearFallbackStats($tenantId);
            
            return response()->json([
                'success' => true,
                'message' => 'Fallback statistics cleared successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Provider'ın son hatasını al
     * 
     * @param string $providerName
     * @return array|null
     */
    private function getLastProviderError(string $providerName): ?array
    {
        $cacheKey = "provider_last_error_{$providerName}";
        return Cache::get($cacheKey);
    }

    /**
     * Provider'ın fallback önceliğini al
     * 
     * @param string $providerName
     * @return int
     */
    private function getFallbackPriority(string $providerName): int
    {
        $priorities = [
            'OpenAI' => 1,
            'Anthropic' => 2,
            'DeepSeek' => 3
        ];
        
        return $priorities[$providerName] ?? 99;
    }

    /**
     * Son fallback geçmişini al
     * 
     * @param int|null $tenantId
     * @return array
     */
    private function getRecentFallbackHistory(?int $tenantId): array
    {
        $cacheKey = "fallback_stats_" . ($tenantId ?? 'central');
        $stats = Cache::get($cacheKey, ['daily' => []]);
        
        $history = [];
        $dates = array_slice(array_keys($stats['daily']), -7, 7, true);
        
        foreach ($dates as $date) {
            $history[] = [
                'date' => $date,
                'count' => $stats['daily'][$date],
                'formatted_date' => \Carbon\Carbon::parse($date)->format('d M')
            ];
        }
        
        return $history;
    }

    /**
     * Analytics sayfası
     * 
     * @return \Illuminate\View\View
     */
    public function analytics()
    {
        $tenant = tenant();
        $tenantId = $tenant?->id;
        
        // Detaylı istatistikler
        $stats = $this->fallbackService->getFallbackStats($tenantId);
        
        // Provider başarı oranları
        $providers = AIProvider::where('is_active', true)->get();
        $providerStats = [];
        
        foreach ($providers as $provider) {
            $providerStats[] = [
                'name' => $provider->name,
                'fallback_count' => $this->getProviderFallbackCount($provider->name, $tenantId),
                'success_rate' => $this->getProviderSuccessRate($provider->name, $tenantId),
                'avg_response_time' => $this->getProviderAvgResponseTime($provider->name, $tenantId)
            ];
        }
        
        return view('ai::admin.silent-fallback.analytics', [
            'stats' => $stats,
            'providerStats' => $providerStats,
            'tenantId' => $tenantId
        ]);
    }
    
    /**
     * Fallback konfigürasyonunu al
     * 
     * @param int|null $tenantId
     * @return array
     */
    private function getFallbackConfiguration(?int $tenantId): array
    {
        $cacheKey = "fallback_config_" . ($tenantId ?? 'central');
        
        return Cache::get($cacheKey, [
            'enabled' => true,
            'max_attempts' => 3,
            'timeout_seconds' => 30,
            'cost_threshold' => 1000, // Max 1000 credits for fallback
            'preferred_order' => ['OpenAI', 'Anthropic', 'DeepSeek'],
            'long_context_models' => [
                'claude-3-5-sonnet-20241022',
                'gpt-4o',
                'gpt-4o-mini',
                'deepseek-chat'
            ]
        ]);
    }

    /**
     * Provider'ın fallback sayısını al
     */
    private function getProviderFallbackCount(string $providerName, ?int $tenantId): int
    {
        $cacheKey = "provider_fallback_count_{$providerName}_" . ($tenantId ?? 'central');
        return Cache::get($cacheKey, 0);
    }

    /**
     * Provider'ın başarı oranını al
     */
    private function getProviderSuccessRate(string $providerName, ?int $tenantId): float
    {
        $cacheKey = "provider_success_rate_{$providerName}_" . ($tenantId ?? 'central');
        return Cache::get($cacheKey, 95.0); // Default %95
    }

    /**
     * Provider'ın ortalama yanıt süresini al
     */
    private function getProviderAvgResponseTime(string $providerName, ?int $tenantId): float
    {
        $cacheKey = "provider_avg_response_{$providerName}_" . ($tenantId ?? 'central');
        return Cache::get($cacheKey, 1500.0); // Default 1.5s
    }
}