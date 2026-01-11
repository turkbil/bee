<?php

declare(strict_types=1);

namespace Modules\AI\App\Services;

use Modules\AI\App\Models\AICreditPackage;
use Modules\AI\App\Models\AICreditPurchase;
use Modules\AI\App\Models\AICreditUsage;
use Modules\AI\App\Exceptions\AICreditException;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * AI Credit System Business Logic Service
 * 
 * Manages all credit operations including:
 * - Credit purchases and transactions
 * - Usage tracking and validation
 * - Credit expiry management
 * - Balance calculations and reporting
 * - Usage analytics and forecasting
 * 
 * @package Modules\AI\app\Services
 * @author AI V2 System
 * @version 2.0.0
 */
class AICreditService
{
    /**
     * Credit usage categories with different costs
     */
    private const USAGE_CATEGORIES = [
        'basic_query' => 1.0,           // Basic AI queries
        'advanced_analysis' => 2.5,     // Complex analysis tasks
        'content_generation' => 3.0,    // Content creation
        'seo_analysis' => 2.0,          // SEO optimization
        'translation' => 1.5,           // Language translation
        'code_generation' => 4.0,       // Code generation
        'image_analysis' => 3.5,        // Image processing
        'bulk_operations' => 5.0,       // Bulk/batch operations
    ];

    /**
     * Credit expiry policies
     */
    private const EXPIRY_POLICIES = [
        'standard' => 365,        // 1 year
        'premium' => 730,         // 2 years
        'enterprise' => null,     // Never expires
    ];

    /**
     * Low credit warning thresholds
     */
    private const WARNING_THRESHOLDS = [
        'critical' => 10,    // Less than 10 credits
        'low' => 50,         // Less than 50 credits
        'moderate' => 100,   // Less than 100 credits
    ];

    private ?ProviderMultiplierService $providerMultiplierService;

    public function __construct(
        ?ProviderMultiplierService $providerMultiplierService = null
    ) {
        $this->providerMultiplierService = $providerMultiplierService;
    }

    /**
     * Purchase credits for a user
     * 
     * @param User $user The user purchasing credits
     * @param AICreditPackage $package The package being purchased
     * @param array $paymentData Payment information
     * @return AICreditPurchase Purchase record
     */
    public function purchaseCredits(
        User $user,
        AICreditPackage $package,
        array $paymentData = []
    ): AICreditPurchase {
        DB::beginTransaction();
        
        try {
            // Create purchase record
            $purchase = AICreditPurchase::create([
                'user_id' => $user->id,
                'ai_credit_package_id' => $package->id,
                'credit_amount' => $package->credits,
                'amount_paid' => $paymentData['amount'] ?? $package->discounted_price,
                'currency' => $paymentData['currency'] ?? 'USD',
                'payment_method' => $paymentData['method'] ?? 'stripe',
                'status' => $paymentData['status'] ?? 'completed',
                'payment_reference' => $paymentData['reference'] ?? null,
                'expires_at' => $this->calculateExpiryDate($package),
                'purchased_at' => now(),
            ]);

            // Update user's credit balance
            $this->addCreditsToUser($user, $package->credits, $purchase);

            // Clear credit cache for user
            $this->clearUserCreditCache($user->id);

            // Log the purchase
            Log::info('Credits purchased successfully', [
                'user_id' => $user->id,
                'package_id' => $package->id,
                'credits' => $package->credits,
                'amount' => $purchase->amount_paid,
                'purchase_id' => $purchase->id,
            ]);

            DB::commit();
            return $purchase;

        } catch (\Exception $e) {
            DB::rollback();
            
            Log::error('Credit purchase failed', [
                'user_id' => $user->id,
                'package_id' => $package->id,
                'error' => $e->getMessage(),
            ]);

            throw AICreditException::purchaseFailed(
                "Credit purchase failed: " . $e->getMessage(),
                [
                    'user_id' => $user->id,
                    'package_id' => $package->id,
                    'payment_data' => $paymentData,
                ]
            );
        }
    }

