# 🏗️ SHOP SİSTEM MİMARİSİ

## 📋 GENEL BAKIŞ

Laravel Shop System veritabanı yapısı ve alan açıklamaları.

**Tablo:** `shop_products`

---

## 🗂️ VERİTABANI ALANLARI

### 1. PRIMARY KEY

| Alan | Tip | Açıklama |
|------|-----|----------|
| `product_id` | BIGINT (ID) | Otomatik artan birincil anahtar |

---

### 2. RELATIONS (İlişkiler)

| Alan | Tip | Açıklama | Örnek |
|------|-----|----------|-------|
| `category_id` | BIGINT | Kategori ID (shop_categories) | `2` (Transpalet) |
| `brand_id` | BIGINT (nullable) | Marka ID (shop_brands) | `1` (İXTİF) |

**Kategoriler** (PDF'den tespit et, ID'yi HARDCODE yaz):
```php
1 => 'Forklift'          // Garanti: 24+60 ay
2 => 'Transpalet'        // Garanti: 12+24 ay
3 => 'İstif Makinesi'    // Garanti: 12+24 ay
4 => 'Sipariş Toplama'   // Garanti: 12+24 ay
5 => 'Otonom/AGV'        // Garanti: 12+24 ay
6 => 'Reach Truck'       // Garanti: 12+24 ay
```

**Markalar:**
```php
1 => 'İXTİF' (varsayılan)
```

---

### 3. PRODUCT IDENTIFIERS (Tanımlayıcılar)

| Alan | Tip | Açıklama | Örnek |
|------|-----|----------|-------|
| `sku` | STRING (unique) | Stok Kodu - Benzersiz | `"F4-201"` |
| `model_number` | STRING (nullable) | Model numarası | `"F4-201-2024"` |
| `barcode` | STRING (nullable) | Barkod numarası | `"1234567890123"` |

---

### 4. VARIANT SYSTEM (Varyant Sistemi)

| Alan | Tip | Açıklama | Örnek |
|------|-----|----------|-------|
| `parent_product_id` | BIGINT (nullable) | Ana ürün ID | `28` (Master product ID) |
| `is_master_product` | BOOLEAN | Ana ürün mü? | `true` / `false` |
| `variant_type` | STRING | Varyant tipi | `"fork-length"`, `"battery"` |

**Variant Type Değerleri** (TÜRKÇE kullan):
```php
'catal-uzunlugu'      // Çatal uzunluğu (1150mm, 1220mm, vb.)
'catal-genisligi'     // Çatal genişliği (685mm, 550mm, vb.)
'tekerlek-tipi'       // Tekerlek tipi (polyurethane, tandem, vb.)
'batarya-tipi'        // Batarya tipi (standart, uzatılmış, vb.)
'kapasite'            // Kapasite (2.0t, 2.5t, vb.)
'direk-yuksekligi'    // Direk yüksekliği (istif için)
'kontrol-tipi'        // Kontrol tipi (manuel, elektrikli, vb.)
```

---

### 5. BASIC INFO (Temel Bilgiler - JSON Çoklu Dil)

| Alan | Tip | Açıklama | Format |
|------|-----|----------|--------|
| `title` | JSON | Ürün başlığı | `{"tr": "F4 201 Transpalet", "en": "..."}` |
| `slug` | JSON | URL slug | `{"tr": "f4-201-transpalet", "en": "..."}` |
| `short_description` | JSON (nullable) | Kısa açıklama (maks 160 karakter) | `{"tr": "Kısa açıklama...", "en": "..."}` |
| `long_description` | JSON (nullable) | Detaylı açıklama (HTML) | `{"tr": "<section>...</section>", "en": "..."}` |

**ÖNEMLI:**
- Tüm JSON alanlar `JSON_UNESCAPED_UNICODE` ile encode edilmeli
- Türkçe karakterler korunmalı (ı, ş, ğ, ü, ö, ç, İ)

**Örnek:**
```php
'title' => json_encode(['tr' => 'İXTİF F4 201 - 1150mm Çatal'], JSON_UNESCAPED_UNICODE)
// Çıktı: {"tr":"İXTİF F4 201 - 1150mm Çatal"}
```

---

### 6. PRODUCT TYPE & CONDITION (Ürün Tipi ve Durumu)

