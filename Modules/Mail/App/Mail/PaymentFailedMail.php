<?php

namespace Modules\Mail\App\Mail;

use App\Models\User;
use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentFailedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public Subscription $subscription,
        public string $reason = ''
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Ödeme Başarısız - İşlem Yapmanız Gerekiyor',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail::emails.payment-failed',
            with: [
                'user' => $this->user,
                'subscription' => $this->subscription,
                'reason' => $this->reason,
                'retryUrl' => route('subscription.payment', $this->subscription->id),
            ],
        );
    }
}
