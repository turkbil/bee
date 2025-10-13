# 🤖 AI PROMPT: JSON'dan SQL INSERT Sorgusu Oluşturma

## 🎯 AMAÇ

Product JSON dosyasından MySQL/MariaDB uyumlu SQL INSERT sorgusu oluşturmak.
**TEK BİR SQL DOSYASI** - Yapıştır → Enter → Ürün hazır!

---

## 📋 INPUT

**JSON Dosyası:**
- `CPD15TVL-product.json`
- `EST122-product.json`
- `F4-product.json`

---

## 📤 OUTPUT FORMAT

### **Dosya Adı:** `{sku}-insert.sql`

**Örnek:** `CPD15TVL-insert.sql`

```sql
-- ============================================
-- SHOP MODULE: PRODUCT INSERT
-- ============================================
-- Product: CPD15TVL Elektrikli Forklift
-- SKU: CPD15TVL
-- Generated: 2025-10-09
-- ============================================

-- Disable foreign key checks
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================
-- 1. BRAND (shop_brands)
-- ============================================
INSERT INTO shop_brands (
    id,
    parent_brand_id,
    name,
    slug,
    description,
    short_description,
    logo_url,
    website_url,
    country_code,
    founded_year,
    is_active,
    is_featured,
    seo_data,
    certifications,
    metadata,
    created_at,
    updated_at
) VALUES (
    1, -- id
    NULL, -- parent_brand_id
    JSON_OBJECT('tr', 'EP Equipment', 'en', 'EP Equipment'), -- name
    'ep-equipment', -- slug
    JSON_OBJECT('tr', 'Lider malzeme taşıma ekipmanları üreticisi', 'en', 'Leading material handling equipment manufacturer'), -- description
    JSON_OBJECT('tr', 'Profesyonel Malzeme Taşıma Çözümleri', 'en', 'Professional Material Handling Solutions'), -- short_description
    'brands/ep-equipment-logo.png', -- logo_url
    'https://www.ep-equipment.com', -- website_url
    'CN', -- country_code
    1997, -- founded_year
    1, -- is_active
    1, -- is_featured
    JSON_OBJECT('tr', JSON_OBJECT('title', 'EP Equipment - Elektrikli Forklift', 'description', 'EP Equipment forklift modelleri', 'keywords', JSON_ARRAY('EP Equipment', 'forklift'))), -- seo_data
    JSON_ARRAY(JSON_OBJECT('name', 'CE', 'year', 2005), JSON_OBJECT('name', 'ISO 9001', 'year', 2000)), -- certifications
    JSON_OBJECT('product_categories', JSON_ARRAY('forklift', 'pallet_truck'), 'technology', JSON_ARRAY('Li-Ion', 'AGM')), -- metadata
    NOW(), -- created_at
    NOW() -- updated_at
) ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    updated_at = NOW();

-- ============================================
-- 2. CATEGORY (shop_categories)
-- ============================================

-- Ana Kategori: Forklift
INSERT INTO shop_categories (
    id,
    parent_id,
    name,
    slug,
    description,
    icon_class,
    level,
    path,
    sort_order,
    is_active,
    is_featured,
    seo_data,
    created_at,
    updated_at
) VALUES (
    1, -- id
    NULL, -- parent_id
    JSON_OBJECT('tr', 'Forklift', 'en', 'Forklift'), -- name
    'forklift', -- slug
    JSON_OBJECT('tr', 'Elektrikli ve dizel forkliftler', 'en', 'Electric and diesel forklifts'), -- description
    'fa-solid fa-truck-pickup', -- icon_class
    1, -- level
    '1', -- path
    1, -- sort_order
    1, -- is_active
    1, -- is_featured
    JSON_OBJECT('tr', JSON_OBJECT('title', 'Forklift Modelleri', 'description', 'Elektrikli forklift çeşitleri')), -- seo_data
    NOW(),
    NOW()
) ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    updated_at = NOW();

-- Alt Kategori: CPD Serisi
INSERT INTO shop_categories (
    id,
    parent_id,
    name,
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
    11, -- id
    1, -- parent_id
    JSON_OBJECT('tr', 'CPD Serisi', 'en', 'CPD Series'), -- name
    'cpd-serisi', -- slug
    JSON_OBJECT('tr', 'Kompakt elektrikli 3 tekerlekli forkliftler', 'en', 'Compact electric 3-wheel forklifts'), -- description
    2, -- level
    '1/11', -- path
    1, -- sort_order
    1, -- is_active
    1, -- is_featured
    NOW(),
    NOW()
) ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    updated_at = NOW();

-- ============================================
-- 3. PRODUCT (shop_products)
-- ============================================
INSERT INTO shop_products (
    id,
    category_id,
    brand_id,
    parent_product_id,
    sku,
    model_number,
    series_name,
    name,
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
    seo_data,
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
    1, -- id
    11, -- category_id (CPD Serisi)
    1, -- brand_id (EP Equipment)
    NULL, -- parent_product_id
    'CPD15TVL', -- sku
    'CPD15TVL', -- model_number
    'CPD TVL Series', -- series_name
    JSON_OBJECT('tr', 'CPD15TVL Elektrikli Forklift', 'en', 'CPD15TVL Electric Forklift'), -- name
    'cpd15tvl-elektrikli-forklift', -- slug
    JSON_OBJECT('tr', '80V Li-Ion teknolojili kompakt 3 tekerlekli elektrikli forklift', 'en', 'Compact 3-wheel dual-drive counterbalance forklift with 80V Li-Ion battery'), -- short_description
    JSON_OBJECT('tr', 'CPD15TVL, 80 voltluk Li-Ion batarya teknolojisi etrafında tasarlanmış kompakt 3 tekerlekli elektrikli forklift...', 'en', 'CPD15TVL is a compact 3-wheel electric forklift designed around 80V Li-Ion battery technology...'), -- body
    JSON_OBJECT('tr', JSON_ARRAY('80V Li-Ion batarya', 'Çift sürüş AC motorları', 'Geniş bacak alanı 394mm'), 'en', JSON_ARRAY('80V Li-Ion battery', 'Dual drive AC motors', 'Big legroom 394mm')), -- features
    JSON_OBJECT(
        'capacity', JSON_OBJECT('load_capacity', JSON_OBJECT('value', 1500, 'unit', 'kg')),
        'electrical', JSON_OBJECT('battery_voltage', JSON_OBJECT('value', 80, 'unit', 'V'), 'battery_type', 'Li-Ion'),
        'performance', JSON_OBJECT('travel_speed_laden', JSON_OBJECT('value', 13, 'unit', 'km/h'))
    ), -- technical_specs
    JSON_ARRAY(
        JSON_OBJECT('icon', 'battery-charging', 'priority', 1, 'title', JSON_OBJECT('tr', '80V Li-Ion', 'en', '80V Li-Ion')),
        JSON_OBJECT('icon', 'zap', 'priority', 2, 'title', JSON_OBJECT('tr', 'Güçlü Dual Motor', 'en', 'Powerful Dual Motors'))
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
    JSON_ARRAY('electric', 'forklift', 'li-ion', 'compact', '80v'), -- tags
    JSON_OBJECT('tr', JSON_ARRAY('Depo operasyonları', 'Dar koridorlu depolar'), 'en', JSON_ARRAY('Warehouse operations', 'Narrow aisle warehouses')), -- use_cases
    JSON_OBJECT('tr', JSON_ARRAY('48V sistemlere göre %20 daha verimli', '6 saat çalışma süresi'), 'en', JSON_ARRAY('20% more efficient than 48V', '6 hours working time')), -- competitive_advantages
    JSON_OBJECT('tr', JSON_ARRAY('Lojistik', 'E-ticaret', 'Perakende'), 'en', JSON_ARRAY('Logistics', 'E-commerce', 'Retail')), -- target_industries
    JSON_OBJECT(
        'tr', JSON_OBJECT('title', 'CPD15TVL Elektrikli Forklift - 1500kg', 'description', 'CPD15TVL 80V Li-Ion forklift', 'keywords', JSON_ARRAY('forklift', 'elektrikli')),
        'en', JSON_OBJECT('title', 'CPD15TVL Electric Forklift - 1500kg', 'description', 'CPD15TVL 80V Li-Ion forklift', 'keywords', JSON_ARRAY('forklift', 'electric'))
    ), -- seo_data
    JSON_ARRAY(
        JSON_OBJECT('question', JSON_OBJECT('tr', 'Şarj süresi?', 'en', 'Charging time?'), 'answer', JSON_OBJECT('tr', '4-5 saat', 'en', '4-5 hours'))
    ), -- faq_data
    JSON_ARRAY(
        JSON_OBJECT('type', 'image', 'url', 'cpd15tvl-main.jpg', 'is_primary', 1, 'sort_order', 1)
    ), -- media_gallery
    JSON_ARRAY('CPD18TVL', 'CPD20TVL'), -- related_products (SKU)
    JSON_ARRAY('Battery Charger 80V', 'Fork Extensions'), -- cross_sell_products
    JSON_ARRAY('CPD20TVL'), -- up_sell_products (SKU)
    JSON_OBJECT('pdf_source', '02_CPD15-18-20TVL-EN-Brochure.pdf', 'voltage_system', '80V'), -- metadata
    NOW(), -- published_at
    NOW(), -- created_at
    NOW() -- updated_at
);

-- ============================================
-- 4. PRODUCT VARIANTS (shop_product_variants)
-- ============================================
INSERT INTO shop_product_variants (
    id,
    product_id,
    sku,
    name,
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
(1, 1, 'CPD15TVL-3000-150', JSON_OBJECT('tr', '3000mm Mast + 150Ah', 'en', '3000mm Mast + 150Ah'), 'configuration', JSON_OBJECT('mast_height', '3000mm', 'battery', '150Ah'), NULL, 0, 5, 1, 1, JSON_OBJECT('lift_height', 3000), NOW(), NOW()),
(2, 1, 'CPD15TVL-4500-150', JSON_OBJECT('tr', '4500mm Mast + 150Ah', 'en', '4500mm Mast + 150Ah'), 'configuration', JSON_OBJECT('mast_height', '4500mm', 'battery', '150Ah'), NULL, 15000, 2, 0, 1, JSON_OBJECT('lift_height', 4500), NOW(), NOW());

-- ============================================
-- 5. ATTRIBUTES (shop_attributes)
-- ============================================
INSERT INTO shop_attributes (
    id,
    name,
    slug,
    type,
    unit,
    is_required,
    is_filterable,
    is_comparable,
    sort_order,
    created_at,
    updated_at
) VALUES
(1, JSON_OBJECT('tr', 'Kapasite', 'en', 'Capacity'), 'capacity', 'number', 'kg', 1, 1, 1, 1, NOW(), NOW()),
(2, JSON_OBJECT('tr', 'Batarya Voltajı', 'en', 'Battery Voltage'), 'battery-voltage', 'number', 'V', 1, 1, 1, 2, NOW(), NOW()),
(3, JSON_OBJECT('tr', 'Kaldırma Yüksekliği', 'en', 'Lift Height'), 'lift-height', 'number', 'mm', 1, 1, 1, 3, NOW(), NOW())
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    updated_at = NOW();

-- ============================================
-- 6. PRODUCT ATTRIBUTES (shop_product_attributes)
-- ============================================
INSERT INTO shop_product_attributes (
    product_id,
    variant_id,
    attribute_id,
    value,
    numeric_value,
    created_at,
    updated_at
) VALUES
(1, NULL, 1, JSON_OBJECT('tr', '1500 kg', 'en', '1500 kg'), 1500, NOW(), NOW()),
(1, NULL, 2, JSON_OBJECT('tr', '80 V', 'en', '80 V'), 80, NOW(), NOW()),
(1, NULL, 3, JSON_OBJECT('tr', '3000 mm', 'en', '3000 mm'), 3000, NOW(), NOW());

-- ============================================
-- 7. MODULE SETTINGS (shop_module_settings)
-- ============================================
-- Sadece ayarlar yoksa ekle
INSERT IGNORE INTO shop_module_settings (
    id,
    is_active,
    shop_mode,
    allow_physical_products,
    allow_digital_products,
    pricing_mode,
    enable_quotes,
    enable_deposit_payment,
    enable_b2b_features,
    enable_stock_tracking,
    default_currency,
    created_at,
    updated_at
) VALUES (
    1,
    1, -- is_active
    'single_vendor', -- shop_mode
    1, -- allow_physical_products
    1, -- allow_digital_products
    'both', -- pricing_mode (show_price, price_on_request, both)
    1, -- enable_quotes
    1, -- enable_deposit_payment
    1, -- enable_b2b_features
    1, -- enable_stock_tracking
    'TRY', -- default_currency
    NOW(),
    NOW()
);

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- ============================================
-- VERIFICATION QUERIES
-- ============================================
-- SELECT * FROM shop_products WHERE sku = 'CPD15TVL';
-- SELECT * FROM shop_product_variants WHERE product_id = 1;
-- SELECT * FROM shop_brands WHERE id = 1;
-- SELECT * FROM shop_categories WHERE id IN (1, 11);
```

