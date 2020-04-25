-- MySQL dump 10.13  Distrib 8.0.16, for macos10.14 (x86_64)
--
-- Host: localhost
-- ------------------------------------------------------
-- Server version	8.0.19

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
 SET NAMES utf8 ;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `answers`
--

DROP TABLE IF EXISTS `answers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `answers` (
  `A_ID` varchar(36) NOT NULL,
  `G_ID` varchar(36) NOT NULL,
  `Q_ID` varchar(36) NOT NULL,
  `P_ID` varchar(36) NOT NULL,
  `A_Answer` text,
  `A_Marked` tinyint NOT NULL DEFAULT '0',
  `A_Correct` tinyint DEFAULT NULL,
  PRIMARY KEY (`A_ID`),
  UNIQUE KEY `A_ID_UNIQUE` (`A_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `games`
--

DROP TABLE IF EXISTS `games`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `games` (
  `G_ID` varchar(36) NOT NULL,
  `G_Name` varchar(255) NOT NULL,
  `G_Started` datetime NOT NULL,
  `G_Ended` datetime DEFAULT NULL,
  `G_Current` varchar(36) DEFAULT NULL,
  PRIMARY KEY (`G_ID`),
  UNIQUE KEY `ID_UNIQUE` (`G_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `players`
--

DROP TABLE IF EXISTS `players`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `players` (
  `P_ID` varchar(36) NOT NULL,
  `G_ID` varchar(36) NOT NULL,
  `P_Name` text,
  `P_Host` tinyint NOT NULL,
  PRIMARY KEY (`P_ID`),
  UNIQUE KEY `ID_UNIQUE` (`P_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `questions`
--

DROP TABLE IF EXISTS `questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `questions` (
  `Q_ID` varchar(36) NOT NULL,
  `R_ID` varchar(36) NOT NULL,
  `Q_Question` text NOT NULL,
  `Q_Answer` text NOT NULL,
  `Q_Order` int NOT NULL,
  `Q_Image_Question` text,
  `Q_Sound` text,
  `Q_Video` text,
  `Q_Points` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`Q_ID`),
  UNIQUE KEY `Q_ID_UNIQUE` (`Q_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rounds`
--

DROP TABLE IF EXISTS `rounds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `rounds` (
  `R_ID` varchar(36) NOT NULL,
  `G_ID` varchar(36) NOT NULL,
  `R_Round` text NOT NULL,
  `R_Order` int NOT NULL,
  PRIMARY KEY (`R_ID`),
  UNIQUE KEY `R_ID_UNIQUE` (`R_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-04-24 15:48:01