| Alan | Tip | Değerler | Açıklama |
|------|-----|----------|----------|
| `product_type` | ENUM | `physical`, `digital`, `service`, `membership`, `bundle` | Ürün tipi |
| `condition` | ENUM | `new`, `used`, `refurbished` | Ürün durumu |

**Çoğunlukla:**
```php
'product_type' => 'physical',
'condition' => 'new',
```

---

### 7. PRICING (Fiyatlandırma)

| Alan | Tip | Açıklama | Örnek |
|------|-----|----------|-------|
| `price_on_request` | BOOLEAN | Fiyat sorunuz aktif mi? | `false` |
| `base_price` | DECIMAL(12,2) | Temel fiyat (₺) | `125000.00` |
| `compare_at_price` | DECIMAL(12,2) | İndirim öncesi fiyat | `150000.00` |
| `cost_price` | DECIMAL(12,2) | Maliyet fiyatı | `90000.00` |
| `currency` | STRING(3) | Para birimi (ISO 4217) | `"TRY"` |

---

### 8. DEPOSIT & INSTALLMENT (Kapora ve Taksit - B2B)

| Alan | Tip | Açıklama | Örnek |
|------|-----|----------|-------|
| `deposit_required` | BOOLEAN | Kapora gerekli mi? | `false` |
| `deposit_amount` | DECIMAL(12,2) | Sabit kapora tutarı | `25000.00` |
| `deposit_percentage` | INTEGER | Kapora yüzdesi | `20` (%) |
| `installment_available` | BOOLEAN | Taksit yapılabilir mi? | `true` |
| `max_installments` | INTEGER | Maksimum taksit sayısı | `12` |

---

### 9. STOCK MANAGEMENT (Stok Yönetimi)

| Alan | Tip | Açıklama | Örnek |
|------|-----|----------|-------|
| `stock_tracking` | BOOLEAN | Stok takibi yapılsın mı? | `true` |
| `current_stock` | INTEGER | Mevcut stok miktarı | `15` |
| `low_stock_threshold` | INTEGER | Düşük stok uyarı seviyesi | `5` |
| `allow_backorder` | BOOLEAN | Stokta yokken sipariş alınabilir mi? | `false` |
| `lead_time_days` | INTEGER (nullable) | Temin süresi (gün) | `30` |

---

### 10. PHYSICAL PROPERTIES (Fiziksel Özellikler)

| Alan | Tip | Açıklama | Format |
|------|-----|----------|--------|
| `weight` | DECIMAL(10,2) | Ağırlık (kg) | `850.50` |
| `dimensions` | JSON | Boyutlar | `{"length":1200,"width":685,"height":1900,"unit":"mm"}` |

---

### 11. TECHNICAL SPECIFICATIONS (Teknik Özellikler)

#### 11.1. technical_specs (JSON)

**Format:**
```json
{
  "Kapasite": "2.0 Ton (2000 kg)",
  "Kaldırma Yüksekliği": "200 mm",
  "Akü": "48V 100Ah Li-Ion",
  "Boyutlar": "1200 x 685 x 1900 mm (U x G x Y)",
  "Ağırlık": "850 kg",
  "Çatal Uzunluğu": "1150 mm (standart)",
  "Çatal Genişliği": "160 / 540 mm (ayarlanabilir)",
  ...
}
```

**Kullanım:**
```php
'technical_specs' => json_encode([
    'Kapasite' => '2.0 Ton (2000 kg)',
    'Akü' => '48V 100Ah Li-Ion',
    // ...
], JSON_UNESCAPED_UNICODE),
```

#### 11.2. features (JSON Array - icon + text formatı ZORUNLU)

**Format:**
```json
[
  {"icon": "battery-full", "text": "48V 100Ah Li-Ion akü sistemi (4 modül)"},
  {"icon": "weight-hanging", "text": "2.0 ton kaldırma kapasitesi"},
  {"icon": "hand", "text": "Ergonomik kontrol kolu"},
  {"icon": "circle-notch", "text": "Polyurethane tekerlek"},
  ...
]
```

