-- HAKKIMIZDA SAYFASI - SQL INSERT
-- tenant_ixtif.pages tablosuna eklenecek

-- NOT: Bu SQL'i çalıştırmadan önce KULLANICI ONAYINI AL!
-- CANLI VERİTABANI - DİKKATLİ OL!

-- HTML içeriği (BODY kolonu) - Dosyadan al
-- CSS içeriği - Dosyadan al
-- JS içeriği - Dosyadan al

-- Önce mevcut "hakkimizda" slug'ı var mı kontrol et
SELECT id, JSON_EXTRACT(title, '$.tr') as title_tr, slug
FROM tenant_ixtif.pages
WHERE slug = 'hakkimizda';

-- Eğer yoksa INSERT yap:
INSERT INTO tenant_ixtif.pages (
    title,
    slug,
    body,
    css,
    js,
    seo_settings,
    schema_org,
    is_active,
    created_at,
    updated_at
) VALUES (
    -- title (JSON)
    '{"tr": "Hakkımızda - Depo Ekipmanlarının Dijital Platformu | İXTİF"}',

    -- slug
    'hakkimizda',

    -- body (HTML içeriği - readme/ixtif/pages/hakkimizda.html)
    -- NOT: Bu alanı manuel doldur veya file_get_contents() kullan
    'BURAYA hakkimizda.html İÇERİĞİ GELECEK',

    -- css (readme/ixtif/pages/hakkimizda.css)
    'BURAYA hakkimizda.css İÇERİĞİ GELECEK',

    -- js (readme/ixtif/pages/hakkimizda.js)
    'BURAYA hakkimizda.js İÇERİĞİ GELECEK',

    -- seo_settings (JSON)
    '{
        "tr": {
            "meta_title": "Hakkımızda - Depo Ekipmanlarının Dijital Platformu | İXTİF",
            "meta_description": "10 yıllık deneyimimizle Türkiye''nin en büyük depo ekipmanları dijital platformu olmak için yola çıktık. 1,020+ ürün, 106 kategori, güvenilir iş ortaklığı.",
            "meta_keywords": "ixtif hakkında, forklift satış, depo ekipmanları bayilik, marketplace platform, ixtif kimdir, istanbul forklift, transpalet satış",
            "og_title": "Hakkımızda - Depo Ekipmanlarının Dijital Platformu | İXTİF",
            "og_description": "İXTİF olarak forklift ve depo ekipmanları pazarını dijitalleştiriyor, alıcı ile satıcıyı buluşturuyoruz. Bayimiz olun veya ürünlerinizi platformumuzda satın!",
            "og_image": "/images/og/hakkimizda-ixtif.jpg",
            "twitter_card": "summary_large_image",
            "canonical_url": "https://ixtif.com/hakkimizda"
        }
    }',

    -- schema_org (JSON)
    '{
        "@context": "https://schema.org",
        "@type": "AboutPage",
        "mainEntity": {
            "@type": "Organization",
            "name": "İXTİF İÇ VE DIŞ TİCARET A.Ş.",
            "legalName": "İXTİF İÇ VE DIŞ TİCARET ANONİM ŞİRKETİ",
            "foundingDate": "2014",
            "description": "Türkiye''nin en büyük depo ekipmanları dijital platformu. Forklift, transpalet, istif makinesi satış, kiralama ve servis hizmetleri.",
            "url": "https://ixtif.com",
            "logo": "https://ixtif.com/images/logo/ixtif-logo.png",
            "image": "https://ixtif.com/images/og/hakkimizda-ixtif.jpg",
            "telephone": "+902167553555",
            "email": "info@ixtif.com",
            "address": {
                "@type": "PostalAddress",
                "streetAddress": "Küçükyalı Mahallesi, Çamlık Sokak, Manzara Adalar Sitesi, B Blok No: 1/B, İç Kapı No: 89",
                "addressLocality": "Kartal",
                "addressRegion": "İstanbul",
                "postalCode": "34840",
                "addressCountry": "TR"
            },
            "vatID": "4831552951",
            "taxID": "4831552951",
            "contactPoint": [
                {
                    "@type": "ContactPoint",
                    "telephone": "+902167553555",
                    "contactType": "customer service",
                    "areaServed": "TR",
                    "availableLanguage": ["Turkish", "English"]
                },
                {
                    "@type": "ContactPoint",
                    "telephone": "+905322160754",
                    "contactType": "sales",
                    "contactOption": "TollFree",
                    "areaServed": "TR",
                    "availableLanguage": "Turkish"
                }
            ],
            "sameAs": [
                "https://www.instagram.com/ixtifcom",
                "https://www.facebook.com/ixtif",
                "https://wa.me/905322160754"
            ],
            "numberOfEmployees": {
                "@type": "QuantitativeValue",
                "value": "25"
            },
            "slogan": "Depo Ekipmanlarının Dijital Platformu",
            "keywords": "forklift, transpalet, istif makinesi, reach truck, depo ekipmanları, akülü istif, elektrikli forklift"
        }
    }',

    -- is_active
    1,

    -- created_at, updated_at
    NOW(),
    NOW()
);

-- Eklenen kaydı kontrol et
SELECT
    id,
    JSON_EXTRACT(title, '$.tr') as title_tr,
    slug,
    CHAR_LENGTH(body) as body_length,
    CHAR_LENGTH(css) as css_length,
    CHAR_LENGTH(js) as js_length,
    is_active,
    created_at
FROM tenant_ixtif.pages
WHERE slug = 'hakkimizda';


-- ÖNEMLİ NOTLAR:
-- 1. HTML/CSS/JS içeriklerini dosyalardan oku ve SQL'e ekle
-- 2. Blade tag'leri ({{ settings() }}) içeriğe dahil et
-- 3. JSON string'lerde tek tırnak escape et ('')
-- 4. CANLI VERİTABANI - DİKKATLİ OL!
-- 5. Test et: https://ixtif.com/hakkimizda
