<?php

namespace Modules\Mail\App\Mail;

use App\Models\User;
use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionRenewalMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public Subscription $subscription
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Abonelik Yenileme Hatırlatması',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail::emails.subscription-renewal',
            with: [
                'user' => $this->user,
                'subscription' => $this->subscription,
                'renewalDate' => $this->subscription->ends_at->format('d.m.Y'),
                'amount' => number_format($this->subscription->price_per_cycle, 2, ',', '.') . ' ₺',
            ],
        );
    }
}
