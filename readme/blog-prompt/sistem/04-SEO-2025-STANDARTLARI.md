# 🎯 SEO 2025 STANDARTLARI VE KURALLARI

> **Google'ın 2025 Algoritma Güncellemelerine Uygun İçerik Üretimi**

---

## 📋 2025 SEO PARADİGMA DEĞİŞİMİ

### Eski SEO (2020 öncesi) vs Yeni SEO (2025)

| Eski Yaklaşım | Yeni Yaklaşım (2025) |
|---------------|----------------------|
| Keyword stuffing | Natural language + semantic search |
| Backlink quantity | Backlink quality + authority |
| Content length | Content quality + user intent |
| Meta keywords | E-E-A-T signals |
| Generic content | Personalized + contextual |
| Desktop-first | Mobile-first mandatory |
| Manual optimization | AI-assisted + automation |

---

## 🏆 E-E-A-T PRENSİPLERİ (2024+ Zorunlu)

### Experience + Expertise + Authoritativeness + Trustworthiness

#### 1. **Experience (Deneyim)**
```
✅ Gerçek kullanım deneyimi
✅ First-hand knowledge
✅ Pratik örnekler
✅ Case studies
✅ Fotoğraf/video kanıtları

Blog İçin Uygulama:
- "15 yıldır endüstriyel ekipman sektöründeyiz"
- "10,000+ müşteriye hizmet verdik"
- "Müşteri başarı hikayeleri" bölümü
- Kullanım videoları (v2)
```

#### 2. **Expertise (Uzmanlık)**
```
✅ Yazar bio (organization)
✅ Sertifikalar/belge mention
✅ Sektör standartları referansı
✅ Teknik detay derinliği

Blog İçin Uygulama:
- "CE, TSE, ISO 3691-1 standartlarına uygun"
- "Mühendislerimiz tarafından onaylandı"
- Teknik çizimler + spesifikasyon tabloları
- Akademik/endüstri kaynakları cite
```

#### 3. **Authoritativeness (Otorite)**
```
✅ Domain authority (organik)
✅ Backlinks (quality sites)
✅ Brand mentions
✅ Industry recognition

Blog İçin Uygulama:
- Sektör liderlerine linkler
- Referans sitelerden backlink
- Sosyal medya mentions
- PR + medya yayınları
```

#### 4. **Trustworthiness (Güvenilirlik)**
```
✅ HTTPS (mandatory)
✅ İletişim bilgileri (açık)
✅ Gizlilik politikası
✅ Güncel içerik (düzenli update)
✅ Hata/yanlış bilgi olmayan

Blog İçin Uygulama:
- Son güncelleme tarihi göster
- Fact-checking yapıldı badge
- İstatistiklerde kaynak linkle
- Hatalı bilgi varsa düzelt + not ekle
```

---

## 🚀 CORE WEB VITALS (Zorunlu Metrikler)

### 1. LCP (Largest Contentful Paint)
**Hedef:** < 2.5 saniye

```
Optimizasyon:
✅ WebP görsel formatı
✅ Lazy loading (images)
✅ CDN kullanımı
✅ Critical CSS inline
✅ Preload key resources
✅ Font optimization

Blog İçin:
- Featured image: WebP, 200KB max
- Thumbmaker servisi kullan
- Above-the-fold content öncelik
- Hero section'ı optimize et
```

### 2. FID (First Input Delay)
**Hedef:** < 100ms

```
Optimizasyon:
✅ JavaScript minimize et
✅ Defer non-critical JS
✅ Code splitting
✅ Remove unused code
✅ Optimize third-party scripts

Blog İçin:
- Alpine.js (minimal JS)
- FontAwesome: only used icons
- Google Analytics: async
- Chat widget: lazy load
```

### 3. CLS (Cumulative Layout Shift)
**Hedef:** < 0.1

```
Optimizasyon:
✅ Image dimensions tanımla
✅ Font swap stratejisi
✅ Reserved space (ads/embeds)
✅ Avoid dynamic content insertion

Blog İçin:
- width/height her görselde
- aspect-ratio CSS property
- Skeleton loading
- Fixed ad spaces
```

---

## 📱 MOBILE-FIRST İNDEXİNG (Mandatory)

