-- =====================================================
-- CPD15TVL ELEKTRIKLI FORKLIFT - SQL INSERT
-- Product: CPD15TVL 1500kg Electric Forklift
-- Source: 02_CPD15-18-20TVL-EN-Brochure.pdf
-- Date: 2025-10-09
-- =====================================================

SET FOREIGN_KEY_CHECKS=0;
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';

-- =====================================================
-- 1. BRAND INSERT (EP Equipment)
-- =====================================================
INSERT INTO shop_brands (
    id, name, slug, description, logo_url, website_url,
    country_code, founded_year, is_active, certifications,
    created_at, updated_at
) VALUES (
    1,
    JSON_OBJECT('tr', 'EP Equipment', 'en', 'EP Equipment'),
    'ep-equipment',
    JSON_OBJECT(
        'tr', 'Dünya çapında lider elektrikli malzeme taşıma ekipmanları üreticisi. 1997 yılında kurulan EP Equipment, inovasyon ve kalite odaklı yaklaşımıyla sektörde öncü konumdadır.',
        'en', 'World-leading manufacturer of electric material handling equipment. Founded in 1997, EP Equipment is an industry pioneer with its innovation and quality-focused approach.'
    ),
    'brands/ep-equipment-logo.png',
    'https://www.ep-equipment.com',
    'CN',
    1997,
    1,
    JSON_ARRAY(
        JSON_OBJECT('name', 'CE', 'year', 2005),
        JSON_OBJECT('name', 'ISO 9001', 'year', 2000)
    ),
    NOW(),
    NOW()
) ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    description = VALUES(description),
    updated_at = NOW();

-- =====================================================
-- 2. CATEGORY INSERT (Forklift > Akülü Forklift)
-- =====================================================
-- Parent Category: Forklift
INSERT INTO shop_categories (
    id, parent_id, name, slug, description,
    level, path, is_active, sort_order, icon,
    created_at, updated_at
) VALUES (
    1,
    NULL,
    JSON_OBJECT('tr', 'Forklift', 'en', 'Forklift'),
    'forklift',
    JSON_OBJECT(
        'tr', 'Elektrikli ve dizel forklift çözümleri',
        'en', 'Electric and diesel forklift solutions'
    ),
    1,
    '1',
    1,
    1,
    'truck',
    NOW(),
    NOW()
) ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    updated_at = NOW();

-- Sub Category: Akülü Forklift
INSERT INTO shop_categories (
    id, parent_id, name, slug, description,
    level, path, is_active, sort_order, icon,
    created_at, updated_at
) VALUES (
    11,
    1,
    JSON_OBJECT('tr', 'Akülü Forklift', 'en', 'Electric Forklift'),
    'akulu-forklift',
    JSON_OBJECT(
        'tr', 'Çevre dostu elektrikli forklift modelleri',
        'en', 'Eco-friendly electric forklift models'
    ),
    2,
    '1.11',
    1,
    1,
    'battery-charging',
    NOW(),
    NOW()
) ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    updated_at = NOW();

