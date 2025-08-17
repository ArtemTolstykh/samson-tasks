-- MySQL dump 10.13  Distrib 8.0.38, for Win64 (x86_64)
--
-- Host: localhost    Database: samson_test
-- ------------------------------------------------------
-- Server version	8.0.39

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `a_category`
--

DROP TABLE IF EXISTS `a_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `a_category` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `parent_id` int DEFAULT NULL,
  `code` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  CONSTRAINT `a_category_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `a_category` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `a_category`
--

LOCK TABLES `a_category` WRITE;
/*!40000 ALTER TABLE `a_category` DISABLE KEYS */;
INSERT INTO `a_category` VALUES (1,'Бумага',NULL,'Bumaga'),(2,'Принтеры',NULL,'Printery'),(3,'МФУ',2,'Mfu');
/*!40000 ALTER TABLE `a_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `a_price`
--

DROP TABLE IF EXISTS `a_price`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `a_price` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `price_type` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `a_price_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `a_product` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `a_price`
--

LOCK TABLES `a_price` WRITE;
/*!40000 ALTER TABLE `a_price` DISABLE KEYS */;
INSERT INTO `a_price` VALUES (1,1,'Базовая',11.50),(2,1,'Москва',12.50),(3,2,'Базовая',18.50),(4,2,'Москва',22.50),(5,3,'Базовая',3010.00),(6,3,'Москва',3500.00),(7,4,'Базовая',3310.00),(8,4,'Москва',2999.00);
/*!40000 ALTER TABLE `a_price` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `a_product`
--

DROP TABLE IF EXISTS `a_product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `a_product` (
  `id` int NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `a_product`
--

LOCK TABLES `a_product` WRITE;
/*!40000 ALTER TABLE `a_product` DISABLE KEYS */;
INSERT INTO `a_product` VALUES (1,'201','Бумага А4'),(2,'202','Бумага А3'),(3,'302','Принтер Canon'),(4,'305','Принтер HP');
/*!40000 ALTER TABLE `a_product` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `a_product_category`
--

DROP TABLE IF EXISTS `a_product_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `a_product_category` (
  `product_id` int NOT NULL,
  `category_id` int NOT NULL,
  PRIMARY KEY (`product_id`,`category_id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `a_product_category_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `a_product` (`id`) ON DELETE CASCADE,
  CONSTRAINT `a_product_category_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `a_category` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `a_product_category`
--

LOCK TABLES `a_product_category` WRITE;
/*!40000 ALTER TABLE `a_product_category` DISABLE KEYS */;
INSERT INTO `a_product_category` VALUES (1,1),(2,1),(3,3),(4,3);
/*!40000 ALTER TABLE `a_product_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `a_property`
--

DROP TABLE IF EXISTS `a_property`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `a_property` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `property_name` varchar(255) NOT NULL,
  `property_value` varchar(255) NOT NULL,
  `unit_measurement` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `a_property_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `a_product` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `a_property`
--

LOCK TABLES `a_property` WRITE;
/*!40000 ALTER TABLE `a_property` DISABLE KEYS */;
INSERT INTO `a_property` VALUES (1,1,'Плотность','100',NULL),(2,1,'Белизна','150','%'),(3,2,'Плотность','90',NULL),(4,2,'Белизна','100','%'),(5,3,'Формат','A4',NULL),(6,3,'Формат','A3',NULL),(7,3,'Тип','Лазерный',NULL),(8,4,'Формат','A3',NULL),(9,4,'Тип','Лазерный',NULL);
/*!40000 ALTER TABLE `a_property` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-05-19 21:42:38
