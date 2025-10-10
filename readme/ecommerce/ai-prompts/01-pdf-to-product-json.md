# 🤖 AI PROMPT: PDF'den SHOP Product JSON'a Dönüştürme (Phase 1)

## 🎯 AMAÇ

Ürün broşür PDF'lerini analiz edip, **Phase 1 SHOP modülü** için uygun JSON formatına dönüştürmek.

**ÖNEMLİ:**
- Bir PDF'de birden fazla ürün olabilir
- Phase 1 standardizasyonuna uygun format
- `name` → `title`, JSON slug, SEO kaldırıldı

---

## 📋 INPUT

**PDF Dosyası:**
- Ürün broşürleri
- Teknik özellikler tabloları
- Ürün görselleri ve açıklamaları
- Özellik listeleri

**PDF Türleri:**
1. **Tek Ürün PDF:** Tek bir ürün modeli
2. **Çoklu Ürün PDF:** Aynı seride birden fazla model

---

## 📤 OUTPUT FORMAT (Phase 1)

### **Her Ürün İçin Ayrı JSON**

```json
{
  "product_info": {
    "sku": "PROD-001",
    "model_number": "MODEL-001",
    "series_name": "Product Series",
    "product_type": "physical",
    "condition": "new"
  },

  "basic_data": {
    "title": {
      "tr": "Ürün Başlığı",
      "en": "Ürün Başlığı",
      "vs.": "..."
    },
    "slug": {
      "tr": "urun-basligi",
      "en": "urun-basligi",
      "vs.": "..."
    },
    "short_description": {
      "tr": "Kısa açıklama buraya gelir",
      "en": "Kısa açıklama buraya gelir",
      "vs.": "..."
    },
    "long_description": {
      "tr": "<section class=\"marketing-intro\">İkna edici, satış odaklı açılış paragrafı...</section><section class=\"marketing-body\">Devamı...</section>",
      "en": "<section class=\"marketing-intro\">İkna edici, satış odaklı açılış paragrafı...</section><section class=\"marketing-body\">Devamı...</section>",
      "vs.": "..."
    }
  },

  "category_brand": {
    "category_id": 11,
    "brand_id": 1,
    "brand_name": "Marka Adı",
    "manufacturer": "Üretici Firma Adı"
  },

  "pricing": {
    "base_price": null,
    "currency": "TRY",
    "price_on_request": true,
    "installment_available": true,
    "deposit_required": true,
    "deposit_percentage": 30
  },

  "stock_info": {
    "stock_tracking": true,
    "stock_quantity": 0,
    "lead_time_days": 60,
    "availability": "on_order",
    "warranty_months": 24
  },

  "physical_properties": {
    "weight": 2950,
    "dimensions": {
      "length": 2733,
      "width": 1070,
      "height": 2075,
      "unit": "mm"
    },
    "service_weight": 2950
  },

  "technical_specs": {
    "capacity": {
      "load_capacity": {"value": 1500, "unit": "kg"},
      "load_center_distance": {"value": 500, "unit": "mm"}
    },
    "performance": {
      "speed": {"value": 13, "unit": "km/h"},
      "max_performance": {"value": 15, "unit": "%"}
    },
    "electrical": {
      "voltage": {"value": 80, "unit": "V"},
      "capacity": {"value": 150, "unit": "Ah"},
      "type": "Li-Ion",
      "rating": {"value": 5.0, "unit": "kW", "note": "2x5.0kW dual motors"}
    },
    "dimensions_detail": {
      "length": {"value": 1813, "unit": "mm"},
      "width": {"value": 1070, "unit": "mm"},
      "turning_radius": {"value": 1450, "unit": "mm"}
    }
  },

  "features": {
    "tr": {
      "list": [
        "Özellik 1",
        "Özellik 2",
        "Özellik 3",
        "vs."
      ],
      "branding": {
        "slogan": "Satışa teşvik eden slogan",
        "motto": "Kısa motto",
        "technical_summary": "Teknik açıdan güçlü özet"
      }
    },
    "en": {
      "list": [
        "Özellik 1",
        "Özellik 2",
        "Özellik 3",
        "vs."
      ],
      "branding": {
        "slogan": "Satışa teşvik eden slogan",
        "motto": "Kısa motto",
        "technical_summary": "Teknik açıdan güçlü özet"
      }
    },
    "vs.": "..."
  },

  "highlighted_features": [
    {
      "icon": "battery-charging",
      "priority": 1,
      "category": "power",
      "title": {"tr": "Güçlü Teknoloji", "en": "Güçlü Teknoloji", "vs.": "..."},
      "description": {"tr": "Açıklama", "en": "Açıklama", "vs.": "..."}
    }
  ],

  "use_cases": {
    "tr": [
      "Kullanım alanı 1",
      "Kullanım alanı 2",
      "vs."
    ],
    "en": [
      "Kullanım alanı 1",
      "Kullanım alanı 2",
      "vs."
    ],
    "vs.": ["..."]
  },

  "competitive_advantages": {
    "tr": [
      "Rekabet avantajı 1",
      "Rekabet avantajı 2",
      "vs."
    ],
    "en": [
      "Rekabet avantajı 1",
      "Rekabet avantajı 2",
      "vs."
    ],
    "vs.": ["..."]
  },

  "target_industries": {
    "tr": ["Sektör 1", "Sektör 2", "Sektör 3", "... (toplam 20+ sektör)"],
    "en": ["Sektör 1", "Sektör 2", "Sektör 3", "... (toplam 20+ sektör)"],
    "vs.": ["..."]
  },

  "primary_specs": [
    {"label": "Başlık 1", "value": "Değer 1"},
    {"label": "Başlık 2", "value": "Değer 2"},
    {"label": "Başlık 3", "value": "Değer 3"},
    {"label": "Başlık 4", "value": "Değer 4"}
  ],

  "faq_data": [
    {
      "question": {"tr": "Soru 1?", "en": "Soru 1?", "vs.": "..."},
      "answer": {"tr": "Detaylı cevap 1", "en": "Detaylı cevap 1", "vs.": "..."}
    },
    "... en az 10 kayıt"
  ],

  "variants": [
    {
      "sku": "PROD-001-V1",
      "title": {"tr": "Varyant 1", "en": "Varyant 1", "vs.": "..."},
      "option_values": {
        "size": "Büyük",
        "color": "Mavi"
      },
      "price_modifier": 0,
      "stock_quantity": 5,
      "is_default": true
    }
  ],

  "options": [
    {
      "category": "size",
      "title": {"tr": "Boyut", "en": "Size", "vs.": "..."},
      "values": [
        {"value": "small", "label": {"tr": "Küçük", "en": "Small", "vs.": "..."}},
        {"value": "large", "label": {"tr": "Büyük", "en": "Large", "vs.": "..."}}
      ]
    }
  ],

  "media_gallery": [
    {
      "type": "image",
      "url": "products/product-001/main.jpg",
      "alt": {"tr": "Ana Görsel", "en": "Main Image", "vs.": "..."},
      "is_primary": true,
      "sort_order": 1
    },
    {
      "type": "pdf",
      "url": "products/product-001/brochure.pdf",
      "alt": {"tr": "Teknik Broşür", "en": "Technical Brochure", "vs.": "..."},
      "sort_order": 10
    }
  ],

  "related_products": ["PROD-002", "PROD-003"],
  "cross_sell_products": ["ACC-001", "ACC-002"],
  "up_sell_products": ["PROD-PREMIUM"],

  "certifications": ["CE", "ISO 9001"],

  "tags": ["tag1", "tag2", "tag3"],

  "metadata": {
    "pdf_source": "product-brochure.pdf",
    "extraction_date": "2025-10-09",
    "product_family": "Product Family Name",
    "custom_field_1": "value",
    "vs.": "..."
  }
}
```

