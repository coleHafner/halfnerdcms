-- phpMyAdmin SQL Dump
-- version 3.2.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 27, 2010 at 09:28 PM
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
  `authentication_id` int(100) NOT NULL,
  `section_id` int(100) NOT NULL,
  `view_id` int(100) NOT NULL,
  `priority` int(100) DEFAULT NULL,
  `tag_string` varchar(5000) DEFAULT NULL,
  PRIMARY KEY (`article_id`),
  KEY `authentication_id` (`authentication_id`),
  KEY `section_id` (`section_id`),
  KEY `view_id` (`view_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=75 ;

--
-- Dumping data for table `common_Articles`
--

INSERT INTO `common_Articles` VALUES(0, NULL, NULL, NULL, 0, 0, 0, 0, NULL, NULL);
INSERT INTO `common_Articles` VALUES(60, 'Love', 'Hi my love! \nYou are sleeping on the couch right now . . . at 8 pm on a Saturday night, and I am SUPER bored!!! Tuna has her head on my lap, which is keeping me nice and warm. I wish you were up so I could play a game with you! I cannot remember the last time Mall Madness was brought out, can you? ; ) Anyway, this is a cool site! You are so smart! And one day it is going to pay off big!! \n\nI love you with all my heart!\n\n', 1292578219, 0, 3, 2, 1, 3, NULL);
INSERT INTO `common_Articles` VALUES(59, 'This is the newest article.', 'Blah blah blah blah bligger---------adsf', 1292577456, 0, 3, 2, 11, 2, NULL);
INSERT INTO `common_Articles` VALUES(61, 'This is the newest article.', 'Blah blah blah blah bligger---------adsf', 1292578241, 0, 3, 2, 11, 6, NULL);
INSERT INTO `common_Articles` VALUES(65, 'Love', 'Hi my love! \nYou are sleeping on the couch right now . . . at 8 pm on a Saturday night, and I am SUPER bored!!! Tuna has her head on my lap, which is keeping me nice and warm. I wish you were up so I could play a game with you! I cannot remember the last time Mall Madness was brought out, can you? ; ) Anyway, this is a cool site! You are so smart! And one day it is going to pay off big!! \n\nI love you with all my heart!\n\n', 1292689141, 1, 3, 2, 1, 2, NULL);
INSERT INTO `common_Articles` VALUES(62, 'This is the newest article.', 'Blah blah blah blah bligger---------adsf', 1292609951, 0, 3, 2, 11, 5, NULL);
INSERT INTO `common_Articles` VALUES(63, 'Love', 'Hi my love! \nYou are sleeping on the couch right now . . . at 8 pm on a Saturday night, and I am SUPER bored!!! Tuna has her head on my lap, which is keeping me nice and warm. I wish you were up so I could play a game with you! I cannot remember the last time Mall Madness was brought out, can you? ; ) Anyway, this is a cool site! You are so smart! And one day it is going to pay off big!! \n\nI love you with all my heart!\n\n', 1292612982, 0, 3, 2, 1, 5, NULL);
INSERT INTO `common_Articles` VALUES(64, 'OMG, Totally The Best Day Ever. Lolz.', 'So, like, OMG, me and Judy went to the mall today and we saw the most perfect pair of shoes .... AND THEY WERE ON SALE!!!!!!! Can you believe it?!?!!!! Only $220, I totally charged it. \n\nCredit cards are the totally the best. It\\''s like free money!\n\nOkay. That\\''s all for now \n\n<3 <3 <3 ;-)', 1292577829, 1, 3, 2, 1, 5, NULL);
INSERT INTO `common_Articles` VALUES(66, 'This is a new article with the new form.', 'This is a new Article Body', 1292696194, 0, 3, 2, 1, 5, NULL);
INSERT INTO `common_Articles` VALUES(67, 'Article Title', 'modded.', 1292697000, 0, 3, 2, 1, 7, NULL);
INSERT INTO `common_Articles` VALUES(68, 'Newest article ever.ever.ever.', 'newest body ever. super duper modded. asfdlkj. asdfl;aksjdfalsdf.', 1292698099, 0, 3, 2, 1, 8, NULL);
INSERT INTO `common_Articles` VALUES(69, 'This is a new article.', 'This is the mother loving body.', 1292701756, 0, 3, 2, 1, 8, NULL);
INSERT INTO `common_Articles` VALUES(70, 'asdflkjadf', 'asdf;lkajsdf;la sdfj als;fdkj as;lfdk jasfd;l kjasfd;l ajksfd -----------------------', 1292701908, 0, 3, 2, 1, 11, NULL);
INSERT INTO `common_Articles` VALUES(71, 'This new title.', 'newest body...', 1292702068, 0, 3, 2, 1, 10, NULL);
INSERT INTO `common_Articles` VALUES(72, 'This is a new title.', 'The documenetary says that dogs are really smart.', 1292752641, 0, 3, 2, 1, 12, NULL);
INSERT INTO `common_Articles` VALUES(73, 'New Title', 'New Article Body', 1292753776, 1, 3, 2, 1, 12, NULL);

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

INSERT INTO `common_ArticleToFile` VALUES(1, 52, 93, NULL);

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=26 ;

--
-- Dumping data for table `common_Sections`
--

INSERT INTO `common_Sections` VALUES(0, NULL, 0);
INSERT INTO `common_Sections` VALUES(2, 'Main', 1);
INSERT INTO `common_Sections` VALUES(25, 'NewTitles', 1);
INSERT INTO `common_Sections` VALUES(24, 'New', 1);
INSERT INTO `common_Sections` VALUES(23, 'New Section', 1);
INSERT INTO `common_Sections` VALUES(22, 'Terciary', 1);

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
INSERT INTO `common_Sessions` VALUES('0bd95698b0cbca691be6d2e51d1895a6', 3, 1292957642, 1292958701, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1');
INSERT INTO `common_Sessions` VALUES('8ccd1062d89921c3df0d86ac5f93ee92', 3, 1292958881, NULL, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1');
INSERT INTO `common_Sessions` VALUES('c5dfc715707b65184a82fa74233983de', 3, 1292969380, NULL, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.6; rv:2.0b7) Gecko/20100101 Firefox/4.0b7', '127.0.0.1');
INSERT INTO `common_Sessions` VALUES('16f3cdc3562987959318bea95b212caa', 3, 1293057006, NULL, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.6; en-US; rv:1.9.2.13) Gecko/20101203 Firefox/3.6.13', '127.0.0.1');
INSERT INTO `common_Sessions` VALUES('7487813eb6aa6314ad2e00d44e8c5889', 3, 1293076153, 1293076298, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1');
INSERT INTO `common_Sessions` VALUES('cc298f95221f9ff4cceceadd6c6a81fa', 3, 1293076965, NULL, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1');
INSERT INTO `common_Sessions` VALUES('d88b6455e818c2d4eb549ab2366cb1da', 3, 1293158432, NULL, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1');
INSERT INTO `common_Sessions` VALUES('9e9cd5216ded1877cf3a72a74764b009', 3, 1293165191, NULL, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1');
INSERT INTO `common_Sessions` VALUES('e721de75b7fd56fef903fc8268d6d5e5', 3, 1293213829, NULL, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1');
INSERT INTO `common_Sessions` VALUES('cbd9f4b17b16644a3921761d61f22039', 3, 1293266622, 1293266667, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1');
INSERT INTO `common_Sessions` VALUES('1caaeb6e74491cc5aaf864cf4c4a5ebf', 3, 1293266695, 1293267873, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1');
INSERT INTO `common_Sessions` VALUES('78245542dc14ced5d73f9224a70e489a', 3, 1293267889, NULL, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1');
INSERT INTO `common_Sessions` VALUES('1438884e0c2d7caacd2b06bfd19a45bc', 3, 1293508527, NULL, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1');

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `common_Users`
--


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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `common_UserTypes`
--


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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `common_Values`
--


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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=42 ;

--
-- Dumping data for table `common_Views`
--

INSERT INTO `common_Views` VALUES(41, NULL, 1, 0, 1, 'Facebook', 3, NULL, 0, 'http://www.facebook.com');
INSERT INTO `common_Views` VALUES(1, 'Index', 1, 1, 1, 'Home', 1, 0, 0, NULL);
INSERT INTO `common_Views` VALUES(11, 'Admin', 1, 1, 0, 'Administration', 0, 0, 0, NULL);
INSERT INTO `common_Views` VALUES(0, NULL, 0, 0, 0, NULL, 0, NULL, 0, NULL);
INSERT INTO `common_Views` VALUES(37, 'Contacts', 1, 0, 1, 'Contact', 2, NULL, 0, NULL);
