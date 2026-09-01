-- MySQL dump 10.13  Distrib 8.4.9, for Win64 (x86_64)
--
-- Host: localhost    Database: db_emas
-- ------------------------------------------------------
-- Server version	8.4.9

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
-- Table structure for table `activity_logs`
--

DROP TABLE IF EXISTS `activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `action` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'misal: product.updated, transaction.created',
  `model_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'misal: Product, Transaction',
  `model_id` bigint unsigned DEFAULT NULL,
  `old_values` json DEFAULT NULL,
  `new_values` json DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `activity_logs_user_id_action_index` (`user_id`,`action`),
  KEY `activity_logs_model_type_model_id_index` (`model_type`,`model_id`),
  CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_logs`
--

LOCK TABLES `activity_logs` WRITE;
/*!40000 ALTER TABLE `activity_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `activity_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` smallint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Cincin Emas','cincin-emas',NULL,'≡ƒÆì',1,1,'2026-09-01 20:16:32','2026-09-01 20:16:32'),(2,'Kalung Emas','kalung-emas',NULL,'≡ƒô┐',1,2,'2026-09-01 20:16:32','2026-09-01 20:16:32'),(3,'Gelang Emas','gelang-emas',NULL,'ΓîÜ',1,3,'2026-09-01 20:16:32','2026-09-01 20:16:32'),(4,'Anting Emas','anting-emas',NULL,'Γ£¿',1,4,'2026-09-01 20:16:32','2026-09-01 20:16:32');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customer_profiles`
--

DROP TABLE IF EXISTS `customer_profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_profiles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `nik` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nomor KTP',
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('male','female') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `province` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_since` date DEFAULT NULL,
  `segment` enum('new','regular','vip','platinum') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'new',
  `notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Catatan internal admin',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customer_profiles_user_id_unique` (`user_id`),
  UNIQUE KEY `customer_profiles_nik_unique` (`nik`),
  CONSTRAINT `customer_profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customer_profiles`
--

