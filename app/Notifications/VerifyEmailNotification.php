<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use App\Mail\VerifyEmailMail;
use Modules\Mail\App\Services\MailTemplateService;

class VerifyEmailNotification extends BaseVerifyEmail implements ShouldQueue
{
    use Queueable;

    /**
     * Tenant-aware verification URL oluştur
     *
     * @param  mixed  $notifiable
     * @return string
     */
    protected function verificationUrl($notifiable)
    {
        // Tenant domain'ini al
        $tenantDomain = null;
        if (function_exists('tenant') && tenant()) {
            $domain = tenant()->domains()->first();
            if ($domain) {
                $tenantDomain = $domain->domain;
            }
        }

        // Tenant domain varsa URL'yi ona göre oluştur
        if ($tenantDomain) {
            $baseUrl = 'https://' . $tenantDomain;

            // Geçici olarak APP_URL'yi değiştir
            $originalUrl = Config::get('app.url');
            Config::set('app.url', $baseUrl);
            URL::forceRootUrl($baseUrl);

            $url = URL::temporarySignedRoute(
                'verification.verify',
                Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
                [
                    'id' => $notifiable->getKey(),
                    'hash' => sha1($notifiable->getEmailForVerification()),
                ]
            );

            // APP_URL'yi geri al
            Config::set('app.url', $originalUrl);
            URL::forceRootUrl($originalUrl);

            return $url;
        }

        // Fallback: parent method
        return parent::verificationUrl($notifiable);
    }
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
            'site_name' => setting('site_title', config('app.name')),
        ];

        $locale = app()->getLocale();
        $subject = $templateService->renderContent($template->getSubjectForLocale($locale), $variables);
        $content = $templateService->renderContent($template->getContentForLocale($locale), $variables);

        // Return Mailable for HTML emails
        return new VerifyEmailMail($subject, $content, $notifiable->email);
    }
}
