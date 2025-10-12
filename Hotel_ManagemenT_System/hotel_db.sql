-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 12, 2025 at 02:40 PM
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
-- Database: `hotel_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `checkin` date NOT NULL,
  `checkout` date NOT NULL,
  `total_amount` decimal(10,2) DEFAULT 0.00,
  `status` enum('pending','confirmed','checked_in','checked_out','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `num_guests` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `room_id`, `checkin`, `checkout`, `total_amount`, `status`, `created_at`, `num_guests`) VALUES
(1, 2, 2, '2025-10-11', '2025-10-12', 150.00, 'cancelled', '2025-10-11 06:09:30', 1),
(2, 2, 2, '2025-10-12', '2025-10-13', 95.00, 'cancelled', '2025-10-11 06:32:17', 2),
(3, 2, 2, '2025-10-12', '2025-10-13', 95.00, 'cancelled', '2025-10-11 06:33:23', 2),
(4, 2, 2, '2025-10-12', '2025-10-13', 80.00, '', '2025-10-11 06:33:58', 2),
(5, 6, 2, '2025-10-13', '2025-10-15', 168.00, 'cancelled', '2025-10-12 10:55:20', 1),
(6, 4, 2, '2025-10-20', '2025-10-22', 172.00, 'cancelled', '2025-10-12 11:59:06', 1),
(7, 4, 1, '2025-10-14', '2025-10-15', 65.00, '', '2025-10-12 12:00:10', 1),
(8, 4, 10, '2025-10-20', '2025-10-21', 125.00, '', '2025-10-12 12:05:12', 1),
(9, 2, 10, '2025-10-20', '2025-10-28', 805.00, '', '2025-10-12 12:36:02', 1);

-- --------------------------------------------------------

--
-- Table structure for table `booking_guests`
--

CREATE TABLE `booking_guests` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `guest_name` varchar(255) NOT NULL,
  `guest_email` varchar(255) DEFAULT NULL,
  `guest_phone` varchar(50) DEFAULT NULL,
  `guest_nid` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking_guests`
--

INSERT INTO `booking_guests` (`id`, `booking_id`, `guest_name`, `guest_email`, `guest_phone`, `guest_nid`) VALUES
(1, 5, 'RAKIBUL HASAN SHAKIL', 's@gamil.com', '01959995961', '123456789526'),
(2, 6, 'RAKIBUL HASAN SHAKIL', 'shakil@gmail.com', '01959995961', NULL),
(3, 7, 'RAKIBUL HASAN SHAKIL', 'siam@gmail.com', '01959995961', '5614565145654'),
(4, 8, 'RAKIBUL HASAN SHAKIL', 'a@gmail.com', '01959995961', '5614565145654'),
(5, 9, 'RAKIBUL HASAN SHAKIL', 's@gmail.com', '01959995961', '56145648564');

-- --------------------------------------------------------

--
-- Table structure for table `booking_services`
--

CREATE TABLE `booking_services` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL DEFAULT 1,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking_services`
--

INSERT INTO `booking_services` (`id`, `booking_id`, `service_id`, `qty`, `total`, `created_at`) VALUES
(1, 1, 1, 1, 8.00, '2025-10-11 06:09:30'),
(2, 1, 3, 1, 15.00, '2025-10-11 06:09:30'),
(3, 1, 4, 1, 5.00, '2025-10-11 06:09:30'),
(4, 1, 2, 1, 12.00, '2025-10-11 06:09:30'),
(5, 1, 5, 1, 30.00, '2025-10-11 06:09:30'),
(6, 5, 1, 1, 8.00, '2025-10-12 10:55:20'),
(7, 6, 2, 1, 12.00, '2025-10-12 11:59:06'),
(8, 7, 3, 1, 15.00, '2025-10-12 12:00:10'),
(9, 8, 5, 1, 30.00, '2025-10-12 12:05:12'),
(10, 9, 3, 1, 15.00, '2025-10-12 12:36:02'),
(11, 9, 5, 1, 30.00, '2025-10-12 12:36:02');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `method` varchar(100) DEFAULT 'cash',
  `paid_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','paid') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `booking_id`, `amount`, `method`, `paid_at`, `status`, `created_at`) VALUES