### 🎤 Pazarlama Açılış Metni (Zorunlu)

- `long_description.tr` alanı mutlaka iki bölümden oluşmalı:
  1. `<section class="marketing-intro">` içinde aşırı ikna edici, duygusal ve satış odaklı açılış (müşteriye “bu ürünü almalıyım” dedirtmeli)
  2. `<section class="marketing-body">` içinde teknik artıları ve faydaları detaylandıran tamamlayıcı içerik
- `long_description.en` alanına aynı Türkçe HTML bloklarını kopyala (çift dil gereksinimi şimdilik Türkçe yürütülüyor)
- Pazarlama tonunda abartı, övgü ve güçlü sıfatlar kullan; mesaj tamamen Türkçe olsun

### 🧠 AI Öneri İçgörüleri

- `long_description`, `features`, `competitive_advantages` ve `faq_data` içinde İXTİF'in **ikinci el, kiralık, yedek parça ve teknik servis** hizmetlerine mutlaka yer ver.
- İletişim satırı: `0216 755 3 555` telefonu ve `info@ixtif.com` e-postası kullanılacak.
- Son kullanıcı odaklı anlat; konteyner yerleşimi, toplu sevkiyat, wholesale/packaging gibi B2B detaylardan bahsetme.
- `primary_specs` alanı dört karttan oluşmalı; transpalet ürünleri için Denge Tekeri, Li-Ion Akü, Şarj Cihazı, Standart Çatal başlıklarını kullan. Forkliftlerde Asansör, Li-Ion Akü, Şarj Cihazı, Raf Aralığı; istif makinelerinde Asansör, Akü, Şarj Cihazı, Çatal başlıklarını kullan. Değerleri ürün verisinden doldur.
- `features` alanını `{ list: [...], branding: { slogan, motto, technical_summary } }` yapısında üret.
- `use_cases` alanında en az 6 gerçekçi kullanım senaryosu yaz (Türkçe, sektör odaklı); `en` alanına birebir kopyala.
- `competitive_advantages` listesinde minimum 5 rekabet avantajı yer alsın; her maddede ölçülebilir fayda + duygusal satış tetiği bulunmalı, `en` alanı aynı olacak.
- `target_industries` için ürünün hedeflediği sektörleri (minimum 20 adet) belirt, `en` değerleri Türkçe kopya olsun.
- `faq_data` içinde minimum 10 soru-cevap çifti hazırla; sorular müşteri endişelerini kapsasın, cevaplar detaylı ve ikna edici olsun. `en` alanı `tr` ile aynı metni içermeli.

