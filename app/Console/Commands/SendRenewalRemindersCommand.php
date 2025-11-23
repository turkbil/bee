<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;
use Modules\Mail\App\Services\MailService;

class SendRenewalRemindersCommand extends Command
{
    protected $signature = 'subscription:send-reminders';
    protected $description = 'Send renewal reminders for subscriptions expiring soon';

    public function handle(MailService $mailService): int
    {
        $this->info('Sending renewal reminders...');

        $reminderDays = (int) setting('auth_subscription_reminder_days', 7);

        // Find subscriptions expiring within reminder period
        $expiringSoon = Subscription::where('status', 'active')
            ->where('auto_renewal', true)
            ->where('ends_at', '>', now())
            ->where('ends_at', '<=', now()->addDays($reminderDays))
            ->whereDate('ends_at', now()->addDays($reminderDays)->toDateString())
            ->with('user', 'plan')
            ->get();

        foreach ($expiringSoon as $subscription) {
            $mailService->sendSubscriptionRenewal($subscription->user, $subscription);
            $this->line("Reminder sent to: {$subscription->user->email}");
        }

        $this->info("Sent {$expiringSoon->count()} renewal reminders");

        return Command::SUCCESS;
    }
}
