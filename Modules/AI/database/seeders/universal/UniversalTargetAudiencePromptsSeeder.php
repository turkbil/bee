<?php

namespace Modules\AI\Database\Seeders\Universal;

use Illuminate\Database\Seeder;
use Modules\AI\App\Models\Prompt;

class UniversalTargetAudiencePromptsSeeder extends Seeder
{
    /**
     * Target audience prompt ID aralığı: 11041-11060
     */
    private const TARGET_AUDIENCE_ID_START = 11041;
    
    public function run()
    {
        $prompts = [
            [
                'prompt_id' => self::TARGET_AUDIENCE_ID_START,
                'name' => 'Genel Kitle',
                'content' => 'Genel kitlenizi hedef alın. Geniş demografik özellikler için uygun dil ve ton kullanın. Herkes tarafından anlaşılır bir üslup benimseyin.',
                'prompt_type' => 'standard',
                'prompt_category' => 'expert_knowledge',
                'priority' => 3,
                'ai_weight' => 65,
                'is_system' => true,
                'is_active' => true
            ],
            [
                'prompt_id' => self::TARGET_AUDIENCE_ID_START + 1,
                'name' => 'Profesyonel/İş Dünyası',
                'content' => 'Profesyonel iş dünyasını hedef alın. Formal dil, teknik terimler ve iş odaklı yaklaşım kullanın. Güvenilirlik ve uzmanlık vurgulayın.',
                'prompt_type' => 'standard',
                'prompt_category' => 'expert_knowledge',
                'priority' => 3,
                'ai_weight' => 65,
                'is_system' => true,
                'is_active' => true
            ],
            [
                'prompt_id' => self::TARGET_AUDIENCE_ID_START + 2,
                'name' => 'Genç Kitle (18-30)',
                'content' => 'Genç yetişkinleri hedef alın. Modern, dinamik ve trend odaklı dil kullanın. Sosyal medya dostu, güncel referanslarla zenginleştirin.',
                'prompt_type' => 'standard',
                'prompt_category' => 'expert_knowledge',
                'priority' => 3,
                'ai_weight' => 65,
                'is_system' => true,
                'is_active' => true
            ],
            [
                'prompt_id' => self::TARGET_AUDIENCE_ID_START + 3,
                'name' => 'Anne/Baba/Aile',
                'content' => 'Ebeveyn ve aile kitlesi için içerik oluşturun. Pratik, güvenlik odaklı ve çocuk dostu yaklaşım benimseyin. Aile değerlerini vurgulayın.',
                'prompt_type' => 'standard',
                'prompt_category' => 'expert_knowledge',
                'priority' => 3,
                'ai_weight' => 65,
                'is_system' => true,
                'is_active' => true
            ],
            [
                'prompt_id' => self::TARGET_AUDIENCE_ID_START + 4,
                'name' => 'Teknologi Meraklıları',
                'content' => 'Teknoloji severler için yazın. Teknik detaylar, yenilikçi yaklaşım ve öncü teknolojiler hakkında bilgi verin. İnovasyon vurgulayın.',
                'prompt_type' => 'standard',
                'prompt_category' => 'expert_knowledge',
                'priority' => 3,
                'ai_weight' => 65,
                'is_system' => true,
                'is_active' => true
            ],
            [
                'prompt_id' => self::TARGET_AUDIENCE_ID_START + 5,
                'name' => 'Sağlık ve Yaşam Tarzı',
                'content' => 'Sağlıklı yaşam odaklı kitle için yazın. Wellness, beslenme ve aktif yaşam konularına odaklanın. Pozitif motivasyon sağlayın.',
                'prompt_type' => 'standard',
                'prompt_category' => 'expert_knowledge',
                'priority' => 3,
                'ai_weight' => 65,
                'is_system' => true,
                'is_active' => true
            ],
            [
                'prompt_id' => self::TARGET_AUDIENCE_ID_START + 6,
                'name' => 'Eğitim ve Akademisyenler',
                'content' => 'Akademik ve eğitim sektörü için yazın. Bilimsel yaklaşım, referanslar ve eğitim odaklı içerik üretin. Öğrenmeyi destekleyin.',
                'prompt_type' => 'standard',
                'prompt_category' => 'expert_knowledge',
                'priority' => 3,
                'ai_weight' => 65,
                'is_system' => true,
                'is_active' => true
            ],
            [
                'prompt_id' => self::TARGET_AUDIENCE_ID_START + 7,
                'name' => 'Yaratıcı ve Sanatsal',
                'content' => 'Sanat ve yaratıcı endüstriler için yazın. İlham verici, estetik odaklı ve yaratıcı düşünceyi destekleyen üslup kullanın.',
                'prompt_type' => 'standard',
                'prompt_category' => 'expert_knowledge',
                'priority' => 3,
                'ai_weight' => 65,
                'is_system' => true,
                'is_active' => true
            ]
        ];

        foreach ($prompts as $promptData) {
            Prompt::updateOrCreate(
                ['prompt_id' => $promptData['prompt_id']],
                [
                    'name' => $promptData['name'],
                    'content' => $promptData['content'],
                    'prompt_type' => $promptData['prompt_type'],
                    'prompt_category' => $promptData['prompt_category'],
                    'priority' => $promptData['priority'],
                    'ai_weight' => $promptData['ai_weight'],
                    'is_system' => $promptData['is_system'],
                    'is_active' => $promptData['is_active'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $this->command->info('✅ Universal Target Audience Prompts oluşturuldu: ' . count($prompts) . ' prompt');
    }
}