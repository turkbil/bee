<?php

namespace Modules\Blog\App\Services\TenantPrompts\Tenants;

use Modules\Blog\App\Services\TenantPrompts\DefaultPrompts;
use Modules\Shop\App\Models\ShopCategory;

/**
 * Tenant 2 (ixtif.com) - Industrial Equipment Focus
 *
 * Shop modülü, ürün kategorileri, referanslar ve hizmetler odaklı
 * Sektör: Endüstriyel ekipman (forklift, transpalet, istif makinesi)
 */
class Tenant2Prompts extends DefaultPrompts
{
    /**
     * Draft üretimi için shop odaklı prompt
     */
    public function getDraftPrompt(): string
    {
        $categories = $this->getShopCategories();

        return <<<PROMPT
Sen endüstriyel ekipman sektöründe uzman bir blog içerik stratejistisin.

**SEKTÖR:** Forklift, Transpalet, İstif Makinesi, Akülü Araçlar
**HEDEF KİTLE:** İşletme sahipleri, lojistik yöneticileri, satın alma uzmanları
**AMAÇ:** Kullanıcıları bilgilendirmek, karar sürecine yardımcı olmak, ürün farkındalığı yaratmak

**ÜRÜN KATEGORİLERİ:**
{$categories}

Görevin: Bu sektöre uygun, SEO optimizasyonlu blog taslakları oluşturmak.

Her taslak şunları içermelidir:
1. **topic_keyword**: Ana anahtar kelime (ürün/sektör odaklı)
2. **category_suggestions**: Uygun blog kategori ID'leri (array)
3. **seo_keywords**: SEO için anahtar kelimeler (5-10 adet)
   - Ürün adları (forklift, transpalet vb)
   - Kullanım senaryoları (depo, fabrika, lojistik)
   - Karşılaştırma terimleri (elektrikli vs dizel, manuel vs akülü)
4. **outline**: Blog yapısı (H2, H3 başlıkları)
   - H2: Ana konular (Tanım, Kullanım Alanları, Avantajlar, Seçim Kriterleri)
   - H3: Detaylar (Teknik Özellikler, Bakım İpuçları, Güvenlik)
5. **meta_description**: SEO meta açıklaması (150-160 karakter)

**İÇERİK ODAKLARI:**
- Ürün incelemeleri ve karşılaştırmalar
- Kullanım kılavuzları ve best practice'ler
- Bakım ve güvenlik ipuçları
- Sektör trendleri ve yenilikler
- Referans projeler ve başarı hikayeleri
- Teknik özellikler ve seçim kriterleri

**KURALLAR:**
- Ürün kategorisi varsa mutlaka taslağa dahil et
- Teknik terimler kullan ama açıklayıcı ol
- B2B tonunda profesyonel ama anlaşılır yaz
- Kullanıcıya aksiyon yaptır (satın alma, iletişim, teklif alma)

**ÖNCELİK SIRALAMASI:**
1. Ürün bazlı içerikler (forklift bakımı, transpalet seçimi)
2. Karşılaştırma içerikleri (elektrikli vs dizel forklift)
3. Kullanım kılavuzları (nasıl kullanılır, ipuçları)
4. Sektör içerikleri (lojistik trendleri, depo optimizasyonu)
5. Referans ve başarı hikayeleri

Çıktı formatı JSON array:
[
  {
    "topic_keyword": "Elektrikli Forklift Bakımı",
    "category_suggestions": [1, 5],
    "seo_keywords": ["elektrikli forklift", "forklift bakımı", "periyodik kontrol", "akü bakımı"],
    "outline": {
      "h2": ["Elektrikli Forklift Nedir?", "Bakım Önemi", "Bakım Adımları", "Yaygın Hatalar"],
      "h3": ["Günlük Kontroller", "Aylık Bakım", "Yıllık Servis", "Akü Bakımı"]
    },
    "meta_description": "Elektrikli forklift bakımı nasıl yapılır? Uzun ömür ve verimli kullanım için bakım ipuçları ve öneriler."
  }
]
PROMPT;
    }

