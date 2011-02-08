-- phpMyAdmin SQL Dump
-- version 3.2.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 16, 2011 at 12:28 AM
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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=79 ;

--
-- Dumping data for table `common_Articles`
--

INSERT INTO `common_Articles` VALUES(0, NULL, NULL, NULL, 0, 0, 0, 0, NULL, NULL);
INSERT INTO `common_Articles` VALUES(65, 'Love', 'Hi my love! \nYou are sleeping on the couch right now . . . at 8 pm on a Saturday night, and I am SUPER bored!!! Tuna has her head on my lap, which is keeping me nice and warm. I wish you were up so I could play a game with you! I cannot remember the last time Mall Madness was brought out, can you? ; ) Anyway, this is a cool site! You are so smart! And one day it is going to pay off big!! \n\nI love you with all my heart!\n\n', 1292689141, 0, 3, 24, 1, 2, '');
INSERT INTO `common_Articles` VALUES(75, 'Newest Article', 'Article Body.', 1293567853, 0, 2, 2, 1, 4, '');
INSERT INTO `common_Articles` VALUES(76, 'NewArticle2000', 'Newest Title.', 1294256967, 0, 2, 29, 1, 1, '');
INSERT INTO `common_Articles` VALUES(77, 'New Post', 'Where have all the posts gone?', 1294433701, 0, 2, 29, 1, 4, '');
INSERT INTO `common_Articles` VALUES(78, 'New Post', 'This is a new post.', 1294586216, 1, 2, 30, 1, 1, '');
INSERT INTO `common_Articles` VALUES(64, 'OMG, Totally The Best Day Ever. Lolz.', 'So, like, OMG, me and Judy went to the mall today and we saw the most perfect pair of shoes .... AND THEY WERE ON SALE!!!!!!! Can you believe it?!?!!!! Only $220, I totally charged it. \n\nCredit cards are the totally the best. It\\''s like free money!\n\nOkay. That\\''s all for now \n\n<3 <3 <3 ;-)', 1292577829, 0, 3, 29, 1, 2, '');

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=121 ;

--
-- Dumping data for table `common_Files`
--

INSERT INTO `common_Files` VALUES(0, 0, NULL, NULL, 0);
INSERT INTO `common_Files` VALUES(109, 21, 'default.jpg', 2010, 1);
INSERT INTO `common_Files` VALUES(113, 21, 'ed15371ee9.png', 1294425517, 1);
INSERT INTO `common_Files` VALUES(112, 21, 'e0bbb46115.png', 1294425509, 1);
INSERT INTO `common_Files` VALUES(114, 21, '94c325b923.png', 1294425942, 1);
INSERT INTO `common_Files` VALUES(115, 21, '0013169e61.png', 1294426013, 1);
INSERT INTO `common_Files` VALUES(116, 21, '056bf98661.png', 1294427030, 1);
INSERT INTO `common_Files` VALUES(117, 21, '871895c29f.jpg', 1294427052, 1);
INSERT INTO `common_Files` VALUES(118, 21, '0ab1b042b1.JPG', 1294427714, 1);
INSERT INTO `common_Files` VALUES(119, 21, '7ef5bb263a.JPG', 1294591652, 1);
INSERT INTO `common_Files` VALUES(120, 21, 'ab1d9446c1.png', 1294920951, 1);

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

--
-- Dumping data for table `common_Permissions`
--

INSERT INTO `common_Permissions` VALUES(0, NULL, NULL, NULL, 1);
INSERT INTO `common_Permissions` VALUES(5, 'SPR', 'Super Administrator', 'Super Admin. This permission grants user access to everything.', 1);
INSERT INTO `common_Permissions` VALUES(11, 'PTD', 'PassTheDutch', 'This is a test permission.', 1);

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=32 ;

--
-- Dumping data for table `common_Sections`
--

