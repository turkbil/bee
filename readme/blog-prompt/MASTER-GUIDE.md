# 📚 BLOG İÇERİK ÜRETİM MASTER GUIDE

> **Endüstriyel Ürün Satışı İçin Eksiksiz Blog Sistemi - Referans Dökümanı**

---

## 📑 İÇİNDEKİLER

1. [Anahtar Kelime Araştırması](#anahtar-kelime-araştırması)
2. [Rakip Analizi](#rakip-analizi)
3. [İçerik Brief Hazırlama](#içerik-brief-hazırlama)
4. [SSS (FAQ) Stratejisi](#sss-faq-stratejisi)
5. [Schema Markup Örnekleri](#schema-markup-örnekleri)
6. [Dahili Bağlantı Stratejisi](#dahili-bağlantı-stratejisi)
7. [Görsel Optimizasyonu](#görsel-optimizasyonu)
8. [Performans Metrikleri](#performans-metrikleri)
9. [İçerik Takvimi](#içerik-takvimi)
10. [Hata Yapma Rehberi](#hata-yapma-rehberi)

---

## 🔍 ANAHTAR KELİME ARAŞTIRMASI

### Araştırma Şablonu

```markdown
## Ana Anahtar Kelime
- **Kelime**: [örn: transpalet nedir]
- **Aylık Arama**: [1000-10000]
- **Keyword Difficulty**: [0-100]
- **CPC**: [₺0.50-5.00]
- **Search Intent**: [Informational/Commercial/Transactional]

## Destek Kelimeler (LSI & Long-tail)
| Anahtar Kelime | Aylık Arama | KD | Kullanım Yeri |
|----------------|-------------|-----|---------------|
| manuel transpalet | 500 | 25 | H2 başlık |
| elektrikli transpalet | 800 | 30 | H2 başlık |
| transpalet fiyatları | 2000 | 40 | H3 başlık |
| transpalet özellikleri | 300 | 20 | İçerik |
| 2 ton transpalet | 400 | 35 | Alt başlık |

## Semantic Entities
- Markalar: [Yale, Crown, BT, Linde]
- Standartlar: [ISO 3691-1, CE, TSE]
- Kategoriler: [Manuel, Elektrikli, Akülü]
- Özellikler: [Kapasite, Çatal uzunluğu, Kaldırma yüksekliği]
```

### Anahtar Kelime Seçim Kriterleri

✅ **İDEAL ANAHTAR KELİME:**
- Aylık arama: 500-5000 (orta rekabet)
- Keyword Difficulty: <40
- CPC: >₺1 (ticari değer var)
- Long-tail variant: 3-5 kelime
- Search Intent: Bilgi arama + Satın alma potansiyeli

---

## 🎯 RAKİP ANALİZİ

### Rakip İçerik Analiz Şablonu

```markdown
## Rakip URL: [rakip-site.com/blog/transpalet-nedir]

### İçerik Analizi
- **Kelime Sayısı**: [2500]
- **H2 Başlık Sayısı**: [6]
- **Görsel Sayısı**: [8]
- **Video**: [Var/Yok]
- **İnfografik**: [Var/Yok]

### SEO Analizi
- **Title Tag**: [60 karakter]
- **Meta Description**: [155 karakter]
- **URL Slug**: [transpalet-nedir]
- **Schema Markup**: [Article, FAQ]
- **İç Link Sayısı**: [12]
- **Dış Link Sayısı**: [5]

### Content Gap Analizi
- Eksik konular
- Daha detaylı açıklanabilecek bölümler
- Eklenebilecek görsel/video fırsatları
```

---

## 📝 İÇERİK BRIEF HAZIRLAMA

### İçerik Brief Şablonu

```markdown
# İÇERİK BRIEF

## Genel Bilgiler
- **Başlık**: [Blog başlığı]
- **Ana Anahtar Kelime**: [Focus keyword]
- **Kelime Sayısı Hedefi**: 2000-2500
- **Yayın Tarihi**: [Tarih]

## Hedef Kitle
- **Persona**: Depo yöneticisi, 35-50 yaş, B2B
- **Bilgi Seviyesi**: Orta (temel bilgiye sahip)
- **Arama Amacı**: Satın alma öncesi araştırma
- **Pain Points**:
  - Doğru model seçimi
  - Maliyet-fayda analizi
  - Güvenlik standartları

## İçerik Yapısı
1. **Giriş** (100-150 kelime)
2. **Ana Bölümler** (H2 başlıkları)
3. **SSS** (10 soru)
4. **Sonuç** (100-150 kelime)

## SEO Gereksinimleri
- Title Tag: 50-60 karakter
- Meta Description: 155-160 karakter
- URL Slug: /[slug]
- Focus Keyword Density: %1-1.5
- LSI Keywords: En az 10 farklı varyant
```

---

## ❓ SSS (FAQ) STRATEJİSİ

### Etkili SSS Sorusu Yapısı

1. **Doğrudan soru**: "Transpalet ne kadar yük kaldırır?"
2. **Uzun kuyruk**: "2 ton kapasiteli manuel transpalet fiyatı nedir?"
3. **Karşılaştırma**: "Manuel mi elektrikli transpalet mi daha avantajlı?"
4. **Problem odaklı**: "Transpalet arıza yaparsa ne yapmalı?"
5. **Satın alma**: "İkinci el transpalet alınır mı?"

### SSS Cevap Formatı

```markdown
**Soru**: Transpalet ne kadar yük kaldırır?

**Cevap** (50-100 kelime):
"Standart manuel transpaletler genellikle 2.000-2.500 kg kapasitelidir.
Ancak özel üretim modellerde bu kapasite 5.000 kg'a kadar çıkabilir.
Elektrikli transpalet modelleri ise 1.500-3.000 kg arasında yük kaldırabilir.
Kapasite seçimi, taşınacak paletin ağırlığına göre yapılmalıdır."
```

---

## 🏗️ SCHEMA MARKUP ÖRNEKLERİ

### Article + FAQPage Schema (Kombine)

```json
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "Article",
      "headline": "Transpalet Nedir? Çeşitleri ve Kullanım Alanları",
      "description": "Transpalet nedir, nasıl çalışır? Manuel ve elektrikli transpalet...",
      "image": "https://domain.com/images/transpalet.jpg",
      "datePublished": "2025-11-06T08:00:00+03:00",
      "dateModified": "2025-11-06T10:00:00+03:00",
      "author": {
        "@type": "Organization",
        "name": "Şirket Adı"
      },
      "publisher": {
        "@type": "Organization",
        "name": "Şirket Adı",
        "logo": {
          "@type": "ImageObject",
          "url": "https://domain.com/logo.png"
        }
      }
    },
    {
      "@type": "FAQPage",
      "mainEntity": [
        {
          "@type": "Question",
          "name": "Transpalet ne kadar yük kaldırır?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Standart manuel transpaletler 2.000-2.500 kg kapasitelidir."
          }
        }
      ]
    },
    {
      "@type": "BreadcrumbList",
      "itemListElement": [
        {
          "@type": "ListItem",
          "position": 1,
          "name": "Ana Sayfa",
          "item": "https://domain.com/"
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
  ]
}
```

---

## 🔗 DAHİLİ BAĞLANTI STRATEJİSİ

### Bağlantı Piramidi

```
                    [Ana Sayfa]
                         |
        ┌────────────────┼────────────────┐
        ▼                ▼                ▼
   [Kategori]       [Kategori]       [Kategori]
        |                |                |
    ┌───┼───┐        ┌───┼───┐        ┌───┼───┐
    ▼   ▼   ▼        ▼   ▼   ▼        ▼   ▼   ▼
  [Blog][Blog]     [Blog][Blog]     [Blog][Blog]
```

### Anchor Text Best Practices

✅ **DOĞRU:**
- "transpalet çeşitleri" → /transpalet-cesitleri
- "manuel transpalet özellikleri" → /manuel-transpalet
- "2 ton kapasiteli modeller" → /2-ton-transpalet

❌ **YANLIŞ:**
- "buraya tıklayın" → /transpalet
- "daha fazla bilgi" → /transpalet-rehberi
- "link" → /urun-sayfasi

---

## 🖼️ GÖRSEL OPTİMİZASYONU

### Görsel Gereksinimleri

```markdown
## Blog İçin Görsel Checklist

### 1. Öne Çıkan Görsel (Featured Image)
- Boyut: 1200x675px (16:9)
- Format: WebP (fallback: JPG)
- Dosya boyutu: <200KB
- Alt text: "Transpalet nedir - endüstriyel transpalet çeşitleri"

### 2. İçerik Görselleri
- Boyut: 800x600px veya 600x400px
- Format: WebP
- Dosya boyutu: <150KB
- Her görselde alt text

### 3. İnfografikler
- Boyut: 800x2000px (vertical)
- Format: PNG veya SVG
- Mobil responsive
```

### Alt Text Formülü

```
Formula: [Ana Nesne] + [Özellik] + [Context]

Örnekler:
✅ "Manuel transpalet kullanımı - depo içi palet taşıma"
✅ "2.5 ton elektrikli transpalet - ürün özellikleri"
✅ "Transpalet güvenlik kuralları infografik"
```

---

## 📊 PERFORMANS METRİKLERİ

### KPI Tablosu

| Metrik | Hedef | Ölçüm Aracı |
|--------|-------|-------------|
| Organic Traffic | %200 artış (90 gün) | GA4 |
| Keyword Rankings | Top 5 (ana kelime) | GSC |
| CTR | >5% | GSC |
| Featured Snippet | En az 1 | GSC |
| Backlinks | 10+ (6 ay) | Ahrefs |
| Bounce Rate | <60% | GA4 |
| Time on Page | >2 dakika | GA4 |
| Scroll Depth | >75% | GA4 |

### Aylık Raporlama Şablonu

```markdown
## Aylık Blog Performans Raporu

### Dönem: [Ay/Yıl]

#### Traffic Overview
- Total Views: [sayı]
- Organic Traffic: [sayı] (%değişim)
- Direct Traffic: [sayı]
- Social Traffic: [sayı]

#### Top Performing Posts
1. [Post Title] - [views]
2. [Post Title] - [views]
3. [Post Title] - [views]

#### Keyword Performance
| Keyword | Position | Change | CTR |
|---------|----------|--------|-----|
| transpalet nedir | 3 | ↑2 | 7.2% |
| manuel transpalet | 5 | ↑1 | 5.4% |
```

---

## 📅 İÇERİK TAKVİMİ

### Aylık Plan Şablonu

| Hafta | Pazartesi | Salı | Çarşamba | Perşembe | Cuma |
|-------|-----------|------|----------|----------|------|
| 1 | Blog araştırma | Taslak | Yazım | Edit | Yayın |
| 2 | FAQ güncelleme | - | Blog araştırma | Taslak | Yazım |
| 3 | Edit | Yayın | Schema kontrol | - | Blog araştırma |
| 4 | Taslak | Yazım | Edit | Yayın | Performans raporu |

### İçerik Tipleri
- 🔍 Rehber içerik (2000+ kelime)
- 💡 How-to içerik (1500+ kelime)
- 📊 Karşılaştırma (1500+ kelime)
- ❓ FAQ güncelleme (500+ kelime)
- 📈 Case study (1000+ kelime)

---

## 🚨 HATA YAPMA REHBERİ

### ❌ YAPMAYIN

1. **Keyword Stuffing**: Aşırı anahtar kelime tekrarı
2. **Duplicate Content**: Kopyala-yapıştır içerik
3. **Thin Content**: 500 kelimeden az içerik
4. **Over-optimization**: Doğal olmayan link/keyword kullanımı
5. **Schema Spam**: Yanlış veya yanıltıcı markup
6. **Clickbait**: Yanıltıcı başlıklar
7. **No Alt Text**: Görsellerde alt text eksikliği
8. **Broken Links**: Kırık dahili/harici linkler

### ✅ YAPIN

1. **Özgün İçerik**: Her zaman unique, değerli içerik
2. **User Intent**: Kullanıcı amacına odaklan
3. **E-A-T**: Uzmanlık, Otorite, Güvenilirlik
4. **Mobile-First**: Önce mobil deneyimi düşün
5. **Page Speed**: Sayfa hızını optimize et
6. **Regular Updates**: İçeriği düzenli güncelle
7. **Schema Markup**: Her zaman ekle ve test et
8. **Internal Linking**: Stratejik dahili bağlantılar

---

## 🧪 TEST ARAÇLARI

### Zorunlu Testler
- [Google Rich Results Test](https://search.google.com/test/rich-results) - Schema validation
- [PageSpeed Insights](https://pagespeed.web.dev/) - Performans
- [Mobile-Friendly Test](https://search.google.com/test/mobile-friendly) - Mobil uyumluluk

### Önerilen Araçlar
- [GTmetrix](https://gtmetrix.com/) - Detaylı performans
- [Screaming Frog](https://www.screamingfrog.co.uk/) - SEO audit
- [Ahrefs](https://ahrefs.com/) - Backlink & keyword analiz
- [SEMrush](https://www.semrush.com/) - Rakip analizi

---

## 📞 DESTEK

**Dosya Konumu:** `/Users/nurullah/Desktop/cms/laravel/readme/blog-prompt/`
**Son Güncelleme:** 6 Kasım 2025
**Platform:** Laravel Multi-tenant E-commerce

---

**✨ İpucu:** Detaylı bilgi için ilgili bölümleri inceleyin. Hızlı başlangıç için `BLOG-YAZDIRMA-AKISI.md` dosyasını kullanın.

---

*Bu döküman, endüstriyel ürün satışı için blog içerik üretim sisteminin detaylı referans kılavuzudur.*