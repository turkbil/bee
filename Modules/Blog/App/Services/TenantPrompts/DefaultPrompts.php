<?php

namespace Modules\Blog\App\Services\TenantPrompts;

/**
 * Default Blog AI Prompts
 * Tenant-specific prompt yoksa bu kullanılır (fallback)
 */
class DefaultPrompts
{
    /**
     * Draft (taslak) üretimi için AI prompt
     */
    public function getDraftPrompt(): string
    {
        return <<<'PROMPT'
Sen profesyonel bir blog içerik stratejistisin.

Görevin: Verilen konu ve kategori bilgilerine göre blog taslakları oluşturmak.

Her taslak şunları içermelidir:
1. **topic_keyword**: Ana anahtar kelime (kısa, net)
2. **category_suggestions**: Uygun kategori ID'leri (array)
3. **seo_keywords**: SEO için anahtar kelimeler (5-10 adet)
4. **outline**: Blog yapısı (H2, H3 başlıkları)
   - "h2": ["Başlık 1", "Başlık 2", ...]
   - "h3": ["Alt Başlık 1", "Alt Başlık 2", ...]
5. **meta_description**: SEO meta açıklaması (150-160 karakter)

KURALLAR:
- Başlıklar özgün ve bilgilendirici olmalı
- SEO anahtar kelimeler doğal ve ilgili olmalı
- Meta description aksiyon odaklı olmalı
- Outline mantıklı bir akış izlemeli (giriş → detay → sonuç)

Çıktı formatı JSON array:
[
  {
    "topic_keyword": "...",
    "category_suggestions": [1, 2],
    "seo_keywords": ["...", "..."],
    "outline": {
      "h2": ["...", "..."],
      "h3": ["...", "..."]
    },
    "meta_description": "..."
  }
]
PROMPT;
    }

    /**
     * Blog içeriği yazımı için AI prompt
     */
    public function getBlogContentPrompt(): string
    {
        return <<<'PROMPT'
Sen profesyonel bir blog yazarısın.

Görevin: Verilen taslak bilgilerine göre tam bir blog yazısı yazmak.

KURALLAR:
- **Kelime sayısı**: 1500-2000 kelime
- **Başlıklar**: H2, H3 yapısını kullan (verilen outline'a uy)
- **SEO**: Anahtar kelimeleri doğal şekilde yerleştir
- **Ton**: Profesyonel ama sıcak
- **Yapı**: Giriş → Detay → Sonuç/Aksiyon
- **HTML format**: <h2>, <h3>, <p>, <strong>, <ul>, <li> kullan
- **Paragraflar**: Kısa ve okunabilir (max 3-4 cümle)
- **E-E-A-T**: Uzmanlık, deneyim, otorite, güvenilirlik göster

Çıktı formatı JSON:
{
  "title": "Blog başlığı",
  "content": "Tam HTML içerik",
  "excerpt": "Kısa özet (180-200 karakter)"
}
PROMPT;
    }

    /**
     * Tenant için genel context (ayarlar, kategoriler vb)
     */
    public function getContext(): array
    {
        // Site bilgileri (Group 6)
        $siteTitle = setting('site_title') ?? setting('site_name') ?? config('app.name');
        $siteSlogan = setting('site_slogan') ?? '';
        $companyName = setting('company_name') ?? $siteTitle;

        // İletişim bilgileri (Group 10)
        $companyEmail = setting('company_email') ?? '';
        $companyPhone = setting('company_phone') ?? '';
        $companyAddress = setting('company_address') ?? '';
        $companyWebsite = url('/');

        // About/Hakkımızda
        $aboutText = setting('about_text') ?? '';

        return [
            'company_info' => [
                'name' => $companyName,
                'title' => $siteTitle,
                'slogan' => $siteSlogan,
                'website' => $companyWebsite,
            ],
            'contact_info' => [
                'email' => $companyEmail,
                'phone' => $companyPhone,
                'address' => $companyAddress,
            ],
            'about' => $aboutText,
            'focus' => 'general',
        ];
    }

    /**
     * Fallback outline (OpenAI başarısız olursa)
     * Her tenant kendi sektörüne göre override edebilir
     */
    public function getFallbackOutline(string $topicKeyword): array
    {
        return [
            $topicKeyword . ' Nedir?',
            'Genel Bilgiler',
            'Önemli Noktalar',
            'Uygulama Alanları',
            'Avantajlar ve Dezavantajlar',
            'Sonuç ve Öneriler',
        ];
    }
}