-- =====================================================
-- 3. PRODUCT INSERT (CPD15TVL)
-- =====================================================
INSERT INTO shop_products (
    id, category_id, brand_id, sku, model_number,
    name, slug, short_description, long_description,
    technical_specs, price_on_request, base_price,
    stock_tracking, current_stock, low_stock_threshold,
    product_type, condition, is_active, is_featured,
    weight, dimensions, meta_title, meta_description, meta_keywords,
    created_at, updated_at
) VALUES (
    1, -- id
    11, -- category_id (Akülü Forklift)
    1, -- brand_id (EP Equipment)
    'CPD15TVL', -- sku
    'CPD15TVL', -- model_number

    -- name (JSON)
    JSON_OBJECT(
        'tr', 'CPD15TVL Elektrikli Forklift',
        'en', 'CPD15TVL Electric Forklift'
    ),

    'cpd15tvl-elektrikli-forklift', -- slug

    -- short_description (JSON)
    JSON_OBJECT(
        'tr', '80V Li-Ion teknolojili kompakt 3 tekerlekli elektrikli forklift. Güçlü dual motor sistemi ve geniş çalışma alanı ile dar koridorlarda üstün performans.',
        'en', 'Compact 3-wheel dual-drive counterbalance forklift with 80V Li-Ion battery. Powerful dual motor system and spacious workspace for superior performance in narrow aisles.'
    ),

    -- long_description (JSON)
    JSON_OBJECT(
        'tr', 'CPD15TVL, 80 voltluk Li-Ion batarya teknolojisi etrafında tasarlanmış kompakt 3 tekerlekli elektrikli forklift. 2x5.0kW güçlü çift sürüş AC çekiş motorları, 48V sistemlere göre %20 daha yüksek güç verimliliği sunar. 394mm geniş bacak alanı ve ayarlanabilir direksiyon simidi ile operatör konforu maksimize edilmiştir. 1450mm dönüş yarıçapı sayesinde 3.5m genişliğindeki dar koridorlarda rahatlıkla çalışabilir. 35A tek fazlı entegre şarj cihazı ile herhangi bir prizden şarj edilebilir, şarj başına 6 saat çalışma süresi sunar. Yüksek mukavemetli mast yapısı, optimal görüş alanı ve mükemmel stabilite sağlar.',
        'en', 'CPD15TVL is a compact 3-wheel electric forklift designed around 80V Li-Ion battery technology. Features powerful 2x5.0kW dual drive AC traction motors, offering 20% higher power efficiency than 48V systems. Operator comfort is maximized with 394mm spacious legroom and adjustable steering wheel. With 1450mm turning radius, it can easily operate in narrow aisles of 3.5m width. Can be charged at any power outlet with 35A single-phase integrated charger, providing 6 hours working time per charge. High-strengthened mast structure ensures optimal visibility and excellent stability.'
    ),

    -- technical_specs (JSON)
    JSON_OBJECT(
        'capacity', JSON_OBJECT(
            'load_capacity', JSON_OBJECT('value', 1500, 'unit', 'kg'),
            'load_center_distance', JSON_OBJECT('value', 500, 'unit', 'mm')
        ),
        'performance', JSON_OBJECT(
            'travel_speed_laden', JSON_OBJECT('value', 13, 'unit', 'km/h'),
            'travel_speed_unladen', JSON_OBJECT('value', 14, 'unit', 'km/h'),
            'lifting_speed_laden', JSON_OBJECT('value', 0.33, 'unit', 'm/s'),
            'lifting_speed_unladen', JSON_OBJECT('value', 0.45, 'unit', 'm/s'),
            'lowering_speed_laden', JSON_OBJECT('value', 0.4, 'unit', 'm/s'),
            'lowering_speed_unladen', JSON_OBJECT('value', 0.44, 'unit', 'm/s'),
            'max_gradeability_laden', JSON_OBJECT('value', 10, 'unit', '%'),
            'max_gradeability_unladen', JSON_OBJECT('value', 15, 'unit', '%')
        ),
        'electrical', JSON_OBJECT(
            'battery_voltage', JSON_OBJECT('value', 80, 'unit', 'V'),
            'battery_capacity', JSON_OBJECT('value', 150, 'unit', 'Ah'),
            'battery_type', 'Li-Ion',
            'drive_motor_rating', JSON_OBJECT('value', 5.0, 'unit', 'kW', 'quantity', 2, 'note', 'Dual 5.0kW motors'),
            'charger_type', '80V-35A single-phase integrated',
            'charger_plug', '16A'
        ),
        'mast', JSON_OBJECT(
            'retracted_height', JSON_OBJECT('value', 2075, 'unit', 'mm'),
            'lift_height', JSON_OBJECT('value', 3000, 'unit', 'mm'),
            'extended_height', JSON_OBJECT('value', 4055, 'unit', 'mm'),
            'free_lift', JSON_OBJECT('value', 100, 'unit', 'mm'),
            'tilt_forward', JSON_OBJECT('value', 6, 'unit', '°'),
            'tilt_backward', JSON_OBJECT('value', 7, 'unit', '°')
        ),
        'dimensions', JSON_OBJECT(
            'length_to_forks', JSON_OBJECT('value', 1813, 'unit', 'mm'),
            'overall_length', JSON_OBJECT('value', 2733, 'unit', 'mm'),
            'overall_width', JSON_OBJECT('value', 1070, 'unit', 'mm'),
            'overall_height', JSON_OBJECT('value', 2078, 'unit', 'mm'),
            'fork_dimensions', '40x100x920mm',
            'turning_radius', JSON_OBJECT('value', 1450, 'unit', 'mm'),
            'wheelbase', JSON_OBJECT('value', 1230, 'unit', 'mm')
        ),
        'wheels', JSON_OBJECT(
            'front_tire_size', '18X7-8',
            'rear_tire_size', '15X4.5-8',
            'tire_type', 'Solid',
            'wheel_configuration', '3-wheel',
            'drive_wheels', '2x front'
        )
    ),

    1, -- price_on_request
    NULL, -- base_price
    1, -- stock_tracking
    5, -- current_stock
    1, -- low_stock_threshold
    'physical', -- product_type
    'new', -- condition
    1, -- is_active
    1, -- is_featured
    2950, -- weight (kg)

    -- dimensions (JSON)
    JSON_OBJECT('length', 2733, 'width', 1070, 'height', 2075, 'unit', 'mm'),

    -- meta_title (JSON)
    JSON_OBJECT(
        'tr', 'CPD15TVL Elektrikli Forklift - 1500kg Kapasite | EP Equipment',
        'en', 'CPD15TVL Electric Forklift - 1500kg Capacity | EP Equipment'
    ),

    -- meta_description (JSON)
    JSON_OBJECT(
        'tr', 'CPD15TVL 80V Li-Ion elektrikli forklift. 1500kg kapasite, 3000mm kaldırma yüksekliği, kompakt tasarım. Dar koridorlar için ideal. ✓ Güçlü dual motor ✓ 6 saat çalışma ✓ Yerleşik şarj',
        'en', 'CPD15TVL 80V Li-Ion electric forklift. 1500kg capacity, 3000mm lift height, compact design. Ideal for narrow aisles. ✓ Powerful dual motors ✓ 6 hours operation ✓ Onboard charging'
    ),

    -- meta_keywords (JSON)
    JSON_OBJECT(
        'tr', 'elektrikli forklift,CPD15TVL,Li-Ion forklift,1500kg forklift,3 tekerlekli forklift,EP Equipment,kompakt forklift,80V forklift,dar koridor forklift',
        'en', 'electric forklift,CPD15TVL,Li-Ion forklift,1500kg forklift,3-wheel forklift,EP Equipment,compact forklift,80V forklift,narrow aisle forklift'
    ),

    NOW(), -- created_at
    NOW() -- updated_at
) ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    short_description = VALUES(short_description),
    long_description = VALUES(long_description),
    technical_specs = VALUES(technical_specs),
    updated_at = NOW();

