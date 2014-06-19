-- phpMyAdmin SQL Dump
-- version 4.1.6
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jun 18, 2014 at 12:37 PM
-- Server version: 5.6.16
-- PHP Version: 5.5.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `gotprojects`
--

-- --------------------------------------------------------

--
-- Table structure for table `default_list`
--

CREATE TABLE IF NOT EXISTS `default_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_type_id` int(11) NOT NULL,
  `list_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=25 ;

--
-- Dumping data for table `default_list`
--

INSERT INTO `default_list` (`id`, `project_type_id`, `list_id`) VALUES
(4, 2, 6),
(5, 3, 6),
(6, 2, 7),
(7, 3, 7),
(8, 2, 8),
(9, 3, 8),
(10, 2, 9),
(11, 3, 9),
(12, 2, 10),
(13, 3, 10),
(16, 2, 12),
(17, 3, 12),
(18, 2, 11),
(19, 2, 13),
(20, 3, 13),
(21, 2, 14),
(22, 3, 14),
(23, 2, 15),
(24, 3, 15);

-- --------------------------------------------------------

--
-- Table structure for table `field`
--

CREATE TABLE IF NOT EXISTS `field` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL,
  `datatype` varchar(250) NOT NULL,
  `default_value` varchar(250) NOT NULL,
  `searchable` int(11) NOT NULL,
  `sortable` int(11) NOT NULL,
  `published` int(11) NOT NULL,
  `count_for_completion` int(11) NOT NULL,
  `options` varchar(8192) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=48 ;

--
-- Dumping data for table `field`
--

INSERT INTO `field` (`id`, `title`, `datatype`, `default_value`, `searchable`, `sortable`, `published`, `count_for_completion`, `options`) VALUES
(3, 'Create Domain on server', 'checkbox', '0', 0, 0, 1, 1, '[""]'),
(6, 'Create Customer Email', 'checkbox', '0', 0, 0, 1, 1, '[""]'),
(7, 'Upload Template', 'checkbox', '0', 0, 0, 1, 1, '[""]'),
(8, 'Joomla Server Con Email Setup', 'checkbox', '0', 0, 0, 1, 1, '[""]'),
(9, 'Upload Site', 'checkbox', '0', 0, 0, 1, 1, '[""]'),
(10, 'Create Menu Items', 'checkbox', '0', 0, 0, 1, 1, '[""]'),
(16, 'Park domain if any', 'string', '0', 0, 0, 1, 1, '[""]'),
(17, 'Upload Content', 'checkbox', '0', 0, 0, 1, 1, '[""]'),
(18, 'Google Map Module', 'checkbox', '0', 0, 0, 1, 1, '[""]'),
(19, 'Insert Favicon', 'checkbox', '0', 0, 0, 1, 1, '[""]'),
(20, 'Design Favicon', 'checkbox', '0', 0, 0, 1, 1, '[""]'),
(21, 'Insert Touch Device Image', 'checkbox', '0', 0, 0, 1, 1, '[""]'),
(22, 'Design Touch Device Image', 'checkbox', '0', 0, 0, 1, 1, '[""]'),
(23, 'Insert Logo', 'checkbox', '0', 0, 0, 1, 1, '[""]'),
(24, 'Design Logo for Site', 'checkbox', '0', 0, 0, 1, 1, '[""]'),
(25, 'Create Site Map', 'checkbox', '0', 0, 0, 1, 1, '[""]'),
(26, 'Meta Description', 'checkbox', '0', 0, 0, 1, 1, '[""]'),
(27, 'Page Titles SEO Friendly', 'checkbox', '0', 0, 0, 1, 1, '[""]'),
(28, 'Client Name', 'string', '0', 1, 1, 1, 1, '[""]'),
(29, 'Project Description', 'string', '0', 1, 1, 1, 1, '[""]'),
(30, 'Webmaster Tools Setup', 'checkbox', '0', 0, 0, 1, 1, '[""]'),
(31, 'Setup Google Analitics', 'checkbox', '0', 0, 0, 1, 1, '[""]'),
(32, 'Create Gmail Account ( _ @jimly.co.za)', 'string', '0', 0, 0, 1, 1, '[""]'),
(33, 'Create Tumblr Account', 'string', '0', 0, 0, 1, 1, '[""]'),
(34, 'RSSEO Setup', 'checkbox', '0', 0, 0, 1, 1, '[""]'),
(35, 'Form Setup (Request a Quote)', 'checkbox', '0', 0, 0, 1, 1, '[""]'),
(36, 'Register Domain', 'string', '0', 0, 0, 1, 1, '[""]'),
(37, 'FS Creator (Gotweb Client)', 'checkbox', '0', 0, 0, 1, 1, '[""]'),
(38, 'Invoice Build Of Site', 'checkbox', '0', 0, 0, 1, 1, '[""]'),
(39, 'Receive Signed Web Order Form', 'checkbox', '0', 0, 0, 1, 1, '[""]'),
(40, 'Load Client In Pastel', 'checkbox', '0', 0, 0, 1, 1, '[""]'),
(41, 'Quotation Sent (Number)', 'string', '0', 0, 0, 1, 1, '[""]'),
(42, 'Deposit Received', 'checkbox', '0', 0, 0, 1, 1, '[""]'),
(43, 'Create Recurring Yearly Invoice #', 'string', '0', 0, 0, 1, 1, '[""]'),
(44, 'Create Recurring Monthly Invoice #', 'string', '0', 0, 0, 1, 1, '[""]'),
(45, 'Get / Write Site Content', 'checkbox', '0', 0, 0, 1, 1, '[""]'),
(46, 'Post Meta Keywords', 'checkbox', '0', 0, 0, 1, 1, '[""]'),
(47, 'Google Places Listing', 'checkbox', '0', 0, 0, 1, 1, '[""]');

-- --------------------------------------------------------

--
-- Table structure for table `field_list`
--

CREATE TABLE IF NOT EXISTS `field_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `field_id` int(11) NOT NULL,
  `list_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=64 ;

--
-- Dumping data for table `field_list`
--

INSERT INTO `field_list` (`id`, `field_id`, `list_id`) VALUES
(1, 3, 7),
(4, 6, 7),
(5, 9, 8),
(6, 8, 8),
(7, 7, 7),
(8, 10, 7),
(9, 16, 7),
(10, 17, 9),
(11, 18, 9),
(12, 20, 10),
(13, 22, 10),
(14, 24, 10),
(18, 19, 8),
(19, 19, 9),
(20, 21, 8),
(21, 21, 9),
(22, 23, 8),
(23, 23, 9),
(24, 25, 8),
(25, 25, 11),
(26, 26, 11),
(27, 26, 12),
(28, 28, 13),
(29, 29, 13),
(30, 30, 11),
(31, 30, 12),
(32, 31, 11),
(33, 31, 12),
(34, 32, 11),
(35, 32, 12),
(36, 33, 12),
(37, 34, 8),
(38, 35, 8),
(39, 35, 9),
(40, 36, 14),
(41, 37, 15),
(42, 38, 6),
(43, 38, 15),
(46, 39, 6),
(49, 41, 6),
(50, 41, 13),
(51, 42, 6),
(54, 44, 6),
(55, 43, 6),
(56, 43, 15),
(57, 45, 9),
(58, 40, 6),
(59, 40, 15),
(60, 27, 11),
(61, 27, 12),
(62, 46, 11),
(63, 46, 12);

-- --------------------------------------------------------

--
-- Table structure for table `field_permissions`
--

CREATE TABLE IF NOT EXISTS `field_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `field_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `field_id` (`field_id`),
  KEY `group_id` (`group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=172 ;

