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
-- Table structure for table `ai_tenant_profiles`
--

DROP TABLE IF EXISTS `ai_tenant_profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ai_tenant_profiles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `company_info` json DEFAULT NULL,
  `sector_details` json DEFAULT NULL,
  `success_stories` json DEFAULT NULL,
  `ai_behavior_rules` json DEFAULT NULL,
  `founder_info` json DEFAULT NULL,
  `additional_info` json DEFAULT NULL,
  `brand_story` text COLLATE utf8mb4_unicode_ci,
  `brand_story_created_at` timestamp NULL DEFAULT NULL,
  `ai_context` text COLLATE utf8mb4_unicode_ci COMMENT 'AI için optimize edilmiş context - öncelikli bilgiler',
  `context_priority` json DEFAULT NULL COMMENT 'Context bilgilerinin priority sıralaması',
  `smart_field_scores` json DEFAULT NULL,
  `field_calculation_metadata` json DEFAULT NULL,
  `profile_completeness_score` decimal(5,2) NOT NULL DEFAULT '0.00',
  `profile_quality_grade` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'F',
  `last_calculation_context` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal',
  `scores_calculated_at` timestamp NULL DEFAULT NULL,
  `context_performance` json DEFAULT NULL,
  `ai_recommendations` json DEFAULT NULL,
  `missing_critical_fields` int NOT NULL DEFAULT '0',
  `field_quality_analysis` json DEFAULT NULL,
  `usage_analytics` json DEFAULT NULL,
  `ai_interactions_count` int NOT NULL DEFAULT '0',
  `last_ai_interaction_at` timestamp NULL DEFAULT NULL,
  `avg_ai_response_quality` decimal(3,2) NOT NULL DEFAULT '0.00',
  `profile_version` int NOT NULL DEFAULT '1',
  `version_history` json DEFAULT NULL,
  `auto_optimization_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_completed` tinyint(1) NOT NULL DEFAULT '0',
  `data` json DEFAULT NULL COMMENT 'Context data for AI',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ai_tenant_profiles_tenant_id_unique` (`tenant_id`),
  KEY `ai_tenant_profiles_tenant_id_index` (`tenant_id`),
  KEY `ai_tenant_profiles_is_active_index` (`is_active`),
  KEY `idx_tenant_completeness` (`tenant_id`,`profile_completeness_score`),
  KEY `idx_quality_completed` (`profile_quality_grade`,`is_completed`),
  KEY `idx_context_timing` (`last_calculation_context`,`scores_calculated_at`),
  KEY `idx_critical_fields` (`missing_critical_fields`,`is_active`),
  CONSTRAINT `ai_tenant_profiles_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) AUTO_INCREMENT=2 ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
