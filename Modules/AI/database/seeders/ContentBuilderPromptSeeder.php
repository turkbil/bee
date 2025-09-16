<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContentBuilderPromptSeeder extends Seeder
{
    // Content Builder için özel ID aralığı (5000-5100)
    private const PROMPT_ID_START = 5000;

    public function run(): void
    {
        // Sadece central database'de çalışsın
        if (config('database.default') !== 'mysql') {
            echo "⚠️ Content Builder Prompt Seeder: Tenant ortamı - atlanıyor\n";
            return;
        }

        echo "\n🎨 CONTENT BUILDER PROMPT SYSTEM oluşturuluyor...\n";

        $prompts = $this->getContentBuilderPrompts();

        foreach ($prompts as $prompt) {
            DB::table('ai_prompts')->updateOrInsert(
                ['prompt_id' => $prompt['prompt_id']],
                $prompt
            );

            echo "✅ {$prompt['name']} prompt eklendi (ID: {$prompt['prompt_id']})\n";
        }

        // Feature tanımını ekle/güncelle
        $this->createContentBuilderFeature();

        echo "✅ CONTENT BUILDER PROMPT SYSTEM HAZIR!\n\n";
    }

    private function getContentBuilderPrompts(): array
    {
        $baseTime = now();

        return [
            // 1. Hero Section Prompt
            [
                'prompt_id' => self::PROMPT_ID_START + 1,
                'name' => 'AI Content Builder - Hero Section',
                'content' => $this->getHeroSectionPrompt(),
                'prompt_type' => 'feature',
                'module_specific' => 'content_builder',
                'variables' => json_encode(['user_input', 'theme_context', 'page_title']),
                'priority' => 100,
                'ai_weight' => 1.0,
                'prompt_category' => 'feature_definition',
                'is_default' => true,
                'is_system' => true,
                'is_active' => true,
                'created_at' => $baseTime,
                'updated_at' => $baseTime
            ],

            // 2. Features Section Prompt
            [
                'prompt_id' => self::PROMPT_ID_START + 2,
                'name' => 'AI Content Builder - Features Section',
                'content' => $this->getFeaturesSectionPrompt(),
                'prompt_type' => 'feature',
                'module_specific' => 'content_builder',
                'variables' => json_encode(['user_input', 'theme_context', 'feature_count']),
                'priority' => 100,
                'ai_weight' => 1.0,
                'prompt_category' => 'feature_definition',
                'is_default' => true,
                'is_system' => true,
                'is_active' => true,
                'created_at' => $baseTime,
                'updated_at' => $baseTime
            ],

            // 3. Pricing Tables Prompt
            [
                'prompt_id' => self::PROMPT_ID_START + 3,
                'name' => 'AI Content Builder - Pricing Tables',
                'content' => $this->getPricingTablesPrompt(),
                'prompt_type' => 'feature',
                'module_specific' => 'content_builder',
                'variables' => json_encode(['user_input', 'theme_context', 'plan_count']),
                'priority' => 100,
                'ai_weight' => 1.0,
                'prompt_category' => 'feature_definition',
                'is_default' => true,
                'is_system' => true,
                'is_active' => true,
                'created_at' => $baseTime,
                'updated_at' => $baseTime
            ],

            // 4. About Section Prompt
            [
                'prompt_id' => self::PROMPT_ID_START + 4,
                'name' => 'AI Content Builder - About Section',
                'content' => $this->getAboutSectionPrompt(),
                'prompt_type' => 'feature',
                'module_specific' => 'content_builder',
                'variables' => json_encode(['user_input', 'theme_context', 'company_info']),
                'priority' => 100,
                'ai_weight' => 1.0,
                'prompt_category' => 'feature_definition',
                'is_default' => true,
                'is_system' => true,
                'is_active' => true,
                'created_at' => $baseTime,
                'updated_at' => $baseTime
            ],

            // 5. Contact Section Prompt
            [
                'prompt_id' => self::PROMPT_ID_START + 5,
                'name' => 'AI Content Builder - Contact Section',
                'content' => $this->getContactSectionPrompt(),
                'prompt_type' => 'feature',
                'module_specific' => 'content_builder',
                'variables' => json_encode(['user_input', 'theme_context', 'contact_info']),
                'priority' => 100,
                'ai_weight' => 1.0,
                'prompt_category' => 'feature_definition',
                'is_default' => true,
                'is_system' => true,
                'is_active' => true,
                'created_at' => $baseTime,
                'updated_at' => $baseTime
            ],

            // 6. Master Content Builder Prompt (Genel)
            [
                'prompt_id' => self::PROMPT_ID_START + 100,
                'name' => 'AI Content Builder - Master Template',
                'content' => $this->getMasterContentPrompt(),
                'prompt_type' => 'feature',
                'module_specific' => 'content_builder',
                'variables' => json_encode(['user_input', 'theme_context', 'content_type', 'length']),
                'priority' => 100,
                'ai_weight' => 1.0,
                'prompt_category' => 'feature_definition',
                'is_default' => true,
                'is_system' => true,
                'is_active' => true,
                'created_at' => $baseTime,
                'updated_at' => $baseTime
            ],
        ];
    }

    private function getHeroSectionPrompt(): string
    {
        return "Sen profesyonel bir web tasarımcı ve içerik üreticisisin. Hero section HTML kodu üreteceksin.

{{theme_context}}

KULLANICI TALEBİ: {{user_input}}
SAYFA BAŞLIĞI: {{page_title}}

Hero section için HTML üret. Aşağıdaki kurallara uy:

TASARIM KURALLARI:
- Tema renklerini kullan (primary, secondary, accent)
- Belirtilen font-family'yi kullan
- Framework'e uygun class'ları kullan (Tailwind veya Bootstrap)
- Responsive tasarım yap (mobile-first)
- Dark mode desteği ekle (varsa)

İÇERİK YAPISI:
- Etkileyici başlık (H1)
- Açıklayıcı alt başlık
- 1-2 CTA butonu
- Opsiyonel: Hero görsel alanı (placeholder)
- Opsiyonel: İstatistikler veya özellikler

HTML STANDARTLARI:
- Semantic HTML kullan
- Accessibility (a11y) standartlarına uy
- SEO uyumlu yapı
- ARIA label'ları ekle
- Alt text'ler ekle

FRAMEWORK KURALLARI:
{{#if tailwind}}
- Tailwind utility class'ları kullan (bg-, text-, p-, m-, etc.)
- Responsive prefix'ler ekle (sm:, md:, lg:, xl:)
- Dark mode class'ları ekle (dark:bg-, dark:text-)
- Container ve max-width kullan
{{/if}}

{{#if bootstrap}}
- Bootstrap grid sistemi kullan (container, row, col)
- Bootstrap component class'ları kullan
- Bootstrap utility class'ları kullan
{{/if}}

TÜRKÇE İÇERİK:
- Tüm metinler Türkçe olmalı
- Profesyonel ve etkileyici dil kullan
- Sektöre uygun terminoloji

Sadece HTML kodu döndür. Açıklama veya yorum ekleme.
Placeholder görseller için: https://via.placeholder.com/800x400 kullan.";
    }

    private function getFeaturesSectionPrompt(): string
    {
        return "Sen profesyonel bir web tasarımcı ve içerik üreticisisin. Features/Özellikler section HTML kodu üreteceksin.

{{theme_context}}

KULLANICI TALEBİ: {{user_input}}
ÖZELLİK SAYISI: {{feature_count}}

Özellikler bölümü için HTML üret:

İÇERİK YAPISI:
- Grid layout (3 veya 4 kolonlu)
- Her özellik kartı: İkon + Başlık + Açıklama
- Responsive tasarım (mobilde tek kolon)
- Hover efektleri (opsiyonel)
- İkonlar için: Font Awesome veya placeholder

TASARIM ÖZELLİKLERİ:
- Tema renkleri ve fontları kullan
- Kartlar arası eşit boşluk
- Okunabilir tipografi
- Visual hierarchy

{{#if tailwind}}
TAILWIND KURALLARI:
- Grid sistem: grid grid-cols-1 md:grid-cols-3 gap-6
- Kart stilleri: bg-white dark:bg-gray-800 rounded-lg p-6
- Hover efekti: hover:shadow-lg transition-shadow
- İkon wrapper: bg-primary/10 rounded-full p-3
{{/if}}

{{#if bootstrap}}
BOOTSTRAP KURALLARI:
- Grid sistem: row ve col-md-4 col-sm-6
- Kart component: card, card-body
- İkon stilleri: text-primary fs-3
- Spacing: mb-4, p-3
{{/if}}

ALPINE.JS (opsiyonel):
- Hover animasyonları için x-data kullan
- Click event'leri için @click ekle

Sadece HTML kodu döndür. Her özellik için gerçekçi Türkçe içerik üret.";
    }

    private function getPricingTablesPrompt(): string
    {
        return "Sen profesyonel bir web tasarımcı ve içerik üreticisisin. Pricing/Fiyatlandırma tabloları HTML kodu üreteceksin.

{{theme_context}}

KULLANICI TALEBİ: {{user_input}}
PLAN SAYISI: {{plan_count}}

Fiyatlandırma tabloları için HTML üret:

TABLO YAPISI:
- 3 plan (Başlangıç, Profesyonel, Kurumsal)
- Her plan: Başlık + Fiyat + Özellikler + CTA
- Önerilen plan vurgusu
- Responsive grid layout

TASARIM ÖGELERİ:
- Tema renkleri kullan
- Önerilen plan farklı renk/boyut
- Temiz ve okunabilir liste
- Fiyat vurgusu (büyük font)
- Periyot bilgisi (aylık/yıllık)

{{#if tailwind}}
TAILWIND STILLERI:
- Grid: grid-cols-1 md:grid-cols-3
- Kart: border rounded-lg p-6
- Önerilen: ring-2 ring-primary scale-105
- Liste: space-y-3 text-gray-600
- Buton: w-full py-3 rounded-lg
{{/if}}

{{#if bootstrap}}
BOOTSTRAP STILLERI:
- Grid: row justify-content-center
- Kart: card pricing-card
- Önerilen: border-primary shadow-lg
- Liste: list-unstyled
- Buton: btn btn-primary btn-block
{{/if}}

FIYAT FORMATI:
- Türk Lirası sembolü (₺)
- Binlik ayracı (.)
- KDV bilgisi

Sadece HTML kodu döndür. Gerçekçi Türkçe plan isimleri ve özellikler kullan.";
    }

    private function getAboutSectionPrompt(): string
    {
        return "Sen profesyonel bir web tasarımcı ve içerik üreticisisin. About/Hakkımızda section HTML kodu üreteceksin.

{{theme_context}}

KULLANICI TALEBİ: {{user_input}}
ŞİRKET BİLGİSİ: {{company_info}}

Hakkımızda bölümü için HTML üret:

İÇERİK BÖLÜMLERİ:
- Şirket hikayesi/misyon
- Değerlerimiz (3-4 değer)
- Neden biz? bölümü
- İstatistikler (müşteri, proje, yıl)
- Opsiyonel: Timeline veya milestone'lar

TASARIM YAPISI:
- İki kolonlu layout (metin + görsel)
- İstatistik kartları
- İkon destekli değerler
- Okunabilir paragraflar

{{#if tailwind}}
TAILWIND LAYOUT:
- Container: max-w-7xl mx-auto
- Grid: grid-cols-1 lg:grid-cols-2
- İstatistikler: grid-cols-2 md:grid-cols-4
- Spacing: space-y-6, gap-8
{{/if}}

{{#if bootstrap}}
BOOTSTRAP LAYOUT:
- Container: container
- Row/Col: row, col-lg-6
- İstatistikler: col-6 col-md-3
- Spacing: mb-4, mt-5
{{/if}}

İÇERİK KALİTESİ:
- Profesyonel Türkçe
- Güven veren ifadeler
- Somut başarı hikayeleri
- Duygusal bağ kurma

Sadece HTML kodu döndür. Placeholder görseller için https://via.placeholder.com/600x400 kullan.";
    }

    private function getContactSectionPrompt(): string
    {
        return "Sen profesyonel bir web tasarımcı ve içerik üreticisisin. Contact/İletişim section HTML kodu üreteceksin.

{{theme_context}}

KULLANICI TALEBİ: {{user_input}}
İLETİŞİM BİLGİLERİ: {{contact_info}}

İletişim bölümü için HTML üret:

İÇERİK YAPISI:
- İletişim formu (isim, email, telefon, mesaj)
- İletişim bilgileri (adres, telefon, email)
- Sosyal medya linkleri
- Opsiyonel: Harita alanı (placeholder)
- Çalışma saatleri

FORM ÖZELLİKLERİ:
- Input validation
- Required field işaretleri
- Placeholder text'ler
- Label'lar
- Submit butonu

{{#if tailwind}}
TAILWIND FORM:
- Input: border rounded-lg px-4 py-2 w-full
- Label: block mb-2 font-medium
- Focus: focus:ring-2 focus:ring-primary
- Button: bg-primary text-white px-6 py-3
{{/if}}

{{#if bootstrap}}
BOOTSTRAP FORM:
- Form groups: form-group
- Input: form-control
- Label: form-label
- Button: btn btn-primary btn-lg
{{/if}}

ALPINE.JS:
- Form validation için x-data
- Submit handling için @submit.prevent

İLETİŞİM BİLGİLERİ:
- İkonlu liste (konum, telefon, email)
- Sosyal medya ikonları
- Responsive layout

Sadece HTML kodu döndür. Form action'ı boş bırak. Türkçe label ve placeholder kullan.";
    }

    private function getMasterContentPrompt(): string
    {
        return "Sen deneyimli bir web içerik üreticisi ve tasarımcısın. Verilen tema analizine göre mükemmel uyumlu HTML içerik üreteceksin.

=== TEMA BİLGİLERİ ===
{{theme_context}}

=== KULLANICI TALEBİ ===
İstek: {{user_input}}
İçerik Tipi: {{content_type}}
Uzunluk: {{length}}

=== İÇERİK ÜRETİM KURALLARI ===

1. TEMA UYUMU:
- Belirtilen primary, secondary, accent renklerini kullan
- Font-family'yi theme_context'ten al ve uygula
- Dark mode varsa dark: prefix'li class'lar ekle
- Framework'e (Tailwind/Bootstrap) uygun class'lar kullan

2. FRAMEWORK KURALLARI:
{{#if framework === 'tailwind'}}
TAILWIND CSS:
- Utility-first class'lar kullan
- Responsive prefix'ler: sm:, md:, lg:, xl:
- Dark mode: dark:bg-gray-900, dark:text-white
- Spacing: p-4, m-6, space-y-4
- Grid: grid, grid-cols-3, gap-6
- Flexbox: flex, justify-between, items-center
{{/if}}

{{#if framework === 'bootstrap'}}
BOOTSTRAP:
- Grid sistem: container, row, col-md-*
- Components: card, btn, form-control
- Utilities: text-center, mb-4, p-3
- Responsive: d-none d-md-block
{{/if}}

3. İÇERİK KALİTESİ:
- SEO uyumlu semantic HTML (header, main, section, article)
- Accessibility: alt text, aria-label, role
- Türkçe içerik, profesyonel dil
- Placeholder images: https://via.placeholder.com/[boyut]

4. İNTERAKTİVİTE:
- Alpine.js direktifleri: x-data, x-show, @click
- Hover efektleri: hover:scale-105, hover:shadow-lg
- Transition'lar: transition-all duration-300

5. RESPONSIVE TASARIM:
- Mobile-first yaklaşım
- Breakpoint'lerde düzgün görünüm
- Touch-friendly element boyutları

6. PERFORMANS:
- Lazy loading için loading='lazy'
- Optimum görsel boyutları
- Minimal DOM yapısı

=== ÇIKTI FORMATI ===
- Sadece HTML kodu döndür
- Yorum satırı ekleme
- Açıklama yazma
- Kod dışında metin ekleme

=== İÇERİK TİPİNE GÖRE ÖZEL TALİMATLAR ===
{{#if content_type === 'hero'}}
Hero section: Büyük başlık, alt başlık, 2 CTA butonu, arka plan öğesi
{{/if}}

{{#if content_type === 'features'}}
Özellikler: 3-6 özellik kartı, ikon + başlık + açıklama
{{/if}}

{{#if content_type === 'pricing'}}
Fiyatlandırma: 3 plan, önerilen plan vurgusu, özellik listesi
{{/if}}

{{#if content_type === 'about'}}
Hakkımızda: Şirket hikayesi, değerler, istatistikler
{{/if}}

{{#if content_type === 'contact'}}
İletişim: Form, iletişim bilgileri, sosyal medya
{{/if}}

{{#if content_type === 'general'}}
Genel içerik: Kullanıcı talebine göre esnek yapı
{{/if}}

HTML kodunu üret:";
    }

    private function createContentBuilderFeature(): void
    {
        $featureData = [
            'id' => 501,
            'name' => 'AI Content Builder',
            'slug' => 'ai-content-builder',
            'description' => 'Tema uyumlu, AI destekli içerik üretim sistemi',
            'emoji' => '🎨',
            'icon' => 'fas fa-magic',
            'module_type' => 'global',
            'category' => 'content_generation',
            'supported_modules' => json_encode(['page', 'blog', 'portfolio']),
            'template_support' => true,
            'bulk_support' => false,
            'streaming_support' => false,
            'created_at' => now(),
            'updated_at' => now()
        ];

        DB::table('ai_features')->updateOrInsert(
            ['id' => $featureData['id']],
            $featureData
        );

        echo "✅ AI Content Builder feature tanımlandı (ID: 501)\n";

        // Feature-Prompt ilişkilerini oluştur
        $relations = [
            ['prompt_id' => self::PROMPT_ID_START + 1, 'role' => 'primary', 'priority' => 1],
            ['prompt_id' => self::PROMPT_ID_START + 2, 'role' => 'secondary', 'priority' => 2],
            ['prompt_id' => self::PROMPT_ID_START + 3, 'role' => 'secondary', 'priority' => 3],
            ['prompt_id' => self::PROMPT_ID_START + 4, 'role' => 'secondary', 'priority' => 4],
            ['prompt_id' => self::PROMPT_ID_START + 5, 'role' => 'secondary', 'priority' => 5],
            ['prompt_id' => self::PROMPT_ID_START + 100, 'role' => 'primary', 'priority' => 100],
        ];

        foreach ($relations as $relation) {
            DB::table('ai_feature_prompt_relations')->updateOrInsert(
                [
                    'feature_id' => 501,
                    'prompt_id' => $relation['prompt_id']
                ],
                [
                    'feature_id' => 501,
                    'prompt_id' => $relation['prompt_id'],
                    'priority' => $relation['priority'],
                    'role' => $relation['role'],
                    'is_active' => true,
                    'feature_type_filter' => 'specific',
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }

        echo "✅ Feature-Prompt ilişkileri oluşturuldu\n";
    }
}