--
-- Dumping data for table `field_permissions`
--

INSERT INTO `field_permissions` (`id`, `field_id`, `group_id`) VALUES
(39, 3, 1),
(40, 3, 10),
(41, 3, 12),
(42, 6, 1),
(43, 6, 10),
(44, 6, 12),
(45, 7, 1),
(46, 7, 10),
(47, 7, 12),
(48, 8, 1),
(49, 8, 10),
(50, 8, 12),
(51, 9, 1),
(52, 9, 10),
(53, 9, 12),
(54, 10, 1),
(55, 10, 10),
(56, 10, 12),
(57, 16, 1),
(58, 16, 7),
(59, 16, 10),
(60, 16, 12),
(61, 16, 13),
(71, 20, 1),
(72, 20, 13),
(73, 20, 14),
(74, 21, 1),
(75, 21, 10),
(76, 21, 12),
(77, 17, 1),
(78, 17, 10),
(79, 17, 11),
(80, 17, 12),
(81, 18, 1),
(82, 18, 10),
(83, 18, 12),
(84, 19, 1),
(85, 19, 10),
(86, 19, 12),
(87, 22, 1),
(88, 22, 13),
(89, 22, 14),
(90, 23, 1),
(91, 23, 10),
(92, 23, 12),
(93, 24, 1),
(94, 24, 13),
(95, 24, 14),
(96, 25, 1),
(97, 25, 10),
(98, 25, 12),
(99, 26, 1),
(100, 26, 10),
(101, 26, 11),
(102, 26, 12),
(103, 27, 1),
(104, 27, 10),
(105, 27, 11),
(106, 27, 12),
(107, 28, 1),
(108, 28, 10),
(109, 28, 11),
(110, 28, 12),
(111, 28, 13),
(112, 29, 1),
(113, 29, 7),
(114, 29, 8),
(115, 29, 10),
(116, 29, 11),
(117, 29, 12),
(118, 29, 13),
(119, 30, 1),
(120, 30, 10),
(121, 30, 12),
(122, 30, 13),
(123, 31, 1),
(124, 31, 10),
(125, 31, 12),
(126, 31, 13),
(127, 32, 1),
(128, 32, 11),
(129, 33, 1),
(130, 33, 11),
(131, 34, 1),
(132, 34, 10),
(133, 34, 12),
(134, 35, 1),
(135, 35, 10),
(136, 35, 12),
(137, 36, 1),
(138, 36, 13),
(139, 37, 1),
(140, 37, 11),
(144, 39, 1),
(145, 39, 11),
(146, 40, 1),
(147, 40, 11),
(148, 40, 13),
(149, 41, 1),
(150, 41, 11),
(151, 41, 13),
(152, 42, 1),
(153, 42, 11),
(154, 42, 13),
(155, 43, 1),
(156, 43, 11),
(157, 43, 13),
(158, 44, 1),
(159, 44, 11),
(160, 44, 13),
(161, 45, 1),
(162, 45, 11),
(163, 45, 13),
(164, 46, 1),
(165, 46, 11),
(166, 46, 12),
(167, 38, 11),
(168, 38, 13),
(169, 47, 1),
(170, 47, 10),
(171, 47, 13);

