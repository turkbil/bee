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
-- Dumping data for table `ai_tenant_profiles`
--

LOCK TABLES `ai_tenant_profiles` WRITE;
/*!40000 ALTER TABLE `ai_tenant_profiles` DISABLE KEYS */;
INSERT INTO `ai_tenant_profiles` (`id`, `tenant_id`, `company_info`, `sector_details`, `success_stories`, `ai_behavior_rules`, `founder_info`, `additional_info`, `brand_story`, `brand_story_created_at`, `ai_context`, `context_priority`, `smart_field_scores`, `field_calculation_metadata`, `profile_completeness_score`, `profile_quality_grade`, `last_calculation_context`, `scores_calculated_at`, `context_performance`, `ai_recommendations`, `missing_critical_fields`, `field_quality_analysis`, `usage_analytics`, `ai_interactions_count`, `last_ai_interaction_at`, `avg_ai_response_quality`, `profile_version`, `version_history`, `auto_optimization_enabled`, `is_active`, `is_completed`, `data`, `created_at`, `updated_at`) VALUES (1,1,'{\"city\": \"İstanbul\", \"brand_name\": \"Türk Bilişim\", \"main_service\": \"Web Tasarım, Sosyal Medya, İnternet Reklamcılığı ve Grafik Tasarım\", \"founder_experience\": {\"tech\": true, \"design\": true, \"business\": true, \"marketing\": true, \"consulting\": true, \"management\": true}, \"founder_permission\": \"yes_full\", \"share_founder_info\": \"evet\", \"business_start_year\": \"2020\", \"share_founder_info_label\": \"Evet, bilgilerimi paylaşmak istiyorum\", \"share_founder_info_question\": \"Kurucu hakkında bilgi paylaşmak ister misiniz?\", \"business_start_year_question\": \"Hangi yıldan beri bu işi yapıyorsunuz?\"}','{\"sector\": \"web_design\", \"branches\": \"hybrid\", \"brand_age\": \"custom\", \"sector_name\": \"Web Tasarım\", \"company_size\": \"small\", \"project_types\": {\"web-apps\": true, \"mobile-apps\": true}, \"tech_services\": {\"e-ticaret\": true, \"web-tasarim\": true, \"mobil-uygulama\": true, \"yazilim-gelistirme\": true}, \"market_position\": \"premium\", \"target_audience\": {\"b2b-large\": true, \"b2b-small\": true, \"b2b-medium\": true}, \"brand_age_custom\": \"1998 den beri\", \"sector_selection\": \"web_design\", \"target_customers\": {\"buyuk_sirketler\": true}, \"brand_personality\": {\"friendly\": true}, \"sector_description\": \"Website tasarım, UI/UX\", \"main_business_activities\": \"WEB TASARIM\", \"main_business_activities_question\": \"Yaptığınız ana iş kolları nelerdir?\"}','{\"brand_voice\": \"advisor\", \"avoid_topics\": {\"controversy\": true}, \"writing_tone\": {\"formal\": true}, \"content_focus\": \"result\", \"writing_style\": {\"kisa_net\": true, \"sade_anlasilir\": true}, \"brand_character\": {\"geleneksel_koklu\": true}, \"emphasis_points\": {\"quality\": true}}','{\"brand_voice\": \"trustworthy\", \"avoid_topics\": {\"politics\": true, \"controversy\": true}, \"writing_tone\": {\"friendly\": true, \"professional\": true}, \"emphasis_points\": {\"trust\": true, \"quality\": true, \"experience\": true}, \"content_approach\": {\"storytelling\": true, \"benefit-focused\": true}, \"communication_style\": {\"consultative\": true, \"solution-focused\": true}}','{\"founder_name\": \"Nurullah Okatan\", \"founder_role\": \"founder\", \"founder_qualities\": {\"liderlik\": true}, \"founder_experience\": {\"tech\": true, \"design\": true, \"business\": true, \"consulting\": true, \"management\": true}, \"founder_personality\": {\"visionary\": true, \"analytical\": true}}','[]','1998 yılında, internetin Türkiye\'de henüz emekleme döneminde olduğu günlerde, Nurullah Okatan bir vizyonla yola çıktı. O dönemde web tasarım dendiğinde akla sadece basit sayfalar geliyordu, ancak kurucumuz dijital dünyanın potansiyelini erken fark edenlerdendi. Küçük bir ekip ve büyük bir tutkuyla başlayan bu yolculuk, zamanla Türk Bilişim\'in bugünkü saygın konumuna ulaşmasını sağladı. İlk günlerden itibaren odak noktası, müşterilerine sadece estetik değil aynı zamanda işlevsel çözümler sunmaktı.\n\nYıllar içinde teknoloji hızla değişirken, ekip olarak kendilerini sürekli yenilemenin önemini kavradılar. Web tasarımın yanı sıra sosyal medya yönetimi ve internet reklamcılığı alanlarında uzmanlaşarak, müşterilerine bütüncül dijital çözümler sunmaya başladılar. Her projeye yaklaşımlarındaki titizlik ve detaylara verilen önem, kısa sürede güvenilir bir marka olarak tanınmalarını sağladı. Müşteri memnuniyetini her şeyin üzerinde tutan bu anlayış, onları sektörde öne çıkaran en önemli değer oldu.\n\nBugün Türk Bilişim, 25 yılı aşkın deneyimiyle yüzlerce başarılı projeye imza atmış bir marka. Ancak hikayenin en güzel yanı, hala o ilk günkü heyecanı koruyor olmaları. Her yeni projeyi bir öncekinden daha iyi nasıl yapabileceklerini düşünerek, sürekli kendilerini geliştirmeye devam ediyorlar. Müşterileriyle kurdukları samimi ilişkiler ve uzun soluklu iş birlikleri, kaliteli hizmet anlayışlarının en büyük kanıtı.\n\nGeleceğe bakarken, dijital dünyanın sınırlarını zorlamaya ve yenilikçi çözümler üretmeye devam edecekler. Çünkü onlar için bu sadece bir iş değil, aynı zamanda bir tutku. Türk Bilişim ekibi, her yeni güne \"bugün daha iyi nasıl yapabiliriz\" sorusuyla başlıyor ve bu sorunun peşinden koşmayı asla bırakmıyor.','2025-07-13 13:22:59',NULL,NULL,NULL,NULL,0.00,'F','normal',NULL,NULL,NULL,0,NULL,NULL,0,NULL,0.00,1,NULL,1,1,1,NULL,'2025-07-08 12:39:26','2025-07-13 15:23:55');
/*!40000 ALTER TABLE `ai_tenant_profiles` ENABLE KEYS */;
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
