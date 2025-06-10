-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 10, 2025 at 06:18 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wecare_clean`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_log`
--

CREATE TABLE `audit_log` (
  `id` int(11) NOT NULL,
  `timestamp` datetime DEFAULT current_timestamp(),
  `email` varchar(255) DEFAULT NULL,
  `violation_type` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_log`
--

INSERT INTO `audit_log` (`id`, `timestamp`, `email`, `violation_type`, `ip_address`) VALUES
(1, '2025-06-10 23:53:21', 'asdasdas@gmail.com', 'Unknown Email', '::1'),
(2, '2025-06-10 23:53:24', 'asdasdas@gmail.com1', 'Unknown Email', '::1'),
(3, '2025-06-10 23:53:27', 'sample ', 'Unknown Email', '::1'),
(4, '2025-06-10 23:53:32', 'resident@gmail.com', 'Wrong Password', '::1'),
(5, '2025-06-10 23:53:38', 'admin@gmail.com', 'Login Success', '::1'),
(6, '2025-06-11 00:08:33', 'osiasrussell@gmail.com', 'Unknown Email', '::1'),
(7, '2025-06-11 00:08:40', 'osiasrussell@gmail.com', 'Unknown Email', '::1'),
(8, '2025-06-11 00:09:02', 'osiasrussell@gmail.com', 'Wrong Password', '::1'),
(9, '2025-06-11 00:09:07', 'osiasrussell@gmail.com', 'Wrong Password', '::1'),
(10, '2025-06-11 00:09:33', 'osiasrussell@gmail.com', 'Wrong OTP', '::1'),
(11, '2025-06-11 00:13:22', 'osiasrussell@gmail.com', 'Invalid OTP', '::1'),
(12, '2025-06-11 00:13:28', 'osiasrussell@gmail.com', 'Invalid OTP', '::1'),
(13, '2025-06-11 00:17:45', 'reikatauchiha@gmail.com', 'Invalid OTP', '::1');

-- --------------------------------------------------------

--
-- Table structure for table `complaints`
--

CREATE TABLE `complaints` (
  `id` int(11) NOT NULL,
  `resident_id` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('pending','in_progress','resolved') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `description_encrypted` blob DEFAULT NULL,
  `assigned_officer_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `complaints`
--

INSERT INTO `complaints` (`id`, `resident_id`, `title`, `description`, `status`, `created_at`, `description_encrypted`, `assigned_officer_id`) VALUES
(1, 3, 'Noise Disturbance', 'Excessive noise from nearby construction at night.', 'in_progress', '2025-06-10 14:20:09', 0x15a72d80bd45ecaf7e09ce1be206de31db00df8b64851ea60fa405ec744ceeb8bf3c4f20e34a6d8e2aefb6691f2f4f28cb8dc79dafc235e6acd10e7729b73a84, 2),
(2, 3, 'Water Supply Issue', 'No water supply for two days.', 'in_progress', '2025-06-10 14:20:09', 0xc9b2a546c8117fb232cf4be5cc39a75d88404e56488a24880130ce69e1414502, 2);

-- --------------------------------------------------------

--
-- Table structure for table `login_logs`
--

CREATE TABLE `login_logs` (
  `id` int(11) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `status` enum('success','failed') NOT NULL,
  `timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login_logs`
--

INSERT INTO `login_logs` (`id`, `email`, `status`, `timestamp`) VALUES
(1, 'asdasdas@gmail.com', 'failed', '2025-06-10 23:48:35'),
(2, 'resident@gmail.com', 'failed', '2025-06-10 23:48:43'),
(3, 'officer@gmail.com', 'success', '2025-06-10 23:48:52'),
(4, 'admin@gmail.com', 'success', '2025-06-10 23:48:58');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','officer','resident') DEFAULT 'resident',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Russell', 'Osias', 'admin@gmail.com', '$2y$10$/ai3Do8jLEOfEsbqPEq8beSa055isZvGsyxKYPeGi8BdeQyqxX4f6', 'admin', '2025-06-10 14:19:59'),
(2, 'Mark John', 'Jopia', 'djrussellosias@gmail.com', '$2y$10$/ai3Do8jLEOfEsbqPEq8beSa055isZvGsyxKYPeGi8BdeQyqxX4f6', 'officer', '2025-06-10 14:19:59'),
(3, 'Sample', 'Resident', 'reikatauchiha@gmail.com', '$2y$10$/ai3Do8jLEOfEsbqPEq8beSa055isZvGsyxKYPeGi8BdeQyqxX4f6', 'resident', '2025-06-10 14:19:59'),
(5, 'Osias', 'Russell', 'osiasrussell@gmail.com', '$2y$10$/ai3Do8jLEOfEsbqPEq8beSa055isZvGsyxKYPeGi8BdeQyqxX4f6', 'admin', '2025-06-10 16:08:58');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `complaints`
--
ALTER TABLE `complaints`
  ADD PRIMARY KEY (`id`),
  ADD KEY `resident_id` (`resident_id`);

--
-- Indexes for table `login_logs`
--
ALTER TABLE `login_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `complaints`
--
ALTER TABLE `complaints`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `login_logs`
--
ALTER TABLE `login_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `complaints`
--
ALTER TABLE `complaints`
  ADD CONSTRAINT `complaints_ibfk_1` FOREIGN KEY (`resident_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
