-- Database: `infsecApp`
--
-- --------------------------------------------------------
--
-- Table structure for table `coordinates`
--

CREATE TABLE `coordinates` (
 `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
 `IMEI` varchar(15) COLLATE utf8_german2_ci NOT NULL,
 `LATITUDE` double NOT NULL,
 `LONGITUDE` double NOT NULL,
 `ACCURACY` double NOT NULL,
 `TIME` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY (`ID`),
 UNIQUE KEY `ID` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
 `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
 `USERNAME` varchar(100) COLLATE utf8_german2_ci NOT NULL,
 `PASSWORD` varchar(72) COLLATE utf8_german2_ci NOT NULL,
 `IMEI` varchar(15) COLLATE utf8_german2_ci NOT NULL,
 `TO_LOCK` tinyint(1) NOT NULL DEFAULT '0',
 `NEW_PHONE_PW` varchar(40) COLLATE utf8_german2_ci DEFAULT NULL,
 `WIPE` tinyint(1) NOT NULL DEFAULT '0',
 PRIMARY KEY (`ID`),
 UNIQUE KEY `ID` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;



CREATE USER 'infsecApp'@'localhost' IDENTIFIED BY 'changeMe';
GRANT SELECT, INSERT, UPDATE ON `infsecApp`.* TO 'infsecApp'@'localhost';
