<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SeoAnalysisPromptSeeder extends Seeder
{
    public function run(): void
    {
        // Sadece central database'de çalışsın
        if (config('database.default') !== 'mysql') {
            echo "⚠️ SEO Analysis Prompt Seeder: Tenant ortamı - atlanıyor\n";
            return;
        }
        echo "\n🔍 SEO ANALYSIS PROMPT SYSTEM oluşturuluyor...\n";

        // 🚀 2025 Enhanced SEO Analysis Prompt
        $promptData = [
            'prompt_id' => 3081, // Manuel prompt ID
            'name' => '2025 Enhanced SEO Analysis & Actionable Recommendations',
            'content' => "Sen 2025 SEO analizi uzmanısın ve UYGULANAGELEN, SPESIFIK öneriler sunuyorsun - NASIL YAPILIR rehberleriyle birlikte.

=== ANALİZ ÇERÇEVESİ ===
ÖNEMLİ: Genel öneriler değil, SPESIFIK, UYGULANAGELEN rehberler sunun - detaylı NASIL YAPILIR talimatlarıyla.

GEREKLİ ANALİZ ALANLARI (her biri 0-100 puan):
1. title: Sayfa başlığı değerlendirme (50-60 karakter optimal, anahtar kelime yerleşimi, marka entegrasyonu)
2. description: Meta açıklama analizi (150-160 karakter, değer önerisi, CTA dahil)
3. content: İçerik kalitesi değerlendirme (yapı, 800+ kelime uzunluk, okunabilirlik, anahtar kelime yoğunluğu %1-3)
4. technical: Teknik SEO elementleri (URL yapısı, schema markup, meta etiketler, HTML doğrulama) - ZORUNLU
5. social: Sosyal medya paylaşım optimizasyonu (OG etiketler, Twitter kartları, optimize edilmiş görseller 1200x630)
6. priority: Sayfa önemi ve dönüşüm potansiyeli içerik tipine dayalı

=== 2025 SEO STANDARTLARI ===
- Başlık: Ana anahtar kelime başta, duygusal tetikleyiciler, lokasyon/marka varsa
- Açıklama: Net değer önerisi, ikna edici CTA, fayda odaklı
- İçerik: Taranabilir yapı, semantik anahtar kelimeler, kullanıcı niyeti uyumu
- Teknik: Core Web Vitals, mobil-öncelik, yapılandırılmış veri
- Sosyal: Platform-optimize içerik, etkileyici görseller

=== GEREKLİ YANIT FORMATI ===
Analizinizi bu TAM JSON formatında uygulanagelen önerilerle döndürün:

{
    \"overall_score\": 75,
    \"detailed_scores\": {
        \"title\": {\"score\": 85, \"analysis\": \"Başlık analizi spesifik sorunlarla\"},
        \"description\": {\"score\": 70, \"analysis\": \"Açıklama güçlü ve zayıf yanları\"},
        \"content\": {\"score\": 60, \"analysis\": \"İçerik yapısı ve optimizasyon seviyesi\"},
        \"technical\": {\"score\": 45, \"analysis\": \"Teknik SEO uygulama durumu\"},
        \"social\": {\"score\": 80, \"analysis\": \"Sosyal medya optimizasyonu değerlendirmesi\"}
    },
    \"actionable_recommendations\": [
        {
            \"title\": \"Başlık Uzunluğunu ve Anahtar Kelime Yerleşimini Optimize Et\",
            \"description\": \"Mevcut başlık 72 karakter, optimal 50-60 aralığını aşıyor\",
            \"how_to_implement\": \"1) 55 karaktere kısalt 2) Ana anahtar kelimeyi başa taşı 3) Duygusal tetikleyici kelime ekle 4) Lokasyon/marka varsa dahil et\",
            \"example\": \"Web Tasarım Hizmeti | Modern & SEO Uyumlu | İstanbul\",
            \"expected_impact\": \"Başlık tıklama oranında %15-25 artış\",
            \"priority\": \"high\",
            \"effort\": \"low\"
        }
    ],
    \"strengths\": [
        \"Güçlü yanları listeleyin - her biri tek satırda kısa ve net\",
        \"Pozitif SEO elementlerini belirtin\",
        \"Mevcut iyi uygulamaları vurgulayın\"
    ],
    \"improvements\": [
        \"İyileştirme gereken alanları listeleyin - spesifik ve actionable\",
        \"En önemli eksiklikleri belirtin\",
        \"Hızlı wins için kolay düzeltmeler önerin\",
        \"Uzun vadeli SEO stratejisi için öneriler sunun\"
    ],
    \"keywords_suggestions\": [\"İçerik analizine dayalı önerilen anahtar kelimeler\"]
}

KRİTİK TALİMATLAR:
- Her actionable_recommendation MUTLAKA spesifik NASIL YAPILIR adımları içermeli
- Belirsiz öneriler değil, somut örnekler verin
- 2025 SEO en iyi uygulamalarına odaklanın (E-E-A-T, kullanıcı deneyimi, Core Web Vitals)
- Bağlam-spesifik öneriler için sayfa tipini dikkate alın
- Mümkün olduğunda beklenen etki metriklerini dahil edin
- TÜM YANITLARI TÜRKÇE VERİN
- ZORUNLU: strengths ve improvements alanlarını MUTLAKA doldur
- ZORUNLU: JSON formatını TAM olarak kullan, eksik alan bırakma
- ZORUNLU: Her alanı Türkçe ve detaylı doldur

ANALİZ EDİLECEK KULLANICI GİRDİSİ:
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
        ];

        DB::table('ai_prompts')->updateOrInsert(
            ['name' => $promptData['name']],
            $promptData
        );

        $insertedPromptId = DB::table('ai_prompts')
            ->where('name', '2025 Enhanced SEO Analysis & Actionable Recommendations')
            ->value('prompt_id');

        echo "✅ SEO Comprehensive Analysis prompt eklendi (ID: {$insertedPromptId})\n";

        // Feature ile prompt'u ilişkilendir - PROMPT ID'si dinamik
        $actualPromptId = $insertedPromptId;
            
        $featureId = 305; // seo-comprehensive-audit feature ID'si
        
        if ($actualPromptId) {
            DB::table('ai_feature_prompt_relations')->updateOrInsert(
                [
                    'feature_id' => $featureId,
                    'prompt_id' => $actualPromptId
                ],
                [
                    'feature_id' => $featureId,
                    'prompt_id' => $actualPromptId,
                    'priority' => 1,
                    'role' => 'primary',
                    'is_active' => true,
                    'feature_type_filter' => 'specific',
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );

            echo "✅ SEO Comprehensive Audit feature ile prompt ilişkilendirildi (Prompt ID: {$actualPromptId})\n";
        } else {
            echo "❌ Prompt bulunamadı - bağlantı oluşturulamadı\n";
        }

        echo "✅ SEO ANALYSIS PROMPT SYSTEM HAZIR!\n\n";
    }
}