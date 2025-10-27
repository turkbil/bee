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
-- Table structure for table `tenant_rate_limits`
--

DROP TABLE IF EXISTS `tenant_rate_limits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tenant_rate_limits` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `endpoint_pattern` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '*',
  `method` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '*',
  `requests_per_minute` int NOT NULL DEFAULT '60',
  `requests_per_hour` int NOT NULL DEFAULT '1000',
  `requests_per_day` int NOT NULL DEFAULT '10000',
  `burst_limit` int NOT NULL DEFAULT '10',
  `concurrent_requests` int NOT NULL DEFAULT '5',
  `ip_whitelist` json DEFAULT NULL,
  `ip_blacklist` json DEFAULT NULL,
  `throttle_strategy` enum('fixed_window','sliding_window','token_bucket') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'sliding_window',
  `penalty_duration` int NOT NULL DEFAULT '60',
  `penalty_action` enum('block','delay','queue','warn') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'delay',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `log_violations` tinyint(1) NOT NULL DEFAULT '1',
  `priority` int NOT NULL DEFAULT '0',
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tenant_rate_limits_tenant_id_is_active_index` (`tenant_id`,`is_active`),
  KEY `tenant_rate_limits_endpoint_pattern_method_index` (`endpoint_pattern`,`method`),
  KEY `tenant_rate_limits_priority_is_active_index` (`priority`,`is_active`),
  CONSTRAINT `tenant_rate_limits_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
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

-- Dump completed on 2025-10-05 16:12:23
