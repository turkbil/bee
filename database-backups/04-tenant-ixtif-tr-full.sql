mysqldump: Deprecated program name. It will be removed in a future release, use '/usr/bin/mariadb-dump' instead
/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-11.4.8-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: tenant_ixtif_tr
-- ------------------------------------------------------
-- Server version	11.4.8-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

--
-- Table structure for table `activity_log`
--

DROP TABLE IF EXISTS `activity_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity_log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `log_name` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `subject_type` varchar(255) DEFAULT NULL,
  `subject_id` bigint(20) unsigned DEFAULT NULL,
  `causer_type` varchar(255) DEFAULT NULL,
  `causer_id` bigint(20) unsigned DEFAULT NULL,
  `event` varchar(255) DEFAULT NULL,
  `properties` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`properties`)),
  `batch_uuid` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `activity_log_subject_type_subject_id_index` (`subject_type`,`subject_id`),
  KEY `activity_log_causer_type_causer_id_index` (`causer_type`,`causer_id`),
  KEY `activity_log_log_name_index` (`log_name`),
  KEY `activity_log_causer_type_causer_id_created_at_index` (`causer_type`,`causer_id`,`created_at`),
  KEY `activity_log_subject_type_subject_id_created_at_index` (`subject_type`,`subject_id`,`created_at`),
  KEY `activity_log_created_at_index` (`created_at`),
  KEY `activity_log_deleted_at_index` (`deleted_at`),
  KEY `activity_log_event_index` (`event`),
  KEY `activity_log_batch_uuid_index` (`batch_uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_log`
--

LOCK TABLES `activity_log` WRITE;
/*!40000 ALTER TABLE `activity_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `activity_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ai_conversations`
--

DROP TABLE IF EXISTS `ai_conversations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ai_conversations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `session_id` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `tenant_id` bigint(20) unsigned DEFAULT NULL,
  `feature_slug` varchar(255) DEFAULT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'chat',
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `is_demo` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `total_tokens_used` int(11) NOT NULL DEFAULT 0,
  `message_count` int(11) NOT NULL DEFAULT 0,
  `context_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`context_data`)),
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `last_message_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ai_conv_tenant_session_idx` (`tenant_id`,`session_id`),
  KEY `ai_conv_user_created_idx` (`user_id`,`created_at`),
  KEY `ai_conv_feature_created_idx` (`feature_slug`,`created_at`),
  KEY `ai_conversations_session_id_index` (`session_id`),
  KEY `ai_conversations_user_id_index` (`user_id`),
  KEY `ai_conversations_tenant_id_index` (`tenant_id`),
  KEY `ai_conversations_feature_slug_index` (`feature_slug`),
  KEY `ai_conversations_type_index` (`type`),
  KEY `ai_conversations_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ai_conversations`
--

LOCK TABLES `ai_conversations` WRITE;
/*!40000 ALTER TABLE `ai_conversations` DISABLE KEYS */;
/*!40000 ALTER TABLE `ai_conversations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ai_knowledge_base`
--

DROP TABLE IF EXISTS `ai_knowledge_base`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ai_knowledge_base` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint(20) unsigned NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `question` text NOT NULL,
  `answer` text NOT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ai_knowledge_base_tenant_id_is_active_index` (`tenant_id`,`is_active`),
  KEY `ai_knowledge_base_tenant_id_category_index` (`tenant_id`,`category`),
  KEY `ai_knowledge_base_tenant_id_index` (`tenant_id`),
  KEY `ai_knowledge_base_category_index` (`category`),
  KEY `ai_knowledge_base_is_active_index` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ai_knowledge_base`
--

LOCK TABLES `ai_knowledge_base` WRITE;
/*!40000 ALTER TABLE `ai_knowledge_base` DISABLE KEYS */;
/*!40000 ALTER TABLE `ai_knowledge_base` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ai_messages`
--

DROP TABLE IF EXISTS `ai_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ai_messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `conversation_id` bigint(20) unsigned NOT NULL,
  `role` enum('user','assistant','system') NOT NULL,
  `content` text NOT NULL,
  `model` varchar(255) DEFAULT NULL,
  `tokens_used` int(11) NOT NULL DEFAULT 0,
  `prompt_tokens` int(11) NOT NULL DEFAULT 0,
  `completion_tokens` int(11) NOT NULL DEFAULT 0,
  `context_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`context_data`)),
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ai_msg_conv_created_idx` (`conversation_id`,`created_at`),
  KEY `ai_messages_role_index` (`role`),
  CONSTRAINT `ai_messages_conversation_id_foreign` FOREIGN KEY (`conversation_id`) REFERENCES `ai_conversations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ai_messages`
--

LOCK TABLES `ai_messages` WRITE;
/*!40000 ALTER TABLE `ai_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `ai_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `announcements`
--

DROP TABLE IF EXISTS `announcements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `announcements` (
  `announcement_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'Çoklu dil başlık: {"tr": "Başlık", "en": "Title"}' CHECK (json_valid(`title`)),
  `slug` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'Çoklu dil slug: {"tr": "baslik", "en": "title"}' CHECK (json_valid(`slug`)),
  `body` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Çoklu dil içerik: {"tr": "İçerik", "en": "Content"}' CHECK (json_valid(`body`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`announcement_id`),
  KEY `announcements_created_at_index` (`created_at`),
  KEY `announcements_updated_at_index` (`updated_at`),
  KEY `announcements_deleted_at_index` (`deleted_at`),
  KEY `announcements_active_deleted_created_idx` (`is_active`,`deleted_at`,`created_at`),
  KEY `announcements_active_deleted_idx` (`is_active`,`deleted_at`),
  KEY `announcements_is_active_index` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `announcements`
--

LOCK TABLES `announcements` WRITE;
/*!40000 ALTER TABLE `announcements` DISABLE KEYS */;
/*!40000 ALTER TABLE `announcements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blog_categories`
--

DROP TABLE IF EXISTS `blog_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `blog_categories` (
  `category_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `title` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'Çoklu dil başlık: {"tr": "Kategori", "en": "Category"}' CHECK (json_valid(`title`)),
  `slug` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'Çoklu dil slug: {"tr": "kategori", "en": "category"}' CHECK (json_valid(`slug`)),
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Çoklu dil açıklama: {"tr": "Açıklama", "en": "Description"}' CHECK (json_valid(`description`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`category_id`),
  KEY `blog_categories_parent_id_foreign` (`parent_id`),
  KEY `blog_categories_created_at_index` (`created_at`),
  KEY `blog_categories_updated_at_index` (`updated_at`),
  KEY `blog_categories_deleted_at_index` (`deleted_at`),
  KEY `blog_categories_active_deleted_sort_idx` (`is_active`,`deleted_at`,`sort_order`),
  KEY `blog_categories_is_active_index` (`is_active`),
  KEY `blog_categories_sort_order_index` (`sort_order`),
  CONSTRAINT `blog_categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `blog_categories` (`category_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blog_categories`
--

LOCK TABLES `blog_categories` WRITE;
/*!40000 ALTER TABLE `blog_categories` DISABLE KEYS */;
/*!40000 ALTER TABLE `blog_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blogs`
--

DROP TABLE IF EXISTS `blogs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `blogs` (
  `blog_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `blog_category_id` bigint(20) unsigned DEFAULT NULL,
  `title` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'Çoklu dil başlık: {"tr": "Başlık", "en": "Title"}' CHECK (json_valid(`title`)),
  `slug` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'Çoklu dil slug: {"tr": "baslik", "en": "title"}' CHECK (json_valid(`slug`)),
  `body` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Çoklu dil içerik: {"tr": "İçerik", "en": "Content"}' CHECK (json_valid(`body`)),
  `excerpt` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Çoklu dil özet: {"tr": "Özet", "en": "Excerpt"}' CHECK (json_valid(`excerpt`)),
  `published_at` timestamp NULL DEFAULT NULL COMMENT 'Yayınlanma tarihi',
  `is_featured` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Öne çıkan yazı',
  `status` enum('draft','published','scheduled') NOT NULL DEFAULT 'draft' COMMENT 'Yazı durumu',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`blog_id`),
  KEY `blogs_blog_category_id_index` (`blog_category_id`),
  KEY `blogs_created_at_index` (`created_at`),
  KEY `blogs_updated_at_index` (`updated_at`),
  KEY `blogs_deleted_at_index` (`deleted_at`),
  KEY `blogs_active_deleted_created_idx` (`is_active`,`deleted_at`,`created_at`),
  KEY `blogs_active_deleted_idx` (`is_active`,`deleted_at`),
  KEY `blogs_is_active_index` (`is_active`),
  KEY `blogs_published_at_index` (`published_at`),
  KEY `blogs_status_active_published_idx` (`status`,`is_active`,`published_at`),
  KEY `blogs_featured_status_published_idx` (`is_featured`,`status`,`published_at`),
  KEY `blogs_is_featured_index` (`is_featured`),
  KEY `blogs_status_index` (`status`),
  CONSTRAINT `blogs_blog_category_id_foreign` FOREIGN KEY (`blog_category_id`) REFERENCES `blog_categories` (`category_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blogs`
--

LOCK TABLES `blogs` WRITE;
/*!40000 ALTER TABLE `blogs` DISABLE KEYS */;
/*!40000 ALTER TABLE `blogs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `job_batches_name_index` (`name`),
  KEY `job_batches_cancelled_at_index` (`cancelled_at`),
  KEY `job_batches_created_at_index` (`created_at`),
  KEY `job_batches_finished_at_index` (`finished_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `media`
--

DROP TABLE IF EXISTS `media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `media` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) DEFAULT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  `collection_name` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `mime_type` varchar(255) DEFAULT NULL,
  `disk` varchar(255) NOT NULL,
  `conversions_disk` varchar(255) DEFAULT NULL,
  `size` bigint(20) unsigned NOT NULL,
  `manipulations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`manipulations`)),
  `custom_properties` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`custom_properties`)),
  `generated_conversions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`generated_conversions`)),
  `responsive_images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`responsive_images`)),
  `order_column` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `media_uuid_unique` (`uuid`),
  KEY `media_model_type_model_id_index` (`model_type`,`model_id`),
  KEY `media_disk_collection_name_index` (`disk`,`collection_name`),
  KEY `media_created_at_index` (`created_at`),
  KEY `media_collection_name_index` (`collection_name`),
  KEY `media_mime_type_index` (`mime_type`),
  KEY `media_disk_index` (`disk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `media`
--

LOCK TABLES `media` WRITE;
/*!40000 ALTER TABLE `media` DISABLE KEYS */;
/*!40000 ALTER TABLE `media` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu_items`
--

DROP TABLE IF EXISTS `menu_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `menu_items` (
  `item_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `menu_id` bigint(20) unsigned NOT NULL,
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `title` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'Çoklu dil başlık: {"tr": "Anasayfa", "en": "Home"}' CHECK (json_valid(`title`)),
  `url_type` enum('internal','external','module') NOT NULL DEFAULT 'internal',
  `url_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'URL verileri: {"page_id": 1} veya {"url": "https://..."}' CHECK (json_valid(`url_data`)),
  `target` varchar(255) NOT NULL DEFAULT '_self' COMMENT '_self, _blank',
  `icon` varchar(255) DEFAULT NULL COMMENT 'Menü ikonu (FontAwesome class)',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `visibility` varchar(255) NOT NULL DEFAULT 'public' COMMENT 'public, logged_in, guest',
  `sort_order` int(11) NOT NULL DEFAULT 0 COMMENT 'Parent içinde sıralama',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`item_id`),
  KEY `menu_items_menu_id_parent_id_sort_order_index` (`menu_id`,`parent_id`,`sort_order`),
  KEY `menu_items_parent_id_sort_order_index` (`parent_id`,`sort_order`),
  KEY `menu_items_is_active_sort_order_index` (`is_active`,`sort_order`),
  KEY `menu_items_created_at_index` (`created_at`),
  KEY `menu_items_updated_at_index` (`updated_at`),
  KEY `menu_items_is_active_index` (`is_active`),
  CONSTRAINT `menu_items_menu_id_foreign` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`menu_id`) ON DELETE CASCADE,
  CONSTRAINT `menu_items_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `menu_items` (`item_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu_items`
--

LOCK TABLES `menu_items` WRITE;
/*!40000 ALTER TABLE `menu_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `menu_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menus`
--

DROP TABLE IF EXISTS `menus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `menus` (
  `menu_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'Çoklu dil menü adı: {"tr": "Ana Menü", "en": "Main Menu"}' CHECK (json_valid(`name`)),
  `slug` varchar(255) NOT NULL COMMENT 'SEF URL için: header-menu, footer-menu',
  `location` varchar(255) NOT NULL DEFAULT 'header' COMMENT 'Menü konumu: header, footer, sidebar',
  `is_default` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Ana menü koruması için',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Menü ayarları: {"css_class": "navbar", "max_depth": 3}' CHECK (json_valid(`settings`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`menu_id`),
  UNIQUE KEY `menus_slug_unique` (`slug`),
  KEY `menus_created_at_index` (`created_at`),
  KEY `menus_updated_at_index` (`updated_at`),
  KEY `menus_is_active_location_index` (`is_active`,`location`),
  KEY `menus_is_default_is_active_index` (`is_default`,`is_active`),
  KEY `menus_is_active_index` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menus`
--

LOCK TABLES `menus` WRITE;
/*!40000 ALTER TABLE `menus` DISABLE KEYS */;
INSERT INTO `menus` VALUES
(1,'{\"tr\":\"Ana Menü\"}','ana-menu','header',1,1,NULL,'2025-10-13 18:13:42','2025-10-13 18:13:42',NULL);
/*!40000 ALTER TABLE `menus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES
(1,'0001_01_01_000000_create_users_table',1),
(2,'0001_01_01_000001_create_cache_table',1),
(3,'0001_01_01_000002_create_jobs_table',1),
(4,'001_create_shop_customer_groups_table',1),
(5,'002_create_shop_categories_table',1),
(6,'003_create_shop_brands_table',1),
(7,'004_create_shop_attributes_table',1),
(8,'005_create_shop_subscription_plans_table',1),
(9,'006_create_shop_taxes_table',1),
(10,'007_create_shop_payment_methods_table',1),
(11,'008_create_shop_warehouses_table',1),
(12,'009_create_shop_coupons_table',1),
(13,'010_create_shop_campaigns_table',1),
(14,'011_create_shop_settings_table',1),
(15,'012_create_shop_customers_table',1),
(16,'013_create_shop_customer_addresses_table',1),
(17,'014_create_shop_products_table',1),
(18,'015_create_shop_product_variants_table',1),
(19,'016_create_shop_product_attributes_table',1),
(20,'017_create_shop_orders_table',1),
(21,'018_create_shop_order_items_table',1),
(22,'019_create_shop_order_addresses_table',1),
(23,'020_create_shop_inventory_table',1),
(24,'021_create_shop_stock_movements_table',1),
(25,'022_create_shop_subscriptions_table',1),
(26,'023_create_shop_payments_table',1),
(27,'024_create_shop_carts_table',1),
(28,'025_create_shop_cart_items_table',1),
(29,'026_create_shop_tax_rates_table',1),
(30,'027_create_shop_coupon_usages_table',1),
(31,'028_create_shop_reviews_table',1),
(32,'029_add_v2_fields_to_shop_products',1),
(33,'030_add_primary_specs_template_to_shop_categories',1),
(34,'2020_06_21_000002_create_tenant_languages_table',1),
(35,'2024_02_17_000000_create_blog_categories_table',1),
(36,'2024_02_17_000000_create_portfolio_categories_table',1),
(37,'2024_02_17_000001_create_announcements_table',1),
(38,'2024_02_17_000001_create_blogs_table',1),
(39,'2024_02_17_000001_create_pages_table',1),
(40,'2024_02_17_000001_create_portfolios_table',1),
(41,'2024_11_27_203421_tenant_create_activity_log_table',1),
(42,'2024_12_01_000003_create_settings_values_table',1),
(43,'2025_02_14_132615_create_sessions_table',1),
(44,'2025_02_15_192502_create_media_table',1),
(45,'2025_03_15_233856_create_permission_tables',1),
(46,'2025_03_20_000002_create_user_module_permissions_table',1),
(47,'2025_04_01_000001_create_tenant_widgets_table',1),
(48,'2025_04_01_000002_create_widget_items_table',1),
(49,'2025_06_12_000001_create_module_tenant_settings_table',1),
(50,'2025_07_19_000001_create_seo_settings_table',1),
(51,'2025_07_30_000001_create_menus_table',1),
(52,'2025_07_30_000002_create_menu_items_table',1),
(53,'2025_08_23_000001_create_tenant_usage_logs_table',1),
(54,'2025_10_08_022821_add_blog_specific_fields_to_blogs_table',1),
(55,'2025_10_13_005806_create_ai_knowledge_base_table',1),
(56,'2025_10_13_200704_create_shop_product_field_templates_table',1),
(57,'2025_10_15_000001_remove_reading_time_from_blogs_table',1),
(58,'2025_10_15_000002_backfill_blog_status_columns',1),
(59,'2025_10_20_100000_create_tags_tables',1),
(60,'2025_10_14_000000_create_shop_product_chat_placeholders_table',2);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_permissions`
--

LOCK TABLES `model_has_permissions` WRITE;
/*!40000 ALTER TABLE `model_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `model_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_roles`
--

LOCK TABLES `model_has_roles` WRITE;
/*!40000 ALTER TABLE `model_has_roles` DISABLE KEYS */;
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `module_tenant_settings`
--

