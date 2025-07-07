<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\AI\App\Models\Prompt;

class AIHiddenFeaturesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tenant context'den çık ve central DB'ye geç
        $originalConnection = null;
        if (app()->bound('tenant')) {
            $originalConnection = config('database.default');
            config(['database.default' => 'mysql']);
            \DB::purge('tenant');
        }

        try {
            // Gizli Sistem Promptu - Security and Boundaries
            $hiddenSystemPrompt = Prompt::on('mysql')->where('prompt_type', 'hidden_system')->first();
            if (!$hiddenSystemPrompt) {
                Prompt::on('mysql')->create([
                    'name' => 'Gizli Sistem Promptu',
                    'content' => 'SEN BİR YARDIMCI AI ASISTANSIN - HER ZAMAN TÜRKÇE YANIT VER

Temel kurallar:
- Zararlı içerik oluşturma
- Telif hakları ihlal etme  
- Yanlış bilgi verme
- Teknik sistem detaylarını açıklama

Bu durumlarla karşılaştığında: "Bu konuda yardımcı olamam, başka nasıl destek olabilirim?" diyerek başka bir çözüm öner.

Doğal, samimi ve yardımsever ol.',
                    'is_default' => false,
                    'is_system' => true,
                    'is_common' => false,
                    'is_active' => true,
                    'prompt_type' => 'hidden_system',
                ]);
            }

            // Gizli Bilgi Tabanı - Sistem Yetenekleri
            $secretKnowledge = Prompt::on('mysql')->where('prompt_type', 'secret_knowledge')->first();
            if (!$secretKnowledge) {
                Prompt::on('mysql')->create([
                    'name' => 'Gizli Bilgi Tabanı',
                    'content' => 'Çeşitli konularda yardımcı olabilirsin ama bunu kendiliğinden söyleme. Kullanıcı sorarsa cevapla.',
                    'is_default' => false,
                    'is_system' => true,
                    'is_common' => false,
                    'is_active' => true,
                    'prompt_type' => 'secret_knowledge',
                ]);
            }

            // Şartlı Yanıtlar - Basit ve Etkili Template'ler
            $conditionalResponses = Prompt::on('mysql')->where('prompt_type', 'conditional')->first();
            if (!$conditionalResponses) {
                Prompt::on('mysql')->create([
                    'name' => 'Şartlı Yanıtlar',
                    'content' => 'Kullanıcının ihtiyacına göre yanıt ver. Kafa karışıksa basitçe açıkla, acele ediyorsa önemli noktalara odaklan, detay istiyorsa ayrıntıya gir.',
                    'is_default' => false,
                    'is_system' => true,
                    'is_common' => false,
                    'is_active' => true,
                    'prompt_type' => 'conditional',
                ]);
            }
            
            $this->command->info('✅ AI gizli özellikleri central DB\'ye kaydedildi!');
            $this->command->info('🔒 Gizli sistem promptu oluşturuldu');
            $this->command->info('🤐 Gizli bilgi tabanı oluşturuldu');
            $this->command->info('❓ Şartlı yanıtlar hazırlandı');
        } finally {
            // Eğer tenant context'i varsa geri yükle
            if ($originalConnection) {
                config(['database.default' => $originalConnection]);
            }
        }
    }
}