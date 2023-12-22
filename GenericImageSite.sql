-- MySQL dump 10.13  Distrib 8.0.18, for Win64 (x86_64)
--
-- Host: localhost    Database: GenericImageSite
-- ------------------------------------------------------
-- Server version	8.0.18

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
-- Table structure for table `Comments`
--

DROP TABLE IF EXISTS `Comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Comments` (
  `IDComment` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `User` char(20) NOT NULL,
  `IDPost` int(10) unsigned NOT NULL,
  `Text` varchar(200) NOT NULL,
  `IDComm` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`IDComment`),
  KEY `IDPostComment_idx` (`IDPost`),
  KEY `UserComment_idx` (`User`),
  KEY `IDcommentComm_idx` (`IDComm`),
  CONSTRAINT `IDPostComment` FOREIGN KEY (`IDPost`) REFERENCES `posts` (`IDPost`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `IDcommentComm` FOREIGN KEY (`IDComm`) REFERENCES `comments` (`IDComment`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `UserComment` FOREIGN KEY (`User`) REFERENCES `users` (`User`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Comments`
--

LOCK TABLES `Comments` WRITE;
/*!40000 ALTER TABLE `Comments` DISABLE KEYS */;
INSERT INTO `Comments` VALUES (2,'dreamtime',2,'To be noted, I particularly like the picture with all of them in the background',NULL),(4,'dreamtime',2,'I know right <3',2),(5,'dreamtime',2,'They are a great group of friends. It makes me go awwww every time I see them',2),(6,'dreamtime',2,'gosh she\'s so cute',NULL),(7,'dreamtime1',2,'they are adorable together <3<3',2),(8,'dreamtime1',2,'i want a copy of that picture, too',2),(9,'dreamtime1',2,'gosh she\'s so cute in that hoodie',NULL),(11,'dreamtime1',2,'I know right',6),(12,'dreamtime1',2,'so adorable, i can\'t even',NULL);
/*!40000 ALTER TABLE `Comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `LikesComm`
--

DROP TABLE IF EXISTS `LikesComm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `LikesComm` (
  `User` char(20) NOT NULL,
  `IDComment` int(10) unsigned NOT NULL,
  PRIMARY KEY (`User`,`IDComment`),
  KEY `IDCommentLikes_idx` (`IDComment`),
  CONSTRAINT `IDCommentsLikesComm` FOREIGN KEY (`IDComment`) REFERENCES `comments` (`IDComment`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `UsersLikesComm` FOREIGN KEY (`User`) REFERENCES `users` (`User`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `LikesComm`
--

LOCK TABLES `LikesComm` WRITE;
/*!40000 ALTER TABLE `LikesComm` DISABLE KEYS */;
INSERT INTO `LikesComm` VALUES ('dreamtime',4),('dreamtime1',4),('dreamtime1',6);
/*!40000 ALTER TABLE `LikesComm` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `LikesPosts`
--

DROP TABLE IF EXISTS `LikesPosts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `LikesPosts` (
  `User` char(20) NOT NULL,
  `IDPost` int(10) unsigned NOT NULL,
  PRIMARY KEY (`User`,`IDPost`),
  KEY `IDPostsLikesPosts_idx` (`IDPost`),
  CONSTRAINT `IDPostsLikesPosts` FOREIGN KEY (`IDPost`) REFERENCES `posts` (`IDPost`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `UsersLikesPosts` FOREIGN KEY (`User`) REFERENCES `users` (`User`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `LikesPosts`
--

LOCK TABLES `LikesPosts` WRITE;
/*!40000 ALTER TABLE `LikesPosts` DISABLE KEYS */;
INSERT INTO `LikesPosts` VALUES ('dreamtime1',1),('dreamtime1',2);
/*!40000 ALTER TABLE `LikesPosts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Posts`
--

DROP TABLE IF EXISTS `Posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Posts` (
  `IDPost` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `User` char(20) NOT NULL,
  `Time` datetime NOT NULL,
  `Image` varchar(52) NOT NULL,
  `Text` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`IDPost`),
  KEY `User_idx` (`User`),
  CONSTRAINT `User` FOREIGN KEY (`User`) REFERENCES `users` (`User`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Posts`
--

LOCK TABLES `Posts` WRITE;
/*!40000 ALTER TABLE `Posts` DISABLE KEYS */;
INSERT INTO `Posts` VALUES (1,'dreamtime','2020-01-26 00:58:00','/Uploads/2020-01-26-00-58-00dreamtime.png','Sayori <3'),(2,'dreamtime','2020-02-04 22:45:27','/Uploads/2020-01-26-01-11-27dreamtime.png','    Casual Monica   '),(3,'dreamtime1','2020-01-30 22:11:21','/Uploads/2020-01-30-22-11-21dreamtime1.gif','awwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwww'),(4,'dreamtime1','2020-01-30 23:11:48','/Uploads/2020-01-30-23-11-48dreamtime1.png','awwwwwww look, she\'s ready for going to the beach. god, how i wish i could have come as well. life can be so unfair sometimes :(');
/*!40000 ALTER TABLE `Posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Users`
--

DROP TABLE IF EXISTS `Users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Users` (
  `User` char(20) NOT NULL,
  `PassHash` varchar(255) NOT NULL,
  PRIMARY KEY (`User`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Users`
--

LOCK TABLES `Users` WRITE;
/*!40000 ALTER TABLE `Users` DISABLE KEYS */;
INSERT INTO `Users` VALUES ('ceiasta','$2y$10$tE/9Wcm4YJTKo0I4PUHGDel496aHN2CgeJfgXXpPY9aV2E6fRd.lO'),('celmaibunuser','$2y$10$5k.qTg7Pmu9vnl1x3Xle2.AKO1.p1U95KDh6mYTzS.WA.fiB0DXo.'),('contdeumplutura','$2y$10$IbcbVkp1iRws.yOjpld2PeIalN8ddcgvb5X0aVEOcdqVJpt1xy8gu'),('dreamtime','$2y$10$v0JH4xPHohy2NpzmNxP/4e6xwySwYci2P4LHMARtoapwR6D1fqEhu'),('dreamtime1','$2y$10$y35fHO8Rf7aSG.Xqv7cBi.GuLoGLOMcsvdTAZz.PTBNSmZuB4Rmri');
/*!40000 ALTER TABLE `Users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-02-06 22:03:54
