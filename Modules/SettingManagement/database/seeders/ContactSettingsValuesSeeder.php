<?php

namespace Modules\SettingManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\SettingManagement\App\Models\Setting;
use Modules\SettingManagement\App\Models\SettingValue;

class ContactSettingsValuesSeeder extends Seeder
{
    /**
     * Run the database seeds for TENANT database.
     *
     * Bu seeder SADECE tenant context içinde çalışır!
     * Kullanım: $tenant->run(function() { (new ContactSettingsValuesSeeder)->run(); });
     */
    public function run(): void
    {
        // Tenant context kontrolü
        if (!tenant()) {
            echo "❌ Bu seeder sadece tenant context içinde çalışır!\n";
            return;
        }

        echo "📝 İletişim Bilgileri değerleri oluşturuluyor (Tenant: " . tenant()->id . ")...\n";

        // Central database'den setting'leri al
        $settingsData = [
            // 📞 TELEFONLAR
            'contact_phone_1' => '0216 755 3 555',
            'contact_phone_2' => '',
            'contact_phone_3' => '',

            // 💬 WHATSAPP
            'contact_whatsapp_1' => '0501 005 67 58',
            'contact_whatsapp_2' => '',
            'contact_whatsapp_3' => '',

            // 📧 E-POSTALAR
            'contact_email_1' => 'info@ixtif.com',
            'contact_email_2' => '',
            'contact_email_3' => '',

            // 🌐 SOSYAL MEDYA
            'social_instagram' => 'https://instagram.com/ixtifcom',
            'social_facebook' => 'https://facebook.com/ixtif',
            'social_twitter' => '',
            'social_linkedin' => '',
            'social_tiktok' => '',
            'social_youtube' => '',
            'social_pinterest' => '',

            // 📍 ADRES BİLGİLERİ
            'contact_address_line_1' => '',
            'contact_address_line_2' => '',
            'contact_city' => 'İstanbul',
            'contact_state' => 'Tuzla',
            'contact_postal_code' => '',
            'contact_country' => 'Türkiye',

            // ⏰ ÇALIŞMA SAATLERİ
            'contact_working_hours' => '08:00 - 20:00 (Hafta içi ve Cumartesi)',
            'contact_working_days' => 'Pazartesi - Cumartesi',
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

            // Değer boşsa atla
            if (empty($value)) {
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

        echo "✅ İletişim Bilgileri değerleri oluşturuldu!\n";
        echo "   ├─ Oluşturulan: {$created}\n";
        echo "   └─ Atlanan (boş): {$skipped}\n";
    }
}
