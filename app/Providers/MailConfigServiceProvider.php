<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;

class MailConfigServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Database ve tenant context hazır olduktan sonra mail config'i güncelle
        if ($this->app->runningInConsole() && !$this->app->runningUnitTests()) {
            // Console komutlarında setting() kullanmayalım (tenant context olmayabilir)
            return;
        }

        try {
            // Mail driver setting'den al
            $mailDriver = setting('mail_driver');
            if ($mailDriver) {
                Config::set('mail.default', $mailDriver);
            }

            // From address ve name
            $mailFromAddress = setting('mail_from_address');
            if ($mailFromAddress) {
                Config::set('mail.from.address', $mailFromAddress);
            }

            $mailFromName = setting('mail_from_name');
            if ($mailFromName) {
                Config::set('mail.from.name', $mailFromName);
            }

            // SMTP ayarları (eğer mail_driver = smtp ise)
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

            // AWS SES ayarları (eğer mail_driver = ses ise)
            if ($mailDriver === 'ses') {
                $awsAccessKeyId = setting('aws_access_key_id');
                if ($awsAccessKeyId) {
                    Config::set('services.ses.key', $awsAccessKeyId);
                }

                $awsSecretAccessKey = setting('aws_secret_access_key');
                if ($awsSecretAccessKey) {
                    Config::set('services.ses.secret', $awsSecretAccessKey);
                }

                $awsRegion = setting('mail_aws_region');
                if ($awsRegion) {
                    Config::set('services.ses.region', $awsRegion);
                }

                $awsConfigSet = setting('aws_ses_configuration_set');
                if ($awsConfigSet) {
                    Config::set('services.ses.options.ConfigurationSetName', $awsConfigSet);
                }
            }
        } catch (\Exception $e) {
            // Hata durumunda sessizce geç (database henüz hazır değilse vb.)
            // Log yazabiliriz ama uygulamayı durdurmayalım
            if (config('app.debug')) {
                logger()->warning('Mail config loading failed: ' . $e->getMessage());
            }
        }
    }
}
