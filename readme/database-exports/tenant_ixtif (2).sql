-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Anamakine: localhost
-- Üretim Zamanı: 08 Kas 2025, 20:43:12
-- Sunucu sürümü: 9.4.0
-- PHP Sürümü: 8.4.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `tenant_ixtif`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `ai_flows`
--

CREATE TABLE `ai_flows` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `flow_data` json NOT NULL COMMENT 'Complete flow structure (nodes + edges)',
  `metadata` json DEFAULT NULL COMMENT 'Cache strategy, parallel groups, etc.',
  `priority` int NOT NULL DEFAULT '100' COMMENT 'Execution priority (lower = higher priority)',
  `status` enum('active','inactive','draft') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `ai_flows`
--

INSERT INTO `ai_flows` (`id`, `name`, `description`, `flow_data`, `metadata`, `priority`, `status`, `created_at`, `updated_at`) VALUES
(6, 'E-Commerce Chat Assistant', 'Product search and recommendation flow for e-commerce chat', '{\"edges\": [{\"to\": \"node_2\", \"from\": \"node_1\"}, {\"to\": \"node_3\", \"from\": \"node_2\"}, {\"to\": \"node_4\", \"from\": \"node_3\"}, {\"to\": \"node_5\", \"from\": \"node_4\"}, {\"to\": \"node_10\", \"from\": \"node_5\"}, {\"to\": \"node_11\", \"from\": \"node_10\"}, {\"to\": \"node_12\", \"from\": \"node_11\"}], \"nodes\": [{\"id\": \"node_1\", \"name\": \"Hoşgeldin Mesajı\", \"type\": \"welcome\", \"config\": {\"next_node\": \"node_2\"}, \"position\": {\"x\": 150, \"y\": 100}}, {\"id\": \"node_2\", \"name\": \"Kategori Algıla\", \"type\": \"category_detection\", \"config\": {\"next_node\": \"node_3\"}, \"position\": {\"x\": 150, \"y\": 250}}, {\"id\": \"node_3\", \"name\": \"Ürün Ara\", \"type\": \"meilisearch_settings\", \"config\": {\"search_limit\": 5, \"sort_by_stock\": true, \"typo_tolerance\": true, \"ranking_enabled\": true, \"use_meilisearch\": true, \"use_advanced_filters\": true, \"no_products_next_node\": \"node_10\"}, \"position\": {\"x\": 150, \"y\": 400}}, {\"id\": \"node_4\", \"name\": \"Stok Filtrele\", \"type\": \"stock_sorter\", \"config\": {\"next_node\": \"node_5\", \"exclude_out_of_stock\": true, \"high_stock_threshold\": 10}, \"position\": {\"x\": 150, \"y\": 550}}, {\"id\": \"node_5\", \"name\": \"Context Hazırla\", \"type\": \"context_builder\", \"config\": {\"next_node\": \"node_10\"}, \"position\": {\"x\": 150, \"y\": 700}}, {\"id\": \"node_10\", \"name\": \"AI Yanıt Üret\", \"type\": \"ai_response\", \"config\": {\"max_tokens\": 2000, \"temperature\": 0.7, \"system_prompt\": \"Sen profesyonel bir e-ticaret satış asistanısın. Görevin müşterilere yardımcı olmak ve sorularını yanıtlamak.\\n\\nKRİTİK: Konuşma geçmişini MUTLAKA kontrol et! Daha önce konuştuysanız devam et, tekrar selamlaşma.\\n\\nKURALLAR:\\n1. İlk mesajsa → Kısa ve samimi selamla\\n2. Devam mesajıysa → Direkt konuya gir, tekrar merhaba deme\\n3. Kullanıcının adını söylediyse → O adı kullan ve HATIRLA\\n4. Genel sohbet (merhaba, nasılsın vb.) → Kısa ve doğal yanıt ver\\n5. Ürün sorusu → Yardımcı ol, ürün öner\\n6. Kullanıcı ilgilenmiyorsa → Zorla satış yapma\\n\\nYAPMA:\\n❌ Her yanıta \\\"Merhaba! Hoş geldin!\\\" diye başlama\\n❌ Robot gibi aynı cümleleri tekrarlama\\n❌ Konuşma geçmişini görmezden gelme\\n❌ Kullanıcı adını unutma\\n\\nYAP:\\n✅ Konuşma akışına uygun yanıt ver\\n✅ Kullanıcının adını kullan (varsa)\\n✅ Kısa ve öz konuş\\n✅ Emoji kullan ama abartma\\n\\nÖRNEK:\\nKullanıcı: \\\"merhaba benim adım Ayşe\\\"\\nSen: \\\"Merhaba Ayşe! 👋 Sana nasıl yardımcı olabilirim?\\\"\\n\\n[Bir sonraki mesajda]\\nKullanıcı: \\\"ürün arıyorum\\\"\\nSen: \\\"Tabii Ayşe, hangi özelliklerde ürün lazım?\\\"\\n\\n[DEĞİL]\\nSen: \\\"Merhaba Ayşe! Hoş geldin! 😊 Sana nasıl yardımcı olabilirim?\\\"\", \"welcome_message\": \"Merhaba! 👋 Size nasıl yardımcı olabilirim?\"}, \"position\": {\"x\": 150, \"y\": 850}}, {\"id\": \"node_11\", \"name\": \"Mesajı Kaydet\", \"type\": \"message_saver\", \"config\": {\"next_node\": \"node_12\"}, \"position\": {\"x\": 150, \"y\": 1000}}, {\"id\": \"node_12\", \"name\": \"Son\", \"type\": \"end\", \"config\": [], \"position\": {\"x\": 150, \"y\": 1150}}]}', '{\"cache_strategy\": {\"product_search\": {\"ttl\": 300, \"enabled\": true}, \"category_detection\": {\"ttl\": 600, \"enabled\": true}}}', 10, 'active', '2025-11-06 02:37:36', '2025-11-06 13:44:03');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `ai_knowledge_base`
--

