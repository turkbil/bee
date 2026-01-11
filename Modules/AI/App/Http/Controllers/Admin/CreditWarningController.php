<?php

declare(strict_types=1);

namespace Modules\AI\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Modules\AI\App\Services\CreditWarningService;
use Modules\AI\App\Services\ModelBasedCreditService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

/**
 * Credit Warning Admin Controller
 * 
 * Kredi uyarı sisteminin yönetimi ve monitoring
 */
class CreditWarningController extends Controller
{
    public function __construct(
        private CreditWarningService $warningService,
        private ModelBasedCreditService $creditService
    ) {}

    /**
     * Credit Warning Dashboard
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $tenant = tenant();
        $tenantId = $tenant?->id;
        
        // DEBUG: Log tenant information
        \Log::info('CreditWarningController::index called', [
            'tenant' => $tenant,
            'tenant_id' => $tenantId,
            'session_tenant_id' => session('admin_selected_tenant_id'),
            'request_host' => request()->getHost(),
            'request_url' => request()->fullUrl()
        ]);
        
        // Fallback tenant ID'si - admin.tenant.select middleware'ından al
        if (!$tenantId) {
            $tenantId = session('admin_selected_tenant_id', 1);
            // Mock tenant object oluştur
            $tenant = (object) ['id' => $tenantId];
            \Log::info('Using fallback tenant', ['tenant_id' => $tenantId]);
        }
        
        // Kredi istatistikleri
        $creditStats = $this->warningService->getCreditStatistics($tenantId);
        
        // Aktif uyarılar
        $activeWarnings = $this->warningService->getActiveWarnings($tenantId);
        
        // Son 30 günün kredi kullanım geçmişi
        $usageHistory = $this->getCreditUsageHistory($tenantId);
        
        // Uyarı konfigürasyonu
        $warningConfig = $this->getWarningConfiguration($tenantId);
        
        return view('ai::admin.credit-warnings.index', [
            'creditStats' => $creditStats,
            'activeWarnings' => $activeWarnings,
            'usageHistory' => $usageHistory,
            'warningConfig' => $warningConfig,
            'tenant' => $tenant
        ]);
    }

    /**
     * Credit Warning Konfigürasyon
     * 
     * @return \Illuminate\View\View
     */
    public function configuration()
    {
        $tenant = tenant();
        $tenantId = $tenant?->id;
        
        // Mevcut konfigürasyon
        $config = $this->getWarningConfiguration($tenantId);
        
        // Default warning levels
        $defaultLevels = CreditWarningService::WARNING_LEVELS;
        
        return view('ai::admin.credit-warnings.configuration', [
            'config' => $config,
            'defaultLevels' => $defaultLevels,
            'tenant' => $tenant
        ]);
    }

    /**
     * Credit Warning Test Paneli
     * 
     * @return \Illuminate\View\View
     */
    public function testing()
    {
        $tenant = tenant();
        
        return view('ai::admin.credit-warnings.testing', [
            'tenant' => $tenant,
            'currentCredits' => $tenant?->credits ?? 0,
            'maxCredits' => $tenant?->max_credits ?? 1000
        ]);
    }

