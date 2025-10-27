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
-- Dumping data for table `tenant_widgets`
--

LOCK TABLES `tenant_widgets` WRITE;
/*!40000 ALTER TABLE `tenant_widgets` DISABLE KEYS */;
INSERT INTO `tenant_widgets` (`id`, `widget_id`, `order`, `settings`, `display_title`, `is_custom`, `custom_html`, `custom_css`, `custom_js`, `is_active`, `created_at`, `updated_at`) VALUES (1,65,0,'{\"widget_height\": 500, \"widget_autoplay\": true, \"widget_unique_id\": \"b21fb4e9-bb68-402b-af22-4b87ef3b0b0c\", \"widget_autoplay_delay\": 5000}','Ana Sayfa Slider',0,NULL,NULL,NULL,1,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `tenant_widgets` (`id`, `widget_id`, `order`, `settings`, `display_title`, `is_custom`, `custom_html`, `custom_css`, `custom_js`, `is_active`, `created_at`, `updated_at`) VALUES (2,66,0,'{\"widget_unique_id\": \"2811f77c-4c94-4771-9dd2-5b60834ad5ee\", \"widget_button_url\": \"/demo-start\", \"widget_hero_title\": \"Merkezi Sistem Hero Başlığı\", \"widget_button_text\": \"Başla\", \"widget_hero_subtitle\": \"Bu merkezi sistem için bir demo alt başlığıdır.\", \"widget_hero_description\": \"Merkezi sistemdeki tüm tenantlar için örnek bir hero açıklaması.\"}','Demo Hero Alanı',0,NULL,NULL,NULL,1,'2025-10-05 00:43:13','2025-10-05 00:43:13');
/*!40000 ALTER TABLE `tenant_widgets` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-10-05 16:14:17
