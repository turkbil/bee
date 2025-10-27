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
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` (`id`, `group_id`, `label`, `key`, `type`, `options`, `default_value`, `sort_order`, `is_active`, `is_system`, `is_required`, `help_text`, `created_at`, `updated_at`) VALUES (1,6,'Site - Firma - Kurum Adı','site_title','text',NULL,'Türk Bilişim',1,1,1,1,'Sitenizin genel başlığı, tarayıcı başlıklarında ve meta etiketlerinde kullanılır.','2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `settings` (`id`, `group_id`, `label`, `key`, `type`, `options`, `default_value`, `sort_order`, `is_active`, `is_system`, `is_required`, `help_text`, `created_at`, `updated_at`) VALUES (2,6,'Site Logo','site_logo','file',NULL,NULL,2,1,1,0,'Site logonuz, tercihen PNG veya SVG formatında şeffaf arka planlı bir dosya.','2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `settings` (`id`, `group_id`, `label`, `key`, `type`, `options`, `default_value`, `sort_order`, `is_active`, `is_system`, `is_required`, `help_text`, `created_at`, `updated_at`) VALUES (3,6,'Favicon','site_favicon','file',NULL,'favicon.ico',3,1,1,0,'Favicon, tarayıcı sekmesinde görünen küçük simgedir. Tercihen 32x32 veya 16x16 boyutlarında PNG, ICO formatında olmalıdır.','2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `settings` (`id`, `group_id`, `label`, `key`, `type`, `options`, `default_value`, `sort_order`, `is_active`, `is_system`, `is_required`, `help_text`, `created_at`, `updated_at`) VALUES (4,6,'Ana E-posta Adresi','site_email','text',NULL,'info@turkbilisim.com.tr',4,1,1,1,'İletişim formlarından ve sistem bildirimlerinden gelen e-postaların gönderileceği adres.','2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `settings` (`id`, `group_id`, `label`, `key`, `type`, `options`, `default_value`, `sort_order`, `is_active`, `is_system`, `is_required`, `help_text`, `created_at`, `updated_at`) VALUES (5,6,'Google Analytics Kodu','site_google_analytics_code','text',NULL,NULL,5,1,0,0,'Google Analytics takip kodunuz (örn: G-XXXXXXXXXX). Boş bırakılırsa analitik takibi devre dışı kalır.','2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `settings` (`id`, `group_id`, `label`, `key`, `type`, `options`, `default_value`, `sort_order`, `is_active`, `is_system`, `is_required`, `help_text`, `created_at`, `updated_at`) VALUES (6,7,'Ana Renk','theme_primary_color','color',NULL,'#0ea5e9',1,1,1,1,'Sitenin ana rengi, düğmeler ve vurgu elementleri için kullanılır.','2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `settings` (`id`, `group_id`, `label`, `key`, `type`, `options`, `default_value`, `sort_order`, `is_active`, `is_system`, `is_required`, `help_text`, `created_at`, `updated_at`) VALUES (7,7,'İkincil Renk','theme_secondary_color','color',NULL,'#64748b',2,1,1,1,'İkincil renk, ikinci derecede önemli elementler için kullanılır.','2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `settings` (`id`, `group_id`, `label`, `key`, `type`, `options`, `default_value`, `sort_order`, `is_active`, `is_system`, `is_required`, `help_text`, `created_at`, `updated_at`) VALUES (8,7,'Vurgu Rengi','theme_accent_color','color',NULL,'#8b5cf6',3,1,1,1,'Vurgu rengi, dikkat çekilmesi gereken öğelerde kullanılır.','2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `settings` (`id`, `group_id`, `label`, `key`, `type`, `options`, `default_value`, `sort_order`, `is_active`, `is_system`, `is_required`, `help_text`, `created_at`, `updated_at`) VALUES (9,7,'Arkaplan Rengi','theme_background_color','color',NULL,'#ffffff',4,1,1,1,'Sayfanın genel arkaplan rengi.','2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `settings` (`id`, `group_id`, `label`, `key`, `type`, `options`, `default_value`, `sort_order`, `is_active`, `is_system`, `is_required`, `help_text`, `created_at`, `updated_at`) VALUES (10,7,'Metin Rengi','theme_text_color','color',NULL,'#333333',5,1,1,1,'Genel metin rengi.','2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `settings` (`id`, `group_id`, `label`, `key`, `type`, `options`, `default_value`, `sort_order`, `is_active`, `is_system`, `is_required`, `help_text`, `created_at`, `updated_at`) VALUES (11,7,'Başarı Rengi','theme_success_color','color',NULL,'#10b981',6,1,1,1,'Başarılı işlemleri belirtmek için kullanılan renk.','2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `settings` (`id`, `group_id`, `label`, `key`, `type`, `options`, `default_value`, `sort_order`, `is_active`, `is_system`, `is_required`, `help_text`, `created_at`, `updated_at`) VALUES (12,7,'Uyarı Rengi','theme_warning_color','color',NULL,'#f59e0b',7,1,1,1,'Uyarıları belirtmek için kullanılan renk.','2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `settings` (`id`, `group_id`, `label`, `key`, `type`, `options`, `default_value`, `sort_order`, `is_active`, `is_system`, `is_required`, `help_text`, `created_at`, `updated_at`) VALUES (13,7,'Hata Rengi','theme_danger_color','color',NULL,'#ef4444',8,1,1,1,'Hataları ve tehlikeli durumları belirtmek için kullanılan renk.','2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `settings` (`id`, `group_id`, `label`, `key`, `type`, `options`, `default_value`, `sort_order`, `is_active`, `is_system`, `is_required`, `help_text`, `created_at`, `updated_at`) VALUES (14,7,'Bilgi Rengi','theme_info_color','color',NULL,'#3b82f6',9,1,1,1,'Bilgi mesajları için kullanılan renk.','2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `settings` (`id`, `group_id`, `label`, `key`, `type`, `options`, `default_value`, `sort_order`, `is_active`, `is_system`, `is_required`, `help_text`, `created_at`, `updated_at`) VALUES (15,7,'Kart Arkaplan Rengi','theme_card_background_color','color',NULL,'#ffffff',10,1,1,1,'Kart elementlerinin arkaplan rengi.','2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `settings` (`id`, `group_id`, `label`, `key`, `type`, `options`, `default_value`, `sort_order`, `is_active`, `is_system`, `is_required`, `help_text`, `created_at`, `updated_at`) VALUES (16,7,'Gölge Rengi','theme_shadow_color','color',NULL,'rgba(0, 0, 0, 0.1)',11,1,1,1,'Element gölgeleri için kullanılan renk.','2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `settings` (`id`, `group_id`, `label`, `key`, `type`, `options`, `default_value`, `sort_order`, `is_active`, `is_system`, `is_required`, `help_text`, `created_at`, `updated_at`) VALUES (17,7,'Kenar Rengi','theme_border_color','color',NULL,'#e5e7eb',12,1,1,1,'Kenarlıklar ve ayırıcılar için kullanılan renk.','2025-10-05 00:43:13','2025-10-05 00:43:13');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
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
