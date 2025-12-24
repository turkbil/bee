<?php

namespace Modules\Subscription\App\Services;

use Modules\Subscription\App\Models\Subscription;
use Modules\Subscription\App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class SubscriptionService
{
    /**
     * Create a new subscription for a user
     */
    public function create(User $user, SubscriptionPlan $plan, string $cycle = 'monthly'): Subscription
    {
        // Cancel existing active subscription if any
        $this->cancelExisting($user);

        $price = $plan->getPriceForCycle($cycle);
        $trialDays = $plan->trial_days;

        $subscription = Subscription::create([
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'billing_cycle' => $cycle,
            'price_per_cycle' => $price,
            'started_at' => now(),
            'current_period_start' => now(),
            'current_period_end' => $this->calculateEndDate($cycle, $trialDays),
            'trial_ends_at' => $trialDays > 0 ? now()->addDays($trialDays) : null,
            'status' => $trialDays > 0 ? 'trial' : 'active',
            'auto_renew' => true,
        ]);

        return $subscription;
    }

    /**
     * Renew an existing subscription
     */
    public function renew(Subscription $subscription): Subscription
    {
        $newEndDate = $this->calculateEndDate(
            $subscription->billing_cycle,
            0,
            $subscription->current_period_end ?? now()
        );

        $subscription->update([
            'current_period_start' => now(),
            'current_period_end' => $newEndDate,
            'status' => 'active',
            'cancelled_at' => null,
        ]);

        return $subscription;
    }

    /**
     * Cancel a subscription
     */
    public function cancel(Subscription $subscription): bool
    {
        return $subscription->cancel();
    }

    /**
     * Change subscription plan
     */
    public function changePlan(Subscription $subscription, SubscriptionPlan $newPlan): Subscription
    {
        $price = $newPlan->getPriceForCycle($subscription->billing_cycle);

        $subscription->update([
            'subscription_plan_id' => $newPlan->id,
            'price_per_cycle' => $price,
        ]);

        return $subscription;
    }

    /**
     * Get subscriptions expiring soon
     */
    public function checkExpiring(int $days = 7): Collection
    {
        return Subscription::expiringSoon($days)
            ->with(['user', 'plan'])
            ->get();
    }

    /**
     * Process expired subscriptions
     */
    public function processExpired(): int
    {
        $count = 0;

        // Get expired active subscriptions
        $expired = Subscription::whereIn('status', ['active', 'trial'])
            ->where(function ($query) {
                $query->where('current_period_end', '<', now())
                      ->orWhere('trial_ends_at', '<', now());
            })
            ->get();

        foreach ($expired as $subscription) {
            $subscription->update(['status' => 'expired']);
            $count++;
        }

        return $count;
    }

    /**
     * Cancel existing active subscriptions for a user
     */
    protected function cancelExisting(User $user): void
    {
        Subscription::where('user_id', $user->id)
            ->whereIn('status', ['active', 'trial'])
            ->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'auto_renew' => false,
            ]);
    }

    /**
     * Calculate subscription end date
     */
    protected function calculateEndDate(string $cycle, int $trialDays = 0, ?Carbon $from = null): Carbon
    {
        $from = $from ?? now();

        if ($trialDays > 0) {
            return $from->copy()->addDays($trialDays);
        }

        return $cycle === 'yearly'
            ? $from->copy()->addYear()
            : $from->copy()->addMonth();
    }

    /**
     * Get subscription statistics
     */
    public function getStats(): array
    {
        return [
            'total_active' => Subscription::active()->count(),
            'total_trial' => Subscription::trial()->count(),
            'total_expired' => Subscription::expired()->count(),
            'total_cancelled' => Subscription::cancelled()->count(),
            'expiring_soon' => Subscription::expiringSoon(7)->count(),
            'monthly_revenue' => Subscription::active()
                ->where('billing_cycle', 'monthly')
                ->sum('price_per_cycle'),
            'yearly_revenue' => Subscription::active()
                ->where('billing_cycle', 'yearly')
                ->sum('price_per_cycle'),
        ];
    }

    /**
     * Get trial plan (is_trial = true)
     * @return SubscriptionPlan|null
     */
    public function getTrialPlan(): ?SubscriptionPlan
    {
        return SubscriptionPlan::where('is_trial', true)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Get trial duration from trial plan's cycle
     * @return int|null
     */
    public function getTrialDuration(): ?int
    {
        $trialPlan = $this->getTrialPlan();
        if (!$trialPlan) {
            return null;
        }

        $cycles = $trialPlan->billing_cycles;
        if (!$cycles || !is_array($cycles)) {
            return null;
        }

        $firstCycle = array_values($cycles)[0];
        return $firstCycle['duration_days'] ?? null;
    }

    /**
     * Create trial subscription for user
     * @param User $user
     * @return Subscription|null
     */
    public function createTrialForUser(User $user): ?Subscription
    {
        // 1. Setting kontrolü
        if (!setting('auth_subscription')) {
            return null;
        }

        // 2. Trial plan kontrolü
        $trialPlan = $this->getTrialPlan();
        if (!$trialPlan) {
            return null;
        }

        // 3. has_used_trial kontrolü
        if ($user->has_used_trial) {
            return null;
        }

        // 4. Subscription oluştur
        $duration = $this->getTrialDuration();
        if (!$duration) {
            return null;
        }

        // Trial plan'dan ilk cycle bilgisini al
        $cycles = $trialPlan->billing_cycles;
        $firstCycle = array_values($cycles)[0];

        $subscription = Subscription::create([
            'user_id' => $user->id,
            'subscription_plan_id' => $trialPlan->subscription_plan_id,
            'status' => 'active',
            'started_at' => now(),
            'current_period_start' => now(),
            'current_period_end' => now()->addDays($duration),
            'price_per_cycle' => 0, // Trial ücretsiz
            'currency' => $trialPlan->currency ?? 'TRY',
            'cycle_key' => array_keys($cycles)[0] ?? 'deneme-7-gun',
            'cycle_metadata' => $firstCycle,
            'has_trial' => true,
            'trial_days' => $duration,
            'trial_ends_at' => now()->addDays($duration),
        ]);

        // 5. has_used_trial = true
        $user->update(['has_used_trial' => true]);

        return $subscription;
    }

    /**
     * Get device limit for user (3-layer hierarchy)
     * @param User $user
     * @return int
     */
    public function getDeviceLimit(User $user): int
    {
        // 1. User override (özel durumlar - VIP/Test/Ban)
        if ($user->device_limit !== null) {
            return $user->device_limit;
        }

        // 2. Plan default (normal akış - çoğu kullanıcı buradan)
        $sub = $user->activeSubscription()->first();
        if ($sub && $sub->plan && $sub->plan->device_limit) {
            return $sub->plan->device_limit;
        }

        // 3. Global fallback (son çare - setting)
        return setting('auth_device_limit', 1);
    }

    /**
     * Check user access (FRESH - no cache!)
     * @param User $user
     * @return array
     */
    public function checkUserAccess(User $user): array
    {
        // 1. Subscription kontrolü (FRESH - cache yok!)
        $sub = Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('current_period_end', '>', now())
            ->first();

        if ($sub) {
            return [
                'status' => 'unlimited',
                'is_trial' => $sub->plan->is_trial ?? false,
                'expires_at' => $sub->current_period_end,
            ];
        }

        // 2. Abonelik yok/bitti - subscription gerekli
        return [
            'status' => 'subscription_required',
            'message' => 'Müzik dinlemek için premium üyelik gereklidir',
        ];
    }
}
