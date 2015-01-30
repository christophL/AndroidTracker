-- phpMyAdmin SQL Dump
-- version 4.2.7.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 30, 2015 at 11:44 
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
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
`ID` bigint(20) unsigned NOT NULL,
  `USERNAME` varchar(100) COLLATE utf8_german2_ci NOT NULL,
  `PASSWORD` varchar(200) COLLATE utf8_german2_ci NOT NULL,
  `SALT` varchar(16) COLLATE utf8_german2_ci NOT NULL,
  `IMEI` varchar(15) COLLATE utf8_german2_ci NOT NULL,
  `TO_LOCK` tinyint(1) NOT NULL DEFAULT '0',
  `NEW_PHONE_PW` varchar(40) COLLATE utf8_german2_ci NOT NULL,
  `WIPE` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci AUTO_INCREMENT=35 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`ID`, `USERNAME`, `PASSWORD`, `SALT`, `IMEI`, `TO_LOCK`, `NEW_PHONE_PW`, `WIPE`) VALUES
(33, 'Alex', '$6$w8q0x1/gwwcJo$KgF71hh7akpOU0zNwKlj.JSGowRQHKMw2lq0F13YzKVECdUj5F/MomAfUS4xs.4rjMpDmE6rWcTp9oZ4QxRtV0', '$6$w8q0x1/gwwcJo', '123456789012345', 0, '', 0),
(34, 'Steve', '$6$mPv0JRFQoIA7P$8hXIp/UB25hvmGcgKun3A/fpyu8zbcnK6Ohn9DfkYSTYh9dYI2tJYtwCqHoxln/y.2itEPJMNzdmpqGgUEQ09.', '$6$mPv0JRFQoIA7P', '111111111111111', 0, '', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
 ADD PRIMARY KEY (`ID`), ADD UNIQUE KEY `ID` (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
MODIFY `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=35;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