---

## 🔧 KURALLAR

### 1. ID Yönetimi
- **Brand ID:** 1'den başla, her yeni marka +1
- **Category ID:** Ana: 1-10, Alt: 11-20, Sub: 21-30...
- **Product ID:** AUTO_INCREMENT veya manuel set
- **Variant ID:** AUTO_INCREMENT

### 2. JSON Formatı
- MySQL JSON fonksiyonları kullan
- `JSON_OBJECT()`, `JSON_ARRAY()`
- Türkçe karakterlere dikkat (UTF-8)

### 3. ON DUPLICATE KEY UPDATE
- Brand ve Category için kullan
- Ürün güncellemesinde dikkatli ol

### 4. Foreign Key Checks
- Başta kapat: `SET FOREIGN_KEY_CHECKS = 0;`
- Sonda aç: `SET FOREIGN_KEY_CHECKS = 1;`

### 5. Timestamp
- `NOW()` kullan
- `published_at` için geçmiş tarih de olabilir

---

## 📋 ÇOKLU ÜRÜN SQL

**Eğer 3 ürün varsa (CPD15, CPD18, CPD20):**

### **Seçenek 1: Tek Dosya (Önerilen)**
```sql
-- CPD-Series-insert.sql
-- 3 ürünü de içerir
INSERT INTO shop_products (...) VALUES (...); -- CPD15
INSERT INTO shop_products (...) VALUES (...); -- CPD18
INSERT INTO shop_products (...) VALUES (...); -- CPD20
```

