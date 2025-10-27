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
-- Dumping data for table `widget_categories`
--

LOCK TABLES `widget_categories` WRITE;
/*!40000 ALTER TABLE `widget_categories` DISABLE KEYS */;
INSERT INTO `widget_categories` (`widget_category_id`, `title`, `slug`, `description`, `icon`, `order`, `is_active`, `parent_id`, `has_subcategories`, `deleted_at`, `created_at`, `updated_at`) VALUES (1,'Moduller','moduller','Sistem modüllerine ait bileşenler','fa-cubes',1,1,NULL,1,NULL,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `widget_categories` (`widget_category_id`, `title`, `slug`, `description`, `icon`, `order`, `is_active`, `parent_id`, `has_subcategories`, `deleted_at`, `created_at`, `updated_at`) VALUES (2,'Cards','cards','Cards bileşenleri','fa-id-card',2,1,NULL,0,NULL,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `widget_categories` (`widget_category_id`, `title`, `slug`, `description`, `icon`, `order`, `is_active`, `parent_id`, `has_subcategories`, `deleted_at`, `created_at`, `updated_at`) VALUES (3,'Content','content','Content bileşenleri','fa-align-left',3,1,NULL,0,NULL,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `widget_categories` (`widget_category_id`, `title`, `slug`, `description`, `icon`, `order`, `is_active`, `parent_id`, `has_subcategories`, `deleted_at`, `created_at`, `updated_at`) VALUES (4,'Features','features','Features bileşenleri','fa-star',4,1,NULL,0,NULL,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `widget_categories` (`widget_category_id`, `title`, `slug`, `description`, `icon`, `order`, `is_active`, `parent_id`, `has_subcategories`, `deleted_at`, `created_at`, `updated_at`) VALUES (5,'Form','form','Form bileşenleri','fa-wpforms',5,1,NULL,0,NULL,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `widget_categories` (`widget_category_id`, `title`, `slug`, `description`, `icon`, `order`, `is_active`, `parent_id`, `has_subcategories`, `deleted_at`, `created_at`, `updated_at`) VALUES (6,'Hero','hero','Hero bileşenleri','fa-heading',6,1,NULL,0,NULL,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `widget_categories` (`widget_category_id`, `title`, `slug`, `description`, `icon`, `order`, `is_active`, `parent_id`, `has_subcategories`, `deleted_at`, `created_at`, `updated_at`) VALUES (7,'Layout','layout','Layout bileşenleri','fa-columns',7,1,NULL,0,NULL,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `widget_categories` (`widget_category_id`, `title`, `slug`, `description`, `icon`, `order`, `is_active`, `parent_id`, `has_subcategories`, `deleted_at`, `created_at`, `updated_at`) VALUES (8,'Media','media','Media bileşenleri','fa-photo-video',8,1,NULL,0,NULL,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `widget_categories` (`widget_category_id`, `title`, `slug`, `description`, `icon`, `order`, `is_active`, `parent_id`, `has_subcategories`, `deleted_at`, `created_at`, `updated_at`) VALUES (9,'Testimonials','testimonials','Testimonials bileşenleri','fa-quote-right',9,1,NULL,0,NULL,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `widget_categories` (`widget_category_id`, `title`, `slug`, `description`, `icon`, `order`, `is_active`, `parent_id`, `has_subcategories`, `deleted_at`, `created_at`, `updated_at`) VALUES (13,'Announcement Modülü','announcement-moduelue','Announcement modülüne ait bileşenler','fa-puzzle-piece',999,1,1,0,NULL,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `widget_categories` (`widget_category_id`, `title`, `slug`, `description`, `icon`, `order`, `is_active`, `parent_id`, `has_subcategories`, `deleted_at`, `created_at`, `updated_at`) VALUES (14,'Page Modülü','page-moduelue','Page modülüne ait bileşenler','fa-puzzle-piece',999,1,1,0,NULL,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `widget_categories` (`widget_category_id`, `title`, `slug`, `description`, `icon`, `order`, `is_active`, `parent_id`, `has_subcategories`, `deleted_at`, `created_at`, `updated_at`) VALUES (15,'Portfolio Modülü','portfolio-moduelue','Portfolio modülüne ait bileşenler','fa-puzzle-piece',999,1,1,0,NULL,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `widget_categories` (`widget_category_id`, `title`, `slug`, `description`, `icon`, `order`, `is_active`, `parent_id`, `has_subcategories`, `deleted_at`, `created_at`, `updated_at`) VALUES (16,'Cards','cards-2','Cards bileşenleri','fa-puzzle-piece',999,1,NULL,0,NULL,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `widget_categories` (`widget_category_id`, `title`, `slug`, `description`, `icon`, `order`, `is_active`, `parent_id`, `has_subcategories`, `deleted_at`, `created_at`, `updated_at`) VALUES (17,'Content','content-2','Content bileşenleri','fa-puzzle-piece',999,1,NULL,0,NULL,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `widget_categories` (`widget_category_id`, `title`, `slug`, `description`, `icon`, `order`, `is_active`, `parent_id`, `has_subcategories`, `deleted_at`, `created_at`, `updated_at`) VALUES (18,'Features','features-2','Features bileşenleri','fa-puzzle-piece',999,1,NULL,0,NULL,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `widget_categories` (`widget_category_id`, `title`, `slug`, `description`, `icon`, `order`, `is_active`, `parent_id`, `has_subcategories`, `deleted_at`, `created_at`, `updated_at`) VALUES (19,'Form','form-2','Form bileşenleri','fa-puzzle-piece',999,1,NULL,0,NULL,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `widget_categories` (`widget_category_id`, `title`, `slug`, `description`, `icon`, `order`, `is_active`, `parent_id`, `has_subcategories`, `deleted_at`, `created_at`, `updated_at`) VALUES (20,'Hero','hero-2','Hero bileşenleri','fa-puzzle-piece',999,1,NULL,0,NULL,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `widget_categories` (`widget_category_id`, `title`, `slug`, `description`, `icon`, `order`, `is_active`, `parent_id`, `has_subcategories`, `deleted_at`, `created_at`, `updated_at`) VALUES (21,'Layout','layout-2','Layout bileşenleri','fa-puzzle-piece',999,1,NULL,0,NULL,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `widget_categories` (`widget_category_id`, `title`, `slug`, `description`, `icon`, `order`, `is_active`, `parent_id`, `has_subcategories`, `deleted_at`, `created_at`, `updated_at`) VALUES (22,'Media','media-2','Media bileşenleri','fa-puzzle-piece',999,1,NULL,0,NULL,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `widget_categories` (`widget_category_id`, `title`, `slug`, `description`, `icon`, `order`, `is_active`, `parent_id`, `has_subcategories`, `deleted_at`, `created_at`, `updated_at`) VALUES (23,'Testimonials','testimonials-2','Testimonials bileşenleri','fa-puzzle-piece',999,1,NULL,0,NULL,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `widget_categories` (`widget_category_id`, `title`, `slug`, `description`, `icon`, `order`, `is_active`, `parent_id`, `has_subcategories`, `deleted_at`, `created_at`, `updated_at`) VALUES (24,'Sliderlar','sliderlar','Slider ve carousel bileşenleri','fa-images',10,1,8,0,NULL,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `widget_categories` (`widget_category_id`, `title`, `slug`, `description`, `icon`, `order`, `is_active`, `parent_id`, `has_subcategories`, `deleted_at`, `created_at`, `updated_at`) VALUES (25,'Herolar','herolar','Sayfa üst kısmında kullanılabilecek hero bileşenleri','fa-heading',5,1,3,0,NULL,'2025-10-05 00:43:13','2025-10-05 00:43:13');
/*!40000 ALTER TABLE `widget_categories` ENABLE KEYS */;
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
