CREATE DATABASE  IF NOT EXISTS `cc` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci */;
USE `cc`;
-- MySQL dump 10.13  Distrib 5.5.44, for debian-linux-gnu (x86_64)
--
-- Host: 127.0.0.1    Database: cc
-- ------------------------------------------------------
-- Server version	5.5.44-0ubuntu0.14.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `fk_accounts_features`
--

DROP TABLE IF EXISTS `fk_accounts_features`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fk_accounts_features` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `accounts_id` bigint(20) unsigned NOT NULL,
  `features_id` int(11) NOT NULL,
  `status` tinyint(4) DEFAULT '1' COMMENT '1: Active, 0: Inactive',
  `features_settings` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `fk_fk_accounts_features_1_idx` (`accounts_id`),
  KEY `fk_fk_accounts_features_2_idx` (`features_id`),
  CONSTRAINT `fk_fk_accounts_features_1` FOREIGN KEY (`accounts_id`) REFERENCES `cc_accounts` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_fk_accounts_features_2` FOREIGN KEY (`features_id`) REFERENCES `cc_features` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fk_accounts_features`
--

LOCK TABLES `fk_accounts_features` WRITE;
/*!40000 ALTER TABLE `fk_accounts_features` DISABLE KEYS */;
INSERT INTO `fk_accounts_features` VALUES (1,227,1,1,NULL);
/*!40000 ALTER TABLE `fk_accounts_features` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cc_features`
--

DROP TABLE IF EXISTS `cc_features`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cc_features` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `feature_name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `feature_level` int(11) NOT NULL,
  `account_type` int(11) NOT NULL,
  `service_info` text COLLATE utf8_unicode_ci,
  `feature_settings` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cc_features`
--

LOCK TABLES `cc_features` WRITE;
/*!40000 ALTER TABLE `cc_features` DISABLE KEYS */;
INSERT INTO `cc_features` VALUES (1,'Change Favicon BL',1,3,'',NULL),(2,'Google Analytics EL',2,3,NULL,NULL),(3,'Change Favicon EL',2,3,NULL,NULL),(4,'Change Favicon SL',3,3,NULL,NULL),(5,'Google Analytics SL',3,3,NULL,NULL);
/*!40000 ALTER TABLE `cc_features` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-10-01 16:05:44
