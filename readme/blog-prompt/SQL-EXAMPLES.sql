-- ====================================================================
-- BLOG VE SEO_SETTINGS TABLOLARI İÇİN HAZIR SQL ÖRNEKLERİ (DÜZELTİLMİŞ)
-- Endüstriyel Ürün Satışı - B2B Blog İçerikleri
-- Tenant: ixtif.com (ID: 2)
--
-- ⚠️ ÖNEMLİ: JSON_OBJECT() fonksiyonu kullanılarak JSON validation hatası önlenmiştir
-- ====================================================================

-- Kategori ID'sini al (varsayılan olarak 1 kullanıyoruz)
SET @category_id = 1;

-- ====================================================================
-- 1. TRANSPALET BLOG
-- ====================================================================

INSERT INTO blogs (
    blog_category_id,
    title,
    slug,
    body,
    excerpt,
    published_at,
    is_featured,
    status,
    is_active,
    created_at,
    updated_at
) VALUES (
    @category_id,
    JSON_OBJECT('tr', 'Transpalet Nedir? Çeşitleri ve Kullanım Alanları [2025 Rehberi]'),
    JSON_OBJECT('tr', 'transpalet-nedir-cesitleri-kullanim-alanlari'),
    JSON_OBJECT('tr', '<section class="py-8 md:py-12"><div class="container mx-auto px-4"><h1 class="text-3xl md:text-5xl font-black mb-6 text-gray-900 dark:text-white">Transpalet Nedir?</h1><p class="text-lg text-gray-700 dark:text-gray-300 leading-relaxed mb-8">Transpalet, depo ve lojistik operasyonlarında paletli yüklerin taşınması için kullanılan temel endüstriyel ekipmandır. Manuel pompalama veya elektrik motoru ile çalışan bu ekipman, 2-3 ton yük taşıma kapasitesine sahiptir.</p><h2 class="text-2xl font-bold mb-4">Manuel Transpalet Özellikleri</h2><p>Manuel transpaletler hidrolik pompa sistemi ile çalışır. Operatör kol hareketleriyle hidrolik pompayı çalıştırarak paletin yerden kalkmasını sağlar. 2000-3000 kg kapasite, 800-2000 mm çatal uzunluğu standart özellikleridir.</p><h2 class="text-2xl font-bold mb-4">Elektrikli Transpalet Avantajları</h2><p>Elektrikli modeller uzun mesafe taşıma ve yoğun kullanım için idealdir. Operatör yorgunluğunu minimuma indirir, verimliliği artırır. 1500-3000 kg kapasite aralığında modeller mevcuttur.</p></div></section>'),
    JSON_OBJECT('tr', 'Transpalet, depo ve lojistik operasyonlarında palet taşıma işlemlerini kolaylaştıran endüstriyel ekipmandır. Manuel ve elektrikli modelleri ile 2-3 ton yük taşıma kapasitesine sahiptir.'),
    NOW(),
    1,
    'published',
    1,
    NOW(),
    NOW()
);

SET @blog1_id = LAST_INSERT_ID();

INSERT INTO seo_settings (
    seoable_type,
    seoable_id,
    titles,
    descriptions,
    og_titles,
    og_descriptions,
    og_images,
    og_type,
    robots_meta,
    schema_type,
    priority_score,
    status,
    created_at,
    updated_at
) VALUES (
    'Modules\\Blog\\App\\Models\\Blog',
    @blog1_id,
    JSON_OBJECT('tr', 'Transpalet Nedir? ⚡ Çeşitleri ve Fiyatları 2025'),
    JSON_OBJECT('tr', 'Transpalet nedir, nasıl kullanılır? ✅ Manuel ve elektrikli transpalet çeşitleri ✅ 2-3 ton kapasite ✅ En uygun fiyatlar ➤ Hemen inceleyin!'),
    JSON_OBJECT('tr', 'Transpalet Rehberi: Manuel ve Elektrikli Modeller'),
    JSON_OBJECT('tr', 'Depo ekipmanlarının vazgeçilmezi transpalet hakkında bilmeniz gereken her şey. Çeşitleri, özellikleri ve fiyat karşılaştırması.'),
    JSON_OBJECT('tr', '/uploads/blog/transpalet-rehber.jpg'),
    'article',
    JSON_OBJECT('index', true, 'follow', true, 'max-snippet', -1, 'max-image-preview', 'large', 'max-video-preview', -1),
    JSON_OBJECT('tr', 'Article'),
    9,
    'active',
    NOW(),
    NOW()
);

