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
-- Table structure for table `ai_profile_questions`
--

DROP TABLE IF EXISTS `ai_profile_questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ai_profile_questions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sector_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `step` int NOT NULL DEFAULT '1',
  `section` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `question_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `question_text` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `help_text` text COLLATE utf8mb4_unicode_ci,
  `input_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` json DEFAULT NULL,
  `validation_rules` json DEFAULT NULL,
  `depends_on` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `show_if` json DEFAULT NULL,
  `is_required` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '0',
  `priority` tinyint unsigned NOT NULL DEFAULT '3' COMMENT 'Priority level: 1=critical, 5=rarely used',
  `ai_weight` tinyint unsigned NOT NULL DEFAULT '50' COMMENT 'AI context building weight (1-100)',
  `category` enum('company','sector','ai','founder') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'company' COMMENT 'Field category grouping',
  `ai_priority` int NOT NULL DEFAULT '3' COMMENT 'AI context priority: 1=critical, 2=important, 3=normal, 4=optional, 5=rarely_used',
  `always_include` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Bu alan her AI context''inde yer alsın mı?',
  `context_category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Context kategori: brand_identity, business_info, behavior_rules',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ai_profile_questions_question_key_unique` (`question_key`),
  KEY `ai_profile_questions_sector_code_index` (`sector_code`),
  KEY `ai_profile_questions_step_index` (`step`),
  KEY `ai_profile_questions_section_index` (`section`),
  KEY `ai_profile_questions_is_active_index` (`is_active`),
  KEY `idx_priority_weight` (`priority`,`ai_weight`),
  KEY `idx_category_step` (`category`,`step`)
) AUTO_INCREMENT=3095 ENGINE=InnoDB AUTO_INCREMENT=3095 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
