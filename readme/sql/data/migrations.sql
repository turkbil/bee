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
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1,'0001_01_01_000000_create_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2,'0001_01_01_000001_create_cache_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3,'0001_01_01_000002_create_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4,'2018_04_06_000001_create_themes_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5,'2019_09_15_000010_create_tenants_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6,'2019_09_15_000020_create_domains_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7,'2020_06_21_000001_create_admin_languages_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8,'2020_06_21_000002_create_tenant_languages_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (9,'2024_02_17_000001_create_announcements_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (10,'2024_02_17_000001_create_pages_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (11,'2024_02_17_000001_create_portfolios_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (12,'2024_03_17_000001_create_modules_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (13,'2024_03_18_000001_create_module_tenants_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (14,'2024_11_27_203421_create_activity_log_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (15,'2024_12_01_000001_create_settings_groups_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (16,'2024_12_01_000002_create_settings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (17,'2024_12_01_000003_create_settings_values_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (18,'2025_02_14_132615_create_sessions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (19,'2025_02_15_190824_create_telescope_entries_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (20,'2025_02_15_192501_create_media_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (21,'2025_03_15_233856_create_permission_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (22,'2025_03_20_000001_create_widget_categories_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (23,'2025_03_20_000002_create_user_module_permissions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (24,'2025_04_01_000001_create_tenant_widgets_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (25,'2025_04_01_000001_create_widgets_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (26,'2025_04_01_000002_create_widget_items_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (27,'2025_04_01_000002_create_widget_modules_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (28,'2025_05_07_000000_create_ai_providers_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (29,'2025_05_07_000001_create_ai_conversations_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (30,'2025_05_07_000002_create_ai_messages_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (31,'2025_05_07_000003_create_ai_prompts_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (32,'2025_06_12_000001_create_module_tenant_settings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (33,'2025_07_01_000001_create_ai_credit_packages_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (34,'2025_07_01_000002_create_ai_credit_purchases_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (35,'2025_07_01_000003_create_ai_credit_usage_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (36,'2025_07_04_000001_create_ai_features_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (37,'2025_07_04_000002_create_ai_feature_prompts_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (38,'2025_07_07_000001_create_ai_tenant_profiles_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (39,'2025_07_07_000002_create_ai_profile_sectors_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (40,'2025_07_07_000003_create_ai_profile_questions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (41,'2025_07_09_021915_create_ai_feature_categories_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (42,'2025_07_11_214031_create_personal_access_tokens_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (43,'2025_07_13_000003_create_ai_tenant_debug_logs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (44,'2025_07_19_000001_add_ai_provider_foreign_key_to_ai_credit_usage',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (45,'2025_07_19_000001_create_seo_settings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (46,'2025_07_19_000002_create_tenant_seo_settings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (47,'2025_07_20_180713_create_a_i_credit_packages_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (48,'2025_07_30_000001_create_menus_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (49,'2025_07_30_000002_create_menu_items_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (50,'2025_08_08_030000_create_ai_feature_prompt_relations_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (51,'2025_08_10_013859_create_ai_dynamic_data_sources_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (52,'2025_08_10_013902_create_ai_input_groups_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (53,'2025_08_10_013903_create_ai_feature_inputs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (54,'2025_08_10_013904_create_ai_input_options_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (55,'2025_08_10_200002_create_ai_prompt_templates_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (56,'2025_08_10_200003_create_ai_context_rules_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (57,'2025_08_10_200004_create_ai_module_integrations_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (58,'2025_08_10_200005_create_ai_bulk_operations_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (59,'2025_08_10_200006_create_ai_translation_mappings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (60,'2025_08_10_200007_create_ai_user_preferences_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (61,'2025_08_10_200008_create_ai_usage_analytics_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (62,'2025_08_10_200009_create_ai_prompt_cache_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (63,'2025_08_16_200000_create_ai_credit_transactions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (64,'2025_08_23_000001_create_tenant_resource_limits_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (65,'2025_08_23_000002_create_tenant_usage_logs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (66,'2025_08_23_000003_create_tenant_rate_limits_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (67,'2025_08_25_120001_create_ai_provider_models_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (68,'2025_08_25_120002_create_ai_model_credit_rates_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (69,'2025_08_26_140624_create_pulse_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (70,'2025_09_17_025000_create_ai_content_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (71,'2025_09_26_131240_remove_redundant_ai_columns_from_seo_settings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (72,'2025_09_30_210000_add_operation_rates_to_ai_provider_models',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (73,'2025_10_01_034613_add_ai_analysis_columns_to_seo_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (74,'2025_10_04_000001_create_portfolio_categories_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (75,'2025_10_04_000002_add_category_to_portfolios_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
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