    /**
     * Credit Warning Test Çalıştır
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function runTest(Request $request): JsonResponse
    {
        $request->validate([
            'test_credits' => 'required|numeric|min:0',
            'test_max_credits' => 'required|numeric|min:1',
            'estimated_tokens' => 'required|numeric|min:1'
        ]);

        try {
            $tenant = tenant();
            if (!$tenant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tenant not found'
                ], 404);
            }
            
            // Test tenant'ı oluştur
            $testTenant = (object) [
                'id' => $tenant->id,
                'credits' => $request->test_credits,
                'max_credits' => $request->test_max_credits
            ];
            
            // Default provider ve model (test için)
            $providerId = 1; // OpenAI
            $model = 'gpt-4o';
            $estimatedTokens = $request->estimated_tokens;
            
            // Test kredi kontrolü
            $result = $this->warningService->checkCreditsBeforeRequest(
                $testTenant,
                $providerId,
                $model,
                $estimatedTokens,
                $estimatedTokens * 0.5 // Output tokens estimate
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Credit warning test completed',
                'data' => [
                    'allowed' => $result['allowed'],
                    'warning' => $result['warning'],
                    'required_credits' => $result['required_credits'],
                    'current_credits' => $result['current_credits'],
                    'test_scenario' => [
                        'credits' => $request->test_credits,
                        'max_credits' => $request->test_max_credits,
                        'estimated_tokens' => $estimatedTokens
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Test failed: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Uyarıları Temizle
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function clearWarnings(Request $request): JsonResponse
    {
        try {
            $tenant = tenant();
            $tenantId = $tenant?->id;
            
            if (!$tenantId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tenant not found'
                ], 404);
            }
            
            $warningType = $request->input('warning_type'); // null = all warnings
            
            $this->warningService->clearWarnings($tenantId, $warningType);
            
            $message = $warningType 
                ? "Warning type '{$warningType}' cleared successfully"
                : 'All warnings cleared successfully';
            
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear warnings: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Anlık Kredi Durumu
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function currentStatus(Request $request): JsonResponse
    {
        try {
            $tenant = tenant();
            $tenantId = $tenant?->id;
            
            if (!$tenantId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tenant not found'
                ], 404);
            }
            
            // Güncel kredi istatistikleri
            $creditStats = $this->warningService->getCreditStatistics($tenantId);
            
            // Aktif uyarılar
            $activeWarnings = $this->warningService->getActiveWarnings($tenantId);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'credit_stats' => $creditStats,
                    'active_warnings' => $activeWarnings,
                    'last_updated' => now()->toISOString()
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get current status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Kredi Kullanım Geçmişi
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function usageHistory(Request $request): JsonResponse
    {
        try {
            $tenant = tenant();
            $tenantId = $tenant?->id;
            
            if (!$tenantId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tenant not found'
                ], 404);
            }
            
            $days = (int) $request->input('days', 30);
            $history = $this->getCreditUsageHistory($tenantId, $days);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'usage_history' => $history,
                    'period_days' => $days,
                    'generated_at' => now()->toISOString()
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get usage history: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Kredi kullanım geçmişini al
     * 
     * @param int $tenantId
     * @param int $days
     * @return array
     */
    private function getCreditUsageHistory(int $tenantId, int $days = 30): array
    {
        $history = [];
        
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $cacheKey = "daily_credit_usage_{$tenantId}_" . $date->format('Y-m-d');
            $usage = Cache::get($cacheKey, 0);
            
            $history[] = [
                'date' => $date->format('Y-m-d'),
                'formatted_date' => $date->format('d M'),
                'usage' => $usage,
                'day_name' => $date->format('l')
            ];
        }
        
        return $history;
    }

    /**
     * Uyarı konfigürasyonunu al
     * 
     * @param int|null $tenantId
     * @return array
     */
    private function getWarningConfiguration(?int $tenantId): array
    {
        $cacheKey = "credit_warning_config_" . ($tenantId ?? 'central');
        
        return Cache::get($cacheKey, [
            'enabled' => true,
            'email_notifications' => true,
            'warning_levels' => CreditWarningService::WARNING_LEVELS,
            'auto_pause_on_zero' => true,
            'daily_email_limit' => 1,
            'notification_channels' => ['email', 'dashboard'],
            'emergency_contacts' => []
        ]);
    }

    /**
     * Uyarı konfigürasyonunu güncelle
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function updateConfiguration(Request $request): JsonResponse
    {
        $request->validate([
            'enabled' => 'required|boolean',
            'email_notifications' => 'required|boolean',
            'auto_pause_on_zero' => 'required|boolean',
            'warning_levels.critical' => 'required|numeric|min:1|max:25',
            'warning_levels.low' => 'required|numeric|min:5|max:50',
            'warning_levels.medium' => 'required|numeric|min:10|max:75'
        ]);

        try {
            $tenant = tenant();
            $tenantId = $tenant?->id;
            
            if (!$tenantId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tenant not found'
                ], 404);
            }
            
            $config = [
                'enabled' => $request->boolean('enabled'),
                'email_notifications' => $request->boolean('email_notifications'),
                'auto_pause_on_zero' => $request->boolean('auto_pause_on_zero'),
                'warning_levels' => [
                    'critical' => $request->input('warning_levels.critical'),
                    'low' => $request->input('warning_levels.low'),
                    'medium' => $request->input('warning_levels.medium')
                ],
                'updated_at' => now()->toISOString(),
                'updated_by' => auth()->id()
            ];
            
            $cacheKey = "credit_warning_config_{$tenantId}";
            Cache::put($cacheKey, $config, now()->addDays(30));
            
            return response()->json([
                'success' => true,
                'message' => 'Warning configuration updated successfully',
                'config' => $config
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update configuration: ' . $e->getMessage()
            ], 500);
        }
    }
}