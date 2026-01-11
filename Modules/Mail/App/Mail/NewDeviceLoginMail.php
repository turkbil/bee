<?php

namespace Modules\Mail\App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewDeviceLoginMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $ip,
        public string $userAgent,
        public string $location = ''
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Yeni Cihazdan Giriş Yapıldı',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail::emails.new-device-login',
            with: [
                'user' => $this->user,
                'ip' => $this->ip,
                'userAgent' => $this->userAgent,
                'location' => $this->location,
                'time' => now()->format('d.m.Y H:i'),
                'devicesUrl' => route('profile.devices'),
            ],
        );
    }
}
