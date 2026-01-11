<?php

namespace Modules\Mail\App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TrialEndingMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public int $daysLeft
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Deneme Süreniz {$this->daysLeft} Gün İçinde Bitiyor",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail::emails.trial-ending',
            with: [
                'user' => $this->user,
                'daysLeft' => $this->daysLeft,
                'subscribeUrl' => route('subscription.plans'),
            ],
        );
    }
}
