<?php

declare(strict_types=1);

namespace Modules\AI\App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\AI\App\Models\AICreditUsage;
use Modules\AI\App\Models\AIProvider;
use Modules\AI\App\Models\AIFeature;
use Modules\AI\App\Models\Conversation;
use App\Models\Tenant;

/**
 * Global AI Monitoring ve Analytics Service
 * 
 * Tüm AI kullanımlarını takip eder, kredi düşümlerini yönetir ve detaylı analytics sağlar.
 * Her AI işlemi bu service'den geçer ve kapsamlı monitoring yapılır.
 */
readonly class GlobalAIMonitoringService
{
    public function __construct(
        private AICreditService $creditService
    ) {}

    /**
     * AI kullanımını kapsamlı şekilde kaydet ve krediyi düş
     *
     * @param array $usageData AI kullanım verileri
     * @return array Kullanım sonucu ve debug verileri
     */
    public function recordAIUsage(array $usageData): array
    {
        $startTime = microtime(true);
        
        try {
            // Veri validasyonu
            $validatedData = $this->validateUsageData($usageData);
            
            // Kredi kontrolü
            if (!$this->hasEnoughCredits($validatedData['tenant_id'], $validatedData['estimated_credits'])) {
                return $this->createErrorResponse('insufficient_credits', 'Yeterli kredi yok', $startTime);
            }
            
            // AI provider bilgisini al
            $provider = $this->getActiveProvider();
            
            // Kullanım kaydı oluştur
            $usage = AICreditUsage::create([
                'tenant_id' => $validatedData['tenant_id'],
                'user_id' => $validatedData['user_id'],
                'conversation_id' => $validatedData['conversation_id'] ?? null,
                'feature_slug' => $validatedData['feature_slug'] ?? 'general',
                'provider_name' => $provider->name ?? 'unknown',
                'model_name' => $provider->model ?? 'unknown',
                'input_tokens' => $validatedData['input_tokens'],
                'output_tokens' => $validatedData['output_tokens'],
                'total_tokens' => $validatedData['input_tokens'] + $validatedData['output_tokens'],
                'credits_used' => $validatedData['credits_used'],
                'credit_cost' => $validatedData['credit_cost'] ?? 0.0,
                'request_type' => $validatedData['request_type'] ?? 'chat',
                'metadata' => json_encode([
                    'user_input' => $validatedData['user_input'] ?? '',
                    'ai_response' => $validatedData['ai_response'] ?? '',
                    'processing_time' => 0, // Will be calculated below
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'feature_category' => $validatedData['feature_category'] ?? null,
                    'success' => true
                ]),
                'used_at' => now(),
            ]);
            
            // Cache temizle
            $this->clearCaches($validatedData['tenant_id']);
            
            $processingTime = microtime(true) - $startTime;
            
            // Metadata'yı güncelle (processing time ile) - Double encoding'i önlemek için array olarak güncelle
            $metadata = json_decode($usage->metadata, true);
            $metadata['processing_time'] = round($processingTime * 1000, 2); // milliseconds
            $usage->update(['metadata' => $metadata]); // Laravel otomatik JSON encode eder
            
            // Debug log
            $this->logAIUsage($usage, $processingTime);
            
            return $this->createSuccessResponse($usage, $processingTime);
            
        } catch (\Exception $e) {
            Log::error('AI Usage Recording Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $usageData
            ]);
            
            return $this->createErrorResponse('recording_failed', $e->getMessage(), $startTime);
        }
    }
    
    /**
     * Konuşma tabanlı AI kullanımını kaydet
     */
    public function recordConversationUsage(
        int $conversationId,
        string $userMessage,
        string $aiResponse,
        array $tokenData,
        string $featureSlug = 'chat'
    ): array {
        $conversation = Conversation::find($conversationId);
        
        if (!$conversation) {
            return $this->createErrorResponse('conversation_not_found', 'Konuşma bulunamadı');
        }
        
        return $this->recordAIUsage([
            'tenant_id' => $conversation->tenant_id,
            'user_id' => $conversation->user_id,
            'conversation_id' => $conversationId,
            'feature_slug' => $featureSlug,
            'input_tokens' => $tokenData['input_tokens'] ?? 0,
            'output_tokens' => $tokenData['output_tokens'] ?? 0,
            'credits_used' => $tokenData['credits_used'] ?? 0,
            'credit_cost' => $tokenData['credit_cost'] ?? 0,
            'user_input' => $userMessage,
            'ai_response' => $aiResponse,
            'request_type' => 'conversation'
        ]);
    }
    
    /**
     * Feature tabanlı AI kullanımını kaydet
     */
    public function recordFeatureUsage(
        string $featureSlug,
        string $userInput,
        string $aiResponse,
        array $tokenData
    ): array {
        $feature = AIFeature::where('slug', $featureSlug)->first();
        
        return $this->recordAIUsage([
            'tenant_id' => tenant('id') ?? '1',
            'user_id' => auth()->id() ?? 1,
            'feature_slug' => $featureSlug,
            'input_tokens' => $tokenData['input_tokens'] ?? 0,
            'output_tokens' => $tokenData['output_tokens'] ?? 0,
            'credits_used' => $tokenData['credits_used'] ?? 0,
            'credit_cost' => $tokenData['credit_cost'] ?? 0,
            'user_input' => $userInput,
            'ai_response' => $aiResponse,
            'request_type' => 'feature',
            'feature_category' => $feature->category->title ?? null
        ]);
    }
    
    /**
     * Kapsamlı AI analytics verilerini al
     */
    public function getComprehensiveAnalytics(?string $tenantId = null, array $filters = []): array
    {
        $tenantId = $tenantId ?: tenant('id');
        $cacheKey = "ai_analytics_{$tenantId}_" . md5(json_encode($filters));
        
        return Cache::remember($cacheKey, 300, function () use ($tenantId, $filters) {
            $query = AICreditUsage::where('tenant_id', $tenantId);
            
            // Tarih filtreleri
            if (!empty($filters['date_from'])) {
                $query->whereDate('used_at', '>=', $filters['date_from']);
            }
            if (!empty($filters['date_to'])) {
                $query->whereDate('used_at', '<=', $filters['date_to']);
            }
            
            $usageData = $query->get();
            
            return [
                'summary' => $this->calculateSummaryStats($usageData),
                'provider_breakdown' => $this->calculateProviderBreakdown($usageData),
                'feature_breakdown' => $this->calculateFeatureBreakdown($usageData),
                'daily_usage' => $this->calculateDailyUsage($usageData),
                'hourly_patterns' => $this->calculateHourlyPatterns($usageData),
                'user_activity' => $this->calculateUserActivity($usageData),
                'cost_analysis' => $this->calculateCostAnalysis($usageData),
                'performance_metrics' => $this->calculatePerformanceMetrics($usageData),
            ];
        });
    }
    
    /**
     * Real-time monitoring verileri
     */
    public function getRealTimeMetrics(?string $tenantId = null): array
    {
        $tenantId = $tenantId ?: tenant('id');
        
        // Son 24 saatlik veriler
        $last24Hours = AICreditUsage::where('tenant_id', $tenantId)
            ->where('used_at', '>=', now()->subDay())
            ->get();
            
        // Son 1 saatlik veriler
        $lastHour = $last24Hours->where('used_at', '>=', now()->subHour());
        
        return [
            'current_balance' => ai_get_credit_balance($tenantId),
            'last_hour' => [
                'total_usage' => $lastHour->sum('credits_used'),
                'request_count' => $lastHour->count(),
                'avg_processing_time' => $this->calculateAvgProcessingTime($lastHour),
                'success_rate' => $this->calculateSuccessRate($lastHour),
            ],
            'last_24_hours' => [
                'total_usage' => $last24Hours->sum('credits_used'),
                'request_count' => $last24Hours->count(),
                'unique_users' => $last24Hours->pluck('user_id')->unique()->count(),
                'top_features' => $this->getTopFeatures($last24Hours),
            ],
            'system_health' => [
                'active_providers' => AIProvider::where('is_active', true)->count(),
                'total_conversations' => Conversation::where('tenant_id', $tenantId)->count(),
                'active_features' => AIFeature::where('status', 'active')->count(),
            ]
        ];
    }
    
    /**
     * Debug dashboard için özel veriler
     */
    public function getDebugData(?string $tenantId = null, int $limit = 100): array
    {
        $tenantId = $tenantId ?: tenant('id');
        
        $recentUsage = AICreditUsage::where('tenant_id', $tenantId)
            ->with(['user', 'tenant'])
            ->orderBy('used_at', 'desc')
            ->limit($limit)
            ->get();
            
        return [
            'recent_usage' => $recentUsage->map(function ($usage) {
                $metadata = json_decode($usage->metadata ?? '{}', true);
                return [
                    'id' => $usage->id,
                    'timestamp' => $usage->used_at->format('d.m.Y H:i:s'),
                    'user' => $usage->user->name ?? 'N/A',
                    'feature' => $usage->feature_slug,
                    'provider' => $usage->provider_name,
                    'tokens' => [
                        'input' => $usage->input_tokens,
                        'output' => $usage->output_tokens,
                        'total' => $usage->total_tokens
                    ],
                    'credits' => [
                        'used' => $usage->credits_used,
                        'cost' => $usage->credit_cost
                    ],
                    'performance' => [
                        'processing_time' => $metadata['processing_time'] ?? 0,
                        'success' => $metadata['success'] ?? true
                    ],
                    'metadata' => $metadata
                ];
            }),
            'error_logs' => $this->getRecentErrors($limit),
            'performance_issues' => $this->getPerformanceIssues($limit)
        ];
    }
    
    /**
     * Kullanım verilerini validate et
     */
    private function validateUsageData(array $data): array
    {
        return [
            'tenant_id' => $data['tenant_id'] ?? tenant('id') ?? '1',
            'user_id' => $data['user_id'] ?? auth()->id() ?? 1,
            'conversation_id' => $data['conversation_id'] ?? null,
            'feature_slug' => $data['feature_slug'] ?? 'general',
            'input_tokens' => (int) ($data['input_tokens'] ?? 0),
            'output_tokens' => (int) ($data['output_tokens'] ?? 0),
            'credits_used' => (float) ($data['credits_used'] ?? 0),
            'credit_cost' => (float) ($data['credit_cost'] ?? 0),
            'estimated_credits' => (float) ($data['credits_used'] ?? 0),
            'user_input' => $data['user_input'] ?? '',
            'ai_response' => $data['ai_response'] ?? '',
            'request_type' => $data['request_type'] ?? 'general',
            'feature_category' => $data['feature_category'] ?? null,
        ];
    }
    
    private function hasEnoughCredits(string $tenantId, float $creditsNeeded): bool
    {
        $currentBalance = ai_get_credit_balance($tenantId);
        return $currentBalance >= $creditsNeeded;
    }
    
    private function getActiveProvider(): ?AIProvider
    {
        return AIProvider::where('is_active', true)
            ->where('is_default', true)
            ->first();
    }
    
    private function logAIUsage(AICreditUsage $usage, float $processingTime): void
    {
        Log::info('AI Usage Recorded', [
            'usage_id' => $usage->id,
            'tenant_id' => $usage->tenant_id,
            'feature' => $usage->feature_slug,
            'provider' => $usage->provider_name,
            'tokens' => [
                'input' => $usage->input_tokens,
                'output' => $usage->output_tokens,
                'total' => $usage->total_tokens
            ],
            'credits' => [
                'used' => $usage->credits_used,
                'cost' => $usage->credit_cost
            ],
            'processing_time_ms' => round($processingTime * 1000, 2),
            'timestamp' => $usage->used_at->toISOString()
        ]);
    }
    
    private function clearCaches(string $tenantId): void
    {
        Cache::forget("ai_token_balance_{$tenantId}");
        Cache::forget("ai_token_stats_{$tenantId}");
        Cache::forget("ai_widget_stats_{$tenantId}");
        Cache::forget("ai_analytics_{$tenantId}_" . md5(''));
    }
    
    private function createSuccessResponse(AICreditUsage $usage, float $processingTime): array
    {
        return [
            'success' => true,
            'usage_id' => $usage->id,
            'credits_used' => $usage->credits_used,
            'processing_time_ms' => round($processingTime * 1000, 2),
            'remaining_balance' => ai_get_credit_balance($usage->tenant_id),
            'debug' => [
                'tenant_id' => $usage->tenant_id,
                'feature' => $usage->feature_slug,
                'provider' => $usage->provider_name,
                'tokens' => [
                    'input' => $usage->input_tokens,
                    'output' => $usage->output_tokens,
                    'total' => $usage->total_tokens
                ]
            ]
        ];
    }
    
    private function createErrorResponse(string $errorCode, string $message, float $startTime = 0): array
    {
        $processingTime = $startTime ? microtime(true) - $startTime : 0;
        
        return [
            'success' => false,
            'error_code' => $errorCode,
            'message' => $message,
            'processing_time_ms' => round($processingTime * 1000, 2),
            'timestamp' => now()->toISOString()
        ];
    }
    
    // Analytics helper methods
    private function calculateSummaryStats($usageData): array
    {
        return [
            'total_requests' => $usageData->count(),
            'total_credits' => $usageData->sum('credits_used'),
            'total_cost' => $usageData->sum('credit_cost'),
            'avg_tokens_per_request' => $usageData->avg('total_tokens'),
            'unique_users' => $usageData->pluck('user_id')->unique()->count(),
            'unique_features' => $usageData->pluck('feature_slug')->unique()->count(),
        ];
    }
    
    private function calculateProviderBreakdown($usageData): array
    {
        return $usageData->groupBy('provider_name')
            ->map(function ($group, $provider) {
                return [
                    'name' => $provider,
                    'requests' => $group->count(),
                    'credits' => $group->sum('credits_used'),
                    'avg_processing_time' => $this->calculateAvgProcessingTime($group)
                ];
            })->values()->toArray();
    }
    
    private function calculateFeatureBreakdown($usageData): array
    {
        return $usageData->groupBy('feature_slug')
            ->map(function ($group, $feature) {
                return [
                    'feature' => $feature,
                    'requests' => $group->count(),
                    'credits' => $group->sum('credits_used'),
                    'avg_tokens' => $group->avg('total_tokens')
                ];
            })->sortByDesc('requests')->take(10)->values()->toArray();
    }
    
    private function calculateDailyUsage($usageData): array
    {
        return $usageData->groupBy(function ($item) {
            return $item->used_at->format('Y-m-d');
        })->map(function ($group, $date) {
            return [
                'date' => $date,
                'requests' => $group->count(),
                'credits' => $group->sum('credits_used')
            ];
        })->sortBy('date')->values()->toArray();
    }
    
    private function calculateHourlyPatterns($usageData): array
    {
        return $usageData->groupBy(function ($item) {
            return $item->used_at->format('H');
        })->map(function ($group, $hour) {
            return [
                'hour' => (int) $hour,
                'requests' => $group->count(),
                'credits' => $group->sum('credits_used')
            ];
        })->sortBy('hour')->values()->toArray();
    }
    
    private function calculateUserActivity($usageData): array
    {
        return $usageData->groupBy('user_id')
            ->map(function ($group, $userId) {
                return [
                    'user_id' => $userId,
                    'requests' => $group->count(),
                    'credits' => $group->sum('credits_used'),
                    'last_activity' => $group->max('used_at')
                ];
            })->sortByDesc('requests')->take(10)->values()->toArray();
    }
    
    private function calculateCostAnalysis($usageData): array
    {
        return [
            'total_cost' => $usageData->sum('credit_cost'),
            'avg_cost_per_request' => $usageData->avg('credit_cost'),
            'cost_by_feature' => $usageData->groupBy('feature_slug')
                ->map(function ($group, $feature) {
                    return [
                        'feature' => $feature,
                        'total_cost' => $group->sum('credit_cost'),
                        'requests' => $group->count()
                    ];
                })->sortByDesc('total_cost')->take(10)->values()->toArray()
        ];
    }
    
    private function calculatePerformanceMetrics($usageData): array
    {
        $processingTimes = $usageData->map(function ($usage) {
            $metadata = json_decode($usage->metadata ?? '{}', true);
            return $metadata['processing_time'] ?? 0;
        })->filter(function ($time) {
            return $time > 0;
        });
        
        return [
            'avg_processing_time' => $processingTimes->avg(),
            'max_processing_time' => $processingTimes->max(),
            'min_processing_time' => $processingTimes->min(),
            'success_rate' => $this->calculateSuccessRate($usageData)
        ];
    }
    
    private function calculateAvgProcessingTime($usageData): float
    {
        $times = $usageData->map(function ($usage) {
            $metadata = json_decode($usage->metadata ?? '{}', true);
            return $metadata['processing_time'] ?? 0;
        })->filter(function ($time) {
            return $time > 0;
        });
        
        return $times->count() > 0 ? round($times->avg(), 2) : 0;
    }
    
    private function calculateSuccessRate($usageData): float
    {
        if ($usageData->count() === 0) return 100;
        
        $successCount = $usageData->filter(function ($usage) {
            $metadata = json_decode($usage->metadata ?? '{}', true);
            return $metadata['success'] ?? true;
        })->count();
        
        return round(($successCount / $usageData->count()) * 100, 2);
    }
    
    private function getTopFeatures($usageData, int $limit = 5): array
    {
        return $usageData->groupBy('feature_slug')
            ->map(function ($group, $feature) {
                return [
                    'feature' => $feature,
                    'requests' => $group->count()
                ];
            })->sortByDesc('requests')->take($limit)->values()->toArray();
    }
    
    private function getRecentErrors(int $limit): array
    {
        // Laravel log dosyasından son hataları al
        try {
            $logPath = storage_path('logs/laravel.log');
            if (!file_exists($logPath)) {
                return [];
            }
            
            $lines = array_slice(file($logPath), -$limit * 10);
            $errors = [];
            
            foreach ($lines as $line) {
                if (strpos($line, 'ERROR') !== false && strpos($line, 'AI') !== false) {
                    $errors[] = trim($line);
                }
            }
            
            return array_slice($errors, -$limit);
        } catch (\Exception $e) {
            return [];
        }
    }
    
    private function getPerformanceIssues(int $limit): array
    {
        return AICreditUsage::whereRaw("JSON_EXTRACT(metadata, '$.processing_time') > 5000")
            ->orderBy('used_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($usage) {
                $metadata = json_decode($usage->metadata ?? '{}', true);
                return [
                    'timestamp' => $usage->used_at->format('d.m.Y H:i:s'),
                    'feature' => $usage->feature_slug,
                    'processing_time' => $metadata['processing_time'] ?? 0,
                    'provider' => $usage->provider_name
                ];
            })->toArray();
    }
}