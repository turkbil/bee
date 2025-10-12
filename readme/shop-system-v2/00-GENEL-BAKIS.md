# 🎯 SHOP SİSTEMİ V2 - GENEL BAKIŞ

## 📋 AMAÇ

Bu sistem **iki temel ihtiyacı** karşılar:

### 1️⃣ **PRODUCTS SAYFASI**
- Ürün listeleme
- Filtreleme (voltaj, kapasite, vb.)
- Kategori bazlı görüntüleme
- Detaylı ürün bilgileri

### 2️⃣ **LANDING PAGE**
- Her ürüne özel landing sayfası
- Marketing içerik (slogan, motto, use cases)
- FAQ bölümü
- Teknik özellikler
- CTA butonları (teklif al, iletişim)

---

## 🏗️ MİMARİ YAKLASIM: **HYBRID**

### ✅ **ANA ÜRÜN** → Monolithic (shop_products tablosu)
**NEDEN?**
- Landing page için zengin içerik gerekli
- Marketing metinleri normalize edilemez
- Her ürünün kendine özel hikayesi var

**İÇİNDE NE VAR?**
```json
{
  "title": "Ürün Adı",
  "long_description": "Marketing intro + body",
  "features": {
    "list": ["Özellik 1", "Özellik 2"],
    "branding": {
      "slogan": "Satış sloganı",
      "motto": "Kısa motto"
    }
  },
  "use_cases": ["Kullanım alanı 1", "..."],
  "competitive_advantages": ["Avantaj 1", "..."],
  "target_industries": ["Sektör 1", "..."],
  "technical_specs": {
    "capacity": {"value": 2000, "unit": "kg"},
    "electrical": {"voltage": 48}
  }
}
```

---

### ✅ **FAQ SİSTEMİ** → **shop_products tablosunda JSON**

**KARAR: FAQ'lar ayrı tablo DEĞİL, ürün içinde JSON!**

**NEDEN?**
- ✅ Her ürünün FAQ'ları kendine özel
- ✅ Landing page yüklerken tek query yeterli
- ✅ AI ile toplu güncelleme kolay
- ✅ Çoklu dil desteği JSON'da hazır

**YAPI:**
```json
{
  "faq_data": [
    {
      "question": "F4 201 bir vardiyada kaç saat çalışır?",
      "answer": "Standart 2 modül ile 6 saate kadar...",
      "sort_order": 1
    }
  ]
}
```

**❌ AYRI TABLO NEDEN KULLANMIYORUZ?**
- Müşteri yorumları değil, ürün FAQ'ı → normalize etmeye gerek yok
- Landing page için JOIN sorgusu gereksiz yük
- FAQ'lar ürünle birlikte versiyonlanmalı

---

### ✅ **CATEGORY SPECS** → Kategori bazlı sabit özellikler

**AMAÇ:** Her kategori için standart özellikleri tanımla

**ÖRNEK:**

#### **TRANSPALET KATEGORİSİ**
```php
'primary_specs_template' => [
    ['label' => 'Denge Tekeri', 'field' => 'stabilizer'],
    ['label' => 'Li-Ion Akü', 'field' => 'battery'],
    ['label' => 'Şarj Cihazı', 'field' => 'charger'],
    ['label' => 'Standart Çatal', 'field' => 'fork']
]
```

#### **FORKLIFT KATEGORİSİ**
```php
'primary_specs_template' => [
    ['label' => 'Asansör', 'field' => 'lift_height'],
    ['label' => 'Li-Ion Akü', 'field' => 'battery'],
    ['label' => 'Şarj Cihazı', 'field' => 'charger'],
    ['label' => 'Raf Aralığı', 'field' => 'aisle_width']
]
```

#### **İSTİF MAKİNESİ KATEGORİSİ**
```php
'primary_specs_template' => [
    ['label' => 'Asansör', 'field' => 'lift_height'],
    ['label' => 'Akü', 'field' => 'battery'],
    ['label' => 'Şarj Cihazı', 'field' => 'charger'],
    ['label' => 'Çatal', 'field' => 'fork']
]
```

---

### ✅ **ATTRIBUTES** → Filtreleme için normalize tablo

**AMAÇ:** Products sayfasında hızlı filtreleme

**ÖRNEK SORGU:**
```sql
-- "48V Li-Ion, 2 ton kapasiteli transpaletler" filtresi
SELECT p.*
FROM shop_products p
INNER JOIN shop_product_attributes pa1 ON p.product_id = pa1.product_id
INNER JOIN shop_product_attributes pa2 ON p.product_id = pa2.product_id
WHERE pa1.attribute_id = 2 AND pa1.value_numeric = 48  -- Voltaj
  AND pa2.attribute_id = 1 AND pa2.value_numeric = 2000 -- Kapasite
```

---

### ✅ **VARIANTS** → Fiziksel farklılıklar

**NE ZAMAN KULLAN?**
- ✅ Farklı çatal uzunlukları (900mm, 1150mm, 1500mm)
- ✅ Farklı batarya paketleri (2x, 4x)
- ✅ Fiyat farkı olan değişiklikler

