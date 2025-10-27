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
-- Dumping data for table `menu_items`
--

LOCK TABLES `menu_items` WRITE;
/*!40000 ALTER TABLE `menu_items` DISABLE KEYS */;
INSERT INTO `menu_items` (`item_id`, `menu_id`, `parent_id`, `title`, `url_type`, `url_data`, `target`, `icon`, `is_active`, `visibility`, `sort_order`, `depth_level`, `created_at`, `updated_at`, `deleted_at`) VALUES (1,1,NULL,'{\"ar\": \"الصفحات\", \"en\": \"Pages\", \"tr\": \"Sayfalar\"}','module','{\"type\": \"index\", \"module\": \"page\"}','_self',NULL,1,'public',1,0,'2025-10-05 00:43:13','2025-10-05 00:43:13',NULL);
INSERT INTO `menu_items` (`item_id`, `menu_id`, `parent_id`, `title`, `url_type`, `url_data`, `target`, `icon`, `is_active`, `visibility`, `sort_order`, `depth_level`, `created_at`, `updated_at`, `deleted_at`) VALUES (2,1,NULL,'{\"en\": \"Portfolio\", \"tr\": \"Portfolyo\"}','module','{\"type\": \"index\", \"module\": \"portfolio\"}','_self',NULL,1,'public',2,0,'2025-10-05 00:43:13','2025-10-05 00:43:13',NULL);
INSERT INTO `menu_items` (`item_id`, `menu_id`, `parent_id`, `title`, `url_type`, `url_data`, `target`, `icon`, `is_active`, `visibility`, `sort_order`, `depth_level`, `created_at`, `updated_at`, `deleted_at`) VALUES (3,1,2,'{\"en\": \"Web Design\", \"tr\": \"Web Tasarım\"}','module','{\"id\": 1, \"type\": \"category\", \"module\": \"portfolio\"}','_self',NULL,1,'public',1,0,'2025-10-05 00:43:13','2025-10-05 00:43:13',NULL);
INSERT INTO `menu_items` (`item_id`, `menu_id`, `parent_id`, `title`, `url_type`, `url_data`, `target`, `icon`, `is_active`, `visibility`, `sort_order`, `depth_level`, `created_at`, `updated_at`, `deleted_at`) VALUES (4,1,2,'{\"en\": \"Mobile App\", \"tr\": \"Mobil Uygulama\"}','module','{\"id\": 2, \"type\": \"category\", \"module\": \"portfolio\"}','_self',NULL,1,'public',2,0,'2025-10-05 00:43:13','2025-10-05 00:43:13',NULL);
INSERT INTO `menu_items` (`item_id`, `menu_id`, `parent_id`, `title`, `url_type`, `url_data`, `target`, `icon`, `is_active`, `visibility`, `sort_order`, `depth_level`, `created_at`, `updated_at`, `deleted_at`) VALUES (5,1,2,'{\"en\": \"E-Commerce\", \"tr\": \"E-Ticaret\"}','module','{\"id\": 3, \"type\": \"category\", \"module\": \"portfolio\"}','_self',NULL,1,'public',3,0,'2025-10-05 00:43:13','2025-10-05 00:43:13',NULL);
INSERT INTO `menu_items` (`item_id`, `menu_id`, `parent_id`, `title`, `url_type`, `url_data`, `target`, `icon`, `is_active`, `visibility`, `sort_order`, `depth_level`, `created_at`, `updated_at`, `deleted_at`) VALUES (6,1,2,'{\"en\": \"Corporate Web\", \"tr\": \"Kurumsal Web\"}','module','{\"id\": 4, \"type\": \"category\", \"module\": \"portfolio\"}','_self',NULL,1,'public',4,0,'2025-10-05 00:43:13','2025-10-05 00:43:13',NULL);
INSERT INTO `menu_items` (`item_id`, `menu_id`, `parent_id`, `title`, `url_type`, `url_data`, `target`, `icon`, `is_active`, `visibility`, `sort_order`, `depth_level`, `created_at`, `updated_at`, `deleted_at`) VALUES (7,1,NULL,'{\"en\": \"Announcements\", \"tr\": \"Duyurular\"}','module','{\"type\": \"index\", \"module\": \"announcement\"}','_self',NULL,1,'public',3,0,'2025-10-05 00:43:13','2025-10-05 00:43:13',NULL);
/*!40000 ALTER TABLE `menu_items` ENABLE KEYS */;
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
