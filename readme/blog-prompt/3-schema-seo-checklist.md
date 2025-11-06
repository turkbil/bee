# 3 - SCHEMA & SEO CHECKLIST

> **Blog içeriği yayınlanmadan önce MUTLAKA kontrol edilmesi gereken SEO ve Schema.org yapılandırılmış veri kontrol listesi**

---

## 📋 İçindekiler

1. [Schema.org Yapılandırılmış Veriler](#schemaorg-yapılandırılmış-veriler)
2. [On-Page SEO Kontrolleri](#on-page-seo-kontrolleri)
3. [İçerik Kalitesi](#içerik-kalitesi)
4. [Teknik SEO](#teknik-seo)
5. [Test Araçları](#test-araçları)

---

## 🏗️ Schema.org Yapılandırılmış Veriler

### 1. Article Schema (Zorunlu - Tüm Blog İçerikleri)

```json
{
  "@context": "https://schema.org",
  "@type": "Article",
  "headline": "Transpalet Nedir? [2025 Detaylı Rehber]",
  "description": "Meta description buraya",
  "image": "https://domain.com/image.jpg",
  "author": {
    "@type": "Organization",
    "name": "Site Adı"
  },
  "publisher": {
    "@type": "Organization",
    "name": "Site Adı",
    "logo": {
      "@type": "ImageObject",
      "url": "https://domain.com/logo.png"
    }
  },
  "datePublished": "2025-11-06",
  "dateModified": "2025-11-06"
}
```

**Kontrol Listesi:**
- ✅ Headline: 60 karakter içinde, anahtar kelime içeriyor
- ✅ Description: Meta description ile aynı
- ✅ Image: Yüksek çözünürlük (min 1200x675px), optimize edilmiş
- ✅ Author/Publisher: Doğru organization bilgisi
- ✅ DatePublished: ISO 8601 formatında (YYYY-MM-DD)
- ✅ DateModified: Güncelleme varsa tarihi güncel

---

### 2. FAQPage Schema (Zorunlu - FAQ Bölümü Olan İçerikler)

```json
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {
      "@type": "Question",
      "name": "Transpalet ne kadar ağırlık taşır?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Standart manuel transpalet 2.000-2.500 kg kapasiteli olup, endüstriyel kullanım için 5.000 kg kapasiteye kadar modeller mevcuttur."
      }
    },
    {
      "@type": "Question",
      "name": "Manuel ve elektrikli transpalet farkı nedir?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Manuel transpalet hidrolik pompa ile çalışırken, elektrikli transpalet akü ile çalışır ve otomatik kaldırma sistemi sunar."
      }
    }
  ]
}
```

**Kontrol Listesi:**
- ✅ Minimum 5 soru-cevap
- ✅ Her soru uzun kuyruklu anahtar kelime içeriyor
- ✅ Cevaplar 50-100 kelime arası, özlü ve net
- ✅ HTML'de de soru-cevap yapısı var (`<div itemscope itemtype="https://schema.org/Question">`)

---

### 3. Product Schema (Ürün İçerikleri İçin)

```json
{
  "@context": "https://schema.org",
  "@type": "Product",
  "name": "Manuel Transpalet 2.5 Ton",
  "description": "2.5 ton kapasiteli manuel hidrolik transpalet",
  "sku": "TP-2500-MAN",
  "brand": {
    "@type": "Brand",
    "name": "Marka Adı"
  },
  "offers": {
    "@type": "Offer",
    "url": "https://domain.com/urun/manuel-transpalet",
    "priceCurrency": "TRY",
    "price": "12500",
    "priceValidUntil": "2025-12-31",
    "itemCondition": "https://schema.org/NewCondition",
    "availability": "https://schema.org/InStock"
  },
  "aggregateRating": {
    "@type": "AggregateRating",
    "ratingValue": "4.5",
    "reviewCount": "24"
  }
}
```

**Kontrol Listesi:**
- ✅ Ürün adı net ve açıklayıcı
- ✅ SKU benzersiz
- ✅ Fiyat güncel ve doğru currency
- ✅ Availability durumu doğru (InStock, OutOfStock, PreOrder)
- ✅ Rating varsa doğru değerler (1-5 arası)

---

### 4. BreadcrumbList Schema (Zorunlu - Tüm Sayfalar)

```json
{
  "@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [
    {
      "@type": "ListItem",
      "position": 1,
      "name": "Ana Sayfa",
      "item": "https://domain.com"
    },
    {
      "@type": "ListItem",
      "position": 2,
      "name": "Blog",
      "item": "https://domain.com/blog"
    },
    {
      "@type": "ListItem",
      "position": 3,
      "name": "Transpalet Nedir?",
      "item": "https://domain.com/blog/transpalet-nedir"
    }
  ]
}
```

**Kontrol Listesi:**
- ✅ Tüm breadcrumb seviyeleri dahil
- ✅ Position numaraları sıralı (1, 2, 3...)
- ✅ URL'ler absolute (https://domain.com ile başlayan)
- ✅ Son seviye (current page) de dahil

---

### 5. HowTo Schema (Rehber İçerikleri İçin)

```json
{
  "@context": "https://schema.org",
  "@type": "HowTo",
  "name": "Transpalet Nasıl Kullanılır?",
  "description": "Manuel transpalet kullanım rehberi - adım adım",
  "step": [
    {
      "@type": "HowToStep",
      "position": 1,
      "name": "Transpalet kontrolü",
      "text": "Transpalet çatallarını kontrol edin, hasar olup olmadığını inceleyin."
    },
    {
      "@type": "HowToStep",
      "position": 2,
      "name": "Palete yaklaşma",
      "text": "Çatalları paletin altına doğru yönlendirin ve tam ortaya yerleştirin."
    },
    {
      "@type": "HowToStep",
      "position": 3,
      "name": "Paleti kaldırma",
      "text": "Hidrolik pompayı yukarı aşağı hareket ettirerek paleti yerden kaldırın."
    }
  ]
}
```

**Kontrol Listesi:**
- ✅ Minimum 3 adım
- ✅ Her adım tek bir işlem içeriyor
- ✅ Adımlar sıralı (position: 1, 2, 3...)
- ✅ Her adımda net açıklama var

---

## 🔍 On-Page SEO Kontrolleri

### Title Tag
- ✅ **Uzunluk**: 50-60 karakter (max 600px)
- ✅ **Anahtar kelime**: Başta yer alıyor
- ✅ **Format**: `Anahtar Kelime | Marka Adı` veya `Anahtar Kelime - Açıklama [Yıl]`
- ✅ **Benzersiz**: Site içinde başka title ile aynı değil
- ✅ **Örnek**: `Transpalet Nedir? Çeşitleri ve Özellikleri [2025] | Site Adı`

### Meta Description
- ✅ **Uzunluk**: 155-160 karakter (max 920px)
- ✅ **Anahtar kelime**: En az 1 kez geçiyor
- ✅ **CTA**: Eylem çağrısı var ("Detaylı bilgi alın", "Keşfedin", "Öğrenin")
- ✅ **Benzersiz**: Site içinde başka description ile aynı değil
- ✅ **Örnek**: `Transpalet nedir, nasıl çalışır, çeşitleri nelerdir? Manuel, elektrikli ve akülü transpalet özellikleri hakkında detaylı rehber. ➤ Hemen öğrenin!`

### URL Slug
- ✅ **Kısa**: Max 5-6 kelime
- ✅ **Anahtar kelime**: Ana anahtar kelime içeriyor
- ✅ **Okunabilir**: Tire (-) ile ayrılmış, Türkçe karakter yok
- ✅ **Statik**: Tarih, ID gibi dinamik değer yok
- ✅ **Örnek**: `transpalet-nedir-cesitleri-ozellikleri`

### H1 Başlık
- ✅ **Tek H1**: Sayfada sadece 1 adet
- ✅ **Anahtar kelime**: Ana anahtar kelime içeriyor
- ✅ **Uzunluk**: 60-70 karakter
- ✅ **Örnek**: `Transpalet Nedir? Çeşitleri, Özellikleri ve Kullanım Alanları`

### H2/H3 Başlıklar
- ✅ **Hiyerarşik**: H2 → H3 → H4 sırası doğru
- ✅ **Anahtar kelime**: Destek anahtar kelimeler dağıtılmış
- ✅ **Soru formatı**: Bazı başlıklar soru formatında (featured snippet için)
- ✅ **Sayı**: Minimum 4-6 H2 başlık
- ✅ **Örnek H2'ler**:
  - Transpalet Tanımı ve Temel Bilgiler
  - Transpalet Çeşitleri
  - Transpalet Nasıl Çalışır?
  - Transpalet Kullanım Alanları

### İçerik Optimizasyonu
- ✅ **Kelime sayısı**: 2.000 ± 200 kelime
- ✅ **Anahtar kelime yoğunluğu**: %1-2 (doğal, spam değil)
- ✅ **İlk paragraf**: İlk 100 kelimede ana anahtar kelime var
- ✅ **LSI terimleri**: Eş anlamlı ve ilgili terimler kullanılmış
- ✅ **Uzun kuyruk**: Long-tail anahtar kelimeler dağıtılmış

### Dahili Bağlantılar
- ✅ **Sayı**: Minimum 5-10 dahili link
- ✅ **Anchor text**: Anlamlı, anahtar kelime içeren (generic "tıklayın" değil)
- ✅ **Hedef**: İlgili sayfalar, kategori, ürünler
- ✅ **Dofollow**: Dahili linkler nofollow değil
- ✅ **Örnek**: `[manuel transpalet özellikleri](URL)` ✅ | `[buraya tıklayın](URL)` ❌

### Dış Bağlantılar (Outbound)
- ✅ **Sayı**: Minimum 3-5 otorite kaynak
- ✅ **Kalite**: Resmi kuruluşlar, endüstri standartları, teknik dökümanlar
- ✅ **Relevance**: Konu ile ilgili, güvenilir
- ✅ **Nofollow**: Gerekirse nofollow attribute ekle
- ✅ **Örnek Kaynaklar**:
  - ISO standartları
  - Üretici teknik dökümanları
  - Ticaret odaları, endüstri birlikleri

### Görseller
- ✅ **Alt text**: Her görselde anlamlı alt text
- ✅ **Format**: Alt text = `[Anahtar kelime] + [açıklama]`
- ✅ **Dosya adı**: Anlamlı, anahtar kelime içeren (`manuel-transpalet-kullanim.jpg`)
- ✅ **Boyut**: Optimize edilmiş (max 200KB, WebP format)
- ✅ **Çözünürlük**: Minimum 800px genişlik
- ✅ **Lazy loading**: `loading="lazy"` attribute

---

## 📊 İçerik Kalitesi

### Okunabilirlik
- ✅ **Flesch Reading Ease**: 50-60 arası (orta zorluk)
- ✅ **Cümle uzunluğu**: Ortalama ≤20 kelime
- ✅ **Paragraf uzunluğu**: ≤150 kelime
- ✅ **Pasif cümle**: <%10 (aktif cümle öncelikli)
- ✅ **Transition words**: Yeterli geçiş kelimesi var

### Yapı
- ✅ **Giriş**: İlk 100-150 kelimede problem tanımı + ana anahtar kelime
- ✅ **Gövde**: Mantıklı H2/H3 hiyerarşisi
- ✅ **Sonuç**: Özet + CTA (call-to-action)
- ✅ **FAQ**: Minimum 5-10 soru-cevap
- ✅ **Madde/Tablo**: Liste veya tablo formatı kullanılmış

### E-A-T (Expertise, Authoritativeness, Trustworthiness)
- ✅ **Kaynak**: Her iddia için kaynak referansı
- ✅ **Güncel**: Tarih, istatistik güncel (2024-2025)
- ✅ **Yazar**: Yazar/organization bilgisi belirtilmiş
- ✅ **İletişim**: İletişim bilgisi mevcut (sayfa altı)

---

## ⚙️ Teknik SEO

### Mobil Uyumluluk
- ✅ **Responsive**: Mobil cihazlarda düzgün görünüyor
- ✅ **Font boyutu**: Minimum 16px
- ✅ **Touch target**: Butonlar minimum 48x48px
- ✅ **Viewport**: `<meta name="viewport" content="width=device-width, initial-scale=1">`

### Sayfa Hızı
- ✅ **Core Web Vitals**:
  - LCP (Largest Contentful Paint): <2.5s
  - FID (First Input Delay): <100ms
  - CLS (Cumulative Layout Shift): <0.1
- ✅ **Görsel optimizasyonu**: WebP, lazy loading
- ✅ **CSS/JS**: Minified, defer/async yüklenmiş
- ✅ **Cache**: Browser cache aktif

### Canonical & Indexing
- ✅ **Canonical tag**: Self-canonical veya doğru canonical URL
- ✅ **Robots meta**: `index, follow` (engellenmiş değil)
- ✅ **XML Sitemap**: Sayfa sitemap'e eklenmiş
- ✅ **Robots.txt**: Sayfa robots.txt'de engellenmiş değil

### Sosyal Medya
- ✅ **Open Graph**:
  ```html
  <meta property="og:title" content="Transpalet Nedir?">
  <meta property="og:description" content="Meta description">
  <meta property="og:image" content="https://domain.com/image.jpg">
  <meta property="og:url" content="https://domain.com/blog/transpalet-nedir">
  <meta property="og:type" content="article">
  ```
- ✅ **Twitter Card**:
  ```html
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="Transpalet Nedir?">
  <meta name="twitter:description" content="Meta description">
  <meta name="twitter:image" content="https://domain.com/image.jpg">
  ```

---

## 🧪 Test Araçları

### Schema Validation
- 🔗 [Google Rich Results Test](https://search.google.com/test/rich-results)
- 🔗 [Schema.org Validator](https://validator.schema.org/)
- ✅ **Kontrol**: Hiç hata yok, tüm required field'lar dolu

### SEO Audit
- 🔗 [Google Search Console](https://search.google.com/search-console)
  - ✅ URL inspection: Indexed, no errors
  - ✅ Mobile usability: No issues
  - ✅ Core Web Vitals: Green zone
- 🔗 [PageSpeed Insights](https://pagespeed.web.dev/)
  - ✅ Performance: >80 (mobile & desktop)
- 🔗 [Screaming Frog SEO Spider](https://www.screamingfrogseoseo.com/)
  - ✅ No broken links
  - ✅ All images have alt text

### Okunabilirlik
- 🔗 [Hemingway Editor](http://www.hemingwayapp.com/)
  - ✅ Readability: Grade 8-10
- 🔗 [Yoast SEO (WordPress)](https://yoast.com/)
  - ✅ SEO score: Green (>80)
  - ✅ Readability score: Green (>60)

---

## 📋 Yayın Öncesi Final Checklist

### Zorunlu Kontroller
- [ ] Title tag optimize edilmiş (50-60 karakter)
- [ ] Meta description optimize edilmiş (155-160 karakter)
- [ ] URL slug SEO-friendly
- [ ] H1 başlık tek ve optimize
- [ ] Article Schema eklendi
- [ ] FAQPage Schema eklendi (FAQ varsa)
- [ ] BreadcrumbList Schema eklendi
- [ ] Tüm görsellerde alt text var
- [ ] Minimum 5 dahili bağlantı
- [ ] Minimum 3 otorite dış kaynak
- [ ] Schema validation hatasız
- [ ] Mobile-friendly
- [ ] PageSpeed score >80

### Opsiyonel Ama Önerilen
- [ ] Product Schema (ürün içeriklerinde)
- [ ] HowTo Schema (rehber içeriklerinde)
- [ ] İnfografik veya görsel içerik
- [ ] Video embed (YouTube)
- [ ] İçerik içi tablo/liste
- [ ] Social media preview optimize
- [ ] Internal linking cluster strategy

---

## 🎯 SEO Performans Metrikleri (90 Gün Hedef)

### Organic Traffic
- 🎯 Hedef: %200 artış
- 📊 Takip: Google Analytics

### Anahtar Kelime Sıralaması
- 🎯 Ana anahtar kelime: Top 5
- 🎯 Destek anahtar kelimeler: Top 10
- 📊 Takip: Google Search Console, Ahrefs/SEMrush

### Featured Snippet
- 🎯 Hedef: Minimum 1 featured snippet
- 📊 Takip: Google Search Console

### CTR (Click-Through Rate)
- 🎯 Hedef: >5% (organic search)
- 📊 Takip: Google Search Console

### Engagement
- 🎯 Bounce rate: <60%
- 🎯 Avg. session duration: >2 dakika
- 🎯 Pages per session: >2
- 📊 Takip: Google Analytics

---

## 📞 Destek

**Dosya Konumu:** `/Users/nurullah/Desktop/cms/laravel/readme/blog-prompt/`

**Güncelleme Tarihi:** 6 Kasım 2025

**İlgili Dökümanlar:**
- `1-blog-taslak-olusturma.md` - Blog outline prompt
- `2-blog-yazdirma.md` - Blog yazma prompt
- `README.md` - Ana kılavuz

---

**Hazırlayan:** Claude AI + Nurullah
**Hedef:** Türkiye pazarında endüstriyel ürün satışı için SEO-optimize blog içerikleri