-- ====================================================================
-- 2. FORKLİFT KİRALAMA BLOG
-- ====================================================================

INSERT INTO blogs (
    blog_category_id,
    title,
    slug,
    body,
    excerpt,
    published_at,
    is_featured,
    status,
    is_active,
    created_at,
    updated_at
) VALUES (
    @category_id,
    JSON_OBJECT('tr', 'Forklift Kiralama Rehberi: Fiyatlar ve Kiralama Şartları 2025'),
    JSON_OBJECT('tr', 'forklift-kiralama-rehberi-fiyatlar-sartlar'),
    JSON_OBJECT('tr', '<section class="py-8 md:py-12"><div class="container mx-auto px-4"><h1 class="text-3xl md:text-5xl font-black mb-6">Forklift Kiralama Rehberi</h1><p class="text-lg mb-8">Forklift kiralama, işletmelerin sermaye bağlamadan ihtiyaç duydukları ekipmana ulaşmasını sağlayan ekonomik çözümdür. Günlük, aylık ve yıllık kiralama seçenekleri mevcuttur.</p><h2 class="text-2xl font-bold mb-4">Kiralama Avantajları</h2><ul class="list-disc pl-6 mb-6"><li>Başlangıç maliyeti yok</li><li>Bakım ve servis dahil</li><li>Vergi avantajı</li><li>Esnek sözleşme süreleri</li></ul><h2 class="text-2xl font-bold mb-4">2025 Kiralama Fiyatları</h2><table class="w-full border-collapse"><tr><th class="border p-2">Model</th><th class="border p-2">Günlük</th><th class="border p-2">Aylık</th></tr><tr><td class="border p-2">1.5 Ton Elektrikli</td><td class="border p-2">₺800</td><td class="border p-2">₺12.000</td></tr><tr><td class="border p-2">2.5 Ton Dizel</td><td class="border p-2">₺1.200</td><td class="border p-2">₺18.000</td></tr></table></div></section>'),
    JSON_OBJECT('tr', 'Forklift kiralama ile sermaye bağlamadan ekipman ihtiyacınızı karşılayın. Günlük, aylık ve yıllık kiralama seçenekleri, bakım dahil paketler.'),
    NOW(),
    0,
    'published',
    1,
    NOW(),
    NOW()
);

SET @blog2_id = LAST_INSERT_ID();

INSERT INTO seo_settings (
    seoable_type,
    seoable_id,
    titles,
    descriptions,
    og_titles,
    og_descriptions,
    og_images,
    og_type,
    robots_meta,
    schema_type,
    priority_score,
    status,
    created_at,
    updated_at
) VALUES (
    'Modules\\Blog\\App\\Models\\Blog',
    @blog2_id,
    JSON_OBJECT('tr', 'Forklift Kiralama 2025 ⚡ Günlük ve Aylık Fiyatlar'),
    JSON_OBJECT('tr', 'Forklift kiralama fiyatları ve şartları ✅ Günlük 800₺''den başlayan fiyatlar ✅ Bakım dahil ✅ 7/24 teknik destek ➤ Hemen teklif alın!'),
    JSON_OBJECT('tr', 'Forklift Kiralama: Ekonomik Çözümler'),
    JSON_OBJECT('tr', 'İşletmeniz için en uygun forklift kiralama seçenekleri. Elektrikli, dizel ve LPG modeller. Esnek sözleşme süreleri.'),
    JSON_OBJECT('tr', '/uploads/blog/forklift-kiralama.jpg'),
    'article',
    JSON_OBJECT('index', true, 'follow', true, 'max-snippet', -1, 'max-image-preview', 'large'),
    JSON_OBJECT('tr', 'Article'),
    8,
    'active',
    NOW(),
    NOW()
);

-- ====================================================================
-- 3. REACH TRUCK BLOG
-- ====================================================================

