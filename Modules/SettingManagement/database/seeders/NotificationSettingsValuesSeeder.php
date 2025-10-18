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

        // Central database'den setting'leri al
        $settingsData = [
            // 📱 TELEGRAM BİLDİRİMLERİ
            'telegram_enabled' => '1',
            'telegram_bot_token' => '8344881512:AAGJQn3Z167ebNx67pwvGuKf1RbzTHazbt0',
            'telegram_chat_id' => '-1002943373765',

            // 💬 WHATSAPP BİLDİRİMLERİ (TWILIO)
            'whatsapp_enabled' => '1',
            'twilio_account_sid' => 'AC1b50075754770609cb4a69be42112e3f',
            'twilio_auth_token' => 'b2b99ddd9ebd4d771bb96c08ece5d97c',
            'twilio_whatsapp_from' => 'whatsapp:+14155238886',
            'twilio_whatsapp_to' => 'whatsapp:+905322160754',

            // 📧 EMAIL BİLDİRİMLERİ
            'email_enabled' => '1',
            'notification_email' => 'info@ixtif.com',
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