---

## ⚠️ PHASE 1 STANDARTLARI

### 🔄 DEĞİŞİKLİKLER (Eski → Yeni)

**1. Field İsimleri:**
- ❌ `"name"` → ✅ `"title"`
- ❌ String `"slug"` → ✅ JSON `"slug": {"tr": "...", "en": "...", "vs.": "..."}`

**2. SEO Yönetimi:**
- ❌ JSON içinde `"seo_data"` field'ı YOK
- ✅ SEO ayrı Universal SEO modülünde yönetiliyor
- SEO bilgilerini JSON'a ekleme!

**3. Options Field:**
- ❌ `"name"` → ✅ `"title"`
- ❌ Label'da string → ✅ Label'da JSON object `{"tr": "...", "en": "...", "vs.": "..."}`

**4. Variant Field:**
- ❌ `"name"` → ✅ `"title"`

**5. Çoklu Dil Desteği:**
- Tüm metin field'larında `"vs.": "..."` ekle
- Bu dinamik dil desteği gösterir (sistem dilediği dili ekleyebilir)

---

## 📊 ÇOKLU ÜRÜN PDF İŞLEME

**Örnek: 3 farklı model içeren PDF**

Bu PDF'de 3 farklı model var. Her biri için AYRI JSON üret:

1. **PROD-001.json**
2. **PROD-002.json**
3. **PROD-003.json**

**Ortak Özellikler:**
- Aynı series_name kullan
- Aynı features listesini paylaşabilirler
- Farklı olan: capacity, weight, dimensions, sku, model_number

---

## 🔍 ÇEVİRİ KURALLARI

### Teknik Terimler
- Teknik terim değerlerinde (ör. `unit`) uluslararası simgeler korunur (mm, kg, kW, V, Ah)
- Tüm açıklamalar, notlar ve metin tabanlı alanlar eksiksiz Türkçe yazılacak

