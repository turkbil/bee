<?php

namespace Modules\Mail\App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TwoFactorCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $code
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Doğrulama Kodunuz: ' . $this->code,
        );
    }

    public function content(): Content
    {
        $expiryMinutes = (int) setting('auth_security_2fa_expiry', 5);

        return new Content(
            view: 'mail::emails.two-factor-code',
            with: [
                'user' => $this->user,
                'code' => $this->code,
                'expiryMinutes' => $expiryMinutes,
            ],
        );
    }
}
