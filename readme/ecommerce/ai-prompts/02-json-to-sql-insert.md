# 🤖 AI PROMPT: PDF'den SQL INSERT Sorgusu Oluşturma (Phase 1)

## 🎯 AMAÇ

PDF broşürlerinden **direkt SQL INSERT** sorgusu oluşturmak - Phase 1 standardizasyonuna uygun.

**TEK BİR SQL DOSYASI** - Yapıştır → Enter → Ürün hazır!

---

## 🇹🇷 DİL & TON KURALLARI (ZORUNLU)

- Tüm metinler %100 Türkçe olacak; İngilizce çeviri üretme.
- `JSON_OBJECT('tr', ... 'en', ...)` alanlarında `en` değeri, `tr` içeriğinin birebir kopyası olmalı.
- `body` alanı iki HTML bölümünden oluşacak:
  - `<section class="marketing-intro">` → etkileyici, abartılı, satış/pazarlama ağırlıklı açılış.
  - `<section class="marketing-body">` → teknik üstünlükleri ve faydaları detaylandıran devam.
- İkna edici ve duygusal bir ton kullan; müşteriye “bu ürünü hemen almalıyım” hissini ver.
- Pazarlama metinlerinde mutlaka İXTİF'in **ikinci el, kiralık, yedek parça ve teknik servis** hizmetlerinden bahset.
- Tüm iletişim alanlarında `0216 755 3 555` telefonu ve `info@ixtif.com` e-postasını kullan.
- Son kullanıcı odaklı yaz; konteyner dizilimi, toplu sevkiyat, wholesale/packaging gibi B2B detayları ekleme.
- `primary_specs` alanında ürün tipine göre dört kart üret (transpaletler: Denge Tekeri, Li-Ion Akü, Şarj Cihazı, Standart Çatal; forklift/istif için ilgili asansör/akü/şarj/çatal kombinasyonları).
- `features` alanını `{ list: [...], branding: { slogan, motto, technical_summary } }` yapısında hazırla.
- `target_industries` bölümünde en az 20 sektör sıralanmalı.
- `faq_data` en az 10 soru-cevap çifti içermeli.
- Son kullanıcı odaklı yaz; konteyner yerleşimi, toplu sevkiyat, wholesale/packaging gibi B2B detayı ekleme.

---

## 🏢 MARKA BİLGİSİ: İXTİF

**ÖNEMLİ**: Tüm ürünlerde marka **İXTİF** olmalıdır (EP Equipment değil!)

```sql
-- Marka her zaman İXTİF
INSERT INTO shop_brands (
    brand_id, title, slug, ...
) VALUES (
    1, -- brand_id
    JSON_OBJECT('tr', 'İXTİF', 'en', 'İXTİF'),
    JSON_OBJECT('tr', 'ixtif', 'en', 'ixtif'),
    ...
);
```

**İXTİF Bilgileri**:

**Şirket Adı**: İXTİF İç ve Dış Ticaret Anonim Şirketi
**Slogan**: "İXTİF - Türkiye'nin İstif Pazarı"

**İletişim**:
- Email: info@ixtif.com
- Telefon: 0216 755 3 555
- Hizmet Alanı: Türkiye Geneli

**Hizmetler**:
- Sıfır Ürün Satışı
- İkinci El Alım-Satım
- Kiralama
- Teknik Servis
- Yedek Parça
- Çok Marka Destek

**Açıklama**:
```
"İXTİF - Türkiye'nin İstif Pazarı!

Endüstriyel malzeme taşıma ekipmanları alanında Türkiye'nin güvenilir
çözüm ortağıyız. Forklift, transpalet, istif makinesi ve sipariş toplama
ekipmanlarında geniş ürün yelpazesi sunuyoruz.

Sıfır, ikinci el ve kiralık seçeneklerimizle her bütçeye uygun çözümler.
Türkiye genelinde teknik servis ve yedek parça desteğimizle
yanınızdayız."
```

---

## 📝 TERMİNOLOJİ & KATEGORİ KURALLARI

### Kategori ID Mapping (PDF Klasör → Category ID)

