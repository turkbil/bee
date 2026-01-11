<?php

namespace Modules\Mail\App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Hoş Geldiniz! - ' . config('app.name'),
        );
    }

    public function content(): Content
    {
        $trialDays = (int) setting('auth_registration_trial_days', 0);

        return new Content(
            view: 'mail::emails.welcome',
            with: [
                'user' => $this->user,
                'trialDays' => $trialDays,
                'loginUrl' => route('login'),
            ],
        );
    }
}
