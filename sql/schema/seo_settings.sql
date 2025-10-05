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
-- Table structure for table `seo_settings`
--

DROP TABLE IF EXISTS `seo_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `seo_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `seoable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `seoable_id` bigint unsigned NOT NULL,
  `titles` json DEFAULT NULL,
  `descriptions` json DEFAULT NULL,
  `keywords` json DEFAULT NULL,
  `canonical_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `author` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `og_titles` json DEFAULT NULL,
  `og_descriptions` json DEFAULT NULL,
  `og_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `og_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'website',
  `twitter_card` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'summary',
  `twitter_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `twitter_description` text COLLATE utf8mb4_unicode_ci,
  `twitter_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `robots_meta` json DEFAULT NULL,
  `focus_keywords` json DEFAULT NULL,
  `additional_keywords` json DEFAULT NULL,
  `seo_score` int NOT NULL DEFAULT '0',
  `seo_analysis` json DEFAULT NULL,
  `last_analyzed` timestamp NULL DEFAULT NULL,
  `content_length` int NOT NULL DEFAULT '0',
  `keyword_density` int NOT NULL DEFAULT '0',
  `readability_score` json DEFAULT NULL,
  `page_speed_insights` json DEFAULT NULL,
  `last_crawled` timestamp NULL DEFAULT NULL,
  `analysis_results` json DEFAULT NULL,
  `analysis_date` timestamp NULL DEFAULT NULL,
  `detailed_scores` json DEFAULT NULL,
  `strengths` json DEFAULT NULL,
  `improvements` json DEFAULT NULL,
  `action_items` json DEFAULT NULL,
  `ai_suggestions` json DEFAULT NULL,
  `status` enum('active','inactive','pending') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `priority_score` int NOT NULL DEFAULT '5',
  PRIMARY KEY (`id`),
  KEY `seo_settings_seoable_type_seoable_id_index` (`seoable_type`,`seoable_id`),
  KEY `seo_settings_seoable_id_seoable_type_index` (`seoable_id`,`seoable_type`),
  KEY `seo_settings_status_index` (`status`),
  KEY `seo_settings_seo_score_index` (`seo_score`),
  KEY `seo_settings_last_analyzed_index` (`last_analyzed`),
  KEY `seo_settings_analysis_date_index` (`analysis_date`)
) AUTO_INCREMENT=40 ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
