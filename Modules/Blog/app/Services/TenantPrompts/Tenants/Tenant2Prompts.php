<?php

namespace Modules\Blog\App\Services\TenantPrompts\Tenants;

use Illuminate\Support\Facades\File;

/**
 * Tenant 2 (iXtif) Blog AI Prompts
 *
 * iXtif.com için özel AI prompt'ları
 * readme/blog-prompt/ klasöründeki dökümanları kullanır
 */
class Tenant2Prompts
{
    protected string $promptPath;

    public function __construct()
    {
        $this->promptPath = base_path('readme/blog-prompt');
    }

    /**
     * Draft (taslak) üretimi için AI prompt
     */
    public function getDraftPrompt(): string
    {
        $promptFile = $this->promptPath . '/1-blog-taslak-olusturma.md';

        if (File::exists($promptFile)) {
            return File::get($promptFile);
        }

        // Fallback prompt
        return $this->getDefaultDraftPrompt();
    }

    /**
     * Blog içeriği yazımı için AI prompt
     */
    public function getBlogContentPrompt(): string
    {
        $promptFile = $this->promptPath . '/2-blog-yazdirma.md';

        if (File::exists($promptFile)) {
            $basePrompt = File::get($promptFile);

            // Firma bilgisi kullanımı kuralını ekle
            $companyRules = $this->getCompanyUsageRules();

            return $basePrompt . "\n\n" . $companyRules;
        }

        // Fallback prompt
        return $this->getDefaultContentPrompt();
    }

    /**
     * Firma bilgisi kullanım kuralları
     */
    protected function getCompanyUsageRules(): string
    {
        return <<<'RULES'

---

## 🏢 FİRMA BİLGİSİ KULLANIMI (ZORUNLU)

### Firma Bilgileri Context'ten Alınacak:
- **Firma Adı**: {company_info.name}
- **Site Başlığı**: {company_info.title}
- **Slogan**: {company_info.slogan}
- **Website**: {company_info.website}
- **Email**: {contact_info.email}
- **Telefon**: {contact_info.phone}
- **Adres**: {contact_info.address}

### KURALLAR:

★ **Firma Adından Bahsetme Zorunluluğu**:
  - Blog yazısında EN AZ 2-3 kez firma adından bahset
  - İlk bahsetme: İlk 300 kelime içinde
  - Son bahsetme: Sonuç/CTA bölümünde

★ **Kullanım Örnekleri**:
  ✅ "Firma adı, endüstriyel ekipman sektöründe..."
  ✅ "Firma adı olarak, müşterilerimize..."
  ✅ "Daha fazla bilgi için firma ekibimizle iletişime geçebilirsiniz."

★ **İletişim Bilgisi Ekleme**:
  - Sonuç bölümünde MUTLAKA iletişim bilgisi ver
  - CTA (Call-to-Action) cümlesi ekle

★ **Otorite & Güvenilirlik Gösterimi**:
  - "Firma adı olarak, profesyonel ekipman tedarikinde..."
  - Firma adıyla uzmanlık vurgusu yap

★ **YASAKLAR**:
  ❌ Rakip firma adı kullanma
  ❌ Genel ifadeler kullanma ("Bu firmalar", "Tedarikçiler")
  ❌ İletişim bilgisi olmadan bitirme

### CTA (Call-to-Action) Zorunluluğu:
Blog sonunda MUTLAKA bir CTA bölümü olmalı:
- Ürün/hizmet hakkında daha fazla bilgi
- İletişim kurmaya davet
- Telefon + Email bilgisi

RULES;
    }

    /**
     * Tenant için genel context (ayarlar, kategoriler vb)
     */
    public function getContext(): array
    {
        // Site bilgileri (Group 6)
        $siteTitle = setting('site_title') ?? setting('site_name') ?? 'iXtif';
        $siteSlogan = setting('site_slogan') ?? 'Endüstriyel Ekipman Uzmanı';
        $companyName = setting('company_name') ?? $siteTitle;

        // İletişim bilgileri (Group 10)
        $companyEmail = setting('company_email') ?? 'info@ixtif.com';
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
            'focus' => 'industrial_equipment',
            'industry' => 'B2B Endüstriyel Ekipman',
            'target_audience' => 'Satın alma müdürleri, depo yöneticileri, lojistik sorumlular',
        ];
    }

    /**
     * Fallback draft prompt
     */
    protected function getDefaultDraftPrompt(): string
    {
        return 'Sen profesyonel bir SEO ve içerik stratejistisin. Endüstriyel ekipman konularında blog taslakları oluştur.';
    }

    /**
     * Fallback content prompt
     */
    protected function getDefaultContentPrompt(): string
    {
        return 'Sen profesyonel bir endüstriyel ekipman içerik yazarısın. 2000 kelimelik teknik blog yazıları yazıyorsun.';
    }
}
