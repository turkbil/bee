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
-- Table structure for table `ai_feature_prompt_relations`
--

DROP TABLE IF EXISTS `ai_feature_prompt_relations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ai_feature_prompt_relations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `feature_id` bigint unsigned NOT NULL,
  `prompt_id` bigint unsigned DEFAULT NULL,
  `feature_prompt_id` bigint unsigned DEFAULT NULL,
  `priority` int NOT NULL DEFAULT '1',
  `role` enum('primary','secondary','supportive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'primary',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `conditions` json DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `category_context` json DEFAULT NULL,
  `feature_type_filter` enum('all','specific','category_based') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'all',
  `business_rules` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ai_feature_prompt_relations_feature_id_priority_index` (`feature_id`,`priority`),
  KEY `ai_feature_prompt_relations_prompt_id_role_index` (`prompt_id`,`role`),
  KEY `ai_feature_prompt_relations_is_active_priority_index` (`is_active`,`priority`),
  KEY `ai_feature_prompt_relations_feature_type_filter_is_active_index` (`feature_type_filter`,`is_active`),
  KEY `ai_feature_prompt_relations_role_priority_is_active_index` (`role`,`priority`,`is_active`),
  KEY `ai_feature_prompt_relations_feature_prompt_id_role_index` (`feature_prompt_id`,`role`),
  CONSTRAINT `ai_feature_prompt_relations_feature_id_foreign` FOREIGN KEY (`feature_id`) REFERENCES `ai_features` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ai_feature_prompt_relations_feature_prompt_id_foreign` FOREIGN KEY (`feature_prompt_id`) REFERENCES `ai_feature_prompts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ai_feature_prompt_relations_prompt_id_foreign` FOREIGN KEY (`prompt_id`) REFERENCES `ai_prompts` (`prompt_id`) ON DELETE CASCADE
) AUTO_INCREMENT=50 ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
