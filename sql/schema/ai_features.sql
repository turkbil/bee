-- MySQL dump 10.13  Distrib 9.4.0, for macos15.4 (arm64)
--
-- Host: 127.0.0.1    Database: laravel
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
  `supported_modules` json DEFAULT NULL COMMENT '[\\"page\\", \\"blog\\", \\"portfolio\\"]',
  `context_rules` json DEFAULT NULL COMMENT 'Module ve context bazlı kurallar',
  `template_support` tinyint(1) NOT NULL DEFAULT '0',
  `bulk_support` tinyint(1) NOT NULL DEFAULT '0',
  `streaming_support` tinyint(1) NOT NULL DEFAULT '0',
  `helper_function` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `helper_examples` json DEFAULT NULL,
  `helper_parameters` json DEFAULT NULL,
  `helper_description` text COLLATE utf8mb4_unicode_ci,
  `helper_returns` json DEFAULT NULL,
  `hybrid_system_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'basic',
  `has_custom_prompt` tinyint(1) NOT NULL DEFAULT '0',
  `has_related_prompts` tinyint(1) NOT NULL DEFAULT '0',
  `quick_prompt` text COLLATE utf8mb4_unicode_ci,
  `response_template` json DEFAULT NULL,
  `custom_prompt` text COLLATE utf8mb4_unicode_ci,
  `additional_config` json DEFAULT NULL,
  `usage_examples` json DEFAULT NULL,
  `input_validation` json DEFAULT NULL,
  `settings` json DEFAULT NULL,
  `error_messages` json DEFAULT NULL,
  `success_messages` json DEFAULT NULL,
  `token_cost` json DEFAULT NULL,
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
  `example_inputs` json DEFAULT NULL,
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
  KEY `ai_features_usage_count_index` (`usage_count`)
) AUTO_INCREMENT=502 ENGINE=InnoDB AUTO_INCREMENT=502 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping routines for database 'laravel'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-10-05 16:12:22