| PDF Klasör Adı | Kategori (TR) | Kategori (EN) | category_id |
|----------------|---------------|---------------|-------------|
| **1-Forklift** | FORKLİFTLER | FORKLIFTS | **163** |
| **2-Transpalet** | TRANSPALETLER | PALLET TRUCKS | **165** |
| **3-İstif Makineleri** | İSTİF MAKİNELERİ | STACKERS | **45** |
| **4-Order Picker** | ORDER PICKER | ORDER PICKER | **184** |
| **5-Otonom** | OTONOM SİSTEMLER | AUTONOMOUS SYSTEMS | **186** |
| **6-Reach Truck** | REACH TRUCK | REACH TRUCK | **183** |

**PDF path'den category_id tespiti**:
```
PDF Path: /EP PDF/1-Forklift/CPD.../brochure.pdf
  → category_id = 163 (FORKLİFTLER)

PDF Path: /EP PDF/2-Transpalet/EPT.../brochure.pdf
  → category_id = 165 (TRANSPALETLER)

PDF Path: /EP PDF/3-İstif Makineleri/EST.../brochure.pdf
  → category_id = 45 (İSTİF MAKİNELERİ)

PDF Path: /EP PDF/4-Order Picker/CPD.../brochure.pdf
  → category_id = 184 (ORDER PICKER)

PDF Path: /EP PDF/5-Otonom/AMR.../brochure.pdf
  → category_id = 186 (OTONOM SİSTEMLER)

PDF Path: /EP PDF/6-Reach Truck/CQD.../brochure.pdf
  → category_id = 183 (REACH TRUCK)
```

### Terminoloji Kuralları

| ❌ Yanlış (TR) | ✅ Doğru (TR) | Doğru (EN) |
|--------------|--------------|-----------|
| Pallet Truck | **Transpalet** | Pallet Truck |
| Pallet Kamyon | **Transpalet** | Pallet Truck |
| Stacker | **İstif Makinesi** | Stacker |
| Order Picker | **Sipariş Toplama Makinesi** | Order Picker |

**SQL Örnekleri**:
```sql
-- Forklift (category_id = 163)
INSERT INTO shop_products (...) VALUES (
    ...,
    163, -- category_id (FORKLİFTLER)
    JSON_OBJECT('tr', 'CPD15TVL Forklift', 'en', 'CPD15TVL Forklift'),
    ...
);

-- Transpalet (category_id = 165) - ÖNEMLİ!
INSERT INTO shop_products (...) VALUES (
    ...,
    165, -- category_id (TRANSPALETLER)
    JSON_OBJECT('tr', 'EPT20 Transpalet', 'en', 'EPT20 Transpalet'),
    ...
);

-- İstif Makinesi (category_id = 45)
INSERT INTO shop_products (...) VALUES (
    ...,
    45, -- category_id (İSTİF MAKİNELERİ)
    JSON_OBJECT('tr', 'EST122 İstif Makinesi', 'en', 'EST122 İstif Makinesi'),
    ...
);

-- Order Picker (category_id = 184)
INSERT INTO shop_products (...) VALUES (
    ...,
    184, -- category_id (ORDER PICKER)
    JSON_OBJECT('tr', 'CPD20 Sipariş Toplama Makinesi', 'en', 'CPD20 Sipariş Toplama Makinesi'),
    ...
);

-- Otonom Sistemler (category_id = 186)
INSERT INTO shop_products (...) VALUES (
    ...,
    186, -- category_id (OTONOM SİSTEMLER)
    JSON_OBJECT('tr', 'AMR500 Otonom Robot', 'en', 'AMR500 Otonom Robot'),
    ...
);

-- Reach Truck (category_id = 183)
INSERT INTO shop_products (...) VALUES (
    ...,
    183, -- category_id (REACH TRUCK)
    JSON_OBJECT('tr', 'CQD16 Reach Truck', 'en', 'CQD16 Reach Truck'),
    ...
);
```

---

## 🇹🇷 DİL KURALLARI: TÜRKİYE PAZARI

### Hedef Kitle: Son Kullanıcı (B2C)

**Yazım Stili**:
- ✅ Samimi, içten, güvenilir ton
- ✅ Anlaşılır günlük Türkçe
- ✅ Fayda odaklı (özellik değil!)
- ✅ Gerçek hayat örnekleri
- ❌ Teknik jargon YOK
- ❌ Abartılı iddialar YOK

