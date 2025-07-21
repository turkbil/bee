<?php

/**
 * AI CREDIT HELPER FUNCTIONS - YENİ KREDİ SİSTEMİ
 * 
 * Token sisteminden credit sistemine geçiş:
 * - ai_use_tokens() → ai_use_credits()
 * - ai_get_token_balance() → ai_get_credit_balance()
 * - ai_can_use_tokens() → ai_can_use_credits()
 * 
 * Bu dosya composer.json autoload files bölümüne eklenerek
 * tüm projede global fonksiyonlar olarak kullanılabilir.
 */

use App\Models\Tenant;
use Modules\AI\App\Models\AICreditUsage;
use Modules\AI\App\Models\AIProvider;
use Illuminate\Support\Facades\Log;

// ============================================================================
// KREDİ KULLANIM FONKSİYONLARI
// ============================================================================

if (!function_exists('ai_use_credits')) {
    /**
     * AI kredi kullanımı - yeni credit sistemi
     * 
     * @param float $creditAmount Kullanılacak kredi miktarı
     * @param string|null $tenantId Tenant ID (null = mevcut tenant)
     * @param array $metadata Kullanım metadata'sı
     * @return bool Başarılı mı?
     */
    function ai_use_credits(float $creditAmount, ?string $tenantId = null, array $metadata = []): bool
    {
        try {
            // Admin paneli için - kullanıcının default tenant'ını al
            if (!$tenantId) {
                $tenantId = tenant('id') ?: null;
                
                // Tenant context yok (admin panel) - kullanıcının default tenant'ını bul
                if (!$tenantId && auth()->check()) {
                    $user = auth()->user();
                    $tenantId = $user->tenant_id ?? null;
                    
                    // Hala tenant yok - ilk tenant'ı al (development için)
                    if (!$tenantId) {
                        $firstTenant = Tenant::first();
                        $tenantId = $firstTenant?->id;
                    }
                }
                
                // Son çare
                if (!$tenantId) {
                    Log::error('ai_use_credits: No tenant context found');
                    return false;
                }
            }
            
            // Tenant'ı bul
            $tenant = is_numeric($tenantId) ? Tenant::find($tenantId) : null;
            if (!$tenant) {
                Log::error('ai_use_credits: Tenant not found', ['tenant_id' => $tenantId]);
                return false;
            }
            
            // Credit kontrolü
            if (!ai_can_use_credits($creditAmount, $tenantId)) {
                Log::warning('ai_use_credits: Insufficient credits', [
                    'tenant_id' => $tenantId,
                    'required_credits' => $creditAmount,
                    'current_balance' => ai_get_credit_balance($tenantId)
                ]);
                return false;
            }
            
            // Credit kullanımını kaydet
            AICreditUsage::create([
                'tenant_id' => $tenant->id,
                'user_id' => auth()->id(),
                'credits_used' => $creditAmount,
                'input_tokens' => $metadata['input_tokens'] ?? 0,
                'output_tokens' => $metadata['output_tokens'] ?? 0,
                'credit_cost' => $creditAmount,
                'usage_type' => $metadata['usage_type'] ?? 'ai_general',
                'description' => $metadata['description'] ?? 'AI credit usage',
                'reference_id' => $metadata['reference_id'] ?? null,
                'provider_name' => $metadata['provider_name'] ?? ai_get_active_provider_name(),
                'feature_slug' => $metadata['feature_slug'] ?? null,
                'metadata' => $metadata,
                'used_at' => $metadata['used_at'] ?? now()
            ]);
            
            // Tenant credit balance güncelle (eğer böyle bir alan varsa)
            if (method_exists($tenant, 'decrementCredit')) {
                $tenant->decrementCredit($creditAmount);
            }
            
            Log::info('✅ AI Credits used successfully', [
                'tenant_id' => $tenantId,
                'credits_used' => $creditAmount,
                'usage_type' => $metadata['usage_type'] ?? 'ai_general',
                'provider' => $metadata['provider_name'] ?? 'unknown'
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('ai_use_credits error', [
                'tenant_id' => $tenantId,
                'credit_amount' => $creditAmount,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}

if (!function_exists('ai_can_use_credits')) {
    /**
     * Kredi kullanım kontrol - yeni credit sistemi
     * 
     * @param float $creditAmount Gerekli kredi miktarı
     * @param string|null $tenantId Tenant ID
     * @return bool Kullanılabilir mi?
     */
    function ai_can_use_credits(float $creditAmount, ?string $tenantId = null): bool
    {
        try {
            $tenantId = $tenantId ?: (tenant('id') ?: 'default');
            $currentBalance = ai_get_credit_balance($tenantId);
            
            Log::debug('ai_can_use_credits check', [
                'tenant_id' => $tenantId,
                'required_credits' => $creditAmount,
                'current_balance' => $currentBalance,
                'can_use' => $currentBalance >= $creditAmount
            ]);
            
            return $currentBalance >= $creditAmount;
            
        } catch (\Exception $e) {
            Log::error('ai_can_use_credits error', [
                'tenant_id' => $tenantId,
                'credit_amount' => $creditAmount,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}

if (!function_exists('ai_get_credit_balance')) {
    /**
     * Mevcut kredi bakiyesi - yeni credit sistemi
     * 
     * @param string|null $tenantId Tenant ID
     * @return float Mevcut kredi bakiyesi
     */
    function ai_get_credit_balance(?string $tenantId = null): float
    {
        try {
            // Admin paneli için - kullanıcının default tenant'ını al
            if (!$tenantId) {
                $tenantId = tenant('id') ?: null;
                
                // Tenant context yok (admin panel) - kullanıcının default tenant'ını bul
                if (!$tenantId && auth()->check()) {
                    $user = auth()->user();
                    $tenantId = $user->tenant_id ?? null;
                    
                    // Hala tenant yok - ilk tenant'ı al (development için)
                    if (!$tenantId) {
                        $firstTenant = Tenant::first();
                        $tenantId = $firstTenant?->id;
                    }
                }
                
                // Son çare
                if (!$tenantId) {
                    Log::warning('ai_get_credit_balance: No tenant context found');
                    return 0.0;
                }
            }
            
            // Tenant'ı bul
            $tenant = is_numeric($tenantId) ? Tenant::find($tenantId) : null;
            if (!$tenant) {
                Log::warning('ai_get_credit_balance: Tenant not found', ['tenant_id' => $tenantId]);
                return 0.0;
            }
            
            // Credit balance alanı varsa kullan
            if (isset($tenant->ai_credits_balance)) {
                return (float) $tenant->ai_credits_balance;
            }
            
            // Fallback: Token balance'ı credit'e çevir (legacy support)
            if (isset($tenant->ai_tokens_balance)) {
                $tokenBalance = (float) $tenant->ai_tokens_balance;
                // Token → Credit conversion rate (örnek: 1000 token = 1 credit)
                return round($tokenBalance / 1000, 4);
            }
            
            // Son fallback: Satın alınan - kullanılan hesaplama
            $totalPurchased = ai_get_total_credits_purchased($tenantId);
            $totalUsed = ai_get_total_credits_used($tenantId);
            
            return max(0, $totalPurchased - $totalUsed);
            
        } catch (\Exception $e) {
            Log::error('ai_get_credit_balance error', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);
            return 0.0;
        }
    }
}

// ============================================================================
// KREDİ İSTATİSTİK FONKSİYONLARI
// ============================================================================

if (!function_exists('ai_get_total_credits_used')) {
    /**
     * Toplam kullanılan kredi miktarı
     * 
     * @param string|null $tenantId Tenant ID
     * @return float Toplam kullanılan kredi
     */
    function ai_get_total_credits_used(?string $tenantId = null): float
    {
        try {
            $tenantId = $tenantId ?: (tenant('id') ?: 'default');
            
            $totalUsed = AICreditUsage::where('tenant_id', $tenantId)
                ->sum('credit_cost');
            
            return (float) $totalUsed;
            
        } catch (\Exception $e) {
            Log::error('ai_get_total_credits_used error', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);
            return 0.0;
        }
    }
}

if (!function_exists('ai_get_total_credits_purchased')) {
    /**
     * Toplam satın alınan kredi miktarı
     * 
     * @param string|null $tenantId Tenant ID
     * @return float Toplam satın alınan kredi
     */
    function ai_get_total_credits_purchased(?string $tenantId = null): float
    {
        try {
            $tenantId = $tenantId ?: (tenant('id') ?: 'default');
            
            // Credit purchase tablosu varsa oradan al
            if (class_exists('Modules\AI\App\Models\AICreditPurchase')) {
                $totalPurchased = \Modules\AI\App\Models\AICreditPurchase::where('tenant_id', $tenantId)
                    ->where('status', 'completed')
                    ->sum('credit_amount');
                
                return (float) $totalPurchased;
            }
            
            // Fallback: Token purchase'lardan hesapla
            if (class_exists('Modules\AI\App\Models\AITokenPurchase')) {
                $totalTokensPurchased = \Modules\AI\App\Models\AITokenPurchase::where('tenant_id', $tenantId)
                    ->where('status', 'completed')
                    ->sum('token_amount');
                
                // Token → Credit conversion (örnek: 1000 token = 1 credit)
                return round($totalTokensPurchased / 1000, 4);
            }
            
            // Default: Varsayılan kredi bakiyesi
            return 100.0; // Yeni tenant'lara varsayılan 100 kredi
            
        } catch (\Exception $e) {
            Log::error('ai_get_total_credits_purchased error', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);
            return 0.0;
        }
    }
}

if (!function_exists('ai_get_monthly_credits_used')) {
    /**
     * Bu ay kullanılan kredi miktarı
     * 
     * @param string|null $tenantId Tenant ID
     * @return float Bu ay kullanılan kredi
     */
    function ai_get_monthly_credits_used(?string $tenantId = null): float
    {
        try {
            $tenantId = $tenantId ?: (tenant('id') ?: 'default');
            
            $monthlyUsed = AICreditUsage::where('tenant_id', $tenantId)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('credit_cost');
            
            return (float) $monthlyUsed;
            
        } catch (\Exception $e) {
            Log::error('ai_get_monthly_credits_used error', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);
            return 0.0;
        }
    }
}

if (!function_exists('ai_get_daily_credits_used')) {
    /**
     * Bugün kullanılan kredi miktarı
     * 
     * @param string|null $tenantId Tenant ID
     * @return float Bugün kullanılan kredi
     */
    function ai_get_daily_credits_used(?string $tenantId = null): float
    {
        try {
            $tenantId = $tenantId ?: (tenant('id') ?: 'default');
            
            $dailyUsed = AICreditUsage::where('tenant_id', $tenantId)
                ->whereDate('created_at', now()->toDateString())
                ->sum('credit_cost');
            
            return (float) $dailyUsed;
            
        } catch (\Exception $e) {
            Log::error('ai_get_daily_credits_used error', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);
            return 0.0;
        }
    }
}

// ============================================================================
// KREDİ HESAPLAMA FONKSİYONLARI
// ============================================================================

if (!function_exists('ai_calculate_credit_cost')) {
    /**
     * AI işlemi için kredi maliyeti hesaplama - TOKEN BAZLI GERÇEKÇİ MALİYET
     * 
     * @param string $operation İşlem türü (chat, feature, analysis, etc.)
     * @param array $params İşlem parametreleri: input_tokens, output_tokens, provider_name
     * @return float Gerçek kredi maliyeti
     */
    function ai_calculate_credit_cost(string $operation, array $params = []): float
    {
        try {
            $inputTokens = $params['input_tokens'] ?? 0;
            $outputTokens = $params['output_tokens'] ?? 0;
            $providerName = $params['provider_name'] ?? ai_get_active_provider_name();
            
            // Provider-specific token maliyetleri (real world pricing)
            $tokenPricing = [
                'claude' => [
                    'input_per_1k' => 0.00025,  // Claude Haiku: $0.25 per 1M input tokens
                    'output_per_1k' => 0.00125  // Claude Haiku: $1.25 per 1M output tokens
                ],
                'deepseek' => [
                    'input_per_1k' => 0.00014,  // DeepSeek: $0.14 per 1M tokens
                    'output_per_1k' => 0.00028  // DeepSeek: $0.28 per 1M tokens  
                ],
                'openai' => [
                    'input_per_1k' => 0.00015,  // GPT-4o-mini: $0.15 per 1M input tokens
                    'output_per_1k' => 0.0006   // GPT-4o-mini: $0.60 per 1M output tokens
                ]
            ];
            
            // Provider pricing'ini al
            $pricing = $tokenPricing[$providerName] ?? $tokenPricing['claude']; // Default Claude
            
            // Gerçek token maliyetini hesapla
            $inputCost = ($inputTokens / 1000) * $pricing['input_per_1k'];
            $outputCost = ($outputTokens / 1000) * $pricing['output_per_1k'];
            $totalCost = $inputCost + $outputCost;
            
            // Provider/Model multiplier (config'den)
            $modelName = $params['model'] ?? null;
            $providerMultiplier = ai_get_provider_multiplier($providerName, $modelName);
            
            // Feature complexity çarpanı
            $featureMultiplier = ai_get_feature_multiplier($params['feature_slug'] ?? null);
            
            // Final maliyet hesaplama
            $finalCost = $totalCost * $providerMultiplier * $featureMultiplier;
            
            // Minimum maliyet (0.001 kredi = ~$0.0015)
            $finalCost = max($finalCost, 0.001);
            
            Log::info('🧮 Token-based cost calculation', [
                'provider' => $providerName,
                'model' => $modelName,
                'input_tokens' => $inputTokens,
                'output_tokens' => $outputTokens,
                'input_cost' => round($inputCost, 6),
                'output_cost' => round($outputCost, 6),
                'base_cost' => round($totalCost, 6),
                'provider_multiplier' => $providerMultiplier,
                'feature_multiplier' => $featureMultiplier,
                'final_cost' => round($finalCost, 6)
            ]);
            
            return round($finalCost, 6);
            
        } catch (\Exception $e) {
            Log::error('ai_calculate_credit_cost error', [
                'operation' => $operation,
                'params' => $params,
                'error' => $e->getMessage()
            ]);
            return 0.001; // Fallback minimum cost
        }
    }
}

if (!function_exists('ai_get_provider_multiplier')) {
    /**
     * Provider ve model bazlı maliyet çarpanı - AKILLI SİSTEM
     * 
     * @param string|null $providerName Provider adı
     * @param string|null $modelName Model adı
     * @return float Maliyet çarpanı
     */
    function ai_get_provider_multiplier(?string $providerName = null, ?string $modelName = null): float
    {
        if (!$providerName) {
            $providerName = ai_get_active_provider_name();
        }
        
        // 1. Önce model-specific multiplier'ı kontrol et (daha spesifik)
        if ($modelName) {
            $modelMultipliers = config('ai.credit_management.model_multipliers', []);
            if (isset($modelMultipliers[$modelName])) {
                Log::debug('Using model-specific multiplier', [
                    'model' => $modelName,
                    'multiplier' => $modelMultipliers[$modelName]
                ]);
                return (float) $modelMultipliers[$modelName];
            }
        }
        
        // 2. Provider-specific fallback multiplier
        $providerMultipliers = config('ai.credit_management.provider_multipliers', []);
        $multiplier = $providerMultipliers[$providerName] ?? $providerMultipliers['default'] ?? 1.0;
        
        Log::debug('Using provider-specific multiplier', [
            'provider' => $providerName,
            'model' => $modelName,
            'multiplier' => $multiplier,
            'fallback_used' => !isset($providerMultipliers[$providerName])
        ]);
        
        return (float) $multiplier;
    }
}

if (!function_exists('ai_get_feature_multiplier')) {
    /**
     * Feature bazlı maliyet çarpanı
     * 
     * @param string|null $featureSlug Feature slug'ı
     * @return float Maliyet çarpanı
     */
    function ai_get_feature_multiplier(?string $featureSlug = null): float
    {
        if (!$featureSlug) {
            return 1.0;
        }
        
        // Feature complexity'ye göre çarpan
        try {
            $feature = \Modules\AI\App\Models\AIFeature::where('slug', $featureSlug)->first();
            if (!$feature) {
                return 1.0;
            }
            
            return match($feature->complexity_level) {
                'simple' => 0.8,
                'medium' => 1.0,
                'complex' => 1.5,
                'advanced' => 2.0,
                default => 1.0
            };
            
        } catch (\Exception $e) {
            Log::error('ai_get_feature_multiplier error', [
                'feature_slug' => $featureSlug,
                'error' => $e->getMessage()
            ]);
            return 1.0;
        }
    }
}

// ============================================================================
// PROVIDER HELPER FONKSİYONLARI
// ============================================================================

if (!function_exists('ai_get_active_provider_name')) {
    /**
     * Aktif provider adını döndür
     * 
     * @return string Provider adı
     */
    function ai_get_active_provider_name(): string
    {
        try {
            $provider = AIProvider::where('is_active', true)
                ->orderBy('priority', 'asc')
                ->first();
                
            return $provider ? $provider->name : 'unknown';
            
        } catch (\Exception $e) {
            Log::error('ai_get_active_provider_name error', ['error' => $e->getMessage()]);
            return 'unknown';
        }
    }
}

if (!function_exists('ai_get_provider_info')) {
    /**
     * Provider bilgilerini döndür
     * 
     * @param string|null $providerName Provider adı
     * @return array Provider bilgileri
     */
    function ai_get_provider_info(?string $providerName = null): array
    {
        try {
            $query = AIProvider::where('is_active', true);
            
            if ($providerName) {
                $query->where('name', $providerName);
            } else {
                $query->orderBy('priority', 'asc');
            }
            
            $provider = $query->first();
            
            if (!$provider) {
                return [
                    'name' => 'unknown',
                    'display_name' => 'Unknown Provider',
                    'priority' => 999,
                    'model' => 'unknown',
                    'multiplier' => 1.0
                ];
            }
            
            return [
                'name' => $provider->name,
                'display_name' => $provider->display_name,
                'priority' => $provider->priority,
                'model' => $provider->default_model,
                'multiplier' => ai_get_provider_multiplier($provider->name),
                'response_time' => $provider->average_response_time,
                'is_default' => $provider->is_default
            ];
            
        } catch (\Exception $e) {
            Log::error('ai_get_provider_info error', [
                'provider_name' => $providerName,
                'error' => $e->getMessage()
            ]);
            
            return [
                'name' => 'error',
                'display_name' => 'Error',
                'priority' => 999,
                'model' => 'error',
                'multiplier' => 1.0
            ];
        }
    }
}

// ============================================================================
// MERKEZİ KREDİ DÜŞME SİSTEMİ - OTOMATIK HESAPLAMA
// ============================================================================

if (!function_exists('ai_use_calculated_credits')) {
    /**
     * Merkezi kredi düşme sistemi - provider response'undan otomatik hesaplama
     * 
     * @param array $apiResponse AI provider'dan dönen yanıt (tokens_used, input_tokens, output_tokens)
     * @param string $providerName Provider adı (claude, deepseek, openai)
     * @param array $metadata İşlem metadata'sı
     * @return bool Başarılı mı?
     */
    function ai_use_calculated_credits(array $apiResponse, string $providerName, array $metadata = []): bool
    {
        try {
            // Token bilgilerini API response'undan al
            $totalTokens = $apiResponse['tokens_used'] ?? 0;
            $inputTokens = $apiResponse['input_tokens'] ?? 0;
            $outputTokens = $apiResponse['output_tokens'] ?? 0;
            
            // Input/output ayrımı yoksa total'i yarı yarıya böl (estimation)
            if ($totalTokens > 0 && $inputTokens == 0 && $outputTokens == 0) {
                $inputTokens = (int) round($totalTokens * 0.6); // Genelde input daha fazla
                $outputTokens = (int) round($totalTokens * 0.4); // Output daha az
            }
            
            // Token-based gerçek kredi maliyetini hesapla
            $creditCost = ai_calculate_credit_cost('api_usage', [
                'input_tokens' => $inputTokens,
                'output_tokens' => $outputTokens,
                'provider_name' => $providerName,
                'feature_slug' => $metadata['feature_slug'] ?? null
            ]);
            
            // Tenant ID belirle
            $tenantId = $metadata['tenant_id'] ?? (tenant('id') ?: 'default');
            
            // Gerçek kredi kullanımını kaydet
            $success = ai_use_credits($creditCost, $tenantId, array_merge($metadata, [
                'provider_name' => $providerName,
                'input_tokens' => $inputTokens,
                'output_tokens' => $outputTokens,
                'total_tokens' => $totalTokens,
                'calculated_cost' => $creditCost,
                'cost_basis' => 'token_usage'
            ]));
            
            Log::info('🎯 Merkezi kredi sistemi - otomatik hesaplama', [
                'provider' => $providerName,
                'input_tokens' => $inputTokens,
                'output_tokens' => $outputTokens,
                'total_tokens' => $totalTokens,
                'calculated_credits' => $creditCost,
                'tenant_id' => $tenantId,
                'success' => $success,
                'usage_type' => $metadata['usage_type'] ?? 'unknown'
            ]);
            
            return $success;
            
        } catch (\Exception $e) {
            Log::error('ai_use_calculated_credits error', [
                'api_response' => $apiResponse,
                'provider_name' => $providerName,
                'metadata' => $metadata,
                'error' => $e->getMessage()
            ]);
            
            // Fallback: Sabit minimum kredi düş
            return ai_use_credits(0.001, $metadata['tenant_id'] ?? null, array_merge($metadata, [
                'provider_name' => $providerName,
                'fallback_reason' => 'calculation_error',
                'error' => $e->getMessage()
            ]));
        }
    }
}

// ============================================================================
// LEGACY SUPPORT - ESKİ TOKEN FONKSİYONLARI
// ============================================================================

if (!function_exists('ai_use_tokens')) {
    /**
     * Legacy token kullanımı - credit sistemine yönlendir
     * 
     * @deprecated Use ai_use_credits() instead
     */
    function ai_use_tokens(int $tokenAmount, ?string $tenantId = null, array $metadata = []): bool
    {
        // Token → Credit dönüşümü (1000 token = 1 credit)
        $creditAmount = round($tokenAmount / 1000, 4);
        
        Log::warning('Legacy ai_use_tokens() called, converting to credits', [
            'tokens' => $tokenAmount,
            'credits' => $creditAmount,
            'tenant_id' => $tenantId
        ]);
        
        return ai_use_credits($creditAmount, $tenantId, array_merge($metadata, [
            'legacy_tokens' => $tokenAmount,
            'conversion_rate' => 1000
        ]));
    }
}

if (!function_exists('ai_can_use_tokens')) {
    /**
     * Legacy token kontrol - credit sistemine yönlendir
     * 
     * @deprecated Use ai_can_use_credits() instead
     */
    function ai_can_use_tokens(int $tokenAmount, ?string $tenantId = null): bool
    {
        // Token → Credit dönüşümü
        $creditAmount = round($tokenAmount / 1000, 4);
        
        return ai_can_use_credits($creditAmount, $tenantId);
    }
}

if (!function_exists('ai_get_token_balance')) {
    /**
     * Legacy token balance - credit sistemine yönlendir
     * 
     * @deprecated Use ai_get_credit_balance() instead
     */
    function ai_get_token_balance(?string $tenantId = null): int
    {
        // Credit → Token dönüşümü (1 credit = 1000 token)
        $creditBalance = ai_get_credit_balance($tenantId);
        
        return (int) round($creditBalance * 1000);
    }
}