    /**
     * Use credits for AI operations
     * 
     * @param User $user User consuming credits
     * @param string $category Usage category
     * @param float $baseCost Base cost before multipliers
     * @param string $provider AI provider used
     * @param string $feature Feature type
     * @param array $metadata Additional metadata
     * @return AICreditUsage Usage record
     */
    public function useCredits(
        User $user,
        string $category,
        float $baseCost = 1.0,
        string $provider = 'openai',
        string $feature = 'basic_query',
        array $metadata = []
    ): AICreditUsage {
        // Calculate final cost with multipliers
        $finalCost = $this->calculateFinalCost($category, $baseCost, $provider, $feature);

        // Check if user has sufficient credits
        $availableCredits = $this->getUserCredits($user);
        if ($availableCredits < $finalCost) {
            throw AICreditException::insufficientCredits(
                $finalCost,
                $availableCredits,
                ['user_id' => $user->id, 'required_credits' => $finalCost]
            );
        }

        DB::beginTransaction();

        try {
            // Create usage record
            $usage = AICreditUsage::create([
                'tenant_id' => 1, // Default to central tenant for now
                'user_id' => $user->id,
                'category' => $category,
                'provider_name' => $provider,
                'model' => $metadata['model'] ?? 'gpt-4o-mini',
                'feature_slug' => $feature,
                'credits_used' => $finalCost,
                'base_cost' => $baseCost,
                'multiplier_applied' => $finalCost / $baseCost,
                'metadata' => $metadata,
                'used_at' => now(),
            ]);

            // Update user's remaining credits
            $this->deductCreditsFromUser($user, $finalCost);

            // Clear credit cache
            $this->clearUserCreditCache($user->id);

            // Check for low credit warnings using calculated remaining credits
            $remainingCredits = max(0.0, $availableCredits - $finalCost);
            $this->checkCreditWarningsWithBalance($user, $remainingCredits);

            DB::commit();

            // Calculate remaining credits without calling getUserCredits again
            $remainingCredits = max(0.0, $availableCredits - $finalCost);
            
            Log::info('Credits used successfully', [
                'user_id' => $user->id,
                'category' => $category,
                'credits_used' => $finalCost,
                'remaining_credits' => $remainingCredits,
                'usage_id' => $usage->id,
            ]);

            return $usage;

        } catch (\Exception $e) {
            DB::rollback();
            
            Log::error('Credit usage failed', [
                'user_id' => $user->id,
                'category' => $category,
                'credits_required' => $finalCost,
                'error' => $e->getMessage(),
            ]);

            throw AICreditException::usageFailed(
                "Credit usage failed: " . $e->getMessage(),
                [
                    'user_id' => $user->id,
                    'category' => $category,
                    'credits_required' => $finalCost,
                ]
            );
        }
    }

    /**
     * Get user's current credit balance by ID
     * 
     * @param int $userId
     * @return float Current credit balance
     */
    public function getCurrentBalance(int $userId): float
    {
        // Get tenant ID from current session or user
        $tenantId = session('tenant_id', 1); // Default to 1 if not in session
        
        return $this->getTenantCredits($tenantId);
    }

    /**
     * Consume credits for a specific operation (simplified version)
     * 
     * @param int $userId
     * @param float $cost
     * @param string $category
     * @param array $metadata
     * @return void
     */
    public function consumeCredits(int $userId, float $cost, string $category, array $metadata = []): void
    {
        $user = User::find($userId);
        if (!$user) {
            throw new \Exception("User not found: {$userId}");
        }
        
        $this->useCredits($user, $category, $cost, 'openai', $category, $metadata);
    }

