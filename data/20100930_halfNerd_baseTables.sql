-- phpMyAdmin SQL Dump
-- version 2.11.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 30, 2010 at 11:08 PM
-- Server version: 5.0.75
-- PHP Version: 5.2.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `sbc`
--

-- --------------------------------------------------------

--
-- Table structure for table `common_Articles`
--

DROP TABLE IF EXISTS `common_Articles`;
CREATE TABLE IF NOT EXISTS `common_Articles` (
  `article_id` int(100) NOT NULL auto_increment,
  `title` varchar(1000) default NULL,
  `body` varchar(1000) default NULL,
  `post_timestamp` int(100) default NULL,
  `active` tinyint(1) default '1',
  `authentication_id` int(100) NOT NULL,
  PRIMARY KEY  (`article_id`),
  KEY `authentication_id` (`authentication_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=17 ;

--
-- Dumping data for table `common_Articles`
--

INSERT INTO `common_Articles` (`article_id`, `title`, `body`, `post_timestamp`, `active`, `authentication_id`) VALUES
(10, 'NewArticle', 'NewArticleTestEdit yup yup yup yup.', 1285180051, 1, 0),
(11, 'Best Day Ever', 'This is a new news article. The other day we went on an awesome ride in the mountains. No one got hurt, so it was a good day. Horray!\n\nIt was a great day!', 1285269806, 1, 0),
(13, 'New News Article', 'paragraph1\nparagraph2\nparagraph3', 1285866771, 1, 3),
(14, 'Newest News', 'p1\np2\np3', 1285866835, 1, 3),
(15, 'Newer News Article', 'paragraphy1\nparagraphy2\nparagraphy3', 1285867307, 1, 3),
(16, 'Test News Article 1000', 'Whaaaaaat?\n\nReally?\n\n1000? No way!', 1285867349, 1, 3),
(2, 'Welcome to our new website!', 'This is simpleBikeCo. We like to make bikes that are strong and simple. Enjoy the site!!!!\nOh yeah, we specialize in custom bikes. Please, no tandem requests. I don\\''t do those.', 1279477800, 1, 3),
(3, 'Check out our products...', 'We have a wide selection of products from dirt jumpers to road cruisers, to BMX. We got it all. And they\\''re not bad, okay, okay, they\\''re actually pretty effing spectacularrrrr! Ha, I\\''m a pirate! And they\\''re not bad, okay, okay, they\\''re actually pretty effing spectacularrrrr! Ha, I\\''m a pirate!\n\nhttp://www.facebook.com\n\nAnd they\\''re not bad, okay, okay, they\\''re actually pretty effing spectacularrrrr! Ha, I\\''m a pirate! And they\\''re not bad, okay, okay, they\\''re actually pretty effing spectacularrrrr! Ha, I\\''m a pirate!And they\\''re not bad, okay, okay, they\\''re actually pretty effing spectacularrrrr! Ha, I\\''m a pirate!', 1280339444, 1, 3),
(4, 'Welcome to the Administration Section', 'This is home article\nThis is second paragraph of home article', 1284592000, 1, 3);

-- --------------------------------------------------------

--
-- Table structure for table `common_ArticleToSection`
--

DROP TABLE IF EXISTS `common_ArticleToSection`;
CREATE TABLE IF NOT EXISTS `common_ArticleToSection` (
  `article_to_view_id` int(100) NOT NULL,
  `section_id` int(100) NOT NULL,
  PRIMARY KEY  (`article_to_view_id`,`section_id`),
  KEY `section_id` (`section_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `common_ArticleToSection`
--

INSERT INTO `common_ArticleToSection` (`article_to_view_id`, `section_id`) VALUES
(0, 2),
(2, 2),
(5, 2),
(6, 2),
(10, 3),
(11, 3),
(13, 3),
(14, 3),
(15, 3),
(16, 3);

-- --------------------------------------------------------

--
-- Table structure for table `common_ArticleToView`
--

DROP TABLE IF EXISTS `common_ArticleToView`;
CREATE TABLE IF NOT EXISTS `common_ArticleToView` (
  `article_to_view_id` int(100) NOT NULL auto_increment,
  `article_id` int(100) NOT NULL,
  `view_id` int(100) NOT NULL,
  PRIMARY KEY  (`article_to_view_id`),
  KEY `article_id` (`article_id`),
  KEY `view_id` (`view_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=17 ;

--
-- Dumping data for table `common_ArticleToView`
--

INSERT INTO `common_ArticleToView` (`article_to_view_id`, `article_id`, `view_id`) VALUES
(0, 0, 0),
(2, 2, 2),
(6, 4, 5),
(5, 3, 3),
(10, 10, 2),
(11, 11, 2),
(13, 13, 2),
(14, 14, 2),
(15, 15, 2),
(16, 16, 2);

-- --------------------------------------------------------

--
-- Table structure for table `common_Authentication`
--

DROP TABLE IF EXISTS `common_Authentication`;
CREATE TABLE IF NOT EXISTS `common_Authentication` (
  `authentication_id` int(100) NOT NULL auto_increment,
  `username` varchar(1000) default NULL,
  `password` varchar(10000) default NULL,
  `active` tinyint(1) default '1',
  PRIMARY KEY  (`authentication_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `common_Authentication`
--

INSERT INTO `common_Authentication` (`authentication_id`, `username`, `password`, `active`) VALUES
(0, NULL, NULL, 1),
(2, 'oscarsbc@gmail.com', '1345Sbc$', 1),
(3, 'admin', 'admin', 1);

-- --------------------------------------------------------

--
-- Table structure for table `common_Captcha`
--

DROP TABLE IF EXISTS `common_Captcha`;
CREATE TABLE IF NOT EXISTS `common_Captcha` (
  `captcha_id` int(100) NOT NULL auto_increment,
  `file_id` int(100) NOT NULL,
  `captcha_string` varchar(1000) default NULL,
  `active` tinyint(1) default '1',
  PRIMARY KEY  (`captcha_id`),
  KEY `file_id` (`file_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `common_Captcha`
--

INSERT INTO `common_Captcha` (`captcha_id`, `file_id`, `captcha_string`, `active`) VALUES
(0, 0, NULL, 0),
(2, 14, 'vavaws', 1),
(3, 15, 'pehuyar', 1),
(4, 16, 'sagihen', 1),
(5, 17, 'fatawir', 1),
(6, 18, 'xum1nov', 1);

-- --------------------------------------------------------

--
-- Table structure for table `common_EnvVars`
--

DROP TABLE IF EXISTS `common_EnvVars`;
CREATE TABLE IF NOT EXISTS `common_EnvVars` (
  `env_var_id` int(100) NOT NULL auto_increment,
  `title` varchar(1000) default NULL,
  `active` tinyint(1) default '1',
  `content` varchar(10000) default NULL,
  PRIMARY KEY  (`env_var_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `common_EnvVars`
--

INSERT INTO `common_EnvVars` (`env_var_id`, `title`, `active`, `content`) VALUES
(0, NULL, 1, NULL),
(2, 'live_mail_to', 1, 'oscarsbc@gmail.com'),
(3, 'dev_mail_to', 1, 'oscarsbc@gmail.com'),
(4, 'local_mail_to', 1, 'colehafner@gmail.com'),
(5, 'alert_status', 1, '1');

-- --------------------------------------------------------

--
-- Table structure for table `common_Files`
--

DROP TABLE IF EXISTS `common_Files`;
CREATE TABLE IF NOT EXISTS `common_Files` (
  `file_id` int(100) NOT NULL auto_increment,
  `file_type_id` int(100) NOT NULL,
  `relative_path` varchar(1000) default NULL,
  `file_name` varchar(1000) default NULL,
  `upload_timestamp` int(100) default NULL,
  `active` tinyint(1) default '1',
  PRIMARY KEY  (`file_id`),
  KEY `file_type_id` (`file_type_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=50 ;

--
-- Dumping data for table `common_Files`
--

INSERT INTO `common_Files` (`file_id`, `file_type_id`, `relative_path`, `file_name`, `upload_timestamp`, `active`) VALUES
(0, 0, NULL, NULL, NULL, 0),
(2, 2, 'images/products', 'bike1_thumb.jpg', 1279471403, 1),
(3, 2, 'images/products', 'bike1_full.jpg', 1279471403, 1),
(4, 2, 'images/products', 'bike2_thumb.jpg', 1279471403, 1),
(5, 2, 'images/products', 'bike2_full.jpg', 1279471403, 1),
(6, 2, 'images/products', 'bike3_thumb.jpg', 1279471403, 1),
(7, 2, 'images/products', 'bike3_full.jpg', 1279471403, 1),
(8, 2, 'images/products', 'bike4_thumb.jpg', 1279471403, 1),
(9, 2, 'images/products', 'bike4_full.jpg', 1279471403, 1),
(10, 3, 'images/prodcat', 'dirt_jumper.gif', 1279471403, 1),
(11, 3, 'images/prodcat', 'road_bike.gif', 1279471403, 1),
(12, 3, 'images/prodcat', 'bmx.gif', 1279471403, 1),
(13, 3, 'images/prodcat', 'custom_bike.gif', 1279471404, 1),
(14, 8, 'images/captcha', 'captcha1.png', 1280338646, 1),
(15, 8, 'images/captcha', 'captcha2.png', 1280338646, 1),
(16, 8, 'images/captcha', 'captcha3.png', 1280338646, 1),
(17, 8, 'images/captcha', 'captcha4.png', 1280338646, 1),
(18, 8, 'images/captcha', 'captcha5.png', 1280338646, 1),
(19, 8, 'images/captcha', 'captcha5.jpeg', 1284568306, 1),
(20, 15, 'images', 'nav_about.png', 1284568306, 1),
(21, 15, 'images', 'nav_bikes.png', 1284568306, 1),
(22, 15, 'images', 'nav_contact.png', 1284568306, 1),
(30, 16, 'images/slideshow', 'img7.jpg', 1285318695, 1),
(24, 16, 'images/slideshow', 'img1.jpg', 1285318684, 1),
(25, 16, 'images/slideshow', 'img2.jpg', 1285318685, 1),
(26, 16, 'images/slideshow', 'img3.jpg', 1285318685, 1),
(27, 16, 'images/slideshow', 'img4.jpg', 1285318685, 1),
(28, 16, 'images/slideshow', 'img5.jpg', 1285318685, 1),
(29, 16, 'images/slideshow', 'img6.jpg', 1285318685, 1),
(31, 16, 'images/slideshow', 'image1_from_interface.png', 1285773136, 1),
(32, 16, 'images/slideshow', 'IMG_0017.JPG', 1285775201, 1),
(33, 16, 'images/slideshow', 'IMG_0040.JPG', 1285775256, 1),
(34, 16, 'images/slideshow', 'IMG_0083.JPG', 1285775442, 1),
(35, 16, 'images/slideshow', 'IMG_0034.JPG', 1285775521, 1),
(36, 16, 'images/slideshow', 'IMG_0136.JPG', 1285775564, 1),
(37, 16, 'images/slideshow', 'IMG_0001.JPG', 1285775771, 1),
(38, 16, 'images/slideshow', 'IMG_0003.JPG', 1285775946, 1),
(39, 16, 'images/slideshow', 'IMG_0002.JPG', 1285776098, 1),
(40, 16, 'images/slideshow', 'IMG_0019.JPG', 1285776230, 1),
(41, 16, 'images/slideshow', 'IMG_0039.JPG', 1285776327, 1),
(42, 16, 'images/slideshow', 'IMG_0069.JPG', 1285776990, 1),
(43, 16, 'images/slideshow', 'IMG_0005.JPG', 1285777112, 1),
(44, 16, 'images/slideshow', 'IMG_0421.JPG', 1285777112, 1),
(45, 16, 'images/slideshow', 'IMG_0420.JPG', 1285777112, 1),
(46, 16, 'images/slideshow', 'IMG_0418.JPG', 1285777265, 1),
(47, 16, 'images/slideshow', 'IMG_0417.JPG', 1285777265, 1),
(48, 16, 'images/slideshow', 'IMG_0416.JPG', 1285777265, 1),
(49, 16, 'images/slideshow', 'IMG_0415.JPG', 1285777317, 1);

-- --------------------------------------------------------

--
-- Table structure for table `common_FileTypes`
--

DROP TABLE IF EXISTS `common_FileTypes`;
CREATE TABLE IF NOT EXISTS `common_FileTypes` (
  `file_type_id` int(100) NOT NULL auto_increment,
  `title` varchar(1000) default NULL,
  `active` tinyint(1) default '1',
  `directory` varchar(1000) default NULL,
  PRIMARY KEY  (`file_type_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=18 ;

--
-- Dumping data for table `common_FileTypes`
--

INSERT INTO `common_FileTypes` (`file_type_id`, `title`, `active`, `directory`) VALUES
(0, NULL, 0, 'images'),
(2, 'Products', 1, 'images'),
(3, 'Product Categories', 1, 'images/prodCat'),
(8, 'Captcha', 1, 'images/captcha'),
(16, 'Image Slideshow', 1, 'images/slideshow'),
(15, 'Image Navigation', 1, 'images');

-- --------------------------------------------------------

--
-- Table structure for table `common_Sections`
--

DROP TABLE IF EXISTS `common_Sections`;
CREATE TABLE IF NOT EXISTS `common_Sections` (
  `section_id` int(100) NOT NULL auto_increment,
  `title` varchar(1000) default NULL,
  `active` tinyint(1) default '1',
  PRIMARY KEY  (`section_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `common_Sections`
--

INSERT INTO `common_Sections` (`section_id`, `title`, `active`) VALUES
(0, NULL, 0),
(2, 'Main', 1),
(3, 'Happenings', 1);

-- --------------------------------------------------------

--
-- Table structure for table `common_States`
--

DROP TABLE IF EXISTS `common_States`;
CREATE TABLE IF NOT EXISTS `common_States` (
  `state_id` int(100) NOT NULL auto_increment,
  `abbrv` varchar(2) default NULL,
  `full_name` varchar(1000) default NULL,
  `active` tinyint(1) default '1',
  PRIMARY KEY  (`state_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=52 ;

--
-- Dumping data for table `common_States`
--

INSERT INTO `common_States` (`state_id`, `abbrv`, `full_name`, `active`) VALUES
(0, NULL, NULL, 0),
(2, 'AL', 'Alabama', 1),
(3, 'AK', 'Alaska', 1),
(4, 'AZ', 'Arizona', 1),
(5, 'AR', 'Arkansas', 1),
(6, 'CA', 'California', 1),
(7, 'CO', 'Colorado', 1),
(8, 'CT', 'Connecticut', 1),
(9, 'DE', 'Delaware', 1),
(10, 'FL', 'Florida', 1),
(11, 'GA', 'Georgia', 1),
(12, 'HI', 'Hawaii', 1),
(13, 'ID', 'Idaho', 1),
(14, 'IL', 'Illinois', 1),
(15, 'IN', 'Indiana', 1),
(16, 'IA', 'Iowa', 1),
(17, 'KS', 'Kansas', 1),
(18, 'KY', 'Kentucky', 1),
(19, 'LA', 'Louisiana', 1),
(20, 'ME', 'Maine', 1),
(21, 'MD', 'Maryland', 1),
(22, 'MA', 'Massachusetts', 1),
(23, 'MI', 'Michigan', 1),
(24, 'MN', 'Minnesota', 1),
(25, 'MS', 'Mississippi', 1),
(26, 'MO', 'Missouri', 1),
(27, 'MT', 'Montana', 1),
(28, 'NE', 'Nebraska', 1),
(29, 'NV', 'Nevada', 1),
(30, 'NH', 'New Hampshire', 1),
(31, 'NJ', 'New Jersey', 1),
(32, 'NM', 'New Mexico', 1),
(33, 'NY', 'New York', 1),
(34, 'NC', 'North Carolina', 1),
(35, 'ND', 'North Dakota', 1),
(36, 'OH', 'Ohio', 1),
(37, 'OK', 'Oklahoma', 1),
(38, 'OR', 'Oregon', 1),
(39, 'PA', 'Pennsylvania', 1),
(40, 'RI', 'Rhode Island', 1),
(41, 'SC', 'South Carolina', 1),
(42, 'SD', 'South Dakota', 1),
(43, 'TN', 'Tennessee', 1),
(44, 'TX', 'Texas', 1),
(45, 'UT', 'Utah', 1),
(46, 'VT', 'Vermont', 1),
(47, 'VA', 'Virginia', 1),
(48, 'WA', 'Washington', 1),
(49, 'WV', 'West Virginia', 1),
(50, 'WI', 'Wisconsin', 1),
(51, 'WY', 'Wyoming', 1);

-- --------------------------------------------------------

--
-- Table structure for table `common_Views`
--

DROP TABLE IF EXISTS `common_Views`;
CREATE TABLE IF NOT EXISTS `common_Views` (
  `view_id` int(100) NOT NULL auto_increment,
  `controller_name` varchar(1000) default NULL,
  `active` tinyint(1) default '1',
  `requires_auth` tinyint(1) default '0',
  `show_in_nav` tinyint(1) default '1',
  `alias` varchar(1000) default NULL,
  `nav_priority` int(11) default NULL,
  `nav_image_id` int(11) default NULL,
  PRIMARY KEY  (`view_id`),
  KEY `nav_image_id` (`nav_image_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `common_Views`
--

INSERT INTO `common_Views` (`view_id`, `controller_name`, `active`, `requires_auth`, `show_in_nav`, `alias`, `nav_priority`, `nav_image_id`) VALUES
(0, NULL, 0, 0, 1, NULL, NULL, NULL),
(2, 'Index', 1, 0, 1, 'About', 1, 20),
(3, 'Products', 1, 0, 1, 'Bikes', 2, 21),
(4, 'Contact', 1, 0, 1, 'Contact', 3, 22),
(5, 'Admin', 1, 1, 0, 'Administration', NULL, NULL);
