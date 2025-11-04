-- ============================================
-- SHOP MODULE: SIMPLE PRODUCT INSERT
-- ============================================
-- Product Series: CPD15TVL / CPD18TVL / CPD20TVL
-- Category: FORKLİFTLER (category_id = 163)
-- Brand: İXTİF (brand_id = 1)
-- Generated: 2025-10-10
-- ============================================

SET FOREIGN_KEY_CHECKS = 0;

-- ============================================
-- 1. BRAND - İXTİF
-- ============================================
INSERT INTO shop_brands (
    brand_id,
    title,
    slug,
    description,
    logo_url,
    website_url,
    country_code,
    founded_year,
    headquarters,
    certifications,
    is_active,
    is_featured,
    sort_order,
    created_at,
    updated_at
) VALUES (
    1,
    JSON_OBJECT('tr', 'İXTİF', 'en', 'iXTiF'),
    JSON_OBJECT('tr', 'ixtif', 'en', 'ixtif'),
    JSON_OBJECT(
        'tr', 'İXTİF - Türkiye\'nin İstif Pazarı! Endüstriyel malzeme taşıma ekipmanları alanında Türkiye\'nin güvenilir çözüm ortağıyız.',
        'en', 'iXTiF - Turkey\'s Material Handling Market!'
    ),
    'brands/ixtif-logo.png',
    'https://www.ixtif.com',
    'TR',
    1995,
    'İstanbul, Türkiye',
    JSON_ARRAY(
        JSON_OBJECT('name', 'CE', 'year', 2010),
        JSON_OBJECT('name', 'ISO 9001', 'year', 2012)
    ),
    1,
    1,
    1,
    NOW(),
    NOW()
) ON DUPLICATE KEY UPDATE
    title = VALUES(title),
    description = VALUES(description),
    updated_at = NOW();

-- ============================================
-- 2. PRODUCTS
-- ============================================

