<?php

namespace Modules\Subscription\App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Subscription\App\Models\Subscription;
use Modules\Subscription\App\Events\SubscriptionExpired;
use Modules\Subscription\App\Events\TrialEnding;
use Illuminate\Support\Facades\Log;

class CheckExpiredSubscriptions extends Command
{
    protected $signature = 'subscription:check-expired';
    protected $description = 'Check and update expired subscriptions, send notifications';

    public function handle()
    {
        $this->info('🔍 Checking expired subscriptions...');

        // 1. Expire bitmiş subscription'ları
        $expiredCount = $this->expireSubscriptions();

        // 2. Trial bitmek üzere olanları bilgilendir (2 gün kala)
        $trialWarningCount = $this->warnTrialEnding();

        $this->info("✅ Expired: {$expiredCount}, Trial warnings: {$trialWarningCount}");

        Log::info('✅ Subscription check completed', [
            'expired' => $expiredCount,
            'trial_warnings' => $trialWarningCount,
        ]);

        return 0;
    }

    /**
     * Süre dolmuş subscription'ları expire et
     */
    protected function expireSubscriptions(): int
    {
        $expired = Subscription::whereIn('status', ['active', 'trial'])
            ->where(function ($query) {
                $query->where('current_period_end', '<', now())
                      ->orWhere(function($q) {
                          $q->where('status', 'trial')
                            ->whereNotNull('trial_ends_at')
                            ->where('trial_ends_at', '<', now());
                      });
            })
            ->get();

        foreach ($expired as $subscription) {
            $subscription->update(['status' => 'expired']);

            // Event fire et
            event(new SubscriptionExpired($subscription));

            $this->line("  ⏰ Expired: Subscription #{$subscription->subscription_id} (User: {$subscription->user->email})");
        }

        return $expired->count();
    }

    /**
     * 2 gün içinde bitecek trial'ları uyar
     */
    protected function warnTrialEnding(): int
    {
        $endingSoon = Subscription::where('status', 'trial')
            ->whereNotNull('trial_ends_at')
            ->whereBetween('trial_ends_at', [now(), now()->addDays(2)])
            ->get();

        foreach ($endingSoon as $subscription) {
            $daysRemaining = now()->diffInDays($subscription->trial_ends_at);

            // Event fire et
            event(new TrialEnding($subscription, $daysRemaining));

            $this->line("  ⚠️ Trial ending in {$daysRemaining} days: {$subscription->user->email}");
        }

        return $endingSoon->count();
    }
}
