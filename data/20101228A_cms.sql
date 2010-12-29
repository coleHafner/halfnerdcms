-- phpMyAdmin SQL Dump
-- version 3.2.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 29, 2010 at 02:39 AM
-- Server version: 5.1.44
-- PHP Version: 5.3.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `cms`
--

-- --------------------------------------------------------

--
-- Table structure for table `common_Articles`
--

DROP TABLE IF EXISTS `common_Articles`;
CREATE TABLE `common_Articles` (
  `article_id` int(100) NOT NULL AUTO_INCREMENT,
  `title` varchar(1000) DEFAULT NULL,
  `body` varchar(5000) DEFAULT NULL,
  `post_timestamp` int(100) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  `user_id` int(100) NOT NULL,
  `section_id` int(100) NOT NULL,
  `view_id` int(100) NOT NULL,
  `priority` int(100) DEFAULT NULL,
  `tag_string` varchar(5000) DEFAULT NULL,
  PRIMARY KEY (`article_id`),
  KEY `authentication_id` (`user_id`),
  KEY `section_id` (`section_id`),
  KEY `view_id` (`view_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=76 ;

--
-- Dumping data for table `common_Articles`
--

INSERT INTO `common_Articles` VALUES(0, NULL, NULL, NULL, 0, 0, 0, 0, NULL, NULL);
INSERT INTO `common_Articles` VALUES(65, 'Love', 'Hi my love! \nYou are sleeping on the couch right now . . . at 8 pm on a Saturday night, and I am SUPER bored!!! Tuna has her head on my lap, which is keeping me nice and warm. I wish you were up so I could play a game with you! I cannot remember the last time Mall Madness was brought out, can you? ; ) Anyway, this is a cool site! You are so smart! And one day it is going to pay off big!! \n\nI love you with all my heart!\n\n', 1292689141, 1, 3, 2, 1, 2, NULL);
INSERT INTO `common_Articles` VALUES(75, 'Newest Article', 'Article Body.', 1293567853, 1, 2, 2, 1, 4, '');
INSERT INTO `common_Articles` VALUES(64, 'OMG, Totally The Best Day Ever. Lolz.', 'So, like, OMG, me and Judy went to the mall today and we saw the most perfect pair of shoes .... AND THEY WERE ON SALE!!!!!!! Can you believe it?!?!!!! Only $220, I totally charged it. \n\nCredit cards are the totally the best. It\\''s like free money!\n\nOkay. That\\''s all for now \n\n<3 <3 <3 ;-)', 1292577829, 1, 3, 2, 1, 5, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `common_ArticleToFile`
--

DROP TABLE IF EXISTS `common_ArticleToFile`;
CREATE TABLE `common_ArticleToFile` (
  `article_to_file_id` int(100) NOT NULL AUTO_INCREMENT,
  `article_id` int(100) NOT NULL,
  `file_id` int(100) NOT NULL,
  `priority` int(100) DEFAULT NULL,
  PRIMARY KEY (`article_to_file_id`),
  KEY `article_id` (`article_id`),
  KEY `file_id` (`file_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `common_ArticleToFile`
--


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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=110 ;

--
-- Dumping data for table `common_Files`
--

INSERT INTO `common_Files` VALUES(0, 0, NULL, NULL, 0);
INSERT INTO `common_Files` VALUES(109, 21, 'default.jpg', 2010, 1);

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=22 ;

--
-- Dumping data for table `common_FileTypes`
--

INSERT INTO `common_FileTypes` VALUES(0, NULL, 0, NULL);
INSERT INTO `common_FileTypes` VALUES(20, 'Site Image', 1, '/images');
INSERT INTO `common_FileTypes` VALUES(21, 'User Image', 1, '/images/user_images');

-- --------------------------------------------------------

--
-- Table structure for table `common_Permissions`
--

DROP TABLE IF EXISTS `common_Permissions`;
CREATE TABLE `common_Permissions` (
  `permission_id` int(100) NOT NULL AUTO_INCREMENT,
  `alias` varchar(1000) DEFAULT NULL,
  `title` varchar(1000) DEFAULT NULL,
  `summary` varchar(1000) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`permission_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `common_Permissions`
--

INSERT INTO `common_Permissions` VALUES(0, NULL, NULL, NULL, 1);
INSERT INTO `common_Permissions` VALUES(5, 'SPR', 'Super Administrator', 'Super Admin. This permission grants user access to everything.', 1);
INSERT INTO `common_Permissions` VALUES(6, 'TST', 'Test Permission', 'This is a test permission.', 1);
INSERT INTO `common_Permissions` VALUES(7, 'ART', 'Article R/W', 'Users with this permission can read and write articles.', 1);

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=27 ;

--
-- Dumping data for table `common_Sections`
--

INSERT INTO `common_Sections` VALUES(0, NULL, 0);
INSERT INTO `common_Sections` VALUES(2, 'MainMod', 1);
INSERT INTO `common_Sections` VALUES(26, 'Newest Section', 1);
INSERT INTO `common_Sections` VALUES(25, 'NewTitles', 0);
INSERT INTO `common_Sections` VALUES(24, 'New', 1);
INSERT INTO `common_Sections` VALUES(23, 'New Section', 1);
INSERT INTO `common_Sections` VALUES(22, 'Terciary', 0);

-- --------------------------------------------------------

--
-- Table structure for table `common_Sessions`
--

DROP TABLE IF EXISTS `common_Sessions`;
CREATE TABLE `common_Sessions` (
  `session_id` varchar(32) NOT NULL,
  `start_timestamp` int(100) DEFAULT NULL,
  `end_timestamp` int(100) DEFAULT NULL,
  `browser` varchar(1000) DEFAULT NULL,
  `ip` varchar(1000) DEFAULT NULL,
  `user_id` int(100) NOT NULL,
  PRIMARY KEY (`session_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `common_Sessions`
--

INSERT INTO `common_Sessions` VALUES('0a28cb6683f2fd21e087c942d4887e0c', 1293619015, NULL, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1', 10);
INSERT INTO `common_Sessions` VALUES('9b26bc981f103cfca47974d70a1229d9', 1293618803, 1293619003, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1', 2);
INSERT INTO `common_Sessions` VALUES('0d660d407f5fa3e2caaab28aed8af0e2', 1293617710, 1293618794, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1', 16);
INSERT INTO `common_Sessions` VALUES('c8ea8bc9811c1e9fedc1a035fe1b12dd', 1293615303, 1293617698, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1', 2);
INSERT INTO `common_Sessions` VALUES('5582013509b6f8069600513626501ca6', 1293615159, 1293615294, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1', 10);
INSERT INTO `common_Sessions` VALUES('edbc468895fc03cbf7234132cb75e53a', 1293613557, 1293615150, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1', 2);
INSERT INTO `common_Sessions` VALUES('e25b522f26cae3be0a75a7d97b886ccb', 1293613534, 1293613549, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1', 5);
INSERT INTO `common_Sessions` VALUES('3fa8f5be29129fb69d34d92940cf4f4f', 1293613449, 1293613452, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1', 2);
INSERT INTO `common_Sessions` VALUES('e0911c7cd003e01841756fd5ee76d7c8', 1293613422, 1293613441, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1', 5);
INSERT INTO `common_Sessions` VALUES('bca3a07b06d04cce05c5768c4692c8e0', 1293613344, 1293613383, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1', 5);
INSERT INTO `common_Sessions` VALUES('9ff6957cff819275f40b863baee2f36d', 1293572418, 1293613335, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1', 2);
INSERT INTO `common_Sessions` VALUES('4b8cb67403254310401e4f778a2b7501', 1293565943, NULL, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1', 2);
INSERT INTO `common_Sessions` VALUES('5b9d96a4c35159f2e93355798b008f79', 1293519266, NULL, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1', 2);

-- --------------------------------------------------------

--
-- Table structure for table `common_Users`
--

DROP TABLE IF EXISTS `common_Users`;
CREATE TABLE `common_Users` (
  `user_id` int(100) NOT NULL AUTO_INCREMENT,
  `thumb_id` int(100) DEFAULT '0',
  `user_type_id` int(100) DEFAULT '0',
  `username` varchar(1000) NOT NULL,
  `email` varchar(1000) NOT NULL,
  `password` varchar(1000) NOT NULL,
  `first_name` varchar(1000) DEFAULT NULL,
  `last_name` varchar(1000) DEFAULT NULL,
  `bio` varchar(5000) DEFAULT NULL,
  `use_gravatar` tinyint(1) DEFAULT '0',
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`user_id`),
  KEY `thumb_id` (`thumb_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=17 ;

--
-- Dumping data for table `common_Users`
--

INSERT INTO `common_Users` VALUES(0, 0, 0, '', '', '', NULL, NULL, NULL, 0, 0);
INSERT INTO `common_Users` VALUES(2, 0, 2, '_admin_', 'colehafner@gmail.com', '139861389ee6e65255e78d52dbb6ae6769a96358756c81825c09702f08e9794396a11b810f88d1e7e411b99e4cddea25eb88c02c8142342a8310c4e832d34b25818a004c', 'cole', 'hafner', 'Cole is the creator and site administrator for this site', 1, 1);
INSERT INTO `common_Users` VALUES(12, 0, 3, '_cole4', 'cole4@spawnordie.com', '9703209832e6763a7a2a534113b7ab0b7eb8866bc84017c6eea295bc0774825475e768dfbfa08ae1bdcbd2cbff6295eade6c9c233a23815d624e9cfe4af3129fcab0cf31', NULL, NULL, NULL, 0, 1);
INSERT INTO `common_Users` VALUES(10, 0, 3, '_cole2', 'cole2@spawnordie.com', '691070218d66bae14c819e9cf27c5357190993bb0c1d5ff6244fce93cbfe837ed7074ea8c91a25cab6642c7b39350620780996b825fe5478c6f40a7471072072a627f5ba', NULL, NULL, NULL, 0, 1);
INSERT INTO `common_Users` VALUES(11, 0, 3, '_cole3', 'cole3@spawnordie.com', '52413116bc93108a37f3fe43b4f84f4c6a345f9a72ad62afaee0ffc09a03a77d2dbab2a5768ef8280b8c3e3673d109f1af382be94069ab0ee86e8a1a056abf60b5f3edbf', NULL, NULL, NULL, 0, 1);
INSERT INTO `common_Users` VALUES(13, 0, 3, '_cole5', 'cole5@spawnordie.com', '14586906e9636b7214936f6009b0b94320507f8e8a50cbaf2ebcf8f0132e038c0a7f99079760260299845a867276c1c29e182999e12c896c7a34acbb45e9a6ed999f52b5', NULL, NULL, NULL, 0, 1);
INSERT INTO `common_Users` VALUES(14, 0, 3, '_cole6', 'cole6@spawnordie.com', '78391903a50fd2065570a87eb64103744fd81aac691ef8c1b4850f7e9f649d13a7fbc1e7cd2c3a0021ebe51218f52d9a2e2c6b78df0ee933132e2cda70950b815439e3c4', NULL, NULL, NULL, 0, 1);
INSERT INTO `common_Users` VALUES(15, 0, 3, '_cole7', 'cole7@spawnordie.com', '69578540ab23013fbb23807b2ec32fdcf45c57eacb6b7cfd17b8e9063181156b24d4c29b8c5d7c14a59f362414d3ec3f1ca82d32df05176a85237847ed519f36dd35fa69', NULL, NULL, NULL, 0, 1);
INSERT INTO `common_Users` VALUES(16, 0, 3, '_cole8', 'cole8@spawnordie.com', '34484793cc7f84c2b0b7f9a20d2f5574a7646c09c66e94f1892c82e77c4e37c472402bbc564a429d06b3721c4fb09aa4125517370e1a34e10827dc10b8be0c1d3caf2593', NULL, NULL, NULL, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `common_UserToPermission`
--

DROP TABLE IF EXISTS `common_UserToPermission`;
CREATE TABLE `common_UserToPermission` (
  `user_id` int(100) NOT NULL,
  `permission_id` int(100) NOT NULL,
  PRIMARY KEY (`user_id`,`permission_id`),
  KEY `permission_id` (`permission_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `common_UserToPermission`
--

INSERT INTO `common_UserToPermission` VALUES(2, 5);
INSERT INTO `common_UserToPermission` VALUES(16, 5);
INSERT INTO `common_UserToPermission` VALUES(16, 6);
INSERT INTO `common_UserToPermission` VALUES(16, 7);

-- --------------------------------------------------------

--
-- Table structure for table `common_UserTypes`
--

DROP TABLE IF EXISTS `common_UserTypes`;
CREATE TABLE `common_UserTypes` (
  `user_type_id` int(100) NOT NULL AUTO_INCREMENT,
  `title` varchar(1000) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`user_type_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

--
-- Dumping data for table `common_UserTypes`
--

INSERT INTO `common_UserTypes` VALUES(1, NULL, 0);
INSERT INTO `common_UserTypes` VALUES(2, 'Super Administrator', 1);
INSERT INTO `common_UserTypes` VALUES(3, 'Regular User', 1);
INSERT INTO `common_UserTypes` VALUES(4, 'Other User Type', 0);
INSERT INTO `common_UserTypes` VALUES(5, 'New User Type Test', 0);
INSERT INTO `common_UserTypes` VALUES(6, 'Super User', 0);
INSERT INTO `common_UserTypes` VALUES(7, 'Tester', 0);
INSERT INTO `common_UserTypes` VALUES(8, 'RefreshTest', 0);
INSERT INTO `common_UserTypes` VALUES(9, 'Test Add IV', 0);
INSERT INTO `common_UserTypes` VALUES(10, 'Test Type XX', 0);
INSERT INTO `common_UserTypes` VALUES(11, 'Watcher', 1);

-- --------------------------------------------------------

--
-- Table structure for table `common_Values`
--

DROP TABLE IF EXISTS `common_Values`;
CREATE TABLE `common_Values` (
  `value_id` int(100) NOT NULL AUTO_INCREMENT,
  `title` varchar(1000) DEFAULT NULL,
  `value` varchar(5000) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`value_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `common_Values`
--

INSERT INTO `common_Values` VALUES(0, NULL, NULL, 0);

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=43 ;

--
-- Dumping data for table `common_Views`
--

INSERT INTO `common_Views` VALUES(1, 'Index', 1, 1, 1, 'Home', 1, 0, 0, NULL);
INSERT INTO `common_Views` VALUES(11, 'Admin', 1, 1, 1, 'Administration', 2, 0, 0, NULL);
INSERT INTO `common_Views` VALUES(0, NULL, 0, 0, 0, NULL, 0, NULL, 0, NULL);
