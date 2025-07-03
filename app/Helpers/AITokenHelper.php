<?php

use App\Models\Tenant;
use Modules\AI\App\Models\AITokenPackage;
use App\Services\AITokenService;

if (!function_exists('ai_token_balance')) {
    /**
     * Tenant'ın AI token bakiyesini döndür
     */
    function ai_token_balance(?Tenant $tenant = null): int
    {
        $tenant = $tenant ?? tenant();
        return $tenant ? $tenant->ai_tokens_balance : 0;
    }
}

if (!function_exists('ai_tokens_remaining_monthly')) {
    /**
     * Bu ay kalan token miktarını döndür
     */
    function ai_tokens_remaining_monthly(?Tenant $tenant = null): int
    {
        $tenant = $tenant ?? tenant();
        return $tenant ? $tenant->remaining_monthly_tokens : 0;
    }
}

if (!function_exists('can_use_ai_tokens')) {
    /**
     * AI token kullanılabilir mi kontrol et
     */
    function can_use_ai_tokens(int $tokensNeeded = 1, ?Tenant $tenant = null): bool
    {
        $tenant = $tenant ?? tenant();
        
        if (!$tenant || !$tenant->ai_enabled) {
            return false;
        }

        $aiTokenService = app(AITokenService::class);
        return $aiTokenService->canUseTokens($tenant, $tokensNeeded);
    }
}

if (!function_exists('ai_enabled')) {
    /**
     * AI kullanımı aktif mi kontrol et
     */
    function ai_enabled(?Tenant $tenant = null): bool
    {
        $tenant = $tenant ?? tenant();
        return $tenant ? $tenant->ai_enabled : false;
    }
}

if (!function_exists('ai_monthly_limit')) {
    /**
     * Aylık AI token limitini döndür
     */
    function ai_monthly_limit(?Tenant $tenant = null): int
    {
        $tenant = $tenant ?? tenant();
        return $tenant ? $tenant->ai_monthly_token_limit : 0;
    }
}

if (!function_exists('ai_monthly_usage')) {
    /**
     * Bu ay kullanılan AI token miktarını döndür
     */
    function ai_monthly_usage(?Tenant $tenant = null): int
    {
        $tenant = $tenant ?? tenant();
        return $tenant ? $tenant->ai_tokens_used_this_month : 0;
    }
}

if (!function_exists('ai_usage_percentage')) {
    /**
     * Aylık kullanım yüzdesini hesapla
     */
    function ai_usage_percentage(?Tenant $tenant = null): float
    {
        $tenant = $tenant ?? tenant();
        
        if (!$tenant || $tenant->ai_monthly_token_limit <= 0) {
            return 0;
        }

        return min(100, ($tenant->ai_tokens_used_this_month / $tenant->ai_monthly_token_limit) * 100);
    }
}

if (!function_exists('ai_last_used')) {
    /**
     * Son AI kullanım tarihini döndür
     */
    function ai_last_used(?Tenant $tenant = null): ?\Carbon\Carbon
    {
        $tenant = $tenant ?? tenant();
        return $tenant ? $tenant->ai_last_used_at : null;
    }
}

if (!function_exists('ai_token_packages')) {
    /**
     * Aktif AI token paketlerini döndür
     */
    function ai_token_packages(): \Illuminate\Database\Eloquent\Collection
    {
        return AITokenPackage::active()->ordered()->get();
    }
}

if (!function_exists('format_token_amount')) {
    /**
     * Token miktarını formatlı şekilde döndür
     */
    function format_token_amount(int $amount): string
    {
        return number_format($amount) . ' Token';
    }
}

if (!function_exists('estimate_ai_cost')) {
    /**
     * AI işlemi için token maliyetini tahmin et
     */
    function estimate_ai_cost(string $operation, array $params = []): int
    {
        $aiTokenService = app(AITokenService::class);
        return $aiTokenService->estimateTokenCost($operation, $params);
    }
}

if (!function_exists('use_ai_tokens')) {
    /**
     * AI token kullan ve kaydet
     */
    function use_ai_tokens(int $tokensUsed, string $usageType = 'chat', ?string $description = null, ?string $referenceId = null, ?Tenant $tenant = null): bool
    {
        $tenant = $tenant ?? tenant();
        
        if (!$tenant) {
            return false;
        }

        $aiTokenService = app(AITokenService::class);
        return $aiTokenService->useTokens($tenant, $tokensUsed, $usageType, $description, $referenceId);
    }
}

if (!function_exists('ai_token_status')) {
    /**
     * AI token durumu hakkında özet bilgi döndür
     */
    function ai_token_status(?Tenant $tenant = null): array
    {
        $tenant = $tenant ?? tenant();
        
        if (!$tenant) {
            return [
                'enabled' => false,
                'balance' => 0,
                'monthly_used' => 0,
                'monthly_limit' => 0,
                'monthly_remaining' => 0,
                'usage_percentage' => 0,
                'can_use' => false,
                'last_used' => null
            ];
        }

        return [
            'enabled' => $tenant->ai_enabled,
            'balance' => $tenant->ai_tokens_balance,
            'monthly_used' => $tenant->ai_tokens_used_this_month,
            'monthly_limit' => $tenant->ai_monthly_token_limit,
            'monthly_remaining' => $tenant->remaining_monthly_tokens,
            'usage_percentage' => ai_usage_percentage($tenant),
            'can_use' => can_use_ai_tokens(1, $tenant),
            'last_used' => $tenant->ai_last_used_at
        ];
    }
}

if (!function_exists('ai_token_widget_data')) {
    /**
     * Widget'larda kullanılmak üzere AI token verilerini döndür
     */
    function ai_token_widget_data(?Tenant $tenant = null): array
    {
        $status = ai_token_status($tenant);
        
        return [
            'balance_formatted' => format_token_amount($status['balance']),
            'monthly_used_formatted' => format_token_amount($status['monthly_used']),
            'monthly_limit_formatted' => $status['monthly_limit'] > 0 ? format_token_amount($status['monthly_limit']) : 'Sınırsız',
            'monthly_remaining_formatted' => format_token_amount($status['monthly_remaining']),
            'usage_percentage' => round($status['usage_percentage'], 1),
            'usage_color' => match(true) {
                $status['usage_percentage'] >= 90 => 'danger',
                $status['usage_percentage'] >= 75 => 'warning',
                $status['usage_percentage'] >= 50 => 'info',
                default => 'success'
            },
            'status_text' => match(true) {
                !$status['enabled'] => 'AI Devre Dışı',
                $status['balance'] <= 0 => 'Token Bitmiş',
                $status['usage_percentage'] >= 100 => 'Aylık Limit Aşıldı',
                $status['usage_percentage'] >= 90 => 'Limit Yaklaşıyor',
                default => 'Normal'
            },
            'last_used_human' => $status['last_used'] ? $status['last_used']->diffForHumans() : 'Hiç kullanılmamış'
        ];
    }
}