-- =====================================================
-- 4. PRODUCT VARIANTS INSERT
-- =====================================================

-- Variant 1: 3000mm Mast + 150Ah Battery (Default)
INSERT INTO shop_product_variants (
    id, product_id, sku, name, option_values,
    price_modifier, stock_quantity, is_default, sort_order,
    created_at, updated_at
) VALUES (
    1,
    1,
    'CPD15TVL-3000-150',
    JSON_OBJECT(
        'tr', '3000mm Mast + 150Ah Batarya',
        'en', '3000mm Mast + 150Ah Battery'
    ),
    JSON_OBJECT(
        'mast_height', '3000mm',
        'mast_type', '2-Standard',
        'battery_capacity', '150Ah'
    ),
    0, -- price_modifier
    5, -- stock_quantity
    1, -- is_default
    1, -- sort_order
    NOW(),
    NOW()
) ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    stock_quantity = VALUES(stock_quantity),
    updated_at = NOW();

-- Variant 2: 3600mm Mast + 150Ah Battery
INSERT INTO shop_product_variants (
    id, product_id, sku, name, option_values,
    price_modifier, stock_quantity, is_default, sort_order,
    created_at, updated_at
) VALUES (
    2,
    1,
    'CPD15TVL-3600-150',
    JSON_OBJECT(
        'tr', '3600mm Mast + 150Ah Batarya',
        'en', '3600mm Mast + 150Ah Battery'
    ),
    JSON_OBJECT(
        'mast_height', '3600mm',
        'mast_type', '2-Standard',
        'battery_capacity', '150Ah'
    ),
    10000, -- price_modifier
    3, -- stock_quantity
    0, -- is_default
    2, -- sort_order
    NOW(),
    NOW()
) ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    stock_quantity = VALUES(stock_quantity),
    updated_at = NOW();

-- Variant 3: 4500mm 3-Free Mast + 150Ah
INSERT INTO shop_product_variants (
    id, product_id, sku, name, option_values,
    price_modifier, stock_quantity, is_default, sort_order,
    created_at, updated_at
) VALUES (
    3,
    1,
    'CPD15TVL-4500-150',
    JSON_OBJECT(
        'tr', '4500mm 3-Free Mast + 150Ah',
        'en', '4500mm 3-Free Mast + 150Ah'
    ),
    JSON_OBJECT(
        'mast_height', '4500mm',
        'mast_type', '3-Free',
        'battery_capacity', '150Ah'
    ),
    25000, -- price_modifier
    2, -- stock_quantity
    0, -- is_default
    3, -- sort_order
    NOW(),
    NOW()
) ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    stock_quantity = VALUES(stock_quantity),
    updated_at = NOW();

-- =====================================================
-- 5. PRODUCT ATTRIBUTES
-- =====================================================

-- Attribute: Load Capacity
INSERT INTO shop_attributes (
    id, name, slug, type, is_filterable, is_visible, sort_order,
    created_at, updated_at
) VALUES (
    1,
    JSON_OBJECT('tr', 'Yük Kapasitesi', 'en', 'Load Capacity'),
    'load-capacity',
    'select',
    1, -- is_filterable
    1, -- is_visible
    1, -- sort_order
    NOW(),
    NOW()
) ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    updated_at = NOW();

