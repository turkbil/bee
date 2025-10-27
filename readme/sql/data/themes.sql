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
-- Dumping data for table `themes`
--

LOCK TABLES `themes` WRITE;
/*!40000 ALTER TABLE `themes` DISABLE KEYS */;
INSERT INTO `themes` (`theme_id`, `name`, `title`, `slug`, `folder_name`, `description`, `is_active`, `is_default`, `created_at`, `updated_at`, `deleted_at`) VALUES (1,'blank','Blank Tema','blank-tema','blank','Boş tema (blank), temel dizayn için test.',1,1,'2025-10-05 00:43:11','2025-10-05 00:43:11',NULL);
INSERT INTO `themes` (`theme_id`, `name`, `title`, `slug`, `folder_name`, `description`, `is_active`, `is_default`, `created_at`, `updated_at`, `deleted_at`) VALUES (2,'dark','Koyu Tema','koyu-tema','dark','Koyu arka plan ve kontrastlı renkler içeren tema.',1,0,'2025-10-05 00:43:11','2025-10-05 00:43:11',NULL);
INSERT INTO `themes` (`theme_id`, `name`, `title`, `slug`, `folder_name`, `description`, `is_active`, `is_default`, `created_at`, `updated_at`, `deleted_at`) VALUES (3,'blue','Mavi Tema','mavi-tema','blue','Mavi tonları ağırlıklı kurumsal görünüm sunan tema.',1,0,'2025-10-05 00:43:11','2025-10-05 00:43:11',NULL);
INSERT INTO `themes` (`theme_id`, `name`, `title`, `slug`, `folder_name`, `description`, `is_active`, `is_default`, `created_at`, `updated_at`, `deleted_at`) VALUES (4,'modern','Modern Tema','modern-tema','modern','Flat tasarım ve modern UI elementleri içeren tema.',0,0,'2025-10-05 00:43:11','2025-10-05 00:43:11',NULL);
/*!40000 ALTER TABLE `themes` ENABLE KEYS */;
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
