# 📝 Blog Prompt Sistemi - Endüstriyel Ürün Satışı

> **Türkiye pazarında endüstriyel ürün satışı için SEO-optimize blog içerikleri oluşturma prompt sistemi**

---

## 📚 İçindekiler

1. [Genel Bakış](#genel-bakış)
2. [Sistem Yapısı](#sistem-yapısı)
3. [Kullanım Adımları](#kullanım-adımları)
4. [Prompt Detayları](#prompt-detayları)
5. [Örnek Senaryo](#örnek-senaryo)
6. [En İyi Uygulamalar](#en-iyi-uygulamalar)

---

## 🎯 Genel Bakış

Bu sistem, **endüstriyel ürün satışı** yapan e-ticaret siteleri için SEO-optimize blog içerikleri oluşturmaya yönelik 2 aşamalı bir prompt sistemidir:

### Hedef Okur Profili
- **Yaş**: 25-65
- **Rol**: B2B kullanıcılar (satın alma müdürleri, depo yöneticileri, lojistik sorumlular, teknik ekipler)
- **Arama Amacı**:
  - Ürün/ekipman teknik özellikleri ve karşılaştırma
  - Kullanım alanları, avantajlar, maliyet-fayda analizi
  - Güvenlik standartları, bakım gereksinimleri
  - Tedarikçi güvenilirliği ve profesyonel destek

### SEO Hedefleri
- **Site DA**: ~25
- **Hedef**: 90 gün içinde ana anahtar kelimelerde ilk 5 sırada
- **İçerik Uzunluğu**: ~2.000 kelime
- **Yapı**: H2/H3 başlıklar + FAQ (şema-uyumlu)

---

## 🗂️ Sistem Yapısı

```
readme/blog-prompt/
├── README.md                      # Bu dosya (kullanım kılavuzu)
├── 1-blog-taslak-olusturma.md     # İlk aşama: Blog anahattı oluşturma
├── 2-blog-yazdirma.md             # İkinci aşama: Blog yazma
└── 3-schema-seo-checklist.md      # Schema.org ve SEO kontrol listesi
```

### Dosya Açıklamaları

**1-blog-taslak-olusturma.md**
- Blog anahattı (outline) oluşturma promptu
- SEO meta bilgileri (title, description, slug)
- Schema.org yapılandırılmış veri planlaması
- Dahili bağlantı stratejisi
- Görsel & medya planı

**2-blog-yazdirma.md**
- Bölüm-bölüm blog içeriği yazma promptu
- SEO optimizasyonu (anahtar kelime yoğunluğu, LSI terimleri)
- Featured snippet optimizasyonu
- Schema markup uyumlu içerik yapısı

**3-schema-seo-checklist.md**
- Schema.org validation checklist
- On-page SEO kontrolleri (title, meta, H1-H3)
- İçerik kalitesi kriterleri
- Teknik SEO kontrolleri
- Test araçları ve metrikler

---

## 🚀 Kullanım Adımları

### Adım 1: Hazırlık

**Gerekli Materyaller:**
- ✅ **Ana anahtar kelime** (örn: "transpalet nedir")
- ✅ **Destek kelimeler** (Excel dosyası - öncelik sütunlu)
- ✅ **Dahili bağlantı fırsatları** (sitedeki mevcut makaleler)

---

### Adım 2: Blog Taslağı Oluşturma

**Kullanılacak Dosya:** `1-blog-taslak-olusturma.md`

**Prompt'a Eklenecek Bilgiler:**
```
Ana Anahtar Kelime: [kelime buraya]
Makale Konusu: [konu buraya]
Excel Dosyası: [dosya eklenir]
Dahili Bağlantı Fırsatları: [liste/dosya eklenir]
```

**Beklenen Çıktı:**
1. ✅ Blog anahattı (H2/H3 başlıklar + madde işaretleri)
2. ✅ FAQ bloğu (şema-uyumlu Soru-Cevap)
3. ✅ Dahili bağlantı fırsatları listesi

**Örnek:**
```
Prompt: "Ana anahtar kelime: 'transpalet nedir',
         Makale konusu: Transpalet özellikleri ve kullanım alanları"

Çıktı:
H1: Transpalet Nedir? [Detaylı Rehber 2025]
H2: Transpalet Tanımı ve Temel Bilgiler
H3: Transpalet Nasıl Çalışır?
H3: Transpalet Kullanım Alanları
H2: Transpalet Çeşitleri
...
FAQ:
- Transpalet ne kadar ağırlık taşır?
- Manuel ve elektrikli transpalet farkı nedir?
...
```

---

### Adım 3: Blog İçeriği Yazma

**Kullanılacak Dosya:** `2-blog-yazdirma.md`

**Prompt'a Eklenecek Bilgiler:**
```
Blog Anahattı: [1. adımdan gelen çıktı]
Şu Anki Bölüm: [örn: H2 - Transpalet Çeşitleri]
Anahtar Kelimeler: [Excel dosyasından ilgili kelimeler]
```

**Beklenen Çıktı:**
- ✅ Bölüm başlığı + detaylı metin
- ✅ Madde listesi veya tablo (gerekirse)
- ✅ Kaynak referansları (inline link)
- ✅ Dahili bağlantılar (varsa)

**Örnek:**
```
Prompt: "Blog outline'dan H2 - Transpalet Çeşitleri bölümünü yaz"

Çıktı:
## Transpalet Çeşitleri

Endüstriyel kullanım alanlarına göre transpalet çeşitleri farklılık gösterir.
Manuel, elektrikli ve akülü transpalet modelleri...

### Manuel Transpalet
Manuel transpalet, hidrolik sistem ile çalışır...

[Kaynak: ISO 3691-1 Standardı]
[Daha fazla bilgi için: Manuel Transpalet Kullanım Kılavuzu sayfasını ziyaret edin]
```

---

## 📖 Prompt Detayları

### 1. Blog Taslağı Oluşturma Promptu

**Özellikler:**
- ✅ 2.000 kelimelik içerik anahattı
- ✅ H2/H3 hiyerarşik yapı
- ✅ Semantik SEO (LSI kelimeleri)
- ✅ FAQ bloğu (şema-uyumlu)
- ✅ Dahili bağlantı fırsatları

**Kurallar:**
- Cümle ≤ 20 kelime
- Profesyonel, teknik ton
- Marka adı yok (context gerektirmedikçe)
- Her bölüm sonunda otorite kaynak

---

### 2. Blog Yazma Promptu

**Özellikler:**
- ✅ Bölüm-bölüm yazma (iteratif)
- ✅ Teknik detaylı, profesyonel üslup
- ✅ Kaynak referansları (endüstri standartları, teknik dökümanlar)
- ✅ Dahili bağlantılar (inline)
- ✅ Featured snippet optimizasyonu (liste, tablo, tanım)
- ✅ Schema markup uyumlu içerik

**Kurallar:**
- İlk 100 kelimede ana anahtar kelime
- Eş anlamlı/LSI kullanımı
- Uzun kuyruklu anahtar kelimeler
- Teknik kaynak zorunlu (ISO, CE, TSE, üretici dökümanları)
- Keyword density: %1-2

---

### 3. Schema & SEO Checklist

**Kapsam:**
- ✅ Schema.org yapılandırılmış veriler (Article, FAQPage, Product, HowTo, BreadcrumbList)
- ✅ On-page SEO kontrolleri (title, meta, URL, başlıklar)
- ✅ İçerik kalitesi metrikleri (okunabilirlik, E-A-T)
- ✅ Teknik SEO (mobil uyumluluk, sayfa hızı, canonical)
- ✅ Test araçları (Google Rich Results Test, PageSpeed Insights)

**Kullanım:**
- İçerik yayınlanmadan önce tüm maddeleri kontrol et
- Schema validation araçlarıyla test et
- SEO skorunu ölç ve optimize et

---

## 💡 Örnek Senaryo

### Senaryo: "Transpalet Nedir?" Makalesi

**1. Hazırlık:**
```
Ana Anahtar Kelime: "transpalet nedir"
Destek Kelimeler: manuel transpalet, elektrikli transpalet, akülü transpalet,
                   hidrolik transpalet, transpalet özellikleri, transpalet fiyatları
Dahili Bağlantı: - "Forklift Nedir?" makalesi
                 - "Depo Ekipmanları" kategorisi
```

**2. Taslak Oluşturma:**
```
[1-blog-taslak-olusturma.md prompt'unu kullan]

Çıktı:
H1: Transpalet Nedir? [2025 Detaylı Rehber]
H2: Transpalet Tanımı ve Temel Bilgiler
  H3: Transpalet Nasıl Çalışır?
  H3: Transpalet Kullanım Alanları
H2: Transpalet Çeşitleri
  H3: Manuel Transpalet
  H3: Elektrikli Transpalet
  H3: Akülü Transpalet
H2: Transpalet Teknik Özellikleri
H2: Transpalet Seçim Kriterleri
H2: Transpalet Güvenlik Standartları
FAQ: 10 soru-cevap
```

**3. İçerik Yazma:**
```
[2-blog-yazdirma.md prompt'unu kullan - her bölüm için]

Bölüm 1: H2 - Transpalet Tanımı ve Temel Bilgiler
Bölüm 2: H2 - Transpalet Çeşitleri
Bölüm 3: H2 - Transpalet Teknik Özellikleri
...
```

**4. Yayın Öncesi Kontrol:**
```
[3-schema-seo-checklist.md kontrol listesini kullan]

✓ Schema.org validation (Article, FAQPage, BreadcrumbList)
✓ Title tag optimize edilmiş (50-60 karakter)
✓ Meta description optimize edilmiş (155-160 karakter)
✓ Tüm görsellerde alt text var
✓ Minimum 5 dahili bağlantı
✓ Minimum 3 otorite dış kaynak
✓ Google Rich Results Test → Hata yok
✓ PageSpeed Insights → Score >80
```

---

## ✅ En İyi Uygulamalar

### Anahtar Kelime Optimizasyonu
- ✅ Ana anahtar kelimeyi H1 ve ilk 100 kelimede kullan
- ✅ Destek kelimeleri başlıklara dağıt
- ✅ Eş anlamlı/LSI terimleri kullan
- ✅ Uzun kuyruklu anahtar kelimeleri dahil et

### Teknik İçerik
- ✅ Endüstri standartlarına atıf yap (ISO, CE, TSE)
- ✅ Üretici teknik dökümanlarını kaynak göster
- ✅ Karşılaştırma tabloları kullan
- ✅ Teknik spesifikasyonları madde listesi ile sun

### SEO ve Kullanıcı Deneyimi
- ✅ FAQ bloğunu şema-uyumlu yaz (FAQPage Schema)
- ✅ Dahili bağlantıları stratejik kullan (semantik anchor text)
- ✅ Cümleleri kısa tut (≤20 kelime)
- ✅ Profesyonel, objektif ton kullan
- ✅ Featured snippet hedefle (tanım, liste, tablo)

### Schema.org Yapılandırılmış Veriler
- ✅ **Article Schema**: Her blog içeriğinde zorunlu
- ✅ **FAQPage Schema**: FAQ bölümü için zorunlu
- ✅ **BreadcrumbList Schema**: Kategori hiyerarşisi için
- ✅ **Product Schema**: Ürün içeriklerinde (teknik özellikler, fiyat)
- ✅ **HowTo Schema**: Kullanım rehberi içeriklerinde (adım-adım)
- ✅ Google Rich Results Test ile validation yap

### Kaynak Kullanımı
- ✅ Her bölüm sonunda 1-2 otorite kaynak
- ✅ Direkt makale/döküman linkine git
- ✅ Güncel kaynakları tercih et
- ✅ Resmi kurumları öncele

---

## ⚠️ Dikkat Edilmesi Gerekenler

### YAPMA ❌
- ❌ Marka adı kullanma (context gerektirmedikçe)
- ❌ Gündelik dil, argo, benzetme
- ❌ Görüş bildirme, subjektif ifadeler
- ❌ Uzun cümleler (>20 kelime)
- ❌ Gereksiz dolgu kelimeler

### YAP ✅
- ✅ Teknik, profesyonel üslup
- ✅ Kanıt-referans göster
- ✅ Sade, kesin ifadeler
- ✅ B2B kullanıcı odaklı yaz
- ✅ Pratik bilgi sun

---

## 📊 Başarı Metrikleri

### SEO Hedefler
- 🎯 İlk 100 kelimede ana anahtar kelime
- 🎯 H2/H3 başlıklarda destek kelimeler
- 🎯 Minimum 5 dahili bağlantı
- 🎯 Minimum 10 FAQ sorusu (şema-uyumlu)
- 🎯 Her bölümde en az 1 otorite kaynak

### İçerik Kalite
- 📝 ~2.000 kelime total uzunluk
- 📝 Cümle ortalama ≤20 kelime
- 📝 Paragraf yapısı net
- 📝 Madde/tablo kullanımı
- 📝 Teknik terimler açıklamalı

---

## 📞 Destek ve Güncellemeler

**Dosya Konumu:** `/Users/nurullah/Desktop/cms/laravel/readme/blog-prompt/`

**Güncelleme Tarihi:** 6 Kasım 2025

**Not:** Bu sistem, ixtif.com (Tenant ID: 2) üzerinde endüstriyel ürün satışı için optimize edilmiştir.

---

## 🔗 İlgili Dökümanlar

- `CLAUDE.md` - Ana çalışma talimatları
- `readme/thumbmaker/` - Görsel optimizasyonu
- `readme/tenant-olusturma.md` - Tenant yönetimi

---

## 🧪 Test & Validation Araçları

### Schema.org Validation
- 🔗 [Google Rich Results Test](https://search.google.com/test/rich-results) - Schema markup test
- 🔗 [Schema.org Validator](https://validator.schema.org/) - Schema syntax validation
- 🔗 [Google Search Console](https://search.google.com/search-console) - Index durumu, mobile usability

### SEO Audit
- 🔗 [PageSpeed Insights](https://pagespeed.web.dev/) - Sayfa hızı ve Core Web Vitals
- 🔗 [GTmetrix](https://gtmetrix.com/) - Performance analizi
- 🔗 [Screaming Frog SEO Spider](https://www.screamingfrogseoseo.com/) - Site crawl, SEO audit

### Okunabilirlik & İçerik
- 🔗 [Hemingway Editor](http://www.hemingwayapp.com/) - Okunabilirlik skoru
- 🔗 [Grammarly](https://www.grammarly.com/) - Grammar & style check
- 🔗 [Yoast SEO](https://yoast.com/) - WordPress SEO plugin (SEO score, readability)

---

## 📊 SEO Başarı Metrikleri

### 90 Gün Hedefler
- 🎯 **Organic Traffic**: %200 artış
- 🎯 **Anahtar Kelime Sıralaması**:
  - Ana anahtar kelime: Top 5
  - Destek anahtar kelimeler: Top 10
- 🎯 **Featured Snippet**: Minimum 1 snippet kazanımı
- 🎯 **CTR**: >5% (organic search)
- 🎯 **Engagement**:
  - Bounce rate: <60%
  - Avg. session duration: >2 dakika
  - Pages per session: >2

### Takip Araçları
- **Google Analytics**: Traffic, engagement, conversion
- **Google Search Console**: Anahtar kelime sıralaması, CTR, impression
- **Ahrefs/SEMrush**: Backlink, domain authority, competitor analysis

---

**Hazırlayan:** Claude AI + Nurullah
**Hedef:** Türkiye pazarında endüstriyel ürün satışı için SEO-optimize blog içerikleri
**Güncelleme Tarihi:** 6 Kasım 2025