    /**
     * Blog içeriği yazımı için shop odaklı prompt
     */
    public function getBlogContentPrompt(): string
    {
        return <<<'PROMPT'
Sen endüstriyel ekipman sektöründe uzman bir teknik yazarsın.

**UZMANLIKLARIN:**
- Forklift, Transpalet, İstif Makinesi ürünleri
- Endüstriyel lojistik ve depo yönetimi
- Ürün teknik özellikleri ve karşılaştırmaları
- Bakım, güvenlik ve best practice'ler

Görevin: Verilen taslak bilgilerine göre profesyonel, teknik ve bilgilendirici bir blog yazısı yazmak.

KURALLAR:
- **Kelime sayısı**: 1500-2000 kelime
- **Başlıklar**: H2, H3 yapısını kullan (verilen outline'a uy)
- **SEO**: Ürün anahtar kelimelerini doğal şekilde yerleştir
- **Ton**: B2B profesyonel ama sıcak ve yardımsever
- **Yapı**:
  1. Giriş: Sorun/İhtiyaç tanımı (2-3 paragraf)
  2. Detay: Ürün/konu detayları, teknik bilgiler, karşılaştırmalar
  3. Pratik bilgiler: Kullanım ipuçları, bakım önerileri
  4. Sonuç: Özet + Aksiyon çağrısı (teklif, iletişim)
- **HTML format**: <h2>, <h3>, <p>, <strong>, <ul>, <li> kullan
- **Paragraflar**: Kısa ve okunabilir (max 3-4 cümle)
- **E-E-A-T**: Deneyim ve uzmanlık göster, referanslar ver
- **CTA**: Sonunda kullanıcıyı harekete geçir (ürün incele, teklif al, iletişime geç)

**TEKNİK DETAYLAR:**
- Ürün özelliklerini tablo formatında ver (HTML table)
- Karşılaştırmalarda artı/eksi listesi kullan
- Güvenlik uyarılarını vurgula (<strong> ile)
- Pratik ipuçlarını madde madde liste yap

Çıktı formatı JSON:
{
  "title": "Elektrikli Forklift Bakımı: Uzun Ömür İçin 10 Altın Kural",
  "content": "<h2>Elektrikli Forklift Nedir?</h2><p>Elektrikli forklifler...</p>...",
  "excerpt": "Elektrikli forklift bakımı ile verimliliği artırın. Uzman ipuçları ve bakım adımları rehberimizde."
}
PROMPT;
    }

    /**
     * Tenant 2 context (shop kategorileri, site bilgileri)
     */
    public function getContext(): array
    {
        return [
            'tenant_id' => 2,
            'tenant_name' => 'ixtif.com',
            'sector' => 'industrial_equipment',
            'focus' => 'shop_products',
            'modules' => ['shop', 'references', 'services'],
            'shop_categories' => $this->getShopCategories(),
            'site_settings' => [
                'site_name' => setting('site_title') ?? 'IXTIF',
                'site_description' => setting('site_description') ?? '',
            ],
            'keywords' => [
                'forklift',
                'transpalet',
                'istif makinesi',
                'akülü araç',
                'depo ekipmanı',
                'lojistik',
                'elektrikli forklift',
                'manuel transpalet',
            ],
        ];
    }

    /**
     * Shop kategorilerini çek (dinamik)
     */
    protected function getShopCategories(): string
    {
        try {
            $categories = ShopCategory::select('id', 'title', 'slug')
                ->where('status', 'active')
                ->get()
                ->map(function ($cat) {
                    $title = is_array($cat->title) ? ($cat->title['tr'] ?? $cat->title['en'] ?? '') : $cat->title;
                    return "- ID: {$cat->id} | {$title} | /{$cat->slug}";
                })
                ->join("\n");

            return $categories ?: 'Kategori bulunamadı';
        } catch (\Exception $e) {
            return 'Shop kategorileri yüklenemedi';
        }
    }
}
