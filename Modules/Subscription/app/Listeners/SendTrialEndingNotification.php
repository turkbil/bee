<?php

namespace Modules\Subscription\App\Listeners;

use Modules\Subscription\App\Events\TrialEnding;
use Illuminate\Support\Facades\Log;

class SendTrialEndingNotification
{
    public function handle(TrialEnding $event)
    {
        $subscription = $event->subscription;
        $user = $subscription->user;
        $daysRemaining = $event->daysRemaining;

        Log::info('📧 Trial Ending Notification', [
            'user_id' => $user->id,
            'subscription_id' => $subscription->subscription_id,
            'days_remaining' => $daysRemaining,
        ]);

        // TODO: Send email/notification to user
        // Mail::to($user->email)->send(new TrialEndingMail($subscription, $daysRemaining));
    }
}
