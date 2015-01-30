-- phpMyAdmin SQL Dump
-- version 4.2.7.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 30, 2015 at 12:38 
-- Server version: 5.6.20
-- PHP Version: 5.5.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `infsecApp`
--

-- --------------------------------------------------------

--
-- Table structure for table `coordinates`
--

CREATE TABLE IF NOT EXISTS `coordinates` (
`ID` bigint(20) unsigned NOT NULL,
  `IMEI` varchar(15) COLLATE utf8_german2_ci NOT NULL,
  `LATITUDE` double NOT NULL,
  `LONGITUDE` double NOT NULL,
  `ACCURACY` double NOT NULL,
  `TIME` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci AUTO_INCREMENT=12 ;

--
-- Dumping data for table `coordinates`
--

INSERT INTO `coordinates` (`ID`, `IMEI`, `LATITUDE`, `LONGITUDE`, `ACCURACY`, `TIME`) VALUES
(1, '123456789012345', 47.2649028, 11.3963183, 10, '2015-01-27 19:24:27'),
(5, '123456789012345', 47.2651041, 11.3975588, 25, '2015-01-27 19:24:27'),
(8, '123456789012345', 47.266, 11.395, 10, '2015-01-27 19:24:27');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
`ID` bigint(20) unsigned NOT NULL,
  `USERNAME` varchar(100) COLLATE utf8_german2_ci NOT NULL,
  `PASSWORD` varchar(72) COLLATE utf8_german2_ci NOT NULL,
  `IMEI` varchar(15) COLLATE utf8_german2_ci NOT NULL,
  `TO_LOCK` tinyint(1) NOT NULL DEFAULT '0',
  `NEW_PHONE_PW` varchar(40) COLLATE utf8_german2_ci NOT NULL,
  `WIPE` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci AUTO_INCREMENT=37 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`ID`, `USERNAME`, `PASSWORD`, `IMEI`, `TO_LOCK`, `NEW_PHONE_PW`, `WIPE`) VALUES
(34, 'Steve', '$6$mPv0JRFQoIA7P$8hXIp/UB25hvmGcgKun3A/fpyu8zbcnK6Ohn9DfkYSTYh9dYI2tJYtw', '111111111111111', 0, '', 0),
(35, 'Bla', '$2y$10$9jHSEQA2PquMdAzVTx3BPe3MMMwfX8cF280VrU2mkazhyadEhRTR2', '111111111111112', 0, '', 0),
(36, 'Alex', '$2y$10$1beWPvtoqnoEDSxgLWJzyOa9jAsjP9OIZrxarTsnifpCLsD2Hwc7O', '123456789012345', 0, '', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `coordinates`
--
ALTER TABLE `coordinates`
 ADD PRIMARY KEY (`ID`), ADD UNIQUE KEY `ID` (`ID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
 ADD PRIMARY KEY (`ID`), ADD UNIQUE KEY `ID` (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `coordinates`
--
ALTER TABLE `coordinates`
MODIFY `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
MODIFY `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=37;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