INSERT INTO shop_product_attributes (
    product_id, attribute_id, value, sort_order,
    created_at, updated_at
) VALUES (
    1,
    1,
    JSON_OBJECT('tr', '1500 kg', 'en', '1500 kg'),
    1,
    NOW(),
    NOW()
) ON DUPLICATE KEY UPDATE
    value = VALUES(value),
    updated_at = NOW();

-- Attribute: Battery Type
INSERT INTO shop_attributes (
    id, name, slug, type, is_filterable, is_visible, sort_order,
    created_at, updated_at
) VALUES (
    2,
    JSON_OBJECT('tr', 'Batarya Tipi', 'en', 'Battery Type'),
    'battery-type',
    'select',
    1,
    1,
    2,
    NOW(),
    NOW()
) ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    updated_at = NOW();

INSERT INTO shop_product_attributes (
    product_id, attribute_id, value, sort_order,
    created_at, updated_at
) VALUES (
    1,
    2,
    JSON_OBJECT('tr', '80V Li-Ion', 'en', '80V Li-Ion'),
    2,
    NOW(),
    NOW()
) ON DUPLICATE KEY UPDATE
    value = VALUES(value),
    updated_at = NOW();

-- Attribute: Wheel Configuration
INSERT INTO shop_attributes (
    id, name, slug, type, is_filterable, is_visible, sort_order,
    created_at, updated_at
) VALUES (
    3,
    JSON_OBJECT('tr', 'Tekerlek Konfigürasyonu', 'en', 'Wheel Configuration'),
    'wheel-configuration',
    'text',
    1,
    1,
    3,
    NOW(),
    NOW()
) ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    updated_at = NOW();

INSERT INTO shop_product_attributes (
    product_id, attribute_id, value, sort_order,
    created_at, updated_at
) VALUES (
    1,
    3,
    JSON_OBJECT('tr', '3 Tekerlek', 'en', '3-Wheel'),
    3,
    NOW(),
    NOW()
) ON DUPLICATE KEY UPDATE
    value = VALUES(value),
    updated_at = NOW();

-- Attribute: Mast Height
INSERT INTO shop_attributes (
    id, name, slug, type, is_filterable, is_visible, sort_order,
    created_at, updated_at
) VALUES (
    4,
    JSON_OBJECT('tr', 'Kaldırma Yüksekliği', 'en', 'Lift Height'),
    'lift-height',
    'range',
    1,
    1,
    4,
    NOW(),
    NOW()
) ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    updated_at = NOW();

INSERT INTO shop_product_attributes (
    product_id, attribute_id, value, sort_order,
    created_at, updated_at
) VALUES (
    1,
    4,
    JSON_OBJECT('tr', '3000-6000 mm', 'en', '3000-6000 mm'),
    4,
    NOW(),
    NOW()
) ON DUPLICATE KEY UPDATE
    value = VALUES(value),
    updated_at = NOW();

-- =====================================================
-- 6. PRODUCT TAGS
-- =====================================================
INSERT INTO shop_product_tags (product_id, tag, created_at)
VALUES
    (1, 'electric', NOW()),
    (1, 'forklift', NOW()),
    (1, 'li-ion', NOW()),
    (1, 'compact', NOW()),
    (1, '3-wheel', NOW()),
    (1, 'narrow-aisle', NOW()),
    (1, '80v', NOW()),
    (1, 'dual-motor', NOW()),
    (1, 'ep-equipment', NOW())
ON DUPLICATE KEY UPDATE created_at = NOW();

-- =====================================================
-- 7. MODULE SETTINGS
-- =====================================================
INSERT INTO module_settings (
    module_name, setting_key, setting_value, setting_type,
    created_at, updated_at
) VALUES (
    'Shop',
    'product_cpd15tvl_enabled',
    '1',
    'boolean',
    NOW(),
    NOW()
),
(
    'Shop',
    'product_cpd15tvl_featured',
    '1',
    'boolean',
    NOW(),
    NOW()
),
(
    'Shop',
    'forklift_category_enabled',
    '1',
    'boolean',
    NOW(),
    NOW()
)
ON DUPLICATE KEY UPDATE
    setting_value = VALUES(setting_value),
    updated_at = NOW();

-- =====================================================
-- RESTORE SETTINGS
-- =====================================================
SET FOREIGN_KEY_CHECKS=1;
SET SQL_MODE=@OLD_SQL_MODE;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- =====================================================
-- VERIFICATION QUERIES
-- =====================================================
-- SELECT * FROM shop_brands WHERE id = 1;
-- SELECT * FROM shop_categories WHERE id IN (1, 11);
-- SELECT * FROM shop_products WHERE sku = 'CPD15TVL';
-- SELECT * FROM shop_product_variants WHERE product_id = 1;
-- SELECT * FROM shop_product_attributes WHERE product_id = 1;
-- SELECT * FROM shop_product_tags WHERE product_id = 1;