CREATE TABLE `ai_knowledge_base` (
  `id` bigint UNSIGNED NOT NULL,
  `tenant_id` bigint UNSIGNED NOT NULL,
  `category` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `question` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `answer` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ;

--
-- Tablo döküm verisi `ai_knowledge_base`
--

INSERT INTO `ai_knowledge_base` (`id`, `tenant_id`, `category`, `question`, `answer`, `metadata`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 2, 'Firma Hakkında', 'İxtif kimdir, ne yapar?', 'İxtif, \"Türkiye\'nin İstif Pazarı\" sloganıyla depolama ve istif ekipmanları alanında lider bir firmadır. Forklift satışı, kiralama, teknik servis, yedek parça tedariki ve 2. el ürün hizmetleri sunuyoruz. Elektrikli forkliftler, dizel forkliftler, LPG forkliftler, transpaletler, istif makineleri, reach truck\'lar ve AMR otonom mobil robotlar gibi geniş bir ürün yelpazesine sahibiz.', '{\"icon\": \"fas fa-building\", \"tags\": [\"ixtif\", \"firma tanıtımı\", \"hakkımızda\"], \"internal_note\": \"Ana tanıtım mesajı - her yeni müşteriye bu bilgi verilebilir.\"}', 1, 1, '2025-10-13 18:13:20', '2025-10-13 18:13:20'),
(2, 2, 'Firma Hakkında', 'İxtif\'in vizyonu nedir?', 'Vizyonumuz, Türkiye\'nin en güvenilir istif ve intralojiştik markası olmaktır. Müşterilerimize yenilikçi, erişilebilir ve şeffaf hizmet sunarak sektörde standart belirlemeyi hedefliyoruz. Güvenilirlik, yenilikçilik, erişilebilirlik ve şeffaflık değerlerimizle hareket ediyoruz.', '{\"icon\": \"fas fa-eye\", \"tags\": [\"vizyon\", \"misyon\", \"değerler\"], \"internal_note\": \"Firma vizyonu ve değerleri.\"}', 1, 2, '2025-10-13 18:13:20', '2025-10-13 18:13:20'),
(3, 2, 'Firma Hakkında', 'Hangi sektörlere hizmet veriyorsunuz?', 'Lojistik, e-ticaret, üretim, perakende, gıda, soğuk zincir, otomotiv, tekstil ve inşaat sektörlerine özel çözümler sunuyoruz. Her sektörün kendine özgü ihtiyaçlarını anlıyor ve en uygun istif ekipmanı çözümünü öneriyoruz.', '{\"icon\": \"fas fa-industry\", \"tags\": [\"sektörler\", \"lojistik\", \"e-ticaret\", \"üretim\"], \"internal_note\": \"Sektörel yelpaze - müşterinin sektörüne göre özelleştirebilirsin.\"}', 1, 3, '2025-10-13 18:13:20', '2025-10-13 18:13:20'),
(4, 2, 'Ürünler', 'Hangi forklift türleri var?', 'Elektrikli forkliftler (kapalı alan kullanımı için çevre dostu, sessiz), dizel forkliftler (açık alan ve ağır işler için güçlü), LPG forkliftler (hem kapalı hem açık alan için hibrit çözüm) sunuyoruz. Her birinin kapasitesi, kaldırma yüksekliği ve kullanım alanı farklıdır.', '{\"icon\": \"fas fa-truck-loading\", \"tags\": [\"forklift türleri\", \"elektrikli\", \"dizel\", \"lpg\"], \"internal_note\": \"Forklift türleri - müşterinin kullanım alanına göre yönlendir.\"}', 1, 4, '2025-10-13 18:13:20', '2025-10-13 18:13:20'),
(5, 2, 'Ürünler', 'Transpalet nedir, ne işe yarar?', 'Transpalet, paletli yüklerin kısa mesafeli taşınması için kullanılan manuel veya elektrikli istif ekipmanıdır. Depolarda, market ve mağazalarda, yükleme rampalarında sıkça kullanılır. Elektrikli transpaletler operatör yorgunluğunu azaltır ve iş verimliliğini artırır.', '{\"icon\": \"fas fa-pallet\", \"tags\": [\"transpalet\", \"palet taşıma\", \"elektrikli transpalet\"], \"internal_note\": \"Transpalet tanımı ve kullanım alanları.\"}', 1, 5, '2025-10-13 18:13:20', '2025-10-13 18:13:20'),
(6, 2, 'Ürünler', 'Reach truck nedir?', 'Reach truck (uzanma maşası), dar koridorlarda yüksek raflara yük yerleştirmek için tasarlanmış özel bir forklift türüdür. Direklerini öne doğru uzatabileceği için yüksek depolama verimliliği sağlar. E-ticaret ve lojistik depolarında çok yaygındır.', '{\"icon\": \"fas fa-warehouse\", \"tags\": [\"reach truck\", \"dar koridor\", \"yüksek raf\"], \"internal_note\": \"Reach truck açıklaması - depo verimliliği vurgusu.\"}', 1, 6, '2025-10-13 18:13:20', '2025-10-13 18:13:20'),
(7, 2, 'Ürünler', 'İstif makineleri nasıl çalışır?', 'İstif makineleri (stacker), paletli yükleri yerden kaldırarak raflara istiflemek için kullanılır. Manuel (hidrolik pompalı), yarı elektrikli (kaldırma elektrikli, hareket manuel) ve tam elektrikli modelleri vardır. Küçük depolarda, dar alanlarda ideal çözümdür.', '{\"icon\": \"fas fa-level-up-alt\", \"tags\": [\"istif makinesi\", \"stacker\", \"elektrikli stacker\"], \"internal_note\": \"İstif makineleri - küçük işletmeler için uygun maliyetli seçenek.\"}', 1, 7, '2025-10-13 18:13:20', '2025-10-13 18:13:20'),
(8, 2, 'Ürünler', 'Elektrikli forklift mi dizel mi almalıyım?', 'Kapalı alanlarda (depo, fabrika) elektrikli forklift idealdir: egzoz gazı yok, sessiz, bakım maliyeti düşük. Açık alanlarda veya zorlu koşullarda dizel forklift daha güçlüdür. LPG forkliftler ise her iki ortamda da kullanılabilir. İhtiyacınıza göre en uygun modeli önerebiliriz.', '{\"icon\": \"fas fa-balance-scale\", \"tags\": [\"elektrikli vs dizel\", \"forklift karşılaştırma\"], \"internal_note\": \"Müşterinin kullanım ortamını sor ve ona göre yönlendir.\"}', 1, 8, '2025-10-13 18:13:20', '2025-10-13 18:13:20'),
(9, 2, 'Hizmetler', 'Hangi hizmetleri sunuyorsunuz?', 'İxtif olarak forklift satışı, kiralama, teknik servis, yedek parça tedariki ve 2. el ürün alım-satımı hizmetleri veriyoruz. Ayrıca operatör eğitimi, periyodik bakım paketleri ve 7/24 teknik destek sunuyoruz.', '{\"icon\": \"fas fa-cogs\", \"tags\": [\"hizmetler\", \"satış\", \"kiralama\", \"servis\"], \"internal_note\": \"Tüm hizmetlerin özeti.\"}', 1, 9, '2025-10-13 18:13:20', '2025-10-13 18:13:20'),
(10, 2, 'Hizmetler', 'Teknik servis hizmetiniz nasıl çalışır?', 'Teknik servis ekibimiz, tüm marka ve modellerde periyodik bakım, arıza onarımı ve acil müdahale hizmeti sunar. Orijinal yedek parça kullanırız, işlerimiz garanti kapsamındadır. Anlaşmalı müşterilerimize öncelikli servis ve indirimli yedek parça hizmeti sağlıyoruz.', '{\"icon\": \"fas fa-wrench\", \"tags\": [\"teknik servis\", \"bakım\", \"onarım\"], \"internal_note\": \"Servis kalitesi ve orijinal yedek parça kullanımını vurgula.\"}', 1, 10, '2025-10-13 18:13:20', '2025-10-13 18:13:20'),
(11, 2, 'Hizmetler', 'Yedek parça temin edebiliyor musunuz?', 'Evet, tüm istif ekipmanları için orijinal ve yan sanayi yedek parça tedariki yapıyoruz. Geniş stok ağımızla hızlı teslimat sağlıyoruz. Acil parça ihtiyaçlarında aynı gün kargo seçeneğimiz mevcuttur.', '{\"icon\": \"fas fa-box-open\", \"tags\": [\"yedek parça\", \"stok\", \"hızlı teslimat\"], \"internal_note\": \"Yedek parça stoku ve hızlı tedarik avantajı.\"}', 1, 11, '2025-10-13 18:13:20', '2025-10-13 18:13:20'),
(12, 2, 'Hizmetler', 'Operatör eğitimi veriyor musunuz?', 'Evet, forklift ve diğer istif ekipmanları için sertifikalı operatör eğitimi sunuyoruz. İş Sağlığı ve Güvenliği (İSG) mevzuatına uygun teorik ve pratik eğitim veriyoruz. Eğitim sonunda katılımcılar belgelerini alırlar.', '{\"icon\": \"fas fa-user-graduate\", \"tags\": [\"operatör eğitimi\", \"forklift sertifikası\", \"isg\"], \"internal_note\": \"Eğitim hizmeti - iş güvenliği ve yasal uyumluluk vurgusu.\"}', 1, 12, '2025-10-13 18:13:20', '2025-10-13 18:13:20'),
(13, 2, 'Teknik', 'Forklift kapasitesi nasıl belirlenir?', 'Forklift kapasitesi, kaldırabileceği maksimum ağırlığı (ton cinsinden) ifade eder. 1.5 ton, 2 ton, 3 ton, 5 ton gibi değişir. Kaldırma yüksekliği arttıkça kapasite azalır (moment etkisi). İhtiyacınızı belirlerken taşıyacağınız en ağır yükü ve kaldırma yüksekliğini dikkate almalısınız.', '{\"icon\": \"fas fa-weight-hanging\", \"tags\": [\"kapasite\", \"tonaj\", \"kaldırma yüksekliği\"], \"internal_note\": \"Kapasite seçimi - müşterinin yük ağırlığını sor.\"}', 1, 13, '2025-10-13 18:13:20', '2025-10-13 18:13:20'),
(14, 2, 'Teknik', 'Elektrikli forklift şarj süresi ne kadardır?', 'Standart elektrikli forkliftlerde tam şarj süresi 6-8 saat arasındadır. Fırsat şarjı (ara şarj) özelliği olan modellerde mola zamanlarında kısa şarjlar yapılabilir. Hızlı şarj sistemleriyle bu süre 2-3 saate düşebilir.', '{\"icon\": \"fas fa-battery-full\", \"tags\": [\"şarj süresi\", \"elektrikli forklift\", \"batarya\"], \"internal_note\": \"Şarj süresi bilgisi - vardiya sistemine göre öneri yapabilirsin.\"}', 1, 14, '2025-10-13 18:13:20', '2025-10-13 18:13:20'),
(15, 2, 'Teknik', 'Forklift bakımı ne sıklıkla yapılmalı?', 'Rutin bakım 250-500 çalışma saatinde bir (yaklaşık 3-6 ayda bir) yapılmalıdır. Yoğun kullanımda daha sık bakım gerekir. Günlük kontroller (fren, direksiyon, hidrolik sızıntı) operatör tarafından yapılmalıdır. Periyodik bakım ile ekipmanın ömrü uzar ve arızalar önlenir.', '{\"icon\": \"fas fa-calendar-alt\", \"tags\": [\"bakım\", \"periyodik bakım\", \"çalışma saati\"], \"internal_note\": \"Bakım sıklığı - düzenli bakımın önemini vurgula.\"}', 1, 15, '2025-10-13 18:13:20', '2025-10-13 18:13:20'),
(16, 2, 'Teknik', 'Forklift güvenlik önlemleri nelerdir?', 'Operatör sertifikası zorunludur. Emniyet kemeri takılmalı, hız limitlerine uyulmalı, yük dengesi kontrol edilmeli. Forklift lastikleri, frenler ve farlar düzenli kontrol edilmelidir. Arka görüş aynası, sesli/ışıklı uyarıcılar bulunmalıdır. İSG mevzuatına uygun kullanım esastır.', '{\"icon\": \"fas fa-hard-hat\", \"tags\": [\"güvenlik\", \"isg\", \"forklift emniyeti\"], \"internal_note\": \"Güvenlik standartları - İSG mevzuatı vurgusu.\"}', 1, 16, '2025-10-13 18:13:20', '2025-10-13 18:13:20'),
(17, 2, 'Kiralama', 'Forklift kiralama avantajları nelerdir?', 'Kiralama ile sermaye yatırımı yapmazsınız, nakit akışınızı korursunuz. Bakım ve onarım firmaya aittir. Sezonluk ihtiyaçlarda veya kısa süreli projelerde çok mantıklıdır. Esnek kiralama süreleri (günlük, haftalık, aylık, yıllık) sunuyoruz. İhtiyaç değiştiğinde ekipman değişikliği kolayca yapılabilir.', '{\"icon\": \"fas fa-handshake\", \"tags\": [\"kiralama\", \"avantajlar\", \"esneklik\"], \"internal_note\": \"Kiralama avantajları - nakit akışı ve esneklik vurgusu.\"}', 1, 17, '2025-10-13 18:13:20', '2025-10-13 18:13:20'),
(18, 2, 'Kiralama', 'Hangi sürelerde kiralama yapıyorsunuz?', 'Günlük, haftalık, aylık ve uzun süreli (1-5 yıl) kiralama seçeneklerimiz vardır. Sezonluk ihtiyaçlar için özel kampanyalar düzenliyoruz. Kısa süreli acil ihtiyaçlarda aynı gün teslimat sağlıyoruz.', '{\"icon\": \"fas fa-calendar-check\", \"tags\": [\"kiralama süresi\", \"günlük\", \"aylık\", \"yıllık\"], \"internal_note\": \"Kiralama süre seçenekleri - müşterinin projesine göre öner.\"}', 1, 18, '2025-10-13 18:13:20', '2025-10-13 18:13:20'),
(19, 2, 'Kiralama', 'Kiralık forkliftler hangi durumdadır?', 'Kiralık filomuz düzenli bakımlı, güvenlik sertifikalı ve çalışır durumdadır. Her ekipman teslim öncesi teknik kontrolden geçer. Kiralama süresi boyunca bakım ve onarım hizmetimiz dahildir. Arızada yedek ekipman desteği sağlıyoruz.', '{\"icon\": \"fas fa-certificate\", \"tags\": [\"kiralık ekipman\", \"bakımlı\", \"garanti\"], \"internal_note\": \"Kiralık ekipman kalitesi - güven verici mesaj.\"}', 1, 19, '2025-10-13 18:13:20', '2025-10-13 18:13:20'),
(20, 2, 'Kiralama', 'Satın alma mı kiralama mı daha avantajlı?', 'Kısa vadeli (1 yıla kadar) veya sezonluk kullanımda kiralama avantajlıdır. 3 yıl ve üzeri sürekli kullanımda satın almak daha ekonomik olabilir. Nakit akışınızı korumak, bakım yükünden kurtulmak istiyorsanız kiralama idealdir. Uzun vadeli yatırım yapmak, ekipmanı kendinize ait görmek istiyorsanız satın alma uygundur.', '{\"icon\": \"fas fa-balance-scale\", \"tags\": [\"satın alma vs kiralama\", \"karşılaştırma\"], \"internal_note\": \"Müşterinin kullanım süresini ve bütçesini sor, ona göre yönlendir.\"}', 1, 20, '2025-10-13 18:13:20', '2025-10-13 18:13:20'),
(21, 2, '2. El', 'İkinci el forklift güvenilir midir?', 'Kaliteli 2. el forklift, düzenli bakımı yapılmış ve düşük çalışma saatine sahipse çok güvenilirdir. Biz tüm 2. el ekipmanları uzman teknisyenlerimize kontrol ettiririz, gerekli bakımları yaparız ve garanti ile satarız. Müşterilerimize ekipmanın servis geçmişini ve durum raporunu sunuyoruz.', '{\"icon\": \"fas fa-shield-alt\", \"tags\": [\"2. el\", \"güvenilirlik\", \"kontrol\"], \"internal_note\": \"2. el kalite standartları - güven verici mesaj.\"}', 1, 21, '2025-10-13 18:13:20', '2025-10-13 18:13:20'),
(22, 2, '2. El', 'İkinci el alırken nelere dikkat etmeliyim?', 'Çalışma saati (5000 saatten az ideal), servis geçmişi (düzenli bakım yapılmış mı), ekipman durumu (motor, hidrolik, fren, lastik kontrolü), garanti süresi (en az 6 ay), satıcının güvenilirliği. İxtif olarak tüm 2. el ekipmanlarımızda bu kriterleri sağlıyor ve detaylı durum raporu sunuyoruz.', '{\"icon\": \"fas fa-clipboard-check\", \"tags\": [\"2. el alım\", \"dikkat edilecekler\", \"çalışma saati\"], \"internal_note\": \"İkinci el alım rehberi - kriterlerimizi vurgula.\"}', 1, 22, '2025-10-13 18:13:20', '2025-10-13 18:13:20'),
(23, 2, '2. El', 'İkinci el forklift garanti veriyor musunuz?', 'Evet, sattığımız tüm 2. el ekipmanlara minimum 6 ay garanti veriyoruz. Garanti kapsamında motor, hidrolik sistem ve elektrik arızalarını karşılıyoruz. Garanti sonrası da uygun ücretli servis desteğimiz devam eder.', '{\"icon\": \"fas fa-award\", \"tags\": [\"2. el garanti\", \"garanti süresi\"], \"internal_note\": \"2. el garanti bilgisi - güven verici.\"}', 1, 23, '2025-10-13 18:13:20', '2025-10-13 18:13:20'),
(24, 2, '2. El', 'Eski ekipmanımı size satabilir miyim?', 'Evet, kullanılmış forklift, transpalet ve diğer istif ekipmanlarınızı değerlendirip satın alabiliriz. Uzman ekibimiz yerinde inceleme yapar, ekipmanın durumuna ve piyasa koşullarına göre adil bir teklif sunarız. Takas imkanlarımız da mevcuttur.', '{\"icon\": \"fas fa-recycle\", \"tags\": [\"2. el alım\", \"takas\", \"satış\"], \"internal_note\": \"Eski ekipman alımı - takas seçeneği vurgula.\"}', 1, 24, '2025-10-13 18:13:20', '2025-10-13 18:13:20'),
(25, 2, 'Sektörel Çözümler', 'Lojistik depolar için hangi ekipmanları önerirsiniz?', 'Lojistik depolar için yüksek kapasiteli forkliftler, reach truck\'lar (dar koridorlar için), elektrikli transpaletler ve AMR otonom robotlar öneriyoruz. Yüksek raf sistemlerinde reach truck, yoğun paletleme işlerinde elektrikli forklift idealdir. Depo büyüklüğüne ve iş yoğunluğuna göre ekipman planlaması yapabiliriz.', '{\"icon\": \"fas fa-shipping-fast\", \"tags\": [\"lojistik\", \"depo çözümleri\", \"reach truck\"], \"internal_note\": \"Lojistik sektörü özel çözümler - depo verimliliği vurgusu.\"}', 1, 25, '2025-10-13 18:13:20', '2025-10-13 18:13:20'),
(26, 2, 'Sektörel Çözümler', 'E-ticaret firmaları için ne önerirsiniz?', 'E-ticaret depoları hızlı sipariş hazırlama gerektirir. Elektrikli transpaletler (hızlı paket toplama), istif makineleri (raflardan çekme) ve AMR robotlar (otomatik taşıma) öneriyoruz. Dar koridorlu depolarda reach truck verimliliği artırır. Sezonluk yoğunluklar için esnek kiralama paketlerimiz var.', '{\"icon\": \"fas fa-shopping-cart\", \"tags\": [\"e-ticaret\", \"sipariş hazırlama\", \"amr\"], \"internal_note\": \"E-ticaret sektörü - hız ve otomasyon vurgusu.\"}', 1, 26, '2025-10-13 18:13:20', '2025-10-13 18:13:20'),
(27, 2, 'Sektörel Çözümler', 'Gıda sektörü için özel çözümleriniz var mı?', 'Gıda ve soğuk zincir depoları için paslanmaz çelik ekipmanlar, soğuk hava deposu uyumlu forkliftler ve hijyen standartlarına uygun transpaletler sunuyoruz. Elektrikli forkliftler egzoz gazı çıkarmadığı için gıda depolarında tercih edilir. HACCP standartlarına uygun ekipman sağlıyoruz.', '{\"icon\": \"fas fa-snowflake\", \"tags\": [\"gıda\", \"soğuk zincir\", \"hijyen\", \"haccp\"], \"internal_note\": \"Gıda sektörü - hijyen ve soğuk zincir uyumluluğu vurgula.\"}', 1, 27, '2025-10-13 18:13:20', '2025-10-13 18:13:20'),
(28, 2, 'AMR & Otomasyon', 'AMR otonom mobil robot nedir?', 'AMR (Autonomous Mobile Robot), yapay zeka ile kendi yolunu bulabilen, insan müdahalesi olmadan yük taşıyabilen robotlardır. Depoda palet ve malzeme taşıma işlerini otomatik yapar. Operatör ihtiyacını azaltır, hata oranını düşürür, 7/24 çalışabilir. Endüstri 4.0 dönüşümünün önemli bir parçasıdır.', '{\"icon\": \"fas fa-robot\", \"tags\": [\"amr\", \"otonom robot\", \"endüstri 4.0\"], \"internal_note\": \"AMR tanımı - otomasyon ve verimlilik vurgusu.\"}', 1, 28, '2025-10-13 18:13:20', '2025-10-13 18:13:20'),
(29, 2, 'AMR & Otomasyon', 'AMR robotlar hangi işletmelere uygundur?', 'Yüksek iş hacmi olan lojistik merkezleri, e-ticaret depoları, üretim tesisleri ve büyük perakende depoları için idealdir. Tekrarlayan taşıma işlerinin olduğu, operatör bulmanın zorlaştığı, 7/24 operasyon gereken yerlerde AMR büyük verimlilik sağlar. Küçük ve orta ölçekli işletmeler için kiralama seçeneği de mevcuttur.', '{\"icon\": \"fas fa-industry\", \"tags\": [\"amr kullanım\", \"lojistik otomasyon\"], \"internal_note\": \"AMR hedef kitle - işletme büyüklüğüne göre yönlendir.\"}', 1, 29, '2025-10-13 18:13:20', '2025-10-13 18:13:20'),
(30, 2, 'AMR & Otomasyon', 'AMR entegrasyonu nasıl yapılır?', 'AMR sistemleri mevcut depo altyapınıza kolayca entegre edilir. Önce depo haritalaması yapılır, rotalar belirlenir, yazılım konfigürasyonu tamamlanır. Mevcut WMS (Warehouse Management System) sisteminize bağlanabilir. Kurulum süresi depo büyüklüğüne göre 2-6 hafta arası sürer. Eğitim ve teknik destek hizmetimiz dahildir.', '{\"icon\": \"fas fa-network-wired\", \"tags\": [\"amr entegrasyon\", \"kurulum\", \"wms\"], \"internal_note\": \"AMR entegrasyon süreci - kolay kurulum vurgusu.\"}', 1, 30, '2025-10-13 18:13:20', '2025-10-13 18:13:20');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `ai_workflow_nodes`
--

CREATE TABLE `ai_workflow_nodes` (
  `id` bigint UNSIGNED NOT NULL,
  `node_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Unique identifier: ai_response, condition, etc.',
  `node_class` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Full PHP class path',
  `node_name` json NOT NULL COMMENT 'Multilingual name: {"en":"AI Response","tr":"AI Yanıtı"}',
  `node_description` json DEFAULT NULL COMMENT 'Multilingual description',
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'common' COMMENT 'common, ecommerce, communication, etc.',
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fa-circle' COMMENT 'FontAwesome icon class',
  `order` int NOT NULL DEFAULT '0' COMMENT 'Display order in palette',
  `is_global` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Available to all tenants',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Active/Inactive',
  `tenant_whitelist` json DEFAULT NULL COMMENT 'Array of tenant IDs if not global',
  `default_config` json DEFAULT NULL COMMENT 'Default configuration for new instances',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `tenant_conversation_flows`
--

CREATE TABLE `tenant_conversation_flows` (
  `id` bigint UNSIGNED NOT NULL COMMENT 'Akış ID - Benzersiz tanımlayıcı',
  `tenant_id` int UNSIGNED NOT NULL COMMENT 'Hangi tenant (örn: 2=ixtif.com, 3=diğer)',
  `flow_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Akış adı - Admin panelde görünen isim (örn: "E-Ticaret Satış Akışı")',
  `flow_description` text COLLATE utf8mb4_unicode_ci COMMENT 'Akış açıklaması - Admin için bilgi notu, kullanıcı görmez',
  `flow_data` json NOT NULL COMMENT 'Tüm akış yapısı: nodes (kutucuklar), edges (bağlantılar), positions - Drawflow JSON',
  `start_node_id` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'İlk çalışacak node ID - Akış buradan başlar (örn: "node_greeting_1")',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Aktif mi? 1=kullanımda, 0=devre dışı (sadece aktif olanlar çalışır)',
  `priority` int NOT NULL DEFAULT '0' COMMENT 'Öncelik - Birden fazla aktif flow varsa en düşük sayı çalışır (0 en yüksek öncelik)',
  `created_by` bigint UNSIGNED DEFAULT NULL COMMENT 'Akışı oluşturan admin user ID - users tablosundan',
  `updated_by` bigint UNSIGNED DEFAULT NULL COMMENT 'Son güncelleyen admin user ID - users tablosundan',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `tenant_conversation_flows`
--

INSERT INTO `tenant_conversation_flows` (`id`, `tenant_id`, `flow_name`, `flow_description`, `flow_data`, `start_node_id`, `is_active`, `priority`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(6, 2, 'İxtif AI Assistant', 'İxtif özel AI asistan - Global kurallar + İxtif satış tonu (coşkulu, SİZ hitabı, önce ürün göster)', '{\"edges\": [{\"id\": \"edge_1_2\", \"source\": \"node_1\", \"target\": \"node_2\"}, {\"id\": \"edge_2_3\", \"source\": \"node_2\", \"target\": \"node_3\"}, {\"id\": \"edge_3_4\", \"source\": \"node_3\", \"target\": \"node_4\"}, {\"id\": \"edge_3_4\", \"source\": \"node_3\", \"target\": \"node_4\"}, {\"id\": \"edge_3_9\", \"source\": \"node_3\", \"target\": \"node_9\"}, {\"id\": \"edge_3_11\", \"source\": \"node_3\", \"target\": \"node_11\"}, {\"id\": \"edge_3_9\", \"source\": \"node_3\", \"target\": \"node_9\"}, {\"id\": \"edge_4_5\", \"source\": \"node_4\", \"target\": \"node_5\"}, {\"id\": \"edge_5_6\", \"source\": \"node_5\", \"target\": \"node_6\"}, {\"id\": \"edge_5_7\", \"source\": \"node_5\", \"target\": \"node_7\"}, {\"id\": \"edge_6_8\", \"source\": \"node_6\", \"target\": \"node_8\"}, {\"id\": \"edge_7_8\", \"source\": \"node_7\", \"target\": \"node_8\"}, {\"id\": \"edge_8_9\", \"source\": \"node_8\", \"target\": \"node_9\"}, {\"id\": \"edge_9_10\", \"source\": \"node_9\", \"target\": \"node_10\"}, {\"id\": \"edge_10_12\", \"source\": \"node_10\", \"target\": \"node_12\"}, {\"id\": \"edge_11_10\", \"source\": \"node_11\", \"target\": \"node_10\"}, {\"id\": \"edge_12_13\", \"source\": \"node_12\", \"target\": \"node_13\"}, {\"id\": \"edge_13_14\", \"source\": \"node_13\", \"target\": \"node_14\"}], \"nodes\": [{\"id\": \"node_1\", \"name\": \"Karşılama\", \"type\": \"welcome\", \"class\": \"\", \"config\": {\"next_node\": \"node_2\", \"suggestions\": [\"Ürün ara\", \"Fiyat bilgisi\", \"İletişim\"], \"welcome_message\": \"Merhaba! Size nasıl yardımcı olabilirim?\", \"show_suggestions\": true}, \"position\": {\"x\": 91, \"y\": 62}}, {\"id\": \"node_2\", \"name\": \"Geçmiş Yükle\", \"type\": \"history_loader\", \"class\": \"\", \"config\": {\"limit\": 10, \"order\": \"asc\", \"next_node\": \"node_3\", \"include_system_messages\": false}, \"position\": {\"x\": 95, \"y\": 185}}, {\"id\": \"node_3\", \"name\": \"Niyet Analizi\", \"type\": \"sentiment_detection\", \"class\": \"\", \"config\": {\"next_node\": \"node_4\", \"sentiment_routes\": {\"browsing\": \"node_9\", \"question\": \"node_9\", \"comparison\": \"node_4\", \"purchase_intent\": \"node_4\", \"support_request\": \"node_11\"}, \"default_next_node\": \"node_9\"}, \"position\": {\"x\": 100, \"y\": 300}}, {\"id\": \"node_4\", \"name\": \"Kategori Tespit\", \"type\": \"category_detection\", \"class\": \"\", \"config\": {\"next_node\": \"node_5\", \"category_questions\": {\"forklift\": [{\"key\": \"capacity\", \"options\": [\"2 ton\", \"3 ton\", \"5 ton\"], \"question\": \"Hangi kapasite forklift arıyorsunuz?\"}, {\"key\": \"fuel\", \"options\": [\"Dizel\", \"Elektrikli\", \"LPG\"], \"question\": \"Yakıt tipi?\"}], \"transpalet\": [{\"key\": \"capacity\", \"options\": [\"1.5 ton\", \"2 ton\", \"2.5 ton\", \"3 ton\"], \"question\": \"Hangi kapasite transpalet arıyorsunuz?\"}, {\"key\": \"type\", \"options\": [\"Manuel\", \"Elektrikli\"], \"question\": \"Manuel mi elektrikli mi?\"}]}, \"no_category_next_node\": \"node_6\"}, \"position\": {\"x\": 351, \"y\": 212}}, {\"id\": \"node_5\", \"name\": \"Fiyat Sorgusu mu?\", \"type\": \"condition\", \"class\": \"\", \"config\": {\"keywords\": [\"fiyat\", \"kaç para\", \"ne kadar\", \"en ucuz\", \"en pahalı\"], \"true_node\": \"node_6\", \"false_node\": \"node_7\", \"condition_type\": \"contains_keywords\"}, \"position\": {\"x\": 619, \"y\": 218}}, {\"id\": \"node_6\", \"name\": \"Fiyat Sorgusu\", \"type\": \"price_query\", \"class\": \"\", \"config\": {\"limit\": 5, \"show_vat\": false, \"vat_rate\": 20, \"next_node\": \"node_8\", \"exclude_categories\": [44], \"no_products_next_node\": \"node_11\"}, \"position\": {\"x\": 916, \"y\": 209}}, {\"id\": \"node_7\", \"name\": \"Ürün Ara\", \"type\": \"product_search\", \"class\": \"\", \"config\": {\"next_node\": \"node_8\", \"search_limit\": 3, \"sort_by_stock\": true, \"use_meilisearch\": true, \"no_products_next_node\": \"node_11\"}, \"position\": {\"x\": 919, \"y\": 420}}, {\"id\": \"node_8\", \"name\": \"Stok Sırala\", \"type\": \"stock_sorter\", \"class\": \"\", \"config\": {\"next_node\": \"node_9\", \"exclude_out_of_stock\": false, \"high_stock_threshold\": 10}, \"position\": {\"x\": 1239.6666666666667, \"y\": 215}}, {\"id\": \"node_9\", \"name\": \"Context Hazırla\", \"type\": \"context_builder\", \"class\": \"\", \"config\": {\"next_node\": \"node_10\", \"history_limit\": 10, \"include_tenant_directives\": true, \"include_conversation_context\": true, \"include_conversation_history\": true}, \"position\": {\"x\": 1407, \"y\": 338.3333333333333}}, {\"id\": \"node_10\", \"name\": \"AI Cevap Üret\", \"type\": \"ai_response\", \"class\": \"\", \"config\": {\"next_node\": \"node_12\", \"max_tokens\": 500, \"temperature\": 0.7, \"system_prompt\": \"Sen İxtif.com satış danışmanısın. Forklift, transpalet ve istif makineleri satıyorsun.\\n\\n🎯 ANA İŞİMİZ (EN ÖNEMLİ!):\\n✅ TAM ÜRÜN SATIŞI (Forklift, Transpalet, İstif Makinesi)\\n✅ Endüstriyel ekipman tanıtımı ve satışı\\n✅ YEDEK PARÇA: En düşük öncelik (sadece müşteri isterse)\\n\\n🚨 GÜVENLİK KURALLARI\\n\\n❌ ÜRÜN UYDURMA YASAĞI:\\n- ASLA ürün/bilgi uydurma!\\n- SADECE veritabanından gelen ürünleri göster\\n- ASLA internetten bilgi alma!\\n\\n❌ İLETİŞİM UYDURMA YASAĞI:\\n- ASLA kendi iletişim bilgisi uyduramazsın!\\n- SADECE verilen iletişim bilgilerini kullan\\n- AYNEN KOPYALA!\\n\\n🔗 ÜRÜN LİNK FORMATI:\\n**{{ÜRÜN ADI}}** [LINK:shop:{{slug}}]\\n\\nMUTLAKA:\\n- Önce ** ile ürün adını sar\\n- Sonra boşluk\\n- Sonra [LINK:shop:slug]\\n- Slug\'u AYNEN kullan!\\n\\n📝 FORMATLAMA:\\n- Nokta kullanımı: \\\"3 ton\\\" (3. ton YASAK!)\\n- Liste: Her madde YENİ SATIRDA\\n- Title: AYNEN kullan, değiştirme!\\n\\n🌟 SATIŞ TONU (İXTİF ÖZEL!):\\n- COŞKULU ve ÖVÜCÜ konuş!\\n- \'Harika\', \'Mükemmel\', \'En popüler\', \'Muhteşem performans\'\\n- Link vermekten çekinme, coşkuyla öner!\\n- DAIMA **SİZ** kullan (asla \'sen\' deme)\\n- Emoji kullan! (4-5 emoji per mesaj) 😊 🎉 💪 ⚡ 🔥 ✨\\n\\n🗣️ SAMİMİ KONUŞMA:\\n- \\\"Nasılsın?\\\" → \\\"İyiyim teşekkürler! 😊 Size nasıl yardımcı olabilirim?\\\"\\n- \\\"Merhaba\\\" → \\\"Merhaba! 🎉 Size yardımcı olmaktan mutluluk duyarım!\\\"\\n- \\\"Nasıl\\\" → Bağlama göre yanıt ver (ürün mü soru mu?)\\n- ROBOT GİBİ KONUŞMA! Samimi ve arkadaşça ol!\\n\\n🚨 MEGA KRİTİK: ÖNCE ÜRÜN GÖSTER!\\n❌ ASLA önce soru sor, sonra ürün göster!\\n✅ DAIMA önce 3-5 ürün göster, SONRA soru sor!\\n\\nZORUNLU SIRALAMA:\\n1. Müşteri \'transpalet\', \'forklift\' söyler\\n2. SEN HEMEN 3-5 ÜRÜN LİNKİ GÖSTER!\\n3. Ürünleri ÖVER! (Harika!, Mükemmel!)\\n4. Fiyatları göster!\\n5. ANCAK SONRA soru sor: \'Hangi kapasite?\'\\n\\n📝 SORU FORMAT:\\nBirden fazla soru sorarken HTML liste kullan:\\n<ul>\\n<li>Kaç ton taşıma kapasitesi?</li>\\n<li>Manuel mi elektrikli mi?</li>\\n</ul>\\n\\n🚨 KATEGORİ KARIŞTIRMA YASAK!\\nMüşteri hangi kategoriyi söylerse SADECE O kategoriden ürün öner!\\n\\nKATEGORLER:\\n1. TRANSPALET: Zemin seviyesi, palet taşıma\\n2. FORKLIFT: Yüksek kaldırma, dikey istifleme\\n3. İSTİF MAKİNESİ: Sadece dikey istifleme\\n4. REACH TRUCK: Çok yüksek kaldırma, teleskopik\\n5. PLATFORM: Operatör + yük yükselir\\n6. TOW TRACTOR: Römork çekme\\n7. YEDEK PARÇA: Sadece müşteri isterse (EN DÜŞÜK ÖNCELİK!)\\n\\n🎯 ÜRÜN ÖNCELİKLENDİRME:\\n1. ✅ TAM ÜRÜN kategorilerini ÖNE! (Transpalet, Forklift, İstif)\\n2. ❌ YEDEK PARÇA kategorisini EN SONA!\\n3. ✅ Ana kategorilere odaklan (Endüstriyel ekipman)\\n\\n💰 FİYAT GÖSTERME:\\n1. ✅ formatted_price varsa → AYNEN göster\\n2. ❌ Fiyat yoksa → \\\"Fiyat teklifi için iletişim\\\"\\n3. ❌ ASLA hafızandan fiyat kullanma!\\n4. ❌ ASLA tahmin yapma!\\n\\n💱 CURRENCY:\\n- formatted_price zaten doğru formatta (örn: \\\"15.000 ₺\\\" veya \\\"$1,350\\\")\\n- Sen sadece AYNEN göster\\n- ASLA currency sembolü kendin ekleme!\\n\\n📞 TELEFON TOPLAMA:\\n🚨 ÜRÜN linklerini göstermeden WhatsApp numarası VERME!\\n\\nDOĞRU SIRA:\\n1. Merhaba\\n2. ÜRÜN LİNKLERİ GÖSTER (MUTLAKA!)\\n3. İlgilendiyse telefon iste\\n4. Telefon alamazsan → O zaman bizim numarayı ver\\n\\n📦 ÜRÜN BULUNAMADI:\\n❌ ASLA \'ürün bulunamadı\' DEME!\\n❌ ASLA \'elimizde yok\' DEME!\\n\\n✅ POZİTİF YANIT:\\n\\\"Harika soru! 🎉 İxtif olarak size kesinlikle yardımcı olabiliriz! 😊\\\"\\n\\n📝 MARKDOWN FORMAT (ZORUNLU!):\\n✅ DOĞRU:\\n⭐ **Ürün Adı** [LINK:shop:slug]\\n\\n- 1.500 kg taşıma kapasitesi\\n- Li-Ion batarya\\n- Ergonomik tasarım\\n\\nFiyat: $1.350\\n\\nKRİTİK:\\n- Her özellik AYRI SATIR\\n- Ürün adından sonra BOŞ SATIR\\n- FİYAT AYRI PARAGRAFTA!\\n- Her ⭐ yeni satırda!\\n\\n📋 YANIT KURALLARI:\\n❌ Reasoning gösterme!\\n❌ Self-talk yapma!\\n❌ Kullanıcının sorusunu tekrarlama!\\n❌ \\\"Anladım ki...\\\" DEME!\\n\\n✅ Direkt coşkulu yanıt ver!\\n✅ Hataları sessizce düzelt!\\n✅ Samimi ve arkadaşça konuş!\\n\\n❌ YASAKLAR:\\n- HTML tagları yasak (sadece <ul><li> soru için)\\n- Konu dışı konular\\n- Kategori karıştırma\\n- Ürün göstermeden WhatsApp verme\\n- \'sen\' hitabı (sadece SİZ!)\\n- Robot gibi konuşma!\\n\"}, \"position\": {\"x\": 1385.6666666666667, \"y\": 504}}, {\"id\": \"node_11\", \"name\": \"İletişim Bilgisi Ver\", \"type\": \"contact_request\", \"class\": \"\", \"config\": {\"next_node\": \"node_10\", \"callback_form_url\": \"/contact/callback\"}, \"position\": {\"x\": 927, \"y\": 545}}, {\"id\": \"node_12\", \"name\": \"Linkleri Render Et\", \"type\": \"link_generator\", \"class\": \"\", \"config\": {\"base_url\": \"https://ixtif.com\", \"next_node\": \"node_13\"}, \"position\": {\"x\": 1379, \"y\": 657}}, {\"id\": \"node_13\", \"name\": \"Mesajları Kaydet\", \"type\": \"message_saver\", \"class\": \"\", \"config\": {\"next_node\": \"node_14\", \"save_metadata\": true, \"save_user_message\": true, \"save_assistant_message\": true}, \"position\": {\"x\": 1387, \"y\": 858}}, {\"id\": \"node_14\", \"name\": \"Bitir\", \"type\": \"end\", \"class\": \"\", \"config\": [], \"position\": {\"x\": 1649, \"y\": 863.6666666666666}}]}', 'node_1', 1, 10, NULL, NULL, '2025-11-05 20:39:23', '2025-11-06 00:51:43');

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `ai_flows`
--
ALTER TABLE `ai_flows`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ai_flows_status_priority_index` (`status`,`priority`);

--
-- Tablo için indeksler `ai_knowledge_base`
--
ALTER TABLE `ai_knowledge_base`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ai_knowledge_base_tenant_id_is_active_index` (`tenant_id`,`is_active`),
  ADD KEY `ai_knowledge_base_tenant_id_category_index` (`tenant_id`,`category`),
  ADD KEY `ai_knowledge_base_tenant_id_index` (`tenant_id`),
  ADD KEY `ai_knowledge_base_category_index` (`category`),
  ADD KEY `ai_knowledge_base_is_active_index` (`is_active`);

--
-- Tablo için indeksler `ai_workflow_nodes`
--
ALTER TABLE `ai_workflow_nodes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ai_workflow_nodes_node_key_unique` (`node_key`),
  ADD KEY `ai_workflow_nodes_category_is_active_order_index` (`category`,`is_active`,`order`),
  ADD KEY `ai_workflow_nodes_is_global_index` (`is_global`);

--
-- Tablo için indeksler `tenant_conversation_flows`
--
ALTER TABLE `tenant_conversation_flows`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tenant_conversation_flows_tenant_id_is_active_index` (`tenant_id`,`is_active`),
  ADD KEY `tenant_conversation_flows_tenant_id_priority_index` (`tenant_id`,`priority`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `ai_flows`
--
ALTER TABLE `ai_flows`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Tablo için AUTO_INCREMENT değeri `ai_knowledge_base`
--
ALTER TABLE `ai_knowledge_base`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `ai_workflow_nodes`
--
ALTER TABLE `ai_workflow_nodes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Tablo için AUTO_INCREMENT değeri `tenant_conversation_flows`
--
ALTER TABLE `tenant_conversation_flows`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Akış ID - Benzersiz tanımlayıcı', AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
