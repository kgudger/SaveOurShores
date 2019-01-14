-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 09, 2019 at 12:16 PM
-- Server version: 5.6.41-84.1
-- PHP Version: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lauren_stewards`
--

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

DROP TABLE IF EXISTS `items`;
CREATE TABLE `items` (
  `iid` int(10) UNSIGNED NOT NULL,
  `item` text NOT NULL,
  `aname` text NOT NULL,
  `recycle` tinyint(1) NOT NULL,
  `weight` float NOT NULL,
  `category` int(11) DEFAULT NULL,
  `used` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`iid`, `item`, `aname`, `recycle`, `weight`, `category`, `used`) VALUES
(1, 'Cigarette Butts', 'butts-in', 0, 0.00198416, 1, 1),
(2, 'Plastic Pieces', 'plast-in', 0, 0.00209439, 1, 1),
(3, 'Plastic Food Wrappers', 'foods-in', 0, 0.01325, 1, 1),
(4, 'Paper Pieces', 'paper-in', 0, 0.00112508, 1, 1),
(5, 'Pieces and Chunks', 'glass-in', 0, 0.00397149, 3, 1),
(6, 'Bottle Caps', 'mcaps-in', 0, 0.00513, 6, 1),
(7, 'Bottle Caps and Rings', 'pcaps-in', 0, 0.00425492, 2, 1),
(8, 'Bottles', 'gbots-in', 1, 0.418878, 3, 1),
(9, 'Plastic To-Go Items', 'pcups-in', 0, 0.017637, 1, 1),
(10, 'Metal Cans', 'mcans-in', 1, 0.03, 6, 0),
(11, 'Polystyrene pieces', 'spics-in', 0, 0.00299608, 2, 0),
(12, 'Bottles', 'pbots-in', 1, 0.0286601, 2, 1),
(13, 'Polystyrence Pieces', 'spaks-in', 0, 0.000649375, 1, 1),
(14, 'Cardboard', 'pcrds-in', 0, 0.625, 5, 1),
(15, 'Food containers, cups, plates, bowls', 'pcons-in', 0, 0.7, 5, 1),
(16, 'Aluminum foil', 'mfoil-in', 0, 0.0975, 6, 0),
(17, 'Straws and stirrers', 'pstws-in', 0, 0.00091886, 2, 1),
(18, 'Cigarette box or wrappers', 'cboxs-in', 0, 0.0135347, 4, 1),
(19, 'Fireworks, small', 'firws-in', 0, 0, 7, 1),
(20, 'Shopping bags', 'pbags-in', 0, 0.0121254, 2, 1),
(21, 'Nails', 'mnail-in', 0, 0.0198416, 6, 0),
(22, 'Plastic bags, ziplock or snack', 'pzips-in', 0, 0.00330693, 2, 0),
(23, 'Clothes, cloth', 'cloth-in', 0, 0, 10, 1),
(24, 'Plastic bags, trash', 'ptras-in', 0, 0.386271, 2, 0),
(25, 'Wood pallets, pieces and processed wood', 'pwood-in', 0, 48, 10, 1),
(26, 'Polystyrene Foodware (foam)', 'scups-in', 0, 0.141978, 2, 1),
(27, 'Balloons', 'pbals-in', 0, 0.0015532, 2, 1),
(28, 'Paper bags', 'pabgs-in', 0, 0.14, 5, 0),
(29, 'Plastic motor oil bottles', 'pmobs-in', 0, 0.132277, 2, 0),
(30, 'Toys and Beach Accessories', 'btoys-in', 0, 0, 2, 1),
(31, 'Styrofoam food containers', 'scons-in', 0, 0.0136, 11, 1),
(32, 'Disposable lighters', 'cltes-in', 0, 0.0206132, 8, 1),
(33, 'Shoes', 'shoes-in', 0, 0.70625, 7, 1),
(34, 'Plastic fishing line, lures, floats (non-commercial)', 'pfish-in', 0, 0, 2, 0),
(35, 'Styrofoam buoys or floats', 'sflos-in', 0, 4.2, 11, 1),
(36, 'Batteries', 'mbats-in', 0, 0.050625, 8, 1),
(37, 'Diapers', 'diaps-in', 0, 0.507063, 8, 0),
(38, 'Bandaids', 'bands-in', 0, 0.001, 8, 1),
(39, 'Feminine products', 'femps-in', 0, 0.00655556, 8, 0),
(40, 'Condoms', 'conds-in', 0, 0.00856, 8, 0),
(41, 'Metal fishing hooks or lures', 'mhook-in', 0, 0.0220462, 6, 0),
(42, 'Crab pots', 'mcrab-in', 0, 0, 6, 0),
(43, 'Syringes or needles', 'needl-in', 0, 0.0095, 8, 1),
(44, 'Shopping carts', 'scart-in', 0, 35, 9, 1),
(45, 'Tires', 'tires-in', 0, 10, 9, 1),
(46, 'Appliances, small / handheld', 'appsm-in', 0, 0, 9, 1),
(47, 'Bike parts, small', 'bikes-in', 0, 0, 9, 1),
(48, 'Car parts, small', 'cprts-in', 0, 0, 9, 1),
(49, 'Car batteries', 'cbats-in', 1, 40, 8, 0),
(50, 'Plastic six-pack rings', 'prngs-in', 0, 0.00685638, 2, 0),
(51, 'Other, small', 'othsm-in', 0, 0, 10, 1),
(52, 'Beach chairs, umbrellas', 'chair-in', 0, 0, 7, 1),
(53, 'Appliances, large', 'applg-in', 0, 0, 9, 1),
(54, 'Bikes / large bike parts', 'bikel-in', 0, 0, 9, 1),
(55, 'Car parts, large', 'cprtl-in', 0, 0, 9, 1),
(56, 'Other, large', 'othlg-in', 0, 0, 10, 1),
(57, 'Fishing gear (lures, nets, etc.)', 'pfisc-in', 0, 0, 10, 1),
(58, 'Fireworks, large', 'firwl-in', 0, 0, 7, 1),
(60, 'Smoking, tobacco, vape items (not butts)', 'smoke-in', 0, 0, 8, 1),
(61, 'Beer Cans', 'mbcans-in', 1, 0.03, 6, 1),
(62, 'Soda Cans', 'mscans-in', 1, 0.03, 6, 1),
(63, 'Personal hygene', 'phymps-in', 0, 0.00655556, 8, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`iid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `iid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
