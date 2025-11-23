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
            'starts_at' => now(),
            'ends_at' => $this->calculateEndDate($cycle, $trialDays),
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
            $subscription->ends_at ?? now()
        );

        $subscription->update([
            'ends_at' => $newEndDate,
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
                $query->where('ends_at', '<', now())
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
}