### **Seçenek 2: Ayrı Dosyalar**
- `CPD15TVL-insert.sql`
- `CPD18TVL-insert.sql`
- `CPD20TVL-insert.sql`

---

## ⚠️ DİKKAT EDİLECEKLER

1. **Sıralama Önemli:**
   - Brand → Category → Product → Variant → Attributes

2. **ID Çakışması:**
   - Birden fazla ürün ekliyorsan ID'leri kontrol et

3. **JSON Encoding:**
   - Türkçe karakterler için UTF-8 MB4

4. **NULL Değerler:**
   - Optional alanlar için NULL kullan

5. **Array/Object Farkı:**
   - `tags` → JSON_ARRAY
   - `name` → JSON_OBJECT
   - `technical_specs` → JSON_OBJECT (nested)

---

## 🎯 KULLANIM

```bash
# MySQL'e aktar
mysql -u root -p database_name < CPD15TVL-insert.sql

# Veya phpMyAdmin'de
# SQL sekmesine yapıştır → Çalıştır
```

---

## 📝 ÇIKTI DOSYA ADLARI

**Tek Ürün:**
- `CPD15TVL-insert.sql`
- `EST122-insert.sql`
- `F4-insert.sql`

**Çoklu Ürün (Seri):**
- `CPD-Series-insert.sql` (3 ürün birlikte)

Konum: `/Users/nurullah/Desktop/cms/laravel/readme/ecommerce/sql-inserts/`
