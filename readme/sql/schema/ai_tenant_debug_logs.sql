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
-- Table structure for table `ai_tenant_debug_logs`
--

DROP TABLE IF EXISTS `ai_tenant_debug_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ai_tenant_debug_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `session_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `feature_slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `request_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `context_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal',
  `prompts_analysis` json NOT NULL,
  `scoring_summary` json NOT NULL,
  `threshold_used` int NOT NULL,
  `total_available_prompts` tinyint NOT NULL,
  `actually_used_prompts` tinyint NOT NULL,
  `filtered_prompts` tinyint NOT NULL,
  `highest_score` int NOT NULL,
  `lowest_used_score` int NOT NULL,
  `execution_time_ms` int NOT NULL,
  `response_length` int DEFAULT NULL,
  `token_usage` int DEFAULT NULL,
  `cost_estimate` decimal(8,4) DEFAULT NULL,
  `input_hash` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `input_preview` text COLLATE utf8mb4_unicode_ci,
  `response_preview` text COLLATE utf8mb4_unicode_ci,
  `response_quality` enum('excellent','good','average','poor') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ai_model` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'deepseek-chat',
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `request_headers` json DEFAULT NULL,
  `has_error` tinyint(1) NOT NULL DEFAULT '0',
  `error_message` text COLLATE utf8mb4_unicode_ci,
  `error_details` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_tenant_timeline` (`tenant_id`,`created_at`),
  KEY `idx_feature_timeline` (`feature_slug`,`created_at`),
  KEY `idx_performance` (`request_type`,`execution_time_ms`),
  KEY `idx_prompt_efficiency` (`actually_used_prompts`,`highest_score`),
  KEY `idx_error_tracking` (`has_error`,`created_at`),
  KEY `ai_tenant_debug_logs_user_id_foreign` (`user_id`),
  KEY `ai_tenant_debug_logs_tenant_id_index` (`tenant_id`),
  KEY `ai_tenant_debug_logs_feature_slug_index` (`feature_slug`),
  KEY `ai_tenant_debug_logs_request_type_index` (`request_type`),
  KEY `ai_tenant_debug_logs_threshold_used_index` (`threshold_used`),
  KEY `ai_tenant_debug_logs_actually_used_prompts_index` (`actually_used_prompts`),
  KEY `ai_tenant_debug_logs_execution_time_ms_index` (`execution_time_ms`),
  KEY `ai_tenant_debug_logs_has_error_index` (`has_error`),
  CONSTRAINT `ai_tenant_debug_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) AUTO_INCREMENT=1 ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