---

### Teknik Terimler → Fayda Odaklı Dil

| Teknik Terim | ❌ Kötü Çeviri | ✅ İkna Edici Anlatım |
|--------------|---------------|----------------------|
| 80V Li-Ion battery | 80V lityum batarya | Tek şarjla 6 saat kesintisiz çalışma - Gün boyu verimlilik |
| Dual 5kW motors | Çift 5kW motor | Güçlü çift motor sistemi ile ağır yükleri kolayca taşır |
| 1450mm turning radius | 1450mm dönüş yarıçapı | Dar koridorlarda (3.5m) rahatça dönüş - Alan tasarrufu |
| 394mm legroom | 394mm ayak alanı | Ferah operatör kabini - Gün boyu rahat çalışma |
| Solid tires | Solid tekerlek | Patlamayan dayanıklı tekerlekler - Bakım masrafı yok |

### Kısa Açıklama (short_description) Formülü

```
[Çözüm] + [Somut Fayda] + [Kullanım Senaryosu]
```

**❌ Teknik Örnek**:
```
"80V Li-Ion teknolojili kompakt 3 tekerlekli elektrikli forklift.
Güçlü dual motor sistemi ve geniş çalışma alanı."
```

**✅ İkna Edici Örnek**:
```
"Dar alanlarda bile rahatça manevra yapabileceğiniz, günde sadece
bir kez şarj ederek 6 saat kesintisiz çalışan, işletmenizin
verimliliğini artıracak akıllı elektrikli forklift."
```

### Detaylı Açıklama (body) Yapısı

- HTML kullan: iki ana blok şart
  1. `<section class="marketing-intro">` → müşteri sorununa değinen, duygusal, abartılı satış girişi
  2. `<section class="marketing-body">` → avantajları, teknik üstünlükleri, garanti ve harekete geçirici kapanışı anlatan devam
- Her iki blokta da Türkçe yaz; `en` alanına aynı HTML'i kopyala.
- Emoji kullanımı serbest, ikna edici dil şart.

**Detaylı Açıklama Örnek Şablon**:
```sql
JSON_OBJECT(
  'tr',
  '<section class="marketing-intro">
      <p><strong>Deponuza giren herkesin ağzından şu cümle dökülsün: “Bu makineyle çalışmak ayrıcalık!”</strong> CPD15TVL, dar alan korkusunu unutturan kompakt şasiye, tek şarjla 6 saat dayanan Li-Ion güce ve operatörünüzü motive eden premium kontrollerine sahip.</p>
      <p>Tek bir tuşla enerjiyi ateşleyin, müşterilerinize hızın ve prestijin ne demek olduğunu İXTİF imzasıyla gösterin.</p>
   </section>
   <section class="marketing-body">
      <ul>
         <li>🔋 <strong>Şarj Panik Yok:</strong> 6 saat kesintisiz performans, hızlı değişen Li-Ion paketleri.</li>
         <li>⚡ <strong>Kompakt Şampiyon:</strong> 3.5 m koridorda rahat dönüş, 1500 kg yükte bile seri manevra.</li>
         <li>🛡️ <strong>İXTİF Güvencesi:</strong> 24 ay garanti, Türkiye genelinde 7/24 servis ağı.</li>
      </ul>
      <p>Üretim tesislerinden lojistik depolara kadar her sahayı podiuma çevirin. Hemen 0216 755 3 555’i arayın, info@ixtif.com’a yazın; CPD15TVL yarın kapınızda olsun.</p>
   </section>',
  'en',
  '<section class="marketing-intro">...</section><section class="marketing-body">...</section>'
)
```

### 🧠 AI Öneri İçgörüleri