-- CPD15TVL (1.5 Ton)
INSERT INTO shop_products (
    product_id,
    category_id,
    brand_id,
    sku,
    model_number,
    title,
    slug,
    short_description,
    long_description,
    product_type,
    condition,
    price_on_request,
    base_price,
    compare_at_price,
    cost_price,
    currency,
    deposit_required,
    deposit_amount,
    deposit_percentage,
    installment_available,
    max_installments,
    stock_tracking,
    current_stock,
    low_stock_threshold,
    allow_backorder,
    lead_time_days,
    weight,
    dimensions,
    technical_specs,
    features,
    highlighted_features,
    media_gallery,
    is_active,
    is_featured,
    is_bestseller,
    view_count,
    sales_count,
    published_at,
    warranty_info,
    tags,
    created_at,
    updated_at
) VALUES (
    1001,
    163,
    1,
    'CPD15TVL',
    'CPD15TVL',
    JSON_OBJECT(
        'tr', 'CPD15TVL - 1.5 Ton Kompakt Elektrikli Forklift',
        'en', 'CPD15TVL - 1.5 Ton Compact Electric Forklift'
    ),
    JSON_OBJECT('tr', 'cpd15tvl-1-5-ton-kompakt-elektrikli-forklift', 'en', 'cpd15tvl-1-5-ton-compact-electric-forklift'),
    JSON_OBJECT(
        'tr', 'Dar alanlarda bile rahatça manevra yapabileceğiniz, günde sadece bir kez şarj ederek 6 saat kesintisiz çalışan, işletmenizin verimliliğini artıracak akıllı elektrikli forklift.',
        'en', 'Smart electric forklift that works 6 hours continuously with just one charge per day.'
    ),
    JSON_OBJECT(
        'tr', 'Deponuzda alan sıkıntısı mı çekiyorsunuz? CPD15TVL, tam da bu sorunlara akıllı çözümler sunan bir elektrikli forklift.

🔋 Gün Boyu Kesintisiz Çalışma - Sabah işe başladığınızda tek şarjla tam 6 saat çalışır.

⚡ Güçlü Motor, Düşük Tüketim - 1500 kg\'a kadar yükü kolayca taşır. Elektrikli motor sayesinde yakıt masrafı sıfır!

👨‍💼 Operatör Dostu Tasarım - Geniş ayak alanı (394mm) ve ergonomik direksiyon.

🏢 Her Türlü İşte Kullanabilirsiniz - Lojistik depo, üretim tesisi, soğuk hava deposu...

✅ Garanti ve Servis - 24 ay garanti. Tel: 0216 755 4 555

İXTİF - Türkiye\'nin İstif Pazarı ile yatırımınızı geleceğe taşıyın!',
        'en', 'Are you experiencing space constraints? CPD15TVL offers smart solutions.'
    ),
    'physical',
    'new',
    1,
    NULL,
    NULL,
    NULL,
    'TRY',
    1,
    NULL,
    30,
    1,
    12,
    1,
    0,
    5,
    0,
    45,
    2950,
    JSON_OBJECT('length', 2733, 'width', 1070, 'height', 2078, 'unit', 'mm'),
    JSON_OBJECT(
        'capacity', JSON_OBJECT('load_capacity', JSON_OBJECT('value', 1500, 'unit', 'kg')),
        'electrical', JSON_OBJECT(
            'voltage', JSON_OBJECT('value', 80, 'unit', 'V'),
            'battery_capacity', JSON_OBJECT('value', 150, 'unit', 'Ah'),
            'battery_type', 'Li-Ion'
        ),
        'dimensions', JSON_OBJECT(
            'turning_radius', JSON_OBJECT('value', 1450, 'unit', 'mm'),
            'aisle_width', JSON_OBJECT('value', 3175, 'unit', 'mm')
        )
    ),
    JSON_OBJECT(
        'tr', JSON_ARRAY(
            '✅ Gün Boyu Kesintisiz Çalışma - Tek şarjla 6 saat',
            '✅ Güçlü ve Ekonomik - Çift motorlu sistem',
            '✅ Dar Alanlarda Üstün Manevra - 1450mm dönüş yarıçapı',
            '✅ Sessiz ve Çevre Dostu - Sıfır emisyon'
        ),
        'en', JSON_ARRAY(
            '✅ All Day Operation - 6 hours with single charge',
            '✅ Powerful and Economical',
            '✅ Superior Maneuverability',
            '✅ Silent and Eco-Friendly'
        )
    ),
    JSON_ARRAY(
        JSON_OBJECT(
            'icon', 'battery-charging',
            'priority', 1,
            'title', JSON_OBJECT('tr', 'Gün Boyu Durmadan Çalışır', 'en', 'Works All Day'),
            'description', JSON_OBJECT('tr', 'Sabah şarj edin, akşama kadar hiç takılma yapmasın.', 'en', 'Charge in morning, works until evening.')
        ),
        JSON_OBJECT(
            'icon', 'bolt',
            'priority', 2,
            'title', JSON_OBJECT('tr', 'Ağır Yükler Artık Sorun Değil', 'en', 'Heavy Loads No Problem'),
            'description', JSON_OBJECT('tr', '1500 kg\'ı oyuncak gibi kaldırır!', 'en', 'Lifts 1500 kg easily!')
        )
    ),
    JSON_ARRAY(
        JSON_OBJECT('type', 'image', 'url', 'products/cpd15tvl/main.jpg', 'is_primary', 1, 'sort_order', 1)
    ),
    1,
    1,
    1,
    0,
    0,
    NOW(),
    JSON_OBJECT(
        'tr', JSON_OBJECT('duration_months', 24, 'coverage', 'Tam garanti, batarya dahil'),
        'en', JSON_OBJECT('duration_months', 24, 'coverage', 'Full warranty including battery')
    ),
    JSON_ARRAY('forklift', 'elektrikli', 'lityum', 'kompakt', '1.5-ton'),
    NOW(),
    NOW()
);