    /**
     * Get tenant's current credit balance
     * 
     * @param int $tenantId
     * @return float Current credit balance
     */
    public function getTenantCredits(int $tenantId): float
    {
        $cacheKey = "tenant_credits_{$tenantId}";

        return (float) Cache::remember($cacheKey, 300, function () use ($tenantId) {
            // Get total purchased credits for tenant
            $totalPurchased = (float) AICreditPurchase::where('tenant_id', $tenantId)
                ->where('status', 'completed')
                ->sum('credit_amount');

            // Get total used credits for tenant
            $totalUsed = (float) AICreditUsage::where('tenant_id', $tenantId)->sum('credits_used');

            $realBalance = (float) max(0.0, $totalPurchased - $totalUsed);
            
            // Debug logging
            Log::debug('Tenant credit balance calculation', [
                'tenant_id' => $tenantId,
                'total_purchased' => $totalPurchased,
                'total_used' => $totalUsed,
                'real_balance' => $realBalance
            ]);

            return $realBalance;
        });
    }

    /**
     * Get user's current credit balance
     * 
     * @param User $user
     * @return float Current credit balance
     */
    public function getUserCredits(User $user): float
    {
        $cacheKey = "user_credits_{$user->id}";

        return (float) Cache::remember($cacheKey, 300, function () use ($user) {
            // Get total purchased credits (not expired)
            $totalPurchased = (float) AICreditPurchase::where('user_id', $user->id)
                ->where('status', 'completed')
                ->sum('credit_amount');

            // Get total used credits
            $totalUsed = (float) AICreditUsage::where('user_id', $user->id)->sum('credits_used');

            $realBalance = (float) max(0.0, $totalPurchased - $totalUsed);

            return $realBalance;
        });
    }

    /**
     * Get detailed credit breakdown for user
     * 
     * @param User $user
     * @return array Credit breakdown
     */
    public function getCreditBreakdown(User $user): array
    {
        $cacheKey = "credit_breakdown_{$user->id}";

        return Cache::remember($cacheKey, 600, function () use ($user) {
            $purchases = AICreditPurchase::where('user_id', $user->id)
                ->where('status', 'completed')
                ->get();

            $totalUsed = (float) AICreditUsage::where('user_id', $user->id)->sum('credits_used');

            $breakdown = [
                'current_balance' => $this->getUserCredits($user),
                'total_purchased' => (float) $purchases->sum('credit_amount'),
                'total_used' => $totalUsed,
                'expired_credits' => 0.0, // Will be calculated properly if needed
                'active_purchases' => $purchases->where('expires_at', '>', now())->count(),
                'warning_level' => $this->getCreditWarningLevel($this->getUserCredits($user)),
            ];

            // Usage by category (last 30 days)
            $recentUsage = AICreditUsage::where('user_id', $user->id)
                ->where('used_at', '>=', now()->subDays(30))
                ->selectRaw('category, SUM(credits_used) as total_used, COUNT(*) as usage_count')
                ->groupBy('category')
                ->get();

            $breakdown['usage_by_category'] = $recentUsage->mapWithKeys(function ($usage) {
                return [$usage->category => [
                    'credits_used' => $usage->total_used,
                    'usage_count' => $usage->usage_count,
                    'avg_per_usage' => round($usage->total_used / $usage->usage_count, 2),
                ]];
            });

            return $breakdown;
        });
    }

    /**
     * Get credit usage analytics
     * 
     * @param User $user
     * @param int $days Number of days to analyze
     * @return array Usage analytics
     */
    public function getUsageAnalytics(User $user, int $days = 30): array
    {
        $startDate = now()->subDays($days);

        $usage = AICreditUsage::where('user_id', $user->id)
            ->where('used_at', '>=', $startDate)
            ->get();

        $analytics = [
            'total_credits_used' => $usage->sum('credits_used'),
            'total_requests' => $usage->count(),
            'average_per_request' => $usage->count() > 0 ? round($usage->sum('credits_used') / $usage->count(), 2) : 0,
            'most_used_category' => $usage->groupBy('category')->map->count()->sortDesc()->keys()->first(),
            'daily_usage' => [],
            'hourly_patterns' => [],
            'provider_distribution' => [],
            'feature_distribution' => [],
        ];

        // Daily usage pattern
        for ($i = 0; $i < $days; $i++) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dayUsage = $usage->where('used_at', '>=', $date . ' 00:00:00')
                             ->where('used_at', '<=', $date . ' 23:59:59')
                             ->sum('credits_used');
            $analytics['daily_usage'][$date] = $dayUsage;
        }

