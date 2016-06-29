-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jun 29, 2016 at 06:36 PM
-- Server version: 5.6.21
-- PHP Version: 5.6.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `symfony_project_dev`
--
CREATE DATABASE IF NOT EXISTS `symfony_project_dev` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `symfony_project_dev`;

-- --------------------------------------------------------

--
-- Table structure for table `system_modules`
--

DROP TABLE IF EXISTS `system_modules`;
CREATE TABLE IF NOT EXISTS `system_modules` (
`id` int(5) NOT NULL,
  `parent_id` int(5) NOT NULL DEFAULT '0',
  `module_name` varchar(200) DEFAULT NULL,
  `module_alias` varchar(200) DEFAULT NULL,
  `module_type` varchar(255) DEFAULT NULL,
  `module_status` smallint(1) NOT NULL DEFAULT '0',
  `module_order` int(10) NOT NULL DEFAULT '0',
  `updated_date` int(10) NOT NULL DEFAULT '0',
  `created_date` int(10) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `system_modules`
--

INSERT INTO `system_modules` (`id`, `parent_id`, `module_name`, `module_alias`, `module_type`, `module_status`, `module_order`, `updated_date`, `created_date`) VALUES
(1, 0, 'asfasfas1', 'asfasfas', NULL, 1, 0, 1467099814, 1467099814),
(2, 0, 'asfasfas222', 'asfasfas', NULL, 1, 0, 1467099837, 1467099837),
(3, 0, 'asfasfas', 'asfasfas', NULL, 1, 0, 1467099878, 1467099878),
(4, 0, 'asfasfas', 'asfasfas', NULL, 1, 0, 1467099936, 1467099936),
(5, 0, 'asfasfas', 'asfasfas', NULL, 1, 0, 1467099959, 1467099959),
(6, 0, 'asfasfas2', 'asfasfas', NULL, 1, 0, 1467099966, 1467099966),
(7, 0, 'asfasfas2', 'asfasfas', NULL, 1, 0, 1467099973, 1467099973),
(8, 0, 'asfasfas2', 'asfasfas', NULL, 0, 2, 1467100007, 1467100007),
(9, 0, 'asfasfas2', 'asfasfas', NULL, 0, 2, 1467100017, 1467100017),
(12, 0, 'asfasfas99', 'asfasfas99', NULL, 1, 20, 1467201011, 1467198726);

-- --------------------------------------------------------

--
-- Table structure for table `system_roles`
--

DROP TABLE IF EXISTS `system_roles`;
CREATE TABLE IF NOT EXISTS `system_roles` (
`role_id` int(10) NOT NULL,
  `role_name` varchar(255) DEFAULT NULL,
  `role_type` text,
  `role_status` smallint(1) NOT NULL DEFAULT '1',
  `access` smallint(1) NOT NULL DEFAULT '0',
  `updated_date` int(10) DEFAULT NULL,
  `created_date` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `system_users`
--

DROP TABLE IF EXISTS `system_users`;
CREATE TABLE IF NOT EXISTS `system_users` (
`id` int(3) NOT NULL,
  `role_id` int(3) NOT NULL DEFAULT '0',
  `username` varchar(150) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `password` varchar(150) DEFAULT NULL,
  `status` smallint(1) NOT NULL DEFAULT '1',
  `updated_date` int(10) DEFAULT NULL,
  `created_date` int(10) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `system_users`
--

INSERT INTO `system_users` (`id`, `role_id`, `username`, `email`, `password`, `status`, `updated_date`, `created_date`) VALUES
(1, 0, 'admin', 'admin@example.com', 'e10adc3949ba59abbe56e057f20f883e', 1, 1466272809, 1466272809);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `system_modules`
--
ALTER TABLE `system_modules`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `system_roles`
--
ALTER TABLE `system_roles`
 ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `system_users`
--
ALTER TABLE `system_users`
 ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `system_modules`
--
ALTER TABLE `system_modules`
MODIFY `id` int(5) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT for table `system_roles`
--
ALTER TABLE `system_roles`
MODIFY `role_id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `system_users`
--
ALTER TABLE `system_users`
MODIFY `id` int(3) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
