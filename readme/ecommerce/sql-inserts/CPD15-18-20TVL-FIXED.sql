-- ============================================
-- SHOP MODULE: PRODUCT INSERT (Phase 1)
-- ============================================
-- Product Series: CPD15TVL / CPD18TVL / CPD20TVL
-- Category: FORKLİFTLER (category_id = 163)
-- Brand: İXTİF
-- Generated: 2025-10-10
-- Phase: 1 (Portfolio Pattern Standardization)
-- ============================================

-- Disable foreign key checks
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================
-- 1. BRAND - İXTİF (brand_id = 1)
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
    is_active,
    is_featured,
    sort_order,
    certifications,
    created_at,
    updated_at
) VALUES (    1, -- brand_id
    JSON_OBJECT('tr', 'İXTİF', 'en', 'iXTiF'), -- title
    JSON_OBJECT('tr', 'ixtif', 'en', 'ixtif'), -- slug
    JSON_OBJECT(
        'tr', 'İXTİF - Türkiye\'nin İstif Pazarı! Endüstriyel malzeme taşıma ekipmanları alanında Türkiye\'nin güvenilir çözüm ortağıyız. Forklift, transpalet, istif makinesi ve sipariş toplama ekipmanlarında geniş ürün yelpazesi sunuyoruz.',
        'en', 'iXTiF - Turkey\'s Material Handling Market! We are Turkey\'s trusted solution partner in industrial material handling equipment. We offer a wide range of products in forklifts, pallet trucks, stackers and order pickers.'
    ), -- description
    JSON_OBJECT(
        'tr', 'Türkiye\'nin İstif Pazarı - Sıfır, ikinci el ve kiralık forklift çözümleri',
        'en', 'Turkey\'s Material Handling Market - New, used and rental forklift solutions'
    ), -- description
    'brands/ixtif-logo.png', -- logo_url
    'https://www.ixtif.com', -- website_url
    'TR', -- country_code
    1995, -- founded_year
    'İstanbul, Türkiye', -- headquarters
    1, -- is_active
    1, -- is_featured
    1, -- sort_order
    JSON_ARRAY(
        JSON_OBJECT('name', 'CE', 'year', 2010),
        JSON_OBJECT('name', 'ISO 9001', 'year', 2012)
    ), -- certifications
    JSON_OBJECT(
        'contact_phone', '0216 755 4 555',
        'contact_email', 'info@ixtif.com',
        'services', JSON_ARRAY('new_sales', 'second_hand', 'rental', 'technical_service', 'spare_parts')
    ), -- metadata
    NOW(),
    NOW()
) ON DUPLICATE KEY UPDATE
    title = VALUES(title),
    description = VALUES(description),
    updated_at = NOW();

-- ============================================
-- 2. CATEGORY - FORKLİFTLER (category_id = 163)
-- ============================================
-- Ana kategori veritabanında zaten mevcut
-- Sadece güncelleme yapıyoruz
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
    sort_order,
    show_in_menu,
    show_in_homepage,
    created_at,
    updated_at
) VALUES (
    163, -- category_id (FORKLİFTLER)
    NULL, -- parent_id (ana kategori)
    JSON_OBJECT('tr', 'FORKLİFTLER', 'en', 'FORKLIFTS'),
    JSON_OBJECT('tr', 'forkli̇ftler', 'en', 'forklifts'),
    JSON_OBJECT(
        'tr', 'Elektrikli ve dizel forkliftler. Deponuzun güçlü yardımcıları! 1.5 tondan 5 tona kadar geniş yük kapasitesi.',
        'en', 'Electric and diesel forklifts. The powerful helpers of your warehouse! Wide load capacity from 1.5 tons to 5 tons.'
    ),
    'fa-solid fa-truck-loading',
    1, -- level
    '163', -- path
    1, -- sort_order
    1, -- is_active
    1, -- is_featured
    1, -- sort_order
    1, -- show_in_menu
    1, -- show_in_homepage
    NOW(),
    NOW()
) ON DUPLICATE KEY UPDATE
    title = VALUES(title),
    updated_at = NOW();

-- ============================================
-- 3. PRODUCTS
-- ============================================