**Kullanım:**
```php
'features' => json_encode([
    ['icon' => 'battery-full', 'text' => '48V 100Ah Li-Ion akü sistemi (4 modül)'],
    ['icon' => 'weight-hanging', 'text' => '2.0 ton kaldırma kapasitesi'],
    ['icon' => 'hand', 'text' => 'Ergonomik kontrol kolu'],
    ['icon' => 'circle-notch', 'text' => 'Polyurethane tekerlek'],
    ['icon' => 'shield-alt', 'text' => 'Elektromanyetik fren sistemi'],
    ['icon' => 'cog', 'text' => 'Düşük bakım maliyeti'],
    ['icon' => 'layer-group', 'text' => 'Platform F mimarisi'],
    ['icon' => 'check-circle', 'text' => 'CE sertifikalı']
    // Toplam 8 madde
], JSON_UNESCAPED_UNICODE),
```

#### 11.3. highlighted_features (JSON Array)

**Format:**
```json
[
  {
    "icon": "battery",
    "title": "Li-Ion Akü Sistemi",
    "description": "48V 100Ah modüler batarya ile 8 saat kesintisiz çalışma"
  },
  {
    "icon": "weight",
    "title": "2 Ton Kapasite",
    "description": "Ağır yük taşımada maksimum güvenlik ve performans"
  }
]
```

#### 11.4. accessories (JSON Array - icon dahil)

**Format:**
```json
[
  {
    "icon": "cog",
    "name": "Extended Battery Pack",
    "description": "4 modül yerine 6 modül (150Ah)",
    "is_standard": false,
    "is_optional": true,
    "price": "Talep üzerine"
  },
  {
    "icon": "plug",
    "name": "Polyurethane Tekerlek",
    "description": "Standart tekerlek sistemi",
    "is_standard": true,
    "is_optional": false,
    "price": null
  }
]
```

**⚠️ ÖNEMLİ:** `is_standard: true` olanların `price` değeri **NULL** olmalı!

#### 11.5. certifications (JSON Array - icon dahil)

**Format:**
```json
[
  {
    "icon": "certificate",
    "name": "CE",
    "year": 2024,
    "authority": "TÜV Rheinland"
  },
  {
    "icon": "award",
    "name": "ISO 9001",
    "year": 2023,
    "authority": "SGS"
  },
  {
    "icon": "shield-check",
    "name": "EN 16796",
    "year": 2024,
    "authority": "CEN"
  }
]
```

---

### 12. MEDIA (Medya)

| Alan | Tip | Açıklama | Örnek |
|------|-----|----------|-------|
| `media_gallery` | JSON | Medya galerisi | `[{"type":"image","url":"...","is_primary":true}]` |
| `video_url` | STRING (nullable) | Video URL | `"https://youtube.com/watch?v=..."` |
| `manual_pdf_url` | STRING (nullable) | Kullanım kılavuzu PDF | `"https://..."` |

**NOT:** Medya yönetimi genellikle Spatie Media Library ile yapılır.

---

### 13. DISPLAY & STATUS (Görünüm ve Durum)

| Alan | Tip | Açıklama | Örnek |
|------|-----|----------|-------|
| `is_active` | BOOLEAN | Aktif/Pasif durumu | `true` |
| `is_featured` | BOOLEAN | Öne çıkan ürün | `false` |
| `is_bestseller` | BOOLEAN | Çok satan ürün | `false` |
| `view_count` | INTEGER | Görüntülenme sayısı | `0` |
| `sales_count` | INTEGER | Satış sayısı | `0` |
| `published_at` | TIMESTAMP | Yayınlanma tarihi | `now()` |

---

### 14. ADDITIONAL DATA (Ek Veriler)

#### 14.1. warranty_info (JSON)

**Format:**
```json
{
  "coverage": "Makineye satın alım tarihinden itibaren 12 ay fabrika garantisi verilir. Li-Ion batarya modülleri ise satın alım tarihinden itibaren 24 ay garanti kapsamındadır. Garanti, normal kullanım koşullarında üretim hatalarını kapsar.",
  "duration_months": 12,
  "battery_warranty_months": 24
}
```

**⚠️ ÖNEMLİ:** Kategori ismi ASLA yazılmamalı!
```php
// ❌ YANLIŞ:
"coverage": "Kategori 2 Transpalet: 12 ay garanti..."

// ✅ DOĞRU:
"coverage": "Makineye 12 ay, Li-Ion batarya modüllerine 24 ay garanti..."
```

