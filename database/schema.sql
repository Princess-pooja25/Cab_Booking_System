-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 08, 2026 at 06:36 PM
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
-- Database: `taxi_booking`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `pickup_location` varchar(255) NOT NULL,
  `drop_location` varchar(255) NOT NULL,
  `pickup_time` datetime NOT NULL,
  `cab_type` varchar(50) DEFAULT NULL,
  `status` enum('Confirmed','Ongoing','Completed','Cancelled') NOT NULL DEFAULT 'Confirmed',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `driver_id` int(11) DEFAULT NULL,
  `fare` decimal(8,2) DEFAULT NULL,
  `payment_status` varchar(20) DEFAULT 'Pending',
  `transaction_id` varchar(100) DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `pickup_location`, `drop_location`, `pickup_time`, `cab_type`, `status`, `created_at`, `driver_id`, `fare`, `payment_status`, `transaction_id`, `paid_at`) VALUES
(117, 8, 'VIT', 'vellore', '2026-02-17 19:53:00', 'Mini', 'Completed', '2026-02-16 14:24:02', 1, 180.00, 'Paid', 'TXN1771251854938', '2026-02-16 19:54:14'),
(118, 9, 'VIT', 'delhi', '2026-03-28 17:11:00', 'Mini', 'Completed', '2026-03-27 11:41:42', 1, 170.00, 'Pending', NULL, NULL),
(119, 9, 'chennai airport', 'vellore', '2026-03-29 17:13:00', 'Sedan', 'Ongoing', '2026-03-27 11:44:06', 2, 200.00, 'Pending', NULL, NULL),
(120, 9, 'chennai', 'vellore', '2026-03-31 17:16:00', 'Mini', 'Confirmed', '2026-03-27 11:46:24', NULL, NULL, 'Pending', NULL, NULL),
(121, 9, 'chennai', 'vellore', '2026-03-31 17:16:00', 'Sedan', 'Ongoing', '2026-03-27 11:46:30', 2, 275.00, 'Pending', NULL, NULL),
(122, 9, 'chennai airport', 'delhi', '2026-04-01 17:33:00', 'SUV', 'Completed', '2026-03-27 12:03:37', 3, 380.00, 'Paid', 'TXN1774613031782', '2026-03-27 17:33:51'),
(123, 11, 'VIT', 'vellore', '2026-03-31 15:58:00', 'Mini', 'Confirmed', '2026-03-30 10:28:24', NULL, NULL, 'Pending', NULL, NULL),
(124, 11, 'VIT', 'vellore', '2026-03-31 15:58:00', 'Sedan', 'Completed', '2026-03-30 10:28:33', 2, 290.00, 'Paid', 'TXN1774866526515', '2026-03-30 15:58:46'),
(125, 11, 'VIT', 'vellore', '2026-03-30 16:53:00', 'Sedan', 'Completed', '2026-03-30 11:23:07', 2, 260.00, 'Paid', 'TXN1774869803511', '2026-03-30 16:53:23'),
(126, 11, 'chennai airport', 'delhi', '2026-03-31 16:54:00', 'SUV', 'Completed', '2026-03-30 11:24:14', 3, 360.00, 'Paid', 'TXN1774869863554', '2026-03-30 16:54:23');

-- --------------------------------------------------------

--
-- Table structure for table `drivers`
--

CREATE TABLE `drivers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `cab_type` varchar(20) NOT NULL,
  `is_available` tinyint(1) DEFAULT 1,
  `rating` float DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `drivers`
--

INSERT INTO `drivers` (`id`, `name`, `phone`, `cab_type`, `is_available`, `rating`) VALUES
(1, 'Ramesh', '9876543210', 'Mini', 0, 4),
(2, 'Suresh', '9123456780', 'Sedan', 1, 0),
(3, 'Mahesh', '9988776655', 'SUV', 1, 4);

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE `ratings` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `driver_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 5),
  `review` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ratings`
--

INSERT INTO `ratings` (`id`, `booking_id`, `driver_id`, `user_id`, `rating`, `review`, `created_at`) VALUES
(1, 104, 3, 7, 5, '', '2026-02-15 15:26:16'),
(2, 117, 1, 8, 4, 'good', '2026-02-16 14:26:17'),
(3, 122, 3, 9, 3, 'good', '2026-03-27 12:09:49');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`) VALUES
(1, 'admin', NULL, '$2y$10$1LUgLLViEvNcb8B78UeEAe8LKdbwv4/uNCXySezFRBbACATfYDg96'),
(2, 'Pooja', 'poojapragasam2005@gmail.com', '$2y$10$MQobtwvmxJiO1pYfbaXTTe4Z3Z7DUoinsAMpo/40dYHKoQWyDdgQK'),
(3, 'Kavi', 'kavi@gmail.com', '$2y$10$0M1I9In2t8PJF6T0u3VBPerBT9.OTT46xDMHcrQycBrGXLJF8gavS'),
(4, 'sangeetha', 'sangeetha@gmail.com', '$2y$10$Jf7gkKaSsp6Aa5fqIjRjluRN.VYAgXaUkuYYmsPAxBeMRWqqIrHLq'),
(5, 'Shivani', 'shivani@gmail.com', '$2y$10$phpRg3pc2xNF80GB2cZ1WO1YBtl5jANemoJtkRAXtuOF4cDuMBW/u'),
(6, 'geet', 'geet@gmail.com', '$2y$10$PH.sCaqCrO9zP8p2SKIT8unlhYxeNPioophoZrwA8VVyQT6.5nc2u'),
(7, 'rani', 'rani@gmail.com', '$2y$10$xC/k6IC84C5oguqxDnBCCOxKq1USnOHmmKFlbN/bJedn.ph/q5dYW'),
(8, 'karthik', 'karthik@gmail.com', '$2y$10$norfyK6GUu2iyOqT2uE92uebw/GQ/iNgeHOlVA4j6solo0fHNZfJq'),
(9, 'shivani1', 'shivani1sh@gmail.com', '$2y$10$6ZgsSiecOkqiQ/lX3oRdXu3ilFA5iE0chfbuDKSo508egiBCIMaxO'),
(10, 'yuva', 'yuvaedu26@gmail.com', '$2y$10$8XTOD5CZ6J664NW7Wck7Ee/wplkXusZ5gbzdePARJNzrgFDnazcA6'),
(11, 'yuva1', 'yuva123@gmail.com', '$2y$10$0iMGreCu2V9uGMqrWuf9IeTRxaRM2sVkyVWeLbODxJbOBmfJ6muAu');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `drivers`
--
ALTER TABLE `drivers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=127;

--
-- AUTO_INCREMENT for table `drivers`
--
ALTER TABLE `drivers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
