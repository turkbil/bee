<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;
use Modules\Mail\App\Services\MailService;

class CheckTrialExpiryCommand extends Command
{
    protected $signature = 'subscription:check-trial';
    protected $description = 'Check for expiring trial subscriptions and send reminders';

    public function handle(MailService $mailService): int
    {
        $this->info('Checking trial expirations...');

        // Find subscriptions with trial ending in 2 days
        $endingSoon = Subscription::whereNotNull('trial_ends_at')
            ->where('trial_ends_at', '>', now())
            ->where('trial_ends_at', '<=', now()->addDays(2))
            ->where('status', 'active')
            ->with('user')
            ->get();

        foreach ($endingSoon as $subscription) {
            $daysLeft = now()->diffInDays($subscription->trial_ends_at);
            $mailService->sendTrialEnding($subscription->user, $daysLeft);
            $this->line("Reminder sent to: {$subscription->user->email} ({$daysLeft} days left)");
        }

        // Find expired trials
        $expired = Subscription::whereNotNull('trial_ends_at')
            ->where('trial_ends_at', '<=', now())
            ->where('status', 'active')
            ->get();

        foreach ($expired as $subscription) {
            // Mark as expired if no payment made
            if (!$subscription->price_per_cycle || $subscription->price_per_cycle == 0) {
                $subscription->update(['status' => 'expired']);
                $this->line("Trial expired: {$subscription->user->email}");
            }
        }

        $this->info("Processed {$endingSoon->count()} reminders, {$expired->count()} expirations");

        return Command::SUCCESS;
    }
}
