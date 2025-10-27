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
-- Dumping data for table `widget_items`
--

LOCK TABLES `widget_items` WRITE;
/*!40000 ALTER TABLE `widget_items` DISABLE KEYS */;
INSERT INTO `widget_items` (`id`, `tenant_widget_id`, `content`, `order`, `created_at`, `updated_at`) VALUES (1,1,'{\"image\": \"https://placehold.co/1200x600\", \"title\": \"Web Sitesi Çözümleri\", \"is_active\": true, \"unique_id\": \"fa37f0f1-5fdf-46ed-9ad6-d02fadf7023d\", \"button_url\": \"/web-cozumleri\", \"button_text\": \"Detaylı Bilgi\", \"description\": \"Modern ve responsive web siteleri ile işletmenizi dijital dünyada öne çıkarın\"}',1,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `widget_items` (`id`, `tenant_widget_id`, `content`, `order`, `created_at`, `updated_at`) VALUES (2,1,'{\"image\": \"https://placehold.co/1200x600\", \"title\": \"E-Ticaret Platformları\", \"is_active\": true, \"unique_id\": \"f2539e69-0711-4f70-9542-16ee394da96e\", \"button_url\": \"/e-ticaret\", \"button_text\": \"Hemen Başlayın\", \"description\": \"Güvenli ve kullanıcı dostu e-ticaret çözümleri ile satışlarınızı artırın\"}',2,'2025-10-05 00:43:13','2025-10-05 00:43:13');
/*!40000 ALTER TABLE `widget_items` ENABLE KEYS */;
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
