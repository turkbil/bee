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
-- Dumping data for table `ai_credit_packages`
--

LOCK TABLES `ai_credit_packages` WRITE;
/*!40000 ALTER TABLE `ai_credit_packages` DISABLE KEYS */;
INSERT INTO `ai_credit_packages` (`id`, `name`, `credit_amount`, `price`, `currency`, `description`, `is_active`, `is_popular`, `features`, `sort_order`, `created_at`, `updated_at`) VALUES (1,'Başlangıç',100,400.00,'TRY','Küçük projeler ve bireysel kullanım için ideal',1,0,'[\"100 kredi\", \"Temel AI özellikleri\", \"Email desteği\", \"30 gün geçerlilik\", \"~20-30 makale yazabilir\"]',1,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `ai_credit_packages` (`id`, `name`, `credit_amount`, `price`, `currency`, `description`, `is_active`, `is_popular`, `features`, `sort_order`, `created_at`, `updated_at`) VALUES (2,'Standart',500,1800.00,'TRY','Küçük işletmeler ve orta ölçekli projeler için',1,1,'[\"500 kredi\", \"Tüm AI özellikleri\", \"Öncelikli email desteği\", \"60 gün geçerlilik\", \"%25 bonus kredi\", \"~100-150 makale yazabilir\"]',2,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `ai_credit_packages` (`id`, `name`, `credit_amount`, `price`, `currency`, `description`, `is_active`, `is_popular`, `features`, `sort_order`, `created_at`, `updated_at`) VALUES (3,'Premium',1500,4800.00,'TRY','Büyük işletmeler ve yoğun kullanım için',1,0,'[\"1500 kredi\", \"Tüm AI özellikleri\", \"Canlı destek\", \"90 gün geçerlilik\", \"%50 bonus kredi\", \"API erişimi\", \"~300-450 makale yazabilir\"]',3,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `ai_credit_packages` (`id`, `name`, `credit_amount`, `price`, `currency`, `description`, `is_active`, `is_popular`, `features`, `sort_order`, `created_at`, `updated_at`) VALUES (4,'Enterprise',5000,14000.00,'TRY','Büyük organizasyonlar ve sınırsız kullanım için',1,0,'[\"5000 kredi\", \"Tüm AI özellikleri\", \"Özel destek temsilcisi\", \"120 gün geçerlilik\", \"%100 bonus kredi\", \"Özel API erişimi\", \"Özel entegrasyonlar\", \"~1000+ makale yazabilir\"]',4,'2025-10-05 00:43:13','2025-10-05 00:43:13');
/*!40000 ALTER TABLE `ai_credit_packages` ENABLE KEYS */;
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
