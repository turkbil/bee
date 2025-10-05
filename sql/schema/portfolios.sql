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
-- Table structure for table `portfolios`
--

DROP TABLE IF EXISTS `portfolios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `portfolios` (
  `portfolio_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `portfolio_category_id` bigint unsigned DEFAULT NULL,
  `title` json NOT NULL COMMENT 'Çoklu dil başlık: {"tr": "Başlık", "en": "Title"}',
  `slug` json NOT NULL COMMENT 'Çoklu dil slug: {"tr": "baslik", "en": "title"}',
  `body` json DEFAULT NULL COMMENT 'Çoklu dil içerik: {"tr": "İçerik", "en": "Content"}',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`portfolio_id`),
  KEY `portfolios_created_at_index` (`created_at`),
  KEY `portfolios_updated_at_index` (`updated_at`),
  KEY `portfolios_deleted_at_index` (`deleted_at`),
  KEY `portfolios_active_deleted_created_idx` (`is_active`,`deleted_at`,`created_at`),
  KEY `portfolios_active_deleted_idx` (`is_active`,`deleted_at`),
  KEY `portfolios_is_active_index` (`is_active`),
  KEY `portfolios_portfolio_category_id_index` (`portfolio_category_id`),
  KEY `portfolios_slug_tr` (((cast(json_unquote(json_extract(`slug`,_utf8mb4'$.tr')) as char(255) charset utf8mb4) collate utf8mb4_unicode_ci))),
  KEY `portfolios_slug_en` (((cast(json_unquote(json_extract(`slug`,_utf8mb4'$.en')) as char(255) charset utf8mb4) collate utf8mb4_unicode_ci))),
  CONSTRAINT `portfolios_portfolio_category_id_foreign` FOREIGN KEY (`portfolio_category_id`) REFERENCES `portfolio_categories` (`category_id`) ON DELETE SET NULL
) AUTO_INCREMENT=13 ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
