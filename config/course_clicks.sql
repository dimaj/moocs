-- phpMyAdmin SQL Dump
-- version 3.5.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 04, 2013 at 10:53 AM
-- Server version: 5.5.29-0ubuntu0.12.04.1
-- PHP Version: 5.3.10-1ubuntu3.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `moocs1`
--

-- --------------------------------------------------------

--
-- Table structure for table `newCourses`
--

DROP TABLE IF EXISTS `course_clicks`;
CREATE TABLE IF NOT EXISTS `course_clicks` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id of an entry',
  `cid` int(11) NOT NULL COMMENT 'class id',
  `numclicks` int(11) NOT NULL COMMENT 'how many times was this class clicked',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Tracks how many times has a course been clicked' AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