        // Hourly patterns (0-23)
        for ($hour = 0; $hour < 24; $hour++) {
            $hourUsage = $usage->filter(function ($record) use ($hour) {
                return $record->used_at->hour === $hour;
            })->sum('credits_used');
            $analytics['hourly_patterns'][$hour] = $hourUsage;
        }

        // Provider distribution
        $analytics['provider_distribution'] = $usage->groupBy('provider')
            ->map(function ($providerUsage) {
                return [
                    'credits_used' => $providerUsage->sum('credits_used'),
                    'request_count' => $providerUsage->count(),
                    'avg_cost' => round($providerUsage->sum('credits_used') / $providerUsage->count(), 2),
                ];
            });

        // Feature distribution
        $analytics['feature_distribution'] = $usage->groupBy('feature_type')
            ->map(function ($featureUsage) {
                return [
                    'credits_used' => $featureUsage->sum('credits_used'),
                    'request_count' => $featureUsage->count(),
                    'avg_cost' => round($featureUsage->sum('credits_used') / $featureUsage->count(), 2),
                ];
            });

        return $analytics;
    }

    /**
     * Predict credit needs based on usage patterns
     * 
     * @param User $user
     * @param int $forecastDays Days to forecast
     * @return array Credit forecast
     */
    public function forecastCreditNeeds(User $user, int $forecastDays = 30): array
    {
        $analytics = $this->getUsageAnalytics($user, 30);
        $dailyAverage = $analytics['total_credits_used'] / 30;
        
        $currentBalance = $this->getUserCredits($user);
        $projectedUsage = $dailyAverage * $forecastDays;
        
        $forecast = [
            'current_balance' => $currentBalance,
            'daily_average_usage' => round($dailyAverage, 2),
            'projected_usage' => round($projectedUsage, 2),
            'projected_balance' => round($currentBalance - $projectedUsage, 2),
            'days_remaining' => $dailyAverage > 0 ? ceil($currentBalance / $dailyAverage) : 999,
            'recommendation' => $this->getCreditRecommendation($currentBalance, $dailyAverage, $forecastDays),
        ];

        // Add trend analysis
        $recentUsage = array_slice($analytics['daily_usage'], 0, 7, true);
        $olderUsage = array_slice($analytics['daily_usage'], -7, 7, true);
        
        $recentAvg = array_sum($recentUsage) / 7;
        $olderAvg = array_sum($olderUsage) / 7;
        
        if ($recentAvg > $olderAvg * 1.2) {
            $forecast['trend'] = 'increasing';
            $forecast['trend_factor'] = round($recentAvg / $olderAvg, 2);
        } elseif ($recentAvg < $olderAvg * 0.8) {
            $forecast['trend'] = 'decreasing';
            $forecast['trend_factor'] = round($recentAvg / $olderAvg, 2);
        } else {
            $forecast['trend'] = 'stable';
            $forecast['trend_factor'] = 1.0;
        }

        return $forecast;
    }

    /**
     * Handle expired credits cleanup
     * 
     * @return array Cleanup results
     */
    public function cleanupExpiredCredits(): array
    {
        $expiredPurchases = AICreditPurchase::where('expires_at', '<', now())
            ->where('status', 'completed')
            ->get();

        $totalExpiredCredits = 0;
        $affectedUsers = [];

        foreach ($expiredPurchases as $purchase) {
            $totalExpiredCredits += $purchase->credit_amount;
            
            if (!in_array($purchase->user_id, $affectedUsers)) {
                $affectedUsers[] = $purchase->user_id;
                // Clear cache for affected user
                $this->clearUserCreditCache($purchase->user_id);
            }
        }

        Log::info('Expired credits cleanup completed', [
            'expired_purchases' => $expiredPurchases->count(),
            'total_expired_credits' => $totalExpiredCredits,
            'affected_users' => count($affectedUsers),
        ]);

        return [
            'expired_purchases' => $expiredPurchases->count(),
            'total_expired_credits' => $totalExpiredCredits,
            'affected_users' => count($affectedUsers),
            'cleanup_date' => now()->toISOString(),
        ];
    }

    /**
     * Calculate final cost with all multipliers
     */
    private function calculateFinalCost(
        string $category,
        float $baseCost,
        string $provider,
        string $feature
    ): float {
        // Apply category multiplier
        $categoryMultiplier = self::USAGE_CATEGORIES[$category] ?? 1.0;
        
        // Apply provider multiplier (fallback to 1.0 if service not available)
        $providerMultiplier = 1.0;
        if ($this->providerMultiplierService !== null) {
            $providerMultiplier = $this->providerMultiplierService->calculateCreditCost(
                $provider,
                $feature,
                1
            );
        }

        return round($baseCost * $categoryMultiplier * $providerMultiplier, 2);
    }

    /**
     * Calculate credit expiry date based on package
     */
    private function calculateExpiryDate(AICreditPackage $package): ?Carbon
    {
        // Determine expiry policy based on package price/credits
        if ($package->credits >= 10000) {
            $policy = 'enterprise';
        } elseif ($package->credits >= 1000) {
            $policy = 'premium';
        } else {
            $policy = 'standard';
        }

        $expiryDays = self::EXPIRY_POLICIES[$policy];
        
        return $expiryDays ? now()->addDays($expiryDays) : null;
    }

    /**
     * Add credits to user's balance
     */
    private function addCreditsToUser(User $user, float $credits, AICreditPurchase $purchase): void
    {
        // Credits are tracked via purchases, no separate balance table needed
        // This method exists for potential future balance management
        Log::info('Credits added to user', [
            'user_id' => $user->id,
            'credits_added' => $credits,
            'purchase_id' => $purchase->id,
        ]);
    }

    /**
     * Deduct credits from user's balance
     */
    private function deductCreditsFromUser(User $user, float $credits): void
    {
        // Credits are tracked via usage records
        Log::debug('Credits deducted from user', [
            'user_id' => $user->id,
            'credits_deducted' => $credits,
        ]);
    }

    /**
     * Clear user credit cache
     */
    private function clearUserCreditCache(int $userId): void
    {
        Cache::forget("user_credits_{$userId}");
        Cache::forget("credit_breakdown_{$userId}");
    }

    /**
     * Check and send credit warnings if needed
     */
    private function checkCreditWarnings(User $user): void
    {
        $credits = $this->getUserCredits($user);
        $warningLevel = $this->getCreditWarningLevel($credits);

        if ($warningLevel !== 'sufficient') {
            // Here you would dispatch events or send notifications
            Log::warning('User has low credits', [
                'user_id' => $user->id,
                'current_credits' => $credits,
                'warning_level' => $warningLevel,
            ]);
        }
    }

    /**
     * Check and send credit warnings with provided balance (avoids getUserCredits call)
     */
    private function checkCreditWarningsWithBalance(User $user, float $remainingBalance): void
    {
        $warningLevel = $this->getCreditWarningLevel($remainingBalance);

        if ($warningLevel !== 'sufficient') {
            // Here you would dispatch events or send notifications
            Log::warning('User has low credits after operation', [
                'user_id' => $user->id,
                'remaining_credits' => $remainingBalance,
                'warning_level' => $warningLevel,
            ]);
        }
    }

    /**
     * Get credit warning level
     */
    private function getCreditWarningLevel(float $credits): string
    {
        if ($credits <= self::WARNING_THRESHOLDS['critical']) {
            return 'critical';
        } elseif ($credits <= self::WARNING_THRESHOLDS['low']) {
            return 'low';
        } elseif ($credits <= self::WARNING_THRESHOLDS['moderate']) {
            return 'moderate';
        }

        return 'sufficient';
    }

    /**
     * Get credit purchase recommendation
     */
    private function getCreditRecommendation(
        float $currentBalance,
        float $dailyAverage,
        int $forecastDays
    ): string {
        $daysRemaining = $dailyAverage > 0 ? $currentBalance / $dailyAverage : 999;
        
        if ($daysRemaining < 7) {
            return 'urgent_purchase_needed';
        } elseif ($daysRemaining < 15) {
            return 'purchase_recommended';
        } elseif ($daysRemaining < 30) {
            return 'monitor_usage';
        }

        return 'sufficient_balance';
    }

    /**
     * Get tenant credit balance (for multi-tenant systems)
     * 
     * @param int $tenantId
     * @return array Balance information
     */
    public function getTenantCreditBalance(int $tenantId): array
    {
        // Get total purchased credits for tenant
        // NOT: expires_at kolonu henüz tabloda yok, ileride migration ile eklenecek
        $totalPurchased = AICreditPurchase::where('tenant_id', $tenantId)
            ->where('status', 'completed')
            ->sum('credit_amount');

        // Get total used credits for tenant
        $totalUsed = AICreditUsage::where('tenant_id', $tenantId)
            ->sum('credits_used');

        $remainingBalance = max(0, $totalPurchased - $totalUsed);

        return [
            'total_purchased' => $totalPurchased,
            'total_used' => $totalUsed,
            'remaining_balance' => $remainingBalance,
            'tenant_id' => $tenantId,
        ];
    }

    /**
     * Add credits to tenant (admin function)
     * 
     * @param int $tenantId
     * @param float $amount
     * @param string $reason
     * @param int $adminUserId
     * @return object Result object
     */
    public function addCreditsToTenant(
        int $tenantId,
        float $amount,
        string $reason = 'Admin credit addition',
        int $adminUserId = null
    ): object {
        try {
            // Create a purchase record for admin credit addition
            $purchase = AICreditPurchase::create([
                'tenant_id' => $tenantId,
                'user_id' => $adminUserId,
                'ai_credit_package_id' => null, // No package for admin additions
                'credit_amount' => $amount,
                'amount_paid' => 0, // Free admin addition
                'currency' => 'TRY',
                'status' => 'completed',
                'payment_method' => 'admin_free',
                'notes' => $reason,
                'purchased_at' => now(),
            ]);

            return (object) [
                'success' => true,
                'message' => "Successfully added {$amount} credits to tenant {$tenantId}",
                'purchase_id' => $purchase->id,
            ];

        } catch (\Exception $e) {
            Log::error('Failed to add credits to tenant', [
                'tenant_id' => $tenantId,
                'amount' => $amount,
                'error' => $e->getMessage(),
            ]);

            return (object) [
                'success' => false,
                'message' => 'Failed to add credits: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Deduct credits from tenant (admin function)
     * 
     * @param int $tenantId
     * @param float $amount
     * @param string $reason
     * @param int $adminUserId
     * @return object Result object
     */
    public function deductCreditsFromTenant(
        int $tenantId,
        float $amount,
        string $reason = 'Admin credit deduction',
        int $adminUserId = null
    ): object {
        try {
            $balance = $this->getTenantCreditBalance($tenantId);
            
            if ($balance['remaining_balance'] < $amount) {
                return (object) [
                    'success' => false,
                    'message' => 'Insufficient credit balance. Available: ' . $balance['remaining_balance'],
                ];
            }

            // Create a usage record for admin deduction
            $usage = AICreditUsage::create([
                'tenant_id' => $tenantId,
                'user_id' => $adminUserId,
                'credits_used' => $amount,
                'usage_type' => 'admin_adjustment',
                'provider_name' => 'admin',
                'model' => 'admin-deduction',
                'feature_slug' => 'deduction',
                'metadata' => [
                    'reason' => $reason,
                    'admin_id' => $adminUserId,
                    'adjustment_type' => 'deduction',
                ],
                'used_at' => now(),
            ]);

            return (object) [
                'success' => true,
                'message' => "Successfully deducted {$amount} credits from tenant {$tenantId}",
                'usage_id' => $usage->id,
            ];

        } catch (\Exception $e) {
            Log::error('Failed to deduct credits from tenant', [
                'tenant_id' => $tenantId,
                'amount' => $amount,
                'error' => $e->getMessage(),
            ]);

            return (object) [
                'success' => false,
                'message' => 'Failed to deduct credits: ' . $e->getMessage(),
            ];
        }
    }
}