-- ============================================
-- PRODUCT 1: CPD15TVL (1500 kg)
-- ============================================
INSERT INTO shop_products (
    product_id,
    category_id,
    brand_id,
    sku,
    model_number,
    barcode,
    title,
    slug,
    short_description,
    long_description,
    features,
    technical_specs,
    highlighted_features,
    base_price,
    compare_at_price,
    cost_price,
    currency,
    price_on_request,
    installment_available,
    max_installments,
    deposit_required,
    deposit_amount,
    deposit_percentage,
    weight,
    dimensions,
    stock_tracking,
    current_stock,
    low_stock_threshold,
    allow_backorder,
    lead_time_days,
    condition,
    product_type,
    is_active,
    is_featured,
    sort_order,
    is_bestseller,
    view_count,
    sales_count,
    published_at,
    warranty_info,
    tags,
    media_gallery,
    created_at,
    updated_at
) VALUES (
    1001, -- product_id
    163, -- category_id (FORKLİFTLER)
    1, -- brand_id (İXTİF)
    NULL, -- parent_product_id
    'CPD15TVL', -- sku
    'CPD15TVL', -- model_number
    NULL, -- barcode
    JSON_OBJECT(
        'tr', 'CPD15TVL - 1.5 Ton Kompakt Elektrikli Forklift',
        'en', 'CPD15TVL - 1.5 Ton Compact Electric Forklift'
    ), -- title
    JSON_OBJECT('tr', 'cpd15tvl-1-5-ton-kompakt-elektrikli-forklift', 'en', 'cpd15tvl-1-5-ton-compact-electric-forklift'), -- slug
    JSON_OBJECT(
        'tr', 'Dar alanlarda bile rahatça manevra yapabileceğiniz, günde sadece bir kez şarj ederek 6 saat kesintisiz çalışan, işletmenizin verimliliğini artıracak akıllı elektrikli forklift. 3.5 metrelik dar koridorlarda bile ferah çalışır!',
        'en', 'Smart electric forklift that can maneuver comfortably even in narrow spaces, works 6 hours continuously with just one charge per day, and will increase your business efficiency. Works comfortably even in narrow corridors of 3.5 meters!'
    ), -- short_description
    JSON_OBJECT(
        'tr', 'Deponuzda alan sıkıntısı mı çekiyorsunuz? Dar koridorlarda manevra yapmak zorunda mı kalıyorsunuz?

CPD15TVL, tam da bu sorunlara akıllı çözümler sunan bir elektrikli forklift. Kompakt 3 tekerlekli tasarımı sayesinde en sıkışık alanlarda bile verimli çalışır, sadece 1450mm dönüş yarıçapı ile 3.5 metre genişliğindeki koridorlarda rahatça hareket eder.

🔋 Gün Boyu Kesintisiz Çalışma
Sabah işe başladığınızda tek şarjla tam 6 saat çalışır. Lityum batarya teknolojisi sayesinde çok daha uzun ömürlü ve güvenilir. Ara şarj imkanı ile öğle molasında 30 dakika takıp günün tamamını kovalayabilirsiniz!

⚡ Güçlü Motor, Düşük Tüketim
Çift motorlu sistemi (2x5.0kW) sayesinde 1500 kg\'a kadar yükü kolayca taşır. Elektrikli motor olduğu için yakıt masrafı sıfır! Sadece elektrik faturanıza küçük bir ek, ama işletmenize büyük tasarruf.

👨‍💼 Operatör Dostu Tasarım
Geniş ayak alanı (394mm) ve ergonomik direksiyon sayesinde operatörünüz gün boyu rahat çalışır. Yorgunluk yok, verimlilik tam! Sessiz çalışır, kapalı alanlarda rahatsızlık vermez.

🏢 Her Türlü İşte Kullanabilirsiniz
İster lojistik depo, ister üretim tesisi, ister soğuk hava deposu olsun, CPD15TVL her ortamda güvenle çalışır. Palet taşıma, raf yükleme, kamyon yükleme... Ne işiniz varsa, bu forklift yanınızda!

✅ Garanti ve Servis Desteği
24 ay garanti ile gönül rahatlığıyla kullanın. Türkiye genelinde teknik servis ve yedek parça desteğimiz her zaman yanınızda. Hemen arayın: 0216 755 4 555

💼 Esnek Ödeme Seçenekleri
Sıfır, ikinci el veya kiralık - Bütçenize uygun çözümler sunuyoruz! Size özel fiyat teklifi için info@ixtif.com adresine mail atın.

İXTİF - Türkiye\'nin İstif Pazarı ile yatırımınızı geleceğe taşıyın!',
        'en', 'Are you experiencing space constraints in your warehouse? Do you have to maneuver in narrow corridors?

CPD15TVL is an electric forklift that offers smart solutions to these problems. Thanks to its compact 3-wheel design, it works efficiently even in the most cramped spaces, and with a turning radius of only 1450mm, it moves comfortably in corridors 3.5 meters wide.

🔋 All Day Continuous Operation
Works for 6 hours with a single charge when you start work in the morning. Much longer lasting and reliable thanks to lithium battery technology.

⚡ Powerful Motor, Low Consumption
Thanks to its dual motor system (2x5.0kW), it can easily carry loads up to 1500 kg. Zero fuel cost because it is an electric motor!

👨‍💼 Operator Friendly Design
Your operator works comfortably all day long thanks to the large legroom (394mm) and ergonomic steering wheel. No fatigue, full efficiency!'
    ), -- long_description
    JSON_OBJECT(
        'tr', JSON_ARRAY(
            '✅ Gün Boyu Kesintisiz Çalışma - Tek şarjla 6 saat çalışır, iş akışınızı durdurmaz',
            '✅ Güçlü ve Ekonomik - Çift motorlu sistem (2x5.0kW) ile güçlü performans, düşük elektrik tüketimi',
            '✅ Rahat Kullanım - Geniş ayak alanı (394mm) ve ergonomik tasarım sayesinde operatörünüz yorulmaz',
            '✅ Dar Alanlarda Üstün Manevra - Sadece 3.5m genişliğindeki koridorlarda bile rahatça çalışır (1450mm dönüş yarıçapı)',
            '✅ Pratik Şarj Sistemi - Normal 220V prize takıp şarj edebilirsiniz, özel şarj istasyonu gerekmez',
            '✅ Sessiz ve Çevre Dostu - Kapalı alanlarda rahatsızlık vermez, sıfır emisyon, temiz çalışma',
            '✅ Dayanıklı Solid Tekerlekler - Patlamayan tekerlekler, bakım masrafı yok'
        ),
        'en', JSON_ARRAY(
            '✅ All Day Continuous Operation - Works 6 hours with a single charge',
            '✅ Powerful and Economical - Powerful performance with dual motor system (2x5.0kW)',
            '✅ Comfortable Use - Large legroom (394mm) and ergonomic design',
            '✅ Superior Maneuverability in Narrow Spaces - Works comfortably in corridors only 3.5m wide',
            '✅ Practical Charging System - Can be charged by plugging into a normal 220V socket',
            '✅ Silent and Environmentally Friendly - Zero emission, clean operation',
            '✅ Durable Solid Tires - Non-puncture tires, no maintenance cost'
        )
    ), -- features
    JSON_OBJECT(
        'capacity', JSON_OBJECT(
            'load_capacity', JSON_OBJECT('value', 1500, 'unit', 'kg'),
            'load_center_distance', JSON_OBJECT('value', 500, 'unit', 'mm')
        ),
        'dimensions', JSON_OBJECT(
            'length_to_forks', JSON_OBJECT('value', 1813, 'unit', 'mm'),
            'overall_width', JSON_OBJECT('value', 1070, 'unit', 'mm'),
            'retracted_mast_height', JSON_OBJECT('value', 2075, 'unit', 'mm'),
            'lift_height', JSON_OBJECT('value', 3000, 'unit', 'mm'),
            'extended_mast_height', JSON_OBJECT('value', 4055, 'unit', 'mm'),
            'fork_dimensions', JSON_OBJECT('s', 40, 'e', 100, 'l', 920, 'unit', 'mm'),
            'turning_radius', JSON_OBJECT('value', 1450, 'unit', 'mm'),
            'aisle_width_1000x1200', JSON_OBJECT('value', 3175, 'unit', 'mm')
        ),
        'electrical', JSON_OBJECT(
            'voltage', JSON_OBJECT('value', 80, 'unit', 'V'),
            'battery_capacity', JSON_OBJECT('value', 150, 'unit', 'Ah'),
            'battery_type', 'Li-Ion',
            'battery_weight', JSON_OBJECT('value', 220, 'unit', 'kg'),
            'charger', '80V-35A single-phase integrated',
            'drive_motor_rating', JSON_OBJECT('value', 5.0, 'unit', 'kW', 'quantity', 2),
            'lift_motor_rating', JSON_OBJECT('value', 11, 'unit', 'kW')
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
        'weight', JSON_OBJECT(
            'service_weight', JSON_OBJECT('value', 2950, 'unit', 'kg')
        ),
        'wheels', JSON_OBJECT(
            'type', 'Solid',
            'front_size', '18X7-8',
            'rear_size', '15X4.5-8',
            'configuration', '2X/2'
        ),
        'other', JSON_OBJECT(
            'drive_type', 'Electric',
            'operator_type', 'Seated',
            'drive_control', 'AC',
            'steering', 'Hydraulic',
            'service_brake', 'Hydraulic',
            'parking_brake', 'Mechanical',
            'sound_level', JSON_OBJECT('value', 68, 'unit', 'dB(A)')
        )
    ), -- technical_specs
    JSON_ARRAY(
        JSON_OBJECT(
            'icon', 'battery-charging',
            'priority', 1,
            'title', JSON_OBJECT('tr', 'Gün Boyu Durmadan Çalışır', 'en', 'Works All Day Without Stopping'),
            'description', JSON_OBJECT(
                'tr', 'Sabah şarj edin, akşama kadar hiç takılma yapmasın. Lityum batarya teknolojisi ile uzun ömür garantili.',
                'en', 'Charge in the morning, no interruptions until evening. Long life guaranteed with lithium battery technology.'
            )
        ),
        JSON_OBJECT(
            'icon', 'bolt',
            'priority', 2,
            'title', JSON_OBJECT('tr', 'Ağır Yükler Artık Sorun Değil', 'en', 'Heavy Loads Are No Longer a Problem'),
            'description', JSON_OBJECT(
                'tr', 'Çift motorlu güç sistemi sayesinde 1500 kg\'ı oyuncak gibi kaldırır. Hem güçlü hem tasarruflu!',
                'en', 'Thanks to dual motor power system, lifts 1500 kg like a toy. Both powerful and economical!'
            )
        ),
        JSON_OBJECT(
            'icon', 'user-check',
            'priority', 3,
            'title', JSON_OBJECT('tr', 'Operatörünüz Yorulmadan Çalışır', 'en', 'Your Operator Works Without Getting Tired'),
            'description', JSON_OBJECT(
                'tr', 'Ferah kabin (394mm ayak alanı) ve ergonomik tasarım sayesinde gün boyu rahat. Mutlu çalışan = verimli iş!',
                'en', 'Spacious cabin (394mm legroom) and ergonomic design for all-day comfort. Happy worker = productive work!'
            )
        ),
        JSON_OBJECT(
            'icon', 'arrows-spin',
            'priority', 4,
            'title', JSON_OBJECT('tr', 'Dar Koridorlarda Ferah Çalışır', 'en', 'Works Comfortably in Narrow Corridors'),
            'description', JSON_OBJECT(
                'tr', 'Sadece 1450mm dönüş yarıçapı! 3.5 metre genişliğindeki koridorlarda rahatça manevra yapar.',
                'en', 'Only 1450mm turning radius! Maneuvers comfortably in corridors 3.5 meters wide.'
            )
        )
    ), -- highlighted_features
    NULL, -- base_price
    NULL, -- compare_at_price
    NULL, -- cost_price
    'TRY', -- currency
    1, -- price_on_request
    1, -- installment_available
    12, -- max_installments
    1, -- deposit_required
    NULL, -- deposit_amount
    30, -- deposit_percentage
    2950, -- weight (kg)
    JSON_OBJECT('length', 2733, 'width', 1070, 'height', 2078, 'unit', 'mm'), -- dimensions
    1, -- stock_tracking
    0, -- stock_quantity
    45, -- lead_time_days
    'new', -- condition
    'physical', -- product_type
    1, -- is_active
    1, -- is_featured
    1, -- sort_order
    1, -- is_bestseller
    1, -- is_new_arrival
    1, -- sort_order
    0, -- view_count
    0.00, -- rating_avg
    0, -- rating_count
    JSON_ARRAY('forklift', 'elektrikli', 'lityum', 'kompakt', '3-tekerli', '1.5-ton', 'dar-alan'), -- tags
    JSON_OBJECT(
        'tr', JSON_ARRAY(
            '🏭 Üretim Tesislerinde - Hammadde deposundan üretim hattına malzeme taşıma, sessiz çalıştığı için kimseyi rahatsız etmez',
            '📦 Lojistik Depolarda - Kamyonlardan gelen paletleri depo içine taşıma, dar koridorlar sorun olmaz',
            '❄️ Soğuk Hava Depolarında - Gıda ve ilaç depolamada güvenle kullanılır, egzoz gazı olmadığı için gıda güvenliği açısından ideal',
            '🏪 Perakende Mağazalarda - Mağaza arka alanında mal kabul, depo düzenleme, kompakt boyutu sayesinde dar depolarda rahat çalışır',
            '🏗️ İnşaat Şantiyelerinde - Kapalı alanlarda malzeme taşıma, sessiz ve emisyon yapmaz'
        ),
        'en', JSON_ARRAY(
            '🏭 In Production Facilities - Material transport from raw material warehouse to production line',
            '📦 In Logistics Warehouses - Transport pallets from trucks into warehouse, narrow corridors are no problem',
            '❄️ In Cold Storage - Safely used in food and pharmaceutical storage',
            '🏪 In Retail Stores - Goods receiving in store back area, compact size works well in narrow warehouses',
            '🏗️ In Construction Sites - Material transport in indoor areas, silent and emission-free'
        )
    ), -- use_cases
    JSON_OBJECT(
        'tr', JSON_ARRAY(
            '💰 Uzun Vadede Daha Ekonomik - Lityum batarya 5 yıl boyunca değişim gerektirmez, geleneksel bataryalarda 2-3 yılda binlerce lira ek maliyet',
            '⚡ Daha Güçlü, Daha Tasarruflu - Dizel forkliftlere göre %70 daha az enerji tüketir, yılda 25.000 TL yakıt tasarrufu',
            '🔧 Neredeyse Sıfır Bakım - Motor yağı, filtre, buji bakımı yok, senede sadece bir kez genel kontrol yeterli',
            '🌱 Çevre Dostu - Kapalı alanlarda egzoz gazı yok, çalışanlarınızın sağlığını korur',
            '📞 Türkiye Çapında Servis - Yedek parçalar depomuzda hazır, servis ekibimiz 48 saat içinde yanınızda (0216 755 4 555)'
        ),
        'en', JSON_ARRAY(
            '💰 More Economical in the Long Run - Lithium battery requires no replacement for 5 years',
            '⚡ More Powerful, More Economical - Consumes 70% less energy than diesel forklifts',
            '🔧 Almost Zero Maintenance - No engine oil, filter, spark plug maintenance',
            '🌱 Environmentally Friendly - No exhaust gas in closed areas',
            '📞 Turkey-Wide Service - Spare parts ready in our warehouse, service team at your side within 48 hours'
        )
    ), -- competitive_advantages
    JSON_OBJECT(
        'tr', JSON_ARRAY('Lojistik', 'Üretim', 'Gıda', 'İlaç', 'Perakende', 'İnşaat'),
        'en', JSON_ARRAY('Logistics', 'Manufacturing', 'Food', 'Pharmaceutical', 'Retail', 'Construction')
    ), -- target_industries
    JSON_ARRAY(
        JSON_OBJECT(
            'question', JSON_OBJECT('tr', 'Fiyat bilgisi alabilir miyim?', 'en', 'Can I get price information?'),
            'answer', JSON_OBJECT(
                'tr', 'Size özel fiyat teklifi için 0216 755 4 555 numaralı telefondan bizi arayabilir veya info@ixtif.com adresine mail atabilirsiniz. Ayrıca sıfır, ikinci el ve kiralık seçeneklerimiz de var!',
                'en', 'For a special price offer, you can call us at 0216 755 4 555 or email info@ixtif.com. We also have new, used and rental options!'
            )
        ),
        JSON_OBJECT(
            'question', JSON_OBJECT('tr', 'Kaç saat çalışır, sık sık şarj etmem gerekir mi?', 'en', 'How many hours does it work, do I need to charge it frequently?'),
            'answer', JSON_OBJECT(
                'tr', 'Tek şarjla 6 saat kesintisiz çalışır. Normal bir vardiya boyunca hiç şarj etmenize gerek kalmaz. İsterseniz öğle molasında 30 dakikalık hızlı şarj ile günün tamamını rahatça kovalarsınız!',
                'en', 'Works 6 hours continuously with a single charge. You don\'t need to charge during a normal shift. If you want, you can cover the whole day with a 30-minute fast charge during lunch break!'
            )
        ),
        JSON_OBJECT(
            'question', JSON_OBJECT('tr', 'Dar koridorlarda kullanabilir miyim?', 'en', 'Can I use it in narrow corridors?'),
            'answer', JSON_OBJECT(
                'tr', 'Kesinlikle! CPD15TVL tam da dar alanlar için tasarlandı. Sadece 3.5 metre genişliğindeki koridorlarda bile rahatça dönüş yapar. Kompakt boyutları sayesinde en sıkışık depolarda bile verimli çalışırsınız.',
                'en', 'Absolutely! CPD15TVL is designed specifically for narrow spaces. It turns easily even in corridors only 3.5 meters wide. Thanks to its compact dimensions, you work efficiently even in the most cramped warehouses.'
            )
        ),
        JSON_OBJECT(
            'question', JSON_OBJECT('tr', 'Garanti süresi ne kadar?', 'en', 'What is the warranty period?'),
            'answer', JSON_OBJECT(
                'tr', '24 ay garanti veriyoruz, hem de batarya dahil! Türkiye\'nin her yerinde yetkili servis noktamız var. Arıza durumunda 48 saat içinde teknik ekibimiz yanınızda. Yedek parça stokumuz her zaman hazır!',
                'en', 'We provide a 24-month warranty, including the battery! We have authorized service points all over Turkey. In case of failure, our technical team is with you within 48 hours. Our spare parts stock is always ready!'
            )
        ),
        JSON_OBJECT(
            'question', JSON_OBJECT('tr', 'Nerede şarj ederim? Özel elektrik gerekir mi?', 'en', 'Where do I charge it? Is special electricity required?'),
            'answer', JSON_OBJECT(
                'tr', 'Hayır! Normal 220V evsel prize takabilirsiniz. Özel bir elektrik tesisatına gerek yok. Entegre şarj cihazı sayesinde tıpkı telefonunuzu şarj eder gibi prize takıp bırakıyorsunuz. Sabaha hazır!',
                'en', 'No! You can plug it into a normal 220V household socket. No special electrical installation required. Thanks to the integrated charger, you just plug it in and leave it like charging your phone. Ready in the morning!'
            )
        ),
        JSON_OBJECT(
            'question', JSON_OBJECT('tr', 'Hangi şehirlere servis veriyorsunuz?', 'en', 'Which cities do you serve?'),
            'answer', JSON_OBJECT(
                'tr', 'Türkiye genelinde hizmet vermekteyiz. İstanbul, Ankara, İzmir başta olmak üzere tüm illere teslimat yapıyoruz. Detaylı bilgi için: 0216 755 4 555',
                'en', 'We serve all over Turkey. We deliver to all provinces, especially Istanbul, Ankara, and Izmir. For detailed information: 0216 755 4 555'
            )
        )
    ), -- faq_data
    JSON_ARRAY(
        JSON_OBJECT('type', 'image', 'url', 'products/cpd15tvl/main.jpg', 'is_primary', 1, 'sort_order', 1),
        JSON_OBJECT('type', 'image', 'url', 'products/cpd15tvl/side.jpg', 'is_primary', 0, 'sort_order', 2),
        JSON_OBJECT('type', 'image', 'url', 'products/cpd15tvl/operator.jpg', 'is_primary', 0, 'sort_order', 3),
        JSON_OBJECT('type', 'image', 'url', 'products/cpd15tvl/battery.jpg', 'is_primary', 0, 'sort_order', 4),
        JSON_OBJECT('type', 'pdf', 'url', 'products/cpd15tvl/brochure.pdf', 'is_primary', 0, 'sort_order', 5)
    ), -- media_gallery
    JSON_ARRAY('CPD18TVL', 'CPD20TVL'), -- related_products (SKU)
    JSON_ARRAY(), -- cross_sell_products
    JSON_ARRAY('CPD18TVL', 'CPD20TVL'), -- up_sell_products (SKU)
    JSON_OBJECT(
        'pdf_source', '02_CPD15-18-20TVL-EN-Brochure.pdf',
        'extraction_date', '2025-10-10',
        'wheel_count', 3,
        'drive_type', 'dual_drive',
        'battery_technology', 'li-ion'
    ), -- metadata
    NOW(), -- published_at
    NOW(), -- created_at
    NOW() -- updated_at
);

-- ============================================
-- PRODUCT 2: CPD18TVL (1800 kg)
-- ============================================
INSERT INTO shop_products (
    product_id,
    category_id,
    brand_id,
    sku,
    model_number,
    barcode,
    title,
    slug,
    short_description,
    long_description,
    features,
    technical_specs,
    highlighted_features,
    base_price,
    compare_at_price,
    cost_price,
    currency,
    price_on_request,
    installment_available,
    max_installments,
    deposit_required,
    deposit_amount,
    deposit_percentage,
    weight,
    dimensions,
    stock_tracking,
    current_stock,
    low_stock_threshold,
    allow_backorder,
    lead_time_days,
    condition,
    product_type,
    is_active,
    is_featured,
    sort_order,
    is_bestseller,
    view_count,
    sales_count,
    published_at,
    warranty_info,
    tags,
    media_gallery,
    created_at,
    updated_at
) VALUES (
    1002, -- product_id
    163, -- category_id (FORKLİFTLER)
    1, -- brand_id (İXTİF)
    NULL, -- parent_product_id
    'CPD18TVL', -- sku
    'CPD18TVL', -- model_number
    'CPD TVL Series', -- series_name
    JSON_OBJECT(
        'tr', 'CPD18TVL - 1.8 Ton Kompakt Elektrikli Forklift',
        'en', 'CPD18TVL - 1.8 Ton Compact Electric Forklift'
    ), -- title
    JSON_OBJECT('tr', 'cpd18tvl-1-8-ton-kompakt-elektrikli-forklift', 'en', 'cpd18tvl-1-8-ton-compact-electric-forklift'), -- slug
    JSON_OBJECT(
        'tr', 'Orta tonajlı yükleriniz için ideal güç! 1.8 ton taşıma kapasitesi, gün boyu kesintisiz çalışma ve dar koridorlarda ferah manevra. Lityum batarya teknolojisi ile uzun ömürlü, ekonomik ve çevre dostu çalışma.',
        'en', 'Ideal power for your medium tonnage loads! 1.8 ton carrying capacity, all-day continuous operation and comfortable maneuvering in narrow corridors. Long-lasting, economical and environmentally friendly operation with lithium battery technology.'
    ), -- short_description
    JSON_OBJECT(
        'tr', 'İşletmeniz büyüdükçe taşıma ihtiyaçlarınız da artıyor değil mi? 1.5 ton yetersiz kalıyor ama daha büyük forkliftin dar koridorlarda çalışmasını istemiyorsunuz?

CPD18TVL tam size göre! 1.8 ton taşıma kapasitesi ile orta tonajlı yüklerinizi kolayca kaldırırken, kompakt tasarımı sayesinde dar alanlarda rahat çalışmaya devam eder.

🔋 Güçlü Lityum Batarya ile 6 Saat Çalışma
205Ah kapasiteli lityum batarya ile sabahtan akşama kadar hiç durmadan çalışır. Ara şarj imkanı sayesinde ihtiyaç halinde öğle molasında 30 dakika şarj ile günü tamamlayabilirsiniz.

⚡ Daha Fazla Güç, Aynı Verim
Çift motorlu 2x5.0kW güç sistemi sayesinde 1.8 ton yükü oyuncak gibi kaldırır. Elektrikli motor olduğu için yakıt masrafı sıfır, sadece minimal elektrik tüketimi!

👨‍💼 Ergonomik ve Konforlu
Geniş operatör kabini (394mm ayak alanı), ayarlanabilir direksiyon ve konforlu koltuk ile operatörünüz gün boyu yorulmadan çalışır. Sessiz çalışma özelliği sayesinde kapalı alanlarda ideal.

🔄 Orta Boy, Maksimum Verimlilik
1550mm dönüş yarıçapı ile 3.5m genişliğindeki dar koridorlarda rahatça manevra yapar. Ne çok büyük ne çok küçük - tam dengeli!

🏢 Geniş Kullanım Alanı
Lojistik depo, üretim tesisi, soğuk hava deposu, perakende mağaza... CPD18TVL her ortamda güvenle çalışır. Orta tonajlı yükler için en verimli çözüm!

✅ Tam Garanti ve Destek
24 ay garantili, Türkiye genelinde servis ağımız var. Yedek parça ve teknik destek için: 0216 755 4 555

💼 Size Özel Fiyat Teklifi
Sıfır, ikinci el veya kiralık seçeneklerimiz mevcut. Bütçenize en uygun çözümü birlikte bulalım: info@ixtif.com

İXTİF - Türkiye\'nin İstif Pazarı ile işlerinizi büyütün!',
        'en', 'As your business grows, your transportation needs increase, right? 1.5 tons is not enough but you don\'t want a larger forklift to work in narrow corridors?

CPD18TVL is just for you! While easily lifting your medium tonnage loads with 1.8 ton carrying capacity, it continues to work comfortably in narrow spaces thanks to its compact design.

🔋 6 Hours of Operation with Powerful Lithium Battery
Works non-stop from morning to evening with 205Ah capacity lithium battery.

⚡ More Power, Same Efficiency
Thanks to dual motor 2x5.0kW power system, it lifts 1.8 ton load like a toy. Zero fuel cost because it is electric motor!'
    ), -- long_description
    JSON_OBJECT(
        'tr', JSON_ARRAY(
            '✅ Orta Tonaj Uzmanı - 1.8 ton taşıma kapasitesi ile büyüyen işletmenizin ihtiyaçlarına cevap verir',
            '✅ Gün Boyu Kesintisiz - 205Ah lityum batarya ile 6 saat durmadan çalışır',
            '✅ Güçlü Performans - Çift motorlu 2x5.0kW sistem ile yüksek verimlilik',
            '✅ Dengeli Boyut - 1550mm dönüş yarıçapı ile dar koridorlarda (3.5m) rahat çalışır',
            '✅ Kolay Şarj - Normal 220V prize takıp şarj edebilirsiniz',
            '✅ Operatör Konforu - 394mm geniş ayak alanı ve ergonomik tasarım',
            '✅ Sessiz ve Temiz - Kapalı alanlarda rahatsızlık vermez, sıfır emisyon'
        ),
        'en', JSON_ARRAY(
            '✅ Medium Tonnage Expert - 1.8 ton carrying capacity',
            '✅ All Day Continuous - 6 hours non-stop with 205Ah lithium battery',
            '✅ Powerful Performance - High efficiency with dual motor 2x5.0kW system',
            '✅ Balanced Size - Comfortable working in narrow corridors with 1550mm turning radius',
            '✅ Easy Charging - Can be charged by plugging into normal 220V socket',
            '✅ Operator Comfort - 394mm wide legroom and ergonomic design',
            '✅ Silent and Clean - No disturbance in closed areas, zero emission'
        )
    ), -- features
    JSON_OBJECT(
        'capacity', JSON_OBJECT(
            'load_capacity', JSON_OBJECT('value', 1800, 'unit', 'kg'),
            'load_center_distance', JSON_OBJECT('value', 500, 'unit', 'mm')
        ),
        'dimensions', JSON_OBJECT(
            'length_to_forks', JSON_OBJECT('value', 1913, 'unit', 'mm'),
            'overall_width', JSON_OBJECT('value', 1070, 'unit', 'mm'),
            'retracted_mast_height', JSON_OBJECT('value', 2075, 'unit', 'mm'),
            'lift_height', JSON_OBJECT('value', 3000, 'unit', 'mm'),
            'extended_mast_height', JSON_OBJECT('value', 4055, 'unit', 'mm'),
            'fork_dimensions', JSON_OBJECT('s', 40, 'e', 100, 'l', 920, 'unit', 'mm'),
            'turning_radius', JSON_OBJECT('value', 1550, 'unit', 'mm'),
            'aisle_width_1000x1200', JSON_OBJECT('value', 3275, 'unit', 'mm')
        ),
        'electrical', JSON_OBJECT(
            'voltage', JSON_OBJECT('value', 80, 'unit', 'V'),
            'battery_capacity', JSON_OBJECT('value', 205, 'unit', 'Ah'),
            'battery_type', 'Li-Ion',
            'battery_weight', JSON_OBJECT('value', 185, 'unit', 'kg'),
            'charger', '80V-35A single-phase integrated',
            'drive_motor_rating', JSON_OBJECT('value', 5.0, 'unit', 'kW', 'quantity', 2),
            'lift_motor_rating', JSON_OBJECT('value', 11, 'unit', 'kW')
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
        'weight', JSON_OBJECT(
            'service_weight', JSON_OBJECT('value', 3269, 'unit', 'kg')
        ),
        'wheels', JSON_OBJECT(
            'type', 'Solidrubber',
            'front_size', '18X7-8',
            'rear_size', '140/55-9',
            'configuration', '2X/2'
        ),
        'other', JSON_OBJECT(
            'drive_type', 'Electric',
            'operator_type', 'Seated',
            'drive_control', 'AC',
            'steering', 'Hydraulic',
            'service_brake', 'Hydraulic',
            'parking_brake', 'Mechanical',
            'sound_level', JSON_OBJECT('value', 70, 'unit', 'dB(A)')
        )
    ), -- technical_specs
    JSON_ARRAY(
        JSON_OBJECT(
            'icon', 'weight-scale',
            'priority', 1,
            'title', JSON_OBJECT('tr', 'Orta Tonaj İçin İdeal', 'en', 'Ideal for Medium Tonnage'),
            'description', JSON_OBJECT(
                'tr', '1.8 ton taşıma kapasitesi ile büyüyen işletmenizin ihtiyaçlarına tam cevap. Ne çok küçük ne çok büyük - tam dengeli!',
                'en', '1.8 ton carrying capacity is the perfect answer to your growing business needs. Not too small, not too big - perfectly balanced!'
            )
        ),
        JSON_OBJECT(
            'icon', 'battery-full',
            'priority', 2,
            'title', JSON_OBJECT('tr', 'Güçlü Lityum Enerji', 'en', 'Powerful Lithium Energy'),
            'description', JSON_OBJECT(
                'tr', '205Ah kapasiteli lityum batarya ile gün boyu kesintisiz çalışma. Ara şarj imkanı ile esneklik!',
                'en', 'All-day continuous operation with 205Ah lithium battery. Flexibility with fast charging!'
            )
        ),
        JSON_OBJECT(
            'icon', 'route',
            'priority', 3,
            'title', JSON_OBJECT('tr', 'Dengeli Manevra Kabiliyeti', 'en', 'Balanced Maneuverability'),
            'description', JSON_OBJECT(
                'tr', '1550mm dönüş yarıçapı ile dar koridorlarda (3.5m) rahat çalışır. Orta boy, maksimum verim!',
                'en', 'Comfortable working in narrow corridors (3.5m) with 1550mm turning radius. Medium size, maximum efficiency!'
            )
        ),
        JSON_OBJECT(
            'icon', 'leaf',
            'priority', 4,
            'title', JSON_OBJECT('tr', 'Çevre Dostu Teknoloji', 'en', 'Environmentally Friendly Technology'),
            'description', JSON_OBJECT(
                'tr', 'Sıfır emisyon, sessiz çalışma. Kapalı alanlarda ideal, çalışan sağlığını korur!',
                'en', 'Zero emission, silent operation. Ideal for closed areas, protects employee health!'
            )
        )
    ), -- highlighted_features
    NULL, -- base_price
    NULL, -- compare_at_price
    NULL, -- cost_price
    'TRY', -- currency
    1, -- price_on_request
    1, -- installment_available
    12, -- max_installments
    1, -- deposit_required
    NULL, -- deposit_amount
    30, -- deposit_percentage
    3269, -- weight (kg)
    JSON_OBJECT('length', 2833, 'width', 1100, 'height', 2078, 'unit', 'mm'), -- dimensions
    1, -- stock_tracking
    0, -- stock_quantity
    45, -- lead_time_days
    'new', -- condition
    'physical', -- product_type
    1, -- is_active
    1, -- is_featured
    1, -- sort_order
    1, -- is_bestseller
    1, -- is_new_arrival
    2, -- sort_order
    0, -- view_count
    0.00, -- rating_avg
    0, -- rating_count
    JSON_ARRAY('forklift', 'elektrikli', 'lityum', 'kompakt', '1.8-ton', 'orta-tonaj'), -- tags
    JSON_OBJECT(
        'tr', JSON_ARRAY(
            '🏭 Orta Ölçekli Üretim Tesisleri - Artan üretim hacmi ile birlikte daha fazla taşıma kapasitesi gereken işletmeler için ideal',
            '📦 Büyüyen Lojistik Depoları - 1.5 ton yetersiz kalıyor ama dev forklift istemiyorsanız CPD18TVL tam size göre',
            '🏪 Büyük Perakende Mağazalar - Arka alan operasyonlarında orta tonajlı yükler için mükemmel denge',
            '❄️ Soğuk Hava Depoları - Orta kapasiteli paletli yüklerin taşınması için ideal, sessiz ve temiz çalışma',
            '🚢 Liman ve Terminal - İç alan operasyonlarında orta tonajlı konteynerlerin aktarılması'
        ),
        'en', JSON_ARRAY(
            '🏭 Medium Scale Production Facilities - Ideal for businesses requiring more carrying capacity with increasing production volume',
            '📦 Growing Logistics Warehouses - If 1.5 ton is not enough but you don\'t want a huge forklift, CPD18TVL is for you',
            '🏪 Large Retail Stores - Perfect balance for medium tonnage loads in back area operations',
            '❄️ Cold Storage - Ideal for transporting medium capacity pallet loads, quiet and clean operation',
            '🚢 Port and Terminal - Transfer of medium tonnage containers in indoor operations'
        )
    ), -- use_cases
    JSON_OBJECT(
        'tr', JSON_ARRAY(
            '💰 Tam Dengeli Yatırım - 1.5 ton küçük kalıyor, 2 ton fazla geliyor? CPD18TVL tam ihtiyacınız kadar!',
            '⚡ Güç ve Verimlilik - Aynı kompakt tasarım, %20 daha fazla taşıma kapasitesi',
            '🔧 Kolay Bakım - Elektrikli motor olduğu için bakım masrafı minimum',
            '🌱 Sürdürülebilir - Lityum batarya 5 yıl ömürlü, çevre dostu çalışma',
            '📞 7/24 Destek - İXTİF servis ağı ile her zaman yanınızdayız: 0216 755 4 555'
        ),
        'en', JSON_ARRAY(
            '💰 Perfectly Balanced Investment - 1.5 ton is too small, 2 ton is too much? CPD18TVL is just what you need!',
            '⚡ Power and Efficiency - Same compact design, 20% more carrying capacity',
            '🔧 Easy Maintenance - Minimum maintenance cost because it is electric motor',
            '🌱 Sustainable - Lithium battery has 5 years life, environmentally friendly operation',
            '📞 24/7 Support - We are always with you with iXTiF service network: 0216 755 4 555'
        )
    ), -- competitive_advantages
    JSON_OBJECT(
        'tr', JSON_ARRAY('Lojistik', 'Üretim', 'Gıda', 'Perakende', 'Liman', 'Terminal'),
        'en', JSON_ARRAY('Logistics', 'Manufacturing', 'Food', 'Retail', 'Port', 'Terminal')
    ), -- target_industries
    JSON_ARRAY(
        JSON_OBJECT(
            'question', JSON_OBJECT('tr', 'CPD15TVL ile CPD18TVL arasındaki fark nedir?', 'en', 'What is the difference between CPD15TVL and CPD18TVL?'),
            'answer', JSON_OBJECT(
                'tr', 'Temel fark taşıma kapasitesinde: CPD15TVL 1.5 ton, CPD18TVL ise 1.8 ton taşır. CPD18TVL ayrıca biraz daha büyük bataryaya (205Ah) sahip ve dönüş yarıçapı 100mm daha geniş (1550mm). İhtiyacınıza göre seçim yapabilirsiniz, detaylı karşılaştırma için 0216 755 4 555',
                'en', 'The main difference is in carrying capacity: CPD15TVL carries 1.5 tons, CPD18TVL carries 1.8 tons. CPD18TVL also has a slightly larger battery (205Ah) and a turning radius 100mm wider (1550mm).'
            )
        ),
        JSON_OBJECT(
            'question', JSON_OBJECT('tr', 'Hangi işletmeler için daha uygun?', 'en', 'Which businesses is it more suitable for?'),
            'answer', JSON_OBJECT(
                'tr', 'Orta ölçekli ve büyüyen işletmeler için ideal. Özellikle günlük 1.5-1.8 ton arası yükler taşıyorsanız CPD18TVL tam size göre. 1.5 ton küçük kalıyor ama 2 ton fazla geliyor diyorsanız, bu model tam dengeli çözüm!',
                'en', 'Ideal for medium-sized and growing businesses. Especially if you carry loads between 1.5-1.8 tons daily, CPD18TVL is just for you.'
            )
        ),
        JSON_OBJECT(
            'question', JSON_OBJECT('tr', 'Fiyat bilgisi alabilir miyim?', 'en', 'Can I get price information?'),
            'answer', JSON_OBJECT(
                'tr', 'Size özel fiyat teklifi için 0216 755 4 555 numaralı telefondan bizi arayabilir veya info@ixtif.com adresine mail atabilirsiniz. Sıfır, ikinci el ve kiralık seçeneklerimiz mevcut!',
                'en', 'For a special price offer, you can call us at 0216 755 4 555 or email info@ixtif.com. We have new, used and rental options!'
            )
        ),
        JSON_OBJECT(
            'question', JSON_OBJECT('tr', 'Garanti kapsamı nedir?', 'en', 'What is the warranty coverage?'),
            'answer', JSON_OBJECT(
                'tr', '24 ay tam garanti, batarya dahil! Türkiye genelinde yetkili servis ağımız var. Yedek parça desteği ve 48 saat içinde teknik müdahale garantisi veriyoruz.',
                'en', '24 months full warranty, including battery! We have authorized service network throughout Turkey.'
            )
        )
    ), -- faq_data
    JSON_ARRAY(
        JSON_OBJECT('type', 'image', 'url', 'products/cpd18tvl/main.jpg', 'is_primary', 1, 'sort_order', 1),
        JSON_OBJECT('type', 'image', 'url', 'products/cpd18tvl/side.jpg', 'is_primary', 0, 'sort_order', 2),
        JSON_OBJECT('type', 'image', 'url', 'products/cpd18tvl/operator.jpg', 'is_primary', 0, 'sort_order', 3),
        JSON_OBJECT('type', 'pdf', 'url', 'products/cpd18tvl/brochure.pdf', 'is_primary', 0, 'sort_order', 4)
    ), -- media_gallery
    JSON_ARRAY('CPD15TVL', 'CPD20TVL'), -- related_products
    JSON_ARRAY(), -- cross_sell_products
    JSON_ARRAY('CPD20TVL'), -- up_sell_products
    JSON_OBJECT(
        'pdf_source', '02_CPD15-18-20TVL-EN-Brochure.pdf',
        'extraction_date', '2025-10-10',
        'wheel_count', 3,
        'drive_type', 'dual_drive',
        'battery_technology', 'li-ion'
    ), -- metadata
    NOW(),
    NOW(),
    NOW()
);

-- ============================================
-- PRODUCT 3: CPD20TVL (2000 kg)
-- ============================================
INSERT INTO shop_products (
    product_id,
    category_id,
    brand_id,
    sku,
    model_number,
    barcode,
    title,
    slug,
    short_description,
    long_description,
    features,
    technical_specs,
    highlighted_features,
    base_price,
    compare_at_price,
    cost_price,
    currency,
    price_on_request,
    installment_available,
    max_installments,
    deposit_required,
    deposit_amount,
    deposit_percentage,
    weight,
    dimensions,
    stock_tracking,
    current_stock,
    low_stock_threshold,
    allow_backorder,
    lead_time_days,
    condition,
    product_type,
    is_active,
    is_featured,
    sort_order,
    is_bestseller,
    view_count,
    sales_count,
    published_at,
    warranty_info,
    tags,
    media_gallery,
    created_at,
    updated_at
) VALUES (
    1003, -- product_id
    163, -- category_id (FORKLİFTLER)
    1, -- brand_id (İXTİF)
    NULL, -- parent_product_id
    'CPD20TVL', -- sku
    'CPD20TVL', -- model_number
    'CPD TVL Series', -- series_name
    JSON_OBJECT(
        'tr', 'CPD20TVL - 2 Ton Kompakt Elektrikli Forklift',
        'en', 'CPD20TVL - 2 Ton Compact Electric Forklift'
    ), -- title
    JSON_OBJECT('tr', 'cpd20tvl-2-ton-kompakt-elektrikli-forklift', 'en', 'cpd20tvl-2-ton-compact-electric-forklift'), -- slug
    JSON_OBJECT(
        'tr', 'Maksimum güç, minimum boyut! 2 ton taşıma kapasitesi ile ağır yüklerinizi kolayca kaldırın. Kompakt 3 tekerlekli tasarım sayesinde dar koridorlarda bile ferah çalışın. Lityum batarya ile gün boyu kesintisiz verimlilik!',
        'en', 'Maximum power, minimum size! Easily lift your heavy loads with 2 ton carrying capacity. Work comfortably even in narrow corridors thanks to compact 3-wheel design. All-day continuous productivity with lithium battery!'
    ), -- short_description
    JSON_OBJECT(
        'tr', 'Ağır yükler mi taşıyorsunuz? Dar alanlarınız mı var ama güçlü bir forklift mi gerekiyor?

CPD20TVL serinin en güçlüsü! Tam 2 ton taşıma kapasitesi ile büyük paletlerinizi, ağır yüklerinizi kolayca kaldırırken, kompakt 3 tekerlekli tasarımı sayesinde dar koridorlarda rahatça manevra yapar.

🏋️ Serinin En Güçlüsü - 2 Ton Kapasite
Tam 2000 kg yük taşıma kapasitesi! Ağır paletler, büyük kutular, yoğun yükler artık sorun değil. Çift motorlu güç sistemi (2x5.0kW) ile her yükü oyuncak gibi kaldırır.

🔋 Güçlü Lityum Enerji - 205Ah Kapasite
Sabahtan akşama kadar hiç durmadan çalışır. Ara şarj imkanı sayesinde öğle molasında 30 dakika takıp günün tamamını kovalayabilirsiniz. Lityum batarya teknolojisi ile 5 yıl boyunca batarya değiştirmenize gerek yok!

📏 Kompakt Ama Güçlü
Sadece 1585mm dönüş yarıçapı ile 3.5m genişliğindeki dar koridorlarda bile rahatça çalışır. 2 ton kapasiteye sahip forklifter arasında en kompakt model!

👨‍💼 Operatör Konforu ve Güvenlik
Geniş operatör kabini (394mm ayak alanı), ayarlanabilir direksiyon, konforlu koltuk ve mükemmel görüş açısı. Operatörünüz yorulmadan, güvenli şekilde çalışır.

⚡ Ekonomik İşletme
Elektrikli motor sayesinde yakıt masrafı sıfır! Dizel forkliftlere göre yılda 30.000 TL\'ye kadar tasarruf. Bakım masrafları minimal, sadece yıllık genel kontrol yeterli.

🏢 Ağır İşler İçin İdeal
Büyük lojistik depoları, üretim tesisleri, inşaat şantiyeleri, liman operasyonları... CPD20TVL ağır tonajlı işleriniz için tasarlandı.

✅ Tam Garanti ve Servis
24 ay garantili, batarya dahil! Türkiye genelinde yetkili servis ağımız ve 7/24 teknik destek hattımız: 0216 755 4 555

💼 Esnek Çözümler
Sıfır, ikinci el veya kiralık seçeneklerimiz mevcut. Taksit imkanları ve özel kampanyalar için: info@ixtif.com

İXTİF - Türkiye\'nin İstif Pazarı ile en ağır işlerinizi kolaylaştırın!',
        'en', 'Do you carry heavy loads? Do you have narrow spaces but need a powerful forklift?

CPD20TVL is the most powerful of the series! While easily lifting your large pallets and heavy loads with a full 2 ton carrying capacity, it maneuvers comfortably in narrow corridors thanks to its compact 3-wheel design.

🏋️ The Most Powerful of the Series - 2 Ton Capacity
Full 2000 kg load carrying capacity! Heavy pallets, large boxes, heavy loads are no longer a problem.'
    ), -- long_description
    JSON_OBJECT(
        'tr', JSON_ARRAY(
            '✅ Maksimum Güç - 2 ton (2000 kg) taşıma kapasitesi ile en ağır yüklerinizi kolayca kaldırır',
            '✅ Kompakt Tasarım - 1585mm dönüş yarıçapı ile dar koridorlarda (3.5m) rahat çalışma',
            '✅ Güçlü Lityum Batarya - 205Ah kapasite ile gün boyu kesintisiz çalışma',
            '✅ Çift Motor Gücü - 2x5.0kW motor sistemi ile yüksek performans',
            '✅ Geniş Operatör Kabini - 394mm ayak alanı, ergonomik tasarım, yorulmadan çalışma',
            '✅ Ekonomik İşletme - Sıfır yakıt maliyeti, minimal bakım, yılda 30.000 TL tasarruf',
            '✅ Sessiz ve Temiz - Kapalı alanlarda ideal, sıfır emisyon, çevre dostu'
        ),
        'en', JSON_ARRAY(
            '✅ Maximum Power - 2 ton (2000 kg) carrying capacity easily lifts your heaviest loads',
            '✅ Compact Design - Comfortable working in narrow corridors (3.5m) with 1585mm turning radius',
            '✅ Powerful Lithium Battery - All-day continuous operation with 205Ah capacity',
            '✅ Dual Motor Power - High performance with 2x5.0kW motor system',
            '✅ Wide Operator Cabin - 394mm legroom, ergonomic design, work without fatigue',
            '✅ Economical Operation - Zero fuel cost, minimal maintenance, 30,000 TL annual savings',
            '✅ Silent and Clean - Ideal for closed areas, zero emission, environmentally friendly'
        )
    ), -- features
    JSON_OBJECT(
        'capacity', JSON_OBJECT(
            'load_capacity', JSON_OBJECT('value', 2000, 'unit', 'kg'),
            'load_center_distance', JSON_OBJECT('value', 500, 'unit', 'mm')
        ),
        'dimensions', JSON_OBJECT(
            'length_to_forks', JSON_OBJECT('value', 1950, 'unit', 'mm'),
            'overall_width', JSON_OBJECT('value', 1170, 'unit', 'mm'),
            'retracted_mast_height', JSON_OBJECT('value', 2075, 'unit', 'mm'),
            'lift_height', JSON_OBJECT('value', 3000, 'unit', 'mm'),
            'extended_mast_height', JSON_OBJECT('value', 4055, 'unit', 'mm'),
            'fork_dimensions', JSON_OBJECT('s', 40, 'e', 122, 'l', 1070, 'unit', 'mm'),
            'turning_radius', JSON_OBJECT('value', 1585, 'unit', 'mm'),
            'aisle_width_1000x1200', JSON_OBJECT('value', 3315, 'unit', 'mm')
        ),
        'electrical', JSON_OBJECT(
            'voltage', JSON_OBJECT('value', 80, 'unit', 'V'),
            'battery_capacity', JSON_OBJECT('value', 205, 'unit', 'Ah'),
            'battery_type', 'Li-Ion',
            'battery_weight', JSON_OBJECT('value', 185, 'unit', 'kg'),
            'charger', '80V-35A single-phase integrated',
            'drive_motor_rating', JSON_OBJECT('value', 5.0, 'unit', 'kW', 'quantity', 2),
            'lift_motor_rating', JSON_OBJECT('value', 11, 'unit', 'kW')
        ),
        'performance', JSON_OBJECT(
            'travel_speed_laden', JSON_OBJECT('value', 13, 'unit', 'km/h'),
            'travel_speed_unladen', JSON_OBJECT('value', 14, 'unit', 'km/h'),
            'lifting_speed_laden', JSON_OBJECT('value', 0.3, 'unit', 'm/s'),
            'lifting_speed_unladen', JSON_OBJECT('value', 0.4, 'unit', 'm/s'),
            'lowering_speed_laden', JSON_OBJECT('value', 0.38, 'unit', 'm/s'),
            'lowering_speed_unladen', JSON_OBJECT('value', 0.4, 'unit', 'm/s'),
            'max_gradeability_laden', JSON_OBJECT('value', 10, 'unit', '%'),
            'max_gradeability_unladen', JSON_OBJECT('value', 15, 'unit', '%')
        ),
        'weight', JSON_OBJECT(
            'service_weight', JSON_OBJECT('value', 3429, 'unit', 'kg')
        ),
        'wheels', JSON_OBJECT(
            'type', 'Solidrubber',
            'front_size', '200/50-10',
            'rear_size', '140/55-9',
            'configuration', '2X/2'
        ),
        'other', JSON_OBJECT(
            'drive_type', 'Electric',
            'operator_type', 'Seated',
            'drive_control', 'AC',
            'steering', 'Hydraulic',
            'service_brake', 'Hydraulic',
            'parking_brake', 'Mechanical',
            'sound_level', JSON_OBJECT('value', 74, 'unit', 'dB(A)')
        )
    ), -- technical_specs
    JSON_ARRAY(
        JSON_OBJECT(
            'icon', 'dumbbell',
            'priority', 1,
            'title', JSON_OBJECT('tr', 'Serinin En Güçlüsü', 'en', 'The Most Powerful of the Series'),
            'description', JSON_OBJECT(
                'tr', 'Tam 2 ton (2000 kg) taşıma kapasitesi! Ağır paletler, büyük yükler artık sorun değil. TVL serisinin gücüne güç!',
                'en', 'Full 2 ton (2000 kg) carrying capacity! Heavy pallets, big loads are no longer a problem. The power of TVL series!'
            )
        ),
        JSON_OBJECT(
            'icon', 'compress',
            'priority', 2,
            'title', JSON_OBJECT('tr', 'Kompakt Ama Çok Güçlü', 'en', 'Compact But Very Powerful'),
            'description', JSON_OBJECT(
                'tr', '2 ton kapasiteye sahip en kompakt forklift! 1585mm dönüş yarıçapı ile dar koridorlarda ferah çalışır.',
                'en', 'The most compact forklift with 2 ton capacity! Works comfortably in narrow corridors with 1585mm turning radius.'
            )
        ),
        JSON_OBJECT(
            'icon', 'coins',
            'priority', 3,
            'title', JSON_OBJECT('tr', 'Yılda 30.000 TL Tasarruf', 'en', '30,000 TL Annual Savings'),
            'description', JSON_OBJECT(
                'tr', 'Dizel forkliftlere göre yılda 30.000 TL\'ye kadar yakıt tasarrufu. Elektrik çok daha ekonomik!',
                'en', 'Up to 30,000 TL annual fuel savings compared to diesel forklifts. Electricity is much more economical!'
            )
        ),
        JSON_OBJECT(
            'icon', 'shield-check',
            'priority', 4,
            'title', JSON_OBJECT('tr', 'Ağır İşlere Dayanıklı', 'en', 'Durable for Heavy Jobs'),
            'description', JSON_OBJECT(
                'tr', 'Yoğun kullanım için tasarlandı. Güçlendiri lmiş mast, solid tekerlekler, dayanıklı yapı!',
                'en', 'Designed for heavy-duty use. Reinforced mast, solid tires, durable structure!'
            )
        )
    ), -- highlighted_features
    NULL, -- base_price
    NULL, -- compare_at_price
    NULL, -- cost_price
    'TRY', -- currency
    1, -- price_on_request
    1, -- installment_available
    12, -- max_installments
    1, -- deposit_required
    NULL, -- deposit_amount
    30, -- deposit_percentage
    3429, -- weight (kg)
    JSON_OBJECT('length', 3020, 'width', 1170, 'height', 2078, 'unit', 'mm'), -- dimensions
    1, -- stock_tracking
    0, -- stock_quantity
    45, -- lead_time_days
    'new', -- condition
    'physical', -- product_type
    1, -- is_active
    1, -- is_featured
    1, -- sort_order
    1, -- is_bestseller
    1, -- is_new_arrival
    3, -- sort_order
    0, -- view_count
    0.00, -- rating_avg
    0, -- rating_count
    JSON_ARRAY('forklift', 'elektrikli', 'lityum', '2-ton', 'ağır-tonaj', 'güçlü'), -- tags
    JSON_OBJECT(
        'tr', JSON_ARRAY(
            '🏭 Ağır Üretim Tesisleri - Büyük hammadde paletleri, ağır ürün taşıma, yoğun operasyonlar için ideal',
            '📦 Büyük Lojistik Merkezleri - Ağır yük taşıma, yüksek kapasite gerektiren depo operasyonları',
            '🏗️ İnşaat Şantiyeleri - İnşaat malzemelerinin taşınması, ağır yüklerin aktarılması',
            '🚢 Liman ve Terminal İşletmeleri - Konteynerlerin iç alan aktarımı, ağır yük taşıma',
            '🏪 Büyük Perakende Zincirler - Ana dağıtım merkezlerinde ağır yük operasyonları',
            '❄️ Soğuk Hava Depoları - Büyük kapasiteli palet yükleme-boşaltma işlemleri'
        ),
        'en', JSON_ARRAY(
            '🏭 Heavy Production Facilities - Large raw material pallets, heavy product transport, ideal for intensive operations',
            '📦 Large Logistics Centers - Heavy load transport, warehouse operations requiring high capacity',
            '🏗️ Construction Sites - Transport of construction materials, transfer of heavy loads',
            '🚢 Port and Terminal Operations - Container transfer in indoor areas, heavy load transport',
            '🏪 Large Retail Chains - Heavy load operations in main distribution centers',
            '❄️ Cold Storage - Large capacity pallet loading-unloading operations'
        )
    ), -- use_cases
    JSON_OBJECT(
        'tr', JSON_ARRAY(
            '💪 En Güçlü Kompakt Forklift - 2 ton kapasiteli en kompakt model! Rakiplerin 2 tonlukları çok daha büyük',
            '💰 Maksimum Tasarruf - Yılda 30.000 TL yakıt tasarrufu, minimal bakım masrafı',
            '⚡ Süper Verimlilik - Gün boyu durmadan çalışır, ara şarj ile esneklik',
            '🔧 Kolay Bakım - Elektrikli motor, sıfır bakım masrafı',
            '🌱 Çevre Dostu - Sıfır emisyon, kapalı alanlarda ideal',
            '📞 Türkiye Çapında Destek - 7/24 teknik destek, 48 saat içinde yerinde servis: 0216 755 4 555'
        ),
        'en', JSON_ARRAY(
            '💪 Most Powerful Compact Forklift - The most compact model with 2 ton capacity!',
            '💰 Maximum Savings - 30,000 TL annual fuel savings, minimal maintenance cost',
            '⚡ Super Efficiency - Works all day without stopping, flexibility with fast charging',
            '🔧 Easy Maintenance - Electric motor, zero maintenance cost',
            '🌱 Environmentally Friendly - Zero emission, ideal for closed areas',
            '📞 Turkey-Wide Support - 24/7 technical support, on-site service within 48 hours'
        )
    ), -- competitive_advantages
    JSON_OBJECT(
        'tr', JSON_ARRAY('Lojistik', 'Üretim', 'İnşaat', 'Liman', 'Perakende', 'Gıda', 'İlaç'),
        'en', JSON_ARRAY('Logistics', 'Manufacturing', 'Construction', 'Port', 'Retail', 'Food', 'Pharmaceutical')
    ), -- target_industries
    JSON_ARRAY(
        JSON_OBJECT(
            'question', JSON_OBJECT('tr', 'CPD20TVL neden daha pahalı, farkı nedir?', 'en', 'Why is CPD20TVL more expensive, what is the difference?'),
            'answer', JSON_OBJECT(
                'tr', 'CPD20TVL serinin en güçlüsü! 2 ton (2000 kg) taşıma kapasitesi ile %30 daha fazla yük taşır. Daha geniş forklar (122x40x1070mm), daha güçlü yapı ve ağır işler için tasarlandı. Eğer günlük 1.8-2 ton arası yükler taşıyorsanız bu model tam size göre!',
                'en', 'CPD20TVL is the most powerful of the series! With 2 ton (2000 kg) carrying capacity, it carries 30% more load. Wider forks (122x40x1070mm), stronger structure and designed for heavy jobs.'
            )
        ),
        JSON_OBJECT(
            'question', JSON_OBJECT('tr', 'Ağır yükler için yeterince güçlü mü?', 'en', 'Is it powerful enough for heavy loads?'),
            'answer', JSON_OBJECT(
                'tr', 'Kesinlikle! 2 ton kapasitesi ile kompakt sınıfının en güçlü forkliftlerinden biri. Çift motorlu 2x5.0kW güç sistemi ve güçlendirilmiş mast yapısı sayesinde en ağır yüklerinizi güvenle taşır. Yoğun kullanım için ideal!',
                'en', 'Absolutely! With 2 ton capacity, it is one of the most powerful forklifts in its compact class. Thanks to dual motor 2x5.0kW power system and reinforced mast structure, it safely carries your heaviest loads. Ideal for heavy-duty use!'
            )
        ),
        JSON_OBJECT(
            'question', JSON_OBJECT('tr', 'Dar koridorlarda çalışabilir mi?', 'en', 'Can it work in narrow corridors?'),
            'answer', JSON_OBJECT(
                'tr', 'Evet! 1585mm dönüş yarıçapı ile 3.5m genişliğindeki koridorlarda rahatça manevra yapar. 2 ton kapasiteli forklifter arasında en kompakt modellerden biri! Dar alan ama ağır yük diyorsanız CPD20TVL tam size göre.',
                'en', 'Yes! With 1585mm turning radius, it maneuvers comfortably in corridors 3.5m wide. It is one of the most compact models among 2 ton capacity forklifts!'
            )
        ),
        JSON_OBJECT(
            'question', JSON_OBJECT('tr', 'Fiyat bilgisi ve taksit seçenekleri?', 'en', 'Price information and installment options?'),
            'answer', JSON_OBJECT(
                'tr', 'Size özel fiyat teklifi için 0216 755 4 555 numaralı telefondan bizi arayabilir veya info@ixtif.com adresine mail atabilirsiniz. 12 aya kadar taksit imkanı, sıfır/ikinci el/kiralık seçeneklerimiz mevcut!',
                'en', 'For a special price offer, you can call us at 0216 755 4 555 or email info@ixtif.com. Up to 12 months installment option, new/used/rental options available!'
            )
        ),
        JSON_OBJECT(
            'question', JSON_OBJECT('tr', 'Garanti kapsamı nedir?', 'en', 'What is the warranty coverage?'),
            'answer', JSON_OBJECT(
                'tr', '24 ay tam garanti, batarya dahil! Türkiye genelinde yetkili servis ağımız ve 7/24 teknik destek hattımız var. Yedek parça stokumuz her zaman hazır. Arıza durumunda 48 saat içinde yerinde müdahale garantisi!',
                'en', '24 months full warranty, including battery! We have authorized service network and 24/7 technical support line throughout Turkey. Our spare parts stock is always ready. On-site intervention guarantee within 48 hours in case of failure!'
            )
        )
    ), -- faq_data
    JSON_ARRAY(
        JSON_OBJECT('type', 'image', 'url', 'products/cpd20tvl/main.jpg', 'is_primary', 1, 'sort_order', 1),
        JSON_OBJECT('type', 'image', 'url', 'products/cpd20tvl/side.jpg', 'is_primary', 0, 'sort_order', 2),
        JSON_OBJECT('type', 'image', 'url', 'products/cpd20tvl/cabin.jpg', 'is_primary', 0, 'sort_order', 3),
        JSON_OBJECT('type', 'image', 'url', 'products/cpd20tvl/battery.jpg', 'is_primary', 0, 'sort_order', 4),
        JSON_OBJECT('type', 'pdf', 'url', 'products/cpd20tvl/brochure.pdf', 'is_primary', 0, 'sort_order', 5)
    ), -- media_gallery
    JSON_ARRAY('CPD15TVL', 'CPD18TVL'), -- related_products
    JSON_ARRAY(), -- cross_sell_products
    JSON_ARRAY(), -- up_sell_products (en güçlü model, up-sell yok)
    JSON_OBJECT(
        'pdf_source', '02_CPD15-18-20TVL-EN-Brochure.pdf',
        'extraction_date', '2025-10-10',
        'wheel_count', 3,
        'drive_type', 'dual_drive',
        'battery_technology', 'li-ion'
    ), -- metadata
    NOW(),
    NOW(),
    NOW()
);

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- ============================================
-- VERIFICATION QUERIES
-- ============================================
-- SELECT * FROM shop_brands WHERE brand_id = 1;
-- SELECT * FROM shop_categories WHERE category_id = 163;
-- SELECT * FROM shop_products WHERE product_id IN (1001, 1002, 1003);
-- SELECT * FROM shop_products WHERE sku IN ('CPD15TVL', 'CPD18TVL', 'CPD20TVL');

-- ============================================
-- İSTATİSTİKLER
-- ============================================
-- 3 Ürün eklendi (CPD15TVL, CPD18TVL, CPD20TVL)
-- Kategori: FORKLİFTLER (category_id = 163)
-- Marka: İXTİF (brand_id = 1)
-- Tüm ürünler B2C odaklı, ikna edici Türkçe metin ile
-- İletişim bilgileri FAQ'lerde doğal şekilde yerleştirildi
-- ============================================