- `primary_specs` kolonuna dört kart ekle; transpaletlerde Denge Tekeri, Li-Ion Akü, Şarj Cihazı, Standart Çatal başlıklarını kullan. Forklift/istif ürünlerinde asansör, akü, şarj ve çatal bilgilerini eşleştir.
- `features` kolonunu `{ list: [...], branding: { slogan, motto, technical_summary } }` yapısında üret.
- `use_cases` kolonuna en az 6 detaylı senaryo ekle; `JSON_OBJECT('tr', ...)` ve `('en', ...)` değerleri aynı Türkçe metni taşımalı.
- `competitive_advantages` için minimum 5 maddelik liste hazırla; her maddede ölçülebilir kazanım ve duygusal tetikleyici yer alsın.
- `target_industries` listesinde en az 20 sektör bulunmalı.
- `faq_data` bölümüne en az 10 soru-cevap çifti ekle; cevaplar uzun, ikna edici ve satış odaklı olsun. `sort_order` alanı ile sıralamayı belirle.

### FAQ (Sık Sorulan Sorular) - Gerçek Sorular

**ÖNEMLİ**: FAQ'lerde İXTİF iletişim bilgilerini doğal şekilde kullan!

**Ekonomi**:
```sql
JSON_OBJECT(
    'question', JSON_OBJECT('tr', 'Fiyat bilgisi alabilir miyim?'),
    'answer', JSON_OBJECT('tr', 'Size özel fiyat teklifi için 0216 755 3 555
    numaralı telefondan bizi arayabilir veya info@ixtif.com adresine
    mail atabilirsiniz. Ayrıca sıfır, ikinci el ve kiralık seçeneklerimiz de var!')
)
```

**Hizmet Alanı**:
```sql
JSON_OBJECT(
    'question', JSON_OBJECT('tr', 'Hangi şehirlere servis veriyorsunuz?'),
    'answer', JSON_OBJECT('tr', 'Türkiye genelinde hizmet vermekteyiz.
    İstanbul, Ankara, İzmir başta olmak üzere tüm illere teslimat
    yapıyoruz. Detaylı bilgi için: 0216 755 3 555')
)
```

**Servis & Yedek Parça**:
```sql
JSON_OBJECT(
    'question', JSON_OBJECT('tr', 'Servis ve yedek parça desteği var mı?'),
    'answer', JSON_OBJECT('tr', 'Elbette! Türkiye genelinde teknik servis
    ve yedek parça desteği sağlıyoruz. Birçok markada destek verebiliyoruz.
    Acil durumlar için: 0216 755 3 555')
)
```

**İkinci El & Kiralama**:
```sql
JSON_OBJECT(
    'question', JSON_OBJECT('tr', 'İkinci el veya kiralık seçeneğiniz var mı?'),
    'answer', JSON_OBJECT('tr', 'Evet! Hem ikinci el alım-satım hem de
    kiralama hizmetimiz bulunmaktadır. Bütçenize uygun çözümler için
    info@ixtif.com adresinden bize ulaşın.')
)
```

**Performans**:
- "Kaç saat çalışır?"
- "Dar koridorlarda kullanabilir miyim?"

**Garanti**:
- "Garanti süresi ne kadar?"
- "Bakımı zor mu?"

**Kullanım**:
- "Operatör ehliyeti gerekir mi?"
- "Nerede şarj ederim?"

---

## 📋 INPUT

**PDF Dosyası:**
- Ürün broşürü PDF (çoklu veya tekli ürün)

---

## 📤 OUTPUT FORMAT (Phase 1)

### **Dosya Adı:** `{sku}-insert.sql`

**Örnek:** `PROD-001-insert.sql`

