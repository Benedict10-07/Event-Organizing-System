-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 29, 2025 at 12:56 PM
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
-- Database: `client_eos`
--

-- --------------------------------------------------------

--
-- Table structure for table `booking_services`
--

CREATE TABLE `booking_services` (
  `id` int(11) NOT NULL,
  `booking_id` varchar(20) DEFAULT NULL,
  `service_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking_services`
--

INSERT INTO `booking_services` (`id`, `booking_id`, `service_id`) VALUES
(1, 'BK-695136F0A0384', 92),
(2, 'BK-695136F0A0384', 91),
(3, 'BK-695136F0A0384', 93),
(4, 'BK-695136F0A0384', 90),
(98, 'BK-69526B9306419', 90),
(99, 'BK-69526B9306419', 97),
(100, 'BK-69526B9306419', 10),
(101, 'BK-69526B9306419', 73),
(102, 'BK-69526B9306419', 72),
(103, 'BK-69526B9306419', 3),
(104, 'BK-69526B9306419', 1),
(105, 'BK-69526B9306419', 2),
(106, 'BK-69526B9306419', 32),
(107, 'BK-69526B9306419', 37),
(108, 'BK-69526B6EC97E3', 90),
(109, 'BK-69526B6EC97E3', 73),
(110, 'BK-69526B6EC97E3', 72),
(111, 'BK-69526B6EC97E3', 1),
(112, 'BK-69526B6EC97E3', 112),
(113, 'BK-69526B6EC97E3', 50),
(114, 'BK-69526BF29D2E2', 12),
(115, 'BK-69526BF29D2E2', 22),
(116, 'BK-69526BF29D2E2', 20);

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `client_id` varchar(20) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `full_name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`client_id`, `user_id`, `full_name`) VALUES
('CL-6e80bf1c', 2, 'Althea Nacion');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `event_id` varchar(20) NOT NULL,
  `client_id` varchar(20) DEFAULT NULL,
  `event_type` varchar(50) DEFAULT NULL,
  `event_date` date DEFAULT NULL,
  `venue_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`event_id`, `client_id`, `event_type`, `event_date`, `venue_name`) VALUES
('EV-695136F0A0387', 'CL-6e80bf1c', 'Birthday', '2025-12-28', 'My Venue'),
('EV-6951380FC3DD1', 'CL-6e80bf1c', 'Birthday', '2025-12-28', 'My Venue'),
('EV-6951487D80329', 'CL-6e80bf1c', 'Wedding', '2025-12-28', 'My Venue'),
('EV-6951D36F18BDE', 'CL-6e80bf1c', 'Birthday', '2025-12-29', 'My Venue'),
('EV-6951D6998FD58', 'CL-6e80bf1c', 'Corporate', '2025-12-29', 'My Venue'),
('EV-6951D77AAF0EC', 'CL-6e80bf1c', 'Birthday', '2025-12-29', 'My Venue'),
('EV-6951DF5110B02', 'CL-6e80bf1c', 'Birthday', '2025-12-29', 'My Venue'),
('EV-6951EBD89647E', 'CL-6e80bf1c', 'Anniversary', '2025-12-29', 'My Venue'),
('EV-6951F08E74B23', 'CL-6e80bf1c', 'Birthday', '2025-12-29', 'My Venue'),
('EV-6951F15ADC460', 'CL-6e80bf1c', 'Debut', '2025-12-26', 'My Venue'),
('EV-6951FEC7C8B46', 'CL-6e80bf1c', 'Birthday', '2025-12-29', 'My Venue'),
('EV-695200AA534C1', 'CL-6e80bf1c', 'Wedding', '2025-12-29', 'My Venue'),
('EV-6952010B868C6', 'CL-6e80bf1c', 'Wedding', '2025-12-29', 'My Venue'),
('EV-6952676B9D13B', 'CL-6e80bf1c', 'Birthday', '2025-12-29', 'My Venue'),
('EV-69526AC6D7172', 'CL-6e80bf1c', 'Birthday', '2025-12-29', 'My Venue'),
('EV-69526B6EC97E6', 'CL-6e80bf1c', 'Corporate', '2025-12-29', 'My Venue'),
('EV-69526B930641C', 'CL-6e80bf1c', 'Anniversary', '2025-12-29', 'My Venue'),
('EV-69526BF29D2E6', 'CL-6e80bf1c', 'Wedding', '2025-12-29', 'My Venue');

