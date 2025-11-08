-- MySQL dump 10.13  Distrib 9.4.0, for macos15.4 (arm64)
--
-- Host: localhost    Database: laravel
-- ------------------------------------------------------
-- Server version	9.4.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `ai_tenant_directives`
--

DROP TABLE IF EXISTS `ai_tenant_directives`;
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
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ai_conversations`
--

DROP TABLE IF EXISTS `ai_conversations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ai_conversations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'chat',
  `feature_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `feature_slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_demo` tinyint(1) NOT NULL DEFAULT '0',
  `user_id` bigint unsigned DEFAULT NULL,
  `tenant_id` bigint unsigned DEFAULT NULL,
  `prompt_id` bigint unsigned DEFAULT NULL,
  `session_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_tokens_used` int NOT NULL DEFAULT '0',
  `last_message_at` timestamp NULL DEFAULT NULL,
  `message_count` int NOT NULL DEFAULT '0',
  `metadata` json DEFAULT NULL,
  `context_data` json DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ai_conversations_user_id_index` (`user_id`),
  KEY `ai_conversations_prompt_id_index` (`prompt_id`),
  KEY `ai_conversations_type_index` (`type`),
  KEY `ai_conversations_feature_name_index` (`feature_name`),
  KEY `ai_conversations_tenant_id_index` (`tenant_id`),
  KEY `ai_conversations_session_id_index` (`session_id`),
  KEY `ai_conversations_status_index` (`status`),
  KEY `ai_conversations_created_at_index` (`created_at`),
  KEY `ai_conversations_updated_at_index` (`updated_at`),
  KEY `ai_conversations_user_created_idx` (`user_id`,`created_at`),
  KEY `ai_conversations_prompt_created_idx` (`prompt_id`,`created_at`),
  KEY `ai_conversations_type_created_idx` (`type`,`created_at`),
  KEY `ai_conversations_tenant_created_idx` (`tenant_id`,`created_at`),
  KEY `ai_conversations_feature_slug_index` (`feature_slug`),
  KEY `ai_conversations_is_active_index` (`is_active`),
  KEY `ai_conversations_last_message_at_index` (`last_message_at`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ai_messages`
--

DROP TABLE IF EXISTS `ai_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ai_messages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `conversation_id` bigint unsigned NOT NULL,
  `role` enum('user','assistant') COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokens` int NOT NULL DEFAULT '0',
  `tokens_used` int NOT NULL DEFAULT '0',
  `prompt_tokens` int NOT NULL DEFAULT '0',
  `completion_tokens` int NOT NULL DEFAULT '0',
  `model_used` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `processing_time_ms` int NOT NULL DEFAULT '0',
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `context_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `message_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ai_messages_conversation_id_index` (`conversation_id`),
  KEY `ai_messages_role_index` (`role`),
  KEY `ai_messages_model_used_index` (`model_used`),
  KEY `ai_messages_message_type_index` (`message_type`),
  KEY `ai_messages_created_at_index` (`created_at`),
  KEY `ai_messages_conversation_created_idx` (`conversation_id`,`created_at`),
  KEY `ai_messages_conversation_role_idx` (`conversation_id`,`role`),
  KEY `ai_messages_conversation_type_idx` (`conversation_id`,`message_type`),
  KEY `ai_messages_tokens_used_index` (`tokens_used`),
  CONSTRAINT `ai_messages_chk_1` CHECK (json_valid(`metadata`)),
  CONSTRAINT `ai_messages_chk_2` CHECK (json_valid(`context_data`))
) ENGINE=InnoDB AUTO_INCREMENT=1103 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ai_providers`
--

DROP TABLE IF EXISTS `ai_providers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ai_providers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `service_class` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `default_model` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `available_models` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `default_settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `api_key` text COLLATE utf8mb4_unicode_ci,
  `base_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `priority` int NOT NULL DEFAULT '0',
  `average_response_time` decimal(8,2) DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `token_cost_multiplier` decimal(8,4) NOT NULL DEFAULT '1.0000',
  `tokens_per_request_estimate` int NOT NULL DEFAULT '1000',
  `cost_structure` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `tracks_usage` tinyint(1) NOT NULL DEFAULT '1',
  `credit_cost_multiplier` decimal(8,4) NOT NULL DEFAULT '1.0000' COMMENT 'Kredi maliyet çarpanı - DeepSeek 0.5, OpenAI 1.0, Anthropic 1.2 gibi',
  `credits_per_request_estimate` int NOT NULL DEFAULT '10' COMMENT 'Request başına ortalama kredi tahmini',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ai_providers_name_unique` (`name`),
  CONSTRAINT `ai_providers_chk_1` CHECK (json_valid(`available_models`)),
  CONSTRAINT `ai_providers_chk_2` CHECK (json_valid(`default_settings`)),
  CONSTRAINT `ai_providers_chk_3` CHECK (json_valid(`cost_structure`))
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ai_features`
--

