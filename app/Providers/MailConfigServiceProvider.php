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
     *
     * ✅ FIX: TenancyInitialized event'ini dinle, tenant hazır olduğunda config yükle
     * Bu sayede hem web hem console'da çalışır (queue jobs, scheduler vb.)
     */
    public function boot(): void
    {
        // Tenancy initialized event'ini dinle
        \Illuminate\Support\Facades\Event::listen(
            \Stancl\Tenancy\Events\TenancyInitialized::class,
            function ($event) {
                $this->loadMailConfig();
            }
        );

        // Eğer zaten tenant context varsa direkt yükle (web request)
        if (function_exists('tenant') && tenant()) {
            $this->loadMailConfig();
        }
    }

    /**
     * Settings'ten mail konfigürasyonunu Laravel Config'e yükle
     */
    protected function loadMailConfig(): void
    {
        // Debug log (geçici)
        \Log::info('📧 MailConfigServiceProvider: Loading mail config...', [
            'tenant_id' => tenant('id'),
            'has_tenant' => function_exists('tenant') && tenant() ? 'YES' : 'NO',
        ]);

        try {
            // Mail driver setting'den al
            $mailDriver = setting('mail_driver');
            if ($mailDriver) {
                Config::set('mail.default', $mailDriver);
                \Log::info('✅ Mail driver set: ' . $mailDriver);
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
                    \Log::info('✅ SMTP host set: ' . $smtpHost);
                }

                $smtpPort = setting('smtp_port');
                if ($smtpPort) {
                    Config::set('mail.mailers.smtp.port', (int) $smtpPort);
                    \Log::info('✅ SMTP port set: ' . $smtpPort);
                }

                $smtpEncryption = setting('smtp_encryption');
                if ($smtpEncryption) {
                    Config::set('mail.mailers.smtp.encryption', $smtpEncryption);
                    \Log::info('✅ SMTP encryption set: ' . $smtpEncryption);
                }

                $smtpUsername = setting('smtp_username');
                if ($smtpUsername) {
                    Config::set('mail.mailers.smtp.username', $smtpUsername);
                }

                $smtpPassword = setting('smtp_password');
                if ($smtpPassword) {
                    Config::set('mail.mailers.smtp.password', $smtpPassword);
                }

                \Log::info('✅ Mail config loaded successfully');
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
