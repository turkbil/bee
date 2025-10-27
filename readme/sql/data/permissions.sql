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
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` (`id`, `name`, `description`, `guard_name`, `created_at`, `updated_at`) VALUES (1,'ai.view','A I - Görüntüleme','web','2025-10-05 00:43:12','2025-10-05 00:43:12');
INSERT INTO `permissions` (`id`, `name`, `description`, `guard_name`, `created_at`, `updated_at`) VALUES (2,'ai.create','A I - Oluşturma','web','2025-10-05 00:43:12','2025-10-05 00:43:12');
INSERT INTO `permissions` (`id`, `name`, `description`, `guard_name`, `created_at`, `updated_at`) VALUES (3,'ai.update','A I - Güncelleme','web','2025-10-05 00:43:12','2025-10-05 00:43:12');
INSERT INTO `permissions` (`id`, `name`, `description`, `guard_name`, `created_at`, `updated_at`) VALUES (4,'ai.delete','A I - Silme','web','2025-10-05 00:43:12','2025-10-05 00:43:12');
INSERT INTO `permissions` (`id`, `name`, `description`, `guard_name`, `created_at`, `updated_at`) VALUES (5,'announcement.view','Announcement - Görüntüleme','web','2025-10-05 00:43:12','2025-10-05 00:43:12');
INSERT INTO `permissions` (`id`, `name`, `description`, `guard_name`, `created_at`, `updated_at`) VALUES (6,'announcement.create','Announcement - Oluşturma','web','2025-10-05 00:43:12','2025-10-05 00:43:12');
INSERT INTO `permissions` (`id`, `name`, `description`, `guard_name`, `created_at`, `updated_at`) VALUES (7,'announcement.update','Announcement - Güncelleme','web','2025-10-05 00:43:12','2025-10-05 00:43:12');
INSERT INTO `permissions` (`id`, `name`, `description`, `guard_name`, `created_at`, `updated_at`) VALUES (8,'announcement.delete','Announcement - Silme','web','2025-10-05 00:43:12','2025-10-05 00:43:12');
INSERT INTO `permissions` (`id`, `name`, `description`, `guard_name`, `created_at`, `updated_at`) VALUES (9,'languagemanagement.view','Language Management - Görüntüleme','web','2025-10-05 00:43:12','2025-10-05 00:43:12');
INSERT INTO `permissions` (`id`, `name`, `description`, `guard_name`, `created_at`, `updated_at`) VALUES (10,'languagemanagement.create','Language Management - Oluşturma','web','2025-10-05 00:43:12','2025-10-05 00:43:12');
INSERT INTO `permissions` (`id`, `name`, `description`, `guard_name`, `created_at`, `updated_at`) VALUES (11,'languagemanagement.update','Language Management - Güncelleme','web','2025-10-05 00:43:12','2025-10-05 00:43:12');
INSERT INTO `permissions` (`id`, `name`, `description`, `guard_name`, `created_at`, `updated_at`) VALUES (12,'languagemanagement.delete','Language Management - Silme','web','2025-10-05 00:43:12','2025-10-05 00:43:12');
INSERT INTO `permissions` (`id`, `name`, `description`, `guard_name`, `created_at`, `updated_at`) VALUES (13,'mediamanagement.view','Media Management - Görüntüleme','web','2025-10-05 00:43:12','2025-10-05 00:43:12');
INSERT INTO `permissions` (`id`, `name`, `description`, `guard_name`, `created_at`, `updated_at`) VALUES (14,'mediamanagement.create','Media Management - Oluşturma','web','2025-10-05 00:43:12','2025-10-05 00:43:12');
INSERT INTO `permissions` (`id`, `name`, `description`, `guard_name`, `created_at`, `updated_at`) VALUES (15,'mediamanagement.update','Media Management - Güncelleme','web','2025-10-05 00:43:12','2025-10-05 00:43:12');
INSERT INTO `permissions` (`id`, `name`, `description`, `guard_name`, `created_at`, `updated_at`) VALUES (16,'mediamanagement.delete','Media Management - Silme','web','2025-10-05 00:43:12','2025-10-05 00:43:12');
INSERT INTO `permissions` (`id`, `name`, `description`, `guard_name`, `created_at`, `updated_at`) VALUES (17,'menumanagement.view','Menu Management - Görüntüleme','web','2025-10-05 00:43:12','2025-10-05 00:43:12');
INSERT INTO `permissions` (`id`, `name`, `description`, `guard_name`, `created_at`, `updated_at`) VALUES (18,'menumanagement.create','Menu Management - Oluşturma','web','2025-10-05 00:43:12','2025-10-05 00:43:12');
INSERT INTO `permissions` (`id`, `name`, `description`, `guard_name`, `created_at`, `updated_at`) VALUES (19,'menumanagement.update','Menu Management - Güncelleme','web','2025-10-05 00:43:12','2025-10-05 00:43:12');
INSERT INTO `permissions` (`id`, `name`, `description`, `guard_name`, `created_at`, `updated_at`) VALUES (20,'menumanagement.delete','Menu Management - Silme','web','2025-10-05 00:43:12','2025-10-05 00:43:12');
INSERT INTO `permissions` (`id`, `name`, `description`, `guard_name`, `created_at`, `updated_at`) VALUES (21,'modulemanagement.view','Module Management - Görüntüleme','web','2025-10-05 00:43:12','2025-10-05 00:43:12');
INSERT INTO `permissions` (`id`, `name`, `description`, `guard_name`, `created_at`, `updated_at`) VALUES (22,'modulemanagement.create','Module Management - Oluşturma','web','2025-10-05 00:43:12','2025-10-05 00:43:12');
INSERT INTO `permissions` (`id`, `name`, `description`, `guard_name`, `created_at`, `updated_at`) VALUES (23,'modulemanagement.update','Module Management - Güncelleme','web','2025-10-05 00:43:12','2025-10-05 00:43:12');
INSERT INTO `permissions` (`id`, `name`, `description`, `guard_name`, `created_at`, `updated_at`) VALUES (24,'modulemanagement.delete','Module Management - Silme','web','2025-10-05 00:43:12','2025-10-05 00:43:12');
INSERT INTO `permissions` (`id`, `name`, `description`, `guard_name`, `created_at`, `updated_at`) VALUES (25,'page.view','Page - Görüntüleme','web','2025-10-05 00:43:12','2025-10-05 00:43:12');
INSERT INTO `permissions` (`id`, `name`, `description`, `guard_name`, `created_at`, `updated_at`) VALUES (26,'page.create','Page - Oluşturma','web','2025-10-05 00:43:12','2025-10-05 00:43:12');
INSERT INTO `permissions` (`id`, `name`, `description`, `guard_name`, `created_at`, `updated_at`) VALUES (27,'page.update','Page - Güncelleme','web','2025-10-05 00:43:12','2025-10-05 00:43:12');
INSERT INTO `permissions` (`id`, `name`, `description`, `guard_name`, `created_at`, `updated_at`) VALUES (28,'page.delete','Page - Silme','web','2025-10-05 00:43:12','2025-10-05 00:43:12');
INSERT INTO `permissions` (`id`, `name`, `description`, `guard_name`, `created_at`, `updated_at`) VALUES (29,'portfolio.view','Portfolio - Görüntüleme','web','2025-10-05 00:43:12','2025-10-05 00:43:12');
INSERT INTO `permissions` (`id`, `name`, `description`, `guard_name`, `created_at`, `updated_at`) VALUES (30,'portfolio.create','Portfolio - Oluşturma','web','2025-10-05 00:43:12','2025-10-05 00:43:12');
INSERT INTO `permissions` (`id`, `name`, `description`, `guard_name`, `created_at`, `updated_at`) VALUES (31,'portfolio.update','Portfolio - Güncelleme','web','2025-10-05 00:43:12','2025-10-05 00:43:12');
INSERT INTO `permissions` (`id`, `name`, `description`, `guard_name`, `created_at`, `updated_at`) VALUES (32,'portfolio.delete','Portfolio - Silme','web','2025-10-05 00:43:12','2025-10-05 00:43:12');
INSERT INTO `permissions` (`id`, `name`, `description`, `guard_name`, `created_at`, `updated_at`) VALUES (33,'seomanagement.view','Seo Management - Görüntüleme','web','2025-10-05 00:43:12','2025-10-05 00:43:12');
INSERT INTO `permissions` (`id`, `name`, `description`, `guard_name`, `created_at`, `updated_at`) VALUES (34,'seomanagement.create','Seo Management - Oluşturma','web','2025-10-05 00:43:12','2025-10-05 00:43:12');
INSERT INTO `permissions` (`id`, `name`, `description`, `guard_name`, `created_at`, `updated_at`) VALUES (35,'seomanagement.update','Seo Management - Güncelleme','web','2025-10-05 00:43:12','2025-10-05 00:43:12');
INSERT INTO `permissions` (`id`, `name`, `description`, `guard_name`, `created_at`, `updated_at`) VALUES (36,'seomanagement.delete','Seo Management - Silme','web','2025-10-05 00:43:12','2025-10-05 00:43:12');
INSERT INTO `permissions` (`id`, `name`, `description`, `guard_name`, `created_at`, `updated_at`) VALUES (37,'settingmanagement.view','Setting Management - Görüntüleme','web','2025-10-05 00:43:12','2025-10-05 00:43:12');
INSERT INTO `permissions` (`id`, `name`, `description`, `guard_name`, `created_at`, `updated_at`) VALUES (38,'settingmanagement.create','Setting Management - Oluşturma','web','2025-10-05 00:43:12','2025-10-05 00:43:12');
INSERT INTO `permissions` (`id`, `name`, `description`, `guard_name`, `created_at`, `updated_at`) VALUES (39,'settingmanagement.update','Setting Management - Güncelleme','web','2025-10-05 00:43:12','2025-10-05 00:43:12');
INSERT INTO `permissions` (`id`, `name`, `description`, `guard_name`, `created_at`, `updated_at`) VALUES (40,'settingmanagement.delete','Setting Management - Silme','web','2025-10-05 00:43:12','2025-10-05 00:43:12');
INSERT INTO `permissions` (`id`, `name`, `description`, `guard_name`, `created_at`, `updated_at`) VALUES (41,'studio.view','Studio - Görüntüleme','web','2025-10-05 00:43:12','2025-10-05 00:43:12');
INSERT INTO `permissions` (`id`, `name`, `description`, `guard_name`, `created_at`, `updated_at`) VALUES (42,'studio.create','Studio - Oluşturma','web','2025-10-05 00:43:12','2025-10-05 00:43:12');
INSERT INTO `permissions` (`id`, `name`, `description`, `guard_name`, `created_at`, `updated_at`) VALUES (43,'studio.update','Studio - Güncelleme','web','2025-10-05 00:43:12','2025-10-05 00:43:12');
INSERT INTO `permissions` (`id`, `name`, `description`, `guard_name`, `created_at`, `updated_at`) VALUES (44,'studio.delete','Studio - Silme','web','2025-10-05 00:43:12','2025-10-05 00:43:12');
INSERT INTO `permissions` (`id`, `name`, `description`, `guard_name`, `created_at`, `updated_at`) VALUES (45,'tenantmanagement.view','Tenant Management - Görüntüleme','web','2025-10-05 00:43:12','2025-10-05 00:43:12');
INSERT INTO `permissions` (`id`, `name`, `description`, `guard_name`, `created_at`, `updated_at`) VALUES (46,'tenantmanagement.create','Tenant Management - Oluşturma','web','2025-10-05 00:43:12','2025-10-05 00:43:12');
INSERT INTO `permissions` (`id`, `name`, `description`, `guard_name`, `created_at`, `updated_at`) VALUES (47,'tenantmanagement.update','Tenant Management - Güncelleme','web','2025-10-05 00:43:12','2025-10-05 00:43:12');
INSERT INTO `permissions` (`id`, `name`, `description`, `guard_name`, `created_at`, `updated_at`) VALUES (48,'tenantmanagement.delete','Tenant Management - Silme','web','2025-10-05 00:43:12','2025-10-05 00:43:12');
INSERT INTO `permissions` (`id`, `name`, `description`, `guard_name`, `created_at`, `updated_at`) VALUES (49,'thememanagement.view','Theme Management - Görüntüleme','web','2025-10-05 00:43:12','2025-10-05 00:43:12');
INSERT INTO `permissions` (`id`, `name`, `description`, `guard_name`, `created_at`, `updated_at`) VALUES (50,'thememanagement.create','Theme Management - Oluşturma','web','2025-10-05 00:43:12','2025-10-05 00:43:12');
INSERT INTO `permissions` (`id`, `name`, `description`, `guard_name`, `created_at`, `updated_at`) VALUES (51,'thememanagement.update','Theme Management - Güncelleme','web','2025-10-05 00:43:12','2025-10-05 00:43:12');
INSERT INTO `permissions` (`id`, `name`, `description`, `guard_name`, `created_at`, `updated_at`) VALUES (52,'thememanagement.delete','Theme Management - Silme','web','2025-10-05 00:43:12','2025-10-05 00:43:12');
INSERT INTO `permissions` (`id`, `name`, `description`, `guard_name`, `created_at`, `updated_at`) VALUES (53,'usermanagement.view','User Management - Görüntüleme','web','2025-10-05 00:43:12','2025-10-05 00:43:12');
INSERT INTO `permissions` (`id`, `name`, `description`, `guard_name`, `created_at`, `updated_at`) VALUES (54,'usermanagement.create','User Management - Oluşturma','web','2025-10-05 00:43:12','2025-10-05 00:43:12');
INSERT INTO `permissions` (`id`, `name`, `description`, `guard_name`, `created_at`, `updated_at`) VALUES (55,'usermanagement.update','User Management - Güncelleme','web','2025-10-05 00:43:12','2025-10-05 00:43:12');
INSERT INTO `permissions` (`id`, `name`, `description`, `guard_name`, `created_at`, `updated_at`) VALUES (56,'usermanagement.delete','User Management - Silme','web','2025-10-05 00:43:12','2025-10-05 00:43:12');
INSERT INTO `permissions` (`id`, `name`, `description`, `guard_name`, `created_at`, `updated_at`) VALUES (57,'widgetmanagement.view','Widget Management - Görüntüleme','web','2025-10-05 00:43:12','2025-10-05 00:43:12');
INSERT INTO `permissions` (`id`, `name`, `description`, `guard_name`, `created_at`, `updated_at`) VALUES (58,'widgetmanagement.create','Widget Management - Oluşturma','web','2025-10-05 00:43:12','2025-10-05 00:43:12');
INSERT INTO `permissions` (`id`, `name`, `description`, `guard_name`, `created_at`, `updated_at`) VALUES (59,'widgetmanagement.update','Widget Management - Güncelleme','web','2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `permissions` (`id`, `name`, `description`, `guard_name`, `created_at`, `updated_at`) VALUES (60,'widgetmanagement.delete','Widget Management - Silme','web','2025-10-05 00:43:13','2025-10-05 00:43:13');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-10-05 16:14:16