**NE ZAMAN KULLANMA?**
- ❌ Sadece renk farkı
- ❌ Küçük aksesuar değişiklikleri

---

## 📊 VERİ AKIŞI

```
┌──────────────────┐
│   EP PDF'LER     │
│  (Klasörlerde)   │
└────────┬─────────┘
         │
         │ AI Dönüşüm
         ▼
┌──────────────────┐
│  JSON EXTRACTS   │
│  (Ürün başına 1) │
└────────┬─────────┘
         │
         │ ShopProductMasterSeeder
         ▼
┌──────────────────────────────────────┐
│        DATABASE                       │
│  ┌────────────────────────────────┐  │
│  │  shop_products                 │  │
│  │  - Rich content (JSON)         │  │
│  │  - faq_data (JSON)            │  │
│  │  - primary_specs (JSON)       │  │
│  └────────┬────────────┬──────────┘  │
│           │            │              │
│  ┌────────▼─────┐  ┌──▼────────────┐│
│  │  VARIANTS    │  │  ATTRIBUTES   ││
│  │  (Fiziksel)  │  │  (Filtreleme) ││
│  └──────────────┘  └───────────────┘│
└──────────────────────────────────────┘
         │
         ▼
┌──────────────────────────────────────┐
│         FRONTEND                      │
│  ┌───────────┐  ┌─────────────────┐ │
│  │ PRODUCTS  │  │ LANDING PAGE    │ │
│  │ PAGE      │  │ (Ürün bazlı)    │ │
│  │           │  │ - Marketing     │ │
│  │ - Liste   │  │ - FAQ           │ │
│  │ - Filtre  │  │ - Specs         │ │
│  │ - Detay   │  │ - CTA           │ │
│  └───────────┘  └─────────────────┘ │
└──────────────────────────────────────┘
```

---

## 📁 KLASÖR YAPISI

```
readme/shop-system-v2/
├── 00-GENEL-BAKIS.md              ← (Bu dosya)
├── 01-KATEGORI-SPECS.md           ← Her kategorinin sabit özellikleri
├── 02-FAQ-SISTEMI.md              ← FAQ yapısı ve kuralları
├── 03-AI-KURALLARI.md             ← PDF→JSON dönüşüm kuralları (ESKİ KURALLARIN)
├── 04-JSON-SABLONU.md             ← Standart JSON şablonu
├── 05-SEEDER-KURULUM.md           ← Database kurulum adımları
└── 06-LANDING-PAGE-YAPISI.md     ← Frontend şablonu
```

---

## ✅ ESKİ KURALLARIN (Korunacak)

### 📝 **İçerik Kuralları**
1. ✅ Tüm metinler %100 Türkçe (en alanı da Türkçe kopya)
2. ✅ Marketing-intro + marketing-body yapısı
3. ✅ features.branding: slogan, motto, technical_summary
4. ✅ use_cases: en az 6 senaryo
5. ✅ competitive_advantages: en az 5 avantaj
6. ✅ target_industries: en az 20 sektör
7. ✅ faq_data: en az 10 soru-cevap
8. ✅ primary_specs: 4 kart (kategori bazlı)

### 📞 **İletişim Bilgileri**
- Telefon: `0216 755 3 555`
- E-posta: `info@ixtif.com`

### 🏷️ **Marka Mesajları**
- İXTİF'in **ikinci el, kiralık, yedek parça ve teknik servis** programlarına mutlaka değin
- Son kullanıcı odaklı anlat (B2B detaylardan kaçın)

### 🔑 **SEO Anahtar Kelimeleri**
- Ürün bazlı: "F4 201 transpalet, 48V Li-Ion transpalet, vs."
- Kategori bazlı: "dar koridor transpalet, 2 ton akülü transpalet"

---

## 🚀 HIZLI BAŞLANGIÇ

```bash
# 1. Kategorileri oluştur (sabit spec'lerle birlikte)
php artisan db:seed --class=ShopCategoryWithSpecsSeeder

# 2. Attribute'ları oluştur
php artisan db:seed --class=ShopAttributeSeeder

# 3. Bir JSON örneği oluştur (test için)
# → readme/shop-system-v2/04-JSON-SABLONU.md'den kopyala

# 4. Tüm ürünleri yükle
php artisan db:seed --class=ShopProductMasterSeeder

# 5. Kontrol et
php artisan tinker
>>> \DB::table('shop_products')->count()
>>> \DB::table('shop_product_attributes')->count()
```

---

## 🎯 SONRAKI ADIMLAR

1. ✅ Kategori spec'lerini tanımla → `01-KATEGORI-SPECS.md`
2. ✅ FAQ yapısını detaylandır → `02-FAQ-SISTEMI.md`
3. ✅ Eski AI kurallarını entegre et → `03-AI-KURALLARI.md`
4. ✅ JSON şablonunu hazırla → `04-JSON-SABLONU.md`
5. ✅ Seeder'ları yaz → `05-SEEDER-KURULUM.md`
6. ✅ Landing page yapısını belirle → `06-LANDING-PAGE-YAPISI.md`

**ŞİMDİ BİR SONRAK İ DOSYAYI HAZIRLAMAYA BAŞLIYORUM...**
