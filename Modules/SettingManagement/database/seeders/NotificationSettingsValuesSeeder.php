<?php

namespace Modules\SettingManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\SettingManagement\App\Models\Setting;
use Modules\SettingManagement\App\Models\SettingValue;

class NotificationSettingsValuesSeeder extends Seeder
{
    /**
     * Run the database seeds for TENANT database.
     *
     * Bu seeder SADECE tenant context içinde çalışır!
     * Kullanım: $tenant->run(function() { (new NotificationSettingsValuesSeeder)->run(); });
     */
    public function run(): void
    {
        // Tenant context kontrolü
        if (!tenant()) {
            echo "❌ Bu seeder sadece tenant context içinde çalışır!\n";
            return;
        }

        echo "📝 Bildirim Ayarları değerleri oluşturuluyor (Tenant: " . tenant()->id . ")...\n";

        // ⚠️ UYARI: Bu seeder sadece örnek amaçlıdır!
        // Gerçek production değerleri admin panelden girilmelidir:
        // https://yourdomain.com/admin/settingmanagement/values/11

        // Central database'den setting'leri al
        $settingsData = [
            // 📱 TELEGRAM BİLDİRİMLERİ
            'telegram_enabled' => '0',  // Admin panelden aktifleştirin
            'telegram_bot_token' => '',  // @BotFather'dan alın ve admin panelde girin
            'telegram_chat_id' => '',    // @userinfobot'tan alın ve admin panelde girin

            // 💬 WHATSAPP BİLDİRİMLERİ (TWILIO)
            'whatsapp_enabled' => '0',   // Admin panelden aktifleştirin
            'twilio_account_sid' => '',  // Twilio Console'dan alın ve admin panelde girin
            'twilio_auth_token' => '',   // Twilio Console'dan alın ve admin panelde girin
            'twilio_whatsapp_from' => '',  // Örnek: whatsapp:+14155238886
            'twilio_whatsapp_to' => '',    // Örnek: whatsapp:+905321234567

            // 📧 EMAIL BİLDİRİMLERİ
            'email_enabled' => '0',      // Admin panelden aktifleştirin
            'notification_email' => '',  // Bildirim alacak email adresinizi admin panelde girin
        ];

        $created = 0;
        $skipped = 0;

        foreach ($settingsData as $key => $value) {
            // Central database'den setting'i bul
            $setting = Setting::on('mysql')->where('key', $key)->first();

            if (!$setting) {
                echo "⚠️  Setting bulunamadı: {$key}\n";
                $skipped++;
                continue;
            }

            // Değer boşsa atla (checkbox'lar için 0 da geçerli)
            if ($value === '' || $value === null) {
                $skipped++;
                continue;
            }

            // Tenant database'de value oluştur/güncelle
            SettingValue::updateOrCreate(
                ['setting_id' => $setting->id],
                ['value' => $value]
            );

            $created++;
        }

        echo "✅ Bildirim Ayarları değerleri oluşturuldu!\n";
        echo "   ├─ Oluşturulan: {$created}\n";
        echo "   └─ Atlanan (boş): {$skipped}\n";
    }
}
