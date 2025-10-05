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
-- Dumping data for table `ai_feature_inputs`
--

LOCK TABLES `ai_feature_inputs` WRITE;
/*!40000 ALTER TABLE `ai_feature_inputs` DISABLE KEYS */;
INSERT INTO `ai_feature_inputs` (`id`, `feature_id`, `name`, `slug`, `type`, `placeholder`, `help_text`, `is_primary`, `group_id`, `sort_order`, `is_required`, `validation_rules`, `default_value`, `prompt_placeholder`, `config`, `conditional_logic`, `dynamic_data_source_id`, `created_at`, `updated_at`) VALUES (6,301,'Kaynak Dil','source_language','select','İçeriğin mevcut dilini seçin','Çevrilecek içeriğin şu anki dili hangisi?',0,3,1,1,'[\"required\", \"string\"]',NULL,NULL,'{\"show_flags\": true, \"auto_detect\": true, \"data_filter\": {\"is_active\": true}, \"data_source\": \"tenant_languages\", \"label_field\": \"name\", \"value_field\": \"code\"}',NULL,NULL,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `ai_feature_inputs` (`id`, `feature_id`, `name`, `slug`, `type`, `placeholder`, `help_text`, `is_primary`, `group_id`, `sort_order`, `is_required`, `validation_rules`, `default_value`, `prompt_placeholder`, `config`, `conditional_logic`, `dynamic_data_source_id`, `created_at`, `updated_at`) VALUES (7,301,'İçerik Türü','content_type','radio',NULL,'Ne tür içerik çevirmek istiyorsunuz?',0,3,2,1,'[\"required\", \"in:page,text,seo,bulk\"]',NULL,NULL,'{\"display\": \"cards\", \"options\": [{\"label\": \"Sayfa İçeriği\", \"value\": \"page\", \"description\": \"Mevcut sayfalarınızı çevirin\"}, {\"label\": \"Metin Çevirisi\", \"value\": \"text\", \"description\": \"Tek seferde metin çevirin\"}, {\"label\": \"SEO Çevirisi\", \"value\": \"seo\", \"description\": \"SEO ayarlarıyla birlikte çeviri\"}, {\"label\": \"Toplu Çeviri\", \"value\": \"bulk\", \"description\": \"Birden fazla içeriği çevirin\"}]}',NULL,NULL,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `ai_feature_inputs` (`id`, `feature_id`, `name`, `slug`, `type`, `placeholder`, `help_text`, `is_primary`, `group_id`, `sort_order`, `is_required`, `validation_rules`, `default_value`, `prompt_placeholder`, `config`, `conditional_logic`, `dynamic_data_source_id`, `created_at`, `updated_at`) VALUES (8,301,'Hedef Diller','target_languages','select','Çeviri yapılacak dilleri seçin','İçeriğin çevrileceği dilleri seçebilirsiniz (birden fazla seçim yapabilirsiniz)',0,4,1,1,'[\"required\", \"array\", \"min:1\"]',NULL,NULL,'{\"multiple\": true, \"show_flags\": true, \"data_filter\": {\"is_active\": true}, \"data_source\": \"tenant_languages\", \"label_field\": \"name\", \"value_field\": \"code\", \"exclude_source\": true, \"max_selections\": 10}',NULL,NULL,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `ai_feature_inputs` (`id`, `feature_id`, `name`, `slug`, `type`, `placeholder`, `help_text`, `is_primary`, `group_id`, `sort_order`, `is_required`, `validation_rules`, `default_value`, `prompt_placeholder`, `config`, `conditional_logic`, `dynamic_data_source_id`, `created_at`, `updated_at`) VALUES (9,301,'Çeviri Kalitesi','translation_quality','select','Çeviri kalitesi seviyesini seçin','Daha yüksek kalite daha fazla token kullanır',0,4,2,0,'[\"nullable\", \"string\"]',NULL,NULL,'{\"options\": [{\"label\": \"Hızlı Çeviri\", \"value\": \"fast\", \"description\": \"Temel çeviri, düşük token\"}, {\"label\": \"Dengeli Çeviri\", \"value\": \"balanced\", \"description\": \"Kalite ve hız dengesi\"}, {\"label\": \"Premium Çeviri\", \"value\": \"premium\", \"description\": \"En yüksek kalite, yüksek token\"}], \"default_value\": \"balanced\"}',NULL,NULL,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `ai_feature_inputs` (`id`, `feature_id`, `name`, `slug`, `type`, `placeholder`, `help_text`, `is_primary`, `group_id`, `sort_order`, `is_required`, `validation_rules`, `default_value`, `prompt_placeholder`, `config`, `conditional_logic`, `dynamic_data_source_id`, `created_at`, `updated_at`) VALUES (10,301,'SEO Ayarlarını Çevir','preserve_seo','checkbox',NULL,'Meta başlıklar, açıklamalar ve SEO alanları da çevrilsin mi?',0,5,1,0,'[\"nullable\", \"boolean\"]',NULL,NULL,'{\"icon\": \"fas fa-search\", \"size\": \"default\", \"color\": \"success\", \"style\": \"switch\", \"default_value\": true}',NULL,NULL,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `ai_feature_inputs` (`id`, `feature_id`, `name`, `slug`, `type`, `placeholder`, `help_text`, `is_primary`, `group_id`, `sort_order`, `is_required`, `validation_rules`, `default_value`, `prompt_placeholder`, `config`, `conditional_logic`, `dynamic_data_source_id`, `created_at`, `updated_at`) VALUES (11,301,'Formatı Koru','preserve_formatting','checkbox',NULL,'HTML etiketleri, Markdown formatı ve linkleri koruyarak çeviri yap',0,5,2,0,'[\"nullable\", \"boolean\"]',NULL,NULL,'{\"icon\": \"fas fa-code\", \"size\": \"default\", \"color\": \"info\", \"style\": \"switch\", \"default_value\": true}',NULL,NULL,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `ai_feature_inputs` (`id`, `feature_id`, `name`, `slug`, `type`, `placeholder`, `help_text`, `is_primary`, `group_id`, `sort_order`, `is_required`, `validation_rules`, `default_value`, `prompt_placeholder`, `config`, `conditional_logic`, `dynamic_data_source_id`, `created_at`, `updated_at`) VALUES (12,301,'Kültürel Uyarlama','cultural_adaptation','checkbox',NULL,'Sadece çeviri değil, hedef kültüre uygun uyarlama yap (daha uzun sürer)',0,5,3,0,'[\"nullable\", \"boolean\"]',NULL,NULL,'{\"icon\": \"fas fa-globe-americas\", \"size\": \"default\", \"color\": \"warning\", \"style\": \"switch\", \"default_value\": false, \"premium_feature\": true}',NULL,NULL,'2025-10-05 00:43:13','2025-10-05 00:43:13');
/*!40000 ALTER TABLE `ai_feature_inputs` ENABLE KEYS */;
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
