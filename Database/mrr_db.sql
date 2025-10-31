-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 31, 2025 at 06:47 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mrr_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `booking_tbl`
--

CREATE TABLE `booking_tbl` (
  `id` int NOT NULL,
  `booking_ref` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `user_id` int NOT NULL,
  `reason` text COLLATE utf8mb4_general_ci NOT NULL,
  `booking_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `extra_request` text COLLATE utf8mb4_general_ci,
  `doc_Attachment` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `extra_info` varchar(100) COLLATE utf8mb4_general_ci DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking_tbl`
--

INSERT INTO `booking_tbl` (`id`, `booking_ref`, `user_id`, `reason`, `booking_date`, `start_time`, `end_time`, `extra_request`, `doc_Attachment`, `created_date`, `extra_info`) VALUES
(2, 'BKG68f12c2d64fc6', 3, 'Test', '2025-10-08', '15:30:00', '17:30:00', 'Nak extra water', 'uploads/1760636029_edc3ec574b1ac05faee0.jpg', '2025-10-16 17:33:49', 'Approved'),
(3, 'BKG68f136ea94d7a', 4, 'Tesitn reason', '2025-10-17', '08:40:00', '22:40:00', 'nuuh', NULL, '2025-10-16 18:19:27', 'Approved'),
(4, 'BKG68f9166d365ba', 3, 'Testingggg', '2025-10-24', '15:40:00', '16:40:00', 'YEHS', 'uploads/1761154703_bd9156f03057d6d53e2f.png', '2025-10-22 17:38:23', 'Approved'),
(5, 'BKG68f91e894871b', 3, 'Testing image ', '2025-10-24', '16:00:00', '17:00:00', 'Tesitn image viewing', 'uploads/1761156804_e3aa5e47ddd961957cc8.png', '2025-10-22 18:13:24', 'Approved');

-- --------------------------------------------------------

--
-- Table structure for table `jabatan_tbl`
--

CREATE TABLE `jabatan_tbl` (
  `jabatan_id` int NOT NULL,
  `jabatan_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jabatan_tbl`
--

INSERT INTO `jabatan_tbl` (`jabatan_id`, `jabatan_name`, `created_at`, `updated_at`) VALUES
(1, 'Jabatan Kejuruteraan Elektronik', '2025-10-08 17:10:26', '2025-10-08 17:10:26'),
(2, 'Jabatan Teknologi Maklumat', '2025-10-08 17:10:26', '2025-10-08 17:10:26'),
(3, 'Jabatan Pendidikan Am', '2025-10-08 17:10:26', '2025-10-08 17:10:26');

-- --------------------------------------------------------

--
-- Table structure for table `status_tbl`
--

CREATE TABLE `status_tbl` (
  `status_id` int NOT NULL,
  `status_name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `status_tbl`
--

INSERT INTO `status_tbl` (`status_id`, `status_name`) VALUES
(2, 'Approved'),
(4, 'Completed'),
(1, 'Pending'),
(3, 'Rejected');

-- --------------------------------------------------------

--
-- Table structure for table `user_table`
--

CREATE TABLE `user_table` (
  `user_Id` int NOT NULL,
  `full_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `Email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `jabatan_id` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `user_Category` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `Category` varchar(50) COLLATE utf8mb4_general_ci DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_tbl`
--

CREATE TABLE `user_tbl` (
  `user_Id` int NOT NULL,
  `full_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `Email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `jabatan_id` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `user_Category` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `Category` varchar(50) COLLATE utf8mb4_general_ci DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_tbl`
--

INSERT INTO `user_tbl` (`user_Id`, `full_name`, `Email`, `jabatan_id`, `user_Category`, `password`, `Category`) VALUES
(1, 'System Admin', 'admin@system.com', 'Administration', 'admin', 'admin123', 'admin'),
(2, 'System Admin', 'admin@system.com', 'Administration', 'admin', 'admin123', 'admin'),
(3, 'Hadi', 'Hadi123@gmail.com', 'Jabatan Pendidikan Am', 'Hadi123@gmail.com', 'Hadi123', 'user'),
(4, 'Hadi', 'Hadi12345@gmail.com', 'Jabatan Teknologi Maklumat dan Komunikasi', 'Hadi12345@gmail.com', 'Hadi123', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `booking_tbl`
--
ALTER TABLE `booking_tbl`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jabatan_tbl`
--
ALTER TABLE `jabatan_tbl`
  ADD PRIMARY KEY (`jabatan_id`);

--
-- Indexes for table `status_tbl`
--
ALTER TABLE `status_tbl`
  ADD PRIMARY KEY (`status_id`),
  ADD UNIQUE KEY `status_name` (`status_name`);

--
-- Indexes for table `user_table`
--
ALTER TABLE `user_table`
  ADD PRIMARY KEY (`user_Id`);

--
-- Indexes for table `user_tbl`
--
ALTER TABLE `user_tbl`
  ADD PRIMARY KEY (`user_Id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `booking_tbl`
--
ALTER TABLE `booking_tbl`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `jabatan_tbl`
--
ALTER TABLE `jabatan_tbl`
  MODIFY `jabatan_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `status_tbl`
--
ALTER TABLE `status_tbl`
  MODIFY `status_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `user_table`
--
ALTER TABLE `user_table`
  MODIFY `user_Id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_tbl`
--
ALTER TABLE `user_tbl`
  MODIFY `user_Id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
