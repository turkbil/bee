<?php

namespace Modules\Subscription\App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Subscription\App\Models\Subscription;

class TrialEnding
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $subscription;
    public $daysRemaining;

    public function __construct(Subscription $subscription, int $daysRemaining)
    {
        $this->subscription = $subscription;
        $this->daysRemaining = $daysRemaining;
    }
}
