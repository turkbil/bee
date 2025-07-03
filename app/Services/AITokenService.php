<?php

namespace App\Services;

use App\Models\Tenant;
use Modules\AI\App\Models\AITokenPackage;
use Modules\AI\App\Models\AITokenPurchase;
use Modules\AI\App\Models\AITokenUsage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AITokenService
{
    /**
     * Check if tenant can use AI with given token amount
     */
    public function canUseTokens(Tenant $tenant, int $tokensNeeded): bool
    {
        if (!$tenant->ai_enabled) {
            return false;
        }

        // Check monthly limit
        if ($tenant->isMonthlyLimitExceeded()) {
            return false;
        }

        // Check balance
        return $tenant->hasEnoughTokens($tokensNeeded);
    }

    /**
     * Use tokens for AI operation
     */
    public function useTokens(Tenant $tenant, int $tokensUsed, string $usageType = 'chat', ?string $description = null, ?string $referenceId = null): bool
    {
        if (!$this->canUseTokens($tenant, $tokensUsed)) {
            Log::warning('AI Token usage denied', [
                'tenant_id' => $tenant->id,
                'tokens_needed' => $tokensUsed,
                'current_balance' => $tenant->ai_tokens_balance,
                'ai_enabled' => $tenant->ai_enabled,
                'monthly_limit_exceeded' => $tenant->isMonthlyLimitExceeded()
            ]);
            return false;
        }

        return DB::transaction(function () use ($tenant, $tokensUsed, $usageType, $description, $referenceId) {
            // Update tenant token balance and usage
            $tenant->decrement('ai_tokens_balance', $tokensUsed);
            $tenant->increment('ai_tokens_used_this_month', $tokensUsed);
            $tenant->update(['ai_last_used_at' => now()]);

            // Log usage
            AITokenUsage::create([
                'tenant_id' => $tenant->id,
                'tokens_used' => $tokensUsed,
                'usage_type' => $usageType,
                'description' => $description,
                'reference_id' => $referenceId,
                'used_at' => now()
            ]);

            Log::info('AI Tokens used successfully', [
                'tenant_id' => $tenant->id,
                'tokens_used' => $tokensUsed,
                'usage_type' => $usageType,
                'new_balance' => $tenant->fresh()->ai_tokens_balance
            ]);

            return true;
        });
    }

    /**
     * Add tokens to tenant (purchase, admin adjustment, etc.)
     */
    public function addTokens(Tenant $tenant, int $tokensToAdd, string $reason = 'Token eklendi'): bool
    {
        return DB::transaction(function () use ($tenant, $tokensToAdd, $reason) {
            $tenant->increment('ai_tokens_balance', $tokensToAdd);

            Log::info('AI Tokens added to tenant', [
                'tenant_id' => $tenant->id,
                'tokens_added' => $tokensToAdd,
                'reason' => $reason,
                'new_balance' => $tenant->fresh()->ai_tokens_balance
            ]);

            return true;
        });
    }

    /**
     * Process token package purchase
     */
    public function processPurchase(Tenant $tenant, AITokenPackage $package, array $paymentData = []): AITokenPurchase
    {
        return DB::transaction(function () use ($tenant, $package, $paymentData) {
            // Create purchase record
            $purchase = AITokenPurchase::create([
                'tenant_id' => $tenant->id,
                'package_id' => $package->id,
                'token_amount' => $package->token_amount,
                'price_paid' => $package->price,
                'currency' => $package->currency,
                'status' => 'pending',
                'payment_method' => $paymentData['method'] ?? null,
                'payment_transaction_id' => $paymentData['transaction_id'] ?? null,
                'payment_data' => $paymentData
            ]);

            Log::info('AI Token purchase created', [
                'tenant_id' => $tenant->id,
                'package_id' => $package->id,
                'purchase_id' => $purchase->id,
                'token_amount' => $package->token_amount,
                'price' => $package->price
            ]);

            return $purchase;
        });
    }

    /**
     * Complete a token purchase
     */
    public function completePurchase(AITokenPurchase $purchase): bool
    {
        if ($purchase->status === 'completed') {
            return true; // Already completed
        }

        return DB::transaction(function () use ($purchase) {
            // Mark purchase as completed
            $purchase->update([
                'status' => 'completed',
                'purchased_at' => now()
            ]);

            // Add tokens to tenant balance
            $this->addTokens(
                $purchase->tenant,
                $purchase->token_amount,
                "Token paketi satın alımı (#{$purchase->id})"
            );

            Log::info('AI Token purchase completed', [
                'purchase_id' => $purchase->id,
                'tenant_id' => $purchase->tenant_id,
                'tokens_added' => $purchase->token_amount
            ]);

            return true;
        });
    }

    /**
     * Reset monthly token usage for all tenants
     */
    public function resetMonthlyUsage(): int
    {
        $resetCount = 0;

        Tenant::where('ai_enabled', true)
            ->where('ai_tokens_used_this_month', '>', 0)
            ->chunk(100, function ($tenants) use (&$resetCount) {
                foreach ($tenants as $tenant) {
                    $tenant->update([
                        'ai_tokens_used_this_month' => 0,
                        'ai_monthly_reset_at' => now()
                    ]);
                    $resetCount++;
                }
            });

        Log::info('Monthly AI token usage reset completed', [
            'tenants_reset' => $resetCount,
            'reset_date' => now()
        ]);

        return $resetCount;
    }

    /**
     * Calculate real token balance for tenant
     */
    public function calculateRealBalance(Tenant $tenant): int
    {
        $totalPurchased = AITokenPurchase::where('tenant_id', $tenant->id)
            ->where('status', 'completed')
            ->sum('token_amount') ?? 0;

        $totalUsed = AITokenUsage::where('tenant_id', $tenant->id)
            ->sum('tokens_used') ?? 0;

        return max(0, $totalPurchased - $totalUsed);
    }

    /**
     * Get comprehensive tenant token statistics
     */
    public function getTenantStats(Tenant $tenant): array
    {
        $thisMonth = Carbon::now()->startOfMonth();
        
        $monthlyUsage = AITokenUsage::where('tenant_id', $tenant->id)
            ->where('used_at', '>=', $thisMonth)
            ->selectRaw('DATE(used_at) as date, SUM(tokens_used) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $usageByType = AITokenUsage::where('tenant_id', $tenant->id)
            ->where('used_at', '>=', $thisMonth)
            ->selectRaw('usage_type, SUM(tokens_used) as total')
            ->groupBy('usage_type')
            ->get()
            ->keyBy('usage_type');

        $totalPurchased = AITokenPurchase::where('tenant_id', $tenant->id)
            ->where('status', 'completed')
            ->sum('token_amount');

        $totalUsed = AITokenUsage::where('tenant_id', $tenant->id)
            ->sum('tokens_used');

        $totalSpent = AITokenPurchase::where('tenant_id', $tenant->id)
            ->where('status', 'completed')
            ->sum('price_paid');

        $realBalance = $this->calculateRealBalance($tenant);

        return [
            'current_balance' => $tenant->ai_tokens_balance, // OLD METHOD - deprecate
            'real_balance' => $realBalance, // NEW CORRECT METHOD
            'total_purchased' => $totalPurchased,
            'total_used' => $totalUsed,
            'used_this_month' => $tenant->ai_tokens_used_this_month,
            'monthly_limit' => $tenant->ai_monthly_token_limit,
            'remaining_monthly' => $tenant->remaining_monthly_tokens,
            'total_spent' => $totalSpent,
            'monthly_usage' => $monthlyUsage,
            'usage_by_type' => $usageByType,
            'ai_enabled' => $tenant->ai_enabled,
            'last_used_at' => $tenant->ai_last_used_at
        ];
    }

    /**
     * Get system-wide token statistics
     */
    public function getSystemStats(): array
    {
        $activeTenantsCount = Tenant::where('ai_enabled', true)->count();
        $totalTokensSold = AITokenPurchase::where('status', 'completed')->sum('token_amount');
        $totalTokensUsed = AITokenUsage::sum('tokens_used');
        $totalRevenue = AITokenPurchase::where('status', 'completed')->sum('price_paid');
        
        $thisMonth = Carbon::now()->startOfMonth();
        $monthlyUsage = AITokenUsage::where('used_at', '>=', $thisMonth)->sum('tokens_used');
        $monthlyRevenue = AITokenPurchase::where('purchased_at', '>=', $thisMonth)
            ->where('status', 'completed')
            ->sum('price_paid');

        return [
            'active_tenants' => $activeTenantsCount,
            'total_tokens_sold' => $totalTokensSold,
            'total_tokens_used' => $totalTokensUsed,
            'total_revenue' => $totalRevenue,
            'monthly_usage' => $monthlyUsage,
            'monthly_revenue' => $monthlyRevenue,
            'usage_efficiency' => $totalTokensSold > 0 ? ($totalTokensUsed / $totalTokensSold) * 100 : 0
        ];
    }

    /**
     * Calculate estimated token cost for AI operation
     */
    public function estimateTokenCost(string $operation, array $params = []): int
    {
        // Basic token cost estimation based on operation type
        return match($operation) {
            'chat_message' => max(1, intval(strlen($params['message'] ?? '') / 4)), // ~4 chars per token
            'image_generation' => $params['tokens'] ?? 50,
            'text_completion' => max(1, intval(strlen($params['prompt'] ?? '') / 4)),
            'translation' => max(1, intval(strlen($params['text'] ?? '') / 3)),
            default => 1
        };
    }
}