-- --------------------------------------------------------

--
-- Table structure for table `event_bookings`
--

CREATE TABLE `event_bookings` (
  `booking_id` varchar(20) NOT NULL,
  `event_id` varchar(20) DEFAULT NULL,
  `package_id` int(11) DEFAULT NULL,
  `agreed_budget` decimal(10,2) DEFAULT NULL,
  `booking_status` enum('Pending','Confirmed','Ongoing','Completed','Cancelled') DEFAULT 'Pending',
  `cancelled_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_bookings`
--

INSERT INTO `event_bookings` (`booking_id`, `event_id`, `package_id`, `agreed_budget`, `booking_status`, `cancelled_at`) VALUES
('BK-695136F0A0384', 'EV-695136F0A0387', 1, 70000.00, 'Completed', NULL),
('BK-69526B6EC97E3', 'EV-69526B6EC97E6', 4, 65000.00, 'Ongoing', NULL),
('BK-69526B9306419', 'EV-69526B930641C', 3, 290500.00, 'Confirmed', NULL),
('BK-69526BF29D2E2', 'EV-69526BF29D2E6', 2, 153500.00, 'Cancelled', '2025-12-29 19:54:40');

-- --------------------------------------------------------

--
-- Table structure for table `feedbacks`
--

CREATE TABLE `feedbacks` (
  `feedback_id` int(11) NOT NULL,
  `booking_id` varchar(20) NOT NULL,
  `client_id` varchar(20) NOT NULL,
  `client_name` varchar(100) NOT NULL,
  `rating` int(1) NOT NULL,
  `comment` text DEFAULT NULL,
  `date_posted` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedbacks`
--

INSERT INTO `feedbacks` (`feedback_id`, `booking_id`, `client_id`, `client_name`, `rating`, `comment`, `date_posted`) VALUES
(1, 'BK-EOS-001', 'CL-001', 'Anessa Malabago', 5, 'Absolutely magical! The Silver Package was more than enough. Thank you EOS!', '2025-12-28 10:00:00'),
(2, 'BK-EOS-002', 'CL-002', 'Christan Biojan', 5, 'Very professional team. The coordination was flawless from start to finish.', '2025-12-27 14:30:00'),
(3, 'BK-EOS-003', 'CL-003', 'Eva Jane Dolar', 5, 'My debut was a dream come true! The styling was exactly what I imagined.', '2025-12-26 09:15:00'),
(4, 'BK-EOS-004', 'CL-004', 'Kim Hallara', 4, 'Great food and setup. Highly recommended for corporate events.', '2025-12-25 11:00:00'),
(5, 'BK-EOS-005', 'CL-005', 'Mariene Labrador', 5, 'Stress-free wedding planning. Best decision ever!', '2025-12-24 16:45:00'),
(6, 'BK-EOS-006', 'CL-006', 'Carla Andura', 5, 'Top-notch service! Will definitely book again for our next reunion.', '2025-12-23 20:00:00'),
(7, 'BK-EOS-007', 'CL-007', 'Kate Nicole Bermejo', 4, 'Solid experience. The venue looked amazing and the staff were kind.', '2025-12-22 08:30:00'),
(8, 'BK-EOS-008', 'CL-008', 'Vhan Jhun Gimarangan', 3, 'Good service but the sound system had minor issues during the speech.', '2025-12-20 13:00:00'),
(9, 'BK-EOS-009', 'CL-009', 'Joel Balidio', 5, 'The mobile bar was a hit! Thanks for the smooth transaction.', '2025-12-18 19:20:00');

-- --------------------------------------------------------

--
-- Table structure for table `packages`
--

CREATE TABLE `packages` (
  `package_id` int(11) NOT NULL,
  `package_name` varchar(50) DEFAULT NULL,
  `base_price` decimal(10,2) DEFAULT NULL,
  `status` enum('Available','Unavailable') DEFAULT 'Available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `packages`
--

INSERT INTO `packages` (`package_id`, `package_name`, `base_price`, `status`) VALUES
(1, 'Silver Package', 18000.00, 'Available'),
(2, 'Gold Package', 45000.00, 'Available'),
(3, 'Platinum Package', 90000.00, 'Available'),
(4, 'Custom Builder', 0.00, 'Available');

-- --------------------------------------------------------

--
-- Table structure for table `package_definitions`
--

CREATE TABLE `package_definitions` (
  `id` int(11) NOT NULL,
  `package_id` int(11) DEFAULT NULL,
  `service_id` int(11) DEFAULT NULL,
  `event_type` varchar(50) DEFAULT 'All'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `package_definitions`
--

INSERT INTO `package_definitions` (`id`, `package_id`, `service_id`, `event_type`) VALUES
(1, 1, 1, 'Wedding'),
(2, 1, 50, 'Wedding'),
(3, 1, 20, 'Wedding'),
(4, 1, 33, 'Wedding'),
(5, 2, 2, 'Wedding'),
(6, 2, 51, 'Wedding'),
(7, 2, 21, 'Wedding'),
(8, 2, 34, 'Wedding'),
(9, 2, 130, 'Wedding'),
(10, 3, 3, 'Wedding'),
(11, 3, 72, 'Wedding'),
(12, 3, 32, 'Wedding'),
(13, 3, 131, 'Wedding'),
(14, 3, 22, 'Wedding'),
(15, 1, 90, 'Birthday'),
(16, 1, 50, 'Birthday'),
(17, 1, 93, 'Birthday'),
(18, 2, 2, 'Birthday'),
(19, 2, 93, 'Birthday'),
(20, 2, 13, 'Birthday'),
(21, 2, 70, 'Birthday'),
(22, 3, 3, 'Birthday'),
(23, 3, 53, 'Birthday'),
(24, 3, 94, 'Birthday'),
(25, 3, 71, 'Birthday'),
(26, 1, 1, 'Debut'),
(27, 1, 35, 'Debut'),
(28, 1, 50, 'Debut'),
(29, 2, 2, 'Debut'),
(30, 2, 95, 'Debut'),
(31, 2, 51, 'Debut'),
(32, 2, 70, 'Debut'),
(33, 3, 3, 'Debut'),
(34, 3, 32, 'Debut'),
(35, 3, 72, 'Debut'),
(36, 3, 95, 'Debut'),
(37, 1, 1, 'Corporate'),
(38, 1, 50, 'Corporate'),
(39, 2, 2, 'Corporate'),
(40, 2, 11, 'Corporate'),
(41, 2, 70, 'Corporate'),
(42, 3, 3, 'Corporate'),
(43, 3, 53, 'Corporate'),
(44, 3, 73, 'Corporate'),
(45, 3, 12, 'Corporate'),
(46, 1, 36, 'Funeral'),
(47, 1, 110, 'Funeral'),
(48, 2, 36, 'Funeral'),
(49, 2, 113, 'Funeral'),
(50, 2, 10, 'Funeral'),
(51, 3, 3, 'Funeral'),
(52, 3, 96, 'Funeral'),
(53, 3, 73, 'Funeral'),
(54, 3, 11, 'Funeral'),
(55, 1, 1, 'Anniversary'),
(56, 1, 37, 'Anniversary'),
(57, 1, 50, 'Anniversary'),
(58, 2, 2, 'Anniversary'),
(59, 2, 97, 'Anniversary'),
(60, 2, 11, 'Anniversary'),
(61, 2, 70, 'Anniversary'),
(62, 3, 3, 'Anniversary'),
(63, 3, 72, 'Anniversary'),
(64, 3, 12, 'Anniversary'),
(65, 3, 51, 'Anniversary'),
(66, 1, 1, 'Social'),
(67, 1, 98, 'Social'),
(68, 1, 114, 'Social'),
(69, 2, 2, 'Social'),
(70, 2, 17, 'Social'),
(71, 2, 74, 'Social'),
(72, 2, 51, 'Social'),
(73, 3, 3, 'Social'),
(74, 3, 95, 'Social'),
(75, 3, 16, 'Social'),
(76, 3, 53, 'Social');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `service_id` int(11) NOT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `service_name` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `event_scope` varchar(50) DEFAULT 'All',
  `status` enum('Available','Unavailable') DEFAULT 'Available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`service_id`, `vendor_id`, `category`, `service_name`, `price`, `event_scope`, `status`) VALUES
(1, 1, 'Planning', 'On-the-Day Coordination', 5000.00, 'All', 'Available'),
(2, 1, 'Planning', 'Partial Planning (Last 3 Months)', 15000.00, 'All', 'Available'),
(3, 1, 'Planning', 'Full Event Production & Management', 40000.00, 'All', 'Available'),
(10, 2, 'Food', 'Standard Buffet (1 Meat, 1 Pasta)', 18000.00, 'All', 'Available'),
(11, 2, 'Food', 'Premium Buffet (3 Meats, Seafood)', 35000.00, 'All', 'Available'),
(12, 17, 'Food', '5-Course Plated Fine Dining', 80000.00, 'All', 'Available'),
(13, 2, 'Food', 'Chocolate Fountain Station', 4500.00, 'Birthday', 'Available'),
(14, 2, 'Food', 'Grazing Table (Charcuterie)', 9500.00, 'Social', 'Available'),
(15, 17, 'Food', 'Premium Wine & Cheese Station', 15000.00, 'Corporate', 'Available'),
(16, 2, 'Food', 'Lechon Baka (Roast Beef)', 25000.00, 'Social', 'Available'),
(17, 2, 'Food', 'Lechon Baboy (Whole)', 12000.00, 'Social', 'Available'),
(20, 12, 'Food', 'Minimalist 1-Tier Cake', 3500.00, 'Wedding', 'Available'),
(21, 12, 'Food', 'Classic 3-Tier Fondant Cake', 9000.00, 'Wedding', 'Available'),
(22, 12, 'Food', '5-Tier Luxury Hanging Cake', 25000.00, 'Wedding', 'Available'),
(30, 4, 'Styling', 'Standard Table Centerpieces', 5000.00, 'All', 'Available'),
(31, 4, 'Styling', 'Themed Stage Backdrop & Sofa', 15000.00, 'All', 'Available'),
(32, 16, 'Styling', 'Full Ceiling Floral & Crystal Treatment', 60000.00, 'All', 'Available'),
(33, 4, 'Styling', 'Bridal Car Floral Decor', 2500.00, 'Wedding', 'Available'),
(34, 4, 'Styling', 'Church Aisle Flowers', 12000.00, 'Wedding', 'Available'),
(35, 4, 'Styling', '18 Roses & Candles Kit', 2000.00, 'Debut', 'Available'),
(36, 4, 'Styling', 'Casket Flower Arrangement', 3500.00, 'Funeral', 'Available'),
(37, 4, 'Styling', 'Romantic Table Setup (for 2)', 3500.00, 'Anniversary', 'Available'),
(50, 3, 'Technical', 'Basic PA System (2 Speakers)', 3500.00, 'All', 'Available'),
(51, 3, 'Technical', 'Full Band Setup + Mood Lights', 12000.00, 'All', 'Available'),
(52, 3, 'Technical', 'Concert Rig (Line Array + Trusses)', 45000.00, 'All', 'Available'),
(53, 3, 'Technical', 'LED Wall (9x12ft)', 15000.00, 'All', 'Available'),
(54, 3, 'Technical', 'Low Lying Fog Machine', 3500.00, 'Wedding', 'Available'),
(70, 6, 'Media', 'Photo Coverage Only', 5000.00, 'All', 'Available'),
(71, 6, 'Media', 'Photo + Video Highlights', 18000.00, 'All', 'Available'),
(72, 7, 'Media', 'Photo + Video + SDE + Drone', 45000.00, 'All', 'Available'),
(73, 6, 'Media', 'Livestream Setup (Zoom/FB)', 5000.00, 'All', 'Available'),
(74, 6, 'Media', 'Photobooth (Unlimited Prints)', 4500.00, 'Social', 'Available'),
(90, 7, 'Entertainment', 'Event Host / Emcee', 4000.00, 'All', 'Available'),
(91, 7, 'Entertainment', 'Acoustic Duo / Trio', 8000.00, 'All', 'Unavailable'),
(92, 18, 'Entertainment', '10-Piece Showband / Orchestra', 35000.00, 'All', 'Unavailable'),
(93, 9, 'Entertainment', 'Clown & Magician', 5000.00, 'Birthday', 'Available'),
(94, 9, 'Entertainment', 'Mascot Appearance', 3500.00, 'Birthday', 'Available'),
(95, 7, 'Entertainment', 'Mobile Bar (Unli Cocktails)', 12000.00, 'Social', 'Available'),
(96, 7, 'Entertainment', 'Violinist (Solo)', 4000.00, 'Funeral', 'Available'),
(97, 7, 'Entertainment', 'Saxophonist (Solo)', 5000.00, 'Anniversary', 'Available'),
(98, 7, 'Entertainment', 'Karaoke Machine', 2500.00, 'Social', 'Available'),
(110, 5, 'Rentals', 'Monobloc Chairs (Set of 10)', 200.00, 'All', 'Available'),
(111, 5, 'Rentals', 'Tiffany Chairs (Set of 10)', 1000.00, 'All', 'Available'),
(112, 5, 'Rentals', 'Ghost/Acrylic Chairs (Set of 10)', 2500.00, 'All', 'Available'),
(113, 5, 'Rentals', 'Air-Conditioned Tent (Marquee)', 25000.00, 'All', 'Available'),
(114, 5, 'Rentals', 'Beer Pong Table', 1500.00, 'Social', 'Available'),
(130, 8, 'Logistics', 'Bridal Sedan (White)', 5000.00, 'Wedding', 'Available'),
(131, 14, 'Logistics', 'Vintage Bridal Car', 15000.00, 'Wedding', 'Available'),
(132, 14, 'Logistics', 'Luxury Rolls Royce / Limousine', 40000.00, 'Wedding', 'Available');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contact_number` varchar(20) NOT NULL,
  `role` enum('admin','client') NOT NULL DEFAULT 'client'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `full_name`, `email`, `contact_number`, `role`) VALUES
(1, 'admin', '$2y$10$u1XwL7zZm6Ias9HNin0n0uwqN9.7HVmBC0qkhrUJO1b9AM5vvjKlC', 'System Admin', 'admin@eos.com', '09000000000', 'admin'),
(2, 'theyi', '$2y$10$9Hd7M1Lx06Mn9fMs232REeGvxxTd27DScXHBGZqbcvcMOqOlBynLm', 'Althea Nacion', 'altheanacion@gmail.com', '09215512321', 'client');

-- --------------------------------------------------------

--
-- Table structure for table `vendors`
--

CREATE TABLE `vendors` (
  `vendor_id` int(11) NOT NULL,
  `vendor_name` varchar(100) NOT NULL,
  `category` varchar(50) NOT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vendors`
--

INSERT INTO `vendors` (`vendor_id`, `vendor_name`, `category`, `status`) VALUES
(1, 'EOS Internal', 'Planning', 'Active'),
(2, 'Chef Master', 'Food', 'Active'),
(3, 'SoundWave Pro', 'Technical', 'Active'),
(4, 'Decor Dreams', 'Styling', 'Active'),
(5, 'Party Rent', 'Rentals', 'Active'),
(6, 'Lens Magic', 'Media', 'Active'),
(7, 'Fun Factory', 'Entertainment', 'Active'),
(8, 'Safe Rides', 'Logistics', 'Active'),
(9, 'Kiddie Party Inc', 'Entertainment', 'Active'),
(12, 'Sugar Rush', 'Food', 'Active'),
(14, 'Vintage Rides', 'Logistics', 'Active'),
(16, 'Luxury Events PH', 'Styling', 'Active'),
(17, 'Gourmet Chefs', 'Food', 'Active'),
(18, 'Symphony Inc', 'Entertainment', 'Active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `booking_services`
--
ALTER TABLE `booking_services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bs_bk_fk` (`booking_id`),
  ADD KEY `bs_svc_fk` (`service_id`);

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`client_id`),
  ADD KEY `cl_user_fk` (`user_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`),
  ADD KEY `ev_client_fk` (`client_id`);

--
-- Indexes for table `event_bookings`
--
ALTER TABLE `event_bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `bk_ev_fk` (`event_id`),
  ADD KEY `bk_pkg_fk` (`package_id`);

--
-- Indexes for table `feedbacks`
--
ALTER TABLE `feedbacks`
  ADD PRIMARY KEY (`feedback_id`);

--
-- Indexes for table `packages`
--
ALTER TABLE `packages`
  ADD PRIMARY KEY (`package_id`);

--
-- Indexes for table `package_definitions`
--
ALTER TABLE `package_definitions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pd_pkg_fk` (`package_id`),
  ADD KEY `pd_svc_fk` (`service_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`service_id`),
  ADD KEY `svc_vendor_fk` (`vendor_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `vendors`
--
ALTER TABLE `vendors`
  ADD PRIMARY KEY (`vendor_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `booking_services`
--
ALTER TABLE `booking_services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=117;

--
-- AUTO_INCREMENT for table `feedbacks`
--
ALTER TABLE `feedbacks`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `packages`
--
ALTER TABLE `packages`
  MODIFY `package_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `package_definitions`
--
ALTER TABLE `package_definitions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `service_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=133;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `vendors`
--
ALTER TABLE `vendors`
  MODIFY `vendor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `booking_services`
--
ALTER TABLE `booking_services`
  ADD CONSTRAINT `bs_bk_fk` FOREIGN KEY (`booking_id`) REFERENCES `event_bookings` (`booking_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bs_svc_fk` FOREIGN KEY (`service_id`) REFERENCES `services` (`service_id`);

--
-- Constraints for table `clients`
--
ALTER TABLE `clients`
  ADD CONSTRAINT `cl_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `ev_client_fk` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`) ON DELETE CASCADE;

--
-- Constraints for table `event_bookings`
--
ALTER TABLE `event_bookings`
  ADD CONSTRAINT `bk_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bk_pkg_fk` FOREIGN KEY (`package_id`) REFERENCES `packages` (`package_id`);

--
-- Constraints for table `package_definitions`
--
ALTER TABLE `package_definitions`
  ADD CONSTRAINT `pd_pkg_fk` FOREIGN KEY (`package_id`) REFERENCES `packages` (`package_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pd_svc_fk` FOREIGN KEY (`service_id`) REFERENCES `services` (`service_id`) ON DELETE CASCADE;

--
-- Constraints for table `services`
--
ALTER TABLE `services`
  ADD CONSTRAINT `svc_vendor_fk` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`vendor_id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