DROP TABLE IF EXISTS `module_tenant_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `module_tenant_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `module_name` varchar(255) NOT NULL,
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`settings`)),
  `title` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`title`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `module_tenant_settings_module_name_unique` (`module_name`),
  KEY `module_tenant_settings_module_name_index` (`module_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `module_tenant_settings`
--

LOCK TABLES `module_tenant_settings` WRITE;
/*!40000 ALTER TABLE `module_tenant_settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `module_tenant_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pages`
--

DROP TABLE IF EXISTS `pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pages` (
  `page_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'Çoklu dil başlık: {"tr": "Başlık", "en": "Title"}' CHECK (json_valid(`title`)),
  `slug` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'Çoklu dil slug: {"tr": "baslik", "en": "title"}' CHECK (json_valid(`slug`)),
  `body` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Çoklu dil içerik: {"tr": "İçerik", "en": "Content"}' CHECK (json_valid(`body`)),
  `css` text DEFAULT NULL,
  `js` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_homepage` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`page_id`),
  KEY `pages_created_at_index` (`created_at`),
  KEY `pages_updated_at_index` (`updated_at`),
  KEY `pages_deleted_at_index` (`deleted_at`),
  KEY `pages_homepage_active_deleted_idx` (`is_homepage`,`is_active`,`deleted_at`),
  KEY `pages_homepage_deleted_active_idx` (`is_homepage`,`deleted_at`,`is_active`),
  KEY `pages_active_deleted_created_idx` (`is_active`,`deleted_at`,`created_at`),
  KEY `pages_active_deleted_idx` (`is_active`,`deleted_at`),
  KEY `pages_is_active_index` (`is_active`),
  KEY `pages_is_homepage_index` (`is_homepage`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pages`
--

LOCK TABLES `pages` WRITE;
/*!40000 ALTER TABLE `pages` DISABLE KEYS */;
INSERT INTO `pages` VALUES
(1,'{\"tr\":\"Anasayfa\"}','{\"tr\":\"anasayfa\"}','{\"tr\":\"<h1>Anasayfa<\\/h1><p>Ho\\u015f geldiniz.<\\/p>\"}',NULL,NULL,1,1,'2025-10-13 22:33:49','2025-10-13 22:33:49',NULL),
(2,'{\"tr\":\"\\u0130leti\\u015fim\"}','{\"tr\":\"iletisim\"}','{\"tr\":\"<h1>\\u0130leti\\u015fim<\\/h1><p>Bizimle ileti\\u015fime ge\\u00e7in.<\\/p>\"}',NULL,NULL,1,0,'2025-10-13 22:33:49','2025-10-13 22:33:49',NULL),
(3,'{\"tr\":\"Hakk\\u0131m\\u0131zda\"}','{\"tr\":\"hakkimizda\"}','{\"tr\":\"<h1>Hakk\\u0131m\\u0131zda<\\/h1><p>Biz kimiz?<\\/p>\"}',NULL,NULL,1,0,'2025-10-13 22:33:49','2025-10-13 22:33:49',NULL);
/*!40000 ALTER TABLE `pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`),
  KEY `password_reset_tokens_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`),
  KEY `permissions_created_at_index` (`created_at`),
  KEY `permissions_updated_at_index` (`updated_at`),
  KEY `permissions_name_index` (`name`),
  KEY `permissions_guard_name_index` (`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `portfolio_categories`
--

DROP TABLE IF EXISTS `portfolio_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `portfolio_categories` (
  `category_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `title` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'Çoklu dil başlık: {"tr": "Kategori", "en": "Category"}' CHECK (json_valid(`title`)),
  `slug` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'Çoklu dil slug: {"tr": "kategori", "en": "category"}' CHECK (json_valid(`slug`)),
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Çoklu dil açıklama: {"tr": "Açıklama", "en": "Description"}' CHECK (json_valid(`description`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`category_id`),
  KEY `portfolio_categories_parent_id_foreign` (`parent_id`),
  KEY `portfolio_categories_created_at_index` (`created_at`),
  KEY `portfolio_categories_updated_at_index` (`updated_at`),
  KEY `portfolio_categories_deleted_at_index` (`deleted_at`),
  KEY `portfolio_categories_active_deleted_sort_idx` (`is_active`,`deleted_at`,`sort_order`),
  KEY `portfolio_categories_is_active_index` (`is_active`),
  KEY `portfolio_categories_sort_order_index` (`sort_order`),
  CONSTRAINT `portfolio_categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `portfolio_categories` (`category_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `portfolio_categories`
--

LOCK TABLES `portfolio_categories` WRITE;
/*!40000 ALTER TABLE `portfolio_categories` DISABLE KEYS */;
/*!40000 ALTER TABLE `portfolio_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `portfolios`
--

DROP TABLE IF EXISTS `portfolios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `portfolios` (
  `portfolio_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `portfolio_category_id` bigint(20) unsigned DEFAULT NULL,
  `title` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'Çoklu dil başlık: {"tr": "Başlık", "en": "Title"}' CHECK (json_valid(`title`)),
  `slug` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'Çoklu dil slug: {"tr": "baslik", "en": "title"}' CHECK (json_valid(`slug`)),
  `body` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Çoklu dil içerik: {"tr": "İçerik", "en": "Content"}' CHECK (json_valid(`body`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`portfolio_id`),
  KEY `portfolios_portfolio_category_id_index` (`portfolio_category_id`),
  KEY `portfolios_created_at_index` (`created_at`),
  KEY `portfolios_updated_at_index` (`updated_at`),
  KEY `portfolios_deleted_at_index` (`deleted_at`),
  KEY `portfolios_active_deleted_created_idx` (`is_active`,`deleted_at`,`created_at`),
  KEY `portfolios_active_deleted_idx` (`is_active`,`deleted_at`),
  KEY `portfolios_is_active_index` (`is_active`),
  CONSTRAINT `portfolios_portfolio_category_id_foreign` FOREIGN KEY (`portfolio_category_id`) REFERENCES `portfolio_categories` (`category_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `portfolios`
--

LOCK TABLES `portfolios` WRITE;
/*!40000 ALTER TABLE `portfolios` DISABLE KEYS */;
/*!40000 ALTER TABLE `portfolios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_has_permissions`
--

LOCK TABLES `role_has_permissions` WRITE;
/*!40000 ALTER TABLE `role_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `role_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `role_type` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `guard_name` varchar(255) NOT NULL,
  `is_protected` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`),
  KEY `roles_created_at_index` (`created_at`),
  KEY `roles_updated_at_index` (`updated_at`),
  KEY `roles_name_index` (`name`),
  KEY `roles_role_type_index` (`role_type`),
  KEY `roles_guard_name_index` (`guard_name`),
  KEY `roles_is_protected_index` (`is_protected`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES
(1,'root','root','Tam yetkili tenant yöneticisi','web',1,'2025-10-14 00:33:19','2025-10-14 00:33:19'),
(2,'admin','admin','Tenant yöneticisi','web',1,'2025-10-14 00:33:19','2025-10-14 00:33:19'),
(3,'editor','editor','İçerik editörü','web',1,'2025-10-14 00:33:19','2025-10-14 00:33:19');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `seo_settings`
--

DROP TABLE IF EXISTS `seo_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `seo_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `seoable_type` varchar(255) NOT NULL,
  `seoable_id` bigint(20) unsigned NOT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `titles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`titles`)),
  `descriptions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`descriptions`)),
  `keywords` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`keywords`)),
  `canonical_url` varchar(255) DEFAULT NULL,
  `author` varchar(255) DEFAULT NULL,
  `author_url` varchar(255) DEFAULT NULL,
  `copyright` varchar(255) DEFAULT NULL,
  `og_titles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`og_titles`)),
  `og_descriptions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`og_descriptions`)),
  `og_images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`og_images`)),
  `og_image` varchar(255) DEFAULT NULL,
  `og_type` varchar(255) NOT NULL DEFAULT 'website',
  `og_locale` varchar(255) DEFAULT NULL,
  `og_site_name` varchar(255) DEFAULT NULL,
  `twitter_card` varchar(255) NOT NULL DEFAULT 'summary',
  `twitter_title` varchar(255) DEFAULT NULL,
  `twitter_description` text DEFAULT NULL,
  `twitter_image` varchar(255) DEFAULT NULL,
  `robots_meta` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`robots_meta`)),
  `schema_markup` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`schema_markup`)),
  `schema_type` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Schema.org page types per language - {"tr": "Article", "en": "BlogPosting"}' CHECK (json_valid(`schema_type`)),
  `focus_keywords` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`focus_keywords`)),
  `additional_keywords` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`additional_keywords`)),
  `hreflang_urls` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`hreflang_urls`)),
  `allow_gptbot` tinyint(1) NOT NULL DEFAULT 1,
  `allow_claudebot` tinyint(1) NOT NULL DEFAULT 1,
  `allow_google_extended` tinyint(1) NOT NULL DEFAULT 1,
  `allow_bingbot_ai` tinyint(1) NOT NULL DEFAULT 1,
  `seo_score` int(11) NOT NULL DEFAULT 0,
  `seo_analysis` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`seo_analysis`)),
  `last_analyzed` timestamp NULL DEFAULT NULL,
  `content_length` int(11) NOT NULL DEFAULT 0,
  `keyword_density` int(11) NOT NULL DEFAULT 0,
  `readability_score` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`readability_score`)),
  `page_speed_insights` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`page_speed_insights`)),
  `last_crawled` timestamp NULL DEFAULT NULL,
  `analysis_results` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`analysis_results`)),
  `analysis_date` timestamp NULL DEFAULT NULL,
  `strengths` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`strengths`)),
  `improvements` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`improvements`)),
  `action_items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`action_items`)),
  `ai_suggestions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`ai_suggestions`)),
  `auto_optimize` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('active','inactive','pending') NOT NULL DEFAULT 'active',
  `priority_score` int(11) NOT NULL DEFAULT 5,
  `available_languages` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`available_languages`)),
  `default_language` varchar(255) DEFAULT NULL,
  `language_fallbacks` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`language_fallbacks`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `seo_settings_seoable_type_seoable_id_index` (`seoable_type`,`seoable_id`),
  KEY `seo_settings_seoable_id_seoable_type_index` (`seoable_id`,`seoable_type`),
  KEY `seo_settings_status_index` (`status`),
  KEY `seo_settings_seo_score_index` (`seo_score`),
  KEY `seo_settings_last_analyzed_index` (`last_analyzed`),
  KEY `seo_settings_analysis_date_index` (`analysis_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `seo_settings`
--

LOCK TABLES `seo_settings` WRITE;
/*!40000 ALTER TABLE `seo_settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `seo_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings_values`
--

DROP TABLE IF EXISTS `settings_values`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings_values` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `setting_id` bigint(20) unsigned NOT NULL,
  `value` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_values_setting_id_unique` (`setting_id`),
  KEY `settings_values_created_at_index` (`created_at`),
  KEY `settings_values_updated_at_index` (`updated_at`),
  KEY `settings_values_setting_id_index` (`setting_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings_values`
--

LOCK TABLES `settings_values` WRITE;
/*!40000 ALTER TABLE `settings_values` DISABLE KEYS */;
/*!40000 ALTER TABLE `settings_values` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_attributes`
--

DROP TABLE IF EXISTS `shop_attributes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `shop_attributes` (
  `attribute_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'Özellik adı: {"tr": "Özellik Adı", "en": "Attribute Name", "vs.": "..."}' CHECK (json_valid(`title`)),
  `slug` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'Çoklu dil slug: {"tr": "ozellik-adi", "en": "attribute-name", "vs.": "..."}' CHECK (json_valid(`slug`)),
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Özellik açıklaması: {"tr": "Açıklama", "en": "Description", "vs.": "..."}' CHECK (json_valid(`description`)),
  `type` enum('text','select','multiselect','boolean','number','range','color') NOT NULL DEFAULT 'text' COMMENT 'Özellik tipi: text=Metin, select=Seçim, multiselect=Çoklu seçim, boolean=Evet/Hayır, number=Sayı, range=Aralık, color=Renk',
  `options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Seçenek değerleri (select için): [{"value":"value1","label":{"tr":"Etiket","en":"Label","vs.":"..."}}, ...]' CHECK (json_valid(`options`)),
  `unit` varchar(255) DEFAULT NULL COMMENT 'Birim (kg, mm, kW, vb)',
  `is_filterable` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Filtrelemede kullanılsın mı?',
  `is_searchable` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Aramada kullanılsın mı?',
  `is_comparable` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Karşılaştırmada gösterilsin mi?',
  `is_visible` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Ürün detayında gösterilsin mi?',
  `is_required` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Zorunlu mu?',
  `validation_rules` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Validasyon kuralları: {"min":0,"max":10000,"vs.":"..."}' CHECK (json_valid(`validation_rules`)),
  `sort_order` int(11) NOT NULL DEFAULT 0 COMMENT 'Sıralama düzeni',
  `icon_class` varchar(255) DEFAULT NULL COMMENT 'İkon sınıfı',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`attribute_id`),
  KEY `shop_attributes_created_at_index` (`created_at`),
  KEY `shop_attributes_updated_at_index` (`updated_at`),
  KEY `shop_attributes_deleted_at_index` (`deleted_at`),
  KEY `shop_attributes_type_index` (`type`),
  KEY `shop_attributes_is_filterable_index` (`is_filterable`),
  KEY `shop_attributes_is_searchable_index` (`is_searchable`),
  KEY `shop_attributes_is_visible_index` (`is_visible`),
  KEY `shop_attributes_sort_order_index` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_attributes`
--

LOCK TABLES `shop_attributes` WRITE;
/*!40000 ALTER TABLE `shop_attributes` DISABLE KEYS */;
/*!40000 ALTER TABLE `shop_attributes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_brands`
--

DROP TABLE IF EXISTS `shop_brands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `shop_brands` (
  `brand_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'Marka başlığı: {"tr": "Marka Adı", "en": "Brand Name", "vs.": "..."}' CHECK (json_valid(`title`)),
  `slug` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'Çoklu dil slug: {"tr": "marka-adi", "en": "brand-name", "vs.": "..."}' CHECK (json_valid(`slug`)),
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Marka açıklaması: {"tr": "Açıklama metni", "en": "Description text", "vs.": "..."}' CHECK (json_valid(`description`)),
  `logo_url` varchar(255) DEFAULT NULL COMMENT 'Marka logosu URL',
  `website_url` varchar(255) DEFAULT NULL COMMENT 'Resmi website URL',
  `country_code` varchar(2) DEFAULT NULL COMMENT 'Ülke kodu (ISO 3166-1 alpha-2: TR, US, DE, vs.)',
  `founded_year` int(11) DEFAULT NULL COMMENT 'Kuruluş yılı',
  `headquarters` varchar(255) DEFAULT NULL COMMENT 'Merkez ofis lokasyonu',
  `certifications` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Sertifikalar: [{"name":"CE","year":2005}, {"name":"ISO 9001","year":2010}, ...]' CHECK (json_valid(`certifications`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Aktif/Pasif durumu',
  `is_featured` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Öne çıkan marka',
  `sort_order` int(11) NOT NULL DEFAULT 0 COMMENT 'Sıralama düzeni',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`brand_id`),
  KEY `shop_brands_country_code_index` (`country_code`),
  KEY `shop_brands_created_at_index` (`created_at`),
  KEY `shop_brands_updated_at_index` (`updated_at`),
  KEY `shop_brands_deleted_at_index` (`deleted_at`),
  KEY `shop_brands_active_deleted_sort_idx` (`is_active`,`deleted_at`,`sort_order`),
  KEY `shop_brands_featured_active_idx` (`is_featured`,`is_active`),
  KEY `shop_brands_is_active_index` (`is_active`),
  KEY `shop_brands_is_featured_index` (`is_featured`),
  KEY `shop_brands_sort_order_index` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_brands`
--

LOCK TABLES `shop_brands` WRITE;
/*!40000 ALTER TABLE `shop_brands` DISABLE KEYS */;
/*!40000 ALTER TABLE `shop_brands` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_campaigns`
--

DROP TABLE IF EXISTS `shop_campaigns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `shop_campaigns` (
  `campaign_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'Kampanya adı (JSON çoklu dil)' CHECK (json_valid(`title`)),
  `slug` varchar(255) NOT NULL COMMENT 'URL-dostu slug',
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Açıklama (JSON çoklu dil)' CHECK (json_valid(`description`)),
  `campaign_type` enum('discount','bogo','bundle','gift','flash_sale','clearance','seasonal') NOT NULL DEFAULT 'discount' COMMENT 'Kampanya tipi',
  `discount_percentage` decimal(5,2) DEFAULT NULL COMMENT 'İndirim yüzdesi (%)',
  `discount_amount` decimal(12,2) DEFAULT NULL COMMENT 'İndirim tutarı (₺)',
  `applies_to` enum('all','categories','products','brands') NOT NULL DEFAULT 'all' COMMENT 'Nerelere uygulanır',
  `category_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Kategori ID''leri (JSON array)' CHECK (json_valid(`category_ids`)),
  `product_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Ürün ID''leri (JSON array)' CHECK (json_valid(`product_ids`)),
  `brand_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Marka ID''leri (JSON array)' CHECK (json_valid(`brand_ids`)),
  `minimum_order_amount` decimal(12,2) DEFAULT NULL COMMENT 'Minimum sipariş tutarı (₺)',
  `minimum_items` int(11) DEFAULT NULL COMMENT 'Minimum ürün adedi',
  `start_date` timestamp NULL DEFAULT NULL COMMENT 'Başlangıç tarihi',
  `end_date` timestamp NULL DEFAULT NULL COMMENT 'Bitiş tarihi',
  `usage_limit_total` int(11) DEFAULT NULL COMMENT 'Toplam kullanım limiti',
  `usage_limit_per_customer` int(11) DEFAULT NULL COMMENT 'Müşteri başına limit',
  `used_count` int(11) NOT NULL DEFAULT 0 COMMENT 'Kullanım sayısı',
  `badge_text` varchar(255) DEFAULT NULL COMMENT 'Rozet metni (Kampanya, %50 İndirim)',
  `badge_color` varchar(7) DEFAULT NULL COMMENT 'Rozet rengi (#FF5733)',
  `banner_image` varchar(255) DEFAULT NULL COMMENT 'Banner görseli',
  `priority` int(11) NOT NULL DEFAULT 0 COMMENT 'Öncelik (yüksek değer önce uygulanır)',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Aktif/Pasif durumu',
  `is_featured` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Öne çıkan kampanya mı?',
  `view_count` int(11) NOT NULL DEFAULT 0 COMMENT 'Görüntülenme sayısı',
  `total_sales` decimal(14,2) NOT NULL DEFAULT 0.00 COMMENT 'Toplam satış (₺)',
  `terms` text DEFAULT NULL COMMENT 'Kullanım şartları',
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Ek veriler (JSON)' CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Soft delete için silinme tarihi',
  PRIMARY KEY (`campaign_id`),
  UNIQUE KEY `shop_campaigns_slug_unique` (`slug`),
  KEY `shop_campaigns_created_at_index` (`created_at`),
  KEY `shop_campaigns_updated_at_index` (`updated_at`),
  KEY `shop_campaigns_deleted_at_index` (`deleted_at`),
  KEY `shop_campaigns_slug_index` (`slug`),
  KEY `shop_campaigns_campaign_type_index` (`campaign_type`),
  KEY `shop_campaigns_is_active_index` (`is_active`),
  KEY `shop_campaigns_is_featured_index` (`is_featured`),
  KEY `shop_campaigns_priority_index` (`priority`),
  KEY `shop_campaigns_start_date_end_date_index` (`start_date`,`end_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Kampanyalar - İndirim kampanyaları, flaş satışlar, sezonluk kampanyalar';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_campaigns`
--

LOCK TABLES `shop_campaigns` WRITE;
/*!40000 ALTER TABLE `shop_campaigns` DISABLE KEYS */;
/*!40000 ALTER TABLE `shop_campaigns` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_cart_items`
--

DROP TABLE IF EXISTS `shop_cart_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `shop_cart_items` (
  `cart_item_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cart_id` bigint(20) unsigned NOT NULL COMMENT 'Sepet ID - shop_carts ilişkisi',
  `product_id` bigint(20) unsigned NOT NULL COMMENT 'Ürün ID - shop_products ilişkisi',
  `product_variant_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Varyant ID - shop_product_variants ilişkisi',
  `quantity` int(11) NOT NULL DEFAULT 1 COMMENT 'Adet',
  `unit_price` decimal(12,2) NOT NULL COMMENT 'Birim fiyat (₺)',
  `discount_amount` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Birim başına indirim (₺)',
  `final_price` decimal(12,2) NOT NULL COMMENT 'İndirimli birim fiyat (₺)',
  `subtotal` decimal(12,2) NOT NULL COMMENT 'Ara toplam (₺) - final_price * quantity',
  `tax_amount` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Vergi tutarı (₺)',
  `tax_rate` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT 'Vergi oranı (%)',
  `total` decimal(12,2) NOT NULL COMMENT 'Satır toplamı (₺) - subtotal + tax_amount',
  `customization_options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Özelleştirme seçenekleri (JSON)' CHECK (json_valid(`customization_options`)),
  `special_instructions` text DEFAULT NULL COMMENT 'Özel talimatlar',
  `in_stock` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Stokta var mı? (sepete ekleme anında)',
  `stock_checked_at` timestamp NULL DEFAULT NULL COMMENT 'Son stok kontrol tarihi',
  `moved_from_wishlist` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Favorilerden mi eklendi?',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`cart_item_id`),
  KEY `shop_cart_items_created_at_index` (`created_at`),
  KEY `shop_cart_items_updated_at_index` (`updated_at`),
  KEY `shop_cart_items_cart_id_index` (`cart_id`),
  KEY `shop_cart_items_product_id_index` (`product_id`),
  KEY `shop_cart_items_product_variant_id_index` (`product_variant_id`),
  KEY `shop_cart_items_cart_id_product_id_index` (`cart_id`,`product_id`),
  CONSTRAINT `shop_cart_items_cart_id_foreign` FOREIGN KEY (`cart_id`) REFERENCES `shop_carts` (`cart_id`) ON DELETE CASCADE,
  CONSTRAINT `shop_cart_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `shop_products` (`product_id`) ON DELETE CASCADE,
  CONSTRAINT `shop_cart_items_product_variant_id_foreign` FOREIGN KEY (`product_variant_id`) REFERENCES `shop_product_variants` (`variant_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Sepet ürünleri - Sepetteki her bir ürün';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_cart_items`
--

LOCK TABLES `shop_cart_items` WRITE;
/*!40000 ALTER TABLE `shop_cart_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `shop_cart_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_carts`
--

DROP TABLE IF EXISTS `shop_carts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `shop_carts` (
  `cart_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Müşteri ID - shop_customers ilişkisi (null ise misafir)',
  `session_id` varchar(255) DEFAULT NULL COMMENT 'Oturum ID (misafir kullanıcılar için)',
  `device_id` varchar(255) DEFAULT NULL COMMENT 'Cihaz ID (cross-device tracking için)',
  `status` enum('active','abandoned','converted','merged') NOT NULL DEFAULT 'active' COMMENT 'Sepet durumu: active=Aktif, abandoned=Terk edilmiş, converted=Siparişe dönüştürüldü, merged=Birleştirilmiş',
  `items_count` int(11) NOT NULL DEFAULT 0 COMMENT 'Toplam ürün sayısı (adet)',
  `subtotal` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Ara toplam (₺)',
  `discount_amount` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT 'İndirim tutarı (₺)',
  `tax_amount` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Vergi tutarı (₺)',
  `shipping_cost` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Kargo ücreti (₺)',
  `total` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Toplam tutar (₺)',
  `coupon_code` varchar(255) DEFAULT NULL COMMENT 'Uygulanan kupon kodu',
  `coupon_discount` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Kupon indirimi (₺)',
  `converted_to_order_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Dönüştürülen sipariş ID',
  `converted_at` timestamp NULL DEFAULT NULL COMMENT 'Siparişe dönüşme tarihi',
  `abandoned_at` timestamp NULL DEFAULT NULL COMMENT 'Terk edilme tarihi (son aktiviteden 24 saat sonra)',
  `recovery_token` varchar(255) DEFAULT NULL COMMENT 'Kurtarma token (e-posta linkinde kullanılır)',
  `recovery_email_sent_at` timestamp NULL DEFAULT NULL COMMENT 'Kurtarma e-postası gönderilme tarihi',
  `recovery_email_count` int(11) NOT NULL DEFAULT 0 COMMENT 'Kaç kez kurtarma e-postası gönderildi',
  `ip_address` varchar(45) DEFAULT NULL COMMENT 'IP adresi',
  `user_agent` text DEFAULT NULL COMMENT 'Tarayıcı bilgisi',
  `currency` varchar(3) NOT NULL DEFAULT 'TRY' COMMENT 'Para birimi (TRY, USD, EUR)',
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Ek veriler (JSON - utm params, referrer, vb)' CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `last_activity_at` timestamp NULL DEFAULT NULL COMMENT 'Son aktivite tarihi',
  PRIMARY KEY (`cart_id`),
  UNIQUE KEY `shop_carts_recovery_token_unique` (`recovery_token`),
  KEY `shop_carts_created_at_index` (`created_at`),
  KEY `shop_carts_updated_at_index` (`updated_at`),
  KEY `shop_carts_customer_id_index` (`customer_id`),
  KEY `shop_carts_session_id_index` (`session_id`),
  KEY `shop_carts_status_index` (`status`),
  KEY `shop_carts_converted_to_order_id_index` (`converted_to_order_id`),
  KEY `shop_carts_recovery_token_index` (`recovery_token`),
  KEY `shop_carts_abandoned_at_index` (`abandoned_at`),
  KEY `shop_carts_last_activity_at_index` (`last_activity_at`),
  CONSTRAINT `shop_carts_converted_to_order_id_foreign` FOREIGN KEY (`converted_to_order_id`) REFERENCES `shop_orders` (`order_id`) ON DELETE CASCADE,
  CONSTRAINT `shop_carts_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `shop_customers` (`customer_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Alışveriş sepetleri - Aktif ve terk edilmiş sepetler';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_carts`
--

LOCK TABLES `shop_carts` WRITE;
/*!40000 ALTER TABLE `shop_carts` DISABLE KEYS */;
/*!40000 ALTER TABLE `shop_carts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_categories`
--

DROP TABLE IF EXISTS `shop_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `shop_categories` (
  `category_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Üst kategori ID (null ise ana kategori)',
  `title` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'Kategori başlığı: {"tr": "Elektronik", "en": "Electronics", "vs.": "..."}' CHECK (json_valid(`title`)),
  `slug` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'Çoklu dil slug: {"tr": "elektronik", "en": "electronics", "vs.": "..."}' CHECK (json_valid(`slug`)),
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Kategori açıklaması: {"tr": "Açıklama metni", "en": "Description text", "vs.": "..."}' CHECK (json_valid(`description`)),
  `primary_specs_template` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Kategori bazlı sabit 4 kart yapısı: {"card_1":{"label":"...","field_path":"...","icon":"...","format":"..."}, ...}' CHECK (json_valid(`primary_specs_template`)),
  `image_url` varchar(255) DEFAULT NULL COMMENT 'Kategori görseli URL',
  `icon_class` varchar(255) DEFAULT NULL COMMENT 'İkon sınıfı (fa-laptop, bi-phone, vb)',
  `level` int(11) NOT NULL DEFAULT 1 COMMENT 'Seviye (1=Ana, 2=Alt, 3=Alt-Alt)',
  `path` varchar(255) DEFAULT NULL COMMENT 'Hiyerarşik yol (1.2.5 formatında)',
  `sort_order` int(11) NOT NULL DEFAULT 0 COMMENT 'Sıralama düzeni',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Aktif/Pasif durumu',
  `show_in_menu` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Menüde göster',
  `show_in_homepage` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Anasayfada göster',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`category_id`),
  KEY `shop_categories_level_index` (`level`),
  KEY `shop_categories_path_index` (`path`),
  KEY `shop_categories_created_at_index` (`created_at`),
  KEY `shop_categories_updated_at_index` (`updated_at`),
  KEY `shop_categories_deleted_at_index` (`deleted_at`),
  KEY `shop_categories_active_deleted_sort_idx` (`is_active`,`deleted_at`,`sort_order`),
  KEY `shop_categories_parent_active_idx` (`parent_id`,`is_active`),
  KEY `shop_categories_sort_order_index` (`sort_order`),
  KEY `shop_categories_is_active_index` (`is_active`),
  CONSTRAINT `shop_categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `shop_categories` (`category_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_categories`
--

LOCK TABLES `shop_categories` WRITE;
/*!40000 ALTER TABLE `shop_categories` DISABLE KEYS */;
/*!40000 ALTER TABLE `shop_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_coupon_usages`
--

DROP TABLE IF EXISTS `shop_coupon_usages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `shop_coupon_usages` (
  `coupon_usage_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `coupon_id` bigint(20) unsigned NOT NULL COMMENT 'Kupon ID - shop_coupons ilişkisi',
  `order_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Sipariş ID - shop_orders ilişkisi',
  `customer_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Müşteri ID - shop_customers ilişkisi',
  `coupon_code` varchar(255) NOT NULL COMMENT 'Kullanılan kupon kodu (snapshot)',
  `discount_amount` decimal(12,2) NOT NULL COMMENT 'İndirim tutarı (₺)',
  `order_amount` decimal(14,2) DEFAULT NULL COMMENT 'Sipariş tutarı (₺)',
  `status` enum('applied','used','refunded','cancelled') NOT NULL DEFAULT 'applied' COMMENT 'Kullanım durumu: applied=Uygulandı (henüz sipariş tamamlanmadı), used=Kullanıldı, refunded=İade edildi, cancelled=İptal edildi',
  `used_at` timestamp NULL DEFAULT NULL COMMENT 'Kullanım tarihi',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`coupon_usage_id`),
  KEY `shop_coupon_usages_created_at_index` (`created_at`),
  KEY `shop_coupon_usages_updated_at_index` (`updated_at`),
  KEY `shop_coupon_usages_coupon_id_index` (`coupon_id`),
  KEY `shop_coupon_usages_order_id_index` (`order_id`),
  KEY `shop_coupon_usages_customer_id_index` (`customer_id`),
  KEY `shop_coupon_usages_coupon_code_index` (`coupon_code`),
  KEY `shop_coupon_usages_status_index` (`status`),
  KEY `shop_coupon_usages_used_at_index` (`used_at`),
  KEY `shop_coupon_usages_customer_id_coupon_id_index` (`customer_id`,`coupon_id`),
  CONSTRAINT `shop_coupon_usages_coupon_id_foreign` FOREIGN KEY (`coupon_id`) REFERENCES `shop_coupons` (`coupon_id`) ON DELETE CASCADE,
  CONSTRAINT `shop_coupon_usages_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `shop_customers` (`customer_id`) ON DELETE CASCADE,
  CONSTRAINT `shop_coupon_usages_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `shop_orders` (`order_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Kupon kullanımları - Kuponların kullanım geçmişi ve limitleri';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_coupon_usages`
--

LOCK TABLES `shop_coupon_usages` WRITE;
/*!40000 ALTER TABLE `shop_coupon_usages` DISABLE KEYS */;
/*!40000 ALTER TABLE `shop_coupon_usages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_coupons`
--

DROP TABLE IF EXISTS `shop_coupons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `shop_coupons` (
  `coupon_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'Kupon adı ({"tr":"Yaz İndirimi","en":"Summer Sale"})' CHECK (json_valid(`title`)),
  `code` varchar(255) NOT NULL COMMENT 'Kupon kodu (SUMMER2024)',
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Açıklama (JSON çoklu dil)' CHECK (json_valid(`description`)),
  `coupon_type` enum('percentage','fixed_amount','free_shipping','buy_x_get_y') NOT NULL DEFAULT 'percentage' COMMENT 'Kupon tipi: percentage=Yüzde indirim, fixed_amount=Sabit tutar, free_shipping=Ücretsiz kargo, buy_x_get_y=X al Y öde',
  `discount_percentage` decimal(5,2) DEFAULT NULL COMMENT 'İndirim yüzdesi (%) - percentage tipinde',
  `discount_amount` decimal(12,2) DEFAULT NULL COMMENT 'İndirim tutarı (₺) - fixed_amount tipinde',
  `max_discount_amount` decimal(12,2) DEFAULT NULL COMMENT 'Maksimum indirim tutarı (₺) - percentage için',
  `buy_quantity` int(11) DEFAULT NULL COMMENT 'Alınması gereken miktar (X)',
  `get_quantity` int(11) DEFAULT NULL COMMENT 'Hediye miktar (Y)',
  `applicable_product_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Geçerli ürün ID''leri (JSON array)' CHECK (json_valid(`applicable_product_ids`)),
  `usage_limit_total` int(11) DEFAULT NULL COMMENT 'Toplam kullanım limiti (null ise sınırsız)',
  `usage_limit_per_customer` int(11) NOT NULL DEFAULT 1 COMMENT 'Müşteri başına kullanım limiti',
  `used_count` int(11) NOT NULL DEFAULT 0 COMMENT 'Kullanım sayısı',
  `minimum_order_amount` decimal(12,2) DEFAULT NULL COMMENT 'Minimum sipariş tutarı (₺)',
  `maximum_order_amount` decimal(14,2) DEFAULT NULL COMMENT 'Maximum sipariş tutarı (₺)',
  `minimum_items` int(11) DEFAULT NULL COMMENT 'Minimum ürün adedi',
  `applies_to` enum('all','categories','products','brands') NOT NULL DEFAULT 'all' COMMENT 'Nerelerde geçerli: all=Tüm ürünler, categories=Kategoriler, products=Belirli ürünler, brands=Markalar',
  `category_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Kategori ID''leri (JSON array)' CHECK (json_valid(`category_ids`)),
  `product_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Ürün ID''leri (JSON array)' CHECK (json_valid(`product_ids`)),
  `brand_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Marka ID''leri (JSON array)' CHECK (json_valid(`brand_ids`)),
  `excluded_category_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Hariç tutulan kategori ID''leri (JSON array)' CHECK (json_valid(`excluded_category_ids`)),
  `excluded_product_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Hariç tutulan ürün ID''leri (JSON array)' CHECK (json_valid(`excluded_product_ids`)),
  `customer_eligibility` enum('all','specific_groups','specific_customers','new_customers','returning_customers') NOT NULL DEFAULT 'all' COMMENT 'Müşteri yeterliliği: all=Herkes, specific_groups=Belirli gruplar, specific_customers=Belirli müşteriler, new_customers=Yeni müşteriler, returning_customers=Eski müşteriler',
  `customer_group_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Müşteri grubu ID''leri (JSON array)' CHECK (json_valid(`customer_group_ids`)),
  `customer_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Müşteri ID''leri (JSON array)' CHECK (json_valid(`customer_ids`)),
  `valid_from` timestamp NULL DEFAULT NULL COMMENT 'Geçerlilik başlangıç tarihi',
  `valid_until` timestamp NULL DEFAULT NULL COMMENT 'Geçerlilik bitiş tarihi',
  `can_combine_with_other_coupons` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Diğer kuponlarla birlikte kullanılabilir mi?',
  `can_combine_with_sales` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'İndirimli ürünlerde kullanılabilir mi?',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Aktif/Pasif durumu',
  `is_public` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Herkese açık mı? (false ise sadece link ile)',
  `banner_text` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Banner metni (JSON çoklu dil)' CHECK (json_valid(`banner_text`)),
  `banner_color` varchar(255) DEFAULT NULL COMMENT 'Banner rengi (#FF5733)',
  `terms` text DEFAULT NULL COMMENT 'Kullanım şartları',
  `notes` text DEFAULT NULL COMMENT 'Admin notları',
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Ek veriler (JSON)' CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Soft delete için silinme tarihi',
  PRIMARY KEY (`coupon_id`),
  UNIQUE KEY `shop_coupons_code_unique` (`code`),
  KEY `shop_coupons_created_at_index` (`created_at`),
  KEY `shop_coupons_updated_at_index` (`updated_at`),
  KEY `shop_coupons_deleted_at_index` (`deleted_at`),
  KEY `shop_coupons_code_index` (`code`),
  KEY `shop_coupons_coupon_type_index` (`coupon_type`),
  KEY `shop_coupons_is_active_index` (`is_active`),
  KEY `shop_coupons_is_public_index` (`is_public`),
  KEY `shop_coupons_valid_from_valid_until_index` (`valid_from`,`valid_until`),
  KEY `shop_coupons_used_count_index` (`used_count`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Kuponlar - İndirim kuponları ve promosyon kodları';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_coupons`
--

LOCK TABLES `shop_coupons` WRITE;
/*!40000 ALTER TABLE `shop_coupons` DISABLE KEYS */;
/*!40000 ALTER TABLE `shop_coupons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_customer_addresses`
--

DROP TABLE IF EXISTS `shop_customer_addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `shop_customer_addresses` (
  `address_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) unsigned NOT NULL COMMENT 'Müşteri ID - shop_customers ilişkisi',
  `address_type` enum('billing','shipping','both') NOT NULL DEFAULT 'both' COMMENT 'Adres tipi: billing=Fatura, shipping=Teslimat, both=Her ikisi',
  `first_name` varchar(255) NOT NULL COMMENT 'Ad',
  `last_name` varchar(255) NOT NULL COMMENT 'Soyad',
  `company_name` varchar(255) DEFAULT NULL COMMENT 'Şirket adı (kurumsal adres için)',
  `tax_office` varchar(255) DEFAULT NULL COMMENT 'Vergi dairesi (fatura adresi için)',
  `tax_number` varchar(255) DEFAULT NULL COMMENT 'Vergi numarası / TC Kimlik',
  `phone` varchar(255) NOT NULL COMMENT 'Telefon numarası',
  `email` varchar(255) DEFAULT NULL COMMENT 'E-posta adresi (opsiyonel)',
  `address_line_1` text NOT NULL COMMENT 'Adres satırı 1 (sokak, bina no)',
  `address_line_2` text DEFAULT NULL COMMENT 'Adres satırı 2 (daire no, vb)',
  `neighborhood` varchar(255) DEFAULT NULL COMMENT 'Mahalle',
  `district` varchar(255) NOT NULL COMMENT 'İlçe',
  `city` varchar(255) NOT NULL COMMENT 'İl/Şehir',
  `postal_code` varchar(10) DEFAULT NULL COMMENT 'Posta kodu',
  `country_code` varchar(2) NOT NULL DEFAULT 'TR' COMMENT 'Ülke kodu (ISO 3166-1 alpha-2: TR, US, DE)',
  `is_default_billing` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Varsayılan fatura adresi mi?',
  `is_default_shipping` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Varsayılan teslimat adresi mi?',
  `delivery_notes` text DEFAULT NULL COMMENT 'Teslimat notları (kapıcıya söyleyin, vb)',
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Ek veriler: {"key":"value","vs.":"..."}' CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`address_id`),
  KEY `shop_customer_addresses_customer_id_index` (`customer_id`),
  KEY `shop_customer_addresses_created_at_index` (`created_at`),
  KEY `shop_customer_addresses_updated_at_index` (`updated_at`),
  KEY `shop_customer_addresses_deleted_at_index` (`deleted_at`),
  KEY `shop_customer_addr_cust_default_billing_idx` (`customer_id`,`is_default_billing`),
  KEY `shop_customer_addr_cust_default_shipping_idx` (`customer_id`,`is_default_shipping`),
  KEY `shop_customer_addresses_address_type_index` (`address_type`),
  KEY `shop_customer_addresses_city_index` (`city`),
  KEY `shop_customer_addresses_country_code_index` (`country_code`),
  KEY `shop_customer_addresses_is_default_billing_index` (`is_default_billing`),
  KEY `shop_customer_addresses_is_default_shipping_index` (`is_default_shipping`),
  CONSTRAINT `shop_customer_addresses_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `shop_customers` (`customer_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_customer_addresses`
--

LOCK TABLES `shop_customer_addresses` WRITE;
/*!40000 ALTER TABLE `shop_customer_addresses` DISABLE KEYS */;
/*!40000 ALTER TABLE `shop_customer_addresses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_customer_groups`
--

DROP TABLE IF EXISTS `shop_customer_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `shop_customer_groups` (
  `customer_group_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'Grup adı: {"tr": "Grup Adı", "en": "Group Name", "vs.": "..."}' CHECK (json_valid(`title`)),
  `slug` varchar(255) NOT NULL COMMENT 'URL-dostu slug',
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Grup açıklaması: {"tr": "Açıklama", "en": "Description", "vs.": "..."}' CHECK (json_valid(`description`)),
  `discount_percentage` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT 'İndirim yüzdesi (%)',
  `has_special_pricing` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Özel fiyatlandırma var mı?',
  `price_on_request_only` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Sadece fiyat teklifi mi? (normal fiyat görmesin)',
  `can_see_stock` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Stok bilgisini görebilir mi?',
  `can_request_quote` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Teklif isteyebilir mi?',
  `can_purchase_on_credit` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Vadeli alışveriş yapabilir mi?',
  `credit_limit` int(11) NOT NULL DEFAULT 0 COMMENT 'Kredi limiti (₺)',
  `payment_term_days` int(11) NOT NULL DEFAULT 0 COMMENT 'Ödeme vadesi (gün)',
  `tax_exempt` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Vergiden muaf mı?',
  `tax_exempt_number` varchar(255) DEFAULT NULL COMMENT 'Vergi muafiyet belge numarası',
  `free_shipping` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Ücretsiz kargo var mı?',
  `free_shipping_threshold` decimal(12,2) DEFAULT NULL COMMENT 'Ücretsiz kargo minimum tutar (₺)',
  `loyalty_points_multiplier` decimal(5,2) NOT NULL DEFAULT 1.00 COMMENT 'Sadakat puanı çarpanı (1.5 = %50 fazla puan)',
  `requires_approval` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Gruba katılım onay gerektirir mi?',
  `is_default` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Varsayılan grup mu? (yeni müşteriler)',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Aktif/Pasif durumu',
  `sort_order` int(11) NOT NULL DEFAULT 0 COMMENT 'Sıralama düzeni',
  `color_code` varchar(7) DEFAULT NULL COMMENT 'Renk kodu (#FF5733)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`customer_group_id`),
  UNIQUE KEY `shop_customer_groups_slug_unique` (`slug`),
  KEY `shop_customer_groups_created_at_index` (`created_at`),
  KEY `shop_customer_groups_updated_at_index` (`updated_at`),
  KEY `shop_customer_groups_deleted_at_index` (`deleted_at`),
  KEY `shop_customer_groups_discount_percentage_index` (`discount_percentage`),
  KEY `shop_customer_groups_is_default_index` (`is_default`),
  KEY `shop_customer_groups_is_active_index` (`is_active`),
  KEY `shop_customer_groups_sort_order_index` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_customer_groups`
--

LOCK TABLES `shop_customer_groups` WRITE;
/*!40000 ALTER TABLE `shop_customer_groups` DISABLE KEYS */;
/*!40000 ALTER TABLE `shop_customer_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_customers`
--

DROP TABLE IF EXISTS `shop_customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `shop_customers` (
  `customer_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID - users tablosu ilişkisi (kayıtlı kullanıcı)',
  `customer_type` enum('individual','corporate','dealer') NOT NULL DEFAULT 'individual' COMMENT 'Müşteri tipi: individual=Bireysel, corporate=Kurumsal, dealer=Bayi',
  `first_name` varchar(255) NOT NULL COMMENT 'Ad',
  `last_name` varchar(255) NOT NULL COMMENT 'Soyad',
  `email` varchar(255) NOT NULL COMMENT 'E-posta adresi',
  `phone` varchar(255) DEFAULT NULL COMMENT 'Telefon numarası',
  `company_name` varchar(255) DEFAULT NULL COMMENT 'Şirket adı (kurumsal müşteri için)',
  `tax_office` varchar(255) DEFAULT NULL COMMENT 'Vergi dairesi',
  `tax_number` varchar(255) DEFAULT NULL COMMENT 'Vergi numarası / TC Kimlik',
  `customer_group_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Müşteri grubu ID - shop_customer_groups ilişkisi',
  `password` varchar(255) DEFAULT NULL COMMENT 'Şifre (hash) - Misafir müşteri kayıt olursa',
  `remember_token` varchar(100) DEFAULT NULL,
  `email_verified` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'E-posta doğrulanmış mı?',
  `email_verified_at` timestamp NULL DEFAULT NULL COMMENT 'E-posta doğrulama tarihi',
  `accepts_marketing` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Pazarlama e-postalarını kabul ediyor mu?',
  `accepts_sms` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'SMS bildirimlerini kabul ediyor mu?',
  `total_orders` int(11) NOT NULL DEFAULT 0 COMMENT 'Toplam sipariş sayısı',
  `total_spent` decimal(14,2) NOT NULL DEFAULT 0.00 COMMENT 'Toplam harcama (₺)',
  `average_order_value` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Ortalama sipariş değeri (₺)',
  `last_order_at` timestamp NULL DEFAULT NULL COMMENT 'Son sipariş tarihi',
  `loyalty_points` int(11) NOT NULL DEFAULT 0 COMMENT 'Sadakat puanı',
  `notes` text DEFAULT NULL COMMENT 'Müşteri hakkında notlar (admin için)',
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Müşteri etiketleri (JSON array): ["tag1", "tag2", "vs."]' CHECK (json_valid(`tags`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Aktif/Pasif durumu',
  `is_verified` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Doğrulanmış müşteri mi? (kimlik kontrolü yapıldı)',
  `last_login_at` timestamp NULL DEFAULT NULL COMMENT 'Son giriş tarihi',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`customer_id`),
  UNIQUE KEY `shop_customers_email_unique` (`email`),
  KEY `shop_customers_user_id_index` (`user_id`),
  KEY `shop_customers_customer_group_id_index` (`customer_group_id`),
  KEY `shop_customers_created_at_index` (`created_at`),
  KEY `shop_customers_updated_at_index` (`updated_at`),
  KEY `shop_customers_deleted_at_index` (`deleted_at`),
  KEY `shop_customers_id_active_idx` (`customer_id`,`is_active`),
  KEY `shop_customers_customer_type_index` (`customer_type`),
  KEY `shop_customers_phone_index` (`phone`),
  KEY `shop_customers_total_spent_index` (`total_spent`),
  KEY `shop_customers_last_order_at_index` (`last_order_at`),
  KEY `shop_customers_is_active_index` (`is_active`),
  CONSTRAINT `shop_customers_customer_group_id_foreign` FOREIGN KEY (`customer_group_id`) REFERENCES `shop_customer_groups` (`customer_group_id`) ON DELETE SET NULL,
  CONSTRAINT `shop_customers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_customers`
--

LOCK TABLES `shop_customers` WRITE;
/*!40000 ALTER TABLE `shop_customers` DISABLE KEYS */;
/*!40000 ALTER TABLE `shop_customers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_inventory`
--

DROP TABLE IF EXISTS `shop_inventory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `shop_inventory` (
  `inventory_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL COMMENT 'Ürün ID - shop_products ilişkisi',
  `product_variant_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Varyant ID - shop_product_variants ilişkisi',
  `warehouse_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Depo ID - shop_warehouses ilişkisi',
  `quantity_on_hand` int(11) NOT NULL DEFAULT 0 COMMENT 'Eldeki stok (fiziksel stok)',
  `quantity_available` int(11) NOT NULL DEFAULT 0 COMMENT 'Kullanılabilir stok (on_hand - reserved)',
  `quantity_reserved` int(11) NOT NULL DEFAULT 0 COMMENT 'Rezerve edilmiş stok (siparişteki ürünler)',
  `quantity_incoming` int(11) NOT NULL DEFAULT 0 COMMENT 'Yolda gelen stok',
  `quantity_damaged` int(11) NOT NULL DEFAULT 0 COMMENT 'Hasarlı/Kullanılamaz stok',
  `reorder_level` int(11) NOT NULL DEFAULT 0 COMMENT 'Yeniden sipariş seviyesi (bu seviyenin altına düşünce uyarı)',
  `reorder_quantity` int(11) NOT NULL DEFAULT 0 COMMENT 'Yeniden sipariş miktarı (kaç adet sipariş verilmeli)',
  `safety_stock` int(11) NOT NULL DEFAULT 0 COMMENT 'Güvenlik stoku (minimum tutulması gereken)',
  `max_stock` int(11) NOT NULL DEFAULT 0 COMMENT 'Maksimum stok seviyesi',
  `unit_cost` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Birim maliyet (₺)',
  `total_value` decimal(14,2) NOT NULL DEFAULT 0.00 COMMENT 'Toplam değer (₺) - quantity_on_hand * unit_cost',
  `costing_method` enum('fifo','lifo','average','standard') NOT NULL DEFAULT 'average' COMMENT 'Maliyet hesaplama yöntemi: fifo=İlk giren ilk çıkar, lifo=Son giren ilk çıkar, average=Ortalama, standard=Standart',
  `last_counted_at` timestamp NULL DEFAULT NULL COMMENT 'Son sayım tarihi',
  `last_counted_quantity` int(11) DEFAULT NULL COMMENT 'Son sayımda tespit edilen miktar',
  `last_counted_by_user_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Son sayımı yapan kullanıcı ID',
  `low_stock_alert` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Düşük stok uyarısı aktif mi?',
  `out_of_stock_alert` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Stok tükendi uyarısı aktif mi?',
  `bin_location` varchar(255) DEFAULT NULL COMMENT 'Raf/Konum bilgisi (A-12-3 gibi)',
  `aisle` varchar(255) DEFAULT NULL COMMENT 'Koridor',
  `shelf` varchar(255) DEFAULT NULL COMMENT 'Raf',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`inventory_id`),
  UNIQUE KEY `unique_product_warehouse` (`product_id`,`product_variant_id`,`warehouse_id`),
  KEY `shop_inventory_created_at_index` (`created_at`),
  KEY `shop_inventory_updated_at_index` (`updated_at`),
  KEY `shop_inventory_product_id_index` (`product_id`),
  KEY `shop_inventory_product_variant_id_index` (`product_variant_id`),
  KEY `shop_inventory_warehouse_id_index` (`warehouse_id`),
  KEY `shop_inventory_quantity_available_index` (`quantity_available`),
  KEY `shop_inventory_reorder_level_index` (`reorder_level`),
  CONSTRAINT `shop_inventory_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `shop_products` (`product_id`) ON DELETE CASCADE,
  CONSTRAINT `shop_inventory_product_variant_id_foreign` FOREIGN KEY (`product_variant_id`) REFERENCES `shop_product_variants` (`variant_id`) ON DELETE CASCADE,
  CONSTRAINT `shop_inventory_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `shop_warehouses` (`warehouse_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Envanter - Ürün stok seviyeleri ve depo yönetimi';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_inventory`
--

LOCK TABLES `shop_inventory` WRITE;
/*!40000 ALTER TABLE `shop_inventory` DISABLE KEYS */;
/*!40000 ALTER TABLE `shop_inventory` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_order_addresses`
--

DROP TABLE IF EXISTS `shop_order_addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `shop_order_addresses` (
  `order_address_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) unsigned NOT NULL COMMENT 'Sipariş ID - shop_orders ilişkisi',
  `address_type` enum('billing','shipping') NOT NULL COMMENT 'Adres tipi: billing=Fatura, shipping=Teslimat',
  `first_name` varchar(255) NOT NULL COMMENT 'Ad (snapshot)',
  `last_name` varchar(255) NOT NULL COMMENT 'Soyad (snapshot)',
  `company_name` varchar(255) DEFAULT NULL COMMENT 'Şirket adı (snapshot)',
  `tax_office` varchar(255) DEFAULT NULL COMMENT 'Vergi dairesi (snapshot)',
  `tax_number` varchar(255) DEFAULT NULL COMMENT 'Vergi numarası / TC Kimlik (snapshot)',
  `phone` varchar(255) NOT NULL COMMENT 'Telefon numarası (snapshot)',
  `email` varchar(255) DEFAULT NULL COMMENT 'E-posta adresi (snapshot)',
  `address_line_1` text NOT NULL COMMENT 'Adres satırı 1 (snapshot)',
  `address_line_2` text DEFAULT NULL COMMENT 'Adres satırı 2 (snapshot)',
  `neighborhood` varchar(255) DEFAULT NULL COMMENT 'Mahalle (snapshot)',
  `district` varchar(255) NOT NULL COMMENT 'İlçe (snapshot)',
  `city` varchar(255) NOT NULL COMMENT 'İl/Şehir (snapshot)',
  `postal_code` varchar(10) DEFAULT NULL COMMENT 'Posta kodu (snapshot)',
  `country_code` varchar(2) NOT NULL DEFAULT 'TR' COMMENT 'Ülke kodu (snapshot - ISO 3166-1 alpha-2)',
  `country_name` varchar(255) DEFAULT NULL COMMENT 'Ülke adı (snapshot - Türkiye, United States)',
  `delivery_notes` text DEFAULT NULL COMMENT 'Teslimat notları (snapshot)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`order_address_id`),
  KEY `shop_order_addresses_created_at_index` (`created_at`),
  KEY `shop_order_addresses_updated_at_index` (`updated_at`),
  KEY `shop_order_addresses_order_id_index` (`order_id`),
  KEY `shop_order_addresses_address_type_index` (`address_type`),
  KEY `shop_order_addresses_order_id_address_type_index` (`order_id`,`address_type`),
  CONSTRAINT `shop_order_addresses_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `shop_orders` (`order_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Sipariş adresleri - Fatura ve teslimat adresleri snapshot (değişmez kayıt)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_order_addresses`
--

LOCK TABLES `shop_order_addresses` WRITE;
/*!40000 ALTER TABLE `shop_order_addresses` DISABLE KEYS */;
/*!40000 ALTER TABLE `shop_order_addresses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_order_items`
--

DROP TABLE IF EXISTS `shop_order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `shop_order_items` (
  `order_item_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) unsigned NOT NULL COMMENT 'Sipariş ID - shop_orders ilişkisi',
  `product_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Ürün ID - shop_products ilişkisi (ürün silinirse null)',
  `product_variant_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Varyant ID - shop_product_variants ilişkisi',
  `sku` varchar(255) NOT NULL COMMENT 'Ürün SKU (snapshot - ürün kodları değişebilir)',
  `model_number` varchar(255) DEFAULT NULL COMMENT 'Model numarası (snapshot)',
  `product_name` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'Ürün adı (JSON snapshot - {"tr":"...", "en":"..."})' CHECK (json_valid(`product_name`)),
  `product_image` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Ürün görseli (JSON snapshot - tek görsel)' CHECK (json_valid(`product_image`)),
  `variant_options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Varyant seçenekleri ({"mast_height":"3000mm","battery":"150Ah"})' CHECK (json_valid(`variant_options`)),
  `unit_price` decimal(12,2) NOT NULL COMMENT 'Birim fiyat (₺) - İndirim öncesi',
  `discount_amount` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Birim başına indirim tutarı (₺)',
  `discount_percentage` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT 'İndirim yüzdesi (%)',
  `final_price` decimal(12,2) NOT NULL COMMENT 'İndirimli birim fiyat (₺)',
  `quantity` int(11) NOT NULL DEFAULT 1 COMMENT 'Adet',
  `subtotal` decimal(12,2) NOT NULL COMMENT 'Ara toplam (₺) - final_price * quantity',
  `tax_amount` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Vergi tutarı (₺)',
  `total_amount` decimal(12,2) NOT NULL COMMENT 'Satır toplamı (₺) - subtotal + tax_amount',
  `tax_rate` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT 'Vergi oranı (%) - 18, 8, 1, 0',
  `tax_class` varchar(255) DEFAULT NULL COMMENT 'Vergi sınıfı (standard, reduced, zero)',
  `customization_options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Özelleştirme seçenekleri (JSON - renk, logo, vb)' CHECK (json_valid(`customization_options`)),
  `special_instructions` text DEFAULT NULL COMMENT 'Özel talimatlar',
  `item_status` enum('pending','processing','ready','shipped','delivered','cancelled','refunded') NOT NULL DEFAULT 'pending' COMMENT 'Ürün durumu (her ürün ayrı takip edilebilir)',
  `is_refundable` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'İade edilebilir mi?',
  `is_refunded` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'İade edildi mi?',
  `refunded_amount` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT 'İade edilen tutar (₺)',
  `notes` text DEFAULT NULL COMMENT 'Notlar',
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Ek veriler (JSON)' CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Soft delete için silinme tarihi',
  PRIMARY KEY (`order_item_id`),
  KEY `shop_order_items_created_at_index` (`created_at`),
  KEY `shop_order_items_updated_at_index` (`updated_at`),
  KEY `shop_order_items_deleted_at_index` (`deleted_at`),
  KEY `shop_order_items_order_id_index` (`order_id`),
  KEY `shop_order_items_product_id_index` (`product_id`),
  KEY `shop_order_items_product_variant_id_index` (`product_variant_id`),
  KEY `shop_order_items_sku_index` (`sku`),
  KEY `shop_order_items_item_status_index` (`item_status`),
  KEY `shop_order_items_order_id_product_id_index` (`order_id`,`product_id`),
  CONSTRAINT `shop_order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `shop_orders` (`order_id`) ON DELETE CASCADE,
  CONSTRAINT `shop_order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `shop_products` (`product_id`) ON DELETE CASCADE,
  CONSTRAINT `shop_order_items_product_variant_id_foreign` FOREIGN KEY (`product_variant_id`) REFERENCES `shop_product_variants` (`variant_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Sipariş ürünleri - Her siparişin içindeki ürünler (snapshot yaklaşımı)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_order_items`
--

LOCK TABLES `shop_order_items` WRITE;
/*!40000 ALTER TABLE `shop_order_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `shop_order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_orders`
--

DROP TABLE IF EXISTS `shop_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `shop_orders` (
  `order_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_number` varchar(255) NOT NULL COMMENT 'Sipariş numarası (ORD-2024-00001)',
  `customer_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Müşteri ID - shop_customers ilişkisi',
  `order_type` enum('sale','quote','rental','service') NOT NULL DEFAULT 'sale' COMMENT 'Sipariş tipi: sale=Satış, quote=Teklif, rental=Kiralama, service=Servis',
  `order_source` enum('web','admin','mobile','api') NOT NULL DEFAULT 'web' COMMENT 'Sipariş kaynağı',
  `status` enum('pending','confirmed','processing','deposit_paid','ready','shipped','delivered','completed','cancelled','refunded') NOT NULL DEFAULT 'pending' COMMENT 'Sipariş durumu',
  `subtotal` decimal(14,2) NOT NULL COMMENT 'Ara toplam (₺) - İndirim ve vergiler hariç',
  `discount_amount` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT 'İndirim tutarı (₺)',
  `tax_amount` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Vergi tutarı (₺)',
  `shipping_cost` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Kargo ücreti (₺)',
  `total_amount` decimal(14,2) NOT NULL COMMENT 'Toplam tutar (₺)',
  `currency` varchar(3) NOT NULL DEFAULT 'TRY' COMMENT 'Para birimi (TRY, USD, EUR)',
  `deposit_required` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Kapora gerekli mi?',
  `deposit_amount` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Kapora tutarı (₺)',
  `deposit_paid` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Kapora ödendi mi?',
  `deposit_paid_at` timestamp NULL DEFAULT NULL COMMENT 'Kapora ödeme tarihi',
  `payment_status` enum('pending','partially_paid','paid','refunded','failed') NOT NULL DEFAULT 'pending' COMMENT 'Ödeme durumu',
  `paid_amount` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Ödenen tutar (₺)',
  `remaining_amount` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Kalan tutar (₺)',
  `payment_method_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Ödeme yöntemi ID',
  `shipping_method_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Kargo yöntemi ID',
  `tracking_number` varchar(255) DEFAULT NULL COMMENT 'Kargo takip numarası',
  `shipped_at` timestamp NULL DEFAULT NULL COMMENT 'Kargoya verilme tarihi',
  `delivered_at` timestamp NULL DEFAULT NULL COMMENT 'Teslim tarihi',
  `coupon_code` varchar(255) DEFAULT NULL COMMENT 'Kullanılan kupon kodu',
  `coupon_discount` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Kupon indirimi (₺)',
  `customer_email` varchar(255) NOT NULL COMMENT 'Müşteri e-posta (snapshot)',
  `customer_phone` varchar(255) DEFAULT NULL COMMENT 'Müşteri telefon (snapshot)',
  `billing_address` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Fatura adresi (JSON snapshot)' CHECK (json_valid(`billing_address`)),
  `shipping_address` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Teslimat adresi (JSON snapshot)' CHECK (json_valid(`shipping_address`)),
  `customer_notes` text DEFAULT NULL COMMENT 'Müşteri notu',
  `admin_notes` text DEFAULT NULL COMMENT 'Admin notu',
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Ek veriler (JSON)' CHECK (json_valid(`metadata`)),
  `ip_address` varchar(45) DEFAULT NULL COMMENT 'IP adresi',
  `user_agent` text DEFAULT NULL COMMENT 'Tarayıcı bilgisi',
  `confirmed_at` timestamp NULL DEFAULT NULL COMMENT 'Onaylanma tarihi',
  `completed_at` timestamp NULL DEFAULT NULL COMMENT 'Tamamlanma tarihi',
  `cancelled_at` timestamp NULL DEFAULT NULL COMMENT 'İptal edilme tarihi',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Soft delete için silinme tarihi',
  PRIMARY KEY (`order_id`),
  UNIQUE KEY `shop_orders_order_number_unique` (`order_number`),
  KEY `shop_orders_created_at_index` (`created_at`),
  KEY `shop_orders_updated_at_index` (`updated_at`),
  KEY `shop_orders_deleted_at_index` (`deleted_at`),
  KEY `shop_orders_order_number_index` (`order_number`),
  KEY `shop_orders_customer_id_index` (`customer_id`),
  KEY `shop_orders_status_index` (`status`),
  KEY `shop_orders_payment_status_index` (`payment_status`),
  KEY `shop_orders_order_type_index` (`order_type`),
  KEY `shop_orders_total_amount_index` (`total_amount`),
  KEY `shop_orders_customer_id_status_index` (`customer_id`,`status`),
  KEY `shop_orders_status_payment_status_index` (`status`,`payment_status`),
  KEY `shop_orders_payment_method_id_foreign` (`payment_method_id`),
  CONSTRAINT `shop_orders_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `shop_customers` (`customer_id`) ON DELETE SET NULL,
  CONSTRAINT `shop_orders_payment_method_id_foreign` FOREIGN KEY (`payment_method_id`) REFERENCES `shop_payment_methods` (`payment_method_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Siparişler - Tüm sipariş tipleri (satış, teklif, kiralama, servis)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_orders`
--

LOCK TABLES `shop_orders` WRITE;
/*!40000 ALTER TABLE `shop_orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `shop_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_payment_methods`
--

DROP TABLE IF EXISTS `shop_payment_methods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `shop_payment_methods` (
  `payment_method_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'Ödeme yöntemi adı ({"tr":"Kredi Kartı","en":"Credit Card"})' CHECK (json_valid(`title`)),
  `slug` varchar(255) NOT NULL COMMENT 'URL-dostu slug (kredi-karti)',
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Açıklama (JSON çoklu dil)' CHECK (json_valid(`description`)),
  `payment_type` enum('credit_card','debit_card','bank_transfer','cash_on_delivery','cash','wire_transfer','check','installment','paypal','stripe','other') NOT NULL COMMENT 'Ödeme tipi',
  `gateway_name` varchar(255) DEFAULT NULL COMMENT 'Ödeme gateway adı (iyzico, paytr, stripe, vb)',
  `gateway_mode` varchar(255) DEFAULT NULL COMMENT 'Gateway modu (test, live)',
  `gateway_config` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Gateway ayarları (JSON - API keys, merchant ID, vb)' CHECK (json_valid(`gateway_config`)),
  `fixed_fee` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Sabit komisyon (₺)',
  `percentage_fee` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT 'Yüzde komisyon (%)',
  `min_amount` decimal(10,2) DEFAULT NULL COMMENT 'Minimum tutar (₺)',
  `max_amount` decimal(14,2) DEFAULT NULL COMMENT 'Maximum tutar (₺)',
  `supports_installment` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Taksit desteği var mı?',
  `installment_options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Taksit seçenekleri (JSON - [{\\"months\\":3,\\"rate\\":1.05}])' CHECK (json_valid(`installment_options`)),
  `max_installments` int(11) NOT NULL DEFAULT 1 COMMENT 'Maksimum taksit sayısı',
  `supported_currencies` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Desteklenen para birimleri (JSON - ["TRY","USD","EUR"])' CHECK (json_valid(`supported_currencies`)),
  `icon` varchar(255) DEFAULT NULL COMMENT 'İkon dosya yolu veya sınıfı',
  `logo_url` varchar(255) DEFAULT NULL COMMENT 'Logo URL',
  `sort_order` int(11) NOT NULL DEFAULT 0 COMMENT 'Sıralama düzeni',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Aktif/Pasif durumu',
  `requires_verification` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Doğrulama gerektirir mi?',
  `is_manual` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Manuel onay gerektirir mi? (havale gibi)',
  `available_for_b2c` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'B2C müşteriler kullanabilir mi?',
  `available_for_b2b` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'B2B müşteriler kullanabilir mi?',
  `customer_group_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Sadece belirli müşteri gruplarına özel (JSON array)' CHECK (json_valid(`customer_group_ids`)),
  `instructions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Ödeme talimatları (JSON çoklu dil - havale için IBAN vb)' CHECK (json_valid(`instructions`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Soft delete için silinme tarihi',
  PRIMARY KEY (`payment_method_id`),
  UNIQUE KEY `shop_payment_methods_slug_unique` (`slug`),
  KEY `shop_payment_methods_created_at_index` (`created_at`),
  KEY `shop_payment_methods_updated_at_index` (`updated_at`),
  KEY `shop_payment_methods_deleted_at_index` (`deleted_at`),
  KEY `shop_payment_methods_slug_index` (`slug`),
  KEY `shop_payment_methods_payment_type_index` (`payment_type`),
  KEY `shop_payment_methods_is_active_index` (`is_active`),
  KEY `shop_payment_methods_gateway_name_index` (`gateway_name`),
  KEY `shop_payment_methods_sort_order_index` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Ödeme yöntemleri - Kredi kartı, havale, kapıda ödeme vb.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_payment_methods`
--

LOCK TABLES `shop_payment_methods` WRITE;
/*!40000 ALTER TABLE `shop_payment_methods` DISABLE KEYS */;
/*!40000 ALTER TABLE `shop_payment_methods` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_payments`
--

DROP TABLE IF EXISTS `shop_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `shop_payments` (
  `payment_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) unsigned NOT NULL COMMENT 'Sipariş ID - shop_orders ilişkisi',
  `payment_method_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Ödeme yöntemi ID - shop_payment_methods ilişkisi',
  `payment_number` varchar(255) NOT NULL COMMENT 'Ödeme numarası (PAY-2024-00001)',
  `payment_type` enum('full','partial','deposit','installment','refund') NOT NULL DEFAULT 'full' COMMENT 'Ödeme tipi: full=Tam ödeme, partial=Kısmi ödeme, deposit=Kapora, installment=Taksit, refund=İade',
  `amount` decimal(12,2) NOT NULL COMMENT 'Ödeme tutarı (₺)',
  `currency` varchar(3) NOT NULL DEFAULT 'TRY' COMMENT 'Para birimi (TRY, USD, EUR)',
  `exchange_rate` decimal(10,4) NOT NULL DEFAULT 1.0000 COMMENT 'Döviz kuru (TRY dışı ödemeler için)',
  `amount_in_base_currency` decimal(12,2) NOT NULL COMMENT 'Ana para biriminde tutar (₺)',
  `status` enum('pending','processing','completed','failed','cancelled','refunded') NOT NULL DEFAULT 'pending' COMMENT 'Ödeme durumu',
  `gateway_name` varchar(255) DEFAULT NULL COMMENT 'Ödeme gateway adı (iyzico, paytr, stripe)',
  `gateway_transaction_id` varchar(255) DEFAULT NULL COMMENT 'Gateway işlem numarası',
  `gateway_payment_id` varchar(255) DEFAULT NULL COMMENT 'Gateway ödeme ID',
  `gateway_response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Gateway yanıtı (JSON - tüm response)' CHECK (json_valid(`gateway_response`)),
  `card_brand` varchar(255) DEFAULT NULL COMMENT 'Kart markası (Visa, MasterCard, vb)',
  `card_last_four` varchar(4) DEFAULT NULL COMMENT 'Kart son 4 hanesi',
  `card_holder_name` varchar(255) DEFAULT NULL COMMENT 'Kart sahibi adı',
  `installment_count` int(11) NOT NULL DEFAULT 1 COMMENT 'Taksit sayısı (1=Tek çekim)',
  `installment_fee` decimal(8,2) NOT NULL DEFAULT 0.00 COMMENT 'Taksit komisyonu (₺)',
  `bank_name` varchar(255) DEFAULT NULL COMMENT 'Banka adı (havale için)',
  `bank_account_name` varchar(255) DEFAULT NULL COMMENT 'Hesap sahibi',
  `bank_reference` varchar(255) DEFAULT NULL COMMENT 'Banka dekontu referans no',
  `receipt_file` varchar(255) DEFAULT NULL COMMENT 'Dekont dosya yolu',
  `refund_for_payment_id` bigint(20) unsigned DEFAULT NULL COMMENT 'İade edilen ödeme ID (refund ise)',
  `refund_reason` text DEFAULT NULL COMMENT 'İade nedeni',
  `is_verified` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Doğrulandı mı? (manuel ödemeler için)',
  `verified_by_user_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Doğrulayan kullanıcı ID',
  `verified_at` timestamp NULL DEFAULT NULL COMMENT 'Doğrulama tarihi',
  `paid_at` timestamp NULL DEFAULT NULL COMMENT 'Ödeme tarihi',
  `failed_at` timestamp NULL DEFAULT NULL COMMENT 'Başarısız olma tarihi',
  `refunded_at` timestamp NULL DEFAULT NULL COMMENT 'İade tarihi',
  `notes` text DEFAULT NULL COMMENT 'Notlar',
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Ek veriler (JSON)' CHECK (json_valid(`metadata`)),
  `ip_address` varchar(45) DEFAULT NULL COMMENT 'IP adresi',
  `user_agent` text DEFAULT NULL COMMENT 'Tarayıcı bilgisi',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Soft delete için silinme tarihi',
  PRIMARY KEY (`payment_id`),
  UNIQUE KEY `shop_payments_payment_number_unique` (`payment_number`),
  KEY `shop_payments_created_at_index` (`created_at`),
  KEY `shop_payments_updated_at_index` (`updated_at`),
  KEY `shop_payments_deleted_at_index` (`deleted_at`),
  KEY `shop_payments_order_id_index` (`order_id`),
  KEY `shop_payments_payment_method_id_index` (`payment_method_id`),
  KEY `shop_payments_payment_number_index` (`payment_number`),
  KEY `shop_payments_status_index` (`status`),
  KEY `shop_payments_payment_type_index` (`payment_type`),
  KEY `shop_payments_gateway_transaction_id_index` (`gateway_transaction_id`),
  KEY `shop_payments_is_verified_index` (`is_verified`),
  KEY `shop_payments_paid_at_index` (`paid_at`),
  KEY `shop_payments_order_id_status_index` (`order_id`,`status`),
  KEY `shop_payments_refund_for_payment_id_foreign` (`refund_for_payment_id`),
  CONSTRAINT `shop_payments_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `shop_orders` (`order_id`) ON DELETE CASCADE,
  CONSTRAINT `shop_payments_payment_method_id_foreign` FOREIGN KEY (`payment_method_id`) REFERENCES `shop_payment_methods` (`payment_method_id`) ON DELETE CASCADE,
  CONSTRAINT `shop_payments_refund_for_payment_id_foreign` FOREIGN KEY (`refund_for_payment_id`) REFERENCES `shop_payments` (`payment_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Ödemeler - Sipariş ödemelerinin detaylı kayıtları';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_payments`
--

LOCK TABLES `shop_payments` WRITE;
/*!40000 ALTER TABLE `shop_payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `shop_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_product_attributes`
--

DROP TABLE IF EXISTS `shop_product_attributes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `shop_product_attributes` (
  `product_attribute_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL COMMENT 'Ürün ID - shop_products ilişkisi',
  `attribute_id` bigint(20) unsigned NOT NULL COMMENT 'Özellik ID - shop_attributes ilişkisi',
  `value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'Özellik değeri: {"tr":"Değer","en":"Value","vs.":"..."} veya basit string/number' CHECK (json_valid(`value`)),
  `value_text` text DEFAULT NULL COMMENT 'Metin değeri (arama için)',
  `value_numeric` decimal(12,2) DEFAULT NULL COMMENT 'Sayısal değer (filtreleme ve sıralama için)',
  `sort_order` int(11) NOT NULL DEFAULT 0 COMMENT 'Sıralama düzeni',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`product_attribute_id`),
  UNIQUE KEY `shop_product_attributes_unique_prod_attr` (`product_id`,`attribute_id`),
  KEY `shop_product_attributes_product_id_index` (`product_id`),
  KEY `shop_product_attributes_attribute_id_index` (`attribute_id`),
  KEY `shop_product_attributes_value_numeric_index` (`value_numeric`),
  KEY `shop_product_attributes_created_at_index` (`created_at`),
  KEY `shop_product_attributes_updated_at_index` (`updated_at`),
  KEY `shop_product_attributes_prod_attr_idx` (`product_id`,`attribute_id`),
  KEY `shop_product_attributes_sort_order_index` (`sort_order`),
  CONSTRAINT `shop_product_attributes_attribute_id_foreign` FOREIGN KEY (`attribute_id`) REFERENCES `shop_attributes` (`attribute_id`) ON DELETE CASCADE,
  CONSTRAINT `shop_product_attributes_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `shop_products` (`product_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_product_attributes`
--

LOCK TABLES `shop_product_attributes` WRITE;
/*!40000 ALTER TABLE `shop_product_attributes` DISABLE KEYS */;
/*!40000 ALTER TABLE `shop_product_attributes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_product_chat_placeholders`
--

DROP TABLE IF EXISTS `shop_product_chat_placeholders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `shop_product_chat_placeholders` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` varchar(255) NOT NULL COMMENT 'Shop product ID',
  `conversation_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'AI-generated placeholder conversation' CHECK (json_valid(`conversation_json`)),
  `generated_at` timestamp NULL DEFAULT NULL COMMENT 'When placeholder was generated',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `shop_product_chat_placeholders_product_id_unique` (`product_id`),
  KEY `shop_product_chat_placeholders_product_id_index` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_product_chat_placeholders`
--

LOCK TABLES `shop_product_chat_placeholders` WRITE;
/*!40000 ALTER TABLE `shop_product_chat_placeholders` DISABLE KEYS */;
/*!40000 ALTER TABLE `shop_product_chat_placeholders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_product_field_templates`
--

DROP TABLE IF EXISTS `shop_product_field_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `shop_product_field_templates` (
  `template_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `fields` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`fields`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`template_id`),
  UNIQUE KEY `shop_product_field_templates_name_unique` (`name`),
  KEY `shop_product_field_templates_is_active_index` (`is_active`),
  KEY `shop_product_field_templates_sort_order_index` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_product_field_templates`
--

LOCK TABLES `shop_product_field_templates` WRITE;
/*!40000 ALTER TABLE `shop_product_field_templates` DISABLE KEYS */;
/*!40000 ALTER TABLE `shop_product_field_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_product_variants`
--

DROP TABLE IF EXISTS `shop_product_variants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `shop_product_variants` (
  `variant_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL COMMENT 'Ana ürün ID - shop_products ilişkisi',
  `sku` varchar(255) NOT NULL COMMENT 'Varyant SKU',
  `barcode` varchar(255) DEFAULT NULL COMMENT 'Varyant barkod',
  `title` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'Varyant adı: {"tr": "Varyant Adı", "en": "Variant Name", "vs.": "..."}' CHECK (json_valid(`title`)),
  `option_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'Seçenek değerleri: {"option1":"value1","option2":"value2", "vs.": "..."}' CHECK (json_valid(`option_values`)),
  `price_modifier` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Fiyat değişimi (+ veya - tutar) - Ana ürüne eklenir',
  `cost_price` decimal(12,2) DEFAULT NULL COMMENT 'Varyant maliyet fiyatı',
  `stock_quantity` int(11) NOT NULL DEFAULT 0 COMMENT 'Varyant stok miktarı',
  `reserved_quantity` int(11) NOT NULL DEFAULT 0 COMMENT 'Rezerve edilen miktar',
  `weight` decimal(10,2) DEFAULT NULL COMMENT 'Varyant ağırlığı (kg) - Ana üründen farklıysa',
  `dimensions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Varyant boyutları - Ana üründen farklıysa: {"length":100,"width":50,"height":30}' CHECK (json_valid(`dimensions`)),
  `image_url` varchar(255) DEFAULT NULL COMMENT 'Varyant görseli - Ana üründen farklıysa',
  `images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Varyant görselleri (JSON array)' CHECK (json_valid(`images`)),
  `is_default` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Varsayılan varyant mı?',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Aktif/Pasif durumu',
  `sort_order` int(11) NOT NULL DEFAULT 0 COMMENT 'Sıralama düzeni',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`variant_id`),
  UNIQUE KEY `shop_product_variants_sku_unique` (`sku`),
  KEY `shop_product_variants_product_id_index` (`product_id`),
  KEY `shop_product_variants_sku_index` (`sku`),
  KEY `shop_product_variants_created_at_index` (`created_at`),
  KEY `shop_product_variants_updated_at_index` (`updated_at`),
  KEY `shop_product_variants_deleted_at_index` (`deleted_at`),
  KEY `shop_product_variants_product_active_idx` (`product_id`,`is_active`),
  KEY `shop_product_variants_product_default_idx` (`product_id`,`is_default`),
  KEY `shop_product_variants_is_default_index` (`is_default`),
  KEY `shop_product_variants_is_active_index` (`is_active`),
  KEY `shop_product_variants_sort_order_index` (`sort_order`),
  CONSTRAINT `shop_product_variants_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `shop_products` (`product_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_product_variants`
--

LOCK TABLES `shop_product_variants` WRITE;
/*!40000 ALTER TABLE `shop_product_variants` DISABLE KEYS */;
/*!40000 ALTER TABLE `shop_product_variants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_products`
--

DROP TABLE IF EXISTS `shop_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `shop_products` (
  `product_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint(20) unsigned NOT NULL COMMENT 'Kategori ID - shop_categories ilişkisi',
  `brand_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Marka ID - shop_brands ilişkisi',
  `sku` varchar(255) NOT NULL COMMENT 'Stok Kodu (Stock Keeping Unit) - Benzersiz',
  `model_number` varchar(255) DEFAULT NULL COMMENT 'Model numarası',
  `barcode` varchar(255) DEFAULT NULL COMMENT 'Barkod numarası',
  `parent_product_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Ana ürün ID (parent product)',
  `is_master_product` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Ana ürün mü? (master product)',
  `variant_type` varchar(100) DEFAULT NULL COMMENT 'Varyant tipi (slug-friendly: standart-catal, genis-catal, etc.)',
  `title` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'Ürün başlığı: {"tr": "Ürün Adı", "en": "Product Name", "vs.": "..."}' CHECK (json_valid(`title`)),
  `slug` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'Çoklu dil slug: {"tr": "urun-adi", "en": "product-name", "vs.": "..."}' CHECK (json_valid(`slug`)),
  `short_description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Kısa açıklama (maksimum 160 karakter): {"tr": "Kısa açıklama", "en": "Short description", "vs.": "..."}' CHECK (json_valid(`short_description`)),
  `body` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Detaylı açıklama (Rich text HTML): {"tr": "<p>Detaylı açıklama</p>", "en": "<p>Detailed description</p>", "vs.": "..."}' CHECK (json_valid(`body`)),
  `product_type` enum('physical','digital','service','membership','bundle') NOT NULL DEFAULT 'physical' COMMENT 'Ürün tipi: physical=Fiziksel, digital=Dijital, service=Hizmet, membership=Üyelik, bundle=Paket',
  `condition` enum('new','used','refurbished') NOT NULL DEFAULT 'new' COMMENT 'Ürün durumu: new=Sıfır, used=İkinci el, refurbished=Yenilenmiş',
  `price_on_request` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Fiyat sorunuz aktif mi? (B2B için)',
  `base_price` decimal(12,2) DEFAULT NULL COMMENT 'Temel fiyat (₺)',
  `compare_at_price` decimal(12,2) DEFAULT NULL COMMENT 'İndirim öncesi fiyat (₺)',
  `cost_price` decimal(12,2) DEFAULT NULL COMMENT 'Maliyet fiyatı (₺) - Kar hesabı için',
  `currency` varchar(3) NOT NULL DEFAULT 'TRY' COMMENT 'Para birimi (ISO 4217: TRY, USD, EUR)',
  `deposit_required` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Kapora gerekli mi?',
  `deposit_amount` decimal(12,2) DEFAULT NULL COMMENT 'Sabit kapora tutarı (₺)',
  `deposit_percentage` int(11) DEFAULT NULL COMMENT 'Kapora yüzdesi (%)',
  `installment_available` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Taksit yapılabilir mi?',
  `max_installments` int(11) DEFAULT NULL COMMENT 'Maksimum taksit sayısı (9, 12, vb)',
  `stock_tracking` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Stok takibi yapılsın mı?',
  `current_stock` int(11) NOT NULL DEFAULT 0 COMMENT 'Mevcut stok miktarı',
  `low_stock_threshold` int(11) NOT NULL DEFAULT 5 COMMENT 'Düşük stok uyarı seviyesi',
  `allow_backorder` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Stokta yokken sipariş alınabilir mi?',
  `lead_time_days` int(11) DEFAULT NULL COMMENT 'Temin süresi (gün)',
  `weight` decimal(10,2) DEFAULT NULL COMMENT 'Ağırlık (kg)',
  `dimensions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Boyutlar: {"length":100,"width":50,"height":30,"unit":"cm"}' CHECK (json_valid(`dimensions`)),
  `technical_specs` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Teknik özellikler (JSON nested object - kapasite, performans, elektrik, vb)' CHECK (json_valid(`technical_specs`)),
  `features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Özellikler listesi (JSON array) - Bullet points' CHECK (json_valid(`features`)),
  `highlighted_features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Öne çıkan özellikler: [{"icon":"battery","title":"...","description":"..."}, ...]' CHECK (json_valid(`highlighted_features`)),
  `accessories` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Aksesuarlar ve opsiyonlar: [{"name":"...","description":"...","is_standard":false,"is_optional":true}, ...]' CHECK (json_valid(`accessories`)),
  `certifications` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Sertifikalar: [{"name":"CE","year":2021,"authority":"TÜV Rheinland","icon":"certificate"}, ...]' CHECK (json_valid(`certifications`)),
  `primary_specs` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Ana özellikler (4 tane, highlight edilmiş): [{"label":"2.0 Ton","value":"Kapasite","icon":"weight-hanging"}, ...]' CHECK (json_valid(`primary_specs`)),
  `use_cases` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Kullanım senaryoları (8 tane): ["E-ticaret depolarında EUR palet transferi", ...]' CHECK (json_valid(`use_cases`)),
  `faq_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'S.S.S (10-12 soru): [{"question":"...","answer":"...","category":"usage","is_highlighted":true,"sort_order":1}, ...]' CHECK (json_valid(`faq_data`)),
  `competitive_advantages` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Rekabet avantajları (7 tane, ikonlu): [{"text":"Modüler Li-Ion ile minimal kesinti","icon":"battery-full"}, ...]' CHECK (json_valid(`competitive_advantages`)),
  `target_industries` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Hedef sektörler (20+ tane, ikonlu): [{"name":"E-ticaret ve Fulfillment","icon":"box-open"}, ...]' CHECK (json_valid(`target_industries`)),
  `media_gallery` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Medya galerisi: [{"type":"image","url":"...","is_primary":true}, ...]' CHECK (json_valid(`media_gallery`)),
  `video_url` varchar(255) DEFAULT NULL COMMENT 'Video URL (YouTube, Vimeo)',
  `manual_pdf_url` varchar(255) DEFAULT NULL COMMENT 'Kullanım kılavuzu PDF URL',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Aktif/Pasif durumu',
  `is_featured` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Öne çıkan ürün',
  `is_bestseller` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Çok satan ürün',
  `view_count` int(11) NOT NULL DEFAULT 0 COMMENT 'Görüntülenme sayısı',
  `sales_count` int(11) NOT NULL DEFAULT 0 COMMENT 'Satış sayısı',
  `published_at` timestamp NULL DEFAULT NULL COMMENT 'Yayınlanma tarihi',
  `warranty_info` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Garanti bilgisi: {"period":24,"unit":"month","details":"..."}' CHECK (json_valid(`warranty_info`)),
  `shipping_info` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Kargo bilgisi: {"weight_limit":50,"size_limit":"large","free_shipping":false}' CHECK (json_valid(`shipping_info`)),
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Etiketler (JSON array): ["tag1", "tag2", "tag3"]' CHECK (json_valid(`tags`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`product_id`),
  UNIQUE KEY `shop_products_sku_unique` (`sku`),
  KEY `shop_products_category_id_index` (`category_id`),
  KEY `shop_products_brand_id_index` (`brand_id`),
  KEY `shop_products_sku_index` (`sku`),
  KEY `shop_products_product_type_index` (`product_type`),
  KEY `shop_products_parent_product_id_index` (`parent_product_id`),
  KEY `shop_products_is_master_product_index` (`is_master_product`),
  KEY `shop_products_variant_type_index` (`variant_type`),
  KEY `shop_products_created_at_index` (`created_at`),
  KEY `shop_products_updated_at_index` (`updated_at`),
  KEY `shop_products_deleted_at_index` (`deleted_at`),
  KEY `shop_products_cat_active_idx` (`category_id`,`is_active`),
  KEY `shop_products_brand_active_idx` (`brand_id`,`is_active`),
  KEY `shop_products_active_deleted_published_idx` (`is_active`,`deleted_at`,`published_at`),
  KEY `shop_products_featured_active_idx` (`is_featured`,`is_active`),
  KEY `shop_products_bestseller_active_idx` (`is_bestseller`,`is_active`),
  KEY `shop_products_price_on_request_index` (`price_on_request`),
  KEY `shop_products_is_active_index` (`is_active`),
  KEY `shop_products_is_featured_index` (`is_featured`),
  KEY `shop_products_is_bestseller_index` (`is_bestseller`),
  KEY `shop_products_published_at_index` (`published_at`),
  CONSTRAINT `shop_products_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `shop_brands` (`brand_id`) ON DELETE SET NULL,
  CONSTRAINT `shop_products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `shop_categories` (`category_id`),
  CONSTRAINT `shop_products_parent_product_id_foreign` FOREIGN KEY (`parent_product_id`) REFERENCES `shop_products` (`product_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_products`
--

LOCK TABLES `shop_products` WRITE;
/*!40000 ALTER TABLE `shop_products` DISABLE KEYS */;
/*!40000 ALTER TABLE `shop_products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_reviews`
--

DROP TABLE IF EXISTS `shop_reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `shop_reviews` (
  `review_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL COMMENT 'Ürün ID - shop_products ilişkisi',
  `customer_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Müşteri ID - shop_customers ilişkisi',
  `order_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Sipariş ID - shop_orders ilişkisi (doğrulanmış alıcı)',
  `reviewer_name` varchar(255) NOT NULL COMMENT 'Yorumcu adı',
  `reviewer_email` varchar(255) NOT NULL COMMENT 'Yorumcu e-posta',
  `title` varchar(255) DEFAULT NULL COMMENT 'Yorum başlığı',
  `comment` text NOT NULL COMMENT 'Yorum metni',
  `rating` int(11) NOT NULL COMMENT 'Puan (1-5 arası)',
  `rating_quality` int(11) DEFAULT NULL COMMENT 'Kalite puanı (1-5)',
  `rating_value` int(11) DEFAULT NULL COMMENT 'Fiyat/Performans puanı (1-5)',
  `rating_delivery` int(11) DEFAULT NULL COMMENT 'Teslimat puanı (1-5)',
  `images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Yorum görselleri (JSON array - dosya yolları)' CHECK (json_valid(`images`)),
  `is_verified_purchase` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Doğrulanmış alıcı mı?',
  `status` enum('pending','approved','rejected','spam') NOT NULL DEFAULT 'pending' COMMENT 'Durum: pending=Onay bekliyor, approved=Onaylandı, rejected=Reddedildi, spam=Spam',
  `moderated_by_user_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Onaylayan/Reddeden admin ID',
  `moderated_at` timestamp NULL DEFAULT NULL COMMENT 'Onay/Red tarihi',
  `moderation_notes` text DEFAULT NULL COMMENT 'Moderasyon notları',
  `helpful_count` int(11) NOT NULL DEFAULT 0 COMMENT 'Yardımcı oldu sayısı',
  `not_helpful_count` int(11) NOT NULL DEFAULT 0 COMMENT 'Yardımcı olmadı sayısı',
  `admin_reply` text DEFAULT NULL COMMENT 'Admin yanıtı',
  `admin_replied_at` timestamp NULL DEFAULT NULL COMMENT 'Admin yanıt tarihi',
  `ip_address` varchar(45) DEFAULT NULL COMMENT 'IP adresi',
  `user_agent` text DEFAULT NULL COMMENT 'Tarayıcı bilgisi',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Soft delete için silinme tarihi',
  PRIMARY KEY (`review_id`),
  KEY `shop_reviews_created_at_index` (`created_at`),
  KEY `shop_reviews_updated_at_index` (`updated_at`),
  KEY `shop_reviews_deleted_at_index` (`deleted_at`),
  KEY `shop_reviews_product_id_index` (`product_id`),
  KEY `shop_reviews_customer_id_index` (`customer_id`),
  KEY `shop_reviews_order_id_index` (`order_id`),
  KEY `shop_reviews_status_index` (`status`),
  KEY `shop_reviews_rating_index` (`rating`),
  KEY `shop_reviews_is_verified_purchase_index` (`is_verified_purchase`),
  KEY `shop_reviews_product_id_status_index` (`product_id`,`status`),
  KEY `shop_reviews_product_id_rating_index` (`product_id`,`rating`),
  CONSTRAINT `shop_reviews_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `shop_customers` (`customer_id`) ON DELETE CASCADE,
  CONSTRAINT `shop_reviews_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `shop_orders` (`order_id`) ON DELETE CASCADE,
  CONSTRAINT `shop_reviews_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `shop_products` (`product_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Ürün yorumları ve değerlendirmeleri - Müşteri görüşleri';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_reviews`
--

LOCK TABLES `shop_reviews` WRITE;
/*!40000 ALTER TABLE `shop_reviews` DISABLE KEYS */;
/*!40000 ALTER TABLE `shop_reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_settings`
--

DROP TABLE IF EXISTS `shop_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `shop_settings` (
  `setting_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `group` varchar(255) NOT NULL COMMENT 'Ayar grubu (general, shipping, payment, email, seo)',
  `key` varchar(255) NOT NULL COMMENT 'Ayar anahtarı (store_name, default_currency)',
  `value` text DEFAULT NULL COMMENT 'Ayar değeri',
  `value_type` enum('string','text','integer','decimal','boolean','json','array') NOT NULL DEFAULT 'string' COMMENT 'Değer tipi',
  `is_multilingual` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Çoklu dil desteği var mı?',
  `multilingual_value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Çoklu dil değeri (JSON)' CHECK (json_valid(`multilingual_value`)),
  `label` varchar(255) DEFAULT NULL COMMENT 'Ayar etiketi (gösterim için)',
  `description` text DEFAULT NULL COMMENT 'Ayar açıklaması',
  `validation_rules` varchar(255) DEFAULT NULL COMMENT 'Validasyon kuralları (required|email|min:3)',
  `options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Seçenekler (select/radio için - JSON array)' CHECK (json_valid(`options`)),
  `sort_order` int(11) NOT NULL DEFAULT 0 COMMENT 'Sıralama düzeni',
  `is_visible` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Admin panelde görünsün mü?',
  `is_editable` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Düzenlenebilir mi?',
  `is_cached` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Cache''lensin mi?',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`setting_id`),
  UNIQUE KEY `unique_group_key` (`group`,`key`),
  KEY `shop_settings_created_at_index` (`created_at`),
  KEY `shop_settings_updated_at_index` (`updated_at`),
  KEY `shop_settings_group_index` (`group`),
  KEY `shop_settings_key_index` (`key`),
  KEY `shop_settings_is_visible_index` (`is_visible`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Ayarlar - Sistem ayarları ve konfigürasyonlar (key-value store)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_settings`
--

LOCK TABLES `shop_settings` WRITE;
/*!40000 ALTER TABLE `shop_settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `shop_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_stock_movements`
--

DROP TABLE IF EXISTS `shop_stock_movements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `shop_stock_movements` (
  `stock_movement_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL COMMENT 'Ürün ID - shop_products ilişkisi',
  `product_variant_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Varyant ID - shop_product_variants ilişkisi',
  `warehouse_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Depo ID - shop_warehouses ilişkisi',
  `movement_type` enum('in','out','transfer','adjustment','return','damage','theft','count') NOT NULL COMMENT 'Hareket tipi',
  `reason` enum('purchase','sale','return','production','damage','adjustment','transfer','initial','count','other') NOT NULL COMMENT 'Hareket nedeni',
  `reference_type` varchar(255) DEFAULT NULL COMMENT 'Referans tipi (Order, Purchase, Transfer)',
  `reference_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Referans ID (ilgili sipariş/satınalma ID)',
  `quantity` int(11) NOT NULL COMMENT 'Miktar (+ veya - olabilir)',
  `quantity_before` int(11) NOT NULL DEFAULT 0 COMMENT 'Hareket öncesi stok',
  `quantity_after` int(11) NOT NULL DEFAULT 0 COMMENT 'Hareket sonrası stok',
  `unit_cost` decimal(12,2) DEFAULT NULL COMMENT 'Birim maliyet (₺)',
  `total_cost` decimal(14,2) DEFAULT NULL COMMENT 'Toplam maliyet (₺)',
  `from_warehouse_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Kaynak depo ID',
  `to_warehouse_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Hedef depo ID',
  `created_by_user_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Hareketi yapan kullanıcı ID',
  `notes` text DEFAULT NULL COMMENT 'Notlar',
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Ek veriler (JSON)' CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`stock_movement_id`),
  KEY `shop_stock_movements_created_at_index` (`created_at`),
  KEY `shop_stock_movements_updated_at_index` (`updated_at`),
  KEY `shop_stock_movements_product_id_index` (`product_id`),
  KEY `shop_stock_movements_product_variant_id_index` (`product_variant_id`),
  KEY `shop_stock_movements_warehouse_id_index` (`warehouse_id`),
  KEY `shop_stock_movements_movement_type_index` (`movement_type`),
  KEY `shop_stock_movements_reason_index` (`reason`),
  KEY `shop_stock_movements_reference_type_reference_id_index` (`reference_type`,`reference_id`),
  KEY `shop_stock_movements_product_id_created_at_index` (`product_id`,`created_at`),
  KEY `shop_stock_movements_from_warehouse_id_foreign` (`from_warehouse_id`),
  KEY `shop_stock_movements_to_warehouse_id_foreign` (`to_warehouse_id`),
  CONSTRAINT `shop_stock_movements_from_warehouse_id_foreign` FOREIGN KEY (`from_warehouse_id`) REFERENCES `shop_warehouses` (`warehouse_id`) ON DELETE CASCADE,
  CONSTRAINT `shop_stock_movements_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `shop_products` (`product_id`) ON DELETE CASCADE,
  CONSTRAINT `shop_stock_movements_product_variant_id_foreign` FOREIGN KEY (`product_variant_id`) REFERENCES `shop_product_variants` (`variant_id`) ON DELETE CASCADE,
  CONSTRAINT `shop_stock_movements_to_warehouse_id_foreign` FOREIGN KEY (`to_warehouse_id`) REFERENCES `shop_warehouses` (`warehouse_id`) ON DELETE CASCADE,
  CONSTRAINT `shop_stock_movements_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `shop_warehouses` (`warehouse_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Stok hareketleri - Tüm stok giriş/çıkış/transfer kayıtları (audit trail)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_stock_movements`
--

LOCK TABLES `shop_stock_movements` WRITE;
/*!40000 ALTER TABLE `shop_stock_movements` DISABLE KEYS */;
/*!40000 ALTER TABLE `shop_stock_movements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_subscription_plans`
--

DROP TABLE IF EXISTS `shop_subscription_plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `shop_subscription_plans` (
  `subscription_plan_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'Plan adı ({"tr":"Altın Paket","en":"Gold Package"})' CHECK (json_valid(`title`)),
  `slug` varchar(255) NOT NULL COMMENT 'URL-dostu slug (altin-paket)',
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Açıklama (JSON çoklu dil)' CHECK (json_valid(`description`)),
  `features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Özellikler listesi (JSON array çoklu dil)' CHECK (json_valid(`features`)),
  `price_daily` decimal(10,2) DEFAULT NULL COMMENT 'Günlük fiyat (₺)',
  `price_weekly` decimal(10,2) DEFAULT NULL COMMENT 'Haftalık fiyat (₺)',
  `price_monthly` decimal(12,2) DEFAULT NULL COMMENT 'Aylık fiyat (₺)',
  `price_quarterly` decimal(12,2) DEFAULT NULL COMMENT '3 aylık fiyat (₺)',
  `price_yearly` decimal(12,2) DEFAULT NULL COMMENT 'Yıllık fiyat (₺)',
  `currency` varchar(3) NOT NULL DEFAULT 'TRY' COMMENT 'Para birimi (TRY, USD, EUR)',
  `has_trial` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Deneme süresi var mı?',
  `trial_days` int(11) NOT NULL DEFAULT 0 COMMENT 'Deneme süresi (gün)',
  `requires_payment_method` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Deneme için ödeme yöntemi gerekli mi?',
  `max_products` int(11) DEFAULT NULL COMMENT 'Maximum ürün sayısı (null ise sınırsız)',
  `max_orders` int(11) DEFAULT NULL COMMENT 'Aylık maximum sipariş sayısı',
  `max_storage_mb` int(11) DEFAULT NULL COMMENT 'Depolama alanı (MB)',
  `custom_limits` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Özel limitler (JSON)' CHECK (json_valid(`custom_limits`)),
  `has_analytics` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Analitik var mı?',
  `has_priority_support` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Öncelikli destek var mı?',
  `has_api_access` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'API erişimi var mı?',
  `enabled_features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Aktif özellikler (JSON array)' CHECK (json_valid(`enabled_features`)),
  `default_billing_cycle` enum('daily','weekly','monthly','quarterly','yearly') NOT NULL DEFAULT 'monthly' COMMENT 'Varsayılan faturalama döngüsü',
  `sort_order` int(11) NOT NULL DEFAULT 0 COMMENT 'Sıralama düzeni',
  `is_featured` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Öne çıkan plan mı?',
  `is_popular` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Popüler plan mı?',
  `badge_text` varchar(255) DEFAULT NULL COMMENT 'Rozet metni (En Popüler, Önerilen)',
  `highlight_color` varchar(7) DEFAULT NULL COMMENT 'Vurgu rengi (#FF5733)',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Aktif/Pasif durumu',
  `is_public` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Herkese açık mı?',
  `subscribers_count` int(11) NOT NULL DEFAULT 0 COMMENT 'Abone sayısı (cache)',
  `terms` text DEFAULT NULL COMMENT 'Kullanım şartları',
  `notes` text DEFAULT NULL COMMENT 'Admin notları',
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Ek veriler (JSON)' CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Soft delete için silinme tarihi',
  PRIMARY KEY (`subscription_plan_id`),
  UNIQUE KEY `shop_subscription_plans_slug_unique` (`slug`),
  KEY `shop_subscription_plans_created_at_index` (`created_at`),
  KEY `shop_subscription_plans_updated_at_index` (`updated_at`),
  KEY `shop_subscription_plans_deleted_at_index` (`deleted_at`),
  KEY `shop_subscription_plans_slug_index` (`slug`),
  KEY `shop_subscription_plans_is_active_index` (`is_active`),
  KEY `shop_subscription_plans_is_public_index` (`is_public`),
  KEY `shop_subscription_plans_is_featured_index` (`is_featured`),
  KEY `shop_subscription_plans_sort_order_index` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Abonelik planları - Farklı abonelik paketleri (Temel, Profesyonel, Kurumsal)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_subscription_plans`
--

LOCK TABLES `shop_subscription_plans` WRITE;
/*!40000 ALTER TABLE `shop_subscription_plans` DISABLE KEYS */;
/*!40000 ALTER TABLE `shop_subscription_plans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_subscriptions`
--

DROP TABLE IF EXISTS `shop_subscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `shop_subscriptions` (
  `subscription_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) unsigned NOT NULL COMMENT 'Müşteri ID - shop_customers ilişkisi',
  `plan_id` bigint(20) unsigned NOT NULL COMMENT 'Plan ID - shop_subscription_plans ilişkisi',
  `subscription_number` varchar(255) NOT NULL COMMENT 'Abonelik numarası (SUB-2024-00001)',
  `status` enum('active','paused','cancelled','expired','trial','pending_payment') NOT NULL DEFAULT 'pending_payment' COMMENT 'Durum: active=Aktif, paused=Duraklatıldı, cancelled=İptal edildi, expired=Süresi doldu, trial=Deneme, pending_payment=Ödeme bekliyor',
  `billing_cycle` enum('daily','weekly','monthly','quarterly','yearly') NOT NULL DEFAULT 'monthly' COMMENT 'Faturalama döngüsü: daily=Günlük, weekly=Haftalık, monthly=Aylık, quarterly=3 aylık, yearly=Yıllık',
  `price_per_cycle` decimal(12,2) NOT NULL COMMENT 'Döngü başına fiyat (₺)',
  `currency` varchar(3) NOT NULL DEFAULT 'TRY' COMMENT 'Para birimi (TRY, USD, EUR)',
  `has_trial` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Deneme süresi var mı?',
  `trial_days` int(11) NOT NULL DEFAULT 0 COMMENT 'Deneme süresi (gün)',
  `trial_ends_at` timestamp NULL DEFAULT NULL COMMENT 'Deneme bitiş tarihi',
  `started_at` timestamp NULL DEFAULT NULL COMMENT 'Başlangıç tarihi',
  `current_period_start` timestamp NULL DEFAULT NULL COMMENT 'Mevcut dönem başlangıcı',
  `current_period_end` timestamp NULL DEFAULT NULL COMMENT 'Mevcut dönem bitişi',
  `next_billing_date` timestamp NULL DEFAULT NULL COMMENT 'Sonraki faturalama tarihi',
  `cancelled_at` timestamp NULL DEFAULT NULL COMMENT 'İptal tarihi',
  `expires_at` timestamp NULL DEFAULT NULL COMMENT 'Son kullanma tarihi',
  `payment_method_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Ödeme yöntemi ID',
  `auto_renew` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Otomatik yenilensin mi?',
  `billing_cycles_completed` int(11) NOT NULL DEFAULT 0 COMMENT 'Tamamlanan faturalama döngüsü sayısı',
  `total_paid` decimal(14,2) NOT NULL DEFAULT 0.00 COMMENT 'Toplam ödenen (₺)',
  `cancellation_reason` text DEFAULT NULL COMMENT 'İptal nedeni',
  `cancellation_feedback` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'İptal geri bildirimi (JSON)' CHECK (json_valid(`cancellation_feedback`)),
  `notes` text DEFAULT NULL COMMENT 'Notlar',
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Ek veriler (JSON)' CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Soft delete için silinme tarihi',
  PRIMARY KEY (`subscription_id`),
  UNIQUE KEY `shop_subscriptions_subscription_number_unique` (`subscription_number`),
  KEY `shop_subscriptions_created_at_index` (`created_at`),
  KEY `shop_subscriptions_updated_at_index` (`updated_at`),
  KEY `shop_subscriptions_deleted_at_index` (`deleted_at`),
  KEY `shop_subscriptions_customer_id_index` (`customer_id`),
  KEY `shop_subscriptions_plan_id_index` (`plan_id`),
  KEY `shop_subscriptions_subscription_number_index` (`subscription_number`),
  KEY `shop_subscriptions_status_index` (`status`),
  KEY `shop_subscriptions_next_billing_date_index` (`next_billing_date`),
  KEY `shop_subscriptions_expires_at_index` (`expires_at`),
  KEY `shop_subscriptions_customer_id_status_index` (`customer_id`,`status`),
  KEY `shop_subscriptions_payment_method_id_foreign` (`payment_method_id`),
  CONSTRAINT `shop_subscriptions_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `shop_customers` (`customer_id`) ON DELETE CASCADE,
  CONSTRAINT `shop_subscriptions_payment_method_id_foreign` FOREIGN KEY (`payment_method_id`) REFERENCES `shop_payment_methods` (`payment_method_id`) ON DELETE CASCADE,
  CONSTRAINT `shop_subscriptions_plan_id_foreign` FOREIGN KEY (`plan_id`) REFERENCES `shop_subscription_plans` (`subscription_plan_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Abonelikler - Müşteri abonelik kayıtları';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_subscriptions`
--

LOCK TABLES `shop_subscriptions` WRITE;
/*!40000 ALTER TABLE `shop_subscriptions` DISABLE KEYS */;
/*!40000 ALTER TABLE `shop_subscriptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_tax_rates`
--

DROP TABLE IF EXISTS `shop_tax_rates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `shop_tax_rates` (
  `tax_rate_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tax_id` bigint(20) unsigned NOT NULL COMMENT 'Vergi ID - shop_taxes ilişkisi',
  `country_code` varchar(2) NOT NULL COMMENT 'Ülke kodu (ISO 3166-1 alpha-2: TR, US, DE)',
  `state_code` varchar(255) DEFAULT NULL COMMENT 'Eyalet/İl kodu (California: CA, İstanbul: 34)',
  `city` varchar(255) DEFAULT NULL COMMENT 'Şehir/İlçe',
  `postal_code` varchar(10) DEFAULT NULL COMMENT 'Posta kodu',
  `rate` decimal(5,2) NOT NULL COMMENT 'Vergi oranı (%) - bu bölge için özel oran',
  `priority` int(11) NOT NULL DEFAULT 0 COMMENT 'Öncelik (birden fazla kural eşleşirse hangisi uygulanacak)',
  `valid_from` timestamp NULL DEFAULT NULL COMMENT 'Geçerlilik başlangıç tarihi',
  `valid_until` timestamp NULL DEFAULT NULL COMMENT 'Geçerlilik bitiş tarihi',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Aktif/Pasif durumu',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`tax_rate_id`),
  KEY `shop_tax_rates_created_at_index` (`created_at`),
  KEY `shop_tax_rates_updated_at_index` (`updated_at`),
  KEY `shop_tax_rates_tax_id_index` (`tax_id`),
  KEY `shop_tax_rates_country_code_index` (`country_code`),
  KEY `shop_tax_rates_state_code_index` (`state_code`),
  KEY `shop_tax_rates_postal_code_index` (`postal_code`),
  KEY `shop_tax_rates_is_active_index` (`is_active`),
  KEY `shop_tax_rates_country_code_state_code_index` (`country_code`,`state_code`),
  KEY `shop_tax_rates_valid_from_valid_until_index` (`valid_from`,`valid_until`),
  CONSTRAINT `shop_tax_rates_tax_id_foreign` FOREIGN KEY (`tax_id`) REFERENCES `shop_taxes` (`tax_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Vergi oranları - Bölge bazlı farklı vergi oranları';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_tax_rates`
--

LOCK TABLES `shop_tax_rates` WRITE;
/*!40000 ALTER TABLE `shop_tax_rates` DISABLE KEYS */;
/*!40000 ALTER TABLE `shop_tax_rates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_taxes`
--

DROP TABLE IF EXISTS `shop_taxes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `shop_taxes` (
  `tax_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'Vergi adı ({"tr":"KDV %18","en":"VAT 18%"})' CHECK (json_valid(`title`)),
  `code` varchar(255) NOT NULL COMMENT 'Vergi kodu (VAT18)',
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Açıklama (JSON çoklu dil)' CHECK (json_valid(`description`)),
  `rate` decimal(5,2) NOT NULL COMMENT 'Vergi oranı (%) - 18, 8, 1, 0',
  `tax_type` enum('vat','sales_tax','service_tax','excise','other') NOT NULL DEFAULT 'vat' COMMENT 'Vergi tipi: vat=KDV, sales_tax=Satış vergisi, service_tax=Hizmet vergisi, excise=ÖTV, other=Diğer',
  `applies_to` enum('products','shipping','both') NOT NULL DEFAULT 'products' COMMENT 'Nerelere uygulanır: products=Ürünler, shipping=Kargo, both=Her ikisi',
  `is_compound` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Bileşik vergi mi? (diğer vergilerin üzerine uygulanır)',
  `country_codes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Geçerli ülkeler (JSON array - ["TR","DE"])' CHECK (json_valid(`country_codes`)),
  `excluded_regions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Hariç tutulan bölgeler (JSON array)' CHECK (json_valid(`excluded_regions`)),
  `priority` int(11) NOT NULL DEFAULT 0 COMMENT 'Öncelik (düşük değer önce uygulanır)',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Aktif/Pasif durumu',
  `sort_order` int(11) NOT NULL DEFAULT 0 COMMENT 'Sıralama düzeni',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Soft delete için silinme tarihi',
  PRIMARY KEY (`tax_id`),
  UNIQUE KEY `shop_taxes_code_unique` (`code`),
  KEY `shop_taxes_created_at_index` (`created_at`),
  KEY `shop_taxes_updated_at_index` (`updated_at`),
  KEY `shop_taxes_deleted_at_index` (`deleted_at`),
  KEY `shop_taxes_code_index` (`code`),
  KEY `shop_taxes_tax_type_index` (`tax_type`),
  KEY `shop_taxes_is_active_index` (`is_active`),
  KEY `shop_taxes_rate_index` (`rate`),
  KEY `shop_taxes_priority_index` (`priority`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Vergiler - KDV, ÖTV ve diğer vergi tanımları';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_taxes`
--

LOCK TABLES `shop_taxes` WRITE;
/*!40000 ALTER TABLE `shop_taxes` DISABLE KEYS */;
/*!40000 ALTER TABLE `shop_taxes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_warehouses`
--

DROP TABLE IF EXISTS `shop_warehouses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `shop_warehouses` (
  `warehouse_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'Depo adı ({"tr":"Ana Depo","en":"Main Warehouse"})' CHECK (json_valid(`title`)),
  `code` varchar(255) NOT NULL COMMENT 'Depo kodu (WH-001)',
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Açıklama (JSON çoklu dil)' CHECK (json_valid(`description`)),
  `warehouse_type` enum('main','branch','virtual','supplier','return','damaged') NOT NULL DEFAULT 'main' COMMENT 'Depo tipi: main=Ana depo, branch=Şube, virtual=Sanal (dropshipping), supplier=Tedarikçi, return=İade, damaged=Hasarlı ürünler',
  `contact_person` varchar(255) DEFAULT NULL COMMENT 'Yetkili kişi',
  `phone` varchar(255) DEFAULT NULL COMMENT 'Telefon numarası',
  `email` varchar(255) DEFAULT NULL COMMENT 'E-posta adresi',
  `address_line_1` text DEFAULT NULL COMMENT 'Adres satırı 1',
  `address_line_2` text DEFAULT NULL COMMENT 'Adres satırı 2',
  `city` varchar(255) DEFAULT NULL COMMENT 'İl/Şehir',
  `postal_code` varchar(10) DEFAULT NULL COMMENT 'Posta kodu',
  `country_code` varchar(2) NOT NULL DEFAULT 'TR' COMMENT 'Ülke kodu (ISO 3166-1 alpha-2)',
  `latitude` decimal(10,8) DEFAULT NULL COMMENT 'Enlem (GPS koordinatı)',
  `longitude` decimal(11,8) DEFAULT NULL COMMENT 'Boylam (GPS koordinatı)',
  `total_area` decimal(10,2) DEFAULT NULL COMMENT 'Toplam alan (m²)',
  `total_capacity` int(11) DEFAULT NULL COMMENT 'Toplam kapasite (ürün adedi)',
  `used_capacity` int(11) NOT NULL DEFAULT 0 COMMENT 'Kullanılan kapasite',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Aktif/Pasif durumu',
  `is_default` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Varsayılan depo mu?',
  `allow_backorders` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Ön sipariş kabul eder mi?',
  `allow_shipping` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Buradan sevkiyat yapılabilir mi?',
  `allow_pickup` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Müşteri teslim alabilir mi?',
  `operating_hours` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Çalışma saatleri (JSON - {"monday":"09:00-18:00"})' CHECK (json_valid(`operating_hours`)),
  `priority` int(11) NOT NULL DEFAULT 0 COMMENT 'Öncelik sırası (stok tahsisinde kullanılır)',
  `sort_order` int(11) NOT NULL DEFAULT 0 COMMENT 'Sıralama düzeni',
  `notes` text DEFAULT NULL COMMENT 'Notlar',
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Ek veriler (JSON)' CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Soft delete için silinme tarihi',
  PRIMARY KEY (`warehouse_id`),
  UNIQUE KEY `shop_warehouses_code_unique` (`code`),
  KEY `shop_warehouses_created_at_index` (`created_at`),
  KEY `shop_warehouses_updated_at_index` (`updated_at`),
  KEY `shop_warehouses_deleted_at_index` (`deleted_at`),
  KEY `shop_warehouses_code_index` (`code`),
  KEY `shop_warehouses_warehouse_type_index` (`warehouse_type`),
  KEY `shop_warehouses_is_active_index` (`is_active`),
  KEY `shop_warehouses_is_default_index` (`is_default`),
  KEY `shop_warehouses_priority_index` (`priority`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Depolar - Stok yönetimi için farklı depo/lokasyonlar';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_warehouses`
--

LOCK TABLES `shop_warehouses` WRITE;
/*!40000 ALTER TABLE `shop_warehouses` DISABLE KEYS */;
/*!40000 ALTER TABLE `shop_warehouses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `taggables`
--

DROP TABLE IF EXISTS `taggables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `taggables` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tag_id` bigint(20) unsigned NOT NULL,
  `taggable_type` varchar(255) NOT NULL,
  `taggable_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tenant_taggables_unique` (`tag_id`,`taggable_type`,`taggable_id`),
  KEY `tenant_taggables_taggable_index` (`taggable_type`,`taggable_id`),
  CONSTRAINT `taggables_tag_id_foreign` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`tag_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `taggables`
--

LOCK TABLES `taggables` WRITE;
/*!40000 ALTER TABLE `taggables` DISABLE KEYS */;
/*!40000 ALTER TABLE `taggables` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tags`
--

DROP TABLE IF EXISTS `tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `tags` (
  `tag_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `type` varchar(255) DEFAULT NULL,
  `color` varchar(32) DEFAULT NULL,
  `meta` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`meta`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`tag_id`),
  UNIQUE KEY `tenant_tags_slug_unique` (`slug`),
  KEY `tags_slug_index` (`slug`),
  KEY `tags_type_index` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tags`
--

LOCK TABLES `tags` WRITE;
/*!40000 ALTER TABLE `tags` DISABLE KEYS */;
/*!40000 ALTER TABLE `tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tenant_languages`
--

DROP TABLE IF EXISTS `tenant_languages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `tenant_languages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(10) NOT NULL COMMENT 'Dil kodu: tr, en, de, fr, es, vs...',
  `name` varchar(255) NOT NULL COMMENT 'İngilizce dil adı: Turkish, English, German, French',
  `native_name` varchar(255) NOT NULL COMMENT 'Yerel dil adı: Türkçe, English, Deutsch, Français',
  `direction` enum('ltr','rtl') NOT NULL DEFAULT 'ltr' COMMENT 'Metin yönü: ltr=soldan sağa, rtl=sağdan sola',
  `flag_icon` varchar(255) DEFAULT NULL COMMENT 'Bayrak emoji veya icon kodu',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=Sitede gözükür, 0=Sadece admin panelde hazırlık',
  `is_visible` tinyint(1) NOT NULL DEFAULT 1 COMMENT '3 SEVİYELİ DİL SİSTEMİ: false=Hiçbir yerde gözükmeyen dünya dilleri, true=Admin panelde en azından görünen',
  `is_main_language` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Ana dil kategorisi mi? (visible=false olanlar için)',
  `is_default` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'RTL eklentisinden',
  `is_rtl` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Sağdan sola yazım desteği',
  `flag_emoji` varchar(10) DEFAULT NULL COMMENT 'Flag emoji',
  `url_prefix_mode` enum('none','except_default','all') NOT NULL DEFAULT 'except_default' COMMENT 'URL prefix strategy: none=no prefix, except_default=prefix except default lang, all=prefix for all',
  `sort_order` int(11) NOT NULL DEFAULT 0 COMMENT 'Dil sıralama numarası',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tenant_languages_code_unique` (`code`),
  KEY `tenant_languages_code_is_active_index` (`code`,`is_active`),
  KEY `tenant_languages_sort_order_index` (`sort_order`)
) ENGINE=InnoDB AUTO_INCREMENT=301 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tenant_languages`
--

LOCK TABLES `tenant_languages` WRITE;
/*!40000 ALTER TABLE `tenant_languages` DISABLE KEYS */;
INSERT INTO `tenant_languages` VALUES
(201,'tr','Türkçe','Türkçe','ltr','🇹🇷',1,1,1,1,0,NULL,'except_default',1,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(202,'en','English','English','ltr','🇬🇧',0,0,1,0,0,NULL,'except_default',2,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(203,'ar','Arapça','العربية','rtl','🇸🇦',0,0,1,0,0,NULL,'except_default',3,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(204,'es','İspanyolca','Español','ltr','🇪🇸',0,0,1,0,0,NULL,'except_default',3,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(205,'fr','Fransızca','Français','ltr','🇫🇷',0,0,1,0,0,NULL,'except_default',4,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(206,'de','Almanca','Deutsch','ltr','🇩🇪',0,0,1,0,0,NULL,'except_default',5,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(207,'it','İtalyanca','Italiano','ltr','🇮🇹',0,0,1,0,0,NULL,'except_default',6,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(208,'pt','Portekizce','Português','ltr','🇵🇹',0,0,1,0,0,NULL,'except_default',7,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(209,'ru','Rusça','Русский','ltr','🇷🇺',0,0,1,0,0,NULL,'except_default',8,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(210,'zh','Çince','中文','ltr','🇨🇳',0,0,1,0,0,NULL,'except_default',9,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(211,'ja','Japonca','日本語','ltr','🇯🇵',0,0,1,0,0,NULL,'except_default',10,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(212,'ko','Korece','한국어','ltr','🇰🇷',0,0,1,0,0,NULL,'except_default',12,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(213,'nl','Hollandaca','Nederlands','ltr','🇳🇱',0,0,1,0,0,NULL,'except_default',13,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(214,'pl','Lehçe','Polski','ltr','🇵🇱',0,0,1,0,0,NULL,'except_default',14,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(215,'sv','İsveççe','Svenska','ltr','🇸🇪',0,0,1,0,0,NULL,'except_default',15,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(216,'no','Norveççe','Norsk','ltr','🇳🇴',0,0,1,0,0,NULL,'except_default',16,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(217,'da','Danca','Dansk','ltr','🇩🇰',0,0,1,0,0,NULL,'except_default',17,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(218,'fi','Fince','Suomi','ltr','🇫🇮',0,0,1,0,0,NULL,'except_default',18,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(219,'cs','Çekçe','Čeština','ltr','🇨🇿',0,0,1,0,0,NULL,'except_default',19,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(220,'hu','Macarca','Magyar','ltr','🇭🇺',0,0,1,0,0,NULL,'except_default',20,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(221,'ro','Romence','Română','ltr','🇷🇴',0,0,1,0,0,NULL,'except_default',21,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(222,'el','Yunanca','Ελληνικά','ltr','🇬🇷',0,0,1,0,0,NULL,'except_default',22,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(223,'bg','Bulgarca','Български','ltr','🇧🇬',0,0,1,0,0,NULL,'except_default',23,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(224,'hi','Hintçe','हिन्दी','ltr','🇮🇳',0,0,1,0,0,NULL,'except_default',24,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(225,'fa','Farsça','فارسی','rtl','🇮🇷',0,0,1,0,0,NULL,'except_default',25,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(226,'th','Tayca','ไทย','ltr','🇹🇭',0,0,1,0,0,NULL,'except_default',26,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(227,'vi','Vietnamca','Tiếng Việt','ltr','🇻🇳',0,0,1,0,0,NULL,'except_default',27,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(228,'id','Endonezce','Bahasa Indonesia','ltr','🇮🇩',0,0,1,0,0,NULL,'except_default',28,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(229,'ms','Malayca','Bahasa Melayu','ltr','🇲🇾',0,0,1,0,0,NULL,'except_default',29,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(230,'he','İbranice','עברית','rtl','🇮🇱',0,0,1,0,0,NULL,'except_default',30,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(231,'uk','Ukraynaca','Українська','ltr','🇺🇦',0,0,1,0,0,NULL,'except_default',31,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(232,'bn','Bengalce','বাংলা','ltr','🇧🇩',0,0,1,0,0,NULL,'except_default',32,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(233,'ur','Urduca','اردو','rtl','🇵🇰',0,0,1,0,0,NULL,'except_default',33,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(234,'sw','Swahili','Kiswahili','ltr','🇰🇪',0,0,1,0,0,NULL,'except_default',34,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(235,'hr','Hırvatça','Hrvatski','ltr','🇭🇷',0,0,1,0,0,NULL,'except_default',35,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(236,'sk','Slovakça','Slovenčina','ltr','🇸🇰',0,0,1,0,0,NULL,'except_default',36,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(237,'sl','Slovence','Slovenščina','ltr','🇸🇮',0,0,1,0,0,NULL,'except_default',37,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(238,'lt','Litvanca','Lietuvių','ltr','🇱🇹',0,0,1,0,0,NULL,'except_default',38,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(239,'lv','Letonca','Latviešu','ltr','🇱🇻',0,0,1,0,0,NULL,'except_default',39,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(240,'et','Estonca','Eesti','ltr','🇪🇪',0,0,1,0,0,NULL,'except_default',40,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(241,'ca','Katalanca','Català','ltr','🇪🇸',0,0,1,0,0,NULL,'except_default',41,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(242,'af','Afrikaans','Afrikaans','ltr','🇿🇦',0,0,1,0,0,NULL,'except_default',42,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(243,'tl','Filipino','Filipino','ltr','🇵🇭',0,0,1,0,0,NULL,'except_default',43,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(244,'az','Azerice','Azərbaycan dili','ltr','🇦🇿',0,0,1,0,0,NULL,'except_default',44,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(245,'sr','Sırpça','Српски','ltr','🇷🇸',0,0,0,0,0,NULL,'except_default',45,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(246,'eu','Baskça','Euskara','ltr','🇪🇸',0,0,0,0,0,NULL,'except_default',46,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(247,'ga','İrlandaca','Gaeilge','ltr','🇮🇪',0,0,0,0,0,NULL,'except_default',47,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(248,'cy','Galce','Cymraeg','ltr','🏴',0,0,0,0,0,NULL,'except_default',48,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(249,'is','İzlandaca','Íslenska','ltr','🇮🇸',0,0,0,0,0,NULL,'except_default',49,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(250,'mt','Maltaca','Malti','ltr','🇲🇹',0,0,0,0,0,NULL,'except_default',50,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(251,'sq','Arnavutça','Shqip','ltr','🇦🇱',0,0,0,0,0,NULL,'except_default',51,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(252,'mk','Makedonca','Македонски','ltr','🇲🇰',0,0,0,0,0,NULL,'except_default',52,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(253,'hy','Ermenice','Հայերեն','ltr','🇦🇲',0,0,0,0,0,NULL,'except_default',53,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(254,'ka','Gürcüce','ქართული','ltr','🇬🇪',0,0,0,0,0,NULL,'except_default',54,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(255,'be','Belarusça','беларуская','ltr','🇧🇾',0,0,0,0,0,NULL,'except_default',55,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(256,'zu','Zulu','isiZulu','ltr','🇿🇦',0,0,0,0,0,NULL,'except_default',56,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(257,'xh','Xhosa','isiXhosa','ltr','🇿🇦',0,0,0,0,0,NULL,'except_default',57,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(258,'am','Amharic','አማርኛ','ltr','🇪🇹',0,0,0,0,0,NULL,'except_default',58,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(259,'ig','Igbo','Asụsụ Igbo','ltr','🇳🇬',0,0,0,0,0,NULL,'except_default',59,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(260,'yo','Yoruba','Yorùbá','ltr','🇳🇬',0,0,0,0,0,NULL,'except_default',60,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(261,'ha','Hausa','Harshen Hausa','ltr','🇳🇬',0,0,0,0,0,NULL,'except_default',61,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(262,'ta','Tamil','தமிழ்','ltr','🇮🇳',0,0,0,0,0,NULL,'except_default',62,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(263,'te','Telugu','తెలుగు','ltr','🇮🇳',0,0,0,0,0,NULL,'except_default',63,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(264,'ml','Malayalam','മലയാളം','ltr','🇮🇳',0,0,0,0,0,NULL,'except_default',64,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(265,'kn','Kannada','ಕನ್ನಡ','ltr','🇮🇳',0,0,0,0,0,NULL,'except_default',65,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(266,'gu','Gujarati','ગુજરાતી','ltr','🇮🇳',0,0,0,0,0,NULL,'except_default',66,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(267,'pa','Punjabi','ਪੰਜਾਬੀ','ltr','🇮🇳',0,0,0,0,0,NULL,'except_default',67,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(268,'mr','Marathi','मराठी','ltr','🇮🇳',0,0,0,0,0,NULL,'except_default',68,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(269,'ne','Nepali','नेपाली','ltr','🇳🇵',0,0,0,0,0,NULL,'except_default',69,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(270,'si','Sinhala','සිංහල','ltr','🇱🇰',0,0,0,0,0,NULL,'except_default',70,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(271,'my','Burmese','မြန်မာ','ltr','🇲🇲',0,0,0,0,0,NULL,'except_default',71,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(272,'km','Khmer','ភាសាខ្មែរ','ltr','🇰🇭',0,0,0,0,0,NULL,'except_default',72,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(273,'lo','Lao','ພາសາລາວ','ltr','🇱🇦',0,0,0,0,0,NULL,'except_default',73,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(274,'mn','Mongolian','Монгол','ltr','🇲🇳',0,0,0,0,0,NULL,'except_default',74,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(275,'uz','Uzbek','Oʻzbek','ltr','🇺🇿',0,0,0,0,0,NULL,'except_default',75,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(276,'kk','Kazakh','Қазақ','ltr','🇰🇿',0,0,0,0,0,NULL,'except_default',76,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(277,'ky','Kyrgyz','Кыргыз','ltr','🇰🇬',0,0,0,0,0,NULL,'except_default',77,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(278,'tg','Tajik','Тоҷикӣ','ltr','🇹🇯',0,0,0,0,0,NULL,'except_default',78,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(279,'tk','Turkmen','Türkmen','ltr','🇹🇲',0,0,0,0,0,NULL,'except_default',79,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(280,'jv','Javanese','basa Jawa','ltr','🇮🇩',0,0,0,0,0,NULL,'except_default',80,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(281,'su','Sundanese','basa Sunda','ltr','🇮🇩',0,0,0,0,0,NULL,'except_default',81,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(282,'ceb','Cebuano','Sinugboanon','ltr','🇵🇭',0,0,0,0,0,NULL,'except_default',82,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(283,'ku','Kurdish','Kurdî','ltr','🇹🇷',0,0,0,0,0,NULL,'except_default',83,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(284,'ckb','Central Kurdish','کوردی','rtl','🇮🇶',0,0,0,0,0,NULL,'except_default',84,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(285,'ps','Pashto','پښتو','rtl','🇦🇫',0,0,0,0,0,NULL,'except_default',85,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(286,'sd','Sindhi','سنڌي','rtl','🇵🇰',0,0,0,0,0,NULL,'except_default',86,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(287,'qu','Quechua','Runa Simi','ltr','🇵🇪',0,0,0,0,0,NULL,'except_default',87,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(288,'gn','Guarani','Avañeẽ','ltr','🇵🇾',0,0,0,0,0,NULL,'except_default',88,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(289,'ht','Haiti Kreolü','Kreyòl Ayisyen','ltr','🇭🇹',0,0,0,0,0,NULL,'except_default',89,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(290,'ay','Aymara','aymar aru','ltr','🇧🇴',0,0,0,0,0,NULL,'except_default',90,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(291,'mi','Maori','Te Reo Māori','ltr','🇳🇿',0,0,0,0,0,NULL,'except_default',91,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(292,'sm','Samoa','Gagana Samoa','ltr','🇼🇸',0,0,0,0,0,NULL,'except_default',92,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(293,'to','Tonga','Lea Fakatonga','ltr','🇹🇴',0,0,0,0,0,NULL,'except_default',93,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(294,'fj','Fiji','Na Vosa Vakaviti','ltr','🇫🇯',0,0,0,0,0,NULL,'except_default',94,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(295,'eo','Esperanto','Esperanto','ltr','🌍',0,0,0,0,0,NULL,'except_default',95,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(296,'ia','Interlingua','Interlingua','ltr','🌐',0,0,0,0,0,NULL,'except_default',96,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(297,'vo','Volapük','Volapük','ltr','🌐',0,0,0,0,0,NULL,'except_default',97,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(298,'la','Latin','Latina','ltr','🏛️',0,0,0,0,0,NULL,'except_default',98,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(299,'sa','Sanskrit','संस्कृत','ltr','🕉️',0,0,0,0,0,NULL,'except_default',99,'2025-10-13 18:13:44','2025-10-13 23:42:06'),
(300,'pi','Pali','पालि','ltr','☸️',0,0,0,0,0,NULL,'except_default',100,'2025-10-13 18:13:44','2025-10-13 23:42:06');
/*!40000 ALTER TABLE `tenant_languages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tenant_usage_logs`
--

DROP TABLE IF EXISTS `tenant_usage_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `tenant_usage_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint(20) unsigned NOT NULL,
  `resource_type` varchar(255) NOT NULL,
  `usage_count` int(11) NOT NULL DEFAULT 0,
  `cpu_usage_percent` decimal(5,2) DEFAULT NULL,
  `memory_usage_mb` bigint(20) DEFAULT NULL,
  `storage_usage_mb` bigint(20) DEFAULT NULL,
  `db_queries` int(11) DEFAULT NULL,
  `api_requests` int(11) DEFAULT NULL,
  `cache_size_mb` bigint(20) DEFAULT NULL,
  `active_connections` int(11) DEFAULT NULL,
  `response_time_ms` decimal(8,2) DEFAULT NULL,
  `additional_metrics` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`additional_metrics`)),
  `status` enum('normal','warning','critical','blocked') NOT NULL DEFAULT 'normal',
  `notes` text DEFAULT NULL,
  `recorded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tenant_usage_logs_tenant_id_recorded_at_index` (`tenant_id`,`recorded_at`),
  KEY `tenant_usage_logs_tenant_id_resource_type_recorded_at_index` (`tenant_id`,`resource_type`,`recorded_at`),
  KEY `tenant_usage_logs_resource_type_status_index` (`resource_type`,`status`),
  KEY `tenant_usage_logs_recorded_at_index` (`recorded_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tenant_usage_logs`
--

LOCK TABLES `tenant_usage_logs` WRITE;
/*!40000 ALTER TABLE `tenant_usage_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `tenant_usage_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tenant_widgets`
--

DROP TABLE IF EXISTS `tenant_widgets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `tenant_widgets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `widget_id` bigint(20) unsigned NOT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`settings`)),
  `display_title` varchar(255) DEFAULT NULL,
  `is_custom` tinyint(1) NOT NULL DEFAULT 0,
  `custom_html` longtext DEFAULT NULL,
  `custom_css` longtext DEFAULT NULL,
  `custom_js` longtext DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tenant_widgets_order_index` (`order`),
  KEY `tenant_widgets_is_active_index` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tenant_widgets`
--

LOCK TABLES `tenant_widgets` WRITE;
/*!40000 ALTER TABLE `tenant_widgets` DISABLE KEYS */;
/*!40000 ALTER TABLE `tenant_widgets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_module_permissions`
--

DROP TABLE IF EXISTS `user_module_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_module_permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `module_name` varchar(50) NOT NULL,
  `permission_type` varchar(20) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ump_user_module_permission_unique` (`user_id`,`module_name`,`permission_type`),
  KEY `user_module_permissions_created_at_index` (`created_at`),
  KEY `user_module_permissions_updated_at_index` (`updated_at`),
  KEY `user_module_permissions_module_name_index` (`module_name`),
  KEY `user_module_permissions_permission_type_index` (`permission_type`),
  KEY `user_module_permissions_is_active_index` (`is_active`),
  CONSTRAINT `user_module_permissions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_module_permissions`
--

LOCK TABLES `user_module_permissions` WRITE;
/*!40000 ALTER TABLE `user_module_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_module_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `admin_locale` varchar(10) DEFAULT NULL,
  `tenant_locale` varchar(5) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_created_at_index` (`created_at`),
  KEY `users_updated_at_index` (`updated_at`),
  KEY `users_admin_locale_index` (`admin_locale`),
  KEY `users_tenant_locale_index` (`tenant_locale`),
  KEY `users_deleted_at_index` (`deleted_at`),
  KEY `users_name_index` (`name`),
  KEY `users_is_active_index` (`is_active`),
  KEY `users_last_login_at_index` (`last_login_at`),
  KEY `users_email_verified_at_index` (`email_verified_at`),
  KEY `users_remember_token_index` (`remember_token`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(1,'Nurullah Okatan','nurullah@nurullah.net',1,'2025-10-13 23:36:35','2025-10-13 18:13:08','$2y$12$w7CEEBKv2EwglgQD1lruO.tgVae3Q.e0f/Nk4DarS5dwltPEuLm8G',NULL,NULL,NULL,'2025-10-13 18:13:08','2025-10-13 23:37:59',NULL),
(2,'Türk Bilişim','info@turkbilisim.com.tr',1,NULL,'2025-10-13 18:13:08','$2y$12$hMgO01Ja3LcMQSX.MwRAQeigATcuQLjvK4vmPBm3jWkbMhsX4i4mi',NULL,NULL,NULL,'2025-10-13 18:13:08','2025-10-13 23:38:13',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `widget_items`
--

DROP TABLE IF EXISTS `widget_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `widget_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tenant_widget_id` bigint(20) unsigned NOT NULL,
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`content`)),
  `order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `widget_items_tenant_widget_id_foreign` (`tenant_widget_id`),
  KEY `widget_items_order_index` (`order`),
  CONSTRAINT `widget_items_tenant_widget_id_foreign` FOREIGN KEY (`tenant_widget_id`) REFERENCES `tenant_widgets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `widget_items`
--

LOCK TABLES `widget_items` WRITE;
/*!40000 ALTER TABLE `widget_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `widget_items` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2025-10-15  3:12:36