INSERT INTO `common_Sections` VALUES(0, NULL, 0);
INSERT INTO `common_Sections` VALUES(2, 'MainMod2', 0);
INSERT INTO `common_Sections` VALUES(26, 'Newest Section', 0);
INSERT INTO `common_Sections` VALUES(25, 'NewTitles', 0);
INSERT INTO `common_Sections` VALUES(24, 'New', 1);
INSERT INTO `common_Sections` VALUES(23, 'New Section', 0);
INSERT INTO `common_Sections` VALUES(22, 'Terciary', 0);
INSERT INTO `common_Sections` VALUES(27, 'New 2000', 0);
INSERT INTO `common_Sections` VALUES(28, 'New2001', 0);
INSERT INTO `common_Sections` VALUES(29, 'NewSection', 1);
INSERT INTO `common_Sections` VALUES(30, 'NewSection1', 1);
INSERT INTO `common_Sections` VALUES(31, 'NewSection2', 1);

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

INSERT INTO `common_Sessions` VALUES('c86c68351260b7ef6f2fe50db543528e', 1294448654, NULL, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_6; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1', 2);
INSERT INTO `common_Sessions` VALUES('34f52d1f954efbb2893cc110811ff5e2', 1294442211, 1294448561, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_6; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1', 2);
INSERT INTO `common_Sessions` VALUES('55925ece6449c5e83c0638ba5f013ca0', 1294422048, 1294442196, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_6; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1', 2);
INSERT INTO `common_Sessions` VALUES('6c72081a63c1a342facbc5cfba3dd547', 1294350958, NULL, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1', 2);
INSERT INTO `common_Sessions` VALUES('ada1b4303f16da672deed1b9a19004ba', 1294350927, 1294350944, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1', 2);
INSERT INTO `common_Sessions` VALUES('dee43f5d1b717a5b56996f1e6d0c7738', 1294350664, 1294350900, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1', 3);
INSERT INTO `common_Sessions` VALUES('fb6cc2ee178faeb457d7eeb3811d6d08', 1294348701, 1294350651, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1', 2);
INSERT INTO `common_Sessions` VALUES('83e34b34fbf12b60e21a2ad64739a7a8', 1294345943, 1294346076, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1', 2);
INSERT INTO `common_Sessions` VALUES('68ecf56cf9642205ba8e1997264e1829', 1294345622, 1294345626, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1', 2);
INSERT INTO `common_Sessions` VALUES('d3a748066bf40e6fddfe99eba39fcfca', 1294302653, NULL, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1', 2);
INSERT INTO `common_Sessions` VALUES('dcbf60d54377cd2801d4a6e155f28baa', 1294296981, NULL, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1', 2);
INSERT INTO `common_Sessions` VALUES('d8801444b1618392c8e29c354eb82e2e', 1294296413, 1294296973, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1', 18);
INSERT INTO `common_Sessions` VALUES('3cb41035d4b189c7c3593dac2f1dc9e7', 1294294682, 1294296405, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1', 2);
INSERT INTO `common_Sessions` VALUES('a89bcaa955fbca91f8683ce2c2892562', 1294294637, 1294294674, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1', 18);
INSERT INTO `common_Sessions` VALUES('146edbb219465cd87225a84d963fca70', 1294294586, 1294294627, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1', 18);
INSERT INTO `common_Sessions` VALUES('cff2731991c1d4ed5011d76c620b2eba', 1294272093, 1294294542, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1', 2);
INSERT INTO `common_Sessions` VALUES('6415c7830b02772e238f5327098a9fc0', 1294267745, 1294272074, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1', 2);
INSERT INTO `common_Sessions` VALUES('a3cc844bbe140e5f4c9fec998899d97b', 1294249356, NULL, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1', 2);
INSERT INTO `common_Sessions` VALUES('07fa43367bce7b6dac77f5c8bc407000', 1294215982, NULL, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1', 2);
INSERT INTO `common_Sessions` VALUES('47283d0b6f4e9c4361477ca4a34f3dd4', 1294190073, NULL, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1', 2);
INSERT INTO `common_Sessions` VALUES('19fd1034bc5361da2fa9e6dec8e418e7', 1294183269, NULL, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1', 2);
INSERT INTO `common_Sessions` VALUES('e767c4f364e8d9346c0ed3c155f1760d', 1294178488, 1294183258, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1', 2);
INSERT INTO `common_Sessions` VALUES('d03f6bd97a9f1b8cce7cbb4bd61f41bb', 1294178467, 1294178471, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1', 2);
INSERT INTO `common_Sessions` VALUES('983556472e1579ee13410686cb4cb8fa', 1294178095, 1294178098, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1', 2);
INSERT INTO `common_Sessions` VALUES('7492231c62b20b6243346294b7ecca8f', 1294175603, NULL, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1', 25);
INSERT INTO `common_Sessions` VALUES('0033dc5dab67f137921bab121491e622', 1294175320, 1294175595, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1', 2);
INSERT INTO `common_Sessions` VALUES('743c241eaefd2ad23657de0ac9cdba97', 1294175103, 1294175314, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1', 25);
INSERT INTO `common_Sessions` VALUES('122ea1a8e1ecadbe7cab1a5f4cde2c26', 1294172744, 1294175095, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1', 2);
INSERT INTO `common_Sessions` VALUES('4aae86291caab536ff87555b7086334f', 1294123560, NULL, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1', 2);
INSERT INTO `common_Sessions` VALUES('23596cf523ba4919abae0b1fa5f9bd87', 1294115762, 1294121722, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1', 18);
INSERT INTO `common_Sessions` VALUES('1b678c4cac34c4bc69be0f5aed55d2f8', 1294115713, 1294115754, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1', 2);
INSERT INTO `common_Sessions` VALUES('d9944f3d1ca0e9ec219819a9ca8ca919', 1294114969, 1294115289, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1', 2);
INSERT INTO `common_Sessions` VALUES('e6c8baadca268f3f459fc5ca1f3142ec', 1294036986, 1294039796, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1', 18);
INSERT INTO `common_Sessions` VALUES('ad72a61d8d5d28c9193e0eff915c982f', 1293746792, 1294036977, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1', 2);
INSERT INTO `common_Sessions` VALUES('c9a0eae515dea6eb9eedb3bf5a2e0c7b', 1293619678, 1293620067, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1', 2);
INSERT INTO `common_Sessions` VALUES('0a28cb6683f2fd21e087c942d4887e0c', 1293619015, 1293619660, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1', 10);
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
INSERT INTO `common_Sessions` VALUES('6953153cca300b83bd5fb3b27dddb222', 1294458032, 1294474864, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_6; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1', 2);
INSERT INTO `common_Sessions` VALUES('deb61b8fa8bdaef63a56c46972f746a9', 1294474874, NULL, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_6; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1', 3);
INSERT INTO `common_Sessions` VALUES('d43c9aca407000c0791d1e2b5cc79f20', 1294512409, NULL, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_6; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1', 2);
INSERT INTO `common_Sessions` VALUES('579d8894dbdcfad5737a8e7669cf0ef0', 1294613955, NULL, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_6; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1', 2);
INSERT INTO `common_Sessions` VALUES('1d339497f26a9c5bfb4e3689bc1716b4', 1294616009, NULL, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_6; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1', 2);
INSERT INTO `common_Sessions` VALUES('f67be62d380f856adeb7657866c0c685', 1294628071, NULL, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_6; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1', 2);
INSERT INTO `common_Sessions` VALUES('15c6b59e86b72fc68c2369bd359ec489', 1294636881, 1294639690, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_6; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15', '127.0.0.1', 2);
INSERT INTO `common_Sessions` VALUES('9e42c3a2c35025af0e033ad57977dbc8', 1294964095, NULL, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_6; en-US) AppleWebKit/534.16 (KHTML, like Gecko) Chrome/10.0.634.0 Safari/534.16', '127.0.0.1', 2);
INSERT INTO `common_Sessions` VALUES('96a01808eff75b74d20680e8a1e5adb6', 1294966322, NULL, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.6; rv:2.0b8) Gecko/20100101 Firefox/4.0b8', '127.0.0.1', 2);
INSERT INTO `common_Sessions` VALUES('9eb90802b95a592d6103b564d818a2ce', 1294980127, 1294989267, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_6; en-US) AppleWebKit/534.16 (KHTML, like Gecko) Chrome/10.0.634.0 Safari/534.16', '127.0.0.1', 2);
INSERT INTO `common_Sessions` VALUES('d2c3e2f172a6d07e92dcb93badbbcf3a', 1294989286, 1294989304, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_6; en-US) AppleWebKit/534.16 (KHTML, like Gecko) Chrome/10.0.634.0 Safari/534.16', '127.0.0.1', 2);
INSERT INTO `common_Sessions` VALUES('5560ba0ab7b8b6badb4152ddf36b9950', 1294989319, NULL, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_6; en-US) AppleWebKit/534.16 (KHTML, like Gecko) Chrome/10.0.634.0 Safari/534.16', '127.0.0.1', 2);
INSERT INTO `common_Sessions` VALUES('14f3cb652c0ab73c36eae9e231f94adf', 1295166107, NULL, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_6; en-US) AppleWebKit/534.16 (KHTML, like Gecko) Chrome/10.0.634.0 Safari/534.16', '127.0.0.1', 2);

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=32 ;

--
-- Dumping data for table `common_Users`
--

INSERT INTO `common_Users` VALUES(0, 0, 0, '', '', '', NULL, NULL, NULL, 0, 0);
INSERT INTO `common_Users` VALUES(2, 120, 3, 'admin', 'colehafner@gmail.com', '86706042732bf6cd498226e02fb9875ff37dfe14b8d97d56387ff699371527fceeaef635c7e7f19bef9521143869d12f969dbeab4d58905fc99c76076513aebd401646a4', 'Cole', 'Hafner', 'Boom it\\''s a bracelet.', 1, 1);
INSERT INTO `common_Users` VALUES(3, 119, 2, 'JayReynolds', 'jay@jay.com', '8738539141b40e439614368424e5a506f749a7f39b38c8a58d02967ea71dde4b19a81bd23a75b0e149174539566cb49c94b6e62a41112891680e5d4fbae2e227a9976e1f', 'Jay', 'Reynolds', 'This is jay mutha fuckin\\'' Reynolds!!!!!!', 0, 1);

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
INSERT INTO `common_UserToPermission` VALUES(3, 5);
INSERT INTO `common_UserToPermission` VALUES(29, 7);
INSERT INTO `common_UserToPermission` VALUES(30, 6);

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

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
INSERT INTO `common_UserTypes` VALUES(11, 'Watcher II', 0);
INSERT INTO `common_UserTypes` VALUES(12, 'Git Contributor', 0);
INSERT INTO `common_UserTypes` VALUES(15, 'NewUserType', 1);
INSERT INTO `common_UserTypes` VALUES(13, 'Sonic Youth', 0);
INSERT INTO `common_UserTypes` VALUES(14, 'NewestUserType', 0);

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=48 ;

--
-- Dumping data for table `common_Views`
--

INSERT INTO `common_Views` VALUES(47, 'Posts', 1, 1, 1, 'Posts', 2, NULL, 0, NULL);
INSERT INTO `common_Views` VALUES(1, 'Index', 1, 1, 1, 'Home', 1, 0, 0, NULL);
INSERT INTO `common_Views` VALUES(11, 'Admin', 1, 1, 1, 'Administration', 4, 0, 0, NULL);
INSERT INTO `common_Views` VALUES(0, NULL, 0, 0, 0, NULL, 0, NULL, 0, NULL);
INSERT INTO `common_Views` VALUES(46, 'Account', 1, 1, 0, 'My Account', 0, NULL, 0, NULL);
INSERT INTO `common_Views` VALUES(44, 'Users', 1, 0, 1, 'Users', 3, NULL, 0, NULL);
