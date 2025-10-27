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
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`id`, `name`, `email`, `phone`, `bio`, `is_active`, `last_login_at`, `email_verified_at`, `password`, `admin_locale`, `tenant_locale`, `dashboard_preferences`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES (1,'Nurullah Okatan','nurullah@nurullah.net',NULL,NULL,1,NULL,'2025-10-05 00:43:11','$2y$12$WOT.c/wggLiV9WJ0JZrQwemjVC84EXWtse6JoOBV4XWJUesDXgU1G',NULL,NULL,NULL,NULL,'2025-10-05 00:43:11','2025-10-05 00:43:11',NULL);
INSERT INTO `users` (`id`, `name`, `email`, `phone`, `bio`, `is_active`, `last_login_at`, `email_verified_at`, `password`, `admin_locale`, `tenant_locale`, `dashboard_preferences`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES (2,'Türk Bilişim','info@turkbilisim.com.tr',NULL,NULL,1,NULL,'2025-10-05 00:43:12','$2y$12$f8kOdkf1Qeuj8VNn.FWqeuszDZNJd8oS5coEaI6grIgopIFrEU8.W',NULL,NULL,NULL,NULL,'2025-10-05 00:43:12','2025-10-05 00:43:12',NULL);
INSERT INTO `users` (`id`, `name`, `email`, `phone`, `bio`, `is_active`, `last_login_at`, `email_verified_at`, `password`, `admin_locale`, `tenant_locale`, `dashboard_preferences`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES (3,'Laravel Admin','laravel@test',NULL,NULL,1,NULL,'2025-10-05 00:43:12','$2y$12$74ySdpzGkXuov44NNdwEdumAX2q/JLV6dFEDoWrvjliON2/dBDte2',NULL,NULL,NULL,NULL,'2025-10-05 00:43:12','2025-10-05 00:43:12',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
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
