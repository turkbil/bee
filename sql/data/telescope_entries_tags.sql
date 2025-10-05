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
-- Dumping data for table `telescope_entries_tags`
--

LOCK TABLES `telescope_entries_tags` WRITE;
/*!40000 ALTER TABLE `telescope_entries_tags` DISABLE KEYS */;
INSERT INTO `telescope_entries_tags` (`entry_uuid`, `tag`) VALUES ('a00a7aae-bb96-47ce-9a8e-5dadcbf6e0dc','App\\Models\\Domain');
INSERT INTO `telescope_entries_tags` (`entry_uuid`, `tag`) VALUES ('a00a7baa-f05a-4c14-a37c-336d9d26e26c','App\\Models\\Domain');
INSERT INTO `telescope_entries_tags` (`entry_uuid`, `tag`) VALUES ('a00a7bbe-5486-48e0-b26a-8862a1a42900','App\\Models\\Domain');
INSERT INTO `telescope_entries_tags` (`entry_uuid`, `tag`) VALUES ('a00a7e1a-2aeb-457e-8c0a-8686a980ac31','App\\Models\\Domain');
INSERT INTO `telescope_entries_tags` (`entry_uuid`, `tag`) VALUES ('a00a7fc0-25ba-4630-8147-32d3e4fae7d6','App\\Models\\Domain');
INSERT INTO `telescope_entries_tags` (`entry_uuid`, `tag`) VALUES ('a00a7aa9-29ce-4269-9df1-e2e65adc4c9c','App\\Models\\Tenant');
INSERT INTO `telescope_entries_tags` (`entry_uuid`, `tag`) VALUES ('a00a7aae-8842-4924-ae5e-998846cff4c3','App\\Models\\Tenant');
INSERT INTO `telescope_entries_tags` (`entry_uuid`, `tag`) VALUES ('a00a7baa-e85d-4c27-8359-3ffe9b66280c','App\\Models\\Tenant');
INSERT INTO `telescope_entries_tags` (`entry_uuid`, `tag`) VALUES ('a00a7bbe-54ec-43c2-be69-91683fedf73a','App\\Models\\Tenant');
INSERT INTO `telescope_entries_tags` (`entry_uuid`, `tag`) VALUES ('a00a7e1a-20f3-4cae-ae35-01af8d184aea','App\\Models\\Tenant');
INSERT INTO `telescope_entries_tags` (`entry_uuid`, `tag`) VALUES ('a00a7fc0-1d5c-4d93-b380-9606095ab740','App\\Models\\Tenant');
INSERT INTO `telescope_entries_tags` (`entry_uuid`, `tag`) VALUES ('a00a81c0-c734-4786-9a3c-e5ebb9713c06','App\\Models\\Tenant');
INSERT INTO `telescope_entries_tags` (`entry_uuid`, `tag`) VALUES ('a00a7aae-8b23-46e0-9dff-d9bee4a87510','Modules\\LanguageManagement\\app\\Models\\TenantLanguage');
INSERT INTO `telescope_entries_tags` (`entry_uuid`, `tag`) VALUES ('a00a7baa-ed57-41f3-a623-ee9d3bdc9010','Modules\\LanguageManagement\\app\\Models\\TenantLanguage');
INSERT INTO `telescope_entries_tags` (`entry_uuid`, `tag`) VALUES ('a00a7e1a-275c-4bcf-b24c-ac21d93ae9c2','Modules\\LanguageManagement\\app\\Models\\TenantLanguage');
INSERT INTO `telescope_entries_tags` (`entry_uuid`, `tag`) VALUES ('a00a7fc0-2264-4f46-912a-9cad82aad672','Modules\\LanguageManagement\\app\\Models\\TenantLanguage');
INSERT INTO `telescope_entries_tags` (`entry_uuid`, `tag`) VALUES ('a00a7aae-9f15-4df0-9d2b-c68e840c2b0d','Modules\\MenuManagement\\App\\Models\\Menu');
INSERT INTO `telescope_entries_tags` (`entry_uuid`, `tag`) VALUES ('a00a7aae-9f7c-4a9e-bf68-6c38f9fc2d54','Modules\\MenuManagement\\App\\Models\\MenuItem');
INSERT INTO `telescope_entries_tags` (`entry_uuid`, `tag`) VALUES ('a00a7a6c-31c0-4c73-8df4-910df4726e83','Modules\\ModuleManagement\\App\\Models\\Module');
INSERT INTO `telescope_entries_tags` (`entry_uuid`, `tag`) VALUES ('a00a7a6c-8dfd-48be-a147-3bc5f495767c','Modules\\ModuleManagement\\App\\Models\\Module');
INSERT INTO `telescope_entries_tags` (`entry_uuid`, `tag`) VALUES ('a00a7a9d-44b2-4199-872b-36efcee2dbcd','Modules\\ModuleManagement\\App\\Models\\Module');
INSERT INTO `telescope_entries_tags` (`entry_uuid`, `tag`) VALUES ('a00a7aae-8948-4c9c-82c0-7aeca59d9171','Modules\\Page\\App\\Models\\Page');
INSERT INTO `telescope_entries_tags` (`entry_uuid`, `tag`) VALUES ('a00a7baa-e91a-4953-b122-a7058ed42106','Modules\\Page\\App\\Models\\Page');
INSERT INTO `telescope_entries_tags` (`entry_uuid`, `tag`) VALUES ('a00a7e1a-21fb-4962-b40b-198e9bb50abe','Modules\\Page\\App\\Models\\Page');
INSERT INTO `telescope_entries_tags` (`entry_uuid`, `tag`) VALUES ('a00a7fc0-1e1a-4156-9d6a-dcab818740c7','Modules\\Page\\App\\Models\\Page');
INSERT INTO `telescope_entries_tags` (`entry_uuid`, `tag`) VALUES ('a00a7aae-8d71-4492-8e3d-644aa247de62','Modules\\SeoManagement\\app\\Models\\SeoSetting');
INSERT INTO `telescope_entries_tags` (`entry_uuid`, `tag`) VALUES ('a00a7aae-8bbd-4489-a5f1-37269acc1f65','Modules\\SettingManagement\\App\\Models\\Setting');
INSERT INTO `telescope_entries_tags` (`entry_uuid`, `tag`) VALUES ('a00a7baa-ebda-4526-98db-ff5fdff89cee','Modules\\SettingManagement\\App\\Models\\Setting');
INSERT INTO `telescope_entries_tags` (`entry_uuid`, `tag`) VALUES ('a00a7bbe-5320-4935-b752-580a3a613e4d','Modules\\SettingManagement\\App\\Models\\Setting');
INSERT INTO `telescope_entries_tags` (`entry_uuid`, `tag`) VALUES ('a00a7e1a-2522-4877-8337-2148eb8d79d0','Modules\\SettingManagement\\App\\Models\\Setting');
INSERT INTO `telescope_entries_tags` (`entry_uuid`, `tag`) VALUES ('a00a7fc0-2085-42f1-baee-b9e105acd1a8','Modules\\SettingManagement\\App\\Models\\Setting');
INSERT INTO `telescope_entries_tags` (`entry_uuid`, `tag`) VALUES ('a00a7aa9-2aaa-4b6c-b17f-3482dc860c1d','Stancl\\Tenancy\\Database\\Models\\Domain');
INSERT INTO `telescope_entries_tags` (`entry_uuid`, `tag`) VALUES ('a00a81c0-c8ab-46f2-8818-4b2deddd28d2','Stancl\\Tenancy\\Database\\Models\\Domain');
INSERT INTO `telescope_entries_tags` (`entry_uuid`, `tag`) VALUES ('a00a81c6-74cc-4bd7-9c33-89146cc013f7','Stancl\\Tenancy\\Database\\Models\\Tenant');
INSERT INTO `telescope_entries_tags` (`entry_uuid`, `tag`) VALUES ('a00a81cc-c675-4719-957b-09585bf7c6b6','Stancl\\Tenancy\\Database\\Models\\Tenant');
/*!40000 ALTER TABLE `telescope_entries_tags` ENABLE KEYS */;
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
