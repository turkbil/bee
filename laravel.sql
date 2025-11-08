-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Anamakine: localhost
-- Üretim Zamanı: 08 Kas 2025, 15:14:04
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
-- Veritabanı: `laravel`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `ai_tenant_directives`
--

CREATE TABLE `ai_tenant_directives` (
  `id` bigint UNSIGNED NOT NULL COMMENT 'Directive ID - Benzersiz tanımlayıcı',
  `tenant_id` int UNSIGNED NOT NULL COMMENT 'Hangi tenant (örn: 2=ixtif.com)',
  `directive_key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Ayar anahtarı - Kod içinde kullanılan isim (örn: "greeting_style", "max_products")',
  `directive_value` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Ayar değeri - String, sayı, JSON olabilir (örn: "friendly", "5", "true")',
  `directive_type` enum('string','integer','boolean','json','array') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'string' COMMENT 'Değer tipi - Kod tarafında nasıl parse edileceğini belirler',
  `category` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general' COMMENT 'Kategori - Ayarları gruplamak için (general, behavior, pricing, contact, display, lead)',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Açıklama - Admin için bilgi, bu ayar ne işe yarar',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Aktif mi? 1=kullanımda, 0=devre dışı (sadece aktif olanlar okunur)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `ai_tenant_directives`
--

INSERT INTO `ai_tenant_directives` (`id`, `tenant_id`, `directive_key`, `directive_value`, `directive_type`, `category`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 2, 'category_boundary_strict', 'true', 'boolean', 'behavior', 'Kategori sınırlaması sıkı olsun mu?', 1, '2025-11-04 17:56:32', '2025-11-04 17:56:32'),
(2, 2, 'allow_cross_category', 'false', 'boolean', 'behavior', 'Kategori dışına çıkılabilir mi?', 1, '2025-11-04 17:56:32', '2025-11-04 17:56:32'),
(3, 2, 'auto_detect_category', 'true', 'boolean', 'behavior', 'Otomatik kategori tespiti aktif mi?', 1, '2025-11-04 17:56:32', '2025-11-04 17:56:32'),
(4, 2, 'priority_homepage_products', 'true', 'boolean', 'display', 'Anasayfa ürünleri öncelikli mi?', 1, '2025-11-04 17:56:32', '2025-11-04 17:56:32'),
(5, 2, 'sort_by_stock', 'true', 'boolean', 'display', 'Stok miktarına göre sırala', 1, '2025-11-04 17:56:32', '2025-11-04 17:56:32'),
(6, 2, 'max_products_per_response', '5', 'integer', 'display', 'Tek yanıtta maksimum kaç ürün gösterilsin', 1, '2025-11-04 17:56:32', '2025-11-04 17:56:32'),
(7, 2, 'show_price_without_asking', 'true', 'boolean', 'pricing', 'Fiyatları sormadan göster', 1, '2025-11-04 17:56:32', '2025-11-04 17:56:32'),
(8, 2, 'currency_conversion_enabled', 'true', 'boolean', 'pricing', 'Kur dönüşümü aktif mi?', 1, '2025-11-04 17:56:32', '2025-11-04 17:56:32'),
(9, 2, 'default_currency', 'USD', 'string', 'pricing', 'Varsayılan para birimi', 1, '2025-11-04 17:56:32', '2025-11-04 17:56:32'),
(10, 2, 'collect_phone_required', 'true', 'boolean', 'lead', 'Telefon numarası toplamak zorunlu mu?', 1, '2025-11-04 17:56:32', '2025-11-04 17:56:32'),
(11, 2, 'auto_save_leads', 'true', 'boolean', 'lead', 'Lead\'leri otomatik kaydet', 1, '2025-11-04 17:56:32', '2025-11-04 17:56:32'),
(12, 2, 'greeting_style', 'friendly', 'string', 'general', 'Selamlama tarzı (formal/friendly/professional)', 1, '2025-11-04 17:56:32', '2025-11-04 17:56:32'),
(13, 2, 'emoji_usage', 'moderate', 'string', 'general', 'Emoji kullanımı (none/moderate/heavy)', 1, '2025-11-04 17:56:32', '2025-11-04 17:56:32'),
(14, 2, 'no_hallucination_rule', '🚨 KRİTİK KURAL: ASLA dünyadan örnek verme (Toyota, Nissan vb. YASAK!). SADECE veritabanında bulunan ürünlerden bahset. Bizde yoksa müşteri temsilcisine yönlendir.', 'string', 'shop_assistant', 'HALÜSİNASYON YASAK - Sadece DB ürünleri', 1, NULL, NULL),
(15, 2, 'price_format', 'Fiyat belirtirken KDV hariç fiyat ver ve \"KDV sonradan eklenir\" notu ekle.', 'string', 'shop_assistant', 'Fiyat gösterimi - KDV hariç', 1, NULL, NULL),
(16, 2, 'product_suggestion_style', 'Ürün önerirken teknik özellikleri vurgula (kapasite, yakıt tipi, marka, model).', 'string', 'shop_assistant', 'Ürün önerisi formatı', 1, NULL, NULL),
(17, 2, 'response_tone', 'Her zaman profesyonel ve yardımsever ol. Kısa ve öz cevap ver (2-3 cümle).', 'string', 'shop_assistant', 'Yanıt tonu ve uzunluğu', 1, NULL, NULL),
(18, 2, 'link_format', 'Link verirken [LINK:shop:product:slug] formatını kullan.', 'string', 'shop_assistant', 'Link formatı', 1, NULL, NULL),
(19, 2, 'spare_parts_priority', 'Yedek parça kategorisini (ID:44) sadece kullanıcı açıkça isterse öner.', 'string', 'shop_assistant', 'Yedek parça önceliği', 1, NULL, NULL),
(20, 2, 'payment_delivery_redirect', 'Ödeme ve teslimat soruları için müşteri temsilcisine yönlendir.', 'string', 'shop_assistant', 'Ödeme/Teslimat yönlendirme', 1, NULL, NULL),
(21, 2, 'stock_privacy', 'Stok bilgisini müşteriye ASLA söyleme. Sadece ürün önerilerini stoka göre sırala (Featured → Yüksek Stok → Normal).', 'string', 'shop_assistant', 'Stok gizliliği', 1, NULL, NULL);

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

--
-- Tablo döküm verisi `ai_workflow_nodes`
--

INSERT INTO `ai_workflow_nodes` (`id`, `node_key`, `node_class`, `node_name`, `node_description`, `category`, `icon`, `order`, `is_global`, `is_active`, `tenant_whitelist`, `default_config`, `created_at`, `updated_at`) VALUES
(1, 'welcome', 'App\\Services\\ConversationNodes\\Common\\WelcomeNode', '{\"en\": \"Welcome\", \"tr\": \"Karşılama\"}', '{\"en\": \"Welcome message\", \"tr\": \"Kullanıcıyı karşılama mesajı\"}', 'flow', 'ti ti-hand-stop', 1, 1, 1, NULL, NULL, NULL, NULL),
(2, 'condition', 'App\\Services\\ConversationNodes\\Common\\ConditionNode', '{\"en\": \"Condition\", \"tr\": \"Şart Kontrolü\"}', '{\"en\": \"IF/ELSE logic\", \"tr\": \"IF/ELSE mantığı\"}', 'flow', 'ti ti-git-branch', 2, 1, 1, NULL, NULL, NULL, NULL),
(3, 'end', 'App\\Services\\ConversationNodes\\Common\\EndNode', '{\"en\": \"End\", \"tr\": \"Bitir\"}', '{\"en\": \"End conversation\", \"tr\": \"Sohbeti bitir\"}', 'flow', 'ti ti-flag-filled', 3, 1, 1, NULL, NULL, NULL, NULL),
(4, 'ai_response', 'App\\Services\\ConversationNodes\\Common\\AIResponseNode', '{\"en\": \"AI Response\", \"tr\": \"AI Yanıt\"}', '{\"en\": \"Generate AI response\", \"tr\": \"AI cevap üretme\"}', 'ai', 'ti ti-robot', 1, 1, 1, NULL, NULL, NULL, NULL),
(5, 'context_builder', 'App\\Services\\ConversationNodes\\Common\\ContextBuilderNode', '{\"en\": \"Build Context\", \"tr\": \"Context Hazırla\"}', '{\"en\": \"Prepare context for AI\", \"tr\": \"AI için context hazırla\"}', 'data', 'ti ti-database', 1, 1, 1, NULL, NULL, NULL, NULL),
(6, 'history_loader', 'App\\Services\\ConversationNodes\\Common\\HistoryLoaderNode', '{\"en\": \"Load History\", \"tr\": \"Geçmiş Yükle\"}', '{\"en\": \"Load conversation history\", \"tr\": \"Konuşma geçmişini yükle\"}', 'data', 'ti ti-clock-hour-4', 2, 1, 1, NULL, NULL, NULL, NULL),
(7, 'message_saver', 'App\\Services\\ConversationNodes\\Common\\MessageSaverNode', '{\"en\": \"Save Message\", \"tr\": \"Mesaj Kaydet\"}', '{\"en\": \"Save messages to database\", \"tr\": \"Mesajları veritabanına kaydet\"}', 'data', 'ti ti-device-floppy', 3, 1, 1, NULL, NULL, NULL, NULL),
(8, 'collect_data', 'App\\Services\\ConversationNodes\\Common\\CollectDataNode', '{\"en\": \"Collect Data\", \"tr\": \"Veri Topla\"}', '{\"en\": \"Collect data from user\", \"tr\": \"Kullanıcıdan veri topla\"}', 'input', 'ti ti-forms', 1, 1, 1, NULL, NULL, NULL, NULL),
(9, 'sentiment_detection', 'App\\Services\\ConversationNodes\\Common\\SentimentDetectionNode', '{\"en\": \"Sentiment Detection\", \"tr\": \"Niyet Analizi\"}', '{\"en\": \"Detect user intent\", \"tr\": \"Kullanıcı niyetini tespit et\"}', 'analysis', 'ti ti-brain', 1, 1, 1, NULL, NULL, NULL, NULL),
(10, 'link_generator', 'App\\Services\\ConversationNodes\\Common\\LinkGeneratorNode', '{\"en\": \"Generate Links\", \"tr\": \"Link Oluştur\"}', '{\"en\": \"Convert custom links to URLs\", \"tr\": \"Custom linkleri URL\'e çevir\"}', 'output', 'ti ti-link', 1, 1, 1, NULL, NULL, NULL, NULL),
(11, 'share_contact', 'App\\Services\\ConversationNodes\\Common\\ShareContactNode', '{\"en\": \"Share Contact\", \"tr\": \"İletişim Paylaş\"}', '{\"en\": \"Share contact information\", \"tr\": \"İletişim bilgilerini paylaş\"}', 'output', 'ti ti-share', 2, 1, 1, NULL, NULL, NULL, NULL),
(12, 'webhook', 'App\\Services\\ConversationNodes\\Common\\WebhookNode', '{\"en\": \"Webhook\", \"tr\": \"Webhook\"}', '{\"en\": \"External API call\", \"tr\": \"External API çağrısı\"}', 'integration', 'ti ti-webhook', 1, 1, 1, NULL, NULL, NULL, NULL),
(13, 'product_search', 'App\\Services\\ConversationNodes\\Shop\\ProductSearchNode', '{\"en\": \"Product Search\", \"tr\": \"Ürün Ara\"}', '{\"en\": \"Product search with Meilisearch/DB\", \"tr\": \"Meilisearch/DB ile ürün arama (HALÜSİNASYON YASAK)\"}', 'shop', 'ti ti-search', 1, 1, 1, '[2, 3]', NULL, NULL, NULL),
(14, 'price_query', 'App\\Services\\ConversationNodes\\Shop\\PriceQueryNode', '{\"en\": \"Price Query\", \"tr\": \"Fiyat Sorgusu\"}', '{\"en\": \"Price-based queries\", \"tr\": \"Fiyat bazlı sorgular (en ucuz, en pahalı)\"}', 'shop', 'ti ti-currency-lira', 2, 1, 1, '[2, 3]', NULL, NULL, NULL),
(15, 'category_detection', 'App\\Services\\ConversationNodes\\Shop\\CategoryDetectionNode', '{\"en\": \"Category Detection\", \"tr\": \"Kategori Tespit\"}', '{\"en\": \"Detect category and ask questions\", \"tr\": \"Kategori tespit ve özel sorular\"}', 'shop', 'ti ti-category', 3, 1, 1, '[2, 3]', NULL, NULL, NULL),
(16, 'currency_converter', 'App\\Services\\ConversationNodes\\Shop\\CurrencyConverterNode', '{\"en\": \"Currency Converter\", \"tr\": \"Döviz Çevirici\"}', '{\"en\": \"Convert prices to USD/EUR\", \"tr\": \"TL/USD/EUR çevirici (tenant kuru)\"}', 'shop', 'ti ti-currency-dollar', 4, 1, 1, '[2, 3]', NULL, NULL, NULL),
(17, 'product_comparison', 'App\\Services\\ConversationNodes\\Shop\\ProductComparisonNode', '{\"en\": \"Product Comparison\", \"tr\": \"Ürün Karşılaştır\"}', '{\"en\": \"Compare two products\", \"tr\": \"İki ürünü karşılaştır ve farkları göster\"}', 'shop', 'ti ti-arrows-left-right', 5, 1, 1, '[2, 3]', NULL, NULL, NULL),
(18, 'contact_request', 'App\\Services\\ConversationNodes\\Shop\\ContactRequestNode', '{\"en\": \"Contact Request\", \"tr\": \"İletişim İsteği\"}', '{\"en\": \"Show contact information\", \"tr\": \"İletişim bilgilerini settings\'ten göster\"}', 'shop', 'ti ti-phone', 6, 1, 1, '[2, 3]', NULL, NULL, NULL),
(19, 'stock_sorter', 'App\\Services\\ConversationNodes\\Shop\\StockSorterNode', '{\"en\": \"Stock Sorter\", \"tr\": \"Stok Sırala\"}', '{\"en\": \"Sort by stock priority\", \"tr\": \"Stok sıralaması (Featured → Yüksek Stok → Normal)\"}', 'shop', 'ti ti-sort-ascending-numbers', 7, 1, 1, '[2, 3]', NULL, NULL, NULL);

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
(1, 2, 'İxtif.com E-Ticaret Akışı', 'İxtif.com için basit e-ticaret satış akışı (Karşılama → Kategori Tespit → Ürün Önerme)', '{\"edges\": [{\"id\": \"edge_1\", \"source\": \"node_greeting\", \"target\": \"node_category\"}, {\"id\": \"edge_2\", \"source\": \"node_category\", \"target\": \"node_products\"}], \"nodes\": [{\"id\": \"node_greeting\", \"name\": \"Karşılama\", \"type\": \"ai_response\", \"class\": \"App\\\\Services\\\\ConversationNodes\\\\Common\\\\AIResponseNode\", \"config\": {\"next_node\": \"node_category\", \"system_prompt\": \"Müşteriyi sıcak karşıla. İxtif.com endüstriyel ekipman satış asistanısın. Transpalet, forklift gibi ürünler hakkında yardımcı olabilirsin.\"}, \"position\": {\"x\": 100, \"y\": 100}}, {\"id\": \"node_category\", \"name\": \"Kategori Tespit\", \"type\": \"category_detection\", \"class\": \"App\\\\Services\\\\ConversationNodes\\\\TenantSpecific\\\\Tenant_2\\\\CategoryDetectionNode\", \"config\": {\"category_found_node\": \"node_products\", \"category_not_found_node\": \"node_greeting\"}, \"position\": {\"x\": 100, \"y\": 300}}, {\"id\": \"node_products\", \"name\": \"Ürün Önerme\", \"type\": \"product_recommendation\", \"class\": \"App\\\\Services\\\\ConversationNodes\\\\TenantSpecific\\\\Tenant_2\\\\ProductRecommendationNode\", \"config\": {\"limit\": 5, \"next_node\": null, \"include_price\": true}, \"position\": {\"x\": 100, \"y\": 500}}]}', 'node_greeting', 1, 1, NULL, NULL, '2025-11-04 17:56:32', '2025-11-04 17:56:32'),
(2, 0, 'Global AI Assistant Template', 'ŞABLON - Tüm tenant\'\'lar için kullanılabilir temel kural seti. Yeni tenant oluştururken bu şablonu kopyala.', '{\"edges\": [{\"id\": \"edge_1\", \"source\": \"node_1\", \"target\": \"node_2\"}, {\"id\": \"edge_2\", \"source\": \"node_2\", \"target\": \"node_3\"}, {\"id\": \"edge_3_purchase\", \"source\": \"node_3\", \"target\": \"node_4\", \"sourceOutput\": \"purchase_intent\"}, {\"id\": \"edge_3_comparison\", \"source\": \"node_3\", \"target\": \"node_4\", \"sourceOutput\": \"comparison\"}, {\"id\": \"edge_3_question\", \"source\": \"node_3\", \"target\": \"node_9\", \"sourceOutput\": \"question\"}, {\"id\": \"edge_3_support\", \"source\": \"node_3\", \"target\": \"node_11\", \"sourceOutput\": \"support\"}, {\"id\": \"edge_3_browsing\", \"source\": \"node_3\", \"target\": \"node_9\", \"sourceOutput\": \"browsing\"}, {\"id\": \"edge_4\", \"source\": \"node_4\", \"target\": \"node_5\"}, {\"id\": \"edge_5_true\", \"source\": \"node_5\", \"target\": \"node_6\"}, {\"id\": \"edge_5_false\", \"source\": \"node_5\", \"target\": \"node_7\"}, {\"id\": \"edge_6\", \"source\": \"node_6\", \"target\": \"node_8\"}, {\"id\": \"edge_7\", \"source\": \"node_7\", \"target\": \"node_8\"}, {\"id\": \"edge_8\", \"source\": \"node_8\", \"target\": \"node_9\"}, {\"id\": \"edge_9\", \"source\": \"node_9\", \"target\": \"node_10\"}, {\"id\": \"edge_10\", \"source\": \"node_10\", \"target\": \"node_12\"}, {\"id\": \"edge_11\", \"source\": \"node_11\", \"target\": \"node_10\"}, {\"id\": \"edge_12\", \"source\": \"node_12\", \"target\": \"node_13\"}, {\"id\": \"edge_13\", \"source\": \"node_13\", \"target\": \"node_14\"}], \"nodes\": [{\"id\": \"node_1\", \"name\": \"Karşılama\", \"type\": \"welcome\", \"config\": {\"next_node\": \"node_2\", \"suggestions\": [\"Ürün ara\", \"Fiyat bilgisi\", \"İletişim\"], \"welcome_message\": \"Merhaba! Size nasıl yardımcı olabilirim?\", \"show_suggestions\": true}, \"position\": {\"x\": 100, \"y\": 100}}, {\"id\": \"node_2\", \"name\": \"Geçmiş Yükle\", \"type\": \"history_loader\", \"config\": {\"limit\": 10, \"order\": \"asc\", \"next_node\": \"node_3\", \"include_system_messages\": false}, \"position\": {\"x\": 100, \"y\": 200}}, {\"id\": \"node_3\", \"name\": \"Niyet Analizi\", \"type\": \"sentiment_detection\", \"config\": {\"next_node\": \"node_4\", \"sentiment_routes\": {\"browsing\": \"node_9\", \"question\": \"node_9\", \"comparison\": \"node_4\", \"purchase_intent\": \"node_4\", \"support_request\": \"node_11\"}, \"default_next_node\": \"node_9\"}, \"position\": {\"x\": 100, \"y\": 300}}, {\"id\": \"node_4\", \"name\": \"Kategori Tespit\", \"type\": \"category_detection\", \"config\": {\"next_node\": \"node_5\", \"category_questions\": {\"forklift\": [{\"key\": \"capacity\", \"options\": [\"2 ton\", \"3 ton\", \"5 ton\"], \"question\": \"Hangi kapasite forklift arıyorsunuz?\"}, {\"key\": \"fuel\", \"options\": [\"Dizel\", \"Elektrikli\", \"LPG\"], \"question\": \"Yakıt tipi?\"}], \"transpalet\": [{\"key\": \"capacity\", \"options\": [\"1.5 ton\", \"2 ton\", \"2.5 ton\", \"3 ton\"], \"question\": \"Hangi kapasite transpalet arıyorsunuz?\"}, {\"key\": \"type\", \"options\": [\"Manuel\", \"Elektrikli\"], \"question\": \"Manuel mi elektrikli mi?\"}]}, \"no_category_next_node\": \"node_6\"}, \"position\": {\"x\": 300, \"y\": 400}}, {\"id\": \"node_5\", \"name\": \"Fiyat Sorgusu mu?\", \"type\": \"condition\", \"config\": {\"keywords\": [\"fiyat\", \"kaç para\", \"ne kadar\", \"en ucuz\", \"en pahalı\"], \"true_node\": \"node_6\", \"false_node\": \"node_7\", \"condition_type\": \"contains_keywords\"}, \"position\": {\"x\": 300, \"y\": 500}}, {\"id\": \"node_6\", \"name\": \"Fiyat Sorgusu\", \"type\": \"price_query\", \"config\": {\"limit\": 5, \"show_vat\": false, \"vat_rate\": 20, \"next_node\": \"node_8\", \"exclude_categories\": [44], \"no_products_next_node\": \"node_11\"}, \"position\": {\"x\": 500, \"y\": 500}}, {\"id\": \"node_7\", \"name\": \"Ürün Ara\", \"type\": \"product_search\", \"config\": {\"next_node\": \"node_8\", \"search_limit\": 3, \"sort_by_stock\": true, \"use_meilisearch\": true, \"no_products_next_node\": \"node_11\"}, \"position\": {\"x\": 500, \"y\": 600}}, {\"id\": \"node_8\", \"name\": \"Stok Sırala\", \"type\": \"stock_sorter\", \"config\": {\"next_node\": \"node_9\", \"exclude_out_of_stock\": false, \"high_stock_threshold\": 10}, \"position\": {\"x\": 700, \"y\": 550}}, {\"id\": \"node_9\", \"name\": \"Context Hazırla\", \"type\": \"context_builder\", \"config\": {\"next_node\": \"node_10\", \"history_limit\": 10, \"include_tenant_directives\": true, \"include_conversation_context\": true, \"include_conversation_history\": true}, \"position\": {\"x\": 900, \"y\": 400}}, {\"id\": \"node_10\", \"name\": \"AI Cevap Üret\", \"type\": \"ai_response\", \"config\": {\"next_node\": \"node_12\", \"max_tokens\": 500, \"temperature\": 0.7, \"system_prompt\": \"Sen bu firmanın AI satış danışmanısın.\\n\\n🚨 GÜVENLİK KURALLARI (EN ÖNEMLİ!)\\n\\n❌ ÜRÜN UYDURMA YASAĞI:\\n- ASLA ürün/bilgi uydurma!\\n- SADECE veritabanından gelen ürünleri göster\\n- ASLA internetten bilgi alma!\\n\\n❌ İLETİŞİM UYDURMA YASAĞI:\\n- ASLA kendi iletişim bilgisi uyduramazsın!\\n- SADECE verilen iletişim bilgilerini kullan\\n- AYNEN KOPYALA!\\n\\n🔗 ÜRÜN LİNK FORMATI:\\n**{{ÜRÜN ADI}}** [LINK:shop:{{slug}}]\\n\\nMUTLAKA:\\n- Önce ** ile ürün adını sar\\n- Sonra boşluk\\n- Sonra [LINK:shop:slug]\\n- Slug\'u AYNEN kullan!\\n\\n📝 FORMATLAMA:\\n- Nokta kullanımı: \\\"3 ton\\\" (3. ton YASAK!)\\n- Liste: Her madde YENİ SATIRDA\\n- Title: AYNEN kullan, değiştirme!\\n\\n🗣️ KONUŞMA TARZI:\\n✅ DOĞAL VE SAMİMİ:\\n- İnsan gibi, arkadaşça\\n- Nazik ve yardımsever\\n- Kısa, net cümleler\\n\\n❌ ASLA YAPMA:\\n- \\\"Ben yapay zeka asistanıyım\\\" DEME!\\n- \\\"Duygularım yok\\\" DEME!\\n- Robotik dil kullanma!\\n- Model adını söyleme!\\n\\n📋 YANIT KURALLARI:\\n❌ Reasoning gösterme!\\n❌ Self-talk yapma!\\n❌ Kullanıcının sorusunu tekrarlama!\\n❌ \\\"Anladım ki...\\\" / \\\"Haklısınız...\\\" DEME!\\n\\n✅ Direkt profesyonel yanıt ver!\\n✅ Hataları sessizce düzelt!\\n\\n💰 FİYAT GÖSTERME:\\n1. ✅ formatted_price varsa → AYNEN göster\\n2. ❌ Fiyat yoksa → \\\"Fiyat teklifi için iletişim\\\"\\n3. ❌ ASLA hafızandan fiyat kullanma!\\n4. ❌ ASLA tahmin yapma!\\n\\n💱 CURRENCY KURALLARI:\\n- Currency bilgisi shop_currencies tablosundan gelir\\n- formatted_price zaten doğru formatta gelir (örn: \\\"15.000 ₺\\\" veya \\\"$1,350\\\")\\n- Sen sadece formatted_price\'ı AYNEN göster\\n- ASLA currency sembolü kendin ekleme!\\n\\n⚙️ AYARLAR SİSTEMİ:\\n- İletişim bilgileri settings_values tablosundan gelir\\n- contact_whatsapp_1, contact_phone_1, contact_email_1\\n- AI kişilik ayarları: ai_assistant_name, ai_response_tone\\n- Sana verilen değerleri AYNEN kullan!\\n\\n📝 FORMAT KURALLARI:\\n- Markdown kullan (HTML yasak!)\\n- Link: **Ürün** [LINK:shop:slug]\\n- Liste: Her madde ayrı satır\\n- Boş satır kullan\\n\\n❌ YASAKLAR:\\n- HTML tagları yasak\\n- Konu dışı konular\\n- Rakip firma ürünleri\"}, \"position\": {\"x\": 900, \"y\": 500}}, {\"id\": \"node_11\", \"name\": \"İletişim Bilgisi Ver\", \"type\": \"contact_request\", \"config\": {\"next_node\": \"node_10\", \"callback_form_url\": \"/contact/callback\"}, \"position\": {\"x\": 500, \"y\": 700}}, {\"id\": \"node_12\", \"name\": \"Linkleri Render Et\", \"type\": \"link_generator\", \"config\": {\"base_url\": \"https://ixtif.com\", \"next_node\": \"node_13\"}, \"position\": {\"x\": 1100, \"y\": 500}}, {\"id\": \"node_13\", \"name\": \"Mesajları Kaydet\", \"type\": \"message_saver\", \"config\": {\"next_node\": \"node_14\", \"save_metadata\": true, \"save_user_message\": true, \"save_assistant_message\": true}, \"position\": {\"x\": 1100, \"y\": 600}}, {\"id\": \"node_14\", \"name\": \"Bitir\", \"type\": \"end\", \"config\": {}, \"position\": {\"x\": 1100, \"y\": 700}}]}', 'node_1', 0, 99, NULL, NULL, '2025-11-05 20:37:09', '2025-11-05 20:37:09');

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `ai_tenant_directives`
--
ALTER TABLE `ai_tenant_directives`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_tenant_key` (`tenant_id`,`directive_key`),
  ADD KEY `ai_tenant_directives_tenant_id_category_index` (`tenant_id`,`category`);

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
-- Tablo için AUTO_INCREMENT değeri `ai_tenant_directives`
--
ALTER TABLE `ai_tenant_directives`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Directive ID - Benzersiz tanımlayıcı', AUTO_INCREMENT=22;

--
-- Tablo için AUTO_INCREMENT değeri `ai_workflow_nodes`
--
ALTER TABLE `ai_workflow_nodes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Tablo için AUTO_INCREMENT değeri `tenant_conversation_flows`
--
ALTER TABLE `tenant_conversation_flows`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Akış ID - Benzersiz tanımlayıcı', AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
