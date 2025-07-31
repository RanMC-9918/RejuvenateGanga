-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 05, 2025 at 09:29 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rejuvenateganga`
--

-- --------------------------------------------------------

--
-- Table structure for table `donation`
--

CREATE TABLE `donation` (
  `currency` enum('dollar','rupee') NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `donation_amount_usd` double DEFAULT NULL,
  `donation_amount_inr` double DEFAULT NULL,
  `date_of_donation` date NOT NULL,
  `comment` varchar(255) NOT NULL,
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `donation`
--

INSERT INTO `donation` (`currency`, `firstname`, `lastname`, `email`, `donation_amount_usd`, `donation_amount_inr`, `date_of_donation`, `comment`, `id`) VALUES
('dollar', 'test', 'st', 'test@gmail.com', NULL, 123, '2025-04-04', 'test', 131),
('dollar', 'TesterID2', 'Tester', 'Teste2r@gmail.com', 21, NULL, '2025-04-17', 'test', 132),
('dollar', 'TesterID2', 'Tester', 'te213231st@gmail.com', 221, NULL, '2025-04-16', 'test', 133),
('dollar', 'TesterID2', 'Tester', 'T32434234e2s4t23e4r2@34gm2a34il.com', 234, NULL, '2025-04-04', 'tewt', 134),
('dollar', 'TesterID2', 'Tester', 'Tester@gmail.com', 123, NULL, '2025-04-04', 'test', 135),
('dollar', 'TesterID2', 'Tester', 'te21234233231st@gmail.com', 123, NULL, '2025-04-04', 'test', 136),
('dollar', '3test', 'Tester', 'te2123432423233231st@gmail.com', 123, NULL, '2025-04-04', 'test', 137),
('dollar', 'TesterID2', 'Tester', 'Tes213123123325678te2r@gmail.com', 21, NULL, '2025-04-04', 'test', 138),
('dollar', 'TesterID2', 'Tester', 'Tes213123123325678te2r@gmail.com', NULL, 234, '2025-04-04', 'test', 139),
('dollar', 'TesterID2', 'Tester', 'test@gmail.com', 123, NULL, '2025-04-04', 'test', 140),
('dollar', 'TesterID2', 'Tester', 'Tester@gmail.com', NULL, 23, '2025-04-10', 'test', 141);

-- --------------------------------------------------------

--
-- Table structure for table `event_planner`
--

CREATE TABLE `event_planner` (
  `event` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `date_of_event` date NOT NULL,
  `duration` float NOT NULL,
  `comment` varchar(255) NOT NULL,
  `published` int(255) NOT NULL,
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `event_registered`
--

CREATE TABLE `event_registered` (
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `event_registered` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `duration` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_registered`
--

INSERT INTO `event_registered` (`firstname`, `lastname`, `email`, `event_registered`, `date`, `duration`) VALUES
('Aarav', 'Singh', '	\r\ntest21@gmail.com', 'test', '2025-03-28', 10),
('Aarav', 'Singh', 'test21@gmail.com', 'test', '2025-03-31', 2),
('Aarav', 'Singh', 'test21@gmail.com', 'test', '2025-03-31', 2);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `recoverytext` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`firstname`, `lastname`, `recoverytext`, `email`, `city`, `state`, `country`, `password`) VALUES
('TesterID2', 'Tester', 'Stevenson High School', '32Teste2r@gmail.com', 'Buffalo Grove', 'Illinois', 'United States', 'test'),
('TesterID2', 'Tester', 'Test', 'Te4123231ster@gmail.com', 'Buffalo Grove', 'Illinois', 'United States', 'test'),
('Test1', 'Test', 'NDA', 'Test1@gmail.com', 'Buffalo Grove', 'IL', 'United States', 'Test@123'),
('TesterID2', 'Tester', 'Test', 'test@gmail.com', 'Buffalo Grove', 'Illinois', 'USA', 'Test'),
('TesterID', 'Tester', 'Test', 'Tester@gmail.com', 'Buffalo Grove', 'Illinois', 'United States', 'Tester');

-- --------------------------------------------------------

--
-- Table structure for table `volunteer`
--

CREATE TABLE `volunteer` (
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `recoverytext` varchar(255) NOT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `dob` date NOT NULL,
  `organization` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `volunteer`
--

INSERT INTO `volunteer` (`firstname`, `lastname`, `email`, `password`, `recoverytext`, `city`, `state`, `country`, `dob`, `organization`) VALUES
('TesterID2', 'Tester', 't21213@gmail.com', 'test', 'test', 'test', 'test', 'test', '2025-03-13', 'test'),
('TesterID2', 'Tester', 'te23232st@gmail.com', 'test', 'test', 'Buffalo Grove', 'Illinois', 'United States', '2025-03-15', 'test'),
('Aarav', 'Singh', 'test21@gmail.com', 'test', 'test', 'test', 'test', 'test', '2025-03-24', 'test'),
('TesterID2', 'Tester', 'test321@gmail.com', 'test', 'test', 'test', 'test', 'test', '2025-03-24', 'test'),
('Test', 'Test', 'Test@gmail.com', '', '', 'Test', 'Test', 'Test', '2025-02-12', 'Test'),
('TesterID2', 'Tester', 'Tester@gmail.com', 'test', 'test', 'test', 'test', 'test', '2025-03-21', 'test');

-- --------------------------------------------------------

--
-- Table structure for table `volunteer_tracker`
--

CREATE TABLE `volunteer_tracker` (
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `event_enrolled` varchar(255) NOT NULL,
  `work_performed` varchar(255) NOT NULL,
  `hours` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `volunteer_tracker`
--

INSERT INTO `volunteer_tracker` (`firstname`, `lastname`, `email`, `event_enrolled`, `work_performed`, `hours`) VALUES
('test', 'test', 'test@gmail.com', 'test', 'test', 25);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `donation`
--
ALTER TABLE `donation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `event_planner`
--
ALTER TABLE `event_planner`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `volunteer`
--
ALTER TABLE `volunteer`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `volunteer_tracker`
--
ALTER TABLE `volunteer_tracker`
  ADD PRIMARY KEY (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `donation`
--
ALTER TABLE `donation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=142;

--
-- AUTO_INCREMENT for table `event_planner`
--
ALTER TABLE `event_planner`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
