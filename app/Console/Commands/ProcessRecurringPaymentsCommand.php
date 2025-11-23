<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;
use App\Services\Auth\SubscriptionService;
use Modules\Mail\App\Services\MailService;

class ProcessRecurringPaymentsCommand extends Command
{
    protected $signature = 'subscription:process-recurring';
    protected $description = 'Process recurring subscription payments';

    public function handle(SubscriptionService $subscriptionService, MailService $mailService): int
    {
        $this->info('Processing recurring payments...');

        $graceDays = (int) setting('auth_subscription_grace_days', 3);

        // Find subscriptions due for renewal
        $dueForRenewal = Subscription::where('status', 'active')
            ->where('auto_renewal', true)
            ->whereDate('ends_at', now()->toDateString())
            ->with('user', 'plan')
            ->get();

        $success = 0;
        $failed = 0;

        foreach ($dueForRenewal as $subscription) {
            // TODO: Process actual payment via PayTR
            // For now, just renew the subscription

            try {
                $subscriptionService->renew($subscription);
                $mailService->sendPaymentSuccess($subscription->user, $subscription);
                $this->line("Renewed: {$subscription->user->email}");
                $success++;
            } catch (\Exception $e) {
                // Add grace period
                $subscription->update([
                    'ends_at' => $subscription->ends_at->addDays($graceDays)
                ]);
                $mailService->sendPaymentFailed($subscription->user, $subscription, $e->getMessage());
                $this->error("Failed: {$subscription->user->email} - {$e->getMessage()}");
                $failed++;
            }
        }

        $this->info("Processed {$dueForRenewal->count()} subscriptions: {$success} success, {$failed} failed");

        return Command::SUCCESS;
    }
}