### Google artık sadece mobile versiyona bakıyor!

```html
✅ Responsive Design (Tailwind)
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3">

✅ Touch-friendly (44x44px minimum)
<button class="px-6 py-3 min-w-[44px] min-h-[44px]">

✅ Readable font sizes (16px+)
<p class="text-base md:text-lg">

✅ No horizontal scroll
<div class="overflow-x-auto">

✅ Fast mobile loading (<3s)
```

---

## 🎨 YAPISAL VERİ (Schema.org 2025)

### Zorunlu Schema Tipleri

#### 1. Article Schema (Blog için)
```json
{
  "@type": "Article",
  "headline": "Max 110 karakter",
  "image": ["1200x675", "1200x1200", "1200x900"],
  "datePublished": "2025-11-14T08:00:00+03:00",
  "dateModified": "2025-11-14T10:30:00+03:00",
  "author": {
    "@type": "Organization",
    "name": "İxtif Endüstriyel",
    "url": "https://ixtif.com",
    "logo": "https://ixtif.com/logo.png"
  },
  "publisher": {
    "@type": "Organization",
    "name": "İxtif Endüstriyel",
    "logo": {
      "@type": "ImageObject",
      "url": "https://ixtif.com/logo.png",
      "width": 800,
      "height": 600
    }
  },
  "description": "Max 200 karakter",
  "mainEntityOfPage": {
    "@type": "WebPage",
    "@id": "https://ixtif.com/blog/slug"
  }
}
```

#### 2. FAQPage Schema (SSS için)
```json
{
  "@type": "FAQPage",
  "mainEntity": [
    {
      "@type": "Question",
      "name": "Soru tam metni?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Cevap (HTML destekli, <p><strong> kullanılabilir)"
      }
    }
  ]
}
```

#### 3. BreadcrumbList Schema (Navigasyon için)
```json
{
  "@type": "BreadcrumbList",
  "itemListElement": [
    {
      "@type": "ListItem",
      "position": 1,
      "name": "Ana Sayfa",
      "item": "https://ixtif.com/"
    },
    {
      "@type": "ListItem",
      "position": 2,
      "name": "Blog",
      "item": "https://ixtif.com/blog"
    },
    {
      "@type": "ListItem",
      "position": 3,
      "name": "Transpalet Nedir",
      "item": "https://ixtif.com/blog/transpalet-nedir"
    }
  ]
}
```

#### 4. HowTo Schema (Nasıl yapılır içerikler için)
```json
{
  "@type": "HowTo",
  "name": "Transpalet Nasıl Kullanılır?",
  "description": "Adım adım kullanım rehberi",
  "totalTime": "PT10M",
  "step": [
    {
      "@type": "HowToStep",
      "position": 1,
      "name": "Adım 1",
      "text": "Detaylı açıklama",
      "image": "https://cdn.com/step1.jpg"
    }
  ]
}
```

---

## 🔍 SEARCH INTENT OPTİMİZASYONU

### Intent Tipleri ve İçerik Yapısı

#### 1. **Informational Intent** (Bilgi arama)
```
Anahtar Kelimeler: "nedir", "nasıl", "ne işe yarar"

İçerik Yapısı:
- Tanım (ilk paragraf)
- Detaylı açıklama
- Örnekler
- Görselleştirme
- İlgili konular

CTA: "Daha fazla bilgi", "İlgili makaleler"
```

#### 2. **Commercial Investigation** (Araştırma)
```
Anahtar Kelimeler: "en iyi", "karşılaştırma", "inceleme", "vs"

İçerik Yapısı:
- Ürün karşılaştırması
- Avantajlar/dezavantajlar
- Fiyat aralıkları
- Kullanıcı yorumları
- Seçim kriterleri

CTA: "Ürünleri incele", "Fiyat teklifi al"
```

#### 3. **Transactional Intent** (Satın alma)
```
Anahtar Kelimeler: "fiyat", "satın al", "sipariş", "teklif"

İçerik Yapısı:
- Fiyat bilgisi
- Stok durumu
- Teslimat bilgileri
- Garanti/servis
- Satın alma süreci

CTA: "Hemen satın al", "Teklif isteyin", "İletişime geçin"
```

