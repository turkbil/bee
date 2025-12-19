<?php

namespace App\Tenancy;

use Illuminate\Support\Facades\Config;
use Stancl\Tenancy\Contracts\TenancyBootstrapper;
use Stancl\Tenancy\Contracts\Tenant;

/**
 * Mail Config Bootstrapper
 *
 * Tenant context initialized olduğunda otomatik olarak mail config'i yükler
 * Bu sayede hem web hem console'da (queue, scheduler vb.) çalışır
 */
class MailConfigBootstrapper implements TenancyBootstrapper
{
    /**
     * Tenant context başlatıldığında mail config'i yükle
     */
    public function bootstrap(Tenant $tenant)
    {
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

            // AWS SES ayarları
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
            // Hata durumunda sessizce geç
            if (config('app.debug')) {
                logger()->warning('Mail config bootstrap failed', [
                    'tenant_id' => $tenant->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Tenant context sonlandırıldığında cleanup (gerekli değil)
     */
    public function revert()
    {
        // Mail config revert etmeye gerek yok
        // Her tenant kendi config'ini yükler
    }
}
