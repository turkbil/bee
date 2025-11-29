<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class VerifySubscriptions extends Command
{
    protected $signature = 'subscriptions:verify';
    protected $description = 'Verify and expire subscriptions that have passed their current_period_end date';

    public function handle()
    {
        $this->info('🔍 Checking subscriptions for expiration...');

        // 🔍 Süre dolmuş ama hala "active" olan subscription'ları bul
        $expiredCount = DB::table('subscriptions')
            ->where('status', 'active')
            ->where('current_period_end', '<', now())
            ->count();

        if ($expiredCount === 0) {
            $this->info('✅ No expired subscriptions found. All good!');
            Log::info('Subscription Verification: No expired subscriptions');
            return 0;
        }

        $this->warn("⚠️  Found {$expiredCount} expired subscriptions. Updating...");

        // 🔄 Status'u "expired" yap
        $updated = DB::table('subscriptions')
            ->where('status', 'active')
            ->where('current_period_end', '<', now())
            ->update([
                'status' => 'expired',
                'updated_at' => now()
            ]);

        // 📊 Log kaydet
        $expiredSubscriptions = DB::table('subscriptions')
            ->where('status', 'expired')
            ->where('current_period_end', '<', now())
            ->get(['subscription_id', 'customer_id', 'current_period_end']);

        foreach ($expiredSubscriptions as $subscription) {
            $user = User::find($subscription->customer_id);
            
            if ($user) {
                Log::info('Subscription Expired', [
                    'subscription_id' => $subscription->subscription_id,
                    'customer_id' => $subscription->customer_id,
                    'user_email' => $user->email,
                    'expired_at' => $subscription->current_period_end,
                ]);

                // 📧 Email gönder (opsiyonel - gelecekte eklenebilir)
                // Mail::to($user->email)->send(new SubscriptionExpiredMail($subscription));
            }
        }

        $this->info("✅ Successfully expired {$updated} subscriptions");
        Log::info("Subscription Verification: Expired {$updated} subscriptions");

        return 0;
    }
}