#### 14.2. shipping_info (JSON)

**Format:**
```json
{
  "weight_limit": 1000,
  "size_limit": "large",
  "free_shipping": false,
  "delivery_time": "7-14 iş günü"
}
```

#### 14.3. tags (JSON Array)

**Format:**
```json
["transpalet", "li-ion", "2-ton", "ixtif", "elektrikli"]
```

---

### 15. MARKETING CONTENT (Pazarlama İçeriği)

**NOT:** Bu alanlar standart alanlarda yok, ama seeder'larda kullanılıyor:

| Alan | Tip | Açıklama | Format |
|------|-----|----------|--------|
| `use_cases` | JSON Array | Kullanım senaryoları (icon + text) | `[{"icon":"box-open", "text":"Senaryo 1"}, ...]` |
| `faq_data` | JSON Array | SSS (son soruda İXTİF bilgisi) | `[{"question":"...","answer":"..."}]` |
| `competitive_advantages` | JSON Array | Rekabet avantajları (icon + text) | `[{"icon":"bolt", "text":"Avantaj 1"}, ...]` |
| `target_industries` | JSON Array | Hedef endüstriler (icon + text, MİN 20) | `[{"icon":"warehouse", "text":"Lojistik"}, ...]` |
| `primary_specs` | JSON Array | Ana özellikler (icon+label+value) | `[{"icon":"weight-hanging", "label":"Kapasite", "value":"2 Ton"}]` |

**Örnek primary_specs:**
```php
'primary_specs' => json_encode([
    ['icon' => 'weight-hanging', 'label' => 'Kapasite', 'value' => '2.0 Ton'],
    ['icon' => 'arrows-left-right', 'label' => 'Çatal', 'value' => '1150 mm'],
    ['icon' => 'battery-full', 'label' => 'Akü', 'value' => '48V Li-Ion'],
    ['icon' => 'gauge', 'label' => 'Hız', 'value' => '4.5 km/s']
], JSON_UNESCAPED_UNICODE),
```

**⚠️ UYARI:** Bu örnekler REFERANS içindir. PDF'deki gerçek verileri kullan!

**Örnek highlighted_features:**
```php
'highlighted_features' => json_encode([
    ['icon' => 'battery-full', 'title' => 'Li-Ion Akü', 'description' => 'Uzun ömürlü ve hızlı şarj'],
    ['icon' => 'weight-scale', 'title' => '2 Ton Kapasite', 'description' => 'Güvenli taşıma'],
    ['icon' => 'compress', 'title' => 'Kompakt Şasi', 'description' => 'Dar koridor çevikliği'],
    ['icon' => 'circle-notch', 'title' => 'PU Tekerlek', 'description' => 'Sessiz çalışma'],
    ['icon' => 'shield-alt', 'title' => 'Elektromanyetik Fren', 'description' => 'Maksimum güvenlik'],
    ['icon' => 'cog', 'title' => 'Düşük TCO', 'description' => 'Az bakım maliyeti']
], JSON_UNESCAPED_UNICODE),
```

**⚠️ UYARI:** Bu örnekler REFERANS içindir. PDF'deki gerçek verileri ve içeriğe uygun iconları kullan!

**Örnek use_cases (icon + text formatı ZORUNLU):**
```php
'use_cases' => json_encode([
    ['icon' => 'box-open', 'text' => 'E-ticaret fulfillment merkezlerinde standart EUR palet sevkiyat'],
    ['icon' => 'store', 'text' => 'Perakende zincir depolarında dar koridor raf arası malzeme transferi'],
    ['icon' => 'snowflake', 'text' => 'Gıda lojistik merkezlerinde soğuk hava deposu operasyonları'],
    ['icon' => 'pills', 'text' => 'İlaç ve kozmetik depolarında hassas ürün taşıma'],
    ['icon' => 'car', 'text' => 'Otomotiv yedek parça depolarında kompakt alan yönetimi'],
    ['icon' => 'tshirt', 'text' => 'Tekstil fabrikalarında kumaş rulosu ve koli paletleme'],
    ['icon' => 'warehouse', 'text' => '3PL merkezlerinde yoğun vardiya içi besleme hatları'],
    ['icon' => 'industry', 'text' => 'Endüstriyel üretim hücrelerinde WIP taşıma']
], JSON_UNESCAPED_UNICODE),
```