-- --------------------------------------------------------

--
-- Table structure for table `field_values`
--

CREATE TABLE IF NOT EXISTS `field_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `field_id` int(11) NOT NULL,
  `value` text NOT NULL,
  `list_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=63 ;

-- --------------------------------------------------------

--
-- Table structure for table `list`
--

CREATE TABLE IF NOT EXISTS `list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

--
-- Dumping data for table `list`
--

INSERT INTO `list` (`id`, `title`) VALUES
(6, 'New Order Start'),
(7, 'Site Server Setup'),
(8, 'Build Site'),
(9, 'Site Content'),
(10, 'Designer Web Site'),
(11, 'SEO Basic'),
(12, 'SEO Client'),
(13, 'Project Info'),
(14, 'Domain Maintance'),
(15, 'Admin');

-- --------------------------------------------------------

--
-- Table structure for table `notes`
--

CREATE TABLE IF NOT EXISTS `notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `created_by` int(11) NOT NULL,
  `assigned_user` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `value` varchar(2048) DEFAULT NULL,
  `project_id` int(11) NOT NULL,
  `options` varchar(4096) NOT NULL,
  `count_for_completion` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `project`
--

CREATE TABLE IF NOT EXISTS `project` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL,
  `project_type` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `project_list`
--

CREATE TABLE IF NOT EXISTS `project_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `project_id` int(11) NOT NULL,
  `custom_id` int(11) DEFAULT NULL,
  `origin_list` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=23 ;

-- --------------------------------------------------------

--
-- Table structure for table `project_type`
--

CREATE TABLE IF NOT EXISTS `project_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `project_type`
--

INSERT INTO `project_type` (`id`, `title`) VALUES
(2, 'New Website'),
(3, 'New site + SEO');

-- --------------------------------------------------------

--
-- Table structure for table `project_users`
--

CREATE TABLE IF NOT EXISTS `project_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL DEFAULT '0',
  `uid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Table structure for table `updates`
--

CREATE TABLE IF NOT EXISTS `updates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `updates` text NOT NULL,
  `uid` int(11) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `usergroups`
--

CREATE TABLE IF NOT EXISTS `usergroups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=15 ;

--
-- Dumping data for table `usergroups`
--

INSERT INTO `usergroups` (`id`, `title`) VALUES
(1, 'Super User'),
(7, 'Administrator'),
(8, 'Basic'),
(10, 'Builders of Sites'),
(11, 'Elanri'),
(12, 'Amanda'),
(13, 'Henry & Neil'),
(14, 'Designer');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(250) NOT NULL,
  `password` varchar(250) NOT NULL,
  `uid` int(11) NOT NULL,
  `group_id` int(11) NOT NULL DEFAULT '-1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `uid`, `group_id`) VALUES
(1, 'jayps', 'password', 1, 1),
(11, 'neil', 'Neil398', 1402563244, 1),
(12, '', '', 1402563303, -1),
(13, 'hnl_henry', 'Pikany$465GoT', 1402563328, 1),
(14, 'Elize', 'Elize474', 1402579099, 14),
(15, 'Elanri', 'Elanri11', 1402579640, 11);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
