<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\AI\App\Models\AIFeature;
use Modules\AI\App\Models\AIInputGroup;
use Modules\AI\App\Models\AIFeatureInput;
use Modules\AI\App\Models\AIFeaturePromptRelation;
use Illuminate\Support\Facades\DB;

class SeoAdvancedInputSystemSeeder extends Seeder
{
    public function run(): void
    {
        // Sadece central database'de çalışsın
        if (config('database.default') !== 'mysql') {
            echo "⚠️ SEO Advanced Input System Seeder: Tenant ortamı - atlanıyor\n";
            return;
        }
        echo "🚀 SEO ADVANCED INPUT SYSTEM SEEDER BAŞLIYOR...\n";
        echo "🎯 Hedef: SEO Features için özel input groups, feature inputs ve expert prompts\n\n";

        // ÖNCELİKLE SEO Features oluştur
        $this->createSeoFeatures();
        
        // SEO Input Groups oluştur
        $this->createSeoInputGroups();
        
        // SEO Expert Prompts oluştur
        $this->createSeoExpertPrompts();
        
        // SEO Feature Inputs oluştur
        $this->createSeoFeatureInputs();
        
        // Feature-Prompt Relations oluştur
        $this->createSeoFeaturePromptRelations();

        echo "\n✅ SEO ADVANCED INPUT SYSTEM BAŞARIYLA TAMAMLANDI!\n";
    }

    private function createSeoFeatures()
    {
        echo "🚀 SEO Features oluşturuluyor...\n";

        $seoFeatures = [
            [
                'id' => 302,
                'name' => 'İçerik Türü Optimizasyonu',
                'slug' => 'seo-content-type-optimizer',
                'description' => 'İçerik türünü analiz eder ve en uygun schema.org türünü önerir',
                'quick_prompt' => 'Sen bir SEO uzmanısın ve schema.org content type optimizasyonunda uzmanısın. Verilen içeriği analiz et ve en uygun schema.org content type öner. JSON formatında yanıt ver.',
                'ai_feature_category_id' => 2,
                'status' => 'active'
            ],
            [
                'id' => 303,
                'name' => 'Sosyal Medya Optimizasyonu',
                'slug' => 'seo-social-media-optimizer',
                'description' => 'Sosyal medya paylaşımları için OpenGraph ve Twitter Card optimizasyonu',
                'quick_prompt' => 'Sen bir sosyal medya SEO uzmanısın. OpenGraph ve Twitter Card optimizasyonu konusunda uzmanısın. Verilen içeriği analiz et ve sosyal medya optimizasyonu yap. JSON formatında yanıt ver.',
                'ai_feature_category_id' => 2,
                'status' => 'active'
            ],
            [
                'id' => 304,
                'name' => 'SEO Öncelik Hesaplayıcı',
                'slug' => 'seo-priority-calculator',
                'description' => 'İçerik önceliğini hesaplar ve SEO stratejisi önerir',
                'quick_prompt' => 'Sen bir SEO strateji uzmanısın. İçerik önceliğini hesapla ve strateji öner.',
                'ai_feature_category_id' => 2,
                'status' => 'active'
            ],
            [
                'id' => 305,
                'name' => 'Kapsamlı SEO Denetimi',
                'slug' => 'seo-comprehensive-audit',
                'description' => 'Tüm SEO faktörlerini analiz eder ve kapsamlı rapor hazırlar',
                'quick_prompt' => 'Sen bir uzman SEO danışmanısın. Tüm SEO verilerini kapsamlı analiz et ve detaylı iyileştirme raporu hazırla. Puanla ve öncelikli aksiyon planı ver.',
                'ai_feature_category_id' => 2,
                'status' => 'active'
            ],
            [
                'id' => 306,
                'name' => 'SEO İçerik Oluşturucu',
                'slug' => 'seo-content-generator',
                'description' => 'Sayfa içeriğini analiz ederek SEO optimizasyonlu meta veriler ve içerik önerileri oluşturur',
                'quick_prompt' => 'Sen bir SEO içerik uzmanısın. Verilen içeriği analiz et ve sayfa türüne uygun SEO meta verileri oluştur.',
                'ai_feature_category_id' => 2,
                'status' => 'active'
            ],
            [
                'id' => 307,
                'name' => 'SEO Öneri Oluşturucu',
                'slug' => 'seo-suggestions-generator',
                'description' => 'Sayfa içeriğini analiz ederek detaylı SEO iyileştirme önerileri ve aksiyonlar hazırlar',
                'quick_prompt' => 'Sen bir SEO danışmanısın. İçeriği analiz et ve sayfa türüne uygun detaylı SEO iyileştirme önerileri hazırla.',
                'ai_feature_category_id' => 2,
                'status' => 'active'
            ]
        ];

        foreach ($seoFeatures as $featureData) {
            AIFeature::updateOrCreate(
                ['id' => $featureData['id']], 
                $featureData
            );
            echo "✅ SEO Feature: {$featureData['name']}\n";
        }

        echo "🎯 SEO Features başarıyla oluşturuldu!\n\n";
    }