**Örnek faq_data** (10-12 adet DETAYLI):
```php
'faq_data' => json_encode([
    [
        'question' => 'Li-Ion batarya sisteminin avantajları nelerdir ve neden tercih edilmelidir?',
        'answer' => 'Li-Ion bataryalar kurşun-asit bataryalara göre 3 kat daha uzun ömürlü olup 2-3 saatte tam şarj olur. Bakım gerektirmez, 2000+ şarj döngüsü sunar ve enerji verimliliği %95\'in üzerindedir.'
    ],
    [
        'question' => 'İki akü yuvasının sağladığı operasyonel faydalar nelerdir?',
        'answer' => 'Çift akü sistemi sayesinde bir akü şarjdayken diğeriyle çalışma devam eder. Bu sistem 7/24 kesintisiz operasyon sağlar ve vardiya geçişlerinde duruş süresini ortadan kaldırır.'
    ],
    [
        'question' => 'Kompakt şasi tasarımının dar koridor operasyonlarına faydası nedir?',
        'answer' => '1360 mm dönüş yarıçapı ve 400 mm çatal ucu mesafesi ile standart raf arası geçişlerde yüksek manevra kabiliyeti sağlar. 2 metrelik koridorlarda rahat çalışma imkanı sunar.'
    ],
    // ... (8-9 teknik soru daha - İXTİF bilgisi YOK)
    [
        'question' => 'Garanti kapsamı ve servis desteği nasıl sağlanır?',
        'answer' => 'Makineye 12 ay, Li-Ion batarya modüllerine 24 ay fabrika garantisi verilir. İXTİF, Türkiye genelinde satış, servis, kiralama ve yedek parça desteği sağlar. 7/24 teknik danışmanlık hattı: 0216 755 3 555.'
    ]
    // Toplam 10-12 adet
], JSON_UNESCAPED_UNICODE),
```

**FAQ Kuralları:**
- **Soru**: 10-15 kelime (müşterinin gerçek sorusu)
- **Yanıt**: 20-40 kelime (teknik detay, sayısal veriler)
- **⚠️ İXTİF bilgisi:** SADECE SON SORUDA (garanti/servis sorusu) olmalı
- **İlk 11 soru:** Teknik yanıtlar, İXTİF bilgisi YOK

---

### 16. TIMESTAMPS & SOFT DELETE

| Alan | Tip | Açıklama |
|------|-----|----------|
| `created_at` | TIMESTAMP | Oluşturulma tarihi |
| `updated_at` | TIMESTAMP | Güncellenme tarihi |
| `deleted_at` | TIMESTAMP (nullable) | Soft delete tarihi |

---

## 🎯 VARIANT SİSTEMİ DETAYLI AÇIKLAMA

### Master Product (Ana Ürün)

**Özellikler:**
- `parent_product_id` = `NULL`
- `is_master_product` = `true`
- Tüm teknik özellikler, features, FAQ burada
- Varyantlar için "base" içerik

**Örnek:**
```php
[
    'sku' => 'F4-201',
    'title' => json_encode(['tr' => 'F4 201 Li-Ion Akülü Transpalet 2.0 Ton'], JSON_UNESCAPED_UNICODE),
    'is_master_product' => true,
    'parent_product_id' => null,
    'features' => json_encode([...], JSON_UNESCAPED_UNICODE),
    'faq_data' => json_encode([...], JSON_UNESCAPED_UNICODE),
    'technical_specs' => json_encode([...], JSON_UNESCAPED_UNICODE),
    // ...
]
```

### Variant Product (Varyant Ürün)

**Özellikler:**
- `parent_product_id` = Master product ID
- `is_master_product` = `false`
- `variant_type` = Varyant tipi (fork-length, battery, vb.)
- **UNIQUE CONTENT** içermeli:
  - `title` (İXTİF + Varyant adı)
  - `slug` (Türkçe karakterli)
  - `short_description` (30-50 kelime)
  - `long_description` (HTML içerik)
  - `use_cases` (6 senaryo)