INSERT INTO blogs (
    blog_category_id,
    title,
    slug,
    body,
    excerpt,
    published_at,
    is_featured,
    status,
    is_active,
    created_at,
    updated_at
) VALUES (
    @category_id,
    JSON_OBJECT('tr', 'Reach Truck Nedir? Dar Koridor İstif Makinesi Özellikleri'),
    JSON_OBJECT('tr', 'reach-truck-nedir-dar-koridor-istif-makinesi'),
    JSON_OBJECT('tr', '<section class="py-8"><div class="container mx-auto px-4"><h1 class="text-4xl font-black mb-6">Reach Truck: Dar Koridor Uzmanı</h1><p class="text-lg mb-8">Reach truck, dar koridorlarda çalışmak üzere tasarlanmış, yüksek raflama sistemlerinde kullanılan özel istif makinesidir. 13 metreye kadar yükseklikte güvenli çalışma imkanı sunar.</p><h2 class="text-2xl font-bold mb-4">Teknik Özellikler</h2><div class="grid md:grid-cols-2 gap-6"><div class="bg-gray-50 p-4 rounded"><h3 class="font-bold mb-2">Kapasite</h3><p>1.4 - 2.5 ton yük taşıma</p></div><div class="bg-gray-50 p-4 rounded"><h3 class="font-bold mb-2">Yükseklik</h3><p>6 - 13 metre kaldırma</p></div><div class="bg-gray-50 p-4 rounded"><h3 class="font-bold mb-2">Koridor Genişliği</h3><p>Minimum 2.7 metre</p></div><div class="bg-gray-50 p-4 rounded"><h3 class="font-bold mb-2">Hız</h3><p>12 km/saat maksimum</p></div></div></div></section>'),
    JSON_OBJECT('tr', 'Reach truck, dar koridorlarda çalışan yüksek raflama sistemleri için özel istif makinesidir. 13 metre yüksekliğe kadar güvenli operasyon.'),
    NOW(),
    0,
    'published',
    1,
    NOW(),
    NOW()
);

SET @blog3_id = LAST_INSERT_ID();

INSERT INTO seo_settings (
    seoable_type,
    seoable_id,
    titles,
    descriptions,
    og_titles,
    og_descriptions,
    og_images,
    og_type,
    robots_meta,
    schema_type,
    priority_score,
    status,
    created_at,
    updated_at
) VALUES (
    'Modules\\Blog\\App\\Models\\Blog',
    @blog3_id,
    JSON_OBJECT('tr', 'Reach Truck Nedir? 🏗️ Dar Koridor İstif Makineleri'),
    JSON_OBJECT('tr', 'Reach truck özellikleri ve fiyatları ✅ 13 metre yükseklik ✅ Dar koridor çalışması ✅ 1.4-2.5 ton kapasite ➤ Detaylı bilgi alın!'),
    JSON_OBJECT('tr', 'Reach Truck: Yüksek Raflama Çözümü'),
    JSON_OBJECT('tr', 'Deponuzda alan tasarrufu sağlayan reach truck modelleri. Dar koridorda maksimum verimlilik.'),
    JSON_OBJECT('tr', '/uploads/blog/reach-truck.jpg'),
    'article',
    JSON_OBJECT('index', true, 'follow', true, 'max-snippet', -1, 'max-image-preview', 'large'),
    JSON_OBJECT('tr', 'Article'),
    7,
    'active',
    NOW(),
    NOW()
);

-- ====================================================================
-- KONTROL SORGULARI
-- ====================================================================

-- Eklenen blogları kontrol et
SELECT
    b.blog_id,
    JSON_UNQUOTE(JSON_EXTRACT(b.title, '$.tr')) as title_tr,
    JSON_UNQUOTE(JSON_EXTRACT(b.slug, '$.tr')) as slug_tr,
    b.status,
    b.is_featured,
    b.published_at
FROM blogs b
WHERE b.blog_id IN (@blog1_id, @blog2_id, @blog3_id)
ORDER BY b.blog_id DESC;

-- SEO ayarlarını kontrol et
SELECT
    s.id,
    s.seoable_id,
    JSON_UNQUOTE(JSON_EXTRACT(s.titles, '$.tr')) as seo_title,
    s.priority_score,
    JSON_UNQUOTE(JSON_EXTRACT(s.schema_type, '$.tr')) as schema
FROM seo_settings s
WHERE s.seoable_type = 'Modules\\Blog\\App\\Models\\Blog'
AND s.seoable_id IN (@blog1_id, @blog2_id, @blog3_id)
ORDER BY s.id DESC;

-- ====================================================================
-- TOPLU SİLME (GEREKTİĞİNDE)
-- ====================================================================

-- Son eklenen blogları silmek için (dikkatli kullan!)
-- DELETE FROM seo_settings
-- WHERE seoable_type = 'Modules\\Blog\\App\\Models\\Blog'
--   AND seoable_id IN (@blog1_id, @blog2_id, @blog3_id);
--
-- DELETE FROM blogs
-- WHERE blog_id IN (@blog1_id, @blog2_id, @blog3_id);

-- ====================================================================
-- KULLANIM:
-- mysql -u root tenant_ixtif < SQL-EXAMPLES-FIXED.sql
-- ====================================================================
