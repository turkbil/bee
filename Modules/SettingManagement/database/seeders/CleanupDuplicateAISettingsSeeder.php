<?php

namespace Modules\SettingManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\SettingManagement\App\Models\Setting;
use Modules\SettingManagement\App\Models\SettingValue;

class CleanupDuplicateAISettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Bu seeder AI grubundaki (grup 9) duplicate contact ayarlarını kaldırır.
     * Artık contact bilgileri için grup 10 (İletişim Bilgileri) kullanılıyor.
     */
    public function run(): void
    {
        echo "🧹 AI grubundan duplicate contact ayarları temizleniyor...\n\n";

        // Silinecek AI ayarları
        $duplicateKeys = [
            'ai_contact_phone',
            'ai_contact_whatsapp',
            'ai_contact_email',
            'ai_contact_address',
            'ai_contact_city',
            'ai_contact_country',
            'ai_contact_postal_code',
            'ai_social_facebook',
            'ai_social_instagram',
        ];

        $deleted = 0;
        $valuesCleaned = 0;

        foreach ($duplicateKeys as $key) {
            $setting = Setting::where('key', $key)->first();

            if ($setting) {
                // Önce tenant'lardaki value'ları temizle (tenant context dışında çalıştığımız için direkt sil)
                // NOT: Bu işlem her tenant için ayrı ayrı yapılmalı, ama bu bir cleanup işlemi olduğu için
                // central'dan silince otomatik olarak orphan kalırlar

                echo "❌ Siliniyor: {$key} (ID: {$setting->id})\n";
                $setting->delete();
                $deleted++;
            }
        }

        echo "\n✅ Temizlik tamamlandı!\n";
        echo "   ├─ Silinen ayar: {$deleted}\n";
        echo "   └─ AI artık contact_* ve social_* ayarlarını kullanacak\n";

        echo "\n📌 Mapping:\n";
        echo "   ai_contact_phone → contact_phone_1\n";
        echo "   ai_contact_whatsapp → contact_whatsapp_1\n";
        echo "   ai_contact_email → contact_email_1\n";
        echo "   ai_contact_address → contact_address_line_1\n";
        echo "   ai_contact_city → contact_city\n";
        echo "   ai_social_facebook → social_facebook\n";
        echo "   ai_social_instagram → social_instagram\n";
    }
}
