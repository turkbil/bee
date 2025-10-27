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
-- Dumping data for table `ai_feature_prompt_relations`
--

LOCK TABLES `ai_feature_prompt_relations` WRITE;
/*!40000 ALTER TABLE `ai_feature_prompt_relations` DISABLE KEYS */;
INSERT INTO `ai_feature_prompt_relations` (`id`, `feature_id`, `prompt_id`, `feature_prompt_id`, `priority`, `role`, `is_active`, `conditions`, `notes`, `category_context`, `feature_type_filter`, `business_rules`, `created_at`, `updated_at`) VALUES (27,501,5001,NULL,1,'primary',1,NULL,NULL,NULL,'specific',NULL,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `ai_feature_prompt_relations` (`id`, `feature_id`, `prompt_id`, `feature_prompt_id`, `priority`, `role`, `is_active`, `conditions`, `notes`, `category_context`, `feature_type_filter`, `business_rules`, `created_at`, `updated_at`) VALUES (28,501,5002,NULL,2,'secondary',1,NULL,NULL,NULL,'specific',NULL,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `ai_feature_prompt_relations` (`id`, `feature_id`, `prompt_id`, `feature_prompt_id`, `priority`, `role`, `is_active`, `conditions`, `notes`, `category_context`, `feature_type_filter`, `business_rules`, `created_at`, `updated_at`) VALUES (29,501,5003,NULL,3,'secondary',1,NULL,NULL,NULL,'specific',NULL,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `ai_feature_prompt_relations` (`id`, `feature_id`, `prompt_id`, `feature_prompt_id`, `priority`, `role`, `is_active`, `conditions`, `notes`, `category_context`, `feature_type_filter`, `business_rules`, `created_at`, `updated_at`) VALUES (30,501,5004,NULL,4,'secondary',1,NULL,NULL,NULL,'specific',NULL,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `ai_feature_prompt_relations` (`id`, `feature_id`, `prompt_id`, `feature_prompt_id`, `priority`, `role`, `is_active`, `conditions`, `notes`, `category_context`, `feature_type_filter`, `business_rules`, `created_at`, `updated_at`) VALUES (31,501,5005,NULL,5,'secondary',1,NULL,NULL,NULL,'specific',NULL,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `ai_feature_prompt_relations` (`id`, `feature_id`, `prompt_id`, `feature_prompt_id`, `priority`, `role`, `is_active`, `conditions`, `notes`, `category_context`, `feature_type_filter`, `business_rules`, `created_at`, `updated_at`) VALUES (32,501,5100,NULL,100,'primary',1,NULL,NULL,NULL,'specific',NULL,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `ai_feature_prompt_relations` (`id`, `feature_id`, `prompt_id`, `feature_prompt_id`, `priority`, `role`, `is_active`, `conditions`, `notes`, `category_context`, `feature_type_filter`, `business_rules`, `created_at`, `updated_at`) VALUES (38,301,20001,NULL,1,'primary',1,NULL,NULL,NULL,'all',NULL,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `ai_feature_prompt_relations` (`id`, `feature_id`, `prompt_id`, `feature_prompt_id`, `priority`, `role`, `is_active`, `conditions`, `notes`, `category_context`, `feature_type_filter`, `business_rules`, `created_at`, `updated_at`) VALUES (39,301,20002,NULL,2,'supportive',1,NULL,NULL,NULL,'all',NULL,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `ai_feature_prompt_relations` (`id`, `feature_id`, `prompt_id`, `feature_prompt_id`, `priority`, `role`, `is_active`, `conditions`, `notes`, `category_context`, `feature_type_filter`, `business_rules`, `created_at`, `updated_at`) VALUES (40,301,20003,NULL,3,'secondary',1,NULL,NULL,NULL,'all',NULL,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `ai_feature_prompt_relations` (`id`, `feature_id`, `prompt_id`, `feature_prompt_id`, `priority`, `role`, `is_active`, `conditions`, `notes`, `category_context`, `feature_type_filter`, `business_rules`, `created_at`, `updated_at`) VALUES (41,301,90013,NULL,4,'supportive',1,NULL,NULL,NULL,'all',NULL,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `ai_feature_prompt_relations` (`id`, `feature_id`, `prompt_id`, `feature_prompt_id`, `priority`, `role`, `is_active`, `conditions`, `notes`, `category_context`, `feature_type_filter`, `business_rules`, `created_at`, `updated_at`) VALUES (47,201,NULL,1001,1,'primary',1,'{\"applies_to\": \"all_content_types\", \"user_level\": [\"beginner\", \"intermediate\", \"advanced\"], \"content_length\": [\"short\", \"medium\", \"long\"]}','Genel içerik üretim prensipleri ve kalite standartları için kullanılır',NULL,'all',NULL,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `ai_feature_prompt_relations` (`id`, `feature_id`, `prompt_id`, `feature_prompt_id`, `priority`, `role`, `is_active`, `conditions`, `notes`, `category_context`, `feature_type_filter`, `business_rules`, `created_at`, `updated_at`) VALUES (48,201,NULL,1002,2,'supportive',1,'{\"applies_when\": \"seo_optimization_enabled\", \"content_type\": [\"blog\", \"article\", \"web_content\"], \"min_content_length\": 300}','Arama motoru optimizasyonu ve organik trafik artışı için kullanılır',NULL,'all',NULL,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `ai_feature_prompt_relations` (`id`, `feature_id`, `prompt_id`, `feature_prompt_id`, `priority`, `role`, `is_active`, `conditions`, `notes`, `category_context`, `feature_type_filter`, `business_rules`, `created_at`, `updated_at`) VALUES (49,201,NULL,1003,3,'secondary',1,'{\"content_type\": \"blog_post\", \"social_sharing\": \"enabled\", \"engagement_focus\": true}','Blog formatına özel teknikler ve engagement stratejileri için kullanılır',NULL,'all',NULL,'2025-10-05 00:43:13','2025-10-05 00:43:13');
/*!40000 ALTER TABLE `ai_feature_prompt_relations` ENABLE KEYS */;
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
