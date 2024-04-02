-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 21, 2024 at 12:50 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.1.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ticketing_ussd_app`
--

-- --------------------------------------------------------

--
-- Table structure for table `ussd_session`
--

CREATE TABLE `ussd_session` (
  `id` int(11) NOT NULL,
  `session_id` varchar(128) NOT NULL,
  `number` varchar(24) DEFAULT NULL,
  `data` varchar(256) NOT NULL,
  `level` int(11) NOT NULL,
  `stage` int(11) NOT NULL,
  `date` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ussd_session`
--

INSERT INTO `ussd_session` (`id`, `session_id`, `number`, `data`, `level`, `stage`, `date`) VALUES
(32, 'S8D6G00G067AS', '233546749482', '{\"input\":\"2\",\"city\":\"Accra\",\"amount\":12,\"payment_number\":\"0546749482\"}', 1, 4, '2024-03-20 23:43:25');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ussd_session`
--
ALTER TABLE `ussd_session`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessionid_index` (`session_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ussd_session`
--
ALTER TABLE `ussd_session`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