-- CPD18TVL (1.8 Ton)
INSERT INTO shop_products (
    product_id,
    category_id,
    brand_id,
    sku,
    model_number,
    title,
    slug,
    short_description,
    long_description,
    product_type,
    condition,
    price_on_request,
    currency,
    deposit_required,
    deposit_percentage,
    installment_available,
    max_installments,
    stock_tracking,
    current_stock,
    lead_time_days,
    weight,
    dimensions,
    technical_specs,
    features,
    highlighted_features,
    media_gallery,
    is_active,
    is_featured,
    is_bestseller,
    published_at,
    warranty_info,
    tags,
    created_at,
    updated_at
) VALUES (
    1002,
    163,
    1,
    'CPD18TVL',
    'CPD18TVL',
    JSON_OBJECT(
        'tr', 'CPD18TVL - 1.8 Ton Kompakt Elektrikli Forklift',
        'en', 'CPD18TVL - 1.8 Ton Compact Electric Forklift'
    ),
    JSON_OBJECT('tr', 'cpd18tvl-1-8-ton-kompakt-elektrikli-forklift', 'en', 'cpd18tvl-1-8-ton-compact-electric-forklift'),
    JSON_OBJECT(
        'tr', 'Orta tonajlı yükleriniz için ideal güç! 1.8 ton taşıma kapasitesi, gün boyu kesintisiz çalışma.',
        'en', 'Ideal power for medium tonnage loads! 1.8 ton capacity.'
    ),
    JSON_OBJECT(
        'tr', '1.8 ton taşıma kapasitesi ile orta tonajlı yüklerinizi kolayca kaldırır.

🔋 205Ah lityum batarya ile 6 saat çalışma
⚡ Çift motorlu 2x5.0kW güç sistemi
📏 1550mm dönüş yarıçapı
✅ 24 ay garanti - Tel: 0216 755 4 555

İXTİF - Türkiye\'nin İstif Pazarı',
        'en', '1.8 ton carrying capacity.'
    ),
    'physical',
    'new',
    1,
    'TRY',
    1,
    30,
    1,
    12,
    1,
    0,
    45,
    3269,
    JSON_OBJECT('length', 2833, 'width', 1100, 'height', 2078, 'unit', 'mm'),
    JSON_OBJECT(
        'capacity', JSON_OBJECT('load_capacity', JSON_OBJECT('value', 1800, 'unit', 'kg')),
        'electrical', JSON_OBJECT(
            'voltage', JSON_OBJECT('value', 80, 'unit', 'V'),
            'battery_capacity', JSON_OBJECT('value', 205, 'unit', 'Ah'),
            'battery_type', 'Li-Ion'
        ),
        'dimensions', JSON_OBJECT(
            'turning_radius', JSON_OBJECT('value', 1550, 'unit', 'mm')
        )
    ),
    JSON_OBJECT(
        'tr', JSON_ARRAY(
            '✅ Orta Tonaj Uzmanı - 1.8 ton kapasite',
            '✅ 205Ah lityum batarya',
            '✅ 1550mm dönüş yarıçapı'
        ),
        'en', JSON_ARRAY(
            '✅ Medium Tonnage Expert',
            '✅ 205Ah lithium battery',
            '✅ 1550mm turning radius'
        )
    ),
    JSON_ARRAY(
        JSON_OBJECT(
            'icon', 'weight-scale',
            'priority', 1,
            'title', JSON_OBJECT('tr', 'Orta Tonaj İçin İdeal', 'en', 'Ideal for Medium Tonnage'),
            'description', JSON_OBJECT('tr', '1.8 ton tam dengeli!', 'en', 'Perfectly balanced!')
        )
    ),
    JSON_ARRAY(
        JSON_OBJECT('type', 'image', 'url', 'products/cpd18tvl/main.jpg', 'is_primary', 1, 'sort_order', 1)
    ),
    1,
    1,
    1,
    NOW(),
    JSON_OBJECT('tr', JSON_OBJECT('duration_months', 24, 'coverage', 'Tam garanti'), 'en', JSON_OBJECT('duration_months', 24, 'coverage', 'Full warranty')),
    JSON_ARRAY('forklift', 'elektrikli', 'lityum', '1.8-ton'),
    NOW(),
    NOW()
);

