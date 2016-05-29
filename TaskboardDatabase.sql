-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: May 28, 2016 at 06:36 PM
-- Server version: 10.1.9-MariaDB
-- PHP Version: 5.6.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `taskboard`
--

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `Id` int(11) NOT NULL,
  `ParentId` int(11) DEFAULT NULL,
  `Title` varchar(100) NOT NULL,
  `UserId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`Id`, `ParentId`, `Title`, `UserId`) VALUES
(3, NULL, 'Main Category', 4),
(13, NULL, 'Main Category', 3),
(14, 13, 'Sub Category', 3);

-- --------------------------------------------------------

--
-- Table structure for table `task`
--

CREATE TABLE `task` (
  `Id` int(11) NOT NULL,
  `Title` varchar(100) NOT NULL,
  `CategoryId` int(11) NOT NULL,
  `DueDate` datetime NOT NULL,
  `DoneDate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `task`
--

INSERT INTO `task` (`Id`, `Title`, `CategoryId`, `DueDate`, `DoneDate`) VALUES
(2, 'My Task', 3, '2016-01-01 00:00:00', NULL),
(3, 'New task 222', 13, '2016-06-30 00:00:00', '2016-06-29 00:00:00'),
(4, 'My Task', 13, '2016-01-01 00:00:00', NULL),
(5, 'My Task', 13, '2016-01-01 00:00:00', NULL),
(6, 'My Task', 13, '2016-01-01 00:00:00', NULL),
(7, 'My Task', 13, '2016-01-01 00:00:00', NULL),
(8, 'My Task', 13, '2016-01-01 00:00:00', NULL),
(9, 'My Task', 13, '2016-01-01 00:00:00', NULL),
(10, 'My Task', 13, '2016-01-01 00:00:00', NULL),
(11, 'My Task', 13, '2016-01-01 00:00:00', NULL),
(12, 'My Task', 13, '2016-01-01 00:00:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `Id` int(11) NOT NULL,
  `Email` varchar(50) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `FirstName` varchar(50) DEFAULT NULL,
  `LastName` varchar(50) DEFAULT NULL,
  `HasImage` bit(1) NOT NULL DEFAULT b'0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`Id`, `Email`, `Password`, `FirstName`, `LastName`, `HasImage`) VALUES
(3, 'dmanavi@live.com', '7d2cefbe919ed6803567487f66885768', 'Danial', 'Manavi', b'1'),
(4, 'dmanavi@gmail.com', 'fe32a8a5c0673d8c18bf85b5df3e6d9d', NULL, NULL, b'0');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `FK_OWNER` (`UserId`);

--
-- Indexes for table `task`
--
ALTER TABLE `task`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `IX_CategoryId` (`CategoryId`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`Id`),
  ADD UNIQUE KEY `IX_USERNAME` (`Email`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
--
-- AUTO_INCREMENT for table `task`
--
ALTER TABLE `task`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `category`
--
ALTER TABLE `category`
  ADD CONSTRAINT `FK_OWNER` FOREIGN KEY (`UserId`) REFERENCES `user` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `task`
--
ALTER TABLE `task`
  ADD CONSTRAINT `FK_CATEGORY` FOREIGN KEY (`CategoryId`) REFERENCES `category` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