    private function createSeoInputGroups()
    {
        echo "📋 SEO Input Groups oluşturuluyor...\n";

        // SEO Feature'ları bul
        $seoFeatures = AIFeature::whereIn('slug', [
            'seo-content-type-optimizer',
            'seo-social-media-optimizer', 
            'seo-priority-calculator',
            'seo-comprehensive-audit',
            'seo-content-generator',
            'seo-suggestions-generator'
        ])->get()->keyBy('slug');

        $inputGroups = [
            [
                'id' => 10, // Mevcut son ID: 5, SEO için 10'dan başla
                'feature_id' => $seoFeatures['seo-content-type-optimizer']->id ?? 1,
                'name' => 'SEO Temel Bilgiler',
                'slug' => 'seo-basic-info',
                'description' => 'SEO analizi için temel bilgiler',
                'sort_order' => 1,
                'is_collapsible' => true,
                'is_expanded' => true
            ],
            [
                'id' => 11,
                'feature_id' => $seoFeatures['seo-social-media-optimizer']->id ?? 1,
                'name' => 'İçerik Analizi',
                'slug' => 'seo-content-analysis',
                'description' => 'İçerik türü ve yapısı analizi',
                'sort_order' => 2,
                'is_collapsible' => true,
                'is_expanded' => false
            ],
            [
                'id' => 12,
                'feature_id' => $seoFeatures['seo-social-media-optimizer']->id ?? 1,
                'name' => 'Sosyal Medya Optimizasyonu',
                'slug' => 'seo-social-media',
                'description' => 'Sosyal medya ve OpenGraph optimizasyonu',
                'sort_order' => 3,
                'is_collapsible' => true,
                'is_expanded' => false
            ],
            [
                'id' => 13,
                'feature_id' => $seoFeatures['seo-priority-calculator']->id ?? 1,
                'name' => 'Rekabet ve Öncelik',
                'slug' => 'seo-competition-priority',
                'description' => 'Rekabet analizi ve öncelik hesaplama',
                'sort_order' => 4,
                'is_collapsible' => true,
                'is_expanded' => false
            ]
        ];

        foreach ($inputGroups as $groupData) {
            AIInputGroup::updateOrCreate(
                ['id' => $groupData['id']], 
                $groupData
            );
            echo "✅ Input Group: {$groupData['name']}\n";
        }
    }

