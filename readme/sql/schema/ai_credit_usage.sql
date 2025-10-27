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
-- Table structure for table `ai_credit_usage`
--

DROP TABLE IF EXISTS `ai_credit_usage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ai_credit_usage` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `conversation_id` bigint unsigned DEFAULT NULL,
  `message_id` bigint unsigned DEFAULT NULL,
  `ai_provider_id` bigint unsigned DEFAULT NULL,
  `provider_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `credits_used` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `input_tokens` int NOT NULL DEFAULT '0',
  `output_tokens` int NOT NULL DEFAULT '0',
  `credit_cost` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `cost_multiplier` decimal(8,4) NOT NULL DEFAULT '1.0000',
  `usage_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'chat',
  `feature_slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `purpose` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `reference_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `response_metadata` json DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `used_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ai_credit_usage_message_id_foreign` (`message_id`),
  KEY `idx_tenant_date` (`tenant_id`,`used_at`),
  KEY `idx_user_date` (`user_id`,`used_at`),
  KEY `idx_provider_date` (`ai_provider_id`,`used_at`),
  KEY `idx_provider_name_date` (`provider_name`,`used_at`),
  KEY `idx_feature_date` (`feature_slug`,`used_at`),
  KEY `idx_type_date` (`usage_type`,`used_at`),
  KEY `idx_conversation` (`conversation_id`),
  KEY `idx_used_at` (`used_at`),
  CONSTRAINT `ai_credit_usage_ai_provider_id_foreign` FOREIGN KEY (`ai_provider_id`) REFERENCES `ai_providers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ai_credit_usage_conversation_id_foreign` FOREIGN KEY (`conversation_id`) REFERENCES `ai_conversations` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ai_credit_usage_message_id_foreign` FOREIGN KEY (`message_id`) REFERENCES `ai_messages` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ai_credit_usage_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
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
