<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class SendEmailVerificationNotificationWithSettingCheck
{
    /**
     * Handle the event.
     *
     * @param  \Illuminate\Auth\Events\Registered  $event
     * @return bool|void Returns false to stop event propagation
     */
    public function handle(Registered $event)
    {
        // Kullanıcı email doğrulama interface'ini implement etmiş mi kontrol et
        if (! $event->user instanceof MustVerifyEmail) {
            return;
        }

        // Email zaten doğrulanmışsa gönderme
        if ($event->user->hasVerifiedEmail()) {
            return;
        }

        // 🔒 DUPLICATE PREVENTION: Bu kullanıcı için 60 saniye içinde sadece 1 email gönder
        $lockKey = 'verify_email_lock_' . (tenant()?->id ?? 0) . '_' . $event->user->id;

        // Atomic lock - eğer zaten gönderilmişse false döner ve early return yapar
        $acquired = Cache::lock($lockKey, 60)->get();

        if (!$acquired) {
            \Log::info('📧 LISTENER: Duplicate prevented by lock', [
                'user_id' => $event->user->id,
                'lock_key' => $lockKey,
            ]);
            return false; // Stop event propagation
        }

        // Settings'den auth_registration_email_verify ayarını kontrol et
        $emailVerificationEnabled = setting('auth_registration_email_verify', 0);

        if ($emailVerificationEnabled != 1) {
            return false; // Stop event propagation - setting disabled
        }

        // Mail config'i tenant settings'den yükle
        $this->configureMailFromSettings();

        // Email doğrulama notification'ını gönder
        \Log::info('📧 VERIFY EMAIL: Sending notification', [
            'user_id' => $event->user->id,
            'email' => $event->user->email,
        ]);

        $event->user->sendEmailVerificationNotification();

        \Log::info('📧 VERIFY EMAIL: Notification dispatched');

        return false; // 🔒 Stop event propagation - prevent other listeners from sending duplicate emails
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
