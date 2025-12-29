<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use App\Mail\VerifyEmailMail;
use Modules\Mail\App\Services\MailTemplateService;

class VerifyEmailNotification extends BaseVerifyEmail implements ShouldQueue
{
    use Queueable;
    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Mail\Mailable|\Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);
        $templateService = app(MailTemplateService::class);
        $template = $templateService->getTemplate('verify_email');

        if (!$template) {
            // Fallback to default if template not found
            return (new MailMessage)
                ->subject('Email Adresinizi Doğrulayın')
                ->line('Lütfen email adresinizi doğrulamak için aşağıdaki butona tıklayın.')
                ->action('Email Adresimi Doğrula', $verificationUrl)
                ->line('Eğer bu hesabı siz oluşturmadıysanız, herhangi bir işlem yapmanıza gerek yoktur.');
        }

        $variables = [
            'user_name' => $notifiable->name,
            'verification_url' => $verificationUrl,
            'site_name' => setting('site_name', config('app.name')),
        ];

        $locale = app()->getLocale();
        $subject = $templateService->renderContent($template->getSubjectForLocale($locale), $variables);
        $content = $templateService->renderContent($template->getContentForLocale($locale), $variables);

        // Return Mailable for HTML emails
        return new VerifyEmailMail($subject, $content, $notifiable->email);
    }
}
