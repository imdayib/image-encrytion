-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 21, 2025 at 02:00 PM
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
CREATE DATABASE IF NOT EXISTS image_encryption;
USE image_encryption;

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE IF NOT EXISTS files (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `encrypted_name` varchar(255) NOT NULL,
  `file_type` varchar(50) NOT NULL,
  `file_size` int(11) NOT NULL,
  `encryption_key` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `files`
--

INSERT INTO files (`id`, `user_id`, `original_name`, `encrypted_name`, `file_type`, `file_size`, `encryption_key`, `created_at`) VALUES
(1, 4, 'samples_of_schengen_insurance_coverage_letter.pdf', 'file_682dbeb8dc0df7.69172487.enc', 'application/pdf', 524958, '988056150b4530b838c2374e82da24c33fd465f92853de744a042abb7a40f149', '2025-05-21 11:53:28'),
(2, 4, 'samples_of_schengen_insurance_coverage_letter.pdf', 'file_682dbf2ea8e6b1.44817024.enc', 'application/pdf', 524958, 'e97a39d492d0cb6b63ef16c64e7c7f49615b73fec08c5d8ed72323c988df4dc2', '2025-05-21 11:55:26'),
(3, 4, 'Main Parts of Your Project.pdf', 'file_682dbf63d8e350.77396073.enc', 'application/pdf', 153065, 'bef3274381842a7166195917011337411e36b9ec975ad1358bc9a168f4b10012', '2025-05-21 11:56:19'),
(4, 4, '3195394-uhd_3840_2160_25fps.mp4', 'file_682dc031d9b0c7.33291191.enc', 'video/mp4', 13927646, '5d4e9a4f33dcf3909fe1bfce8c6129b2263fabb8acbce09a3772e6bc3e794d88', '2025-05-21 11:59:45');

-- --------------------------------------------------------

--
-- Table structure for table `operation_logs`
--

CREATE TABLE IF NOT EXISTS operation_logs (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `operation` enum('encrypt','decrypt') NOT NULL,
  `filename` varchar(255) NOT NULL,
  `status` enum('success','failed') NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `operation_logs`
--

INSERT INTO operation_logs (`id`, `user_id`, `operation`, `filename`, `status`, `timestamp`) VALUES
(1, 2, '', 'N/A', 'success', '2025-04-05 11:04:40'),
(2, 2, '', 'N/A', 'success', '2025-04-07 20:31:39'),
(3, 2, '', 'N/A', 'success', '2025-04-16 14:32:04'),
(4, 2, '', 'N/A', 'success', '2025-04-16 15:47:05'),
(5, 2, '', 'N/A', 'success', '2025-04-16 15:52:34'),
(6, 2, '', 'N/A', 'success', '2025-04-16 15:54:57'),
(7, 2, '', 'N/A', 'success', '2025-04-16 16:38:05');

-- --------------------------------------------------------

--
-- Table structure for table `report`
--

CREATE TABLE IF NOT EXISTS report (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` enum('encrypt','decrypt','login','signup','password_change') NOT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `report`
--

INSERT INTO report (`id`, `user_id`, `action`, `details`, `created_at`) VALUES
(1, 4, 'encrypt', 'Encrypted file: samples_of_schengen_insurance_coverage_letter.pdf', '2025-05-21 11:53:28'),
(2, 4, 'encrypt', 'Encrypted file: samples_of_schengen_insurance_coverage_letter.pdf', '2025-05-21 11:55:26'),
(3, 4, 'decrypt', 'Decrypted file: samples_of_schengen_insurance_coverage_letter.pdf', '2025-05-21 11:55:36'),
(4, 4, 'encrypt', 'Encrypted file: Main Parts of Your Project.pdf', '2025-05-21 11:56:19'),
(5, 4, 'decrypt', 'Decrypted file: Main Parts of Your Project.pdf', '2025-05-21 11:56:29'),
(6, 4, 'decrypt', 'Decrypted file: Main Parts of Your Project.pdf', '2025-05-21 11:58:04'),
(7, 4, 'encrypt', 'Encrypted file: 3195394-uhd_3840_2160_25fps.mp4', '2025-05-21 11:59:45'),
(8, 4, 'decrypt', 'Decrypted file: 3195394-uhd_3840_2160_25fps.mp4', '2025-05-21 12:00:04');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS users (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `is_active` tinyint(1) DEFAULT 1,
  `metamask_address` varchar(42) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO users (`id`, `username`, `email`, `password_hash`, `role`, `is_active`, `metamask_address`, `created_at`) VALUES
(1, 'admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1, NULL, '2025-04-03 12:04:21'),
(2, 'admin@admin.com', 'imhamza1@yahoo.com', '$2y$10$POfP1KmIh9peAM2ii6pZyOXu8q2ZfCMdxP1t9m/3qkgCtgsqVS6/i', 'admin', 1, '', '2025-04-03 18:04:47'),
(4, 'abdi', 'abdi@gmail.com', '$2y$10$nLhgJ0RRnuLSFoHYgbz9TOySg.VFerRrzk1lA8Lb1k826CHCPcu9G', 'admin', 1, '0xb0a09d11c251c4df082e5129aa7f7f33d85c71fb', '2025-05-20 09:00:33');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `files`
--
ALTER TABLE files
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_file_user` (`user_id`);

--
-- Indexes for table `operation_logs`
--
ALTER TABLE operation_logs
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `report`
--
ALTER TABLE report
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_report_user` (`user_id`),
  ADD KEY `idx_report_action` (`action`),
  ADD KEY `idx_report_created` (`created_at`);

--
-- Indexes for table `users`
--
ALTER TABLE users
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_user_email` (`email`),
  ADD KEY `idx_user_metamask` (`metamask_address`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `files`
--
ALTER TABLE files
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `operation_logs`
--
ALTER TABLE operation_logs
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `report`
--
ALTER TABLE report
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `files`
--
ALTER TABLE files
  ADD CONSTRAINT `files_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `operation_logs`
--
ALTER TABLE operation_logs
  ADD CONSTRAINT `operation_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `report`
-
ALTER TABLE report
  ADD CONSTRAINT `report_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
