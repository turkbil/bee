-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Anamakine: localhost
-- Üretim Zamanı: 08 Kas 2025, 15:13:26
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
(1, 2, 'welcome_message', 'Merhaba! Nasıl yardımcı olabilirim?', 'string', 'chat', 'Chat başlangıcında gösterilen karşılama mesajı', 1, '2025-11-06 12:37:06', '2025-11-06 12:39:27'),
(2, 2, 'max_tokens', '500', 'integer', 'ai_config', 'AI yanıtlarının maksimum token sayısı', 1, '2025-11-06 12:37:06', '2025-11-06 12:37:06'),
(3, 2, 'temperature', '0.7', 'string', 'ai_config', 'AI yaratıcılık seviyesi (0-1 arası)', 1, '2025-11-06 12:37:06', '2025-11-06 12:37:06'),
(7, 2, 'welcome_variations', '[\"🎯 Hangi ürünümüz ilginizi çekti?\", \"💼 Size nasıl yardımcı olabilirim?\", \"🚚 Hangi ürünü arıyorsunuz?\", \"✨ Hoş geldiniz! Ne lazım?\", \"💡 Merhaba! Ürün mü arıyorsunuz?\"]', 'json', 'chat', 'Karşılama mesajı çeşitleri', 1, NULL, NULL),
(8, 2, 'product_found_responses', '[\"🔥 İşte en uygun seçenekler:\", \"✅ Tam aradığınız ürünler:\", \"💡 Size özel fiyatlar:\", \"🎯 Bu ürünler tam size göre:\", \"⭐ En çok satanlar:\"]', 'json', 'chat', 'Ürün bulundu yanıtları', 1, NULL, NULL),
(9, 2, 'call_to_action', '[\"📞 Detaylı bilgi: 0212 XXX XX XX\", \"💬 Hemen sipariş verin!\", \"🚚 Bugün sipariş, yarın kargoda!\", \"✅ Tıklayın, detaylı bilgi alın!\", \"💰 Özel fiyat için arayın!\"]', 'json', 'chat', 'Harekete geçirici mesajlar', 1, NULL, NULL),
(10, 2, 'system_prompt_override', 'Satış odaklı konuş. Ürün özellikleri ve fiyatları vurgula. Doğal dil kullan.', 'string', 'ai_config', 'AI sistem prompt override', 1, NULL, NULL),
(11, 2, 'chatbot_system_prompt', 'Sen profesyonel bir e-ticaret satış asistanısın.\n\n**KRİTİK KURALLAR:**\n\n1. **ÜRÜN VARSA:**\n   - {product_context} içindeki ürünleri kullan\n   - ASLA ürün uydurma, sadece listedeki ürünleri göster\n   - Fiyatları göster (zaten formatlı)\n   - Stok durumunu belirt\n   - Link\'leri paylaş\n\n2. **ÜRÜN YOKSA:**\n   - \"Aradığınız ürün şu anda stoklarımızda bulunmuyor.\"\n   - \"Müşteri temsilcimiz size yardımcı olabilir.\"\n   - \"Lütfen iletişim bilgilerinizi paylaşır mısınız?\"\n   - ASLA ürün uydurma!\n\n3. **KONUŞMA:**\n   - Konuşma geçmişini kontrol et\n   - Kullanıcı adını hatırla\n   - Samimi ama profesyonel ol\n   - Emoji kullan ama abartma\n\n**YAPMA:**\n❌ Olmayan ürün uydurma\n❌ Fiyat uydurma\n❌ \"Model A, B, C\" gibi genel isimler\n❌ \"Stokta uygun ürün yok\" sonra ürün gösterme', 'string', 'chatbot', 'Ana chatbot system prompt', 1, NULL, NULL),
(12, 2, 'chatbot_no_product_response', '🔍 Aradığınız ürün şu anda stoklarımızda bulunmuyor.\n\n💬 **Müşteri temsilcimiz size yardımcı olabilir!**\n\nLütfen iletişim bilgilerinizi (telefon/email) paylaşır mısınız? En kısa sürede size dönüş yapacağız.', 'string', 'chatbot', 'Ürün bulunamadığında gösterilecek mesaj', 1, NULL, NULL),
(13, 2, 'chatbot_hallucination_prevention', 'true', 'boolean', 'chatbot', 'AI hallucination\'ı engelle - sadece gerçek ürünleri göster', 1, NULL, NULL),
(14, 2, 'chatbot_require_product_context', 'true', 'boolean', 'chatbot', 'product_context olmadan ürün önerme', 1, NULL, NULL);

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
-- Tablo için AUTO_INCREMENT değeri `ai_flows`
--
ALTER TABLE `ai_flows`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Tablo için AUTO_INCREMENT değeri `ai_tenant_directives`
--
ALTER TABLE `ai_tenant_directives`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Directive ID - Benzersiz tanımlayıcı', AUTO_INCREMENT=15;

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