#### 4. **Navigational Intent** (Marka/site arama)
```
Anahtar Kelimeler: "[marka] transpalet", "[şirket] ürünleri"

İçerik Yapısı:
- Marka/şirket tanıtımı
- Ürün kataloğu
- İletişim bilgileri
- Referanslar

CTA: "Kataloğu incele", "İletişim"
```

---

## 📊 İÇERİK KALİTE FAKTÖRLERİ

### Google Helpful Content Update (2024+)

#### ✅ YAP
```
1. USER-FIRST İÇERİK
   - Gerçek soru/probleme cevap ver
   - Pratik bilgi + actionable insights
   - Okuyucuya değer kat

2. ORİJİNAL İÇERİK
   - Unique perspective
   - Kendi deneyimlerimiz
   - Yeni bilgi/veri sun

3. DERINLEMESINE ANALİZ
   - Yüzeysel değil, detaylı
   - Kapsamlı coverage
   - Alt konular explore et

4. GÜNCEL İÇERİK
   - 2025 verileri kullan
   - Eski bilgileri update et
   - Tarih belirt

5. UZMANCA YAZILMIŞ
   - Sektör terminolojisi doğru
   - Teknik doğruluk
   - Referanslar güvenilir
```

#### ❌ YAPMA
```
1. AI-GENERATED İÇERİK (Belli edilmemeli)
   - Çok generic ifadeler
   - "In today's digital world..." clicheleri
   - Yüzeysel genel bilgiler

2. KEYWORD STUFFING
   - Doğal olmayan tekrarlar
   - Her yere keyword sıkıştırma

3. THIN CONTENT
   - <1000 kelime (bilgilendirme için yetersiz)
   - Değer katmayan içerik

4. DUPLICATE CONTENT
   - Başka sitelerden kopya
   - Kendi sayfalardan tekrar (canonicalize)

5. CLICKBAIT
   - Yanıltıcı başlıklar
   - İçerikte olmayan şeyler başlıkta
```

---

## 🔗 LINK STRATEJİSİ

### Internal Linking (Dahili Bağlantı)

```html
✅ DOĞRU:
<a href="/kategori/transpalet" class="text-blue-600 hover:underline">
  transpalet modelleri
</a>

Kurallar:
- Anchor text = hedef sayfanın keyword'ü
- Contextual link (cümle içinde doğal)
- 8-12 adet internal link/blog
- Orphan page bırakma (her sayfa linkli)
- Link depth: max 3 click
```

### External Linking (Dış Bağlantı)

```html
✅ DOĞRU:
<a href="https://guvenilir-kaynak.com"
   class="text-blue-600 hover:underline"
   target="_blank"
   rel="nofollow noopener">
  ISO 3691-1 standardı
</a>

Kurallar:
- Güvenilir kaynaklara link (gov, edu, authority sites)
- 3-5 adet external link/blog
- rel="nofollow" (PageRank loss önle)
- rel="noopener" (security)
- target="_blank" (yeni sekme)
```

---

## 🖼️ GÖRSEL OPTİMİZASYONU

### 2025 Görsel SEO Kuralları

```html
✅ DOĞRU KULLANIM:
<img
  src="https://cdn.ixtif.com/transpalet-manual.webp"
  alt="Manuel transpalet kullanımı - 2.5 ton kapasiteli model"
  width="800"
  height="600"
  loading="lazy"
  decoding="async"
  class="rounded-lg shadow-md"
/>

Kurallar:
1. Format: WebP (fallback: JPG)
2. Alt text: descriptive + keyword
3. File name: keyword-based (transpalet-manual.webp)
4. Dimensions: width + height (CLS önle)
5. Loading: lazy (below fold)
6. Decoding: async
7. File size: <200KB
8. CDN: kullan (Thumbmaker servisi)
```

### Alt Text Formülü

```
[Ana Nesne] + [Özellik/Durum] + [Context]

Örnekler:
✅ "Manuel transpalet kullanımı - depo içi palet taşıma"
✅ "2.5 ton elektrikli transpalet - teknik özellikleri"
✅ "Transpalet güvenlik kuralları infografiği"

❌ "resim1"
❌ "transpalet" (çok generic)
❌ "transpalet transpalet manuel transpalet" (stuffing)
```

