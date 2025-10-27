<?php

namespace Modules\SettingManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\SettingManagement\App\Models\SettingGroup;

class CleanupAILayoutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Layout'tan duplicate contact alanlarını kaldırır.
     * AI artık Group 10'daki contact_* ve social_* ayarlarını kullanacak.
     */
    public function run(): void
    {
        echo "🧹 Group 9 layout'ından duplicate contact alanları temizleniyor...\n\n";

        $group9 = SettingGroup::find(9);

        if (!$group9) {
            echo "❌ Group 9 bulunamadı!\n";
            return;
        }

        $layout = is_string($group9->layout) ? json_decode($group9->layout, true) : $group9->layout;

        if (!$layout || !isset($layout['elements'])) {
            echo "❌ Layout bulunamadı!\n";
            return;
        }

        // İletişim ve sosyal medya row'larını kaldır
        $removedFields = [];
        $cleanedElements = [];

        foreach ($layout['elements'] as $element) {
            // Row tipinde ise kontrol et
            if ($element['type'] === 'row') {
                $hasContactField = false;

                // Her kolonu kontrol et
                foreach ($element['columns'] as $column) {
                    foreach ($column['elements'] as $field) {
                        if (isset($field['properties']['name'])) {
                            $name = $field['properties']['name'];
                            // İletişim veya sosyal medya alanı mı?
                            if (strpos($name, 'ai_contact_') === 0 ||
                                strpos($name, 'ai_social_') === 0 ||
                                $name === 'ai_working_hours' ||
                                $name === 'ai_support_hours') {
                                $hasContactField = true;
                                $removedFields[] = $name;
                            }
                        }
                    }
                }

                // İletişim alanı içermiyorsa ekle
                if (!$hasContactField) {
                    $cleanedElements[] = $element;
                }
            } else {
                // Heading vb. diğer elementler
                $cleanedElements[] = $element;
            }
        }

        $layout['elements'] = $cleanedElements;

        $group9->layout = $layout;
        $group9->save();

        echo "✅ Layout temizlendi!\n";
        echo "   ├─ Önceki element sayısı: " . count($layout['elements'] ?? []) . "\n";
        echo "   ├─ Yeni element sayısı: " . count($cleanedElements) . "\n";
        echo "   └─ Kaldırılan alanlar:\n";

        foreach (array_unique($removedFields) as $field) {
            echo "      - {$field}\n";
        }

        echo "\n📌 AI artık Group 10'daki iletişim bilgilerini kullanacak:\n";
        echo "   - contact_phone_1, contact_whatsapp_1, contact_email_1\n";
        echo "   - social_facebook, social_instagram\n";
    }
}
