-- phpMyAdmin SQL Dump
-- version 3.2.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 21, 2010 at 12:04 AM
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

CREATE TABLE `common_Articles` (
  `article_id` int(100) NOT NULL AUTO_INCREMENT,
  `title` varchar(1000) DEFAULT NULL,
  `body` varchar(5000) DEFAULT NULL,
  `post_timestamp` int(100) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  `authentication_id` int(100) NOT NULL,
  `section_id` int(100) NOT NULL,
  `view_id` int(100) NOT NULL,
  `priority` int(100) DEFAULT NULL,
  PRIMARY KEY (`article_id`),
  KEY `authentication_id` (`authentication_id`),
  KEY `section_id` (`section_id`),
  KEY `view_id` (`view_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=75 ;

--
-- Dumping data for table `common_Articles`
--

INSERT INTO `common_Articles` VALUES(0, NULL, NULL, NULL, 0, 0, 0, 0, NULL);
INSERT INTO `common_Articles` VALUES(60, 'Love', 'Hi my love! \nYou are sleeping on the couch right now . . . at 8 pm on a Saturday night, and I am SUPER bored!!! Tuna has her head on my lap, which is keeping me nice and warm. I wish you were up so I could play a game with you! I cannot remember the last time Mall Madness was brought out, can you? ; ) Anyway, this is a cool site! You are so smart! And one day it is going to pay off big!! \n\nI love you with all my heart!\n\n', 1292578219, 0, 3, 2, 1, 3);
INSERT INTO `common_Articles` VALUES(59, 'This is the newest article.', 'Blah blah blah blah bligger---------adsf', 1292577456, 0, 3, 2, 11, 2);
INSERT INTO `common_Articles` VALUES(61, 'This is the newest article.', 'Blah blah blah blah bligger---------adsf', 1292578241, 0, 3, 2, 11, 6);
INSERT INTO `common_Articles` VALUES(65, 'Love', 'Hi my love! \nYou are sleeping on the couch right now . . . at 8 pm on a Saturday night, and I am SUPER bored!!! Tuna has her head on my lap, which is keeping me nice and warm. I wish you were up so I could play a game with you! I cannot remember the last time Mall Madness was brought out, can you? ; ) Anyway, this is a cool site! You are so smart! And one day it is going to pay off big!! \n\nI love you with all my heart!\n\n', 1292689141, 1, 3, 2, 1, 2);
INSERT INTO `common_Articles` VALUES(62, 'This is the newest article.', 'Blah blah blah blah bligger---------adsf', 1292609951, 0, 3, 2, 11, 5);
INSERT INTO `common_Articles` VALUES(63, 'Love', 'Hi my love! \nYou are sleeping on the couch right now . . . at 8 pm on a Saturday night, and I am SUPER bored!!! Tuna has her head on my lap, which is keeping me nice and warm. I wish you were up so I could play a game with you! I cannot remember the last time Mall Madness was brought out, can you? ; ) Anyway, this is a cool site! You are so smart! And one day it is going to pay off big!! \n\nI love you with all my heart!\n\n', 1292612982, 0, 3, 2, 1, 5);
INSERT INTO `common_Articles` VALUES(64, 'OMG, Totally The Best Day Ever. Lolz.', 'So, like, OMG, me and Judy went to the mall today and we saw the most perfect pair of shoes .... AND THEY WERE ON SALE!!!!!!! Can you believe it?!?!!!! Only $220, I totally charged it. \n\nCredit cards are the totally the best. It\\''s like free money!\n\nOkay. That\\''s all for now \n\n<3 <3 <3 ;-)', 1292577829, 1, 3, 2, 1, 5);
INSERT INTO `common_Articles` VALUES(66, 'This is a new article with the new form.', 'This is a new Article Body', 1292696194, 0, 3, 2, 1, 5);
INSERT INTO `common_Articles` VALUES(67, 'Article Title', 'modded.', 1292697000, 0, 3, 2, 1, 7);
INSERT INTO `common_Articles` VALUES(68, 'Newest article ever.ever.ever.', 'newest body ever. super duper modded. asfdlkj. asdfl;aksjdfalsdf.', 1292698099, 0, 3, 2, 1, 8);
INSERT INTO `common_Articles` VALUES(69, 'This is a new article.', 'This is the mother loving body.', 1292701756, 0, 3, 2, 1, 8);
INSERT INTO `common_Articles` VALUES(70, 'asdflkjadf', 'asdf;lkajsdf;la sdfj als;fdkj as;lfdk jasfd;l kjasfd;l ajksfd -----------------------', 1292701908, 0, 3, 2, 1, 11);
INSERT INTO `common_Articles` VALUES(71, 'This new title.', 'newest body...', 1292702068, 0, 3, 2, 1, 10);
INSERT INTO `common_Articles` VALUES(72, 'This is a new title.', 'The documenetary says that dogs are really smart.', 1292752641, 0, 3, 2, 1, 12);
INSERT INTO `common_Articles` VALUES(73, 'New Title', 'New Article Body', 1292753776, 1, 3, 2, 1, 12);

-- --------------------------------------------------------

--
-- Table structure for table `common_ArticleToFile`
--

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

INSERT INTO `common_ArticleToFile` VALUES(1, 52, 93, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `common_Authentication`
--

CREATE TABLE `common_Authentication` (
  `authentication_id` int(100) NOT NULL AUTO_INCREMENT,
  `username` varchar(1000) DEFAULT NULL,
  `password` varchar(10000) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  `email` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`authentication_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `common_Authentication`
--

INSERT INTO `common_Authentication` VALUES(0, NULL, NULL, 0, NULL);
INSERT INTO `common_Authentication` VALUES(3, 'admin', '78208247673bac2eb1fca9de76ab521f4e3c3214683cedad7c28301802d13205887b8b5de9b949c8929c8f6ae443e3dc5cb9ddfb0ffce5f584c4f5fbae56666b4dff6871', 1, 'colehafner@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `common_AuthenticationToPermission`
--

CREATE TABLE `common_AuthenticationToPermission` (
  `authentication_id` int(100) NOT NULL,
  `permission_id` int(100) NOT NULL,
  PRIMARY KEY (`authentication_id`,`permission_id`),
  KEY `permission_id` (`permission_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `common_AuthenticationToPermission`
--

INSERT INTO `common_AuthenticationToPermission` VALUES(3, 2);

-- --------------------------------------------------------

--
-- Table structure for table `common_Captcha`
--

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

INSERT INTO `common_Contacts` VALUES(0, NULL, NULL, NULL, 0, 74, 0, 0, 8);
INSERT INTO `common_Contacts` VALUES(1, 'admin', 'admin', 'Site Administrator', 1, 0, 0, 3, 8);

-- --------------------------------------------------------

--
-- Table structure for table `common_ContactTypes`
--

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

CREATE TABLE `common_Files` (
  `file_id` int(100) NOT NULL AUTO_INCREMENT,
  `file_type_id` int(100) NOT NULL,
  `file_name` varchar(1000) DEFAULT NULL,
  `upload_timestamp` int(100) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`file_id`),
  KEY `file_type_id` (`file_type_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=109 ;

--
-- Dumping data for table `common_Files`
--

INSERT INTO `common_Files` VALUES(0, 0, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `common_FileTypes`
--

CREATE TABLE `common_FileTypes` (
  `file_type_id` int(100) NOT NULL AUTO_INCREMENT,
  `title` varchar(1000) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  `directory` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`file_type_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=21 ;

--
-- Dumping data for table `common_FileTypes`
--

INSERT INTO `common_FileTypes` VALUES(0, NULL, 0, NULL);
INSERT INTO `common_FileTypes` VALUES(20, 'image', 1, 'images');

-- --------------------------------------------------------

--
-- Table structure for table `common_Permissions`
--

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

CREATE TABLE `common_Sections` (
  `section_id` int(100) NOT NULL AUTO_INCREMENT,
  `title` varchar(1000) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`section_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=25 ;

--
-- Dumping data for table `common_Sections`
--

INSERT INTO `common_Sections` VALUES(0, NULL, 0);
INSERT INTO `common_Sections` VALUES(2, 'Main', 1);
INSERT INTO `common_Sections` VALUES(24, 'New', 1);
INSERT INTO `common_Sections` VALUES(23, 'New Section', 1);
INSERT INTO `common_Sections` VALUES(22, 'Terciary', 1);

-- --------------------------------------------------------

--
-- Table structure for table `common_Sessions`
--

CREATE TABLE `common_Sessions` (
  `session_id` varchar(32) NOT NULL,
  `authentication_id` int(100) NOT NULL,
  `start_timestamp` int(100) DEFAULT NULL,
  `end_timestamp` int(100) DEFAULT NULL,
  `browser` varchar(1000) DEFAULT NULL,
  `ip` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`session_id`),
  KEY `authentication_id` (`authentication_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `common_Sessions`
--

INSERT INTO `common_Sessions` VALUES('070a378b4add2f1b85d4af5e2cd44ac7', 3, 1292314215, 1292314776, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.13 (KHTML, like Gecko) Chrome/9.0.597.16 Safari/534.13', '127.0.0.1');
INSERT INTO `common_Sessions` VALUES('9cccabb43b857f80887fc12db4b3f66c', 3, 1292313698, 1292314208, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.13 (KHTML, like Gecko) Chrome/9.0.597.16 Safari/534.13', '127.0.0.1');
INSERT INTO `common_Sessions` VALUES('4a5ec1933cce0148bc83b6a9bf84fa8c', 3, 1292315149, 1292315709, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.13 (KHTML, like Gecko) Chrome/9.0.597.16 Safari/534.13', '127.0.0.1');
INSERT INTO `common_Sessions` VALUES('8aa364c36006f543aaf9bc4826c0be8e', 3, 1292315865, 1292318471, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.13 (KHTML, like Gecko) Chrome/9.0.597.16 Safari/534.13', '127.0.0.1');
INSERT INTO `common_Sessions` VALUES('b6fb2c70d0acf7b4fdfeb7d614ea39da', 3, 1292318502, 1292319108, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.13 (KHTML, like Gecko) Chrome/9.0.597.16 Safari/534.13', '127.0.0.1');
INSERT INTO `common_Sessions` VALUES('2bf10f123f1b31eb6e1897abcbb3a178', 3, 1292320328, 1292320555, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.13 (KHTML, like Gecko) Chrome/9.0.597.16 Safari/534.13', '127.0.0.1');
INSERT INTO `common_Sessions` VALUES('1c82e4073dd9a04556b201a3299f219d', 3, 1292320563, 1292320622, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.13 (KHTML, like Gecko) Chrome/9.0.597.16 Safari/534.13', '127.0.0.1');
INSERT INTO `common_Sessions` VALUES('a003ce8fc28a777e5dd20ec5cee6c5fc', 3, 1292320635, 1292320837, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.13 (KHTML, like Gecko) Chrome/9.0.597.16 Safari/534.13', '127.0.0.1');
INSERT INTO `common_Sessions` VALUES('21138b161329e024bac5fa1363fc61c6', 3, 1292391749, NULL, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.13 (KHTML, like Gecko) Chrome/9.0.597.19 Safari/534.13', '127.0.0.1');
INSERT INTO `common_Sessions` VALUES('d508a4b6011f22df8ee3316504bd4530', 3, 1292444665, NULL, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.13 (KHTML, like Gecko) Chrome/9.0.597.19 Safari/534.13', '127.0.0.1');
INSERT INTO `common_Sessions` VALUES('83435ae956af1fd2641fb9540c5038b9', 3, 1292474635, NULL, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.13 (KHTML, like Gecko) Chrome/9.0.597.19 Safari/534.13', '127.0.0.1');
INSERT INTO `common_Sessions` VALUES('1c0bb32cfee2315d98518f60fc467897', 3, 1292481296, NULL, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.13 (KHTML, like Gecko) Chrome/9.0.597.19 Safari/534.13', '127.0.0.1');
INSERT INTO `common_Sessions` VALUES('a7d5c1c7473ef0dc44c8bf5b83f44b39', 3, 1292547143, NULL, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.13 (KHTML, like Gecko) Chrome/9.0.597.19 Safari/534.13', '127.0.0.1');
INSERT INTO `common_Sessions` VALUES('2fb9184a129f8e2e0f80f33e3412f61b', 3, 1292547148, 1292547308, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.13 (KHTML, like Gecko) Chrome/9.0.597.19 Safari/534.13', '127.0.0.1');
INSERT INTO `common_Sessions` VALUES('7cb934eca457a4ee47f5d0a42ac1764d', 3, 1292547318, 1292547375, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.13 (KHTML, like Gecko) Chrome/9.0.597.19 Safari/534.13', '127.0.0.1');
INSERT INTO `common_Sessions` VALUES('c09c59de7ea4a665fd6cd887dce14b7c', 3, 1292547469, NULL, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.13 (KHTML, like Gecko) Chrome/9.0.597.19 Safari/534.13', '127.0.0.1');
INSERT INTO `common_Sessions` VALUES('ac4fcd15d71d9976e5976c8e2ba5e2c2', 3, 1292562362, 1292562500, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.13 (KHTML, like Gecko) Chrome/9.0.597.19 Safari/534.13', '127.0.0.1');
INSERT INTO `common_Sessions` VALUES('092ad583af76fe58763d30f778a45317', 3, 1292562538, 1292562749, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.13 (KHTML, like Gecko) Chrome/9.0.597.19 Safari/534.13', '127.0.0.1');
INSERT INTO `common_Sessions` VALUES('4707a6f234a2527f974fa243582a20d0', 3, 1292562760, 1292562889, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.13 (KHTML, like Gecko) Chrome/9.0.597.19 Safari/534.13', '127.0.0.1');
INSERT INTO `common_Sessions` VALUES('3c8c36c819a889c7322d34cb7dee1293', 3, 1292562898, 1292562909, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.13 (KHTML, like Gecko) Chrome/9.0.597.19 Safari/534.13', '127.0.0.1');
INSERT INTO `common_Sessions` VALUES('592d529a60ee1532448024063cf5443c', 3, 1292563027, 1292563036, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.13 (KHTML, like Gecko) Chrome/9.0.597.19 Safari/534.13', '127.0.0.1');
INSERT INTO `common_Sessions` VALUES('896167186f648ad0338833d40147471f', 3, 1292563061, 1292563073, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.13 (KHTML, like Gecko) Chrome/9.0.597.19 Safari/534.13', '127.0.0.1');
INSERT INTO `common_Sessions` VALUES('e4dcb64878b9d2dcf23213cc62f0f4e8', 3, 1292563582, 1292566104, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.13 (KHTML, like Gecko) Chrome/9.0.597.19 Safari/534.13', '127.0.0.1');
INSERT INTO `common_Sessions` VALUES('1b99a4dd31e5dab27e3a36159e02f34c', 3, 1292566532, 1292571581, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.13 (KHTML, like Gecko) Chrome/9.0.597.19 Safari/534.13', '127.0.0.1');
INSERT INTO `common_Sessions` VALUES('b07f437d5a1c3d29864baa9a1cdc25fe', 3, 1292571651, 1292571660, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.13 (KHTML, like Gecko) Chrome/9.0.597.19 Safari/534.13', '127.0.0.1');
INSERT INTO `common_Sessions` VALUES('fe94a28276e745d3f2705f03fceeb839', 3, 1292571668, 1292572376, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.13 (KHTML, like Gecko) Chrome/9.0.597.19 Safari/534.13', '127.0.0.1');
INSERT INTO `common_Sessions` VALUES('63532ee5bd3b1fe7474af7989c2990d8', 3, 1292572387, 1292574130, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.13 (KHTML, like Gecko) Chrome/9.0.597.19 Safari/534.13', '127.0.0.1');
INSERT INTO `common_Sessions` VALUES('dff54926636a169f1d134856c08f4cb7', 3, 1292574151, 1292579275, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.13 (KHTML, like Gecko) Chrome/9.0.597.19 Safari/534.13', '127.0.0.1');
INSERT INTO `common_Sessions` VALUES('d82b530a1bf9a2a30ae35cdc9030e64f', 3, 1292579356, NULL, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.13 (KHTML, like Gecko) Chrome/9.0.597.19 Safari/534.13', '127.0.0.1');
INSERT INTO `common_Sessions` VALUES('cd9e5db437117b1311ec665d406249bf', 3, 1292609909, NULL, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.1 Safari/534.15', '127.0.0.1');
INSERT INTO `common_Sessions` VALUES('ac3e5ec9f719c88fc2d50ec03f638dfb', 3, 1292652985, NULL, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.1 Safari/534.15', '127.0.0.1');
INSERT INTO `common_Sessions` VALUES('38ed34e4ecddee702c17c53253f1c837', 3, 1292721644, 1292752964, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.1 Safari/534.15', '127.0.0.1');
INSERT INTO `common_Sessions` VALUES('1577fff3f6ae69a25a119aad6ec878c2', 3, 1292753733, NULL, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.1 Safari/534.15', '127.0.0.1');
INSERT INTO `common_Sessions` VALUES('59a693dc0e526621cf4b8ccecbb7bd69', 3, 1292782117, 1292814275, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.1 Safari/534.15', '127.0.0.1');
INSERT INTO `common_Sessions` VALUES('ccb9f3ed28909587e318dd0b4b2c83fa', 3, 1292795560, NULL, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.6; en-US; rv:1.9.2.12) Gecko/20101026 Firefox/3.6.12', '127.0.0.1');
INSERT INTO `common_Sessions` VALUES('a5547bf4f50e828a342a605e688a9d0a', 3, 1292810224, NULL, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.6; en-US; rv:1.9.2.12) Gecko/20101026 Firefox/3.6.12', '127.0.0.1');
INSERT INTO `common_Sessions` VALUES('13aa096fb801fb69bdd48d7927cb1af4', 3, 1292814295, 1292814617, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.1 Safari/534.15', '127.0.0.1');
INSERT INTO `common_Sessions` VALUES('813c878e26e39043cf131fd9710ca2b1', 3, 1292814632, 1292820594, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.1 Safari/534.15', '127.0.0.1');
INSERT INTO `common_Sessions` VALUES('fb87d8f90850a9bda4a2a901b0089bdc', 3, 1292820606, 1292820628, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.1 Safari/534.15', '127.0.0.1');
INSERT INTO `common_Sessions` VALUES('ab73951bc7d7d93575a00ec1de9fa120', 3, 1292820636, NULL, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.1 Safari/534.15', '127.0.0.1');
INSERT INTO `common_Sessions` VALUES('76b7247b0f4707c748d14b4723421c1c', 3, 1292821493, NULL, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.6; en-US; rv:1.9.2.12) Gecko/20101026 Firefox/3.6.12', '127.0.0.1');
INSERT INTO `common_Sessions` VALUES('67007e7e8d19d0b3632b8aeb0277a376', 3, 1292860633, NULL, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.1 Safari/534.15', '127.0.0.1');
INSERT INTO `common_Sessions` VALUES('14af8ded53cbbc312ec858caa765abd0', 3, 1292892314, NULL, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1');

-- --------------------------------------------------------

--
-- Table structure for table `common_States`
--

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=41 ;

--
-- Dumping data for table `common_Views`
--

INSERT INTO `common_Views` VALUES(1, 'Index', 1, 0, 1, 'Home', 2, 0, 0, NULL);
INSERT INTO `common_Views` VALUES(11, 'Admin', 1, 1, 0, 'Administration', 4, 0, 0, NULL);
INSERT INTO `common_Views` VALUES(0, NULL, 0, 0, 0, NULL, 0, NULL, 0, NULL);
INSERT INTO `common_Views` VALUES(37, 'Contacts', 1, 0, 1, 'Contact', 3, NULL, 0, NULL);
