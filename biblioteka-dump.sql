-- MariaDB dump 10.17  Distrib 10.4.12-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: biblioteka
-- ------------------------------------------------------
-- Server version	10.4.12-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `bibliotekarz`
--

DROP TABLE IF EXISTS `bibliotekarz`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bibliotekarz` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `imie` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nazwisko` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `login` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `haslo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bibliotekarz`
--

LOCK TABLES `bibliotekarz` WRITE;
/*!40000 ALTER TABLE `bibliotekarz` DISABLE KEYS */;
INSERT INTO `bibliotekarz` VALUES (1,'Filip','Bibliotekarz','login1','haslo1'),(2,'Dorota','Karp','login2','haslo2'),(3,'Noname','Niemamweny','login3','haslo3'),(4,'Imie','Nazwisko','login4','haslo4'),(5,'Zbigniew','Niewodecki','login5','haslo5');
/*!40000 ALTER TABLE `bibliotekarz` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `czytelnik`
--

DROP TABLE IF EXISTS `czytelnik`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `czytelnik` (
  `id_czytelnik` int(11) NOT NULL AUTO_INCREMENT,
  `imie` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nazwisko` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `miasto` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `adres` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kara` int(11) DEFAULT NULL,
  `telefon` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_czytelnik`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `czytelnik`
--

LOCK TABLES `czytelnik` WRITE;
/*!40000 ALTER TABLE `czytelnik` DISABLE KEYS */;
INSERT INTO `czytelnik` VALUES (1,'Jan','Kowalski','Wroclaw','Kromera 21',0,'123456789'),(2,'Andrzej','Nowak','Wroclaw','Krzywoustego 1',0,'123123123'),(3,'Maria','Nienowak','Wroclaw','Komandorska 44',5,'321321321'),(4,'Anna','Niekowalska','Wroclaw','Wroclawska',1,'987654321'),(5,'Jan','Niewadomy','Wroclaw','Niepusta 11',0,'876543111');
/*!40000 ALTER TABLE `czytelnik` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dzial`
--

DROP TABLE IF EXISTS `dzial`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dzial` (
  `id_dzial` int(11) NOT NULL AUTO_INCREMENT,
  `nazwa` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_dzial`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dzial`
--

LOCK TABLES `dzial` WRITE;
/*!40000 ALTER TABLE `dzial` DISABLE KEYS */;
INSERT INTO `dzial` VALUES (1,'Kryminal'),(2,'Fantastyka'),(3,'Romans'),(4,'Horror'),(5,'Sensacja');
/*!40000 ALTER TABLE `dzial` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `egzemplarz`
--

DROP TABLE IF EXISTS `egzemplarz`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `egzemplarz` (
  `id_egzemplaz` int(11) NOT NULL AUTO_INCREMENT,
  `wydawnictwo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` int(11) NOT NULL,
  `rok_wydania` int(11) DEFAULT NULL,
  `id_ksiazka` int(11) NOT NULL,
  PRIMARY KEY (`id_egzemplaz`),
  KEY `id_ksiazka` (`id_ksiazka`),
  CONSTRAINT `egzemplarz_ibfk_1` FOREIGN KEY (`id_ksiazka`) REFERENCES `ksiazka` (`id_ksiazka`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `egzemplarz`
--

LOCK TABLES `egzemplarz` WRITE;
/*!40000 ALTER TABLE `egzemplarz` DISABLE KEYS */;
INSERT INTO `egzemplarz` VALUES (1,'Albatros',0,2010,5),(2,'Prószyński i S-ka',0,2013,3),(3,'Zysk i S-ka',1,2012,1),(4,'Insignis',1,2015,2),(5,'Świat Książki',0,2017,4);
/*!40000 ALTER TABLE `egzemplarz` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ksiazka`
--

DROP TABLE IF EXISTS `ksiazka`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ksiazka` (
  `id_ksiazka` int(11) NOT NULL AUTO_INCREMENT,
  `autor` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tytul` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `strony` int(11) DEFAULT NULL,
  `ISBN` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_dzial` int(11) NOT NULL,
  PRIMARY KEY (`id_ksiazka`),
  KEY `id_dzial` (`id_dzial`),
  CONSTRAINT `ksiazka_ibfk_1` FOREIGN KEY (`id_dzial`) REFERENCES `dzial` (`id_dzial`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ksiazka`
--

LOCK TABLES `ksiazka` WRITE;
/*!40000 ALTER TABLE `ksiazka` DISABLE KEYS */;
INSERT INTO `ksiazka` VALUES (1,'J.R.R. Tolkien','Hobbit',200,'978-83-2440-387-5',2),(2,'Dmitry Glukhovsky','Metro 2033',300,'978-83-653150-1-4',2),(3,'Stephen King','Joyland',150,'978-83-7839-535-5',4),(4,'Paula Hawkins','Dziewczyna z pociagu',255,'978-83-8031-450-4',1),(5,'Nicholas Sparks','Pamietnik',211,'978-83-66071-91-9',3);
/*!40000 ALTER TABLE `ksiazka` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wypozyczenie`
--

DROP TABLE IF EXISTS `wypozyczenie`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wypozyczenie` (
  `id_wypozyczenie` int(11) NOT NULL AUTO_INCREMENT,
  `data_zamow` date NOT NULL,
  `data_zwrotu` date NOT NULL,
  `data_rez` date NOT NULL,
  `id_czytelnik` int(11) NOT NULL,
  `id_egzemplaz` int(11) NOT NULL,
  PRIMARY KEY (`id_wypozyczenie`),
  KEY `id_czytelnik` (`id_czytelnik`),
  KEY `id_egzemplaz` (`id_egzemplaz`),
  CONSTRAINT `wypozyczenie_ibfk_1` FOREIGN KEY (`id_czytelnik`) REFERENCES `czytelnik` (`id_czytelnik`) ON UPDATE CASCADE,
  CONSTRAINT `wypozyczenie_ibfk_2` FOREIGN KEY (`id_egzemplaz`) REFERENCES `egzemplarz` (`id_egzemplaz`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wypozyczenie`
--

LOCK TABLES `wypozyczenie` WRITE;
/*!40000 ALTER TABLE `wypozyczenie` DISABLE KEYS */;
INSERT INTO `wypozyczenie` VALUES (1,'2015-08-23','2015-09-23','2015-08-20',1,1),(2,'2015-07-23','2015-08-23','2015-07-20',2,2),(3,'2017-07-23','2017-08-23','2017-07-20',3,3),(4,'2017-05-23','2017-06-23','2017-05-20',4,4),(5,'2019-05-23','2019-06-23','2019-05-20',5,5);
/*!40000 ALTER TABLE `wypozyczenie` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-05-04 22:16:44
