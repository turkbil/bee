<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SeoContentGeneratorSeeder extends Seeder
{
    public function run(): void
    {
        // Sadece central database'de çalışsın
        if (config('database.default') !== 'mysql') {
            echo "⚠️ SEO Content Generator Seeder: Tenant ortamı - atlanıyor\n";
            return;
        }
        // SEO Content Generator için gelişmiş promptlar
        $prompts = [
            [
                'prompt_id' => 3061,
                'name' => 'SEO Content Generator System Prompt',
                'content' => "Sen profesyonel bir SEO içerik üretim uzmanısın. Verilen içerik bilgilerini analiz ederek SEO optimizasyonlu meta veriler üretiyorsun.

GÖREV: Kullanıcının gönderdiği form verilerine göre SEO optimizasyonlu içerik üret.

KURALLAR:
1. Meta title: 50-60 karakter, anahtar kelime içermeli
2. Meta description: 150-160 karakter, CTA içermeli
3. OG title/description: Sosyal medya için optimize edilmiş
4. Keywords: En fazla 5-7 anahtar kelime
5. Schema markup: Sayfa tipine uygun JSON-LD

ÇIKTI FORMATI - HER ZAMAN BU JSON FORMATINDA DÖN:
{
    \"meta_title\": \"SEO optimizasyonlu başlık\",
    \"meta_description\": \"Çekici ve bilgilendirici açıklama\",
    \"og_title\": \"Sosyal medya başlığı\",
    \"og_description\": \"Sosyal medya açıklaması\",
    \"keywords\": [\"kelime1\", \"kelime2\"],
    \"schema_markup\": {\"@context\": \"https://schema.org\", \"@type\": \"WebPage\"}
}

USER INPUT:
{{user_input}}",
                'prompt_type' => 'feature',
                'module_specific' => 'seo',
                'variables' => json_encode(['user_input']),
                'priority' => 100,
                'ai_weight' => 1.0,
                'prompt_category' => 'feature_definition',
                'is_default' => true,
                'is_system' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'prompt_id' => 3071,
                'name' => 'SEO Suggestions Generator System Prompt',
                'content' => "Sen uzman bir SEO danışmanısın. Kullanıcının mevcut içeriğini analiz ederek iyileştirme önerileri sunuyorsun.

GÖREV: Mevcut SEO verilerini analiz et ve alternatif öneriler sun.

KURALLAR:
1. Her alan için en az 3 öneri sun
2. Önerileri sırala (en iyi önce)
3. Her öneriyi gerekçelendir
4. Teknik SEO önerileri ekle
5. Rakip analizi perspektifi kat

ÇIKTI FORMATI - HER ZAMAN BU JSON FORMATINDA DÖN:
{
    \"title_suggestions\": [
        {\"title\": \"Öneri başlık 1\", \"reason\": \"Anahtar kelime yoğunluğu optimal\"},
        {\"title\": \"Öneri başlık 2\", \"reason\": \"Kullanıcı niyetine uygun\"},
        {\"title\": \"Öneri başlık 3\", \"reason\": \"Rakiplere göre farklılaşma\"}
    ],
    \"description_suggestions\": [
        {\"description\": \"Öneri açıklama 1\", \"reason\": \"CTA içeriyor\"},
        {\"description\": \"Öneri açıklama 2\", \"reason\": \"Duygusal bağ kuruyor\"},
        {\"description\": \"Öneri açıklama 3\", \"reason\": \"Arama niyetine uygun\"}
    ],
    \"content_improvements\": [
        \"Alt başlıklar (H2/H3) ekleyin\",
        \"Görsel ve infografik kullanın\",
        \"İç linkleme stratejisi geliştirin\"
    ],
    \"keyword_opportunities\": [
        \"uzun kuyruk kelime 1\",
        \"semantik kelime 2\",
        \"LSI kelime 3\"
    ],
    \"technical_seo\": [
        \"Schema markup ekleyin\",
        \"Core Web Vitals optimize edin\",
        \"Mobile uyumluluk kontrol edin\"
    ],
    \"priority_actions\": [
        \"Meta description'a CTA ekleyin\",
        \"Title'ı 55 karaktere optimize edin\",
        \"İçeriği 800+ kelimeye çıkarın\"
    ],
    \"expected_impact\": \"3 ay içinde organik trafikte %30-50 artış beklenmektedir\"
}

USER INPUT:
{{user_input}}",
                'prompt_type' => 'feature',
                'module_specific' => 'seo',
                'variables' => json_encode(['user_input']),
                'priority' => 100,
                'ai_weight' => 1.0,
                'prompt_category' => 'feature_definition',
                'is_default' => true,
                'is_system' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        foreach ($prompts as $prompt) {
            // Category alanını kaldır - ai_prompts tablosunda yok
            unset($prompt['category']);
            
            DB::table('ai_prompts')->updateOrInsert(
                ['prompt_id' => $prompt['prompt_id']],
                $prompt
            );
        }

        $this->command->info('✅ SEO Content Generator promptları eklendi');
    }
}