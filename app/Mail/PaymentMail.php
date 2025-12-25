<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentMail extends Mailable
{
    use Queueable, SerializesModels;

    public $htmlContent;
    public $emailSubject;
    public $recipientEmail;

    public function __construct(string $subject, string $htmlContent, string $recipientEmail)
    {
        $this->emailSubject = $subject;
        $this->htmlContent = $htmlContent;
        $this->recipientEmail = $recipientEmail;
    }

    public function build()
    {
        return $this->to($this->recipientEmail)
            ->subject($this->emailSubject)
            ->html($this->htmlContent);
    }
}
