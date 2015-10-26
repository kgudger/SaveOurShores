-- phpMyAdmin SQL Dump
-- version 3.5.7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 26, 2015 at 01:22 PM
-- Server version: 5.5.44-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.13

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `SOS`
--

-- --------------------------------------------------------

--
-- Table structure for table `Categories`
--

DROP TABLE IF EXISTS `Categories`;
CREATE TABLE IF NOT EXISTS `Categories` (
  `catid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  PRIMARY KEY (`catid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `Categories`
--

INSERT INTO `Categories` (`catid`, `name`) VALUES
(1, 'COMMON ITEMS'),
(2, 'PLASTIC ITEMS'),
(3, 'STYROFOAM ITEMS'),
(4, 'SMOKING RELATED ITEMS'),
(5, 'PAPER ITEMS'),
(6, 'METAL ITEMS'),
(7, 'BEACH USERS'),
(8, 'MEDICAL / PERSONAL HYGENE'),
(9, 'LARGE ITEMS'),
(10, 'OTHER');

-- --------------------------------------------------------

--
-- Table structure for table `Collector`
--

DROP TABLE IF EXISTS `Collector`;
CREATE TABLE IF NOT EXISTS `Collector` (
  `cid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lat` float NOT NULL,
  `lon` float NOT NULL,
  PRIMARY KEY (`cid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=45 ;

--
-- Dumping data for table `Collector`
--

INSERT INTO `Collector` (`cid`, `name`, `date`, `lat`, `lon`) VALUES
(2, 'GEORGE JETSON', '2015-10-06 04:39:19', 37.0067, -121.97),
(30, 'GEORGE JETSON', '2015-10-07 23:04:46', 37.0177, -121.962),
(32, 'KEITH GUDGER', '2015-10-08 04:33:40', 37.0177, -121.962),
(36, 'RYAN', '2015-10-09 18:28:42', 36.9661, -122.001),
(37, 'KEITH GUDGER', '2015-10-10 10:28:02', 0, 0),
(39, 'KEITH GUDGER', '2015-10-17 21:39:32', 37.0067, -121.97),
(40, 'KEITH GUDGER', '2015-10-19 14:39:56', 0, 0),
(41, 'TEST', '2015-10-21 16:27:35', 36.966, -122.001),
(42, 'TEST', '2015-10-21 16:27:37', 36.966, -122.001),
(43, 'TEST', '2015-10-26 16:19:32', 36.9659, -122.036),
(44, 'TEST', '2015-10-26 16:19:32', 36.9659, -122.036);

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

DROP TABLE IF EXISTS `items`;
CREATE TABLE IF NOT EXISTS `items` (
  `iid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `item` text NOT NULL,
  `aname` text NOT NULL,
  `recycle` tinyint(1) NOT NULL,
  `weight` float NOT NULL,
  `category` int(11) DEFAULT NULL,
  PRIMARY KEY (`iid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=52 ;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`iid`, `item`, `aname`, `recycle`, `weight`, `category`) VALUES
(1, 'Cigarette Butts', 'butts-in', 0, 0.00198416, 1),
(2, 'Plastic Pieces', 'plast-in', 0, 0.00209439, 1),
(3, 'Food Wrappers', 'foods-in', 0, 0.01325, 1),
(4, 'Paper Pieces', 'paper-in', 0, 0.00112508, 1),
(5, 'Glass Pieces', 'glass-in', 0, 0.00397149, 1),
(6, 'Metal Caps / Pulls', 'mcaps-in', 0, 0.00513, 1),
(7, 'Plastic Caps / Rings', 'pcaps-in', 0, 0.00425492, 2),
(8, 'Glass Bottles', 'gbots-in', 1, 0.418878, 1),
(9, 'Plastic Cups, plates etc.', 'pcups-in', 0, 0.017637, 2),
(10, 'Metal Cans', 'mcans-in', 1, 0.03, 6),
(11, 'Styrofoam pieces', 'spics-in', 0, 0.00299608, 3),
(12, 'Plastic bottles', 'pbots-in', 1, 0.0286601, 2),
(13, 'Styrofoam peanuts or packing materials', 'spaks-in', 0, 0.000649375, 3),
(14, 'Cardboard, newspapers, magazines', 'pcrds-in', 0, 0.625, 5),
(15, 'Paper food containers, cups, plates', 'pcons-in', 0, 0.7, 5),
(16, 'Aluminum foil', 'mfoil-in', 0, 0.0975, 6),
(17, 'Plastic straws or stirrers', 'pstws-in', 0, 0.00091886, 2),
(18, 'Cigarette box or wrappers', 'cboxs-in', 0, 0.0135347, 4),
(19, 'Fireworks', 'firew-in', 0, 0, 7),
(20, 'Plastic bags, grocery or shopping', 'pbags-in', 0, 0.0121254, 2),
(21, 'Nails', 'mnail-in', 0, 0.0198416, 6),
(22, 'Plastic bags, ziplock or snack', 'pzips-in', 0, 0.00330693, 2),
(23, 'Clothes or towels', 'cloth-in', 0, 0, 7),
(24, 'Plastic bags, trash', 'ptras-in', 0, 0.386271, 2),
(25, 'Pallets or wood', 'pwood-in', 0, 48, 7),
(26, 'Styrofoam cups, plates or bowls', 'scups-in', 0, 0.141978, 3),
(27, 'Balloons or ribbon', 'pbals-in', 0, 0.0015532, 2),
(28, 'Paper bags', 'pabgs-in', 0, 0.14, 5),
(29, 'Plastic motor oil bottles', 'pmobs-in', 0, 0.132277, 2),
(30, 'Beach chairs, toys, umbrellas', 'chair-in', 0, 0, 7),
(31, 'Styrofoam food containers', 'scons-in', 0, 0.0136, 3),
(32, 'Disposable cigarette lighters', 'cltes-in', 0, 0.0206132, 4),
(33, 'Shoes', 'shoes-in', 0, 0.70625, 7),
(34, 'Plastic fishing line, nets, lures, floats', 'pfish-in', 0, 0, 2),
(35, 'Styrofoam buoys or floats', 'sflos-in', 0, 4.2, 3),
(36, 'Batteries', 'mbats-in', 0, 0.050625, 6),
(37, 'Diapers', 'diaps-in', 0, 0.507063, 8),
(38, 'Bandaids or bandages', 'bands-in', 0, 0.001, 8),
(39, 'Feminine products', 'femps-in', 0, 0.00655556, 8),
(40, 'Condoms', 'conds-in', 0, 0.00856, 8),
(41, 'Metal fishing hooks or lures', 'mhook-in', 0, 0.0220462, 6),
(42, 'Crab pots', 'mcrab-in', 0, 0, 6),
(43, 'Syringes or needles', 'needl-in', 0, 0.0095, 8),
(44, 'Shopping carts', 'scart-in', 0, 35, 9),
(45, 'Tires', 'tires-in', 0, 10, 9),
(46, 'Appliances', 'appls-in', 0, 0, 9),
(47, 'Bikes or bike parts', 'bikes-in', 0, 0, 9),
(48, 'Car parts', 'cprts-in', 0, 0, 9),
(49, 'Car batteries', 'cbats-in', 1, 40, 9),
(50, 'Plastic six-pack rings', 'prngs-in', 0, 0.00685638, 2),
(51, 'Other, e.g. Rope', 'other-in', 0, 0, 10);

-- --------------------------------------------------------

--
-- Table structure for table `tally`
--

DROP TABLE IF EXISTS `tally`;
CREATE TABLE IF NOT EXISTS `tally` (
  `cid` int(10) unsigned NOT NULL,
  `iid` int(11) NOT NULL,
  `number` int(11) NOT NULL,
  KEY `cid` (`cid`,`iid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tally`
--

INSERT INTO `tally` (`cid`, `iid`, `number`) VALUES
(2, 1, 10),
(30, 2, 7),
(32, 6, 1),
(32, 12, 1),
(32, 50, 1),
(32, 35, 1),
(32, 28, 1),
(32, 16, 1),
(32, 19, 1),
(32, 40, 1),
(32, 47, 1),
(36, 2, 6),
(36, 3, 8),
(39, 1, 1),
(39, 2, 2),
(39, 3, 3),
(39, 4, 5),
(39, 5, 6),
(39, 6, 7),
(39, 8, 8),
(40, 1, 1),
(40, 2, 1),
(40, 3, 1),
(40, 4, 1),
(40, 5, 1),
(40, 6, 1),
(40, 8, 1),
(40, 7, 1),
(40, 9, 1),
(40, 12, 1),
(40, 17, 1),
(40, 20, 1),
(40, 22, 1),
(40, 24, 1),
(40, 27, 1),
(40, 29, 1),
(40, 34, 1),
(40, 50, 1),
(40, 11, 1),
(40, 13, 1),
(40, 26, 1),
(40, 31, 1),
(40, 35, 1),
(40, 18, 1),
(40, 32, 1),
(40, 14, 1),
(40, 15, 1),
(40, 28, 1),
(40, 10, 1),
(40, 16, 1),
(40, 21, 1),
(40, 36, 1),
(40, 41, 1),
(40, 42, 1),
(40, 19, 1),
(40, 23, 1),
(40, 25, 1),
(40, 30, 1),
(40, 33, 1),
(40, 37, 1),
(40, 38, 1),
(40, 39, 1),
(40, 40, 1),
(40, 43, 1),
(40, 44, 1),
(40, 45, 1),
(40, 46, 1),
(40, 47, 1),
(40, 48, 1),
(40, 49, 1),
(40, 51, 1),
(41, 1, 1),
(41, 2, 1),
(41, 3, 1),
(41, 9, 2),
(41, 26, 4),
(41, 16, 3),
(41, 48, 3),
(41, 51, 2),
(43, 2, 2),
(43, 3, 1),
(43, 4, 1),
(43, 5, 1),
(43, 9, 3),
(43, 50, 1),
(43, 35, 3),
(43, 19, 1),
(43, 23, 1),
(43, 30, 1),
(43, 37, 1),
(43, 39, 1),
(43, 40, 1);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