---

## 📈 PERFORMANS TAKİP METRİKLERİ

### Blog Başarı KPI'ları

```
1. ORGANIC TRAFFIC
   Hedef: %200+ artış (6 ay)
   Ölçüm: Google Analytics 4

2. KEYWORD RANKINGS
   Hedef: Ana keyword Top 5
   Ölçüm: Google Search Console

3. CTR (Click-Through Rate)
   Hedef: >5% (industry avg: 3%)
   Ölçüm: GSC

4. DWELL TIME
   Hedef: >2 dakika
   Ölçüm: GA4

5. BOUNCE RATE
   Hedef: <60%
   Ölçüm: GA4

6. SCROLL DEPTH
   Hedef: >75%
   Ölçüm: GA4 (custom event)

7. FEATURED SNIPPETS
   Hedef: En az 1 snippet/blog
   Ölçüm: Manual check + GSC

8. BACKLINKS
   Hedef: 10+ quality backlinks (6 ay)
   Ölçüm: Ahrefs / SEMrush
```

---

## 🛠️ SEO ARAÇLARI VE KONTROL

### Zorunlu Testler (Yayın Öncesi)

```bash
1. Google Rich Results Test
   https://search.google.com/test/rich-results
   → Schema markup validation

2. PageSpeed Insights
   https://pagespeed.web.dev/
   → Core Web Vitals score

3. Mobile-Friendly Test
   https://search.google.com/test/mobile-friendly
   → Mobil uyumluluk

4. Structured Data Testing Tool
   https://validator.schema.org/
   → JSON-LD validation

5. GTmetrix
   https://gtmetrix.com/
   → Detaylı performans analizi
```

### Hedef Skorlar

```
PageSpeed Insights:
- Mobile: >90
- Desktop: >95

GTmetrix:
- Performance: A (>90%)
- Structure: A (>90%)
- LCP: <2.5s
- CLS: <0.1

Google Search Console:
- Core Web Vitals: "Good" URL'ler >75%
```

---

## 📋 SEO CHECKLIST (Blog Yayın Öncesi)

```markdown
## TEMEL SEO
- [ ] Title tag: 50-60 karakter
- [ ] Meta description: 155-160 karakter
- [ ] URL slug: keyword-rich, kısa
- [ ] H1: 1 adet, focus keyword içerir
- [ ] H2: 6-8 adet, keyword variants
- [ ] H3: 10-15 adet

## İÇERİK
- [ ] Kelime sayısı: 2000-2500
- [ ] Focus keyword density: %1-1.5
- [ ] LSI keywords: 10+
- [ ] Giriş paragrafı: featured snippet format
- [ ] SSS: 8-10 soru
- [ ] CTA: 2-3 adet

## GÖRSEL
- [ ] Featured image: 1200x675, WebP, <200KB
- [ ] Inline images: 5-8 adet, optimized
- [ ] Alt text: tüm görsellerde
- [ ] Width/height: tanımlı

## LİNKLER
- [ ] Internal links: 8-12 adet
- [ ] External links: 3-5 adet (authority)
- [ ] Anchor text: optimize

## SCHEMA MARKUP
- [ ] Article schema
- [ ] FAQPage schema
- [ ] BreadcrumbList schema
- [ ] @graph formatında birleştirilmiş

## TEKNİK
- [ ] HTTPS: ✅
- [ ] Canonical URL: doğru
- [ ] Robots meta: index, follow
- [ ] OpenGraph tags: tam
- [ ] Twitter Card: tam
- [ ] Mobile responsive: test edildi
- [ ] Core Web Vitals: yeşil

## E-E-A-T
- [ ] Author/Organization bilgisi
- [ ] Yayın + güncelleme tarihi
- [ ] Kaynak linkleri (güvenilir)
- [ ] İletişim bilgileri ulaşılabilir

## YAYINDAN SONRA
- [ ] Google Search Console: URL gönder
- [ ] XML Sitemap: update edildi
- [ ] Social media: paylaş
- [ ] Internal link: eski bloglara ekle
```

---

**Son Güncelleme:** 2025-11-14
**Versiyon:** 1.0-SEO2025
**Kaynak:** Google Algorithm Updates 2024-2025
