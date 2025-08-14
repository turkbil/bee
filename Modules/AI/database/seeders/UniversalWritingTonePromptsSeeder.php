<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Helpers\TenantHelpers;

class UniversalWritingTonePromptsSeeder extends Seeder
{
    /**
     * Universal Writing Tone Prompts - Yazım tonu ayarları
     * UNIVERSAL-INPUT-SYSTEM-V3-PROFESSIONAL-ROADMAP.md uyumlu
     */
    public function run(): void
    {
        // Bu seeder sadece central veritabanında çalışmalı
        if (!TenantHelpers::isCentral()) {
            return;
        }
        $writingTonePrompts = [
            // 1. PROFESYONEL TON
            [
                'prompt_id' => 90021,
                'name' => 'Profesyonel',
                'content' => 'Profesyonel bir yaklaşım benimseyin:
- İş hayatına uygun terminoloji kullan
- Net ve açık ifadeler tercih et
- Güvenilir ve ciddi bir dil kullan
- Teknik konularda detay ver
- Resmi yazım kurallarına uy
- Objektif bakış açısı benimse',
                'prompt_type' => 'writing_tone',
                'prompt_category' => 'response_format',
                'is_active' => true,
                'is_common' => true,
                'is_system' => false,
                'priority' => 5,
                'ai_weight' => 95,
            ],

            // 2. SAMİMİ TON
            [
                'prompt_id' => 90022,
                'name' => 'Samimi',
                'content' => 'Samimi ve yakın bir dil kullan:
- Dostane ve sıcak ifadeler tercih et
- Okuyucuyla bağlantı kurmaya odaklan
- Günlük konuşma tarzında yaz
- "Sen" hitabını kullan
- Empati kurmaya özen göster
- İnsan hikayelerine yer ver',
                'prompt_type' => 'writing_tone',
                'prompt_category' => 'response_format',
                'is_active' => true,
                'is_common' => true,
                'is_system' => false,
                'priority' => 4,
                'ai_weight' => 90,
            ],

            // 3. EĞİTİCİ TON
            [
                'prompt_id' => 90023,
                'name' => 'Eğitici',
                'content' => 'Eğitici ve öğretici yaklaşım benimse:
- Adım adım açıklamalar yap
- Karmaşık kavramları basitleştir
- Örneklerle destekle
- Sorular sorarak interaktif ol
- Temel bilgilerden başla
- İlerlemeli bilgi sunumu yap',
                'prompt_type' => 'writing_tone',
                'prompt_category' => 'response_format',
                'is_active' => true,
                'is_common' => true,
                'is_system' => false,
                'priority' => 3,
                'ai_weight' => 85,
            ],

            // 4. EĞLENCELİ TON
            [
                'prompt_id' => 90024,
                'name' => 'Eğlenceli',
                'content' => 'Eğlenceli ve enerjik bir yaklaşım benimse:
- Mizahi unsurlar ekle (uygun olduğunda)
- Canlı ve dinamik dil kullan
- Yaratıcı metaforlar tercih et
- Pozitif enerji yansıt
- Okuyucunun ilgisini çekmeye odaklan
- Sürprizli ifadeler kullan',
                'prompt_type' => 'writing_tone',
                'prompt_category' => 'response_format',
                'is_active' => true,
                'is_common' => false,
                'is_system' => false,
                'priority' => 2,
                'ai_weight' => 80,
            ],

            // 5. UZMAN TON
            [
                'prompt_id' => 90025,
                'name' => 'Uzman',
                'content' => 'Alanda uzman bir yaklaşım benimse:
- Detaylı teknik bilgi ver
- Sektörel terminolojiyi kullan
- Kanıt ve verilerle destekle
- Analitik yaklaşım benimse
- Karşılaştırmalar yap
- Derinlemesine açıklama sun',
                'prompt_type' => 'writing_tone',
                'prompt_category' => 'response_format',
                'is_active' => true,
                'is_common' => false,
                'is_system' => false,
                'priority' => 1,
                'ai_weight' => 75,
            ]
        ];

        // Insert writing tone prompts
        foreach ($writingTonePrompts as $prompt) {
            DB::table('ai_prompts')->updateOrInsert(
                ['prompt_id' => $prompt['prompt_id']],
                array_merge($prompt, [
                    'created_at' => now(),
                    'updated_at' => now()
                ])
            );
        }

        $this->command->info('✅ Universal Writing Tone Prompts seeder başarıyla tamamlandı.');
        $this->command->info('📊 Toplam ' . count($writingTonePrompts) . ' yazım tonu prompt\'u eklendi.');
    }
}