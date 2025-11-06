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
└── 2-blog-yazdirma.md             # İkinci aşama: Blog yazma
```

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

**Kurallar:**
- İlk 100 kelimede ana anahtar kelime
- Eş anlamlı/LSI kullanımı
- Uzun kuyruklu anahtar kelimeler
- Teknik kaynak zorunlu (ISO, CE, TSE, üretici dökümanları)

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
- ✅ FAQ bloğunu şema-uyumlu yaz
- ✅ Dahili bağlantıları stratejik kullan
- ✅ Cümleleri kısa tut (≤20 kelime)
- ✅ Profesyonel, objektif ton kullan

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

**Hazırlayan:** Claude AI + Nurullah
**Hedef:** Türkiye pazarında endüstriyel ürün satışı için SEO-optimize blog içerikleri