-- CPD20TVL (2.0 Ton)
INSERT INTO shop_products (
    product_id,
    category_id,
    brand_id,
    sku,
    model_number,
    title,
    slug,
    short_description,
    long_description,
    product_type,
    condition,
    price_on_request,
    currency,
    deposit_required,
    deposit_percentage,
    installment_available,
    max_installments,
    stock_tracking,
    current_stock,
    lead_time_days,
    weight,
    dimensions,
    technical_specs,
    features,
    highlighted_features,
    media_gallery,
    is_active,
    is_featured,
    is_bestseller,
    published_at,
    warranty_info,
    tags,
    created_at,
    updated_at
) VALUES (
    1003,
    163,
    1,
    'CPD20TVL',
    'CPD20TVL',
    JSON_OBJECT(
        'tr', 'CPD20TVL - 2 Ton Kompakt Elektrikli Forklift',
        'en', 'CPD20TVL - 2 Ton Compact Electric Forklift'
    ),
    JSON_OBJECT('tr', 'cpd20tvl-2-ton-kompakt-elektrikli-forklift', 'en', 'cpd20tvl-2-ton-compact-electric-forklift'),
    JSON_OBJECT(
        'tr', 'Maksimum güç, minimum boyut! 2 ton taşıma kapasitesi ile ağır yüklerinizi kolayca kaldırın.',
        'en', 'Maximum power, minimum size! 2 ton capacity.'
    ),
    JSON_OBJECT(
        'tr', 'Serinin en güçlüsü! 2 ton taşıma kapasitesi.

🏋️ 2000 kg yük kapasitesi
🔋 205Ah lityum batarya
📏 1585mm dönüş yarıçapı
💰 Yılda 30.000 TL tasarruf
✅ 24 ay garanti - Tel: 0216 755 4 555

İXTİF - Türkiye\'nin İstif Pazarı',
        'en', 'The most powerful! 2 ton capacity.'
    ),
    'physical',
    'new',
    1,
    'TRY',
    1,
    30,
    1,
    12,
    1,
    0,
    45,
    3429,
    JSON_OBJECT('length', 3020, 'width', 1170, 'height', 2078, 'unit', 'mm'),
    JSON_OBJECT(
        'capacity', JSON_OBJECT('load_capacity', JSON_OBJECT('value', 2000, 'unit', 'kg')),
        'electrical', JSON_OBJECT(
            'voltage', JSON_OBJECT('value', 80, 'unit', 'V'),
            'battery_capacity', JSON_OBJECT('value', 205, 'unit', 'Ah'),
            'battery_type', 'Li-Ion'
        ),
        'dimensions', JSON_OBJECT(
            'turning_radius', JSON_OBJECT('value', 1585, 'unit', 'mm')
        )
    ),
    JSON_OBJECT(
        'tr', JSON_ARRAY(
            '✅ Maksimum Güç - 2 ton kapasite',
            '✅ Kompakt Tasarım - 1585mm dönüş',
            '✅ Ekonomik - Yılda 30.000 TL tasarruf'
        ),
        'en', JSON_ARRAY(
            '✅ Maximum Power - 2 ton capacity',
            '✅ Compact Design',
            '✅ Economical Operation'
        )
    ),
    JSON_ARRAY(
        JSON_OBJECT(
            'icon', 'dumbbell',
            'priority', 1,
            'title', JSON_OBJECT('tr', 'Serinin En Güçlüsü', 'en', 'Most Powerful'),
            'description', JSON_OBJECT('tr', 'Tam 2 ton kapasite!', 'en', 'Full 2 ton capacity!')
        )
    ),
    JSON_ARRAY(
        JSON_OBJECT('type', 'image', 'url', 'products/cpd20tvl/main.jpg', 'is_primary', 1, 'sort_order', 1)
    ),
    1,
    1,
    1,
    NOW(),
    JSON_OBJECT('tr', JSON_OBJECT('duration_months', 24, 'coverage', 'Tam garanti'), 'en', JSON_OBJECT('duration_months', 24, 'coverage', 'Full warranty')),
    JSON_ARRAY('forklift', 'elektrikli', '2-ton', 'güçlü'),
    NOW(),
    NOW()
);

SET FOREIGN_KEY_CHECKS = 1;

-- Verification
SELECT product_id, sku, JSON_UNQUOTE(JSON_EXTRACT(title, '$.tr')) as title_tr FROM shop_products WHERE sku IN ('CPD15TVL', 'CPD18TVL', 'CPD20TVL');
