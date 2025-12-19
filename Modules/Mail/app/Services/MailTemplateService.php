<?php

declare(strict_types=1);

namespace Modules\Mail\App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Mail\App\Models\MailTemplate;

class MailTemplateService
{
    /**
     * Şablon key'ine göre template al
     * Önce tenant tablosuna bakar, yoksa central'dan çeker
     */
    public function getTemplate(string $key): ?MailTemplate
    {
        // 1. Önce tenant tablosuna bak
        $tenantTemplate = MailTemplate::where('key', $key)->first();

        if ($tenantTemplate) {
            return $tenantTemplate;
        }

        // 2. Yoksa central'dan çek
        return $this->getCentralTemplate($key);
    }

    /**
     * Central veritabanından şablon çek
     */
    protected function getCentralTemplate(string $key): ?MailTemplate
    {
        $centralData = DB::connection('mysql')
            ->table('mail_templates')
            ->where('key', $key)
            ->first();

        if (!$centralData) {
            return null;
        }

        // Eloquent model olarak dön (memory'de)
        $template = new MailTemplate();
        $template->id = $centralData->id;
        $template->key = $centralData->key;
        $template->name = $centralData->name;
        $template->subject = json_decode($centralData->subject, true);
        $template->content = json_decode($centralData->content, true);
        $template->variables = json_decode($centralData->variables ?? '[]', true);
        $template->category = $centralData->category;
        $template->is_active = (bool) $centralData->is_active;
        $template->created_at = $centralData->created_at;
        $template->updated_at = $centralData->updated_at;
        $template->exists = true; // Model'in kaydedilmiş olduğunu belirt

        return $template;
    }

