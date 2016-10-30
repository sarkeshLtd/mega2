-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Oct 30, 2016 at 11:55 PM
-- Server version: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `mega`
--

-- --------------------------------------------------------

--
-- Table structure for table `translations`
--

CREATE TABLE IF NOT EXISTS `translations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `source` text NOT NULL,
  `translated` text NOT NULL,
  `lang_code` varchar(10) NOT NULL,
  `plugin` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `translations`
--

INSERT INTO `translations` (`id`, `source`, `translated`, `lang_code`, `plugin`) VALUES
(1, 'hello %s !', 'سلام %s !', 'fa_IR', 0),
(3, 'User Profile', 'پروفایل شما', 'fa_IR', 0),
(4, 'First page', 'برگه اول', 'fa_IR', 0),
(5, 'Welcome to your site please change home page from basic settings in administrator area.', 'به وب سایت خود خوش آمدید.میتوانید با مراجعه به بخش مدیریت صفحه اولیه وب سایت خود را تغییر دهید.', 'fa_IR', 0),
(8, 'Administrator', 'مدیریت', 'fa_IR', 0);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