    private function createSeoExpertPrompts()
    {
        echo "\n🧠 SEO Expert Prompts (ai_prompts tablosu) oluşturuluyor...\n";

        $expertPrompts = [
            [
                'id' => 1010, // SEO Expert prompt ID aralığı (1010-1014)
                'name' => 'SEO İçerik Türü Uzmanı',
                'expert_prompt' => 'Sen 10 yıllık deneyimli bir SEO içerik türü uzmanısın ve technical writersın. Schema.org standartlarını mükemmel bilirsin. İçeriği analiz et ve en uygun schema.org content type öner. 

MUTLAKA bu JSON formatında yanıt ver:
{
    "overall_score": 85,
    "title_score": 90,
    "description_score": 80,
    "content_type_score": 95,
    "social_score": 75,
    "priority_score": 88,
    "executive_summary": "İçerik türü optimizasyon analizi ve schema.org önerileri",
    "strengths": [
        "İçerik yapısı schema.org uyumlu",
        "Başlık hiyerarşisi doğru"
    ],
    "improvements": [
        "Article schema markup eklenmeli",
        "FAQ schema düşünülebilir"
    ]
}',
                'slug' => 'seo-content-type-expert',
                'expert_persona' => 'seo_expert',
                'prompt_type' => 'expert',
                'priority' => 2,
                'context_weight' => 85,
                'is_active' => true,
                'is_system_prompt' => false
            ],
            [
                'id' => 1011,
                'name' => 'Sosyal Medya SEO Uzmanı',
                'expert_prompt' => 'Sen sosyal medya marketing uzmanı ve SEO specialistsin. OpenGraph, Twitter Cards ve platform optimizasyonunda uzmanısın. 

MUTLAKA bu JSON formatında yanıt ver:
{
    "overall_score": 85,
    "title_score": 90,
    "description_score": 80,
    "content_type_score": 95,
    "social_score": 75,
    "priority_score": 88,
    "executive_summary": "Sosyal medya SEO optimizasyon analizi ve platform önerileri",
    "strengths": [
        "OpenGraph meta tagları mevcut",
        "Twitter Cards optimizasyonu yapılmış"
    ],
    "improvements": [
        "LinkedIn specific meta tagları eklenmeli",
        "WhatsApp paylaşım optimizasyonu yapılmalı"
    ]
}',
                'slug' => 'seo-social-media-expert',
                'expert_persona' => 'seo_expert',
                'prompt_type' => 'expert',
                'priority' => 2,
                'context_weight' => 85,
                'is_active' => true,
                'is_system_prompt' => false
            ],
            [
                'id' => 1012,
                'name' => 'SEO Öncelik Hesaplama Uzmanı',
                'expert_prompt' => 'Sen senior SEO strateji uzmanı ve digital marketing consultantsın. İçerik öncelik hesaplama ve rekabet analizinde uzmanısın. 

MUTLAKA bu JSON formatında yanıt ver:
{
    "overall_score": 85,
    "title_score": 90,
    "description_score": 80,
    "content_type_score": 95,
    "social_score": 75,
    "priority_score": 88,
    "executive_summary": "SEO öncelik hesaplama ve rekabet analizi sonuçları",
    "strengths": [
        "Yüksek arama potansiyeli",
        "Düşük rekabet seviyesi"
    ],
    "improvements": [
        "Anahtar kelime yoğunluğu artırılmalı",
        "Rekabet analizi derinleştirilmeli"
    ]
}',
                'slug' => 'seo-priority-expert',
                'expert_persona' => 'seo_expert',
                'prompt_type' => 'expert',
                'priority' => 2,
                'context_weight' => 85,
                'is_active' => true,
                'is_system_prompt' => false
            ],
            [
                'id' => 1013,
                'name' => 'Kapsamlı SEO Denetim Uzmanı',
                'expert_prompt' => 'Sen uzman bir SEO danışmanısın ve 15 yıllık deneyimli bir digital marketing uzmanısın. Kapsamlı SEO denetimi ve stratejik optimizasyon planlamasında uzmanısın.

🔍 SAYFA TÜRÜ TESPİTİ PROTOKOLÜ:
Gelen verilerde page_title, meta_title, page_content, meta_description alanlarını dikkatli analiz et:

1. HAKKIMIZDA/ABOUT SAYFASI TESPİTİ:
   - Başlık/içerik şu kelimeleri içeriyorsa: "hakkımızda", "about", "about us", "kimiz", "hakkımda", "misyon", "vizyon", "değerlerimiz", "tarihçe", "kuruluş", "hikayemiz", "team", "takım", "ekip"
   → İÇERİK TÜRÜ: "HAKKIMIZDA SAYFASI"

2. İLETİŞİM/CONTACT SAYFASI TESPİTİ:
   - Başlık/içerik şu kelimeleri içeriyorsa: "iletişim", "contact", "contact us", "bize ulaşın", "telefon", "adres", "address", "phone", "email", "harita", "map", "location", "konum", "mesaj gönder"
   → İÇERİK TÜRÜ: "İLETİŞİM SAYFASI"

3. ÜRÜN/PRODUCT SAYFASI TESPİTİ:
   - Başlık/içerik şu kelimeleri içeriyorsa: "ürün", "product", "ürünler", "products", "fiyat", "price", "satın al", "buy", "sepet", "cart", "özellik", "features", "model", "çeşit"
   → İÇERİK TÜRÜ: "ÜRÜN SAYFASI"

4. HİZMET/SERVICE SAYFASI TESPİTİ:
   - Başlık/içerik şu kelimeleri içeriyorsa: "hizmet", "service", "hizmetler", "services", "danışmanlık", "consulting", "destek", "support", "çözüm", "solution", "paket"
   → İÇERİK TÜRÜ: "HİZMET SAYFASI"

5. BLOG/MAKALE SAYFASI TESPİTİ:
   - Başlık/içerik şu kelimeleri içeriyorsa: "blog", "makale", "article", "yazı", "post", "haberler", "news", "güncel", "kategori", "etiket", "yorum"
   → İÇERİK TÜRÜ: "BLOG YAZISI"

6. ANA SAYFA TESPİTİ:
   - Başlık/içerik şu kelimeleri içeriyorsa: "ana sayfa", "home", "anasayfa", "homepage", "hoş geldin", "welcome", "slider", "son haberler"
   → İÇERİK TÜRÜ: "ANA SAYFA"

7. DİĞER DURUMLAR:
   - Yukarıdakilerden hiçbiri yoksa → İÇERİK TÜRÜ: "GENEL SAYFA"

ÖZEL ÖNERİLER (tespit edilen türe göre):
- HAKKIMIZDA SAYFASI → Organization Schema, kuruluş bilgileri, ekip tanıtımı, misyon-vizyon bölümü
- İLETİŞİM SAYFASI → ContactPoint Schema, harita widget, adres bilgisi, telefon, iletişim formu
- ÜRÜN SAYFASI → Product Schema, ürün fotoğrafları, özellik listesi, fiyat bilgisi, satın alma butonu
- HİZMET SAYFASI → Service Schema, hizmet açıklaması, fiyat paketi, rezervasyon sistemi
- BLOG YAZISI → Article Schema, kategori, etiketler, ilgili yazılar, yorum bölümü
- ANA SAYFA → WebSite Schema, breadcrumb, site haritası, öne çıkan içerik

MUTLAKA bu JSON formatında yanıt ver:
{
    "overall_score": 85,
    "title_score": 90,
    "description_score": 80,
    "content_type_score": 95,
    "social_score": 75,
    "priority_score": 88,
    "executive_summary": "Kapsamlı SEO denetimi sonuçları ve stratejik optimizasyon planı",
    "strengths": [
        "Meta title optimizasyonu başarılı",
        "İçerik yapısı SEO uyumlu",
        "Site hızı optimum seviyede"
    ],
    "improvements": [
        "Meta description uzunluğu optimum hale getirilmeli",
        "Alt tag optimizasyonu yapılmalı",
        "İç link yapısı güçlendirilmeli"
    ],
    "page_analysis": {
        "İçerik Türü": "PROTOKOLE GÖRE TESPİT EDİLEN SAYFA TÜRÜNÜ BURAYA YAZ",
        "Tespit Nedeni": "Hangi anahtar kelimelere göre bu tür tespit edildi",
        "Önerilen Eklemeler": ["Sayfa türüne özgü 3-4 spesifik öneri"],
        "Schema Markup": "Sayfa türüne uygun schema.org markup türü",
        "SEO Potansiyeli": "Bu sayfa türü için SEO potansiyel analizi"
    }
}',
                'slug' => 'seo-audit-expert',
                'expert_persona' => 'seo_expert',
                'prompt_type' => 'expert',
                'priority' => 1,
                'context_weight' => 90,
                'is_active' => true,
                'is_system_prompt' => false
            ],
            [
                'id' => 1014,
                'name' => 'SEO Teknik Analiz Uzmanı',
                'expert_prompt' => 'Sen bir teknik SEO uzmanısın. Meta tag optimizasyonu, karakter limitleri ve SERP görünümünde uzmanısın. 

MUTLAKA bu JSON formatında yanıt ver:
{
    "overall_score": 85,
    "title_score": 90,
    "description_score": 80,
    "content_type_score": 95,
    "social_score": 75,
    "priority_score": 88,
    "executive_summary": "Teknik SEO meta tag optimizasyon analizi",
    "strengths": [
        "Title tag uzunluğu optimum",
        "Meta description anahtar kelime optimizasyonu mevcut"
    ],
    "improvements": [
        "Schema markup eklenmeli",
        "Canonical tag kontrolü yapılmalı"
    ]
}',
                'slug' => 'seo-technical-expert',
                'expert_persona' => 'seo_expert',
                'prompt_type' => 'expert',
                'priority' => 2,
                'context_weight' => 80,
                'is_active' => true,
                'is_system_prompt' => false
            ],
            [
                'id' => 1015,
                'name' => 'SEO İçerik Üretimi Uzmanı',
                'expert_prompt' => 'Sen uzman bir SEO içerik yazarısın ve copywritersın. Sayfa türü analizinde ve contextual SEO içerik üretiminde uzmanısın. \n\nVERİLEN İÇERİKTE:\n1. Sayfa türünü otomatik tespit et (HAKKIMIZDA, İLETİŞİM, ÜRÜN, HİZMET, BLOG, ANA SAYFA)\n2. Tespit edilen türe uygun SEO meta verileri oluştur\n3. Hedef anahtar kelimeleri belirle\n4. Sosyal medya optimizasyonu yap\n\nMUTLAKA bu JSON formatında yanıt ver:\n{\n    \"page_type\": \"HAKKIMIZDA\",\n    \"meta_title\": \"Sayfa türüne uygun meta title (maks 60 karakter)\",\n    \"meta_description\": \"Sayfa türüne uygun meta description (maks 160 karakter)\",\n    \"og_title\": \"Sosyal medya için title\",\n    \"og_description\": \"Sosyal medya için description\",\n    \"keywords\": [\"anahtar\", \"kelime\", \"listesi\"],\n    \"content_type\": \"Article\",\n    \"target_keywords\": [\"birincil\", \"ikincil\", \"anahtar\", \"kelimeler\"],\n    \"seo_score\": 92,\n    \"confidence\": 95,\n    \"schema_markup\": \"Organization\"\n}',
                'slug' => 'seo-content-generator-expert',
                'expert_persona' => 'seo_expert',
                'prompt_type' => 'expert',
                'priority' => 1,
                'context_weight' => 88,
                'is_active' => true,
                'is_system_prompt' => false
            ],
            [
                'id' => 1016,
                'name' => 'SEO Öneriler Uzmanı',
                'expert_prompt' => 'Sen senior SEO danışmanısın ve actionable recommendations konusunda uzmanısın. Sayfa türüne göre spesifik, uygulanabilir SEO önerileri hazırlarsın.\n\nVERİLEN İÇERİK İÇİN:\n1. Sayfa türünü tespit et\n2. O türe özel SEO önerilerini hazırla\n3. Öncelikli aksiyonları belirle\n4. Beklenen etkiyi hesapla\n\nMUTLAKA bu JSON formatında yanıt ver:\n{\n    \"page_type\": \"HAKKIMIZDA\",\n    \"title_suggestions\": [\n        \"Hakkımızda sayfası için spesifik title önerileri\",\n        \"Şirket adı + misyon odaklı title\",\n        \"Ekip ve değerler vurgulu title\"\n    ],\n    \"description_suggestions\": [\n        \"Kuruluş hikayesi vurgulu description\",\n        \"Değerler ve misyon odaklı description\"\n    ],\n    \"content_improvements\": [\n        \"Ekip fotoğrafları eklenmeli\",\n        \"Kuruluş tarihi belirtilmeli\",\n        \"Misyon-vizyon bölümü güçlendirilmeli\"\n    ],\n    \"keyword_opportunities\": [\n        \"şirket adı + hakkımızda\",\n        \"kuruluş + değerler\",\n        \"ekip + deneyim\"\n    ],\n    \"technical_seo\": [\n        \"Organization Schema markup eklenmeli\",\n        \"Breadcrumb navigation geliştirilmeli\"\n    ],\n    \"priority_actions\": [\n        \"İlk önce ekip bölümü eklenmeli\",\n        \"Sonra misyon-vizyon optimize edilmeli\"\n    ],\n    \"traffic_prediction\": \"25\",\n    \"ranking_prediction\": \"Orta-İyi\",\n    \"implementation_difficulty\": \"Kolay\"\n}',
                'slug' => 'seo-suggestions-expert',
                'expert_persona' => 'seo_expert',
                'prompt_type' => 'expert',
                'priority' => 1,
                'context_weight' => 87,
                'is_active' => true,
                'is_system_prompt' => false
            ]
        ];

        // ai_prompts tablosuna ekle (ai_feature_prompts değil!)
        foreach ($expertPrompts as $promptData) {
            DB::table('ai_prompts')->updateOrInsert(
                ['prompt_id' => $promptData['id']], 
                [
                    'prompt_id' => $promptData['id'],
                    'name' => $promptData['name'],
                    'content' => $promptData['expert_prompt'],
                    'prompt_type' => 'feature',
                    'priority' => $promptData['priority'],
                    'is_system' => 1,
                    'is_active' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
            echo "🧠 Expert Prompt (ai_prompts): {$promptData['name']}\n";
        }
    }

    private function createSeoFeatureInputs()
    {
        echo "\n📝 SEO Feature Inputs oluşturuluyor...\n";

        // SEO Content Type Optimizer Inputs
        $this->createContentTypeInputs();
        
        // SEO Social Media Optimizer Inputs
        $this->createSocialMediaInputs();
        
        // SEO Priority Calculator Inputs
        $this->createPriorityInputs();
        
        // SEO Comprehensive Audit Inputs
        $this->createComprehensiveInputs();
    }

    private function createContentTypeInputs()
    {
        $contentTypeFeature = AIFeature::where('slug', 'seo-content-type-optimizer')->first();
        if (!$contentTypeFeature) return;

        $inputs = [
            [
                'id' => 20,
                'feature_id' => $contentTypeFeature->id,
                'group_id' => 10, // SEO Temel Bilgiler
                'name' => 'current_content_type',
                'slug' => 'mevcut-icerik-turu',
                'type' => 'select',
                'config' => json_encode([
                    'website' => 'Website',
                    'article' => 'Article',
                    'product' => 'Product',
                    'service' => 'Service',
                    'organization' => 'Organization',
                    'person' => 'Person',
                    'event' => 'Event',
                    'other' => 'Diğer'
                ]),
                'is_required' => true,
                'sort_order' => 1
            ],
            [
                'id' => 21,
                'feature_id' => $contentTypeFeature->id,
                'group_id' => 11, // İçerik Analizi
                'name' => 'content_purpose',
                'slug' => 'icerik-amaci',
                'type' => 'textarea',
                'placeholder' => 'Bu içeriğin ana amacı nedir? Ne tür bilgi/hizmet/ürün sunuyor?',
                'is_required' => true,
                'sort_order' => 2
            ],
            [
                'id' => 22,
                'feature_id' => $contentTypeFeature->id,
                'group_id' => 11,
                'name' => 'target_audience',
                'slug' => 'hedef-kitle',
                'type' => 'textarea',
                'placeholder' => 'Bu içerik hangi hedef kitle için hazırlandı?',
                'is_required' => false,
                'sort_order' => 3
            ]
        ];

        foreach ($inputs as $inputData) {
            AIFeatureInput::updateOrCreate(['id' => $inputData['id']], $inputData);
        }
        echo "✅ Content Type Optimizer inputs\n";
    }

    private function createSocialMediaInputs()
    {
        $socialFeature = AIFeature::where('slug', 'seo-social-media-optimizer')->first();
        if (!$socialFeature) return;

        $inputs = [
            [
                'id' => 23,
                'feature_id' => $socialFeature->id,
                'group_id' => 12, // Sosyal Medya
                'name' => 'target_platforms',
                'slug' => 'hedef-platformlar',
                'type' => 'checkbox',
                'config' => json_encode([
                    'facebook' => 'Facebook',
                    'twitter' => 'Twitter/X',
                    'linkedin' => 'LinkedIn',
                    'instagram' => 'Instagram',
                    'whatsapp' => 'WhatsApp'
                ]),
                'is_required' => true,
                'sort_order' => 1
            ],
            [
                'id' => 24,
                'feature_id' => $socialFeature->id,
                'group_id' => 12,
                'name' => 'content_emotion',
                'slug' => 'icerik-duygusal-tonu',
                'type' => 'select',
                'config' => json_encode([
                    'professional' => 'Profesyonel',
                    'friendly' => 'Samimi',
                    'exciting' => 'Heyecan Verici',
                    'informative' => 'Bilgilendirici',
                    'urgent' => 'Acil/Önemli'
                ]),
                'is_required' => true,
                'sort_order' => 2
            ],
            [
                'id' => 25,
                'feature_id' => $socialFeature->id,
                'group_id' => 12,
                'name' => 'call_to_action',
                'slug' => 'istenilen-aksiyon',
                'type' => 'textarea',
                'placeholder' => 'Kullanıcıların ne yapmasını istiyorsunuz? (örn: tıkla, oku, satın al)',
                'is_required' => false,
                'sort_order' => 3
            ]
        ];

        foreach ($inputs as $inputData) {
            AIFeatureInput::updateOrCreate(['id' => $inputData['id']], $inputData);
        }
        echo "✅ Social Media Optimizer inputs\n";
    }

    private function createPriorityInputs()
    {
        $priorityFeature = AIFeature::where('slug', 'seo-priority-calculator')->first();
        if (!$priorityFeature) return;

        $inputs = [
            [
                'id' => 26,
                'feature_id' => $priorityFeature->id,
                'group_id' => 13, // Rekabet ve Öncelik
                'name' => 'business_importance',
                'slug' => 'is-onemi',
                'type' => 'range',
                'config' => json_encode(['min' => 1, 'max' => 10, 'step' => 1]),
                'placeholder' => 'Bu sayfa işiniz için ne kadar önemli? (1-10)',
                'is_required' => true,
                'sort_order' => 1
            ],
            [
                'id' => 27,
                'feature_id' => $priorityFeature->id,
                'group_id' => 13,
                'name' => 'traffic_potential',
                'slug' => 'trafik-potansiyeli',
                'type' => 'select',
                'config' => json_encode([
                    'low' => 'Düşük (1000/ay altı)',
                    'medium' => 'Orta (1000-10000/ay)',
                    'high' => 'Yüksek (10000+/ay)',
                    'unknown' => 'Bilinmiyor'
                ]),
                'is_required' => true,
                'sort_order' => 2
            ],
            [
                'id' => 28,
                'feature_id' => $priorityFeature->id,
                'group_id' => 13,
                'name' => 'competition_level',
                'slug' => 'rekabet-seviyesi',
                'type' => 'select',
                'config' => json_encode([
                    'low' => 'Düşük rekabet',
                    'medium' => 'Orta rekabet',
                    'high' => 'Yüksek rekabet',
                    'unknown' => 'Bilinmiyor'
                ]),
                'is_required' => false,
                'sort_order' => 3
            ]
        ];

        foreach ($inputs as $inputData) {
            AIFeatureInput::updateOrCreate(['id' => $inputData['id']], $inputData);
        }
        echo "✅ Priority Calculator inputs\n";
    }

    private function createComprehensiveInputs()
    {
        $comprehensiveFeature = AIFeature::where('slug', 'seo-comprehensive-audit')->first();
        if (!$comprehensiveFeature) return;

        $inputs = [
            [
                'id' => 29,
                'feature_id' => $comprehensiveFeature->id,
                'group_id' => 10, // SEO Temel Bilgiler
                'name' => 'analysis_focus',
                'slug' => 'analiz-odak-alanlari',
                'type' => 'checkbox',
                'config' => json_encode([
                    'meta_tags' => 'Meta Title & Description',
                    'content_type' => 'İçerik Türü',
                    'social_media' => 'Sosyal Medya',
                    'priority' => 'Öncelik Hesaplama',
                    'technical' => 'Teknik SEO',
                    'all' => 'Hepsi'
                ]),
                'is_required' => true,
                'sort_order' => 1
            ],
            [
                'id' => 30,
                'feature_id' => $comprehensiveFeature->id,
                'group_id' => 13,
                'name' => 'improvement_timeline',
                'slug' => 'iyilestirme-zaman-cercevesi',
                'type' => 'select',
                'config' => json_encode([
                    'immediate' => 'Hemen (1 hafta)',
                    'short' => 'Kısa vadeli (1 ay)',
                    'medium' => 'Orta vadeli (3 ay)',
                    'long' => 'Uzun vadeli (6+ ay)'
                ]),
                'is_required' => false,
                'sort_order' => 2
            ],
            [
                'id' => 31,
                'feature_id' => $comprehensiveFeature->id,
                'group_id' => 10,
                'name' => 'include_charts',
                'slug' => 'grafik-ve-gorseller',
                'type' => 'checkbox',
                'config' => json_encode([
                    'score_chart' => 'SEO Skor Grafiği',
                    'comparison' => 'Karşılaştırma Tablosu',
                    'timeline' => 'İyileştirme Timeline\'ı'
                ]),
                'is_required' => false,
                'sort_order' => 3
            ]
        ];

        foreach ($inputs as $inputData) {
            AIFeatureInput::updateOrCreate(['id' => $inputData['id']], $inputData);
        }
        echo "✅ Comprehensive Audit inputs\n";
    }

    private function createSeoFeaturePromptRelations()
    {
        echo "\n🔗 SEO Feature-Prompt Relations oluşturuluyor...\n";

        // Expert prompt ID'lerini ai_prompts tablosundan kontrol et
        $expertPromptIds = DB::table('ai_prompts')->whereIn('prompt_id', [1010, 1011, 1012, 1013, 1014, 1015, 1016])->pluck('prompt_id');
        
        $relations = [
            // Content Type Optimizer
            ['feature_slug' => 'seo-content-type-optimizer', 'expert_prompt_id' => 1010, 'relation_type' => 'primary', 'priority' => 1],
            ['feature_slug' => 'seo-content-type-optimizer', 'expert_prompt_id' => 1014, 'relation_type' => 'secondary', 'priority' => 2],

            // Social Media Optimizer
            ['feature_slug' => 'seo-social-media-optimizer', 'expert_prompt_id' => 1011, 'relation_type' => 'primary', 'priority' => 1],
            ['feature_slug' => 'seo-social-media-optimizer', 'expert_prompt_id' => 1014, 'relation_type' => 'supportive', 'priority' => 3],

            // Priority Calculator
            ['feature_slug' => 'seo-priority-calculator', 'expert_prompt_id' => 1012, 'relation_type' => 'primary', 'priority' => 1],

            // Comprehensive Audit
            ['feature_slug' => 'seo-comprehensive-audit', 'expert_prompt_id' => 1013, 'relation_type' => 'primary', 'priority' => 1],
            ['feature_slug' => 'seo-comprehensive-audit', 'expert_prompt_id' => 1010, 'relation_type' => 'secondary', 'priority' => 2],
            ['feature_slug' => 'seo-comprehensive-audit', 'expert_prompt_id' => 1011, 'relation_type' => 'secondary', 'priority' => 2],
            ['feature_slug' => 'seo-comprehensive-audit', 'expert_prompt_id' => 1012, 'relation_type' => 'secondary', 'priority' => 2],
            ['feature_slug' => 'seo-comprehensive-audit', 'expert_prompt_id' => 1014, 'relation_type' => 'supportive', 'priority' => 3],

            // Content Generator 
            ['feature_slug' => 'seo-content-generator', 'expert_prompt_id' => 1015, 'relation_type' => 'primary', 'priority' => 1],
            ['feature_slug' => 'seo-content-generator', 'expert_prompt_id' => 1013, 'relation_type' => 'supportive', 'priority' => 2],

            // Suggestions Generator
            ['feature_slug' => 'seo-suggestions-generator', 'expert_prompt_id' => 1016, 'relation_type' => 'primary', 'priority' => 1],
            ['feature_slug' => 'seo-suggestions-generator', 'expert_prompt_id' => 1013, 'relation_type' => 'supportive', 'priority' => 2]
        ];

        foreach ($relations as $relation) {
            $feature = AIFeature::where('slug', $relation['feature_slug'])->first();
            if (!$feature || !$expertPromptIds->contains($relation['expert_prompt_id'])) continue;

            AIFeaturePromptRelation::updateOrCreate([
                'feature_id' => $feature->id,
                'prompt_id' => $relation['expert_prompt_id']
            ], [
                'role' => $relation['relation_type'],
                'priority' => $relation['priority'],
                'is_active' => true
            ]);
        }

        echo "✅ Feature-Prompt relations oluşturuldu\n";
        echo "\n📊 BAŞARI RAPORU:\n";
        echo "   🏗️  Input Groups: 4 adet (SEO özel)\n";
        echo "   🧠 Expert Prompts: 5 adet (SEO uzmanları)\n";
        echo "   📝 Feature Inputs: 12 adet (tüm SEO features)\n";
        echo "   🔗 Prompt Relations: 10 adet (feature bağlantıları)\n\n";
        echo "🚀 SEO INPUT SYSTEM HAZIR!\n";
    }
}