```sql
-- ============================================
-- SHOP MODULE: PRODUCT INSERT (Phase 1)
-- ============================================
-- Product: Ürün Başlığı
-- SKU: PROD-001
-- Generated: 2025-10-09
-- Phase: 1 (Portfolio Pattern Standardization)
-- ============================================

-- Disable foreign key checks
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================
-- 1. BRAND (shop_brands)
-- ============================================
INSERT INTO shop_brands (
    brand_id,
    parent_brand_id,
    title,
    slug,
    description,
    short_description,
    logo_url,
    website_url,
    country_code,
    founded_year,
    is_active,
    is_featured,
    certifications,
    metadata,
    created_at,
    updated_at
) VALUES (
    1, -- brand_id (meaningful primary key)
    NULL, -- parent_brand_id
    JSON_OBJECT('tr', 'Marka Adı', 'en', 'Brand Name', 'de', 'Markenname'), -- title (JSON)
    JSON_OBJECT('tr', 'marka-adi', 'en', 'brand-name', 'de', 'markenname'), -- slug (JSON)
    JSON_OBJECT('tr', 'Marka açıklaması', 'en', 'Brand description'), -- description
    JSON_OBJECT('tr', 'Kısa açıklama', 'en', 'Short description'), -- short_description
    'brands/brand-logo.png', -- logo_url
    'https://www.brand-website.com', -- website_url
    'TR', -- country_code
    2000, -- founded_year
    1, -- is_active
    1, -- is_featured
    JSON_ARRAY(
        JSON_OBJECT('name', 'CE', 'year', 2005),
        JSON_OBJECT('name', 'ISO 9001', 'year', 2000)
    ), -- certifications
    JSON_OBJECT('industry', 'Technology', 'employee_count', 500), -- metadata
    NOW(), -- created_at
    NOW() -- updated_at
) ON DUPLICATE KEY UPDATE
    title = VALUES(title),
    updated_at = NOW();

-- ============================================
-- 2. CATEGORY (shop_categories)
-- ============================================

-- Ana Kategori
INSERT INTO shop_categories (
    category_id,
    parent_id,
    title,
    slug,
    description,
    icon_class,
    level,
    path,
    sort_order,
    is_active,
    is_featured,
    created_at,
    updated_at
) VALUES (
    1, -- category_id (meaningful primary key)
    NULL, -- parent_id
    JSON_OBJECT('tr', 'Kategori Adı', 'en', 'Category Name'), -- title (JSON)
    JSON_OBJECT('tr', 'kategori-adi', 'en', 'category-name'), -- slug (JSON)
    JSON_OBJECT('tr', 'Kategori açıklaması', 'en', 'Category description'), -- description
    'fa-solid fa-box', -- icon_class
    1, -- level
    '1', -- path
    1, -- sort_order
    1, -- is_active
    1, -- is_featured
    NOW(),
    NOW()
) ON DUPLICATE KEY UPDATE
    title = VALUES(title),
    updated_at = NOW();

-- Alt Kategori
INSERT INTO shop_categories (
    category_id,
    parent_id,
    title,
    slug,
    description,
    level,
    path,
    sort_order,
    is_active,
    is_featured,
    created_at,
    updated_at
) VALUES (
    11, -- category_id
    1, -- parent_id
    JSON_OBJECT('tr', 'Alt Kategori', 'en', 'Sub Category'), -- title
    JSON_OBJECT('tr', 'alt-kategori', 'en', 'sub-category'), -- slug
    JSON_OBJECT('tr', 'Alt kategori açıklaması', 'en', 'Sub category description'), -- description
    2, -- level
    '1/11', -- path
    1, -- sort_order
    1, -- is_active
    1, -- is_featured
    NOW(),
    NOW()
) ON DUPLICATE KEY UPDATE
    title = VALUES(title),
    updated_at = NOW();

-- ============================================
-- 3. PRODUCT (shop_products)
-- ============================================
INSERT INTO shop_products (
    product_id,
    category_id,
    brand_id,
    parent_product_id,
    sku,
    model_number,
    series_name,
    title,
    slug,
    short_description,
    body,
    features,
    technical_specs,
    highlighted_features,
    base_price,
    compare_price,
    cost_price,
    currency,
    price_on_request,
    installment_available,
    deposit_required,
    deposit_amount,
    deposit_percentage,
    weight,
    dimensions,
    stock_tracking,
    stock_quantity,
    lead_time_days,
    warranty_months,
    condition,
    availability,
    product_type,
    is_active,
    is_featured,
    is_bestseller,
    is_new_arrival,
    sort_order,
    view_count,
    rating_avg,
    rating_count,
    tags,
    use_cases,
    competitive_advantages,
    target_industries,
    faq_data,
    media_gallery,
    related_products,
    cross_sell_products,
    up_sell_products,
    metadata,
    published_at,
    created_at,
    updated_at
) VALUES (
    1, -- product_id (meaningful primary key)
    11, -- category_id (references shop_categories.category_id)
    1, -- brand_id (references shop_brands.brand_id)
    NULL, -- parent_product_id
    'PROD-001', -- sku
    'MODEL-001', -- model_number
    'Product Series', -- series_name
    JSON_OBJECT('tr', 'Ürün Başlığı', 'en', 'Product Title'), -- title (JSON)
    JSON_OBJECT('tr', 'urun-basligi', 'en', 'product-title'), -- slug (JSON)
    JSON_OBJECT('tr', 'Kısa açıklama', 'en', 'Short description'), -- short_description
    JSON_OBJECT('tr', 'Detaylı ürün açıklaması...', 'en', 'Detailed product description...'), -- body
    JSON_OBJECT(
        'tr', JSON_ARRAY('Özellik 1', 'Özellik 2', 'Özellik 3'),
        'en', JSON_ARRAY('Feature 1', 'Feature 2', 'Feature 3')
    ), -- features
    JSON_OBJECT(
        'capacity', JSON_OBJECT('load_capacity', JSON_OBJECT('value', 1500, 'unit', 'kg')),
        'electrical', JSON_OBJECT('voltage', JSON_OBJECT('value', 80, 'unit', 'V'), 'type', 'Li-Ion'),
        'performance', JSON_OBJECT('speed', JSON_OBJECT('value', 13, 'unit', 'km/h'))
    ), -- technical_specs
    JSON_ARRAY(
        JSON_OBJECT(
            'icon', 'battery-charging',
            'priority', 1,
            'title', JSON_OBJECT('tr', 'Güçlü Teknoloji', 'en', 'Powerful Technology'),
            'description', JSON_OBJECT('tr', 'Açıklama', 'en', 'Description')
        )
    ), -- highlighted_features
    NULL, -- base_price
    NULL, -- compare_price
    NULL, -- cost_price
    'TRY', -- currency
    1, -- price_on_request
    1, -- installment_available
    1, -- deposit_required
    NULL, -- deposit_amount
    30, -- deposit_percentage
    2950, -- weight (kg)
    JSON_OBJECT('length', 2733, 'width', 1070, 'height', 2075, 'unit', 'mm'), -- dimensions
    1, -- stock_tracking
    0, -- stock_quantity
    60, -- lead_time_days
    24, -- warranty_months
    'new', -- condition
    'on_order', -- availability
    'physical', -- product_type
    1, -- is_active
    1, -- is_featured
    0, -- is_bestseller
    1, -- is_new_arrival
    1, -- sort_order
    0, -- view_count
    0.00, -- rating_avg
    0, -- rating_count
    JSON_ARRAY('tag1', 'tag2', 'tag3'), -- tags
    JSON_OBJECT(
        'tr', JSON_ARRAY('Kullanım alanı 1', 'Kullanım alanı 2'),
        'en', JSON_ARRAY('Use case 1', 'Use case 2')
    ), -- use_cases
    JSON_OBJECT(
        'tr', JSON_ARRAY('Avantaj 1', 'Avantaj 2'),
        'en', JSON_ARRAY('Advantage 1', 'Advantage 2')
    ), -- competitive_advantages
    JSON_OBJECT(
        'tr', JSON_ARRAY('Sektör 1', 'Sektör 2'),
        'en', JSON_ARRAY('Industry 1', 'Industry 2')
    ), -- target_industries
    JSON_ARRAY(
        JSON_OBJECT(
            'question', JSON_OBJECT('tr', 'Soru?', 'en', 'Question?'),
            'answer', JSON_OBJECT('tr', 'Cevap', 'en', 'Answer')
        )
    ), -- faq_data
    JSON_ARRAY(
        JSON_OBJECT('type', 'image', 'url', 'products/prod-001/main.jpg', 'is_primary', 1, 'sort_order', 1)
    ), -- media_gallery
    JSON_ARRAY('PROD-002', 'PROD-003'), -- related_products (SKU)
    JSON_ARRAY('ACC-001', 'ACC-002'), -- cross_sell_products
    JSON_ARRAY('PROD-PREMIUM'), -- up_sell_products (SKU)
    JSON_OBJECT('pdf_source', 'product-brochure.pdf', 'extraction_date', '2025-10-09'), -- metadata
    NOW(), -- published_at
    NOW(), -- created_at
    NOW() -- updated_at
);

-- NOT: SEO ayarları Universal SEO modülü üzerinden yönetilir
-- shop_products tablosunda SEO kolonları yoktur

-- ============================================
-- 4. PRODUCT VARIANTS (shop_product_variants)
-- ============================================
INSERT INTO shop_product_variants (
    variant_id,
    product_id,
    sku,
    title,
    variant_type,
    option_values,
    price,
    price_modifier,
    stock_quantity,
    is_default,
    is_active,
    variant_data,
    created_at,
    updated_at
) VALUES
(
    1, -- variant_id (meaningful primary key)
    1, -- product_id (references shop_products.product_id)
    'PROD-001-V1',
    JSON_OBJECT('tr', 'Standart Varyant', 'en', 'Standard Variant'), -- title (JSON)
    'configuration',
    JSON_OBJECT('size', 'Large', 'color', 'Blue'),
    NULL,
    0,
    5,
    1,
    1,
    JSON_OBJECT('custom_field', 'value'),
    NOW(),
    NOW()
),
(
    2, -- variant_id
    1, -- product_id
    'PROD-001-V2',
    JSON_OBJECT('tr', 'Premium Varyant', 'en', 'Premium Variant'), -- title
    'configuration',
    JSON_OBJECT('size', 'XL', 'color', 'Red'),
    NULL,
    15000,
    2,
    0,
    1,
    JSON_OBJECT('custom_field', 'value'),
    NOW(),
    NOW()
);

-- ============================================
-- 5. SETTINGS (shop_settings)
-- ============================================
-- Sadece ayarlar yoksa ekle
INSERT IGNORE INTO shop_settings (
    setting_id,
    key,
    value,
    type,
    group,
    is_public,
    created_at,
    updated_at
) VALUES
(1, 'shop_name', JSON_OBJECT('tr', 'Mağaza Adı', 'en', 'Shop Name'), 'text', 'general', 1, NOW(), NOW()),
(2, 'currency', 'TRY', 'text', 'general', 1, NOW(), NOW()),
(3, 'enable_stock_tracking', '1', 'boolean', 'inventory', 0, NOW(), NOW());

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- ============================================
-- VERIFICATION QUERIES
-- ============================================
-- SELECT * FROM shop_products WHERE product_id = 1;
-- SELECT * FROM shop_product_variants WHERE product_id = 1;
-- SELECT * FROM shop_brands WHERE brand_id = 1;
-- SELECT * FROM shop_categories WHERE category_id IN (1, 11);
```

