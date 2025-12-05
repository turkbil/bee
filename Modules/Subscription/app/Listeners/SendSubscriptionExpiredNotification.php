<?php

namespace Modules\Subscription\App\Listeners;

use Modules\Subscription\App\Events\SubscriptionExpired;
use Illuminate\Support\Facades\Log;

class SendSubscriptionExpiredNotification
{
    public function handle(SubscriptionExpired $event)
    {
        $subscription = $event->subscription;
        $user = $subscription->user;

        Log::info('📧 Subscription Expired Notification', [
            'user_id' => $user->id,
            'subscription_id' => $subscription->subscription_id,
            'plan' => $subscription->plan->title ?? 'Unknown',
        ]);

        // TODO: Send email/notification to user
        // Mail::to($user->email)->send(new SubscriptionExpiredMail($subscription));
    }
}