**Örnek:**
```php
[
    'sku' => 'F4-201-1150',
    'parent_product_id' => 28, // Master product ID
    'is_master_product' => false,
    'variant_type' => 'catal-uzunlugu', // ✅ TÜRKÇE!

    'title' => json_encode(['tr' => 'İXTİF F4 201 - 1150mm Çatal'], JSON_UNESCAPED_UNICODE),
    'slug' => json_encode(['tr' => 'ixtif-f4-201-1150mm-catal'], JSON_UNESCAPED_UNICODE),

    'short_description' => json_encode(['tr' => 'Standart 1150mm çatal uzunluğu ile EUR palet (1200x800mm) taşımada maksimum verimlilik. Dar koridor operasyonlarında ideal dönüş yarıçapı ve manevra özgürlüğü sunan, endüstride en yaygın tercih edilen çatal boyutu.'], JSON_UNESCAPED_UNICODE),

    'long_description' => json_encode(['tr' => '<section class="variant-intro">...</section>'], JSON_UNESCAPED_UNICODE),

    'use_cases' => json_encode([
        ['icon' => 'box-open', 'text' => 'E-ticaret fulfillment merkezlerinde standart EUR palet sevkiyat'],
        ['icon' => 'store', 'text' => 'Perakende zincir depolarında dar koridor raf arası malzeme transferi'],
        ['icon' => 'snowflake', 'text' => 'Gıda depolarında soğuk oda operasyonları'],
        ['icon' => 'warehouse', 'text' => '3PL merkezlerinde hat besleme'],
        ['icon' => 'car', 'text' => 'Otomotiv yedek parça depolarında malzeme transferi'],
        ['icon' => 'industry', 'text' => 'Endüstriyel üretimde WIP taşıma']
        // Toplam 6 senaryo - icon + text formatında
    ], JSON_UNESCAPED_UNICODE),

    // features, faq_data, technical_specs master'dan inherit edilir
    // 'features' => null, (ShopController'da parent'tan çekilir)
    // 'faq_data' => null,
    // 'technical_specs' => null,
]
```

---

## 🎨 FRONTEND RENDER STRATEJİSİ

### Master Product Page (show.blade.php)

**Gösterilen İçerik:**
- ✅ Hero section (title, short_description)
- ✅ Gallery
- ✅ Long description
- ✅ Technical specs (detaylı tablo)
- ✅ Features (bullet points)
- ✅ FAQ
- ✅ Use cases
- ✅ Competitive advantages
- ✅ Target industries
- ✅ Variants (varyant listesi)
- ✅ Contact form

### Variant Product Page (show-variant.blade.php)

**Gösterilen İçerik:**
- ✅ Hero section (title, short_description)
- ✅ Variant info box ("Bu bir varyant ürünüdür")
- ✅ "Ana Ürüne Git" butonu
- ✅ Gallery (varsa kendi, yoksa parent'tan)
- ✅ Long description (varyanta özel)
- ✅ Use cases (varyanta özel)
- ✅ Sibling variants (diğer varyantlar)
- ✅ Contact form

**Gösterilmeyen İçerik:**
- ❌ Technical specs (master'da var)
- ❌ Features (master'da var)
- ❌ FAQ (master'da var)
- ❌ Competitive advantages (master'da var)
- ❌ Target industries (master'da var)

---

## 📌 ÖNEMLİ NOTLAR

### 1. JSON Encoding
**MUTLAKA** `JSON_UNESCAPED_UNICODE` kullan:
```php
json_encode($data, JSON_UNESCAPED_UNICODE)
```

### 2. Slug Generation
```php
use Illuminate\Support\Str;

$slug = Str::slug('İXTİF F4 201 - 1150mm Çatal');
// Çıktı: ixtif-f4-201-1150mm-catal
```

### 3. Marka Adı
**ASLA "EP" KULLANMA! → DAIMA "İXTİF" KULLAN**

### 4. Varyant Short Description
**30-50 kelime**, açıklayıcı olmalı.

### 5. Unique Content
Her varyant için:
- ✅ Unique `long_description`
- ✅ Unique `use_cases`
- ✅ Unique `short_description`

---

Bu dosya ChatGPT'nin seeder üretirken referans alacağı sistem mimarisidir.
