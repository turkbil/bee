<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Config;

class SendEmailVerificationNotificationWithSettingCheck
{
    /**
     * Handle the event.
     *
     * @param  \Illuminate\Auth\Events\Registered  $event
     * @return void
     */
    public function handle(Registered $event): void
    {
        \Log::info('📧 LISTENER START', ['user_id' => $event->user->id ?? 'N/A']);

        // Kullanıcı email doğrulama interface'ini implement etmiş mi kontrol et
        if (! $event->user instanceof MustVerifyEmail) {
            \Log::info('📧 LISTENER: Not MustVerifyEmail, skipping');
            return;
        }

        // Email zaten doğrulanmışsa gönderme
        if ($event->user->hasVerifiedEmail()) {
            \Log::info('📧 LISTENER: Already verified, skipping');
            return;
        }

        // Settings'den auth_registration_email_verify ayarını kontrol et
        // Ayar yoksa veya 0 ise email gönderme
        $emailVerificationEnabled = setting('auth_registration_email_verify', 0);
        \Log::info('📧 LISTENER: Setting check', ['enabled' => $emailVerificationEnabled]);

        if ($emailVerificationEnabled != 1) {
            \Log::info('📧 LISTENER: Email verify disabled, skipping');
            return;
        }

        // Mail config'i tenant settings'den yükle (tenant context artık mevcut)
        $this->configureMailFromSettings();

        // Tüm kontroller geçti, email doğrulama notification'ını gönder
        \Log::info('📧 VERIFY EMAIL: Sending notification to queue', [
            'user_id' => $event->user->id,
            'email' => $event->user->email,
        ]);

        $event->user->sendEmailVerificationNotification();

        \Log::info('📧 VERIFY EMAIL: Notification dispatched');
    }

    /**
     * Mail konfigürasyonunu tenant settings'den yükle
     */
    protected function configureMailFromSettings(): void
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

            \Log::info('📧 Mail config loaded from settings', [
                'driver' => $mailDriver,
                'from' => $mailFromAddress,
                'smtp_host' => $mailDriver === 'smtp' ? setting('smtp_host') : 'N/A',
            ]);

        } catch (\Exception $e) {
            \Log::error('📧 Mail config loading failed', ['error' => $e->getMessage()]);
        }
    }
}
