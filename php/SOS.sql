-- phpMyAdmin SQL Dump
-- version 3.5.7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 05, 2016 at 12:22 PM
-- Server version: 5.5.46-0ubuntu0.14.04.2
-- PHP Version: 5.5.9-1ubuntu4.14

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `lauren`
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
  `tdate` date NOT NULL,
  PRIMARY KEY (`cid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=93 ;

--
-- Dumping data for table `Collector`
--

INSERT INTO `Collector` (`cid`, `name`, `date`, `lat`, `lon`, `tdate`) VALUES
(2, 'GEORGE JETSON', '2015-10-06 04:39:19', 37.0067, -121.97, '2015-10-05'),
(30, 'GEORGE JETSON', '2015-10-07 23:04:46', 37.0177, -121.962, '2015-10-07'),
(32, 'KEITH GUDGER', '2015-10-08 04:33:40', 37.0177, -121.962, '2015-10-07'),
(36, 'RYAN', '2015-10-09 18:28:42', 36.9661, -122.001, '2015-10-09'),
(37, 'KEITH GUDGER', '2015-10-10 10:28:02', 0, 0, '2015-10-10'),
(39, 'KEITH GUDGER', '2015-10-17 21:39:32', 37.0067, -121.97, '2015-10-17'),
(40, 'KEITH GUDGER', '2015-10-19 14:39:56', 0, 0, '2015-10-19'),
(41, 'TEST', '2015-10-21 16:27:35', 36.966, -122.001, '2015-10-21'),
(42, 'TEST', '2015-10-21 16:27:37', 36.966, -122.001, '2015-10-21'),
(43, 'TEST', '2015-10-26 16:19:32', 36.9659, -122.036, '2015-10-26'),
(44, 'TEST', '2015-10-26 16:19:32', 36.9659, -122.036, '2015-10-26'),
(45, 'KEITH GUDGER', '2015-10-29 04:42:03', 0, 0, '2015-10-28'),
(46, 'KEITH GUDGER', '2015-10-29 04:48:51', 37.0176, -121.962, '2015-10-28'),
(47, 'KEITH GUDGER', '2015-10-29 04:48:58', 37.0176, -121.962, '2015-10-28'),
(48, 'KEITH GUDGER', '2015-10-29 04:48:59', 37.0176, -121.962, '2015-10-28'),
(49, 'KEITH GUDGER', '2015-10-29 04:48:59', 37.0176, -121.962, '2015-10-28'),
(50, 'KEITH GUDGER', '2015-10-29 04:49:00', 37.0176, -121.962, '2015-10-28'),
(51, 'KEITH GUDGER', '2015-10-29 04:49:00', 37.0176, -121.962, '2015-10-28'),
(52, 'KEITH GUDGER', '2015-10-29 04:49:00', 37.0176, -121.962, '2015-10-28'),
(53, 'KEITH GUDGER', '2015-10-29 04:49:00', 37.0176, -121.962, '2015-10-28'),
(54, 'KEITH GUDGER', '2015-10-29 04:49:00', 37.0176, -121.962, '2015-10-28'),
(55, 'KEITH GUDGER', '2015-10-29 04:49:01', 37.0176, -121.962, '2015-10-28'),
(56, 'KEITH GUDGER', '2015-10-29 04:49:01', 37.0176, -121.962, '2015-10-28'),
(57, 'KEITH GUDGER', '2015-10-29 04:49:06', 37.0176, -121.962, '2015-10-28'),
(58, 'KEITH GUDGER', '2015-10-29 04:49:07', 37.0176, -121.962, '2015-10-28'),
(59, 'KEITH GUDGER', '2015-10-29 04:49:07', 37.0176, -121.962, '2015-10-28'),
(60, 'KEITH GUDGER', '2015-10-29 04:49:07', 37.0176, -121.962, '2015-10-28'),
(61, 'KEITH GUDGER', '2015-10-29 04:49:11', 37.0176, -121.962, '2015-10-28'),
(62, 'JOE MAIN', '2015-11-02 00:27:25', 36.9636, -122.024, '2015-11-01'),
(63, 'TEST 2', '2015-11-05 19:45:37', 36.9663, -122.036, '2015-11-05'),
(64, 'TEST 2', '2015-11-05 19:45:48', 36.9663, -122.036, '2015-11-05'),
(68, 'KEITH GUDGER', '2015-11-10 03:47:37', 37.0177, -121.961, '2015-11-09'),
(69, 'TEST', '2015-12-03 21:03:17', 36.9678, -121.987, '2015-12-03'),
(70, 'TEST', '2015-12-03 21:03:20', 36.9678, -121.987, '2015-12-03');

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=59 ;

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
(19, 'Fireworks, small', 'firws-in', 0, 0, 7),
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
(30, 'Beach toys', 'btoys-in', 0, 0, 7),
(31, 'Styrofoam food containers', 'scons-in', 0, 0.0136, 3),
(32, 'Disposable cigarette lighters', 'cltes-in', 0, 0.0206132, 4),
(33, 'Shoes', 'shoes-in', 0, 0.70625, 7),
(34, 'Plastic fishing line, lures, floats (non-commercial)', 'pfish-in', 0, 0, 2),
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
(46, 'Appliances, small / handheld', 'appsm-in', 0, 0, 9),
(47, 'Bike parts, small', 'bikes-in', 0, 0, 9),
(48, 'Car parts, small', 'cprts-in', 0, 0, 9),
(49, 'Car batteries', 'cbats-in', 1, 40, 9),
(50, 'Plastic six-pack rings', 'prngs-in', 0, 0.00685638, 2),
(51, 'Other, small', 'othsm-in', 0, 0, 10),
(52, 'Beach chairs, umbrellas', 'chair-in', 0, 0, 7),
(53, 'Appliances, large', 'applg-in', 0, 0, 9),
(54, 'Bikes / large bike parts', 'bikel-in', 0, 0, 9),
(55, 'Car parts, large', 'cprtl-in', 0, 0, 9),
(56, 'Other, large', 'othlg-in', 0, 0, 10),
(57, 'Plastic fishing line, nets, lures, floats, commercial sized', 'pfisc-in', 0, 0, 2),
(58, 'Fireworks, large', 'firwl-in', 0, 0, 7);

-- --------------------------------------------------------

--
-- Table structure for table `Places`
--

DROP TABLE IF EXISTS `Places`;
CREATE TABLE IF NOT EXISTS `Places` (
  `pid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `lat` float NOT NULL,
  `lon` float NOT NULL,
  PRIMARY KEY (`pid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=45 ;

--
-- Dumping data for table `Places`
--

INSERT INTO `Places` (`pid`, `name`, `lat`, `lon`) VALUES
(1, 'Seabright State Beach', 36.9624, -122.007),
(3, 'Cowell and Main Beach', 36.9621, -122.023),
(4, 'Lighthouse Field State Beach', 36.9534, -122.029),
(5, 'Mitchell''s Cove Beach', 36.953, -122.043),
(6, 'Sunny Cove Beach', 36.9609, -121.992),
(7, 'Pleasure Point Park', 36.9577, -121.971),
(8, 'Capitola City Beach', 36.9716, -121.951),
(9, 'New Brighton Beach State Park', 36.9815, -121.938),
(10, 'Seacliff State Beach', 36.9722, -121.916),
(11, 'Rio Del Mar State Beach', 36.9689, -121.907),
(12, 'Davenport Main Beach', 37.0096, -122.197),
(13, 'Panther State Beach', 36.9914, -122.172),
(16, 'Manresa State Beach', 36.9318, -121.865),
(17, 'Casa Verde Beach/North Del Monte', 36.6027, -121.878),
(18, 'Del Monte Beach at Wharf 2', 36.6015, -121.889),
(19, 'Bonny Doon State Beach', 37.0009, -122.181),
(20, 'Sunny Cove Beach', 36.9609, -121.992),
(21, 'Moran Lake Beach', 36.9567, -121.979),
(22, 'Twin Lakes State Beach', 36.962, -122.001),
(24, 'Carmel City Beach', 36.5451, -121.934),
(25, 'Hidden Beach', 36.5105, -121.944),
(26, 'Scott Creek Beach', 37.0242, -122.233),
(27, 'Natural Bridges State Beach', 36.9527, -122.06),
(29, 'Palm State Beach', 36.8688, -121.821),
(30, 'Blacks Beach', 36.9605, -122.012),
(31, 'Monterey State Beach (North of Best Western)', 36.606, -121.867),
(32, 'SLR at Felker St.', 36.9846, -122.027),
(33, 'SLR at Laurel St. Bridge', 36.9698, -122.023),
(34, 'SLR at Riverside Ave.', 36.9718, -122.022),
(35, 'SLR at Soquel St. Bridge', 36.9736, -122.023),
(36, 'Rail Trail', 36.9663, -122.012),
(37, '4 Mile Beach', 36.9661, -122.123),
(38, 'Beer Can Beach', 36.9549, -121.888),
(39, 'Elkhorn Slough', 36.8058, -121.79),
(40, 'Soquel Creek', 36.9827, -121.957),
(41, 'Sand City Beach', 36.6257, -121.845),
(42, 'SLR at Tannery', 36.9869, -122.028),
(43, 'Waddell Creek State Beach', 37.0935, -122.283),
(44, 'Rodeo Creek', 37.0067, -121.97);

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
(43, 40, 1),
(45, 1, 4),
(45, 3, 3),
(45, 8, 3),
(46, 2, 1),
(61, 51, 2),
(62, 1, 2),
(62, 2, 3),
(62, 3, 3),
(62, 4, 3),
(62, 5, 3),
(62, 6, 2),
(62, 8, 8),
(62, 44, 1),
(62, 45, 1),
(62, 46, 1),
(62, 47, 1),
(62, 48, 1),
(62, 49, 1),
(62, 51, 1),
(63, 4, 4),
(63, 5, 4),
(63, 8, 900),
(63, 7, 98797),
(63, 9, 4),
(63, 17, 3),
(63, 29, 3),
(63, 34, 4),
(63, 13, 5),
(63, 26, 5),
(63, 32, 4),
(63, 14, 3),
(63, 28, 4),
(63, 10, 15),
(63, 21, 2),
(63, 36, 7),
(63, 38, 2),
(68, 1, 1),
(69, 1, 1),
(69, 2, 1),
(69, 3, 1),
(69, 4, 1),
(69, 5, 1),
(69, 9, 1),
(69, 12, 1),
(69, 34, 1),
(69, 50, 3);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
