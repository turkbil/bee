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
-- Dumping data for table `ai_input_groups`
--

LOCK TABLES `ai_input_groups` WRITE;
/*!40000 ALTER TABLE `ai_input_groups` DISABLE KEYS */;
INSERT INTO `ai_input_groups` (`id`, `feature_id`, `name`, `slug`, `description`, `is_collapsible`, `is_expanded`, `sort_order`, `created_at`, `updated_at`) VALUES (3,301,'Kaynak Dil ve İçerik','translation_source','Çevrilecek içerik ve kaynak dil bilgileri',0,1,1,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `ai_input_groups` (`id`, `feature_id`, `name`, `slug`, `description`, `is_collapsible`, `is_expanded`, `sort_order`, `created_at`, `updated_at`) VALUES (4,301,'Hedef Diller ve Seçenekler','translation_targets','Hangi dillere çeviri yapılacağı ve çeviri seçenekleri',0,1,2,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `ai_input_groups` (`id`, `feature_id`, `name`, `slug`, `description`, `is_collapsible`, `is_expanded`, `sort_order`, `created_at`, `updated_at`) VALUES (5,301,'Gelişmiş Çeviri Ayarları','translation_advanced','SEO uyumluluğu ve özel çeviri kuralları',1,0,3,'2025-10-05 00:43:13','2025-10-05 00:43:13');
/*!40000 ALTER TABLE `ai_input_groups` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-10-05 16:14:15
