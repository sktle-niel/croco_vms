-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 10, 2026 at 10:07 AM
-- Server version: 8.0.44
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `database_scv`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` char(7) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_type` enum('administrator','stelcom') NOT NULL DEFAULT 'stelcom',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `email`, `password`, `user_type`, `created_at`) VALUES
('4552383', 'ptci.ic2official@gmail.com', '$2y$10$YQ1JEzBbMDTbMxUMhHUoVOHiJJFi9mJrpjHRjLgWTdCJBWUJxzGGe', 'administrator', '2026-03-10 06:45:47');

-- --------------------------------------------------------

--
-- Table structure for table `candidate`
--

CREATE TABLE `candidate` (
  `cand_id` int NOT NULL,
  `cand_fullname` varchar(100) DEFAULT NULL,
  `cand_partylist` varchar(45) DEFAULT NULL,
  `cand_position` varchar(45) DEFAULT NULL,
  `cand_photo` varchar(45) DEFAULT NULL,
  `cand_electionbatch` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `dept_id` int NOT NULL,
  `dept_name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`dept_id`, `dept_name`) VALUES
(11, 'BSIT');

-- --------------------------------------------------------

--
-- Table structure for table `election_batch`
--

CREATE TABLE `election_batch` (
  `elc_id` int NOT NULL,
  `elc_name` varchar(45) DEFAULT NULL,
  `elc_schoolyear` date DEFAULT NULL,
  `elc_status` varchar(45) DEFAULT NULL,
  `elc_createdby` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `otp_verifications`
--

CREATE TABLE `otp_verifications` (
  `id` int NOT NULL,
  `email` varchar(255) NOT NULL,
  `otp` varchar(6) NOT NULL,
  `expires_at` bigint NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `partylist`
--

CREATE TABLE `partylist` (
  `partylist_id` int NOT NULL,
  `partylist_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `partylist`
--

INSERT INTO `partylist` (`partylist_id`, `partylist_name`) VALUES
(1, 'IC2 TEAM');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL,
  `school_id` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `department` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `otp` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_verified` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `verified_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `school_id`, `full_name`, `department`, `otp`, `is_verified`, `created_at`, `verified_at`) VALUES
('1744261', '220054', 'Niel Patrick L. Penlas', 'Bachelor of Science in Hospitality Management', '679732', 0, '2026-03-06 13:19:29', NULL),
('3156505', '220058', 'niel', 'Senior Highschool', '000029', 0, '2026-03-06 17:38:43', NULL),
('3369781', '220052', 'Niel Patrick L. Penlas', 'Bachelor of Science in Information Communication Technology', '534869', 0, '2026-03-06 12:41:35', NULL),
('3849548', '220053', 'Niel Patrick L. Penlas', 'Bachelor of Science in Information Communication Technology', '614198', 0, '2026-03-06 13:03:59', NULL),
('4540964', '220041', 'Niel', 'Bachelor of Science in Hospitality Management', '047274', 0, '2026-03-10 13:29:57', NULL),
('5228257', '220056', 'Niel Patrick L Penlas', 'Bachelor of Science in Information Communication Technology', '095533', 0, '2026-03-06 12:06:59', NULL),
('6121508', '220057', 'niel', 'Associate in Computer Technology', '425862', 0, '2026-03-06 17:34:51', NULL),
('8688483', '220050', 'Niel', 'Senior Highschool', '392224', 1, '2026-03-10 13:17:42', NULL),
('8747552', '220051', 'Niel Patrick L Penlas', 'Bachelor of Science in Information Communication Technology', '272447', 0, '2026-03-06 12:11:57', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `candidate`
--
ALTER TABLE `candidate`
  ADD PRIMARY KEY (`cand_id`),
  ADD KEY `election_evnt_idx` (`cand_electionbatch`);

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`dept_id`);

--
-- Indexes for table `election_batch`
--
ALTER TABLE `election_batch`
  ADD PRIMARY KEY (`elc_id`),
  ADD KEY `created_admin_idx` (`elc_createdby`);

--
-- Indexes for table `otp_verifications`
--
ALTER TABLE `otp_verifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `partylist`
--
ALTER TABLE `partylist`
  ADD PRIMARY KEY (`partylist_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `school_id` (`school_id`),
  ADD KEY `idx_school_id` (`school_id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `candidate`
--
ALTER TABLE `candidate`
  MODIFY `cand_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `dept_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `election_batch`
--
ALTER TABLE `election_batch`
  MODIFY `elc_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `otp_verifications`
--
ALTER TABLE `otp_verifications`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `partylist`
--
ALTER TABLE `partylist`
  MODIFY `partylist_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `candidate`
--
ALTER TABLE `candidate`
  ADD CONSTRAINT `election_event` FOREIGN KEY (`cand_electionbatch`) REFERENCES `election_batch` (`elc_id`),
  ADD CONSTRAINT `election_evnt` FOREIGN KEY (`cand_electionbatch`) REFERENCES `election_batch` (`elc_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