DROP TABLE IF EXISTS `ai_features`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ai_features` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ai_feature_category_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `emoji` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `module_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'blog, page, email, seo, translation',
  `category` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'content_generation, optimization, translation',
  `supported_modules` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT '[\\"page\\", \\"blog\\", \\"portfolio\\"]',
  `context_rules` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'Module ve context bazlı kurallar',
  `template_support` tinyint(1) NOT NULL DEFAULT '0',
  `bulk_support` tinyint(1) NOT NULL DEFAULT '0',
  `streaming_support` tinyint(1) NOT NULL DEFAULT '0',
  `helper_function` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `helper_examples` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `helper_parameters` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `helper_description` text COLLATE utf8mb4_unicode_ci,
  `helper_returns` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `hybrid_system_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'basic',
  `has_custom_prompt` tinyint(1) NOT NULL DEFAULT '0',
  `has_related_prompts` tinyint(1) NOT NULL DEFAULT '0',
  `quick_prompt` text COLLATE utf8mb4_unicode_ci,
  `response_template` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `custom_prompt` text COLLATE utf8mb4_unicode_ci,
  `additional_config` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `usage_examples` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `input_validation` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `error_messages` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `success_messages` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `token_cost` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `response_length` enum('short','medium','long','variable') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'medium',
  `response_format` enum('text','markdown','structured','code','list','json') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'markdown',
  `complexity_level` enum('beginner','intermediate','advanced','expert') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'intermediate',
  `status` enum('active','inactive','planned','beta') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `is_system` tinyint(1) NOT NULL DEFAULT '0',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `show_in_examples` tinyint(1) NOT NULL DEFAULT '1',
  `show_in_prowess` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Prowess sayfasında gösterilsin mi?',
  `sort_order` int NOT NULL DEFAULT '0',
  `order` int NOT NULL DEFAULT '0',
  `badge_color` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'success',
  `requires_input` tinyint(1) NOT NULL DEFAULT '1',
  `input_placeholder` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `button_text` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Canlı Test Et',
  `example_inputs` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `usage_count` bigint unsigned NOT NULL DEFAULT '0',
  `last_used_at` timestamp NULL DEFAULT NULL,
  `avg_rating` decimal(3,2) NOT NULL DEFAULT '0.00',
  `rating_count` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ai_features_slug_unique` (`slug`),
  KEY `ai_features_ai_feature_category_id_index` (`ai_feature_category_id`),
  KEY `ai_features_status_show_in_examples_sort_order_index` (`status`,`show_in_examples`,`sort_order`),
  KEY `ai_features_is_featured_status_index` (`is_featured`,`status`),
  KEY `ai_features_slug_index` (`slug`),
  KEY `ai_features_usage_count_index` (`usage_count`),
  CONSTRAINT `ai_features_chk_1` CHECK (json_valid(`supported_modules`)),
  CONSTRAINT `ai_features_chk_10` CHECK (json_valid(`settings`)),
  CONSTRAINT `ai_features_chk_11` CHECK (json_valid(`error_messages`)),
  CONSTRAINT `ai_features_chk_12` CHECK (json_valid(`success_messages`)),
  CONSTRAINT `ai_features_chk_13` CHECK (json_valid(`token_cost`)),
  CONSTRAINT `ai_features_chk_14` CHECK (json_valid(`example_inputs`)),
  CONSTRAINT `ai_features_chk_2` CHECK (json_valid(`context_rules`)),
  CONSTRAINT `ai_features_chk_3` CHECK (json_valid(`helper_examples`)),
  CONSTRAINT `ai_features_chk_4` CHECK (json_valid(`helper_parameters`)),
  CONSTRAINT `ai_features_chk_5` CHECK (json_valid(`helper_returns`)),
  CONSTRAINT `ai_features_chk_6` CHECK (json_valid(`response_template`)),
  CONSTRAINT `ai_features_chk_7` CHECK (json_valid(`additional_config`)),
  CONSTRAINT `ai_features_chk_8` CHECK (json_valid(`usage_examples`)),
  CONSTRAINT `ai_features_chk_9` CHECK (json_valid(`input_validation`))
) ENGINE=InnoDB AUTO_INCREMENT=502 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-11-08 22:29:37
