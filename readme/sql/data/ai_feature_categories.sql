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
-- Dumping data for table `ai_feature_categories`
--

LOCK TABLES `ai_feature_categories` WRITE;
/*!40000 ALTER TABLE `ai_feature_categories` DISABLE KEYS */;
INSERT INTO `ai_feature_categories` (`ai_feature_category_id`, `title`, `slug`, `description`, `order`, `icon`, `is_active`, `parent_id`, `has_subcategories`, `created_at`, `updated_at`) VALUES (1,'SEO ve Optimizasyon','seo-optimization','Arama motoru optimizasyonu ve web site performansı',1,'fas fa-search',1,NULL,0,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `ai_feature_categories` (`ai_feature_category_id`, `title`, `slug`, `description`, `order`, `icon`, `is_active`, `parent_id`, `has_subcategories`, `created_at`, `updated_at`) VALUES (2,'İçerik Yazıcılığı','content-writing','Blog, makale, sosyal medya içerik üretimi',2,'fas fa-pen-fancy',1,NULL,0,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `ai_feature_categories` (`ai_feature_category_id`, `title`, `slug`, `description`, `order`, `icon`, `is_active`, `parent_id`, `has_subcategories`, `created_at`, `updated_at`) VALUES (3,'Çeviri ve Lokalizasyon','translation','Çoklu dil çeviri ve yerelleştirme hizmetleri',3,'fas fa-language',1,NULL,0,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `ai_feature_categories` (`ai_feature_category_id`, `title`, `slug`, `description`, `order`, `icon`, `is_active`, `parent_id`, `has_subcategories`, `created_at`, `updated_at`) VALUES (4,'Pazarlama & Reklam','marketing-advertising','Reklam metinleri, kampanya içerikleri, landing page',4,'fas fa-bullhorn',1,NULL,0,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `ai_feature_categories` (`ai_feature_category_id`, `title`, `slug`, `description`, `order`, `icon`, `is_active`, `parent_id`, `has_subcategories`, `created_at`, `updated_at`) VALUES (5,'E-ticaret ve Satış','ecommerce-sales','Ürün açıklamaları, satış metinleri, e-ticaret içerikleri',5,'fas fa-shopping-cart',1,NULL,0,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `ai_feature_categories` (`ai_feature_category_id`, `title`, `slug`, `description`, `order`, `icon`, `is_active`, `parent_id`, `has_subcategories`, `created_at`, `updated_at`) VALUES (6,'Sosyal Medya','social-media','Sosyal medya paylaşımları, hashtag önerileri, engagement',6,'fas fa-share-alt',1,NULL,0,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `ai_feature_categories` (`ai_feature_category_id`, `title`, `slug`, `description`, `order`, `icon`, `is_active`, `parent_id`, `has_subcategories`, `created_at`, `updated_at`) VALUES (7,'Email & İletişim','email-communication','Newsletter, email marketing, iş iletişimi',7,'fas fa-envelope',1,NULL,0,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `ai_feature_categories` (`ai_feature_category_id`, `title`, `slug`, `description`, `order`, `icon`, `is_active`, `parent_id`, `has_subcategories`, `created_at`, `updated_at`) VALUES (8,'Analiz ve Raporlama','analytics-reporting','Veri analizi, rapor yazımı, istatistiksel değerlendirmeler',8,'fas fa-chart-line',1,NULL,0,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `ai_feature_categories` (`ai_feature_category_id`, `title`, `slug`, `description`, `order`, `icon`, `is_active`, `parent_id`, `has_subcategories`, `created_at`, `updated_at`) VALUES (9,'Müşteri Hizmetleri','customer-service','Müşteri yanıtları, destek metinleri, FAQ\'lar',9,'fas fa-headset',1,NULL,0,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `ai_feature_categories` (`ai_feature_category_id`, `title`, `slug`, `description`, `order`, `icon`, `is_active`, `parent_id`, `has_subcategories`, `created_at`, `updated_at`) VALUES (10,'İş Geliştirme','business-development','İş planları, sunum metinleri, kurumsal içerikler',10,'fas fa-briefcase',1,NULL,0,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `ai_feature_categories` (`ai_feature_category_id`, `title`, `slug`, `description`, `order`, `icon`, `is_active`, `parent_id`, `has_subcategories`, `created_at`, `updated_at`) VALUES (11,'Araştırma & Pazar','research-market','Pazar araştırması, competitor analizi, survey',11,'fas fa-chart-pie',1,NULL,0,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `ai_feature_categories` (`ai_feature_category_id`, `title`, `slug`, `description`, `order`, `icon`, `is_active`, `parent_id`, `has_subcategories`, `created_at`, `updated_at`) VALUES (12,'Yaratıcı İçerik','creative-content','Hikaye yazımı, yaratıcı metinler, senaryolar',12,'fas fa-palette',1,NULL,0,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `ai_feature_categories` (`ai_feature_category_id`, `title`, `slug`, `description`, `order`, `icon`, `is_active`, `parent_id`, `has_subcategories`, `created_at`, `updated_at`) VALUES (13,'Teknik Dokümantasyon','technical-docs','API dokümantasyonu, kullanıcı kılavuzları, teknik açıklamalar',13,'fas fa-book',1,NULL,0,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `ai_feature_categories` (`ai_feature_category_id`, `title`, `slug`, `description`, `order`, `icon`, `is_active`, `parent_id`, `has_subcategories`, `created_at`, `updated_at`) VALUES (14,'Kod & Yazılım','code-software','API dokümantasyonu, kod açıklamaları, tutorial',14,'fas fa-laptop-code',1,NULL,0,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `ai_feature_categories` (`ai_feature_category_id`, `title`, `slug`, `description`, `order`, `icon`, `is_active`, `parent_id`, `has_subcategories`, `created_at`, `updated_at`) VALUES (15,'Tasarım & UI/UX','design-ui-ux','Microcopy, error messages, UI metinleri',15,'fas fa-paint-brush',1,NULL,0,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `ai_feature_categories` (`ai_feature_category_id`, `title`, `slug`, `description`, `order`, `icon`, `is_active`, `parent_id`, `has_subcategories`, `created_at`, `updated_at`) VALUES (16,'Eğitim ve Öğretim','education','Eğitim materyalleri, kurs içerikleri, sınav soruları',16,'fas fa-graduation-cap',1,NULL,0,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `ai_feature_categories` (`ai_feature_category_id`, `title`, `slug`, `description`, `order`, `icon`, `is_active`, `parent_id`, `has_subcategories`, `created_at`, `updated_at`) VALUES (17,'Finans & İş','finance-business','İş planları, finansal analiz, ROI raporları',17,'fas fa-calculator',1,NULL,0,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `ai_feature_categories` (`ai_feature_category_id`, `title`, `slug`, `description`, `order`, `icon`, `is_active`, `parent_id`, `has_subcategories`, `created_at`, `updated_at`) VALUES (18,'Hukuki ve Uyumluluk','legal-compliance','Sözleşmeler, kullanım şartları, gizlilik politikaları',18,'fas fa-gavel',1,NULL,0,'2025-10-05 00:43:13','2025-10-05 00:43:13');
/*!40000 ALTER TABLE `ai_feature_categories` ENABLE KEYS */;
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