LOCK TABLES `customer_profiles` WRITE;
/*!40000 ALTER TABLE `customer_profiles` DISABLE KEYS */;
INSERT INTO `customer_profiles` VALUES (1,2,NULL,'089876543210',NULL,NULL,'Jl. Pelanggan Setia No 2',NULL,NULL,NULL,NULL,'2026-09-02','new',NULL,'2026-09-01 20:16:31','2026-09-01 20:16:31');
/*!40000 ALTER TABLE `customer_profiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customer_rewards`
--

DROP TABLE IF EXISTS `customer_rewards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_rewards` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `current_points` int NOT NULL DEFAULT '0',
  `total_earned_points` int NOT NULL DEFAULT '0',
  `total_redeemed_points` int NOT NULL DEFAULT '0',
  `tier` enum('bronze','silver','gold','platinum') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'bronze',
  `tier_updated_at` timestamp NULL DEFAULT NULL,
  `lifetime_spending` decimal(18,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customer_rewards_user_id_unique` (`user_id`),
  CONSTRAINT `customer_rewards_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customer_rewards`
--

LOCK TABLES `customer_rewards` WRITE;
/*!40000 ALTER TABLE `customer_rewards` DISABLE KEYS */;
INSERT INTO `customer_rewards` VALUES (1,2,1,1,0,'bronze',NULL,815000.00,'2026-09-01 20:16:35','2026-09-01 20:16:35');
/*!40000 ALTER TABLE `customer_rewards` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `digital_certificates`
--

DROP TABLE IF EXISTS `digital_certificates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `digital_certificates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `certificate_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `transaction_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `issued_at` timestamp NOT NULL,
  `pdf_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qr_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Path atau data string QR Code verifikasi',
  `is_valid` tinyint(1) NOT NULL DEFAULT '1',
  `invalidated_at` timestamp NULL DEFAULT NULL,
  `invalidation_reason` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `digital_certificates_certificate_number_unique` (`certificate_number`),
  UNIQUE KEY `digital_certificates_transaction_id_unique` (`transaction_id`),
  KEY `digital_certificates_certificate_number_index` (`certificate_number`),
  KEY `digital_certificates_user_id_is_valid_index` (`user_id`,`is_valid`),
  CONSTRAINT `digital_certificates_transaction_id_foreign` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `digital_certificates_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `digital_certificates`
--

LOCK TABLES `digital_certificates` WRITE;
/*!40000 ALTER TABLE `digital_certificates` DISABLE KEYS */;
INSERT INTO `digital_certificates` VALUES (1,'CERT-20260902-6998',1,2,'2026-09-01 20:16:35',NULL,NULL,1,NULL,NULL,'2026-09-01 20:16:35','2026-09-01 20:16:35');
/*!40000 ALTER TABLE `digital_certificates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gold_prices`
--

DROP TABLE IF EXISTS `gold_prices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gold_prices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `price_date` date NOT NULL,
  `buy_price_per_gram` decimal(12,2) NOT NULL COMMENT 'Harga beli emas dari pelanggan',
  `sell_price_per_gram` decimal(12,2) NOT NULL COMMENT 'Harga jual emas ke pelanggan',
  `source` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Sumber harga: ANTAM, lokal, dll.',
  `recorded_by` bigint unsigned NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `gold_prices_price_date_unique` (`price_date`),
  KEY `gold_prices_recorded_by_foreign` (`recorded_by`),
  CONSTRAINT `gold_prices_recorded_by_foreign` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gold_prices`
--

LOCK TABLES `gold_prices` WRITE;
/*!40000 ALTER TABLE `gold_prices` DISABLE KEYS */;
INSERT INTO `gold_prices` VALUES (1,'2026-08-04',1583000.00,1623000.00,'API Eksternal (Histori)',1,'Data histori berbasis API eksternal.','2026-09-01 20:16:34','2026-09-01 20:16:34'),(2,'2026-08-05',1595000.00,1636000.00,'API Eksternal (Histori)',1,'Data histori berbasis API eksternal.','2026-09-01 20:16:34','2026-09-01 20:16:34'),(3,'2026-08-06',1575000.00,1615000.00,'API Eksternal (Histori)',1,'Data histori berbasis API eksternal.','2026-09-01 20:16:34','2026-09-01 20:16:34'),(4,'2026-08-07',1576000.00,1616000.00,'API Eksternal (Histori)',1,'Data histori berbasis API eksternal.','2026-09-01 20:16:34','2026-09-01 20:16:34'),(5,'2026-08-08',1569000.00,1609000.00,'API Eksternal (Histori)',1,'Data histori berbasis API eksternal.','2026-09-01 20:16:34','2026-09-01 20:16:34'),(6,'2026-08-09',1588000.00,1628000.00,'API Eksternal (Histori)',1,'Data histori berbasis API eksternal.','2026-09-01 20:16:34','2026-09-01 20:16:34'),(7,'2026-08-10',1598000.00,1639000.00,'API Eksternal (Histori)',1,'Data histori berbasis API eksternal.','2026-09-01 20:16:34','2026-09-01 20:16:34'),(8,'2026-08-11',1575000.00,1614000.00,'API Eksternal (Histori)',1,'Data histori berbasis API eksternal.','2026-09-01 20:16:34','2026-09-01 20:16:34'),(9,'2026-08-12',1576000.00,1616000.00,'API Eksternal (Histori)',1,'Data histori berbasis API eksternal.','2026-09-01 20:16:34','2026-09-01 20:16:34'),(10,'2026-08-13',1588000.00,1628000.00,'API Eksternal (Histori)',1,'Data histori berbasis API eksternal.','2026-09-01 20:16:34','2026-09-01 20:16:34'),(11,'2026-08-14',1597000.00,1637000.00,'API Eksternal (Histori)',1,'Data histori berbasis API eksternal.','2026-09-01 20:16:34','2026-09-01 20:16:34'),(12,'2026-08-15',1562000.00,1601000.00,'API Eksternal (Histori)',1,'Data histori berbasis API eksternal.','2026-09-01 20:16:34','2026-09-01 20:16:34'),(13,'2026-08-16',1561000.00,1600000.00,'API Eksternal (Histori)',1,'Data histori berbasis API eksternal.','2026-09-01 20:16:34','2026-09-01 20:16:34'),(14,'2026-08-17',1592000.00,1633000.00,'API Eksternal (Histori)',1,'Data histori berbasis API eksternal.','2026-09-01 20:16:34','2026-09-01 20:16:34'),(15,'2026-08-18',1596000.00,1637000.00,'API Eksternal (Histori)',1,'Data histori berbasis API eksternal.','2026-09-01 20:16:34','2026-09-01 20:16:34'),(16,'2026-08-19',1601000.00,1642000.00,'API Eksternal (Histori)',1,'Data histori berbasis API eksternal.','2026-09-01 20:16:34','2026-09-01 20:16:34'),(17,'2026-08-20',1572000.00,1612000.00,'API Eksternal (Histori)',1,'Data histori berbasis API eksternal.','2026-09-01 20:16:34','2026-09-01 20:16:34'),(18,'2026-08-21',1592000.00,1633000.00,'API Eksternal (Histori)',1,'Data histori berbasis API eksternal.','2026-09-01 20:16:34','2026-09-01 20:16:34'),(19,'2026-08-22',1600000.00,1641000.00,'API Eksternal (Histori)',1,'Data histori berbasis API eksternal.','2026-09-01 20:16:34','2026-09-01 20:16:34'),(20,'2026-08-23',1576000.00,1616000.00,'API Eksternal (Histori)',1,'Data histori berbasis API eksternal.','2026-09-01 20:16:34','2026-09-01 20:16:34'),(21,'2026-08-24',1598000.00,1638000.00,'API Eksternal (Histori)',1,'Data histori berbasis API eksternal.','2026-09-01 20:16:34','2026-09-01 20:16:34'),(22,'2026-08-25',1598000.00,1639000.00,'API Eksternal (Histori)',1,'Data histori berbasis API eksternal.','2026-09-01 20:16:34','2026-09-01 20:16:34'),(23,'2026-08-26',1590000.00,1631000.00,'API Eksternal (Histori)',1,'Data histori berbasis API eksternal.','2026-09-01 20:16:34','2026-09-01 20:16:34'),(24,'2026-08-27',1573000.00,1613000.00,'API Eksternal (Histori)',1,'Data histori berbasis API eksternal.','2026-09-01 20:16:35','2026-09-01 20:16:35'),(25,'2026-08-28',1562000.00,1601000.00,'API Eksternal (Histori)',1,'Data histori berbasis API eksternal.','2026-09-01 20:16:35','2026-09-01 20:16:35'),(26,'2026-08-29',1587000.00,1627000.00,'API Eksternal (Histori)',1,'Data histori berbasis API eksternal.','2026-09-01 20:16:35','2026-09-01 20:16:35'),(27,'2026-08-30',1563000.00,1603000.00,'API Eksternal (Histori)',1,'Data histori berbasis API eksternal.','2026-09-01 20:16:35','2026-09-01 20:16:35'),(28,'2026-08-31',1578000.00,1618000.00,'API Eksternal (Histori)',1,'Data histori berbasis API eksternal.','2026-09-01 20:16:35','2026-09-01 20:16:35'),(29,'2026-09-01',1575000.00,1615000.00,'API Eksternal (Histori)',1,'Data histori berbasis API eksternal.','2026-09-01 20:16:35','2026-09-01 20:16:35'),(30,'2026-09-02',1580000.00,1620000.00,'API Eksternal',1,'Harga live real-time dari API eksternal.','2026-09-01 20:16:35','2026-09-01 20:16:35');
/*!40000 ALTER TABLE `gold_prices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `installment_payments`
--

DROP TABLE IF EXISTS `installment_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `installment_payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `installment_plan_id` bigint unsigned NOT NULL,
  `installment_number` smallint NOT NULL COMMENT 'Angsuran ke-N',
  `due_date` date NOT NULL,
  `paid_date` date DEFAULT NULL,
  `amount_due` decimal(12,2) NOT NULL,
  `amount_paid` decimal(12,2) DEFAULT NULL,
  `payment_method` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','paid','overdue','waived') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `received_by` bigint unsigned DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `installment_payments_received_by_foreign` (`received_by`),
  KEY `installment_payments_installment_plan_id_status_index` (`installment_plan_id`,`status`),
  KEY `installment_payments_due_date_index` (`due_date`),
  CONSTRAINT `installment_payments_installment_plan_id_foreign` FOREIGN KEY (`installment_plan_id`) REFERENCES `installment_plans` (`id`) ON DELETE CASCADE,
  CONSTRAINT `installment_payments_received_by_foreign` FOREIGN KEY (`received_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `installment_payments`
--

LOCK TABLES `installment_payments` WRITE;
/*!40000 ALTER TABLE `installment_payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `installment_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `installment_plans`
--

DROP TABLE IF EXISTS `installment_plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `installment_plans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `transaction_id` bigint unsigned NOT NULL,
  `down_payment` decimal(15,2) NOT NULL COMMENT 'Uang muka awal (dibayar di toko)',
  `total_installment` decimal(15,2) NOT NULL COMMENT 'Total angsuran setelah DP',
  `tenure_months` smallint NOT NULL COMMENT 'Jumlah bulan cicilan',
  `monthly_amount` decimal(12,2) NOT NULL COMMENT 'Nominal per bulan',
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('active','completed','defaulted') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `installment_plans_transaction_id_unique` (`transaction_id`),
  CONSTRAINT `installment_plans_transaction_id_foreign` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `installment_plans`
--

LOCK TABLES `installment_plans` WRITE;
/*!40000 ALTER TABLE `installment_plans` DISABLE KEYS */;
/*!40000 ALTER TABLE `installment_plans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
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
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000001_create_cache_table',1),(2,'0001_01_01_000002_create_jobs_table',1),(3,'2024_01_01_000000_create_users_table',1),(4,'2024_01_01_000001_create_categories_table',1),(5,'2024_01_01_000002_create_gold_prices_table',1),(6,'2024_01_01_000003_create_products_table',1),(7,'2024_01_01_000004_create_customer_profiles_table',1),(8,'2024_01_01_000005_create_reward_programs_table',1),(9,'2024_01_01_000006_create_customer_rewards_table',1),(10,'2024_01_01_000007_create_reservations_table',1),(11,'2024_01_01_000008_create_transactions_table',1),(12,'2024_01_01_000009_add_transaction_id_to_reservations_table',1),(13,'2024_01_01_000010_create_transaction_items_table',1),(14,'2024_01_01_000011_create_installment_plans_table',1),(15,'2024_01_01_000012_create_installment_payments_table',1),(16,'2024_01_01_000013_create_pawns_table',1),(17,'2024_01_01_000014_create_digital_certificates_table',1),(18,'2024_01_01_000015_create_reward_redemptions_table',1),(19,'2024_01_01_000016_create_notifications_table',1),(20,'2024_01_01_000017_create_activity_logs_table',1),(21,'2026_07_19_000000_add_pawn_and_installment_to_reservations_table',1),(22,'2026_07_19_000001_add_payment_method_to_reservations_table',1),(23,'2026_07_23_000000_add_is_basic_to_products_table',1),(24,'2026_07_28_000000_create_price_negotiations_table',1),(25,'2026_07_28_000001_add_negotiation_to_reservations_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'misal: reservation.confirmed, installment.due',
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `data` json DEFAULT NULL COMMENT 'Payload tambahan, misal: {reservation_id: 5}',
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `notifications_user_id_read_at_index` (`user_id`,`read_at`),
  CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES (1,2,'reward.earned','Selamat! Anda Mendapatkan Poin Reward','Transaksi Anda berhasil diverifikasi. Poin reward Anda bertambah +1 poin!','{\"points\": 1}',NULL,'2026-09-01 18:16:35'),(2,2,'reservation.confirmed','Reservasi Emas Dikonfirmasi','Reservasi Anda telah dikonfirmasi oleh admin toko. Silakan datang ke toko sesuai jadwal.','{\"status\": \"confirmed\"}',NULL,'2026-08-31 20:16:35'),(3,2,'price.info','Update Harga Emas Hari Ini','Harga buyback dan jual emas murni 24K telah diperbarui di sistem Toko Emas Sinar Baru II.','[]','2026-08-30 20:16:35','2026-08-30 20:16:35'),(4,1,'reservation.created','Reservasi Baru Masuk','Pelanggan telah membuat reservasi baru untuk kunjungan ke toko. Mohon segera verifikasi.','[]',NULL,'2026-09-01 19:16:35'),(5,1,'negotiation.created','Pengajuan Tawar Harga Baru','Ada pengajuan penawaran harga produk emas yang menunggu respon persetujuan.','[]',NULL,'2026-09-01 17:16:35');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
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
-- Table structure for table `pawns`
--

DROP TABLE IF EXISTS `pawns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pawns` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `transaction_id` bigint unsigned NOT NULL,
  `pawn_code` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gold_description` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Deskripsi emas yang digadai',
  `gold_purity` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `weight_gram` decimal(8,3) NOT NULL,
  `appraised_value` decimal(15,2) NOT NULL COMMENT 'Nilai taksiran',
  `loan_amount` decimal(15,2) NOT NULL COMMENT 'Pinjaman yang diberikan',
  `interest_rate` decimal(5,2) NOT NULL COMMENT 'Bunga per bulan (%)',
  `start_date` date NOT NULL,
  `due_date` date NOT NULL,
  `status` enum('active','redeemed','extended','forfeited') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `redemption_date` date DEFAULT NULL,
  `redemption_amount` decimal(15,2) DEFAULT NULL COMMENT 'Total dibayar saat tebus',
  `transaction_item_id` bigint unsigned DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pawns_transaction_id_unique` (`transaction_id`),
  UNIQUE KEY `pawns_pawn_code_unique` (`pawn_code`),
  KEY `pawns_transaction_item_id_foreign` (`transaction_item_id`),
  KEY `pawns_pawn_code_index` (`pawn_code`),
  KEY `pawns_status_index` (`status`),
  CONSTRAINT `pawns_transaction_id_foreign` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pawns_transaction_item_id_foreign` FOREIGN KEY (`transaction_item_id`) REFERENCES `transaction_items` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pawns`
--

LOCK TABLES `pawns` WRITE;
/*!40000 ALTER TABLE `pawns` DISABLE KEYS */;
/*!40000 ALTER TABLE `pawns` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `price_negotiations`
--

DROP TABLE IF EXISTS `price_negotiations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `price_negotiations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `negotiation_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `original_price` decimal(12,2) NOT NULL,
  `offered_price` decimal(12,2) NOT NULL,
  `agreed_price` decimal(12,2) DEFAULT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `status` enum('pending','approved','rejected','used') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `admin_notes` text COLLATE utf8mb4_unicode_ci,
  `responded_by` bigint unsigned DEFAULT NULL,
  `responded_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `price_negotiations_negotiation_code_unique` (`negotiation_code`),
  KEY `price_negotiations_user_id_foreign` (`user_id`),
  KEY `price_negotiations_product_id_foreign` (`product_id`),
  KEY `price_negotiations_responded_by_foreign` (`responded_by`),
  CONSTRAINT `price_negotiations_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `price_negotiations_responded_by_foreign` FOREIGN KEY (`responded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `price_negotiations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `price_negotiations`
--

LOCK TABLES `price_negotiations` WRITE;
/*!40000 ALTER TABLE `price_negotiations` DISABLE KEYS */;
/*!40000 ALTER TABLE `price_negotiations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint unsigned NOT NULL,
  `sku` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(220) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `gold_purity` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Kadar emas: 24K, 22K, 18K, dll.',
  `weight_gram` decimal(8,3) NOT NULL COMMENT 'Berat dalam gram',
  `base_price` decimal(15,2) NOT NULL COMMENT 'Harga dasar jual',
  `buy_back_price` decimal(15,2) DEFAULT NULL COMMENT 'Harga beli kembali dari pelanggan',
  `stock` int NOT NULL DEFAULT '0',
  `images` json DEFAULT NULL COMMENT 'Array path gambar; index 0 = thumbnail utama',
  `is_available` tinyint(1) NOT NULL DEFAULT '1',
  `is_reservable` tinyint(1) NOT NULL DEFAULT '1',
  `is_basic` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'True jika merupakan produk standar/ori yang tampil di landing page tanpa filter',
  `sort_order` smallint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_sku_unique` (`sku`),
  UNIQUE KEY `products_slug_unique` (`slug`),
  KEY `products_category_id_foreign` (`category_id`),
  CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,1,'JW-CIN-BANGKOK-05G','Cincin Bangkok (Setengah Gram - 0,5g)','cincin-bangkok-setengah-gram-05g','Cincin murni model Bangkok berkadar 24 karat (999) dengan ukuran berat Setengah Gram (0.5 gram). Memiliki kilau mewah, garansi keaslian, dan cocok untuk pemakaian maupun investasi.','24K',0.500,810000.00,786000.00,11,'[\"/images/products/cincin.png\"]',1,1,0,1,'2026-09-01 20:16:32','2026-09-01 20:16:32',NULL),(2,1,'JW-CIN-BANGKOK-10G','Cincin Bangkok 1 Gram','cincin-bangkok-1-gram','Cincin murni model Bangkok berkadar 24 karat (999) dengan ukuran berat 1 Gram (1 gram). Memiliki kilau mewah, garansi keaslian, dan cocok untuk pemakaian maupun investasi.','24K',1.000,1620000.00,1571000.00,21,'[\"/images/products/cincin.png\"]',1,1,1,2,'2026-09-01 20:16:32','2026-09-01 20:16:32',NULL),(3,1,'JW-CIN-BANGKOK-14S','Cincin Bangkok (1/4 Suku - 1,65g)','cincin-bangkok-14-suku-165g','Cincin murni model Bangkok berkadar 24 karat (999) dengan ukuran berat 1/4 Suku (1.65 gram). Memiliki kilau mewah, garansi keaslian, dan cocok untuk pemakaian maupun investasi.','24K',1.650,2673000.00,2593000.00,10,'[\"/images/products/cincin.png\"]',1,1,0,3,'2026-09-01 20:16:32','2026-09-01 20:16:32',NULL),(4,1,'JW-CIN-BANGKOK-12S','Cincin Bangkok (Setengah Suku - 3,4g)','cincin-bangkok-setengah-suku-34g','Cincin murni model Bangkok berkadar 24 karat (999) dengan ukuran berat Setengah Suku (3.4 gram). Memiliki kilau mewah, garansi keaslian, dan cocok untuk pemakaian maupun investasi.','24K',3.400,5508000.00,5343000.00,10,'[\"/images/products/cincin.png\"]',1,1,0,4,'2026-09-01 20:16:32','2026-09-01 20:16:32',NULL),(5,1,'JW-CIN-BANGKOK-1S','Cincin Bangkok (1 Suku - 6,7g)','cincin-bangkok-1-suku-67g','Cincin murni model Bangkok berkadar 24 karat (999) dengan ukuran berat 1 Suku (6.7 gram). Memiliki kilau mewah, garansi keaslian, dan cocok untuk pemakaian maupun investasi.','24K',6.700,10854000.00,10528000.00,20,'[\"/images/products/cincin.png\"]',1,1,0,5,'2026-09-01 20:16:32','2026-09-01 20:16:32',NULL),(6,1,'JW-CIN-ASAHAN-05G','Cincin Asahan (Setengah Gram - 0,5g)','cincin-asahan-setengah-gram-05g','Cincin murni model Asahan berkadar 24 karat (999) dengan ukuran berat Setengah Gram (0.5 gram). Memiliki kilau mewah, garansi keaslian, dan cocok untuk pemakaian maupun investasi.','24K',0.500,810000.00,786000.00,23,'[\"/images/products/cincin.png\"]',1,1,0,6,'2026-09-01 20:16:32','2026-09-01 20:16:32',NULL),(7,1,'JW-CIN-ASAHAN-10G','Cincin Asahan 1 Gram','cincin-asahan-1-gram','Cincin murni model Asahan berkadar 24 karat (999) dengan ukuran berat 1 Gram (1 gram). Memiliki kilau mewah, garansi keaslian, dan cocok untuk pemakaian maupun investasi.','24K',1.000,1620000.00,1571000.00,21,'[\"/images/products/cincin.png\"]',1,1,0,7,'2026-09-01 20:16:32','2026-09-01 20:16:32',NULL),(8,1,'JW-CIN-ASAHAN-14S','Cincin Asahan (1/4 Suku - 1,65g)','cincin-asahan-14-suku-165g','Cincin murni model Asahan berkadar 24 karat (999) dengan ukuran berat 1/4 Suku (1.65 gram). Memiliki kilau mewah, garansi keaslian, dan cocok untuk pemakaian maupun investasi.','24K',1.650,2673000.00,2593000.00,18,'[\"/images/products/cincin.png\"]',1,1,0,8,'2026-09-01 20:16:32','2026-09-01 20:16:32',NULL),(9,1,'JW-CIN-ASAHAN-12S','Cincin Asahan (Setengah Suku - 3,4g)','cincin-asahan-setengah-suku-34g','Cincin murni model Asahan berkadar 24 karat (999) dengan ukuran berat Setengah Suku (3.4 gram). Memiliki kilau mewah, garansi keaslian, dan cocok untuk pemakaian maupun investasi.','24K',3.400,5508000.00,5343000.00,13,'[\"/images/products/cincin.png\"]',1,1,0,9,'2026-09-01 20:16:32','2026-09-01 20:16:32',NULL),(10,1,'JW-CIN-ASAHAN-1S','Cincin Asahan (1 Suku - 6,7g)','cincin-asahan-1-suku-67g','Cincin murni model Asahan berkadar 24 karat (999) dengan ukuran berat 1 Suku (6.7 gram). Memiliki kilau mewah, garansi keaslian, dan cocok untuk pemakaian maupun investasi.','24K',6.700,10854000.00,10528000.00,13,'[\"/images/products/cincin.png\"]',1,1,0,10,'2026-09-01 20:16:32','2026-09-01 20:16:32',NULL),(11,1,'JW-CIN-BOROBUDUR-05G','Cincin Borobudur (Setengah Gram - 0,5g)','cincin-borobudur-setengah-gram-05g','Cincin murni model Borobudur berkadar 24 karat (999) dengan ukuran berat Setengah Gram (0.5 gram). Memiliki kilau mewah, garansi keaslian, dan cocok untuk pemakaian maupun investasi.','24K',0.500,810000.00,786000.00,24,'[\"/images/products/cincin.png\"]',1,1,0,11,'2026-09-01 20:16:32','2026-09-01 20:16:32',NULL),(12,1,'JW-CIN-BOROBUDUR-10G','Cincin Borobudur 1 Gram','cincin-borobudur-1-gram','Cincin murni model Borobudur berkadar 24 karat (999) dengan ukuran berat 1 Gram (1 gram). Memiliki kilau mewah, garansi keaslian, dan cocok untuk pemakaian maupun investasi.','24K',1.000,1620000.00,1571000.00,19,'[\"/images/products/cincin.png\"]',1,1,0,12,'2026-09-01 20:16:32','2026-09-01 20:16:32',NULL),(13,1,'JW-CIN-BOROBUDUR-14S','Cincin Borobudur (1/4 Suku - 1,65g)','cincin-borobudur-14-suku-165g','Cincin murni model Borobudur berkadar 24 karat (999) dengan ukuran berat 1/4 Suku (1.65 gram). Memiliki kilau mewah, garansi keaslian, dan cocok untuk pemakaian maupun investasi.','24K',1.650,2673000.00,2593000.00,15,'[\"/images/products/cincin.png\"]',1,1,0,13,'2026-09-01 20:16:32','2026-09-01 20:16:32',NULL),(14,1,'JW-CIN-BOROBUDUR-12S','Cincin Borobudur (Setengah Suku - 3,4g)','cincin-borobudur-setengah-suku-34g','Cincin murni model Borobudur berkadar 24 karat (999) dengan ukuran berat Setengah Suku (3.4 gram). Memiliki kilau mewah, garansi keaslian, dan cocok untuk pemakaian maupun investasi.','24K',3.400,5508000.00,5343000.00,23,'[\"/images/products/cincin.png\"]',1,1,0,14,'2026-09-01 20:16:32','2026-09-01 20:16:32',NULL),(15,1,'JW-CIN-BOROBUDUR-1S','Cincin Borobudur (1 Suku - 6,7g)','cincin-borobudur-1-suku-67g','Cincin murni model Borobudur berkadar 24 karat (999) dengan ukuran berat 1 Suku (6.7 gram). Memiliki kilau mewah, garansi keaslian, dan cocok untuk pemakaian maupun investasi.','24K',6.700,10854000.00,10528000.00,21,'[\"/images/products/cincin.png\"]',1,1,0,15,'2026-09-01 20:16:32','2026-09-01 20:16:32',NULL),(16,1,'JW-CIN-CHANEL-05G','Cincin Chanel (Setengah Gram - 0,5g)','cincin-chanel-setengah-gram-05g','Cincin murni model Chanel berkadar 24 karat (999) dengan ukuran berat Setengah Gram (0.5 gram). Memiliki kilau mewah, garansi keaslian, dan cocok untuk pemakaian maupun investasi.','24K',0.500,810000.00,786000.00,18,'[\"/images/products/cincin.png\"]',1,1,0,16,'2026-09-01 20:16:32','2026-09-01 20:16:32',NULL),(17,1,'JW-CIN-CHANEL-10G','Cincin Chanel 1 Gram','cincin-chanel-1-gram','Cincin murni model Chanel berkadar 24 karat (999) dengan ukuran berat 1 Gram (1 gram). Memiliki kilau mewah, garansi keaslian, dan cocok untuk pemakaian maupun investasi.','24K',1.000,1620000.00,1571000.00,22,'[\"/images/products/cincin.png\"]',1,1,0,17,'2026-09-01 20:16:32','2026-09-01 20:16:32',NULL),(18,1,'JW-CIN-CHANEL-14S','Cincin Chanel (1/4 Suku - 1,65g)','cincin-chanel-14-suku-165g','Cincin murni model Chanel berkadar 24 karat (999) dengan ukuran berat 1/4 Suku (1.65 gram). Memiliki kilau mewah, garansi keaslian, dan cocok untuk pemakaian maupun investasi.','24K',1.650,2673000.00,2593000.00,23,'[\"/images/products/cincin.png\"]',1,1,0,18,'2026-09-01 20:16:32','2026-09-01 20:16:32',NULL),(19,1,'JW-CIN-CHANEL-12S','Cincin Chanel (Setengah Suku - 3,4g)','cincin-chanel-setengah-suku-34g','Cincin murni model Chanel berkadar 24 karat (999) dengan ukuran berat Setengah Suku (3.4 gram). Memiliki kilau mewah, garansi keaslian, dan cocok untuk pemakaian maupun investasi.','24K',3.400,5508000.00,5343000.00,18,'[\"/images/products/cincin.png\"]',1,1,0,19,'2026-09-01 20:16:32','2026-09-01 20:16:32',NULL),(20,1,'JW-CIN-CHANEL-1S','Cincin Chanel (1 Suku - 6,7g)','cincin-chanel-1-suku-67g','Cincin murni model Chanel berkadar 24 karat (999) dengan ukuran berat 1 Suku (6.7 gram). Memiliki kilau mewah, garansi keaslian, dan cocok untuk pemakaian maupun investasi.','24K',6.700,10854000.00,10528000.00,13,'[\"/images/products/cincin.png\"]',1,1,0,20,'2026-09-01 20:16:32','2026-09-01 20:16:32',NULL),(21,1,'JW-CIN-SULTAN-05G','Cincin Sultan (Setengah Gram - 0,5g)','cincin-sultan-setengah-gram-05g','Cincin murni model Sultan berkadar 24 karat (999) dengan ukuran berat Setengah Gram (0.5 gram). Memiliki kilau mewah, garansi keaslian, dan cocok untuk pemakaian maupun investasi.','24K',0.500,810000.00,786000.00,11,'[\"/images/products/cincin.png\"]',1,1,0,21,'2026-09-01 20:16:32','2026-09-01 20:16:32',NULL),(22,1,'JW-CIN-SULTAN-10G','Cincin Sultan 1 Gram','cincin-sultan-1-gram','Cincin murni model Sultan berkadar 24 karat (999) dengan ukuran berat 1 Gram (1 gram). Memiliki kilau mewah, garansi keaslian, dan cocok untuk pemakaian maupun investasi.','24K',1.000,1620000.00,1571000.00,16,'[\"/images/products/cincin.png\"]',1,1,0,22,'2026-09-01 20:16:32','2026-09-01 20:16:32',NULL),(23,1,'JW-CIN-SULTAN-14S','Cincin Sultan (1/4 Suku - 1,65g)','cincin-sultan-14-suku-165g','Cincin murni model Sultan berkadar 24 karat (999) dengan ukuran berat 1/4 Suku (1.65 gram). Memiliki kilau mewah, garansi keaslian, dan cocok untuk pemakaian maupun investasi.','24K',1.650,2673000.00,2593000.00,22,'[\"/images/products/cincin.png\"]',1,1,0,23,'2026-09-01 20:16:32','2026-09-01 20:16:32',NULL),(24,1,'JW-CIN-SULTAN-12S','Cincin Sultan (Setengah Suku - 3,4g)','cincin-sultan-setengah-suku-34g','Cincin murni model Sultan berkadar 24 karat (999) dengan ukuran berat Setengah Suku (3.4 gram). Memiliki kilau mewah, garansi keaslian, dan cocok untuk pemakaian maupun investasi.','24K',3.400,5508000.00,5343000.00,22,'[\"/images/products/cincin.png\"]',1,1,0,24,'2026-09-01 20:16:32','2026-09-01 20:16:32',NULL),(25,1,'JW-CIN-SULTAN-1S','Cincin Sultan (1 Suku - 6,7g)','cincin-sultan-1-suku-67g','Cincin murni model Sultan berkadar 24 karat (999) dengan ukuran berat 1 Suku (6.7 gram). Memiliki kilau mewah, garansi keaslian, dan cocok untuk pemakaian maupun investasi.','24K',6.700,10854000.00,10528000.00,20,'[\"/images/products/cincin.png\"]',1,1,0,25,'2026-09-01 20:16:32','2026-09-01 20:16:32',NULL),(26,2,'JW-KAL-PADI-10G','Kalung Padi 1 Gram','kalung-padi-1-gram','Kalung murni model Padi berkadar 24 karat (999) dengan ukuran berat 1 Gram (1 gram). Memiliki kilau mewah, garansi keaslian, dan cocok untuk pemakaian maupun investasi.','24K',1.000,1620000.00,1571000.00,12,'[\"/images/products/kalung.png\"]',1,1,1,26,'2026-09-01 20:16:32','2026-09-01 20:16:32',NULL),(27,2,'JW-KAL-PADI-14S','Kalung Padi (1/4 Suku - 1,65g)','kalung-padi-14-suku-165g','Kalung murni model Padi berkadar 24 karat (999) dengan ukuran berat 1/4 Suku (1.65 gram). Memiliki kilau mewah, garansi keaslian, dan cocok untuk pemakaian maupun investasi.','24K',1.650,2673000.00,2593000.00,24,'[\"/images/products/kalung.png\"]',1,1,0,27,'2026-09-01 20:16:32','2026-09-01 20:16:32',NULL),(28,2,'JW-KAL-PADI-12S','Kalung Padi (Setengah Suku - 3,4g)','kalung-padi-setengah-suku-34g','Kalung murni model Padi berkadar 24 karat (999) dengan ukuran berat Setengah Suku (3.4 gram). Memiliki kilau mewah, garansi keaslian, dan cocok untuk pemakaian maupun investasi.','24K',3.400,5508000.00,5343000.00,25,'[\"/images/products/kalung.png\"]',1,1,0,28,'2026-09-01 20:16:32','2026-09-01 20:16:32',NULL),(29,2,'JW-KAL-PADI-1S','Kalung Padi (1 Suku - 6,7g)','kalung-padi-1-suku-67g','Kalung murni model Padi berkadar 24 karat (999) dengan ukuran berat 1 Suku (6.7 gram). Memiliki kilau mewah, garansi keaslian, dan cocok untuk pemakaian maupun investasi.','24K',6.700,10854000.00,10528000.00,9,'[\"/images/products/kalung.png\"]',1,1,0,29,'2026-09-01 20:16:32','2026-09-01 20:16:32',NULL),(30,2,'JW-KAL-PADI-2S','Kalung Padi (2 Suku - 13,4g)','kalung-padi-2-suku-134g','Kalung murni model Padi berkadar 24 karat (999) dengan ukuran berat 2 Suku (13.4 gram). Memiliki kilau mewah, garansi keaslian, dan cocok untuk pemakaian maupun investasi.','24K',13.400,21708000.00,21057000.00,11,'[\"/images/products/kalung.png\"]',1,1,0,30,'2026-09-01 20:16:32','2026-09-01 20:16:32',NULL),(31,2,'JW-KAL-PADI-3S','Kalung Padi (3 Suku - 20,1g)','kalung-padi-3-suku-201g','Kalung murni model Padi berkadar 24 karat (999) dengan ukuran berat 3 Suku (20.1 gram). Memiliki kilau mewah, garansi keaslian, dan cocok untuk pemakaian maupun investasi.','24K',20.100,32562000.00,31585000.00,9,'[\"/images/products/kalung.png\"]',1,1,0,31,'2026-09-01 20:16:32','2026-09-01 20:16:32',NULL),(32,2,'JW-KAL-PADI-5S','Kalung Padi (5 Suku - 33,5g)','kalung-padi-5-suku-335g','Kalung murni model Padi berkadar 24 karat (999) dengan ukuran berat 5 Suku (33.5 gram). Memiliki kilau mewah, garansi keaslian, dan cocok untuk pemakaian maupun investasi.','24K',33.500,54270000.00,52642000.00,13,'[\"/images/products/kalung.png\"]',1,1,0,32,'2026-09-01 20:16:32','2026-09-01 20:16:32',NULL),(33,2,'JW-KAL-MEDAN-10G','Kalung Medan 1 Gram','kalung-medan-1-gram','Kalung murni model Medan berkadar 24 karat (999) dengan ukuran berat 1 Gram (1 gram). Memiliki kilau mewah, garansi keaslian, dan cocok untuk pemakaian maupun investasi.','24K',1.000,1620000.00,1571000.00,24,'[\"/images/products/kalung.png\"]',1,1,0,33,'2026-09-01 20:16:32','2026-09-01 20:16:32',NULL),(34,2,'JW-KAL-MEDAN-14S','Kalung Medan (1/4 Suku - 1,65g)','kalung-medan-14-suku-165g','Kalung murni model Medan berkadar 24 karat (999) dengan ukuran berat 1/4 Suku (1.65 gram). Memiliki kilau mewah, garansi keaslian, dan cocok untuk pemakaian maupun investasi.','24K',1.650,2673000.00,2593000.00,24,'[\"/images/products/kalung.png\"]',1,1,0,34,'2026-09-01 20:16:32','2026-09-01 20:16:32',NULL),(35,2,'JW-KAL-MEDAN-12S','Kalung Medan (Setengah Suku - 3,4g)','kalung-medan-setengah-suku-34g','Kalung murni model Medan berkadar 24 karat (999) dengan ukuran berat Setengah Suku (3.4 gram). Memiliki kilau mewah, garansi keaslian, dan cocok untuk pemakaian maupun investasi.','24K',3.400,5508000.00,5343000.00,15,'[\"/images/products/kalung.png\"]',1,1,0,35,'2026-09-01 20:16:32','2026-09-01 20:16:32',NULL),(36,2,'JW-KAL-MEDAN-1S','Kalung Medan (1 Suku - 6,7g)','kalung-medan-1-suku-67g','Kalung murni model Medan berkadar 24 karat (999) dengan ukuran berat 1 Suku (6.7 gram). Memiliki kilau mewah, garansi keaslian, dan cocok untuk pemakaian maupun investasi.','24K',6.700,10854000.00,10528000.00,25,'[\"/images/products/kalung.png\"]',1,1,0,36,'2026-09-01 20:16:32','2026-09-01 20:16:32',NULL),(37,2,'JW-KAL-MEDAN-2S','Kalung Medan (2 Suku - 13,4g)','kalung-medan-2-suku-134g','Kalung murni model Medan berkadar 24 karat (999) dengan ukuran berat 2 Suku (13.4 gram). Memiliki kilau mewah, garansi keaslian, dan cocok untuk pemakaian maupun investasi.','24K',13.400,21708000.00,21057000.00,16,'[\"/images/products/kalung.png\"]',1,1,0,37,'2026-09-01 20:16:32','2026-09-01 20:16:32',NULL),(38,2,'JW-KAL-MEDAN-3S','Kalung Medan (3 Suku - 20,1g)','kalung-medan-3-suku-201g','Kalung murni model Medan berkadar 24 karat (999) dengan ukuran berat 3 Suku (20.1 gram). Memiliki kilau mewah, garansi keaslian, dan cocok untuk pemakaian maupun investasi.','24K',20.100,32562000.00,31585000.00,10,'[\"/images/products/kalung.png\"]',1,1,0,38,'2026-09-01 20:16:32','2026-09-01 20:16:32',NULL),(39,2,'JW-KAL-MEDAN-5S','Kalung Medan (5 Suku - 33,5g)','kalung-medan-5-suku-335g','Kalung murni model Medan berkadar 24 karat (999) dengan ukuran berat 5 Suku (33.5 gram). Memiliki kilau mewah, garansi keaslian, dan cocok untuk pemakaian maupun investasi.','24K',33.500,54270000.00,52642000.00,10,'[\"/images/products/kalung.png\"]',1,1,0,39,'2026-09-01 20:16:32','2026-09-01 20:16:32',NULL),(40,3,'JW-GEL-PADI-10G','Gelang Padi 1 Gram','gelang-padi-1-gram','Gelang murni model Padi berkadar 24 karat (999) dengan ukuran berat 1 Gram (1 gram). Memiliki kilau mewah, garansi keaslian, dan cocok untuk pemakaian maupun investasi.','24K',1.000,1620000.00,1571000.00,21,'[\"/images/products/gelang.png\"]',1,1,1,40,'2026-09-01 20:16:32','2026-09-01 20:16:32',NULL),(41,3,'JW-GEL-PADI-14S','Gelang Padi (1/4 Suku - 1,65g)','gelang-padi-14-suku-165g','Gelang murni model Padi berkadar 24 karat (999) dengan ukuran berat 1/4 Suku (1.65 gram). Memiliki kilau mewah, garansi keaslian, dan cocok untuk pemakaian maupun investasi.','24K',1.650,2673000.00,2593000.00,12,'[\"/images/products/gelang.png\"]',1,1,0,41,'2026-09-01 20:16:32','2026-09-01 20:16:32',NULL),(42,3,'JW-GEL-PADI-12S','Gelang Padi (Setengah Suku - 3,4g)','gelang-padi-setengah-suku-34g','Gelang murni model Padi berkadar 24 karat (999) dengan ukuran berat Setengah Suku (3.4 gram). Memiliki kilau mewah, garansi keaslian, dan cocok untuk pemakaian maupun investasi.','24K',3.400,5508000.00,5343000.00,21,'[\"/images/products/gelang.png\"]',1,1,0,42,'2026-09-01 20:16:32','2026-09-01 20:16:32',NULL),(43,3,'JW-GEL-PADI-1S','Gelang Padi (1 Suku - 6,7g)','gelang-padi-1-suku-67g','Gelang murni model Padi berkadar 24 karat (999) dengan ukuran berat 1 Suku (6.7 gram). Memiliki kilau mewah, garansi keaslian, dan cocok untuk pemakaian maupun investasi.','24K',6.700,10854000.00,10528000.00,24,'[\"/images/products/gelang.png\"]',1,1,0,43,'2026-09-01 20:16:32','2026-09-01 20:16:32',NULL),(44,3,'JW-GEL-PADI-2S','Gelang Padi (2 Suku - 13,4g)','gelang-padi-2-suku-134g','Gelang murni model Padi berkadar 24 karat (999) dengan ukuran berat 2 Suku (13.4 gram). Memiliki kilau mewah, garansi keaslian, dan cocok untuk pemakaian maupun investasi.','24K',13.400,21708000.00,21057000.00,10,'[\"/images/products/gelang.png\"]',1,1,0,44,'2026-09-01 20:16:32','2026-09-01 20:16:32',NULL),(45,3,'JW-GEL-PADI-3S','Gelang Padi (3 Suku - 20,1g)','gelang-padi-3-suku-201g','Gelang murni model Padi berkadar 24 karat (999) dengan ukuran berat 3 Suku (20.1 gram). Memiliki kilau mewah, garansi keaslian, dan cocok untuk pemakaian maupun investasi.','24K',20.100,32562000.00,31585000.00,13,'[\"/images/products/gelang.png\"]',1,1,0,45,'2026-09-01 20:16:32','2026-09-01 20:16:32',NULL),(46,3,'JW-GEL-PADI-5S','Gelang Padi (5 Suku - 33,5g)','gelang-padi-5-suku-335g','Gelang murni model Padi berkadar 24 karat (999) dengan ukuran berat 5 Suku (33.5 gram). Memiliki kilau mewah, garansi keaslian, dan cocok untuk pemakaian maupun investasi.','24K',33.500,54270000.00,52642000.00,10,'[\"/images/products/gelang.png\"]',1,1,0,46,'2026-09-01 20:16:32','2026-09-01 20:16:32',NULL),(47,3,'JW-GEL-MEDAN-10G','Gelang Medan 1 Gram','gelang-medan-1-gram','Gelang murni model Medan berkadar 24 karat (999) dengan ukuran berat 1 Gram (1 gram). Memiliki kilau mewah, garansi keaslian, dan cocok untuk pemakaian maupun investasi.','24K',1.000,1620000.00,1571000.00,18,'[\"/images/products/gelang.png\"]',1,1,0,47,'2026-09-01 20:16:32','2026-09-01 20:16:32',NULL),(48,3,'JW-GEL-MEDAN-14S','Gelang Medan (1/4 Suku - 1,65g)','gelang-medan-14-suku-165g','Gelang murni model Medan berkadar 24 karat (999) dengan ukuran berat 1/4 Suku (1.65 gram). Memiliki kilau mewah, garansi keaslian, dan cocok untuk pemakaian maupun investasi.','24K',1.650,2673000.00,2593000.00,15,'[\"/images/products/gelang.png\"]',1,1,0,48,'2026-09-01 20:16:32','2026-09-01 20:16:32',NULL),(49,3,'JW-GEL-MEDAN-12S','Gelang Medan (Setengah Suku - 3,4g)','gelang-medan-setengah-suku-34g','Gelang murni model Medan berkadar 24 karat (999) dengan ukuran berat Setengah Suku (3.4 gram). Memiliki kilau mewah, garansi keaslian, dan cocok untuk pemakaian maupun investasi.','24K',3.400,5508000.00,5343000.00,14,'[\"/images/products/gelang.png\"]',1,1,0,49,'2026-09-01 20:16:32','2026-09-01 20:16:32',NULL),(50,3,'JW-GEL-MEDAN-1S','Gelang Medan (1 Suku - 6,7g)','gelang-medan-1-suku-67g','Gelang murni model Medan berkadar 24 karat (999) dengan ukuran berat 1 Suku (6.7 gram). Memiliki kilau mewah, garansi keaslian, dan cocok untuk pemakaian maupun investasi.','24K',6.700,10854000.00,10528000.00,14,'[\"/images/products/gelang.png\"]',1,1,0,50,'2026-09-01 20:16:32','2026-09-01 20:16:32',NULL),(51,3,'JW-GEL-MEDAN-2S','Gelang Medan (2 Suku - 13,4g)','gelang-medan-2-suku-134g','Gelang murni model Medan berkadar 24 karat (999) dengan ukuran berat 2 Suku (13.4 gram). Memiliki kilau mewah, garansi keaslian, dan cocok untuk pemakaian maupun investasi.','24K',13.400,21708000.00,21057000.00,22,'[\"/images/products/gelang.png\"]',1,1,0,51,'2026-09-01 20:16:32','2026-09-01 20:16:32',NULL),(52,3,'JW-GEL-MEDAN-3S','Gelang Medan (3 Suku - 20,1g)','gelang-medan-3-suku-201g','Gelang murni model Medan berkadar 24 karat (999) dengan ukuran berat 3 Suku (20.1 gram). Memiliki kilau mewah, garansi keaslian, dan cocok untuk pemakaian maupun investasi.','24K',20.100,32562000.00,31585000.00,13,'[\"/images/products/gelang.png\"]',1,1,0,52,'2026-09-01 20:16:32','2026-09-01 20:16:32',NULL),(53,3,'JW-GEL-MEDAN-5S','Gelang Medan (5 Suku - 33,5g)','gelang-medan-5-suku-335g','Gelang murni model Medan berkadar 24 karat (999) dengan ukuran berat 5 Suku (33.5 gram). Memiliki kilau mewah, garansi keaslian, dan cocok untuk pemakaian maupun investasi.','24K',33.500,54270000.00,52642000.00,12,'[\"/images/products/gelang.png\"]',1,1,0,53,'2026-09-01 20:16:32','2026-09-01 20:16:32',NULL),(54,4,'JW-ANT-RANTAIBINTANG-05G','Anting Rantai Bintang (Setengah Gram - 0,5g)','anting-rantai-bintang-setengah-gram-05g','Anting murni model Rantai Bintang berkadar 24 karat (999) dengan ukuran berat Setengah Gram (0.5 gram). Memiliki kilau mewah, garansi keaslian, dan cocok untuk pemakaian maupun investasi.','24K',0.500,810000.00,786000.00,9,'[\"/images/products/anting.png\"]',1,1,1,54,'2026-09-01 20:16:32','2026-09-01 20:16:32',NULL),(55,4,'JW-ANT-RANTAIBINTANG-10G','Anting Rantai Bintang 1 Gram','anting-rantai-bintang-1-gram','Anting murni model Rantai Bintang berkadar 24 karat (999) dengan ukuran berat 1 Gram (1 gram). Memiliki kilau mewah, garansi keaslian, dan cocok untuk pemakaian maupun investasi.','24K',1.000,1620000.00,1571000.00,13,'[\"/images/products/anting.png\"]',1,1,1,55,'2026-09-01 20:16:32','2026-09-01 20:16:32',NULL),(56,4,'JW-ANT-RANTAIBINTANG-14S','Anting Rantai Bintang (1/4 Suku - 1,65g)','anting-rantai-bintang-14-suku-165g','Anting murni model Rantai Bintang berkadar 24 karat (999) dengan ukuran berat 1/4 Suku (1.65 gram). Memiliki kilau mewah, garansi keaslian, dan cocok untuk pemakaian maupun investasi.','24K',1.650,2673000.00,2593000.00,22,'[\"/images/products/anting.png\"]',1,1,0,56,'2026-09-01 20:16:32','2026-09-01 20:16:32',NULL),(57,4,'JW-ANT-MICIMOUSE-05G','Anting Micimouse (Setengah Gram - 0,5g)','anting-micimouse-setengah-gram-05g','Anting murni model Micimouse berkadar 24 karat (999) dengan ukuran berat Setengah Gram (0.5 gram). Memiliki kilau mewah, garansi keaslian, dan cocok untuk pemakaian maupun investasi.','24K',0.500,810000.00,786000.00,15,'[\"/images/products/anting.png\"]',1,1,0,57,'2026-09-01 20:16:32','2026-09-01 20:16:32',NULL),(58,4,'JW-ANT-MICIMOUSE-10G','Anting Micimouse 1 Gram','anting-micimouse-1-gram','Anting murni model Micimouse berkadar 24 karat (999) dengan ukuran berat 1 Gram (1 gram). Memiliki kilau mewah, garansi keaslian, dan cocok untuk pemakaian maupun investasi.','24K',1.000,1620000.00,1571000.00,21,'[\"/images/products/anting.png\"]',1,1,0,58,'2026-09-01 20:16:32','2026-09-01 20:16:32',NULL),(59,4,'JW-ANT-MICIMOUSE-14S','Anting Micimouse (1/4 Suku - 1,65g)','anting-micimouse-14-suku-165g','Anting murni model Micimouse berkadar 24 karat (999) dengan ukuran berat 1/4 Suku (1.65 gram). Memiliki kilau mewah, garansi keaslian, dan cocok untuk pemakaian maupun investasi.','24K',1.650,2673000.00,2593000.00,15,'[\"/images/products/anting.png\"]',1,1,0,59,'2026-09-01 20:16:32','2026-09-01 20:16:32',NULL),(60,4,'JW-ANT-PATAM-05G','Anting Patam (Setengah Gram - 0,5g)','anting-patam-setengah-gram-05g','Anting murni model Patam berkadar 24 karat (999) dengan ukuran berat Setengah Gram (0.5 gram). Memiliki kilau mewah, garansi keaslian, dan cocok untuk pemakaian maupun investasi.','24K',0.500,810000.00,786000.00,13,'[\"/images/products/anting.png\"]',1,1,0,60,'2026-09-01 20:16:32','2026-09-01 20:16:32',NULL),(61,4,'JW-ANT-PATAM-10G','Anting Patam 1 Gram','anting-patam-1-gram','Anting murni model Patam berkadar 24 karat (999) dengan ukuran berat 1 Gram (1 gram). Memiliki kilau mewah, garansi keaslian, dan cocok untuk pemakaian maupun investasi.','24K',1.000,1620000.00,1571000.00,25,'[\"/images/products/anting.png\"]',1,1,0,61,'2026-09-01 20:16:32','2026-09-01 20:16:32',NULL),(62,4,'JW-ANT-PATAM-14S','Anting Patam (1/4 Suku - 1,65g)','anting-patam-14-suku-165g','Anting murni model Patam berkadar 24 karat (999) dengan ukuran berat 1/4 Suku (1.65 gram). Memiliki kilau mewah, garansi keaslian, dan cocok untuk pemakaian maupun investasi.','24K',1.650,2673000.00,2593000.00,12,'[\"/images/products/anting.png\"]',1,1,0,62,'2026-09-01 20:16:32','2026-09-01 20:16:32',NULL);
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reservations`
--

DROP TABLE IF EXISTS `reservations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reservations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `reservation_code` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `type` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'purchase',
  `product_id` bigint unsigned DEFAULT NULL,
  `price_negotiation_id` bigint unsigned DEFAULT NULL,
  `quantity` smallint NOT NULL DEFAULT '1',
  `agreed_price` decimal(12,2) DEFAULT NULL,
  `preferred_date` date NOT NULL,
  `preferred_time` time DEFAULT NULL,
  `payment_method` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','confirmed','ready','completed','cancelled','expired') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `pawn_gold_description` text COLLATE utf8mb4_unicode_ci,
  `pawn_gold_purity` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pawn_weight_gram` decimal(12,3) DEFAULT NULL,
  `pawn_amount_requested` decimal(15,2) DEFAULT NULL,
  `installment_tenure` int DEFAULT NULL,
  `installment_down_payment` decimal(15,2) DEFAULT NULL,
  `admin_notes` text COLLATE utf8mb4_unicode_ci,
  `confirmed_by` bigint unsigned DEFAULT NULL,
  `confirmed_at` timestamp NULL DEFAULT NULL,
  `expired_at` timestamp NULL DEFAULT NULL,
  `transaction_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reservations_reservation_code_unique` (`reservation_code`),
  KEY `reservations_product_id_foreign` (`product_id`),
  KEY `reservations_confirmed_by_foreign` (`confirmed_by`),
  KEY `reservations_user_id_status_index` (`user_id`,`status`),
  KEY `reservations_preferred_date_index` (`preferred_date`),
  KEY `reservations_transaction_id_foreign` (`transaction_id`),
  KEY `reservations_price_negotiation_id_foreign` (`price_negotiation_id`),
  CONSTRAINT `reservations_confirmed_by_foreign` FOREIGN KEY (`confirmed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `reservations_price_negotiation_id_foreign` FOREIGN KEY (`price_negotiation_id`) REFERENCES `price_negotiations` (`id`) ON DELETE SET NULL,
  CONSTRAINT `reservations_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `reservations_transaction_id_foreign` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE SET NULL,
  CONSTRAINT `reservations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reservations`
--

LOCK TABLES `reservations` WRITE;
/*!40000 ALTER TABLE `reservations` DISABLE KEYS */;
/*!40000 ALTER TABLE `reservations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reward_programs`
--

DROP TABLE IF EXISTS `reward_programs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reward_programs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `type` enum('points','cashback','discount','gift','tier_upgrade') COLLATE utf8mb4_unicode_ci NOT NULL,
  `earn_rule` json DEFAULT NULL COMMENT 'Aturan perolehan poin, misal: {"per_amount":100000,"points":1}',
  `redeem_rule` json DEFAULT NULL COMMENT 'Aturan penukaran, misal: {"points":100,"discount":10000}',
  `points_per_transaction` int NOT NULL DEFAULT '0',
  `min_transaction_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reward_programs`
--

LOCK TABLES `reward_programs` WRITE;
/*!40000 ALTER TABLE `reward_programs` DISABLE KEYS */;
INSERT INTO `reward_programs` VALUES (1,'Poin per Transaksi','Dapatkan 1 poin setiap kali menyelesaikan transaksi di toko.','points','{\"event\": \"transaction_completed\", \"points\": 1}',NULL,1,100000.00,1,NULL,NULL,'2026-09-01 20:16:35','2026-09-01 20:16:35'),(2,'Reward ke-10 Transaksi ΓÇô Gold','Pelanggan yang mencapai 10 transaksi selesai mendapatkan diskon khusus dan cuci emas gratis.','gift','{\"milestone_transactions\": 10}','{\"benefit\": \"diskon_khusus + cuci_emas_gratis\"}',0,0.00,1,NULL,NULL,'2026-09-01 20:16:35','2026-09-01 20:16:35');
/*!40000 ALTER TABLE `reward_programs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reward_redemptions`
--

DROP TABLE IF EXISTS `reward_redemptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reward_redemptions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `reward_program_id` bigint unsigned DEFAULT NULL,
  `transaction_id` bigint unsigned DEFAULT NULL,
  `type` enum('earn','redeem','adjust','expire') COLLATE utf8mb4_unicode_ci NOT NULL,
  `points_change` int NOT NULL COMMENT 'Positif = earn, Negatif = redeem/expire',
  `points_before` int NOT NULL,
  `points_after` int NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `processed_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `reward_redemptions_reward_program_id_foreign` (`reward_program_id`),
  KEY `reward_redemptions_processed_by_foreign` (`processed_by`),
  KEY `reward_redemptions_user_id_type_index` (`user_id`,`type`),
  KEY `reward_redemptions_transaction_id_index` (`transaction_id`),
  CONSTRAINT `reward_redemptions_processed_by_foreign` FOREIGN KEY (`processed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `reward_redemptions_reward_program_id_foreign` FOREIGN KEY (`reward_program_id`) REFERENCES `reward_programs` (`id`) ON DELETE SET NULL,
  CONSTRAINT `reward_redemptions_transaction_id_foreign` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE SET NULL,
  CONSTRAINT `reward_redemptions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reward_redemptions`
--

LOCK TABLES `reward_redemptions` WRITE;
/*!40000 ALTER TABLE `reward_redemptions` DISABLE KEYS */;
/*!40000 ALTER TABLE `reward_redemptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transaction_items`
--

DROP TABLE IF EXISTS `transaction_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transaction_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `transaction_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned DEFAULT NULL,
  `product_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Snapshot nama produk saat transaksi',
  `gold_purity` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Snapshot kadar emas',
  `weight_gram` decimal(8,3) NOT NULL,
  `quantity` smallint NOT NULL DEFAULT '1',
  `price_per_unit` decimal(15,2) NOT NULL,
  `subtotal` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `transaction_items_product_id_foreign` (`product_id`),
  KEY `transaction_items_transaction_id_index` (`transaction_id`),
  CONSTRAINT `transaction_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  CONSTRAINT `transaction_items_transaction_id_foreign` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transaction_items`
--

LOCK TABLES `transaction_items` WRITE;
/*!40000 ALTER TABLE `transaction_items` DISABLE KEYS */;
INSERT INTO `transaction_items` VALUES (1,1,1,'Cincin Bangkok (Setengah Gram - 0,5g)','24K',0.500,1,810000.00,810000.00,'2026-09-01 20:16:35','2026-09-01 20:16:35');
/*!40000 ALTER TABLE `transaction_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `transaction_code` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `type` enum('purchase','buyback','installment','pawn') COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('draft','confirmed','in_progress','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `gold_price_id` bigint unsigned DEFAULT NULL,
  `subtotal` decimal(15,2) NOT NULL,
  `admin_fee` decimal(12,2) NOT NULL DEFAULT '0.00',
  `discount` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT 'Diskon dari reward/poin',
  `total_amount` decimal(15,2) NOT NULL,
  `payment_method` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'cash, transfer, dll. ΓÇö dibayar di toko',
  `payment_date` date DEFAULT NULL,
  `payment_proof` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reservation_id` bigint unsigned DEFAULT NULL,
  `processed_by` bigint unsigned NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `transactions_transaction_code_unique` (`transaction_code`),
  KEY `transactions_gold_price_id_foreign` (`gold_price_id`),
  KEY `transactions_reservation_id_foreign` (`reservation_id`),
  KEY `transactions_processed_by_foreign` (`processed_by`),
  KEY `transactions_user_id_type_status_index` (`user_id`,`type`,`status`),
  KEY `transactions_transaction_code_index` (`transaction_code`),
  CONSTRAINT `transactions_gold_price_id_foreign` FOREIGN KEY (`gold_price_id`) REFERENCES `gold_prices` (`id`) ON DELETE SET NULL,
  CONSTRAINT `transactions_processed_by_foreign` FOREIGN KEY (`processed_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `transactions_reservation_id_foreign` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE SET NULL,
  CONSTRAINT `transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transactions`
--

LOCK TABLES `transactions` WRITE;
/*!40000 ALTER TABLE `transactions` DISABLE KEYS */;
INSERT INTO `transactions` VALUES (1,'TX-20260902-0001',2,'purchase','completed',30,810000.00,5000.00,0.00,815000.00,'transfer','2026-09-02',NULL,NULL,1,'Pembelian lunas Cincin Bangkok (Setengah Gram - 0,5g)','2026-09-01 20:16:35','2026-09-01 20:16:35');
/*!40000 ALTER TABLE `transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','customer') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'customer',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Administrator','admin@tokoemas.com',NULL,'$2y$12$6d/ZwqlPzRRaetJEoNOexeMWMar/KgqhKikWrFz6KFizRDoSEGPq6','admin',1,NULL,'2026-09-01 20:16:31','2026-09-01 20:16:31'),(2,'Budi Pelanggan','customer@gmail.com',NULL,'$2y$12$olc2qDp91qfq4rQEr9ISwO5Z2EO5sQVZtrZSCnH/vY1RCcWYTto7G','customer',1,NULL,'2026-09-01 20:16:31','2026-09-01 20:16:31');
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

-- Dump completed on 2026-09-02  3:25:35