(1, 4, 2500.00, 'cash', '2025-10-11 06:59:51', 'paid', '2025-10-11 06:59:51'),
(2, 4, 2500.00, 'cash', '2025-10-11 06:59:57', 'paid', '2025-10-11 06:59:57'),
(3, 4, 2500.00, 'cash', '2025-10-11 07:00:01', 'paid', '2025-10-11 07:00:01'),
(4, 4, 2500.00, 'cash', '2025-10-11 07:00:05', 'paid', '2025-10-11 07:00:05'),
(5, 4, 150.00, 'cash', '2025-10-12 10:09:16', 'paid', '2025-10-12 10:09:16');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `room_no` varchar(50) NOT NULL,
  `type` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `image` varchar(255) DEFAULT NULL,
  `status` enum('available','occupied','maintenance','reserved') DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `room_no`, `type`, `description`, `price`, `image`, `status`, `created_at`) VALUES
(1, '101', 'Single', 'Single bed room with AC', 50.00, 'images/room1.jpg', 'available', '2025-10-11 06:05:41'),
(2, '102', 'Double', 'Double bed room with TV', 80.00, 'images/room2.png', 'available', '2025-10-11 06:05:41'),
(3, '103', 'Suite', 'A Premimu room with TV $AC', 150.00, 'images/room3.jpg', 'available', '2025-10-11 06:05:41'),
(5, '501', 'Couple Deluxe', 'A room with best comfort for couple', 90.00, 'uploads/room_1760167234.jpg', 'available', '2025-10-11 07:20:34'),
(9, '105', 'Single', 'Single bed room with AC', 55.00, 'uploads/room_1760168101.jpg', 'available', '2025-10-11 07:33:18'),
(10, '502', 'Couple Deluxe', 'A room with best comfort for couple', 95.00, 'uploads/room_1760168147.jpg', 'available', '2025-10-11 07:35:47');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `name`, `price`) VALUES
(1, 'Breakfast', 8.00),
(2, 'Lunch', 12.00),
(3, 'Dinner', 15.00),
(4, 'Laundry', 5.00),
(5, 'Spa', 30.00);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `nid_passport` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `usertype` enum('admin','receptionist','guest') NOT NULL DEFAULT 'guest',
  `status` enum('active','pending','blocked') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `email`, `phone`, `nid_passport`, `password`, `usertype`, `status`, `created_at`, `reset_token`, `reset_expires`) VALUES
(1, 'System Admin', 'admin@hotel.com', '+880000000000', 'ADMIN-NID', '$2y$10$sP6rjxUBESEAIOr/4QmdiOzSnCyKTcy504KAyf3VAedj.rFo9UMeO', 'admin', 'active', '2025-10-11 05:03:25', NULL, NULL),
(2, 'RAKIBUL HASAN SHAKIL', 'hasanshakilr@gmail.com', '01959995961', '12345678912', '$2y$10$oQPohuNfCk8VjNpBTh/xNuwtc2PVhkBzvX5bBBfkwMETpL.5bTELe', 'guest', 'active', '2025-10-11 05:20:18', NULL, NULL),
(4, 'sumaiya', 'sumai@gmail.com', '01406524560', '123456789777', '$2y$10$nenDIWOg8Wm4VaIY4iDQI.zQJqtbKbioeCvbbOb1M2U34WDT07fMe', 'receptionist', 'active', '2025-10-11 06:44:35', NULL, NULL),
(5, 'siam', 'siam@gmail.com', '017234986', '123456789', '$2y$10$Q.tlWG/CwR5jkqWYbG5rVuk7O1GQRIYBYEq6NvRblmB3X6E9pYqu6', 'receptionist', 'active', '2025-10-11 09:18:02', NULL, NULL),
(6, 'sajib', 'sajib@gmail.com', '01575134112', '12345678963', '$2y$10$vJyyuZv9LSCpEUcamMXJ2uKsR7HlGDhQcF28SwAZsdDOSDaJMr8wC', 'guest', 'active', '2025-10-12 10:51:06', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `booking_guests`
--
ALTER TABLE `booking_guests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `booking_services`
--
ALTER TABLE `booking_services`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `booking_id` (`booking_id`,`service_id`),
  ADD KEY `service_id` (`service_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `room_no` (`room_no`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

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
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `booking_guests`
--
ALTER TABLE `booking_guests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `booking_services`
--
ALTER TABLE `booking_services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `booking_guests`
--
ALTER TABLE `booking_guests`
  ADD CONSTRAINT `booking_guests_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `booking_services`
--
ALTER TABLE `booking_services`
  ADD CONSTRAINT `booking_services_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `booking_services_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
