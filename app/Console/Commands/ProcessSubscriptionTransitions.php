<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Subscription\App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class ProcessSubscriptionTransitions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:process-transitions {--tenant=* : Specific tenant IDs to process}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process subscription status transitions (active→expired, pending→active)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Processing subscription transitions...');

        $tenantIds = $this->option('tenant');

        if (empty($tenantIds)) {
            // Tüm tenant'ları işle
            $tenants = \Stancl\Tenancy\Tenant::all();
        } else {
            $tenants = \Stancl\Tenancy\Tenant::whereIn('id', $tenantIds)->get();
        }

        $totalExpired = 0;
        $totalActivated = 0;

        foreach ($tenants as $tenant) {
            tenancy()->initialize($tenant);

            $this->info("📦 Processing tenant: {$tenant->id}");

            // 1. Süresi dolan active subscription'ları expired yap
            $expiredCount = $this->expireActiveSubscriptions();
            $totalExpired += $expiredCount;

            // 2. Sırası gelen pending subscription'ları active yap
            $activatedCount = $this->activatePendingSubscriptions();
            $totalActivated += $activatedCount;

            $this->info("   ✅ Expired: {$expiredCount}, Activated: {$activatedCount}");

            tenancy()->end();
        }

        $this->info("🎉 Done! Total expired: {$totalExpired}, Total activated: {$totalActivated}");

        Log::channel('daily')->info('🔄 Subscription transitions processed', [
            'total_expired' => $totalExpired,
            'total_activated' => $totalActivated,
        ]);

        return Command::SUCCESS;
    }

    /**
     * Süresi dolan active subscription'ları expired yap
     */
    protected function expireActiveSubscriptions(): int
    {
        $expiredSubscriptions = Subscription::where('status', 'active')
            ->whereNotNull('current_period_end')
            ->where('current_period_end', '<', now())
            ->get();

        $count = 0;
        $affectedUserIds = [];

        foreach ($expiredSubscriptions as $subscription) {
            $subscription->update(['status' => 'expired']);

            Log::channel('daily')->info('⏰ Subscription expired', [
                'subscription_id' => $subscription->subscription_id,
                'user_id' => $subscription->user_id,
                'ended_at' => $subscription->current_period_end->toDateTimeString(),
            ]);

            $affectedUserIds[] = $subscription->user_id;
            $count++;
        }

        // Etkilenen kullanıcıların subscription_expires_at değerlerini güncelle
        foreach (array_unique($affectedUserIds) as $userId) {
            $user = User::find($userId);
            if ($user) {
                $user->recalculateSubscriptionExpiry();
            }
        }

        return $count;
    }

    /**
     * Sırası gelen pending subscription'ları active yap
     */
    protected function activatePendingSubscriptions(): int
    {
        $pendingSubscriptions = Subscription::where('status', 'pending')
            ->whereNotNull('current_period_start')
            ->where('current_period_start', '<=', now())
            ->get();

        $count = 0;
        $affectedUserIds = [];

        foreach ($pendingSubscriptions as $subscription) {
            // Kullanıcının başka aktif subscription'ı var mı kontrol et
            $hasActive = Subscription::where('user_id', $subscription->user_id)
                ->where('subscription_id', '!=', $subscription->subscription_id)
                ->where('status', 'active')
                ->where('current_period_end', '>', now())
                ->exists();

            // Eğer başka aktif yoksa bu subscription'ı aktif yap
            if (!$hasActive) {
                $subscription->update([
                    'status' => 'active',
                    'started_at' => now(),
                ]);

                Log::channel('daily')->info('✅ Subscription activated from pending', [
                    'subscription_id' => $subscription->subscription_id,
                    'user_id' => $subscription->user_id,
                    'start_date' => $subscription->current_period_start->toDateTimeString(),
                    'end_date' => $subscription->current_period_end->toDateTimeString(),
                ]);

                $affectedUserIds[] = $subscription->user_id;
                $count++;
            }
        }

        // Etkilenen kullanıcıların subscription_expires_at değerlerini güncelle
        foreach (array_unique($affectedUserIds) as $userId) {
            $user = User::find($userId);
            if ($user) {
                $user->recalculateSubscriptionExpiry();
            }
        }

        return $count;
    }
}
