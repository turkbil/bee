# HAKKIMIZDA SAYFASI KÜTÜPHANELER

## Gerekli CDN Linkleri

Sayfa `<head>` içine eklenecekler:

```html
<!-- AOS (Animate On Scroll) CSS -->
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
```

Sayfa `</body>` öncesine eklenecekler:

```html
<!-- AOS (Animate On Scroll) JS -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<!-- CountUp.js (Sayı Animasyonları) -->
<script src="https://cdn.jsdelivr.net/npm/countup@1.8.2/dist/countUp.min.js"></script>
```

## Pages Tablosuna Eklenecek Veriler

### title (JSON)
```json
{
  "tr": "Hakkımızda - Depo Ekipmanlarının Dijital Platformu | İXTİF"
}
```

### slug
```
hakkimizda
```

### body
```
[hakkimizda.html dosyasının içeriği]
```

### css
```
[hakkimizda.css dosyasının içeriği]
```

### js
```
[hakkimizda.js dosyasının içeriği]
```

### seo_settings (JSON)
```json
{
  "tr": {
    "meta_title": "Hakkımızda - Depo Ekipmanlarının Dijital Platformu | İXTİF",
    "meta_description": "10 yıllık deneyimimizle Türkiye'nin en büyük depo ekipmanları dijital platformu olmak için yola çıktık. 1,020+ ürün, 106 kategori, güvenilir iş ortaklığı.",
    "meta_keywords": "ixtif hakkında, forklift satış, depo ekipmanları bayilik, marketplace platform, ixtif kimdir, istanbul forklift, transpalet satış",
    "og_title": "Hakkımızda - Depo Ekipmanlarının Dijital Platformu | İXTİF",
    "og_description": "İXTİF olarak forklift ve depo ekipmanları pazarını dijitalleştiriyor, alıcı ile satıcıyı buluşturuyoruz. Bayimiz olun veya ürünlerinizi platformumuzda satın!",
    "og_image": "/images/og/hakkimizda-ixtif.jpg",
    "twitter_card": "summary_large_image",
    "canonical_url": "https://ixtif.com/hakkimizda"
  }
}
```

### schema_org (JSON)
```json
{
  "@context": "https://schema.org",
  "@type": "AboutPage",
  "mainEntity": {
    "@type": "Organization",
    "name": "İXTİF İÇ VE DIŞ TİCARET A.Ş.",
    "legalName": "İXTİF İÇ VE DIŞ TİCARET ANONİM ŞİRKETİ",
    "foundingDate": "2014",
    "description": "Türkiye'nin en büyük depo ekipmanları dijital platformu. Forklift, transpalet, istif makinesi satış, kiralama ve servis hizmetleri.",
    "url": "https://ixtif.com",
    "logo": "https://ixtif.com/images/logo/ixtif-logo.png",
    "image": "https://ixtif.com/images/og/hakkimizda-ixtif.jpg",
    "telephone": "+902167553555",
    "email": "info@ixtif.com",
    "address": {
      "@type": "PostalAddress",
      "streetAddress": "Küçükyalı Mahallesi, Çamlık Sokak, Manzara Adalar Sitesi, B Blok No: 1/B, İç Kapı No: 89",
      "addressLocality": "Kartal",
      "addressRegion": "İstanbul",
      "postalCode": "34840",
      "addressCountry": "TR"
    },
    "vatID": "4831552951",
    "taxID": "4831552951",
    "contactPoint": [
      {
        "@type": "ContactPoint",
        "telephone": "+902167553555",
        "contactType": "customer service",
        "areaServed": "TR",
        "availableLanguage": ["Turkish", "English"]
      },
      {
        "@type": "ContactPoint",
        "telephone": "+905322160754",
        "contactType": "sales",
        "contactOption": "TollFree",
        "areaServed": "TR",
        "availableLanguage": "Turkish"
      }
    ],
    "sameAs": [
      "https://www.instagram.com/ixtifcom",
      "https://www.facebook.com/ixtif",
      "https://wa.me/905322160754"
    ],
    "numberOfEmployees": {
      "@type": "QuantitativeValue",
      "value": "25"
    },
    "slogan": "Depo Ekipmanlarının Dijital Platformu",
    "keywords": "forklift, transpalet, istif makinesi, reach truck, depo ekipmanları, akülü istif, elektrikli forklift"
  }
}
```

## Layout Template'e Eklenecekler

Layout dosyasında (örn: `resources/views/layouts/app.blade.php`) şu değişiklikler yapılmalı:

### Head içine:
```blade
@if(isset($page) && $page->slug === 'hakkimizda')
    <!-- AOS CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
@endif

<!-- Sayfa özel CSS -->
@if(isset($page) && $page->css)
    <style>{!! $page->css !!}</style>
@endif
```

### Body sonuna:
```blade
@if(isset($page) && $page->slug === 'hakkimizda')
    <!-- AOS JS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <!-- CountUp.js -->
    <script src="https://cdn.jsdelivr.net/npm/countup@1.8.2/dist/countUp.min.js"></script>
@endif

<!-- Sayfa özel JS -->
@if(isset($page) && $page->js)
    <script>{!! $page->js !!}</script>
@endif
```

## NOT

- **Alpine.js** ve **Tailwind CSS** zaten sistemde mevcut (ekstra yükleme gereksiz)
- **Font Awesome Pro** zaten sistemde mevcut
- **AOS** ve **CountUp.js** sadece bu sayfa için yüklenecek (conditional loading)