    /**
     * Tüm şablonları listele (tenant overrides ile birleştirilmiş)
     */
    public function getAllTemplates(): array
    {
        // Central şablonları al ve JSON decode et
        $centralTemplates = DB::connection('mysql')
            ->table('mail_templates')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'key' => $item->key,
                    'name' => $item->name,
                    'subject' => json_decode($item->subject, true),
                    'content' => json_decode($item->content, true),
                    'variables' => json_decode($item->variables ?? '[]', true),
                    'category' => $item->category,
                    'is_active' => (bool) $item->is_active,
                ];
            })
            ->keyBy('key')
            ->toArray();

        // Tenant overrides al
        $tenantTemplates = MailTemplate::all()->keyBy('key')->toArray();

        // Birleştir (tenant öncelikli)
        $merged = [];

        foreach ($centralTemplates as $key => $central) {
            if (isset($tenantTemplates[$key])) {
                // Tenant override var
                $merged[$key] = array_merge($central, $tenantTemplates[$key], [
                    'is_overridden' => true,
                    'source' => 'tenant',
                ]);
            } else {
                // Sadece central
                $merged[$key] = array_merge($central, [
                    'is_overridden' => false,
                    'source' => 'central',
                ]);
            }
        }

        // Sadece tenant'ta olan şablonları da ekle
        foreach ($tenantTemplates as $key => $tenant) {
            if (!isset($merged[$key])) {
                $merged[$key] = array_merge($tenant, [
                    'is_overridden' => true,
                    'source' => 'tenant',
                ]);
            }
        }

        return array_values($merged);
    }

    /**
     * Tenant için şablon oluştur veya güncelle
     */
    public function saveTemplate(string $key, array $data): MailTemplate
    {
        return MailTemplate::updateOrCreate(
            ['key' => $key],
            $data
        );
    }

    /**
     * Tenant override'ı sil (central'a geri dön)
     */
    public function resetToDefault(string $key): bool
    {
        return MailTemplate::where('key', $key)->delete() > 0;
    }

    /**
     * Template içindeki değişkenleri değiştir
     */
    public function renderContent(string $content, array $variables): string
    {
        foreach ($variables as $key => $value) {
            $content = str_replace('{{' . $key . '}}', (string) $value, $content);
            $content = str_replace('{{ ' . $key . ' }}', (string) $value, $content);
        }

        return $content;
    }

    /**
     * Kurumsal mail wrapper - tüm mailler için ortak tasarım
     */
    protected function wrapInCorporateDesign(string $content, string $title = ''): string
    {
        return '
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, \'Helvetica Neue\', Arial, sans-serif; background-color: #f8fafc;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f8fafc;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" style="background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);">
                    <!-- Header -->
                    <tr>
                        <td style="padding: 32px 40px; background: linear-gradient(135deg, #1e293b 0%, #334155 100%); border-radius: 12px 12px 0 0;">
                            <h1 style="margin: 0; font-size: 24px; font-weight: 700; color: #ffffff; letter-spacing: -0.025em;">' . $title . '</h1>
                        </td>
                    </tr>
                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px;">
                            ' . $content . '
                        </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                        <td style="padding: 24px 40px; background-color: #f1f5f9; border-radius: 0 0 12px 12px; border-top: 1px solid #e2e8f0;">
                            <p style="margin: 0; font-size: 13px; color: #64748b; text-align: center;">
                                Bu mail otomatik olarak gönderilmiştir. Lütfen yanıtlamayınız.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';
    }

    /**
     * Varsayılan şablonları seed et (central database için)
     */
    public function seedDefaultTemplates(): void
    {
        $templates = [
            [
                'key' => 'welcome',
                'name' => 'Hoş Geldin Maili',
                'description' => 'Yeni kayıt olan kullanıcılara gönderilir',
                'subject' => ['tr' => 'Hoş Geldiniz! 🎉', 'en' => 'Welcome! 🎉'],
                'content' => [
                    'tr' => $this->wrapInCorporateDesign('
                        <p style="margin: 0 0 20px; font-size: 16px; line-height: 1.6; color: #334155;">
                            Merhaba <strong style="color: #1e293b;">{{user_name}}</strong>,
                        </p>
                        <p style="margin: 0 0 24px; font-size: 16px; line-height: 1.6; color: #334155;">
                            Platformumuza katıldığınız için çok mutluyuz! Artık tüm özelliklere erişebilirsiniz.
                        </p>
                        <div style="background-color: #f0fdf4; border-left: 4px solid #22c55e; padding: 16px 20px; border-radius: 0 8px 8px 0; margin-bottom: 24px;">
                            <p style="margin: 0; font-size: 14px; color: #166534;">
                                ✅ Hesabınız başarıyla oluşturuldu
                            </p>
                        </div>
                        <p style="margin: 0; font-size: 14px; color: #64748b;">
                            Herhangi bir sorunuz olursa destek ekibimize ulaşabilirsiniz.
                        </p>
                    ', 'Hoş Geldiniz! 🎉'),
                    'en' => $this->wrapInCorporateDesign('
                        <p style="margin: 0 0 20px; font-size: 16px; line-height: 1.6; color: #334155;">
                            Hello <strong style="color: #1e293b;">{{user_name}}</strong>,
                        </p>
                        <p style="margin: 0 0 24px; font-size: 16px; line-height: 1.6; color: #334155;">
                            We are thrilled to have you on our platform! You can now access all features.
                        </p>
                        <div style="background-color: #f0fdf4; border-left: 4px solid #22c55e; padding: 16px 20px; border-radius: 0 8px 8px 0; margin-bottom: 24px;">
                            <p style="margin: 0; font-size: 14px; color: #166534;">
                                ✅ Your account has been created successfully
                            </p>
                        </div>
                    ', 'Welcome! 🎉'),
                ],
                'variables' => ['user_name', 'user_email'],
                'category' => 'auth',
                'display_order' => 1,
            ],
            [
                'key' => 'trial_ending',
                'name' => 'Deneme Süresi Bitiyor',
                'description' => 'Deneme süresi bitmeden önce hatırlatma',
                'subject' => ['tr' => '⏰ Deneme süreniz bitiyor', 'en' => '⏰ Your trial is ending'],
                'content' => [
                    'tr' => $this->wrapInCorporateDesign('
                        <p style="margin: 0 0 20px; font-size: 16px; line-height: 1.6; color: #334155;">
                            Merhaba <strong style="color: #1e293b;">{{user_name}}</strong>,
                        </p>
                        <div style="background-color: #fef3c7; border-left: 4px solid #f59e0b; padding: 16px 20px; border-radius: 0 8px 8px 0; margin-bottom: 24px;">
                            <p style="margin: 0; font-size: 14px; color: #92400e;">
                                ⚠️ Deneme süreniz <strong>{{days_left}} gün</strong> sonra sona erecek
                            </p>
                        </div>
                        <p style="margin: 0 0 24px; font-size: 16px; line-height: 1.6; color: #334155;">
                            Hizmetlerimizden kesintisiz yararlanmaya devam etmek için abonelik planlarımıza göz atın.
                        </p>
                        <table role="presentation" cellspacing="0" cellpadding="0">
                            <tr>
                                <td style="border-radius: 8px; background-color: #3b82f6;">
                                    <a href="#" style="display: inline-block; padding: 14px 28px; font-size: 14px; font-weight: 600; color: #ffffff; text-decoration: none;">
                                        Planları İncele →
                                    </a>
                                </td>
                            </tr>
                        </table>
                    ', 'Deneme Süreniz Bitiyor ⏰'),
                    'en' => $this->wrapInCorporateDesign('
                        <p style="margin: 0 0 20px; font-size: 16px; line-height: 1.6; color: #334155;">
                            Hello <strong style="color: #1e293b;">{{user_name}}</strong>,
                        </p>
                        <div style="background-color: #fef3c7; border-left: 4px solid #f59e0b; padding: 16px 20px; border-radius: 0 8px 8px 0; margin-bottom: 24px;">
                            <p style="margin: 0; font-size: 14px; color: #92400e;">
                                ⚠️ Your trial ends in <strong>{{days_left}} days</strong>
                            </p>
                        </div>
                    ', 'Trial Ending ⏰'),
                ],
                'variables' => ['user_name', 'days_left'],
                'category' => 'subscription',
                'display_order' => 10,
            ],
            [
                'key' => 'payment_success',
                'name' => 'Başarılı Ödeme',
                'description' => 'Ödeme başarıyla alındığında gönderilir',
                'subject' => ['tr' => '✅ Ödemeniz alındı', 'en' => '✅ Payment received'],
                'content' => [
                    'tr' => $this->wrapInCorporateDesign('
                        <p style="margin: 0 0 20px; font-size: 16px; line-height: 1.6; color: #334155;">
                            Merhaba <strong style="color: #1e293b;">{{user_name}}</strong>,
                        </p>
                        <div style="background-color: #f0fdf4; border-left: 4px solid #22c55e; padding: 16px 20px; border-radius: 0 8px 8px 0; margin-bottom: 24px;">
                            <p style="margin: 0; font-size: 14px; color: #166534;">
                                ✅ Ödemeniz başarıyla alınmıştır
                            </p>
                        </div>
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-bottom: 24px;">
                            <tr>
                                <td style="padding: 16px; background-color: #f8fafc; border-radius: 8px;">
                                    <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0;">
                                                <span style="font-size: 13px; color: #64748b;">Plan</span>
                                            </td>
                                            <td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0; text-align: right;">
                                                <strong style="font-size: 14px; color: #1e293b;">{{plan_name}}</strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0;">
                                                <span style="font-size: 13px; color: #64748b;">Tutar</span>
                                            </td>
                                            <td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0; text-align: right;">
                                                <strong style="font-size: 14px; color: #1e293b;">{{amount}}</strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 8px 0;">
                                                <span style="font-size: 13px; color: #64748b;">İşlem No</span>
                                            </td>
                                            <td style="padding: 8px 0; text-align: right;">
                                                <code style="font-size: 12px; color: #64748b; background: #e2e8f0; padding: 2px 6px; border-radius: 4px;">{{transaction_id}}</code>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    ', 'Ödeme Başarılı ✅'),
                    'en' => $this->wrapInCorporateDesign('
                        <p style="margin: 0 0 20px; font-size: 16px; line-height: 1.6; color: #334155;">
                            Hello <strong style="color: #1e293b;">{{user_name}}</strong>,
                        </p>
                        <div style="background-color: #f0fdf4; border-left: 4px solid #22c55e; padding: 16px 20px; border-radius: 0 8px 8px 0; margin-bottom: 24px;">
                            <p style="margin: 0; font-size: 14px; color: #166534;">
                                ✅ Your payment has been received
                            </p>
                        </div>
                    ', 'Payment Successful ✅'),
                ],
                'variables' => ['user_name', 'amount', 'plan_name', 'transaction_id'],
                'category' => 'payment',
                'display_order' => 20,
            ],
            [
                'key' => 'payment_failed',
                'name' => 'Başarısız Ödeme',
                'description' => 'Ödeme başarısız olduğunda gönderilir',
                'subject' => ['tr' => '❌ Ödeme başarısız', 'en' => '❌ Payment failed'],
                'content' => [
                    'tr' => $this->wrapInCorporateDesign('
                        <p style="margin: 0 0 20px; font-size: 16px; line-height: 1.6; color: #334155;">
                            Merhaba <strong style="color: #1e293b;">{{user_name}}</strong>,
                        </p>
                        <div style="background-color: #fef2f2; border-left: 4px solid #ef4444; padding: 16px 20px; border-radius: 0 8px 8px 0; margin-bottom: 24px;">
                            <p style="margin: 0; font-size: 14px; color: #991b1b;">
                                ❌ Ödemeniz işlenemedi
                            </p>
                        </div>
                        <p style="margin: 0 0 16px; font-size: 16px; line-height: 1.6; color: #334155;">
                            <strong>Sebep:</strong> {{reason}}
                        </p>
                        <p style="margin: 0 0 24px; font-size: 14px; color: #64748b;">
                            Lütfen ödeme bilgilerinizi kontrol edip tekrar deneyin.
                        </p>
                        <table role="presentation" cellspacing="0" cellpadding="0">
                            <tr>
                                <td style="border-radius: 8px; background-color: #ef4444;">
                                    <a href="#" style="display: inline-block; padding: 14px 28px; font-size: 14px; font-weight: 600; color: #ffffff; text-decoration: none;">
                                        Tekrar Dene →
                                    </a>
                                </td>
                            </tr>
                        </table>
                    ', 'Ödeme Başarısız ❌'),
                    'en' => $this->wrapInCorporateDesign('
                        <p style="margin: 0 0 20px; font-size: 16px; line-height: 1.6; color: #334155;">
                            Hello <strong style="color: #1e293b;">{{user_name}}</strong>,
                        </p>
                        <div style="background-color: #fef2f2; border-left: 4px solid #ef4444; padding: 16px 20px; border-radius: 0 8px 8px 0; margin-bottom: 24px;">
                            <p style="margin: 0; font-size: 14px; color: #991b1b;">
                                ❌ Your payment could not be processed
                            </p>
                        </div>
                    ', 'Payment Failed ❌'),
                ],
                'variables' => ['user_name', 'amount', 'reason'],
                'category' => 'payment',
                'display_order' => 21,
            ],
            [
                'key' => 'new_device_login',
                'name' => 'Yeni Cihaz Girişi',
                'description' => 'Yeni cihazdan giriş yapıldığında güvenlik uyarısı',
                'subject' => ['tr' => '🔐 Yeni cihazdan giriş yapıldı', 'en' => '🔐 New device login'],
                'content' => [
                    'tr' => $this->wrapInCorporateDesign('
                        <p style="margin: 0 0 20px; font-size: 16px; line-height: 1.6; color: #334155;">
                            Merhaba <strong style="color: #1e293b;">{{user_name}}</strong>,
                        </p>
                        <div style="background-color: #eff6ff; border-left: 4px solid #3b82f6; padding: 16px 20px; border-radius: 0 8px 8px 0; margin-bottom: 24px;">
                            <p style="margin: 0; font-size: 14px; color: #1e40af;">
                                🔐 Hesabınıza yeni bir cihazdan giriş yapıldı
                            </p>
                        </div>
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-bottom: 24px;">
                            <tr>
                                <td style="padding: 16px; background-color: #f8fafc; border-radius: 8px;">
                                    <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0;">
                                                <span style="font-size: 13px; color: #64748b;">Cihaz</span>
                                            </td>
                                            <td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0; text-align: right;">
                                                <strong style="font-size: 14px; color: #1e293b;">{{device_name}}</strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0;">
                                                <span style="font-size: 13px; color: #64748b;">IP Adresi</span>
                                            </td>
                                            <td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0; text-align: right;">
                                                <code style="font-size: 12px; color: #64748b;">{{ip_address}}</code>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0;">
                                                <span style="font-size: 13px; color: #64748b;">Konum</span>
                                            </td>
                                            <td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0; text-align: right;">
                                                <span style="font-size: 14px; color: #1e293b;">{{location}}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 8px 0;">
                                                <span style="font-size: 13px; color: #64748b;">Zaman</span>
                                            </td>
                                            <td style="padding: 8px 0; text-align: right;">
                                                <span style="font-size: 14px; color: #1e293b;">{{login_time}}</span>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                        <p style="margin: 0; font-size: 14px; color: #64748b;">
                            Bu siz değilseniz, lütfen hemen şifrenizi değiştirin.
                        </p>
                    ', 'Güvenlik Uyarısı 🔐'),
                    'en' => $this->wrapInCorporateDesign('
                        <p style="margin: 0 0 20px; font-size: 16px; line-height: 1.6; color: #334155;">
                            Hello <strong style="color: #1e293b;">{{user_name}}</strong>,
                        </p>
                        <div style="background-color: #eff6ff; border-left: 4px solid #3b82f6; padding: 16px 20px; border-radius: 0 8px 8px 0;">
                            <p style="margin: 0; font-size: 14px; color: #1e40af;">
                                🔐 A new device logged into your account
                            </p>
                        </div>
                    ', 'Security Alert 🔐'),
                ],
                'variables' => ['user_name', 'device_name', 'ip_address', 'login_time', 'location'],
                'category' => 'auth',
                'display_order' => 5,
            ],
            [
                'key' => 'two_factor_code',
                'name' => '2FA Doğrulama Kodu',
                'description' => 'İki faktörlü doğrulama için gönderilir',
                'subject' => ['tr' => '🔑 Doğrulama kodunuz', 'en' => '🔑 Your verification code'],
                'content' => [
                    'tr' => $this->wrapInCorporateDesign('
                        <p style="margin: 0 0 24px; font-size: 16px; line-height: 1.6; color: #334155;">
                            Hesabınıza giriş yapmak için aşağıdaki doğrulama kodunu kullanın:
                        </p>
                        <div style="text-align: center; margin-bottom: 24px;">
                            <div style="display: inline-block; padding: 20px 40px; background: linear-gradient(135deg, #1e293b 0%, #334155 100%); border-radius: 12px;">
                                <span style="font-size: 32px; font-weight: 700; letter-spacing: 8px; color: #ffffff;">{{code}}</span>
                            </div>
                        </div>
                        <div style="background-color: #fef3c7; border-left: 4px solid #f59e0b; padding: 16px 20px; border-radius: 0 8px 8px 0; margin-bottom: 24px;">
                            <p style="margin: 0; font-size: 14px; color: #92400e;">
                                ⏱️ Bu kod <strong>{{expires_in}} dakika</strong> içinde geçerliliğini yitirecektir
                            </p>
                        </div>
                        <p style="margin: 0; font-size: 14px; color: #64748b;">
                            Bu kodu kimseyle paylaşmayın. Ekibimiz sizden asla bu kodu istemez.
                        </p>
                    ', 'Doğrulama Kodu 🔑'),
                    'en' => $this->wrapInCorporateDesign('
                        <p style="margin: 0 0 24px; font-size: 16px; line-height: 1.6; color: #334155;">
                            Use the following code to log into your account:
                        </p>
                        <div style="text-align: center; margin-bottom: 24px;">
                            <div style="display: inline-block; padding: 20px 40px; background: linear-gradient(135deg, #1e293b 0%, #334155 100%); border-radius: 12px;">
                                <span style="font-size: 32px; font-weight: 700; letter-spacing: 8px; color: #ffffff;">{{code}}</span>
                            </div>
                        </div>
                    ', 'Verification Code 🔑'),
                ],
                'variables' => ['code', 'expires_in'],
                'category' => 'auth',
                'display_order' => 2,
            ],
            [
                'key' => 'corporate_invite',
                'name' => 'Kurumsal Davet',
                'description' => 'Kurumsal hesaba davet edildiğinde gönderilir',
                'subject' => ['tr' => '🏢 {{company_name}} sizi davet ediyor', 'en' => '🏢 {{company_name}} invites you'],
                'content' => [
                    'tr' => $this->wrapInCorporateDesign('
                        <p style="margin: 0 0 20px; font-size: 16px; line-height: 1.6; color: #334155;">
                            Merhaba,
                        </p>
                        <div style="background-color: #faf5ff; border-left: 4px solid #a855f7; padding: 16px 20px; border-radius: 0 8px 8px 0; margin-bottom: 24px;">
                            <p style="margin: 0; font-size: 14px; color: #6b21a8;">
                                🏢 <strong>{{company_name}}</strong> sizi kurumsal hesabına davet ediyor
                            </p>
                        </div>
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-bottom: 24px;">
                            <tr>
                                <td style="padding: 16px; background-color: #f8fafc; border-radius: 8px;">
                                    <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0;">
                                                <span style="font-size: 13px; color: #64748b;">Plan</span>
                                            </td>
                                            <td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0; text-align: right;">
                                                <strong style="font-size: 14px; color: #1e293b;">{{plan_name}}</strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 8px 0;">
                                                <span style="font-size: 13px; color: #64748b;">Davet Kodu</span>
                                            </td>
                                            <td style="padding: 8px 0; text-align: right;">
                                                <code style="font-size: 14px; font-weight: 600; color: #a855f7; background: #faf5ff; padding: 4px 8px; border-radius: 4px;">{{invite_code}}</code>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                        <table role="presentation" cellspacing="0" cellpadding="0">
                            <tr>
                                <td style="border-radius: 8px; background-color: #a855f7;">
                                    <a href="#" style="display: inline-block; padding: 14px 28px; font-size: 14px; font-weight: 600; color: #ffffff; text-decoration: none;">
                                        Daveti Kabul Et →
                                    </a>
                                </td>
                            </tr>
                        </table>
                    ', 'Kurumsal Davet 🏢'),
                    'en' => $this->wrapInCorporateDesign('
                        <p style="margin: 0 0 20px; font-size: 16px; line-height: 1.6; color: #334155;">
                            Hello,
                        </p>
                        <div style="background-color: #faf5ff; border-left: 4px solid #a855f7; padding: 16px 20px; border-radius: 0 8px 8px 0;">
                            <p style="margin: 0; font-size: 14px; color: #6b21a8;">
                                🏢 <strong>{{company_name}}</strong> invites you to their corporate account
                            </p>
                        </div>
                    ', 'Corporate Invitation 🏢'),
                ],
                'variables' => ['company_name', 'invite_code', 'plan_name'],
                'category' => 'corporate',
                'display_order' => 30,
            ],
            [
                'key' => 'subscription_renewal',
                'name' => 'Abonelik Yenileme',
                'description' => 'Abonelik yenileme hatırlatması',
                'subject' => ['tr' => '🔄 Aboneliğiniz yenileniyor', 'en' => '🔄 Subscription renewal'],
                'content' => [
                    'tr' => $this->wrapInCorporateDesign('
                        <p style="margin: 0 0 20px; font-size: 16px; line-height: 1.6; color: #334155;">
                            Merhaba <strong style="color: #1e293b;">{{user_name}}</strong>,
                        </p>
                        <div style="background-color: #eff6ff; border-left: 4px solid #3b82f6; padding: 16px 20px; border-radius: 0 8px 8px 0; margin-bottom: 24px;">
                            <p style="margin: 0; font-size: 14px; color: #1e40af;">
                                🔄 Aboneliğiniz <strong>{{renewal_date}}</strong> tarihinde otomatik olarak yenilenecektir
                            </p>
                        </div>
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-bottom: 24px;">
                            <tr>
                                <td style="padding: 16px; background-color: #f8fafc; border-radius: 8px;">
                                    <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0;">
                                                <span style="font-size: 13px; color: #64748b;">Plan</span>
                                            </td>
                                            <td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0; text-align: right;">
                                                <strong style="font-size: 14px; color: #1e293b;">{{plan_name}}</strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 8px 0;">
                                                <span style="font-size: 13px; color: #64748b;">Tutar</span>
                                            </td>
                                            <td style="padding: 8px 0; text-align: right;">
                                                <strong style="font-size: 14px; color: #1e293b;">{{amount}}</strong>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    ', 'Abonelik Yenileme 🔄'),
                    'en' => $this->wrapInCorporateDesign('
                        <p style="margin: 0 0 20px; font-size: 16px; line-height: 1.6; color: #334155;">
                            Hello <strong style="color: #1e293b;">{{user_name}}</strong>,
                        </p>
                        <div style="background-color: #eff6ff; border-left: 4px solid #3b82f6; padding: 16px 20px; border-radius: 0 8px 8px 0;">
                            <p style="margin: 0; font-size: 14px; color: #1e40af;">
                                🔄 Your subscription will renew on <strong>{{renewal_date}}</strong>
                            </p>
                        </div>
                    ', 'Subscription Renewal 🔄'),
                ],
                'variables' => ['user_name', 'renewal_date', 'plan_name', 'amount'],
                'category' => 'subscription',
                'display_order' => 11,
            ],
        ];

        foreach ($templates as $template) {
            DB::connection('mysql')->table('mail_templates')->updateOrInsert(
                ['key' => $template['key']],
                [
                    'name' => $template['name'],
                    'description' => $template['description'] ?? null,
                    'subject' => json_encode($template['subject']),
                    'content' => json_encode($template['content']),
                    'variables' => json_encode($template['variables']),
                    'category' => $template['category'],
                    'display_order' => $template['display_order'] ?? 0,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
