-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 27, 2022 at 12:08 PM
-- Server version: 10.4.25-MariaDB
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `shop1`
--

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

CREATE TABLE `carts` (
  `ID` int(100) NOT NULL,
  `USERID` int(100) NOT NULL,
  `PRODUCTID` int(100) NOT NULL,
  `DATEOFINSERTION` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `ID` int(100) NOT NULL,
  `NAME` varchar(100) NOT NULL,
  `PRODUCTCODE` varchar(100) NOT NULL,
  `PRICE` float NOT NULL,
  `DATEOFWITHDRAWAL` datetime DEFAULT NULL,
  `SELLERNAME` varchar(100) NOT NULL,
  `CATEGORY` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`ID`, `NAME`, `PRODUCTCODE`, `PRICE`, `DATEOFWITHDRAWAL`, `SELLERNAME`, `CATEGORY`) VALUES
(2, 'candle 2', 'A002', 5, '1955-06-08 00:00:00', 'seller1', 'aromatic'),
(5, 'art 1', 'A0025', 15, NULL, 'seller1', 'halloween'),
(6, 'candle 3', 'A0024', 20, NULL, 'seller1', 'christmas'),
(22, 'demo candle 7', 'asdf', 20, NULL, 'seller1', 'aromatic'),
(26, 'aaa', 'aaaa', 2, '2022-11-21 13:52:04', 'seller1', 'aaa'),
(28, 'pillar', 'A52', 22, NULL, 'seller1', 'halloween'),
(34, 'goofy', 'A5', 5, NULL, 'seller1', 'aromatic'),
(40, 'body', 'A55', 11, NULL, 'seller1', 'aromatic'),
(41, 'bubbles', 'A001', 15, NULL, 'seller1', 'aromatic'),
(44, 'candle 10', 'q1', 12, NULL, 'seller2', 'halloween'),
(45, 'deco1', 'd1', 15, NULL, 'seller2', 'christmas'),
(46, 'deco2', 'd2', 20, NULL, 'seller2', 'aromatic'),
(47, 'rudolf', 'A12', 30, NULL, 'seller2', 'christmas'),
(48, 'monkey', 'B1', 10, NULL, 'seller1', 'animal'),
(49, 'panda', 'B2', 12, NULL, 'seller1', 'animal'),
(50, 'coalla', 'B3', 20, NULL, 'seller1', 'animal'),
(51, 'dog', 'B4', 10, NULL, 'seller1', 'animal'),
(52, 'cat', 'B5', 5, NULL, 'seller1', 'animal');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `ID` int(100) NOT NULL,
  `NAME` varchar(100) NOT NULL,
  `SURNAME` varchar(100) NOT NULL,
  `USERNAME` varchar(100) NOT NULL,
  `PASSWORD` varchar(200) NOT NULL,
  `EMAIL` varchar(100) NOT NULL,
  `ROLE` enum('ADMIN','PRODUCTSELLER','USER') NOT NULL,
  `CONFIRMED` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`ID`, `NAME`, `SURNAME`, `USERNAME`, `PASSWORD`, `EMAIL`, `ROLE`, `CONFIRMED`) VALUES
(1, 'Giannis', 'Tasio', 'f', '$2y$10$OOni58CTcWbilED6x8qpT.gLba.q7s9fzm3r8r5BuXuwACP/9CEWu', 'asdf', 'USER', 1),
(2, 'seller1', 'sss', 'seller1', '$2y$10$MbQy8M1kqMIfG9UmKwUtE.RRi2iVOvZxR8p0kbs3ful8O58DFQy3y', 'fwqtgeg', 'PRODUCTSELLER', 1),
(6, 'ad1', 'aaa', 'admin', '$2y$10$.NTDdtz6dQWlqvXYnHDASuuS49S5OiMrDOUtEz9RRbucYnQlSw5N2', 'asdfg', 'ADMIN', 1),
(7, 'John', 'Doe', 'admin2', '$2y$10$AHJlKCLWdAsrd.ui7gfa2eHvhwGB4i2lzW7wTSX9.3nol.BHKwIfW', 'qwerty', 'ADMIN', 1),
(15, 'adwqfe', 'a', 'a', '$2y$10$4l72tj4DuSX4csd8ianuUuqSb8A.4pbv2iHaa45XJTGZBGC7M1akm', 'a', 'ADMIN', 1),
(16, 'seller2', 's2', 'seller2', '$2y$10$Vzh1bpuncMchloe6FekyreBzJ0w69ahWQfleEFfYf8TERF9LIxE9y', 'abdah', 'PRODUCTSELLER', 1),
(17, 'vsfabe', 'vdsfvse', 'seller3', '$2y$10$ue7jxbeAFOlSInUCQy5pPeiODyof31nZdHT8ulzTPdxjR70RnKowK', 'fsgve', 'PRODUCTSELLER', 1),
(18, 'veqga', 'veqbwet', 'seller4', '$2y$10$lLKagkRUlqJxi23XqmNpVOPJkf9I6Rhi3lQa0R.6e8aeR3yRGZMbW', 'fsabtwg', 'PRODUCTSELLER', 1),
(19, 'ewgeg', 'givbkj', 'seller5', '$2y$10$ZMzg8zGE9Zlq8By9sVFsKeKUws4DgMY56ePjEq85fmIpzbImOiG8u', 'hbkvjnb', 'PRODUCTSELLER', 1),
(20, 'Jack', 'Black', 'J', '$2y$10$s6s5gLENkgghjUqnFhVsdOmx6u2ElAdKAAgD/G7SZLwCMXCo59pSe', 'gsdgs', 'PRODUCTSELLER', 1),
(21, 'Albus', 'Dumbledore', 'd', '$2y$10$xsi4XBNQ1cAOgEvMGfZay.Zu6CwOys3tey8BktM388xq4eonhO0RS', 'csdnfuwr', 'USER', 1),
(22, 'Lord', 'Voldemord', 'l', '$2y$10$C3gaIy5iLuUQE4oSdGCGZuSfmoRqgSJSJvPSghFHYtv.tffzCJ1MC', 'ighghknv', 'USER', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `user_id_fkey` (`USERID`),
  ADD KEY `product_id_fkey` (`PRODUCTID`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `PRODUCTCODE` (`PRODUCTCODE`,`CATEGORY`),
  ADD KEY `sellername_fkey` (`SELLERNAME`) USING BTREE;

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `USERNAME` (`USERNAME`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `ID` int(100) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `ID` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `ID` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `product_id_fkey` FOREIGN KEY (`PRODUCTID`) REFERENCES `products` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `user_id_fkey` FOREIGN KEY (`USERID`) REFERENCES `users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `sellername_fkey` FOREIGN KEY (`SELLERNAME`) REFERENCES `users` (`USERNAME`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
