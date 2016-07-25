-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 25, 2016 at 09:08 PM
-- Server version: 5.5.50-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.17

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `transfriendly`
--
CREATE DATABASE IF NOT EXISTS `transfriendly` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `transfriendly`;

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
CREATE TABLE IF NOT EXISTS `category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `type` int(10) unsigned NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `create_user` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Truncate table before insert `category`
--

TRUNCATE TABLE `category`;
--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `name`, `type`, `create_date`, `create_user`) VALUES
(1, 'general', 1, '2016-07-25 21:06:13', 1);

-- --------------------------------------------------------

--
-- Table structure for table `join_location_category`
--

DROP TABLE IF EXISTS `join_location_category`;
CREATE TABLE IF NOT EXISTS `join_location_category` (
  `location_id` int(10) unsigned NOT NULL,
  `category_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`location_id`,`category_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Truncate table before insert `join_location_category`
--

TRUNCATE TABLE `join_location_category`;
--
-- Dumping data for table `join_location_category`
--

INSERT INTO `join_location_category` (`location_id`, `category_id`, `user_id`, `create_date`) VALUES
(1, 1, 1, '2016-07-25 21:04:32');

-- --------------------------------------------------------

--
-- Table structure for table `location`
--

DROP TABLE IF EXISTS `location`;
CREATE TABLE IF NOT EXISTS `location` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `address` varchar(64) DEFAULT NULL,
  `address2` varchar(64) DEFAULT NULL,
  `city` varchar(32) DEFAULT NULL,
  `state` varchar(2) DEFAULT NULL,
  `zip` varchar(16) DEFAULT NULL,
  `phone` varchar(64) DEFAULT NULL,
  `lat` decimal(10,8) DEFAULT NULL,
  `lng` decimal(11,8) DEFAULT NULL,
  `description` text,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `create_user_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Truncate table before insert `location`
--

TRUNCATE TABLE `location`;
--
-- Dumping data for table `location`
--

INSERT INTO `location` (`id`, `name`, `address`, `address2`, `city`, `state`, `zip`, `phone`, `lat`, `lng`, `description`, `create_date`, `create_user_id`) VALUES
(1, 'Macys', '151 W 34th St', NULL, 'New York', 'NY', '10001', '2126954400', 40.75119750, -73.99006720, 'Department store chain providing brand-name clothing, accessories, home furnishings & housewares. ', '2016-07-25 21:01:40', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `hash` varchar(255) NOT NULL,
  `email` varchar(64) NOT NULL,
  `is_verified` enum('y','n') NOT NULL DEFAULT 'n',
  `is_transgender` enum('y','n') NOT NULL DEFAULT 'n',
  `is_curator` enum('y','n') NOT NULL DEFAULT 'n',
  `is_admin` enum('y','n') NOT NULL DEFAULT 'n',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_date` datetime DEFAULT NULL,
  `last_ip` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Truncate table before insert `user`
--

TRUNCATE TABLE `user`;
--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `name`, `hash`, `email`, `is_verified`, `is_transgender`, `is_curator`, `is_admin`, `create_date`, `last_date`, `last_ip`) VALUES
(1, 'admin', '', 'dur@stop.foo', 'y', 'y', 'y', 'y', '2016-07-25 20:51:02', '2016-07-25 20:51:02', 0);
SET FOREIGN_KEY_CHECKS=1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
