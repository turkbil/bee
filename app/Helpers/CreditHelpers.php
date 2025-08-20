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
                
                // Son çare - default tenant ID=1 kullan (development/test için)
                if (!$tenantId) {
                    $tenantId = 1; // Default tenant
                    Log::warning('ai_use_credits: No tenant context found, using default tenant ID=1');
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
                'user_id' => auth()->id() ?: 1, // Default user ID 1 for system usage
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
            // Central admin mode kontrolü - tenant yoksa unlimited credit
            if (!$tenantId) {
                $tenantId = tenant('id');
                if (!$tenantId) {
                    // Central admin mode - limitless credit
                    Log::debug('ai_can_use_credits: Central admin mode - unlimited credits');
                    return true;
                }
            }
            
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
            // Central admin mode'da error olursa true dön
            return !tenant('id'); // Tenant yoksa true, varsa false
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
            
            // DOĞRU HESAPLAMA: Satın alınan - kullanılan (gerçek zamanlı)
            $totalPurchased = ai_get_total_credits_purchased($tenantId);
            $totalUsed = ai_get_total_credits_used($tenantId);
            $realBalance = max(0, $totalPurchased - $totalUsed);
            
            // Debug log
            Log::debug('Credit balance calculation', [
                'tenant_id' => $tenantId,
                'total_purchased' => $totalPurchased,
                'total_used' => $totalUsed,
                'real_balance' => $realBalance
            ]);
            
            return $realBalance;
            
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
            if (!$tenantId) {
                $tenantId = tenant('id');
                if (!$tenantId) {
                    Log::warning('Function called without tenant context');
                    // İlk tenant'ı al (admin panel için) - diğer fonksiyonlarla aynı logic
                    $firstTenant = Tenant::first();
                    $tenantId = $firstTenant?->id;
                    
                    if (!$tenantId) {
                        return 0.0;
                    }
                }
            }
            
            $totalUsed = AICreditUsage::where('tenant_id', $tenantId)
                ->sum('credits_used');
            
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
            if (!$tenantId) {
                $tenantId = tenant('id');
                if (!$tenantId) {
                    Log::warning('Function called without tenant context');
                    // İlk tenant'ı al (admin panel için)
                    $firstTenant = Tenant::first();
                    $tenantId = $firstTenant?->id;
                    
                    if (!$tenantId) {
                        return 0.0;
                    }
                }
            }
            
            // Credit purchase tablosu varsa oradan al
            if (class_exists('Modules\AI\App\Models\AICreditPurchase')) {
                $totalPurchased = \Modules\AI\App\Models\AICreditPurchase::where('tenant_id', $tenantId)
                    ->where('status', 'completed')
                    ->sum('credit_amount');
                
                // Eğer satın alma varsa onu döndür, yoksa default değere düş
                if ($totalPurchased > 0) {
                    return (float) $totalPurchased;
                }
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
            if (!$tenantId) {
                $tenantId = tenant('id');
                if (!$tenantId) {
                    Log::warning('Function called without tenant context');
                    // İlk tenant'ı al (admin panel için)
                    $firstTenant = Tenant::first();
                    $tenantId = $firstTenant?->id;
                    
                    if (!$tenantId) {
                        return 0.0;
                    }
                }
            }
            
            $monthlyUsed = AICreditUsage::where('tenant_id', $tenantId)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('credits_used');
            
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
            if (!$tenantId) {
                $tenantId = tenant('id');
                if (!$tenantId) {
                    Log::warning('Function called without tenant context');
                    // İlk tenant'ı al (admin panel için)
                    $firstTenant = Tenant::first();
                    $tenantId = $firstTenant?->id;
                    
                    if (!$tenantId) {
                        return 0.0;
                    }
                }
            }
            
            $dailyUsed = AICreditUsage::where('tenant_id', $tenantId)
                ->whereDate('created_at', now()->toDateString())
                ->sum('credits_used');
            
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
            $tenantId = $metadata['tenant_id'] ?? tenant('id');
            if (!$tenantId) {
                Log::warning('ai_use_calculated_credits: No tenant context found');
                return false;
            }
            
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
// KREDİ FORMATLAMA SİSTEMİ - MERKEZİ KONTROL
// ============================================================================

if (!function_exists('format_credit')) {
    /**
     * Merkezi kredi formatlaması - TEK NOKTADAN KONTROL
     * 
     * Bu fonksiyon tüm sistemdeki kredi gösterimlerini kontrol eder.
     * Sadece buradaki ayarları değiştirerek tüm kredi gösterimlerini 
     * anında güncelleyebilirsiniz.
     * 
     * @param float|int $amount Kredi miktarı
     * @param bool $withUnit "Kredi" kelimesi eklensin mi?
     * @param string|null $customUnit Özel birim adı
     * @return string Formatlanmış kredi miktarı
     */
    function format_credit($amount, bool $withUnit = true, ?string $customUnit = null): string
    {
        try {
            // =====================================
            // 🎛️ FORMAT AYARLARI - TEK NOKTA KONTROL
            // =====================================
            
            // Ondalık basamak sayısı (buradan tüm sistemi kontrol edebilirsiniz)
            $decimalPlaces = 2; // 2 = "100.00", 0 = "100", 4 = "100.0000"
            
            // Binlik ayırıcı
            $thousandsSeparator = '.'; // Türkiye: "." (örn: 1.000.00)
            
            // Ondalık ayırıcı  
            $decimalSeparator = ','; // Türkiye: "," (örn: 100,50)
            
            // Birim adı
            $defaultUnit = $customUnit ?? 'Kredi';
            
            // =====================================
            // FORMATLAMA İŞLEMİ
            // =====================================
            
            // Sayıyı float'a çevir
            $numericAmount = is_numeric($amount) ? (float) $amount : 0.0;
            
            // Number format uygula
            $formattedAmount = number_format(
                $numericAmount, 
                $decimalPlaces, 
                $decimalSeparator, 
                $thousandsSeparator
            );
            
            // Birim ekleme
            if ($withUnit) {
                return $formattedAmount . ' ' . $defaultUnit;
            }
            
            return $formattedAmount;
            
        } catch (\Exception $e) {
            Log::error('format_credit error', [
                'amount' => $amount,
                'error' => $e->getMessage()
            ]);
            
            // Fallback format
            return $withUnit ? '0,00 Kredi' : '0,00';
        }
    }
}

if (!function_exists('format_credit_short')) {
    /**
     * Kısa kredi formatlaması (sadece sayı, birim yok)
     * 
     * @param float|int $amount Kredi miktarı
     * @return string Formatlanmış kredi miktarı (birim olmadan)
     */
    function format_credit_short($amount): string
    {
        return format_credit($amount, false);
    }
}

if (!function_exists('format_credit_detailed')) {
    /**
     * Detaylı kredi formatlaması (daha fazla ondalık basamakla)
     * 
     * @param float|int $amount Kredi miktarı  
     * @param bool $withUnit Birim eklensin mi?
     * @return string Formatlanmış kredi miktarı
     */
    function format_credit_detailed($amount, bool $withUnit = true): string
    {
        try {
            $numericAmount = is_numeric($amount) ? (float) $amount : 0.0;
            
            // Detaylı format (4 ondalık basamak)
            $formattedAmount = number_format($numericAmount, 4, ',', '.');
            
            return $withUnit ? $formattedAmount . ' Kredi' : $formattedAmount;
            
        } catch (\Exception $e) {
            return format_credit($amount, $withUnit);
        }
    }
}

if (!function_exists('format_credit_currency')) {
    /**
     * Para birimi tarzında kredi formatlaması
     * 
     * @param float|int $amount Kredi miktarı
     * @param string $currency Para birimi simgesi
     * @return string Formatlanmış kredi miktarı
     */
    function format_credit_currency($amount, string $currency = '₺'): string
    {
        try {
            $numericAmount = is_numeric($amount) ? (float) $amount : 0.0;
            $formattedAmount = number_format($numericAmount, 2, ',', '.');
            
            return $currency . $formattedAmount;
            
        } catch (\Exception $e) {
            return $currency . '0,00';
        }
    }
}

// ============================================================================
// YENI MODEL BAZLI KREDİ FONKSİYONLARI - V2 SİSTEM
// ============================================================================

if (!function_exists('ai_calculate_model_credits')) {
    /**
     * Model bazlı kredi hesaplama - YENI SİSTEM
     * 
     * @param int $inputTokens Input token sayısı
     * @param int $outputTokens Output token sayısı  
     * @param string $provider Provider adı
     * @param string $model Model adı
     * @return float Hesaplanan kredi miktarı
     */
    function ai_calculate_model_credits(int $inputTokens, int $outputTokens, string $provider, string $model): float
    {
        try {
            $calculator = app(\Modules\AI\App\Services\CreditCalculatorService::class);
            return $calculator->calculateCreditsForModel($provider, $model, $inputTokens, $outputTokens);
        } catch (\Exception $e) {
            Log::error('ai_calculate_model_credits error', [
                'provider' => $provider,
                'model' => $model,
                'error' => $e->getMessage()
            ]);
            return ($inputTokens + $outputTokens) / 1000 * 1.0; // Fallback
        }
    }
}

if (!function_exists('ai_get_model_rate')) {
    /**
     * Model rate detaylarını getir - YENI SİSTEM
     * 
     * @param string $provider Provider adı
     * @param string $model Model adı
     * @return float|null Model kredi oranı (1K token için)
     */
    function ai_get_model_rate(string $provider, string $model): ?float
    {
        try {
            $calculator = app(\Modules\AI\App\Services\CreditCalculatorService::class);
            $details = $calculator->getModelRateDetails($provider, $model);
            
            return $details['found'] ? $details['input_rate'] : null;
        } catch (\Exception $e) {
            Log::error('ai_get_model_rate error', [
                'provider' => $provider,
                'model' => $model,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}

if (!function_exists('ai_estimate_cost_by_model')) {
    /**
     * Model bazlı maliyet tahmini - YENI SİSTEM
     * 
     * @param string $input Input text
     * @param string $provider Provider adı
     * @param string $model Model adı
     * @return array Maliyet tahmini detayları
     */
    function ai_estimate_cost_by_model(string $input, string $provider, string $model): array
    {
        try {
            $calculator = app(\Modules\AI\App\Services\CreditCalculatorService::class);
            return $calculator->estimateCreditCost($input, $provider, $model);
        } catch (\Exception $e) {
            Log::error('ai_estimate_cost_by_model error', [
                'provider' => $provider,
                'model' => $model,
                'error' => $e->getMessage()
            ]);
            return [
                'estimated_input_tokens' => 0,
                'estimated_output_tokens' => 0,
                'estimated_total_tokens' => 0,
                'estimated_credits' => 0.001,
                'provider' => $provider,
                'model' => $model
            ];
        }
    }
}

if (!function_exists('ai_get_base_token_rate')) {
    /**
     * Base token rate getir - YENI SİSTEM
     * 
     * @return float Base token rate (varsayılan 1.0)
     */
    function ai_get_base_token_rate(): float
    {
        try {
            $calculator = app(\Modules\AI\App\Services\CreditCalculatorService::class);
            return $calculator->getBaseTokenRate();
        } catch (\Exception $e) {
            Log::error('ai_get_base_token_rate error', ['error' => $e->getMessage()]);
            return 1.0;
        }
    }
}

if (!function_exists('ai_set_base_token_rate')) {
    /**
     * Base token rate ayarla - YENI SİSTEM
     * 
     * @param float $rate Yeni rate değeri
     * @return bool Başarılı mı?
     */
    function ai_set_base_token_rate(float $rate): bool
    {
        try {
            $calculator = app(\Modules\AI\App\Services\CreditCalculatorService::class);
            return $calculator->setBaseTokenRate($rate);
        } catch (\Exception $e) {
            Log::error('ai_set_base_token_rate error', [
                'rate' => $rate,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}

if (!function_exists('ai_use_credits_with_model')) {
    /**
     * Model bilgisi ile kredi kullanım kaydı - YENI SİSTEM
     * 
     * @param int $inputTokens Input token sayısı
     * @param int $outputTokens Output token sayısı
     * @param string $provider Provider adı
     * @param string $model Model adı
     * @param array $metadata Ek metadata
     * @return float Kullanılan kredi miktarı (0.0 = başarısız)
     */
    function ai_use_credits_with_model(
        int $inputTokens, 
        int $outputTokens, 
        string $provider, 
        string $model, 
        array $metadata = []
    ): float {
        try {
            // Model bazlı kredi hesapla
            $credits = ai_calculate_model_credits($inputTokens, $outputTokens, $provider, $model);
            
            // Metadata'yı güçlendir
            $enrichedMetadata = array_merge($metadata, [
                'provider_name' => $provider,
                'model_name' => $model,
                'input_tokens' => $inputTokens,
                'output_tokens' => $outputTokens,
                'total_tokens' => $inputTokens + $outputTokens,
                'credit_calculation_method' => 'model_based',
                'calculated_credits' => $credits
            ]);
            
            // Kredi kullan
            $success = ai_use_credits($credits, null, $enrichedMetadata);
            return $success ? $credits : 0.0;
            
        } catch (\Exception $e) {
            Log::error('ai_use_credits_with_model error', [
                'provider' => $provider,
                'model' => $model,
                'input_tokens' => $inputTokens,
                'output_tokens' => $outputTokens,
                'error' => $e->getMessage()
            ]);
            return 0.0;
        }
    }
}

if (!function_exists('ai_compare_model_costs')) {
    /**
     * Model maliyet karşılaştırması - YENI SİSTEM
     * 
     * @param int $estimatedTokens Tahmini token sayısı
     * @return array Tüm provider/model'ların maliyet karşılaştırması
     */
    function ai_compare_model_costs(int $estimatedTokens): array
    {
        try {
            $calculator = app(\Modules\AI\App\Services\CreditCalculatorService::class);
            return $calculator->compareModelsAcrossProviders($estimatedTokens);
        } catch (\Exception $e) {
            Log::error('ai_compare_model_costs error', [
                'estimated_tokens' => $estimatedTokens,
                'error' => $e->getMessage()
            ]);
            return [
                'comparison' => [],
                'cheapest' => null,
                'most_expensive' => null,
                'average_cost' => 0,
                'generated_at' => now()->toISOString()
            ];
        }
    }
}

// ============================================================================
// LEGACY SUPPORT - ESKİ TOKEN FONKSİYONLARI
// ============================================================================

// REMOVED: ai_use_tokens() - Moved to AITokenHelper.php with correct signature

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

if (!function_exists('can_use_ai_credits')) {
    /**
     * AI kredi kullanım kontrolü (ConversationService uyumluluğu için)
     * 
     * @param float $creditAmount Gerekli kredi miktarı
     * @param Tenant|null $tenant Tenant modeli
     * @return bool Kullanılabilir mi?
     */
    function can_use_ai_credits(float $creditAmount, ?Tenant $tenant = null): bool
    {
        $tenantId = $tenant?->id ?? tenant('id');
        return ai_can_use_credits($creditAmount, $tenantId);
    }
}

if (!function_exists('ai_credit_balance')) {
    /**
     * AI kredi bakiyesi (ConversationService uyumluluğu için)  
     * 
     * @param Tenant|null $tenant Tenant modeli
     * @return float Kredi bakiyesi
     */
    function ai_credit_balance(?Tenant $tenant = null): float
    {
        $tenantId = $tenant?->id ?? tenant('id');
        return ai_get_credit_balance($tenantId);
    }
}