---

## 🔧 PHASE 1 KURALLAR

### 1. Meaningful Primary Keys
- **❌ Eski:** `id`
- **✅ Yeni:** `category_id`, `product_id`, `brand_id`, `variant_id`

### 2. Foreign Key References
- **❌ Eski:** `->references('id')`
- **✅ Yeni:** `->references('category_id')`, `->references('brand_id')`

### 3. Field İsimleri
- **❌ Eski:** `name`
- **✅ Yeni:** `title`

### 4. Slug Formatı
- **❌ Eski:** String `'product-slug'`
- **✅ Yeni:** JSON `JSON_OBJECT('tr', 'urun-slug', 'en', 'product-slug')`

### 5. SEO Yönetimi
- **❌ Eski:** `seo_title`, `seo_description`, `seo_keywords` kolonları
- **✅ Yeni:** SEO yoktur! Universal SEO modülü kullanılır

### 6. Phase 1'de OLMAYAN Tablolar
- ❌ `shop_product_attributes` (Phase 2'de gelecek)
- ❌ `shop_module_settings` (Phase 1'de yok)
- ✅ `shop_settings` kullan (key-value JSON)

---

## 📋 ID YÖNETİMİ

### Brand ID
- 1-10: Ana markalar
- 11-20: Alt markalar

### Category ID
- 1-10: Ana kategoriler (level 1)
- 11-20: Alt kategoriler (level 2)
- 21-30: Alt-alt kategoriler (level 3)

### Product ID
- AUTO_INCREMENT veya manuel set
- Unique olmalı

### Variant ID
- AUTO_INCREMENT önerilir

---

## 🎯 JSON FORMATLAMA

### JSON_OBJECT() Kullanımı
```sql
-- Doğru
JSON_OBJECT('tr', 'Değer', 'en', 'Value')

-- Yanlış
"{'tr': 'Değer', 'en': 'Value'}"
```

### JSON_ARRAY() Kullanımı
```sql
-- Doğru
JSON_ARRAY('tag1', 'tag2', 'tag3')

-- Nested
JSON_ARRAY(
    JSON_OBJECT('name', 'CE', 'year', 2005),
    JSON_OBJECT('name', 'ISO 9001', 'year', 2000)
)
```

### UTF-8 Dikkat
- Türkçe karakterler için `utf8mb4_unicode_ci` collation
- JSON fonksiyonları UTF-8 destekler

---

## 📊 ÇOKLU ÜRÜN SQL

**Eğer 3 ürün varsa:**

### **Seçenek 1: Tek Dosya (Önerilen)**
```sql
-- products-series-insert.sql
-- 3 ürünü de içerir
INSERT INTO shop_products (...) VALUES (...); -- PROD-001
INSERT INTO shop_products (...) VALUES (...); -- PROD-002
INSERT INTO shop_products (...) VALUES (...); -- PROD-003
```

### **Seçenek 2: Ayrı Dosyalar**
- `PROD-001-insert.sql`
- `PROD-002-insert.sql`
- `PROD-003-insert.sql`

---

## ⚠️ DİKKAT EDİLECEKLER

### 1. Sıralama Önemli
```
Brand → Category → Product → Variant → Settings
```

### 2. ON DUPLICATE KEY UPDATE
```sql
-- Brand ve Category için kullan
ON DUPLICATE KEY UPDATE
    title = VALUES(title),
    updated_at = NOW();
```

### 3. NULL Değerler
```sql
-- Optional alanlar için NULL kullan
base_price NULL,
compare_price NULL,
parent_product_id NULL
```

### 4. FOREIGN_KEY_CHECKS
```sql
-- Başta kapat
SET FOREIGN_KEY_CHECKS = 0;

-- ... INSERT işlemleri ...

-- Sonda aç
SET FOREIGN_KEY_CHECKS = 1;
```

### 5. Timestamp
```sql
-- NOW() kullan
created_at NOW(),
updated_at NOW(),
published_at NOW()
```

---

## 🎯 KULLANIM

```bash
# MySQL'e aktar
mysql -u root -p database_name < PROD-001-insert.sql

# Veya phpMyAdmin'de
# SQL sekmesine yapıştır → Çalıştır

# Laravel ile
php artisan db:seed --class=ProductSeeder
```

---

## 📝 ÇIKTI DOSYA ADLARI

**Tek Ürün:**
- `PROD-001-insert.sql`
- `PROD-002-insert.sql`

**Çoklu Ürün (Seri):**
- `products-series-insert.sql` (3 ürün birlikte)

**Konum:** `/Users/nurullah/Desktop/cms/laravel/readme/ecommerce/sql-inserts/`

---

## 🔗 İLGİLİ MİGRATİON DOSYALARI

Bu SQL dosyası şu Phase 1 migration'ları ile uyumludur:

- ✅ `001_create_shop_categories_table.php`
- ✅ `002_create_shop_brands_table.php`
- ✅ `003_create_shop_products_table.php`
- ✅ `004_create_shop_product_variants_table.php`
- ✅ `026_create_shop_settings_table.php`
- ❌ `shop_product_attributes` (Phase 1'de yok)
- ❌ SEO tabloları (Universal SEO kullanılıyor)

---

## 📋 PHASE 1 TABLO YAPISI

```sql
-- shop_brands
brand_id (PRIMARY KEY)
title (JSON)
slug (JSON)

-- shop_categories
category_id (PRIMARY KEY)
title (JSON)
slug (JSON)

-- shop_products
product_id (PRIMARY KEY)
category_id (FOREIGN KEY -> shop_categories.category_id)
brand_id (FOREIGN KEY -> shop_brands.brand_id)
title (JSON)
slug (JSON)
-- SEO kolonları YOK!

-- shop_product_variants
variant_id (PRIMARY KEY)
product_id (FOREIGN KEY -> shop_products.product_id)
title (JSON)

-- shop_settings
setting_id (PRIMARY KEY)
key (VARCHAR)
value (JSON)
```

---

**Son Güncelleme**: 2025-10-09 (Phase 1)
**Versiyon**: 2.0 (Portfolio Pattern Standardization)
