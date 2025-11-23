<?php

namespace Modules\Mail\App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CorporateInviteMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $corporateUser,
        public string $inviteEmail
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->corporateUser->name . ' Sizi Davet Ediyor',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail::emails.corporate-invite',
            with: [
                'corporateUser' => $this->corporateUser,
                'corporateCode' => $this->corporateUser->corporate_code,
                'registerUrl' => route('register') . '?corporate_code=' . $this->corporateUser->corporate_code,
            ],
        );
    }
}
