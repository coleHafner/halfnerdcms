-- phpMyAdmin SQL Dump
-- version 3.2.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 21, 2010 at 04:46 PM
-- Server version: 5.1.44
-- PHP Version: 5.3.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `mdp`
--

-- --------------------------------------------------------

--
-- Table structure for table `common_Articles`
--

DROP TABLE IF EXISTS `common_Articles`;
CREATE TABLE `common_Articles` (
  `article_id` int(100) NOT NULL AUTO_INCREMENT,
  `title` varchar(1000) DEFAULT NULL,
  `body` varchar(1000) DEFAULT NULL,
  `post_timestamp` int(100) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  `authentication_id` int(100) NOT NULL,
  PRIMARY KEY (`article_id`),
  KEY `authentication_id` (`authentication_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=52 ;

--
-- Dumping data for table `common_Articles`
--
INSERT INTO `common_Articles` VALUES(0, NULL, NULL, NULL, 0, 0);
-- --------------------------------------------------------

--
-- Table structure for table `common_ArticleToSection`
--

DROP TABLE IF EXISTS `common_ArticleToSection`;
CREATE TABLE `common_ArticleToSection` (
  `article_to_view_id` int(100) NOT NULL,
  `section_id` int(100) NOT NULL,
  PRIMARY KEY (`article_to_view_id`,`section_id`),
  KEY `section_id` (`section_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `common_ArticleToSection`
--

INSERT INTO `common_ArticleToSection` VALUES(0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `common_ArticleToView`
--

DROP TABLE IF EXISTS `common_ArticleToView`;
CREATE TABLE `common_ArticleToView` (
  `article_to_view_id` int(100) NOT NULL AUTO_INCREMENT,
  `article_id` int(100) NOT NULL,
  `view_id` int(100) NOT NULL,
  PRIMARY KEY (`article_to_view_id`),
  KEY `article_id` (`article_id`),
  KEY `view_id` (`view_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=51 ;

--
-- Dumping data for table `common_ArticleToView`
--

INSERT INTO `common_ArticleToView` VALUES(0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `common_Authentication`
--

DROP TABLE IF EXISTS `common_Authentication`;
CREATE TABLE `common_Authentication` (
  `authentication_id` int(100) NOT NULL AUTO_INCREMENT,
  `email` varchar(1000) DEFAULT NULL,
  `username` varchar(1000) DEFAULT NULL,
  `password` varchar(10000) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`authentication_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `common_Authentication`
--

INSERT INTO `common_Authentication` VALUES(0, NULL, NULL, 0);
INSERT INTO `common_Authentication` VALUES(3, 'admin@admin.com', 'admin', 1);
-- --------------------------------------------------------

--
-- Table structure for table `common_AuthenticationToPermission`
--

DROP TABLE IF EXISTS `common_AuthenticationToPermission`;
CREATE TABLE `common_AuthenticationToPermission` (
  `authentication_id` int(100) NOT NULL,
  `permission_id` int(100) NOT NULL,
  PRIMARY KEY (`authentication_id`,`permission_id`),
  KEY `permission_id` (`permission_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `common_AuthenticationToPermission`
--
INSERT INTO `common_AuthenticationToPermission` VALUES( 3, 2 );
-- --------------------------------------------------------

--
-- Table structure for table `common_Captcha`
--

DROP TABLE IF EXISTS `common_Captcha`;
CREATE TABLE `common_Captcha` (
  `captcha_id` int(100) NOT NULL AUTO_INCREMENT,
  `file_id` int(100) NOT NULL,
  `captcha_string` varchar(1000) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`captcha_id`),
  KEY `file_id` (`file_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `common_Captcha`
--

INSERT INTO `common_Captcha` VALUES(0, 0, NULL, 0);
-- --------------------------------------------------------

--
-- Table structure for table `common_Contacts`
--

DROP TABLE IF EXISTS `common_Contacts`;
CREATE TABLE `common_Contacts` (
  `contact_id` int(100) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(1000) DEFAULT NULL,
  `last_name` varchar(1000) DEFAULT NULL,
  `description` varchar(1000) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  `thumb_id` int(100) NOT NULL,
  `full_img_id` int(100) NOT NULL,
  `authentication_id` int(100) NOT NULL,
  `contact_type_id` int(100) NOT NULL,
  PRIMARY KEY (`contact_id`),
  KEY `thumb_id` (`thumb_id`),
  KEY `full_img_id` (`full_img_id`),
  KEY `authentication_id` (`authentication_id`),
  KEY `contact_type_id` (`contact_type_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;

--
-- Dumping data for table `common_Contacts`
--

INSERT INTO `common_Contacts` VALUES(0, NULL, NULL, NULL, 0, 0, 0, 0, 0);
INSERT INTO `common_Contacts` VALUES(1, 'admin', 'admin', 'Site Administrator', 1, 0, 0, 3, 8);

-- --------------------------------------------------------

--
-- Table structure for table `common_ContactTypes`
--

DROP TABLE IF EXISTS `common_ContactTypes`;
CREATE TABLE `common_ContactTypes` (
  `contact_type_id` int(100) NOT NULL AUTO_INCREMENT,
  `title` varchar(1000) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`contact_type_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=15 ;

--
-- Dumping data for table `common_ContactTypes`
--

INSERT INTO `common_ContactTypes` VALUES(0, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `common_EnvVars`
--

DROP TABLE IF EXISTS `common_EnvVars`;
CREATE TABLE `common_EnvVars` (
  `env_var_id` int(100) NOT NULL AUTO_INCREMENT,
  `title` varchar(1000) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  `content` varchar(10000) DEFAULT NULL,
  PRIMARY KEY (`env_var_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;

--
-- Dumping data for table `common_EnvVars`
--

INSERT INTO `common_EnvVars` VALUES(0, NULL, 0, NULL);
-- --------------------------------------------------------

--
-- Table structure for table `common_Files`
--

DROP TABLE IF EXISTS `common_Files`;
CREATE TABLE `common_Files` (
  `file_id` int(100) NOT NULL AUTO_INCREMENT,
  `file_type_id` int(100) NOT NULL,
  `file_name` varchar(1000) DEFAULT NULL,
  `upload_timestamp` int(100) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`file_id`),
  KEY `file_type_id` (`file_type_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=93 ;

--
-- Dumping data for table `common_Files`
--

INSERT INTO `common_Files` VALUES(0, 0, NULL, NULL, 0);
-- --------------------------------------------------------

--
-- Table structure for table `common_FileTypes`
--

DROP TABLE IF EXISTS `common_FileTypes`;
CREATE TABLE `common_FileTypes` (
  `file_type_id` int(100) NOT NULL AUTO_INCREMENT,
  `title` varchar(1000) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  `directory` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`file_type_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=20 ;

--
-- Dumping data for table `common_FileTypes`
--

INSERT INTO `common_FileTypes` VALUES(0, NULL, 0, 'images');
-- --------------------------------------------------------

--
-- Table structure for table `common_Permissions`
--

DROP TABLE IF EXISTS `common_Permissions`;
CREATE TABLE `common_Permissions` (
  `permission_id` int(100) NOT NULL AUTO_INCREMENT,
  `alias` varchar(1000) DEFAULT NULL,
  `title` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`permission_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `common_Permissions`
--

INSERT INTO `common_Permissions` VALUES(0, NULL, NULL);
INSERT INTO `common_Permissions` VALUES(2, 'ADM', 'Administrator');

-- --------------------------------------------------------

--
-- Table structure for table `common_Sections`
--

DROP TABLE IF EXISTS `common_Sections`;
CREATE TABLE `common_Sections` (
  `section_id` int(100) NOT NULL AUTO_INCREMENT,
  `title` varchar(1000) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`section_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;

--
-- Dumping data for table `common_Sections`
--

INSERT INTO `common_Sections` VALUES(0, NULL, 0);
INSERT INTO `common_Sections` VALUES(2, 'Main', 1);
-- --------------------------------------------------------

--
-- Table structure for table `common_Sessions`
--

DROP TABLE IF EXISTS `common_Sessions`;
CREATE TABLE `common_Sessions` (
  `session_id` varchar(32) NOT NULL,
  `authentication_id` int(100) NOT NULL,
  `start_timestamp` int(100) DEFAULT NULL,
  `end_timestamp` int(100) DEFAULT NULL,
  PRIMARY KEY (`session_id`),
  KEY `authentication_id` (`authentication_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `common_Sessions`
--

-- --------------------------------------------------------

--
-- Table structure for table `common_States`
--

DROP TABLE IF EXISTS `common_States`;
CREATE TABLE `common_States` (
  `state_id` int(100) NOT NULL AUTO_INCREMENT,
  `abbrv` varchar(2) DEFAULT NULL,
  `full_name` varchar(1000) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`state_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=52 ;

--
-- Dumping data for table `common_States`
--

INSERT INTO `common_States` VALUES(0, NULL, NULL, 0);
INSERT INTO `common_States` VALUES(2, 'AL', 'Alabama', 1);
INSERT INTO `common_States` VALUES(3, 'AK', 'Alaska', 1);
INSERT INTO `common_States` VALUES(4, 'AZ', 'Arizona', 1);
INSERT INTO `common_States` VALUES(5, 'AR', 'Arkansas', 1);
INSERT INTO `common_States` VALUES(6, 'CA', 'California', 1);
INSERT INTO `common_States` VALUES(7, 'CO', 'Colorado', 1);
INSERT INTO `common_States` VALUES(8, 'CT', 'Connecticut', 1);
INSERT INTO `common_States` VALUES(9, 'DE', 'Delaware', 1);
INSERT INTO `common_States` VALUES(10, 'FL', 'Florida', 1);
INSERT INTO `common_States` VALUES(11, 'GA', 'Georgia', 1);
INSERT INTO `common_States` VALUES(12, 'HI', 'Hawaii', 1);
INSERT INTO `common_States` VALUES(13, 'ID', 'Idaho', 1);
INSERT INTO `common_States` VALUES(14, 'IL', 'Illinois', 1);
INSERT INTO `common_States` VALUES(15, 'IN', 'Indiana', 1);
INSERT INTO `common_States` VALUES(16, 'IA', 'Iowa', 1);
INSERT INTO `common_States` VALUES(17, 'KS', 'Kansas', 1);
INSERT INTO `common_States` VALUES(18, 'KY', 'Kentucky', 1);
INSERT INTO `common_States` VALUES(19, 'LA', 'Louisiana', 1);
INSERT INTO `common_States` VALUES(20, 'ME', 'Maine', 1);
INSERT INTO `common_States` VALUES(21, 'MD', 'Maryland', 1);
INSERT INTO `common_States` VALUES(22, 'MA', 'Massachusetts', 1);
INSERT INTO `common_States` VALUES(23, 'MI', 'Michigan', 1);
INSERT INTO `common_States` VALUES(24, 'MN', 'Minnesota', 1);
INSERT INTO `common_States` VALUES(25, 'MS', 'Mississippi', 1);
INSERT INTO `common_States` VALUES(26, 'MO', 'Missouri', 1);
INSERT INTO `common_States` VALUES(27, 'MT', 'Montana', 1);
INSERT INTO `common_States` VALUES(28, 'NE', 'Nebraska', 1);
INSERT INTO `common_States` VALUES(29, 'NV', 'Nevada', 1);
INSERT INTO `common_States` VALUES(30, 'NH', 'New Hampshire', 1);
INSERT INTO `common_States` VALUES(31, 'NJ', 'New Jersey', 1);
INSERT INTO `common_States` VALUES(32, 'NM', 'New Mexico', 1);
INSERT INTO `common_States` VALUES(33, 'NY', 'New York', 1);
INSERT INTO `common_States` VALUES(34, 'NC', 'North Carolina', 1);
INSERT INTO `common_States` VALUES(35, 'ND', 'North Dakota', 1);
INSERT INTO `common_States` VALUES(36, 'OH', 'Ohio', 1);
INSERT INTO `common_States` VALUES(37, 'OK', 'Oklahoma', 1);
INSERT INTO `common_States` VALUES(38, 'OR', 'Oregon', 1);
INSERT INTO `common_States` VALUES(39, 'PA', 'Pennsylvania', 1);
INSERT INTO `common_States` VALUES(40, 'RI', 'Rhode Island', 1);
INSERT INTO `common_States` VALUES(41, 'SC', 'South Carolina', 1);
INSERT INTO `common_States` VALUES(42, 'SD', 'South Dakota', 1);
INSERT INTO `common_States` VALUES(43, 'TN', 'Tennessee', 1);
INSERT INTO `common_States` VALUES(44, 'TX', 'Texas', 1);
INSERT INTO `common_States` VALUES(45, 'UT', 'Utah', 1);
INSERT INTO `common_States` VALUES(46, 'VT', 'Vermont', 1);
INSERT INTO `common_States` VALUES(47, 'VA', 'Virginia', 1);
INSERT INTO `common_States` VALUES(48, 'WA', 'Washington', 1);
INSERT INTO `common_States` VALUES(49, 'WV', 'West Virginia', 1);
INSERT INTO `common_States` VALUES(50, 'WI', 'Wisconsin', 1);
INSERT INTO `common_States` VALUES(51, 'WY', 'Wyoming', 1);

-- --------------------------------------------------------

--
-- Table structure for table `common_Views`
--

DROP TABLE IF EXISTS `common_Views`;
CREATE TABLE `common_Views` (
  `view_id` int(100) NOT NULL AUTO_INCREMENT,
  `controller_name` varchar(1000) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  `requires_auth` tinyint(1) DEFAULT '0',
  `show_in_nav` tinyint(1) DEFAULT '1',
  `alias` varchar(1000) DEFAULT NULL,
  `nav_priority` int(11) DEFAULT NULL,
  `nav_image_id` int(11) DEFAULT NULL,
  `parent_view_id` int(100) NOT NULL DEFAULT '0',
  `external_link` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`view_id`),
  KEY `nav_image_id` (`nav_image_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=21 ;

--
-- Dumping data for table `common_Views`
--

INSERT INTO `common_Views` VALUES(0, NULL, 0, 0, 1, NULL, NULL, NULL, 0);
INSERT INTO `common_Views` VALUES(1, 'Index', 1, 0, 1, 'Home', 1, 0, 0);
INSERT INTO `common_Views` VALUES(2, 'Contact', 1, 0, 1, 'Contact', 5, 0, 0);
INSERT INTO `common_Views` VALUES(11, 'Admin', 1, 1, 0, 'Administration', 0, 0, 0);
