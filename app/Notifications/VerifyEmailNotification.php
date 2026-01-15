<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use App\Mail\VerifyEmailMail;
use Modules\Mail\App\Services\MailTemplateService;

class VerifyEmailNotification extends BaseVerifyEmail implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    /**
     * Unique job key - aynı kullanıcıya aynı anda sadece 1 mail gönder
     */
    public function uniqueId(): string
    {
        return 'verify_email_' . ($this->tenantId ?? 0) . '_' . ($this->notifiable->id ?? 0);
    }

    /**
     * Unique lock süresi (saniye) - 60 saniye içinde tekrar gönderilemez
     */
    public function uniqueFor(): int
    {
        return 60;
    }

    /**
     * Tenant ID - Queue job çalışırken tenant context'i korumak için
     */
    public ?int $tenantId = null;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        // Tenant ID'yi sakla (queue job çalışırken kullanılacak)
        if (function_exists('tenant') && tenant()) {
            $this->tenantId = tenant()->id;
        }
    }

    /**
     * Queue job çalışmadan önce tenant context'i initialize et
     */
    protected function initializeTenantContext(): void
    {
        if ($this->tenantId && function_exists('tenancy')) {
            $currentTenant = tenant();
            if (!$currentTenant || $currentTenant->id !== $this->tenantId) {
                $tenant = \App\Models\Tenant::find($this->tenantId);
                if ($tenant) {
                    tenancy()->initialize($tenant);
                }
            }
        }
    }

    /**
     * Tenant-aware verification URL oluştur
     *
     * @param  mixed  $notifiable
     * @return string
     */
    protected function verificationUrl($notifiable)
    {
        // Queue job'da çalışıyorsak tenant context'i initialize et
        $this->initializeTenantContext();

        // APP_URL kullanarak signed URL oluştur
        // Config değişikliği yapmıyoruz, mevcut ayarları kullanıyoruz
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }
    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Mail\Mailable|\Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        // Queue job'da çalışıyorsak tenant context'i initialize et
        $this->initializeTenantContext();

        // Mail config'i tenant settings'den yükle
        $this->loadMailConfig();

        $verificationUrl = $this->verificationUrl($notifiable);

        // 🔧 DEBUG: URL'i log'la
        \Log::info('📧 EMAIL VERIFICATION URL GENERATED', [
            'user_id' => $notifiable->id,
            'email' => $notifiable->email,
            'url' => $verificationUrl,
        ]);

        // Geçici olarak sadece fallback kullan (template render sorunu varsa)
        return (new MailMessage)
            ->subject('Email Adresinizi Doğrulayın')
            ->greeting('Merhaba ' . $notifiable->name . ',')
            ->line('Lütfen email adresinizi doğrulamak için aşağıdaki butona tıklayın.')
            ->action('Email Adresimi Doğrula', $verificationUrl)
            ->line('Bu link 60 dakika geçerlidir.')
            ->line('Eğer bu hesabı siz oluşturmadıysanız, herhangi bir işlem yapmanıza gerek yoktur.')
            ->salutation('Saygılarımızla, ' . config('app.name'));
    }

    /**
     * Mail config'i tenant settings'den yükle
     */
    protected function loadMailConfig(): void
    {
        try {
            $mailDriver = setting('mail_driver');
            if ($mailDriver) {
                Config::set('mail.default', $mailDriver);
            }

            $mailFromAddress = setting('mail_from_address');
            if ($mailFromAddress) {
                Config::set('mail.from.address', $mailFromAddress);
            }

            $mailFromName = setting('mail_from_name');
            if ($mailFromName) {
                Config::set('mail.from.name', $mailFromName);
            }

            // SMTP ayarları
            if ($mailDriver === 'smtp') {
                $smtpHost = setting('smtp_host');
                if ($smtpHost) {
                    Config::set('mail.mailers.smtp.host', $smtpHost);
                }

                $smtpPort = setting('smtp_port');
                if ($smtpPort) {
                    Config::set('mail.mailers.smtp.port', (int) $smtpPort);
                }

                $smtpEncryption = setting('smtp_encryption');
                if ($smtpEncryption) {
                    Config::set('mail.mailers.smtp.encryption', $smtpEncryption);
                }

                $smtpUsername = setting('smtp_username');
                if ($smtpUsername) {
                    Config::set('mail.mailers.smtp.username', $smtpUsername);
                }

                $smtpPassword = setting('smtp_password');
                if ($smtpPassword) {
                    Config::set('mail.mailers.smtp.password', $smtpPassword);
                }
            }
        } catch (\Exception $e) {
            \Log::error('📧 VerifyEmailNotification: Mail config loading failed', [
                'error' => $e->getMessage(),
                'tenant_id' => $this->tenantId,
            ]);
        }
    }
}
