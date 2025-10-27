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
-- Dumping data for table `modules`
--

LOCK TABLES `modules` WRITE;
/*!40000 ALTER TABLE `modules` DISABLE KEYS */;
INSERT INTO `modules` (`module_id`, `name`, `display_name`, `version`, `description`, `settings`, `type`, `is_active`, `created_at`, `updated_at`) VALUES (1,'modulemanagement','Modüller Yönetimi','1.0.0','Sistem modüllerinin yönetimi',NULL,'system',1,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `modules` (`module_id`, `name`, `display_name`, `version`, `description`, `settings`, `type`, `is_active`, `created_at`, `updated_at`) VALUES (2,'tenantmanagement','Alan Adı Yönetimi','1.0.0','Çoklu müşteri yönetim sistemi',NULL,'system',1,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `modules` (`module_id`, `name`, `display_name`, `version`, `description`, `settings`, `type`, `is_active`, `created_at`, `updated_at`) VALUES (3,'ai','Yapay Zeka','1.0.0','Yapay Zeka modülü',NULL,'ai',1,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `modules` (`module_id`, `name`, `display_name`, `version`, `description`, `settings`, `type`, `is_active`, `created_at`, `updated_at`) VALUES (4,'usermanagement','Kullanıcılar','1.0.0','Kullanıcı yönetim sistemi',NULL,'management',1,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `modules` (`module_id`, `name`, `display_name`, `version`, `description`, `settings`, `type`, `is_active`, `created_at`, `updated_at`) VALUES (5,'menumanagement','Menü Yönetimi','1.0.0','Site menü yönetim sistemi',NULL,'management',1,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `modules` (`module_id`, `name`, `display_name`, `version`, `description`, `settings`, `type`, `is_active`, `created_at`, `updated_at`) VALUES (6,'mediamanagement','Medya Yönetimi','1.0.0','Universal medya yönetim sistemi - Image, Video, Audio, Document desteği',NULL,'management',1,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `modules` (`module_id`, `name`, `display_name`, `version`, `description`, `settings`, `type`, `is_active`, `created_at`, `updated_at`) VALUES (7,'settingmanagement','Ayarlar Yönetimi','1.0.0','Sistem ayarlarının yönetimi',NULL,'system',1,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `modules` (`module_id`, `name`, `display_name`, `version`, `description`, `settings`, `type`, `is_active`, `created_at`, `updated_at`) VALUES (8,'widgetmanagement','Bileşenler','1.0.0','Widget bileşenlerinin yönetimi',NULL,'widget',1,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `modules` (`module_id`, `name`, `display_name`, `version`, `description`, `settings`, `type`, `is_active`, `created_at`, `updated_at`) VALUES (9,'thememanagement','Tema Yönetimi','1.0.0','Tema yönetimi',NULL,'system',1,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `modules` (`module_id`, `name`, `display_name`, `version`, `description`, `settings`, `type`, `is_active`, `created_at`, `updated_at`) VALUES (10,'studio','Studio Editör','1.0.0','Studio ile site yönetimi',NULL,'system',1,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `modules` (`module_id`, `name`, `display_name`, `version`, `description`, `settings`, `type`, `is_active`, `created_at`, `updated_at`) VALUES (11,'announcement','Duyurular','1.0.0','Duyuru yönetimi',NULL,'content',1,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `modules` (`module_id`, `name`, `display_name`, `version`, `description`, `settings`, `type`, `is_active`, `created_at`, `updated_at`) VALUES (12,'page','Sayfalar','1.0.0','Statik sayfa yönetim sistemi',9,'content',1,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `modules` (`module_id`, `name`, `display_name`, `version`, `description`, `settings`, `type`, `is_active`, `created_at`, `updated_at`) VALUES (13,'portfolio','Portfolyo','1.0.0','Portföy yönetim sistemi',10,'content',1,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `modules` (`module_id`, `name`, `display_name`, `version`, `description`, `settings`, `type`, `is_active`, `created_at`, `updated_at`) VALUES (14,'languagemanagement','Dil Yönetimi','1.0.0','Çoklu dil yönetim sistemi',NULL,'system',1,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `modules` (`module_id`, `name`, `display_name`, `version`, `description`, `settings`, `type`, `is_active`, `created_at`, `updated_at`) VALUES (15,'seomanagement','SEO Yönetimi','1.0.0','Universal SEO yönetim sistemi - Çoklu dil desteği ile tüm modüller için merkezi SEO ayarları',NULL,'system',1,'2025-10-05 00:43:13','2025-10-05 00:43:13');
/*!40000 ALTER TABLE `modules` ENABLE KEYS */;
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
