<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\Coupon;

class SubscriptionService
{
    /**
     * Create a new subscription
     */
    public function create(User $user, SubscriptionPlan $plan, ?Coupon $coupon = null): Subscription
    {
        $price = $plan->price;

        // Apply coupon discount
        if ($coupon) {
            $discount = $coupon->calculateDiscount($price);
            $price = $price - $discount;
        }

        // Calculate dates
        $startsAt = now();
        $endsAt = now()->addDays($plan->duration_days);

        // Check for trial
        $trialEndsAt = null;
        $trialDays = (int) setting('auth_registration_trial_days', 0);
        if ($trialDays > 0 && !$user->subscriptions()->exists()) {
            $trialEndsAt = now()->addDays($trialDays);
            $endsAt = $endsAt->addDays($trialDays); // Add trial to subscription
        }

        $subscription = Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'price_per_cycle' => $price,
            'started_at' => $startsAt,
            'current_period_start' => $startsAt,
            'current_period_end' => $endsAt,
            'trial_ends_at' => $trialEndsAt,
            'auto_renewal' => (bool) setting('auth_subscription_auto_renewal', true),
        ]);

        return $subscription;
    }

    /**
     * Renew a subscription
     */
    public function renew(Subscription $subscription): Subscription
    {
        $newEndsAt = $subscription->current_period_end->addDays($subscription->plan->duration_days);

        $subscription->update([
            'current_period_start' => now(),
            'current_period_end' => $newEndsAt,
            'status' => 'active',
        ]);

        return $subscription;
    }

    /**
     * Cancel a subscription
     */
    public function cancel(Subscription $subscription): Subscription
    {
        $subscription->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'auto_renewal' => false,
        ]);

        return $subscription;
    }

    /**
     * Add trial days to subscription
     */
    public function addTrialDays(Subscription $subscription, int $days): Subscription
    {
        $subscription->update([
            'current_period_end' => $subscription->current_period_end->addDays($days),
            'trial_ends_at' => ($subscription->trial_ends_at ?? now())->addDays($days),
        ]);

        return $subscription;
    }

    /**
     * Get subscriptions expiring soon
     */
    public function getExpiringSoon(int $days = 7): \Illuminate\Database\Eloquent\Collection
    {
        return Subscription::where('status', 'active')
            ->where('current_period_end', '<=', now()->addDays($days))
            ->where('current_period_end', '>', now())
            ->with('user', 'plan')
            ->get();
    }

    /**
     * Get expired subscriptions
     */
    public function getExpired(): \Illuminate\Database\Eloquent\Collection
    {
        return Subscription::where('status', 'active')
            ->where('current_period_end', '<=', now())
            ->with('user', 'plan')
            ->get();
    }

    /**
     * Mark expired subscriptions
     */
    public function markExpired(): int
    {
        return Subscription::where('status', 'active')
            ->where('current_period_end', '<=', now())
            ->update(['status' => 'expired']);
    }
}