### Genel Kurallar
- Tüm metinsel alanlarda yalnızca Türkçe içerik üret (pazarlama tonu dahil)
- `en` alanlarına da Türkçe metni birebir kopyala (çeviri üretme)
- `"vs.": "..."` mutlaka ekle; sistem yeni dil eklerse çeviri yapılacak
- Slug'lar URL-friendly olmalı (küçük harf, tire ile)

---

## ⚠️ ÖNEMLİ NOTLAR

### 1. YORUM SİSTEMLERİ
**3 Farklı Yorum Tipi Var:**

#### a) Ürün Açıklamaları (Bizim Yazdıklarımız)
```json
"long_description": {
  "tr": "Detaylı ürün açıklaması...",
  "en": "Detailed product description...",
  "vs.": "..."
}
```

#### b) Özellik Notları (Technical Notes)
```json
"technical_specs": {
  "electrical": {
    "rating": {
      "value": 5.0,
      "unit": "kW",
      "note": "Çift 2x5.0kW motor paketi"  // ← Bu not Türkçe olacak
    }
  }
}
```

#### c) Müşteri Yorumları (Reviews) - FARKLI TABLO!
```json
// Bu JSON'da YOK!
// shop_reviews tablosunda tutulacak
```

### 2. Fiyatlandırma
- PDF'lerde fiyat yoksa: `"price_on_request": true`
- B2B ürünlerse: `"deposit_required": true`
- Taksit varsa: `"installment_available": true`

### 3. Stok Bilgisi
- Yeni ürün: `"stock_quantity": 0, "availability": "on_order"`
- Lead time: Üretim/tedarik süresi (genelde 30-90 gün)

### 4. Varyantlar
- Ana farklılıklar (boyut, renk, kapasite) → Variants
- Küçük opsiyonlar → `options` array'inde

### 5. Media Gallery
- PDF'den görsel çıkarmaya çalış
- Placeholder URL'ler kullan
- `is_primary: true` ilk görsele

### 6. SEO (ÖNEMLİ!)
- **JSON içine SEO field'ı EKLEME!**
- SEO Universal SEO modülü tarafından yönetiliyor
- `shop_products` tablosunda SEO kolonları yok

---

## 🎯 KULLANIM

```bash
# Claude'a gönder:
"Şu PDF'i analiz et ve Phase 1 formatına göre JSON çıkar:
[PDF path]

Dikkat:
- Çoklu ürün varsa her biri için ayrı JSON
- name değil TITLE kullan
- Slug JSON formatında olmalı
- SEO data ekleme
- vs. ile dinamik dil desteği belirt
- Teknik özellikleri tam çıkar
- Türkçe + İngilizce çevirileri ekle"
```

---

## 📝 ÇIKTI DOSYA ADLARI

**Tek Ürün:**
- `PROD-001-product.json`
- `PROD-002-product.json`

**Çoklu Ürün:**
- `PROD-001-product.json`
- `PROD-002-product.json`
- `PROD-003-product.json`

**Konum:** `/Users/nurullah/Desktop/cms/laravel/readme/ecommerce/json-extracts/`

---

## 📋 PHASE 1 TABLO UYUMLULUĞU

Bu JSON formatı şu Phase 1 tablolarıyla uyumludur:

- ✅ `shop_products` (title, slug, JSON fields)
- ✅ `shop_product_variants` (title, JSON)
- ✅ `shop_categories` (category_id ile ilişki)
- ✅ `shop_brands` (brand_id ile ilişki)
- ✅ `shop_attributes` (Phase 1'de yok, Phase 2'de gelecek)
- ✅ `shop_reviews` (FAQ değil, müşteri yorumları için)
- ❌ `shop_product_images` (Media library kullanılıyor)
- ❌ SEO tabloları (Universal SEO modülü kullanılıyor)

---

**Son Güncelleme**: 2025-10-09 (Phase 1)
**Versiyon**: 2.0 (Portfolio Pattern Standardization)
