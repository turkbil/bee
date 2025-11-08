/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tenant_conversation_flows` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'Akış ID - Benzersiz tanımlayıcı',
  `tenant_id` int unsigned NOT NULL COMMENT 'Hangi tenant (örn: 2=ixtif.com, 3=diğer)',
  `flow_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Akış adı - Admin panelde görünen isim (örn: "E-Ticaret Satış Akışı")',
  `flow_description` text COLLATE utf8mb4_unicode_ci COMMENT 'Akış açıklaması - Admin için bilgi notu, kullanıcı görmez',
  `flow_data` json NOT NULL COMMENT 'Tüm akış yapısı: nodes (kutucuklar), edges (bağlantılar), positions - Drawflow JSON',
  `start_node_id` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'İlk çalışacak node ID - Akış buradan başlar (örn: "node_greeting_1")',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Aktif mi? 1=kullanımda, 0=devre dışı (sadece aktif olanlar çalışır)',
  `priority` int NOT NULL DEFAULT '0' COMMENT 'Öncelik - Birden fazla aktif flow varsa en düşük sayı çalışır (0 en yüksek öncelik)',
  `created_by` bigint unsigned DEFAULT NULL COMMENT 'Akışı oluşturan admin user ID - users tablosundan',
  `updated_by` bigint unsigned DEFAULT NULL COMMENT 'Son güncelleyen admin user ID - users tablosundan',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tenant_conversation_flows_tenant_id_is_active_index` (`tenant_id`,`is_active`),
  KEY `tenant_conversation_flows_tenant_id_priority_index` (`tenant_id`,`priority`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `tenant_conversation_flows` VALUES (1,1,'Demo AI Flow','Simple demo flow for a.test','{\"edges\": [], \"nodes\": [{\"id\": \"node_welcome\", \"name\": \"Welcome Message\", \"type\": \"ai_response\", \"class\": \"App\\\\Services\\\\ConversationNodes\\\\Common\\\\AIResponseNode\", \"config\": {\"system_prompt\": \"Hello\\\\! I am an AI assistant. How can I help you today?\"}, \"position\": {\"x\": 100, \"y\": 100}}]}','node_welcome',1,1,NULL,NULL,'2025-11-04 21:57:32','2025-11-04 21:57:32');
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ai_tenant_directives` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'Directive ID - Benzersiz tanımlayıcı',
  `tenant_id` int unsigned NOT NULL COMMENT 'Hangi tenant (örn: 2=ixtif.com)',
  `directive_key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Ayar anahtarı - Kod içinde kullanılan isim (örn: "greeting_style", "max_products")',
  `directive_value` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Ayar değeri - String, sayı, JSON olabilir (örn: "friendly", "5", "true")',
  `directive_type` enum('string','integer','boolean','json','array') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'string' COMMENT 'Değer tipi - Kod tarafında nasıl parse edileceğini belirler',
  `category` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general' COMMENT 'Kategori - Ayarları gruplamak için (general, behavior, pricing, contact, display, lead)',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Açıklama - Admin için bilgi, bu ayar ne işe yarar',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Aktif mi? 1=kullanımda, 0=devre dışı (sadece aktif olanlar okunur)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_tenant_key` (`tenant_id`,`directive_key`),
  KEY `ai_tenant_directives_tenant_id_category_index` (`tenant_id`,`category`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `ai_tenant_directives` VALUES (1,1,'greeting_style','friendly','string','general',NULL,1,'2025-11-04 21:57:32','2025-11-04 21:57:32');
INSERT INTO `ai_tenant_directives` VALUES (2,1,'max_tokens','500','integer','general',NULL,1,'2025-11-04 21:57:32','2025-11-04 21:57:32');
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ai_workflow_nodes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ai_workflow_nodes_node_key_unique` (`node_key`),
  KEY `ai_workflow_nodes_category_is_active_order_index` (`category`,`is_active`,`order`),
  KEY `ai_workflow_nodes_is_global_index` (`is_global`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `ai_workflow_nodes` VALUES (1,'ai_response','App\\Services\\ConversationNodes\\Common\\AIResponseNode','{\"en\": \"AI Response\", \"tr\": \"AI Yanıtı\"}','{\"en\": \"Send instruction to AI\", \"tr\": \"AI\'a talimat gönder\"}','common','fa-robot',1,1,1,NULL,NULL,'2025-11-04 23:23:56','2025-11-04 23:23:56');
INSERT INTO `ai_workflow_nodes` VALUES (2,'condition','App\\Services\\ConversationNodes\\Common\\ConditionNode','{\"en\": \"Condition\", \"tr\": \"Koşul\"}','{\"en\": \"If/else logic\", \"tr\": \"If/else mantığı\"}','common','fa-code-branch',2,1,1,NULL,NULL,'2025-11-04 23:23:56','2025-11-04 23:23:56');
INSERT INTO `ai_workflow_nodes` VALUES (3,'collect_data','App\\Services\\ConversationNodes\\Common\\CollectDataNode','{\"en\": \"Collect Data\", \"tr\": \"Veri Topla\"}','{\"en\": \"Collect phone/email\", \"tr\": \"Telefon/email topla\"}','common','fa-database',3,1,1,NULL,NULL,'2025-11-04 23:23:56','2025-11-04 23:23:56');
INSERT INTO `ai_workflow_nodes` VALUES (4,'share_contact','App\\Services\\ConversationNodes\\Common\\ShareContactNode','{\"en\": \"Share Contact\", \"tr\": \"İletişim Paylaş\"}','{\"en\": \"Share contact info\", \"tr\": \"İletişim bilgisi paylaş\"}','communication','fa-address-card',4,1,1,NULL,NULL,'2025-11-04 23:23:56','2025-11-04 23:23:56');
INSERT INTO `ai_workflow_nodes` VALUES (5,'webhook','App\\Services\\ConversationNodes\\Common\\WebhookNode','{\"en\": \"Webhook\", \"tr\": \"Web Kancası\"}','{\"en\": \"Send HTTP request\", \"tr\": \"HTTP isteği gönder\"}','communication','fa-plug',5,1,1,NULL,NULL,'2025-11-04 23:23:56','2025-11-04 23:23:56');
INSERT INTO `ai_workflow_nodes` VALUES (6,'end','App\\Services\\ConversationNodes\\Common\\EndNode','{\"en\": \"End Conversation\", \"tr\": \"Sohbeti Bitir\"}','{\"en\": \"End conversation\", \"tr\": \"Sohbeti bitir\"}','common','fa-stop-circle',99,1,1,NULL,NULL,'2025-11-04 23:23:56','2025-11-04 23:23:56');
INSERT INTO `ai_workflow_nodes` VALUES (7,'category_detection','App\\Services\\ConversationNodes\\Shop\\CategoryDetectionNode','{\"en\": \"Category Detection\", \"tr\": \"Kategori Tespiti\"}','{\"en\": \"Detect product category (transpalet/forklift)\", \"tr\": \"Ürün kategorisi tespit et (transpalet/forklift)\"}','ecommerce','fa-tags',10,1,1,'[2]',NULL,'2025-11-04 23:23:56','2025-11-04 23:23:56');
INSERT INTO `ai_workflow_nodes` VALUES (8,'product_recommendation','App\\Services\\ConversationNodes\\Shop\\ProductSearchNode','{\"en\": \"Product Recommendation\", \"tr\": \"Ürün Önerme\"}','{\"en\": \"Recommend products (homepage+stock priority)\", \"tr\": \"Ürün öner (anasayfa+stok öncelikli)\"}','ecommerce','fa-shopping-cart',11,1,1,'[2]',NULL,'2025-11-04 23:23:56','2025-11-04 23:23:56');
INSERT INTO `ai_workflow_nodes` VALUES (9,'price_filter','App\\Services\\ConversationNodes\\Shop\\PriceQueryNode','{\"en\": \"Price Filter\", \"tr\": \"Fiyat Filtresi\"}','{\"en\": \"Filter by price (cheap/expensive)\", \"tr\": \"Fiyata göre filtrele (ucuz/pahalı)\"}','ecommerce','fa-filter',12,1,1,'[2]',NULL,'2025-11-04 23:23:56','2025-11-04 23:23:56');
INSERT INTO `ai_workflow_nodes` VALUES (10,'currency_convert','App\\Services\\ConversationNodes\\Shop\\CurrencyConverterNode','{\"en\": \"Currency Convert\", \"tr\": \"Para Birimi Çevir\"}','{\"en\": \"Convert USD to TL\", \"tr\": \"USD\'yi TL\'ye çevir\"}','ecommerce','fa-dollar-sign',13,1,1,'[2]',NULL,'2025-11-04 23:23:56','2025-11-04 23:23:56');
INSERT INTO `ai_workflow_nodes` VALUES (11,'stock_check','App\\Services\\ConversationNodes\\Shop\\StockSorterNode','{\"en\": \"Stock Check\", \"tr\": \"Stok Kontrolü\"}','{\"en\": \"Check stock availability\", \"tr\": \"Stok durumunu kontrol et\"}','ecommerce','fa-boxes',14,1,1,'[2]',NULL,'2025-11-04 23:23:56','2025-11-04 23:23:56');
INSERT INTO `ai_workflow_nodes` VALUES (12,'comparison','App\\Services\\ConversationNodes\\Shop\\ProductComparisonNode','{\"en\": \"Product Comparison\", \"tr\": \"Ürün Karşılaştırma\"}','{\"en\": \"Compare products\", \"tr\": \"Ürünleri karşılaştır\"}','ecommerce','fa-balance-scale',15,1,1,'[2]',NULL,'2025-11-04 23:23:56','2025-11-04 23:23:56');
INSERT INTO `ai_workflow_nodes` VALUES (13,'quotation','App\\Services\\ConversationNodes\\Shop\\ContactRequestNode','{\"en\": \"Quotation\", \"tr\": \"Teklif Hazırla\"}','{\"en\": \"Prepare quotation\", \"tr\": \"Teklif hazırla\"}','ecommerce','fa-file-invoice-dollar',16,1,1,'[2]',NULL,'2025-11-04 23:23:56','2025-11-04 23:23:56');
