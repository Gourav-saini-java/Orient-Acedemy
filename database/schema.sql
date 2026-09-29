-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 01, 2026 at 04:14 PM
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
-- Database: `pyqhunt`
--
CREATE DATABASE IF NOT EXISTS `pyqhunt` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `pyqhunt`;

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE IF NOT EXISTS `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(1, 'gourav', '$2y$10$XFPWezpLJrgL2DOE8Eqg8enqCKg4us6hrdwhPcWvmrQk5hTETZgYa'),
(2, 'admin', '$2y$10$wWvB1TWCSX5exbMRNSg2eu9M.MZ4ff1FjyCf28CQO3qzEEd7MW5mG')
ON DUPLICATE KEY UPDATE `username`=`username`;

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE IF NOT EXISTS `courses` (
  `id` int(11) NOT NULL,
  `course_name` varchar(100) NOT NULL,
  `course_slug` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `icon_class` varchar(50) DEFAULT 'bi-mortarboard-fill',
  `badge_color` varchar(30) DEFAULT 'primary'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `course_name`, `course_slug`, `description`, `icon_class`, `badge_color`) VALUES
(1, 'MCA', 'mca', 'Master of Computer Applications', 'bi-mortarboard-fill', 'primary'),
(2, 'BCA', 'bca', 'Bachelor of Computer Applications', 'bi-laptop-fill', 'success'),
(3, 'B.Tech', 'btech', 'Bachelor of Technology', 'bi-cpu-fill', 'danger'),
(4, 'MBA', 'mba', 'Master of Business Administration', 'bi-briefcase-fill', 'warning')
ON DUPLICATE KEY UPDATE `course_name`=`course_name`;

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE IF NOT EXISTS `subjects` (
  `id` int(11) NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `subject_name` varchar(100) DEFAULT NULL,
  `semester` varchar(20) DEFAULT '1',
  `subject_code` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `course_id`, `subject_name`, `semester`, `subject_code`) VALUES
(1, 4, 'Business Research Methods', '2', 'MBA-201'),
(2, 4, 'Consumer Management', '2', 'MBA-202'),
(3, 4, 'Financial Management', '2', 'MBA-203'),
(4, 4, 'Marketing Management', '2', 'MBA-204'),
(5, 4, 'Production and Materials Management', '2', 'MBA-205')
ON DUPLICATE KEY UPDATE `subject_name`=`subject_name`;

-- --------------------------------------------------------

--
-- Table structure for table `papers`
--

CREATE TABLE IF NOT EXISTS `papers` (
  `id` int(11) NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `semester` varchar(20) DEFAULT NULL,
  `paper_title` varchar(255) DEFAULT NULL,
  `drive_link` text DEFAULT NULL,
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `views` int(11) NOT NULL DEFAULT 0,
  `downloads` int(11) NOT NULL DEFAULT 0,
  `is_latest` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `papers`
--

INSERT INTO `papers` (`id`, `course_id`, `subject_id`, `year`, `semester`, `paper_title`, `drive_link`, `upload_date`, `views`, `downloads`, `is_latest`) VALUES
(1, 4, 1, 2026, '2', 'MBA 2ND SEM BUSINESS RESEARCH METHODS MAY 2026', 'https://drive.google.com/file/d/1vxf_JZb0yYxE1Xpg-m36M1KaZcOAy2TI/view?usp=drive_link', '2026-06-15 08:51:18', 1, 0, 1),
(2, 4, 2, 2024, '2', 'MBA 2ND SEM CONSUMER MANAGEMENT JUNE 2024', 'https://drive.google.com/file/d/1aaX75I55k8ty-UFBgBQfPB87Jbh15K7g/view?usp=drive_link', '2026-06-15 10:02:48', 0, 0, 1),
(3, 4, 3, 2026, '2', 'MBA 2ND SEM FINANCIAL MANAGEMENT MAY 2026', 'https://drive.google.com/file/d/1Drw3Z6obcQ2MSPIbqGA1_tt3BOM53-SQ/view?usp=drive_link', '2026-06-15 10:05:06', 2, 1, 1),
(4, 4, 4, 2026, '2', 'MBA 2ND SEM MARKETING MANAGEMENT MAY 2026', 'https://drive.google.com/file/d/1tSOoh9kHVE3oxBgkvsTYTMURjvrDCxsP/view?usp=drive_link', '2026-06-15 10:07:18', 1, 1, 1),
(5, 4, 5, 2026, '2', 'MBA 2ND SEM PRODUCTION AND MATERIALS MANAGEMENT MAY 2026', 'https://drive.google.com/file/d/1XXFMq6xYh11fhoCxEgaEhJkvfWXXBU6Q/view?usp=drive_link', '2026-06-15 10:07:47', 4, 2, 1)
ON DUPLICATE KEY UPDATE `paper_title`=`paper_title`;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `phone`, `email`, `password`, `created_at`) VALUES
(1, 'Gourav Saini', '9238562121', 'sainigourav2121@gmail.com', '$2y$10$fJsfA8BogBq6fPs/sJiMVenQT9cGjqu/QEPXttjiwoQKMiOLj906.', '2026-06-13 12:56:21'),
(2, 'Ritvik Saini', '7452630188', 'sainiritvik25@gmail.com', '$2y$10$UNCHfGZN2gIclKbeKljeuOe4RXDjzznyk900QdyCOdyZQuaHS2AJy', '2026-06-14 17:08:26'),
(3, 'Nikhil chouhan', '7489332403', 'nikhilchouhan@gmail.com', '$2y$10$PYCAtr61X4vIhbpA2DzKNek6xry9mSrPbPOwVLazVnnZuXSlmvUsi', '2026-06-21 08:56:37')
ON DUPLICATE KEY UPDATE `email`=`email`;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY IF NOT EXISTS (`id`),
  ADD UNIQUE KEY IF NOT EXISTS `username` (`username`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY IF NOT EXISTS (`id`);

--
-- Indexes for table `papers`
--
ALTER TABLE `papers`
  ADD PRIMARY KEY IF NOT EXISTS (`id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY IF NOT EXISTS (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY IF NOT EXISTS (`id`),
  ADD UNIQUE KEY IF NOT EXISTS `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `papers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
