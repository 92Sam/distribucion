-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 17, 2016 at 12:35 AM
-- Server version: 5.7.13-0ubuntu0.16.04.2
-- PHP Version: 7.0.9-1+deb.sury.org~xenial+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `teayudop_distribucion`
--

-- --------------------------------------------------------

--
-- Table structure for table `cliente_datos`
--

CREATE TABLE `cliente_datos` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `tipo` int(11) NOT NULL,
  `valor` varchar(300) NOT NULL,
  `principal` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cliente_datos`
--

INSERT INTO `cliente_datos` (`id`, `cliente_id`, `tipo`, `valor`, `principal`) VALUES
(5, 252, 1, 'dir1', 0),
(6, 252, 1, 'dir1', 0),
(7, 252, 2, 'tlf1', 0),
(8, 252, 2, 'tlf1', 0),
(9, 253, 1, '11111111', 1),
(10, 253, 1, '11111111', 1),
(11, 253, 2, '22222222', 1),
(12, 253, 2, '22222222', 1),
(13, 254, 1, 'dir1', 0),
(14, 254, 1, 'dir1', 1),
(15, 254, 2, 'tlf1', 1),
(16, 254, 2, 'tlf1', 0),
(17, 254, 3, 'mail1', 0),
(18, 254, 3, 'mail1', 1),
(19, 254, 4, 'web1', 1),
(20, 254, 4, 'web1', 0),
(21, 255, 1, 'dir1', 0),
(22, 255, 1, 'dir1', 1),
(23, 255, 2, 'tlf1', 1),
(24, 255, 2, 'tlf1', 0),
(25, 255, 3, 'mail1', 0),
(26, 255, 3, 'mail1', 1),
(27, 255, 4, 'web1', 1),
(28, 255, 4, 'web1', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cliente_datos`
--
ALTER TABLE `cliente_datos`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cliente_datos`
--
ALTER TABLE `cliente_datos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
