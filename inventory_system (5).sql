-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 17, 2025 at 12:06 AM
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
-- Database: `inventory_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`, `description`, `company_id`) VALUES
(23, 'Electronics', NULL, NULL),
(24, 'Appliances', NULL, NULL),
(25, 'Clothing & Apparel', NULL, NULL),
(26, 'Food & Beverages', NULL, NULL),
(27, 'Office Supplies', NULL, NULL),
(28, 'Tools & Hardware', NULL, NULL),
(29, 'Furniture', NULL, NULL),
(30, 'Health & Personal Care', NULL, NULL),
(31, 'Automotive', NULL, NULL),
(32, 'Stationery', NULL, NULL),
(33, 'Household Items', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `company_id` int(11) NOT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone_number` varchar(15) DEFAULT NULL,
  `is_approved` tinyint(1) DEFAULT 0,
  `approved_by` int(11) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`company_id`, `company_name`, `address`, `phone_number`, `is_approved`, `approved_by`, `status`) VALUES
(2, '', NULL, NULL, 0, NULL, -1),
(9, 'cenrep', NULL, NULL, 0, NULL, 1),
(10, 'ttnow', NULL, NULL, 0, NULL, 1),
(11, 'test1', NULL, NULL, 0, NULL, 1),
(16, 'botty', NULL, NULL, 0, NULL, 1),
(17, 'fentyb', NULL, NULL, 0, NULL, 0),
(18, 'aserty', NULL, NULL, 0, NULL, 0),
(19, 'bb', NULL, NULL, 0, NULL, 0),
(20, 'poppy', NULL, NULL, 0, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `email`, `token`, `expires_at`, `created_at`) VALUES
(1, 'john@yahoo.com', '04788ab67df563f3dc0d2d1e9acd89bf2935dcbaf4168f6bf629f20e9a305844', '2025-05-06 22:15:23', '2025-05-06 19:15:23'),
(2, 'superadmin@example.com', '28ac39e4fed8f172bb1b2af9fc94f0264b36f6b95715cadf8c2e121a30e67e18', '2025-05-06 22:17:43', '2025-05-06 19:17:43');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `barcode` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `company_id` int(11) DEFAULT NULL,
  `stock` int(11) DEFAULT 0,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `name`, `description`, `price`, `quantity`, `category_id`, `barcode`, `created_at`, `updated_at`, `company_id`, `stock`, `image`) VALUES
(4, 'ccc', 'ggf', 45.00, 0, 24, '6807af559ae9d', '2025-04-22 15:01:41', '2025-05-07 16:44:00', 9, 4, ''),
(5, 'ww', 'rrr', 34.00, 0, 26, '6807aff1237d7', '2025-04-22 15:04:17', '2025-05-07 16:44:00', 9, 5, ''),
(6, 'wer', 'jjj', 66.00, 0, 28, '6807b2c27b175', '2025-04-22 15:16:18', '2025-05-07 16:44:00', 9, 8, ''),
(7, 'gg', 'hhh', 678.00, 0, 25, '6807b6216ede7', '2025-04-22 15:30:41', '2025-05-07 16:44:00', 9, 88, ''),
(8, 'john', NULL, 2222.00, 0, 29, 'ww22', '2025-04-24 16:27:06', '2025-05-07 16:44:00', 9, 0, 'uploads/screenshot.PNG'),
(10, 'john', NULL, 2222.00, 0, 29, 'ww228', '2025-04-24 16:27:21', '2025-05-07 16:44:00', 9, 0, 'uploads/screenshot.PNG'),
(12, 'john', NULL, 2222.00, 0, 29, 'ww2289', '2025-04-24 16:31:35', '2025-05-08 18:17:59', 9, -14, 'uploads/screenshot.PNG'),
(14, 'john', NULL, 2222.00, 0, 29, 'ww22899', '2025-04-24 16:32:02', '2025-05-07 16:44:00', 9, 0, 'uploads/screenshot.PNG'),
(15, 'aaa', NULL, 567.00, 0, 23, '33355f', '2025-04-24 16:32:29', '2025-05-08 18:04:19', 9, -6, 'uploads/screenshot1.PNG'),
(17, 'John', NULL, 890.00, 0, 23, '1223', '2025-04-24 16:33:35', '2025-05-07 16:44:00', 9, 0, 'uploads/screenshot.PNG'),
(18, 'bag', NULL, 20.00, 0, 25, '1234', '2025-05-09 09:25:05', '2025-05-09 09:25:05', 10, 0, 'uploads/Moira_Black_WhenWorn.webp'),
(19, 'fan', NULL, 50.00, 0, 23, '4321', '2025-05-09 09:55:51', '2025-05-09 09:55:51', 10, 0, 'uploads/fan.jpeg'),
(20, 'cup', NULL, 44.00, 0, 33, '678', '2025-05-09 11:22:24', '2025-05-09 11:22:24', 10, 0, ''),
(21, 'water', NULL, 10.00, 0, 26, '466', '2025-05-09 11:34:17', '2025-05-09 11:34:17', 10, 0, ''),
(22, 'spoon', NULL, 9.00, 0, 33, '890', '2025-05-09 11:35:44', '2025-05-09 11:35:44', 10, 0, ''),
(23, 'mirror', NULL, 900.00, 0, 33, '555', '2025-05-09 11:39:18', '2025-05-09 11:39:18', 10, 0, ''),
(24, 'broom', NULL, 300.00, 190, 33, 'b1broom', '2025-05-16 16:58:02', '2025-05-16 21:01:27', 16, 25, 'uploads/broom.webp'),
(25, 'tv', NULL, 6000.00, 0, 23, 'b1tv', '2025-05-16 20:07:22', '2025-05-16 20:24:55', 16, 100, 'uploads/tv.webp'),
(26, 'fan', NULL, 2200.00, 0, 23, 'b1fan', '2025-05-16 20:54:10', '2025-05-16 21:16:24', 16, 100, 'uploads/fan.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`) VALUES
(1, 'Admin'),
(2, 'Seller'),
(3, 'Supplier'),
(4, 'Admin 2'),
(5, 'Super Admin');

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `sale_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `sale_date` datetime DEFAULT current_timestamp(),
  `company_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `processed_by_email` varchar(255) DEFAULT NULL,
  `discount` decimal(10,2) DEFAULT 0.00,
  `total_amount` decimal(10,2) GENERATED ALWAYS AS ((`price` - `discount`) * `quantity`) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`sale_id`, `product_id`, `quantity`, `price`, `sale_date`, `company_id`, `user_id`, `processed_by_email`, `discount`) VALUES
(1, 12, 4, 2222.00, '2025-05-08 20:43:21', 10, 68, NULL, 0.00),
(2, 15, 6, 567.00, '2025-05-08 21:04:19', 10, 68, NULL, 0.00),
(3, 12, 8, 2222.00, '2025-05-08 21:08:01', 10, 68, NULL, 0.00),
(4, 12, 2, 2222.00, '2025-05-08 21:17:59', 10, 68, NULL, 0.00),
(5, 24, 1, 250.00, '2025-05-16 19:59:23', 16, 73, NULL, 0.00),
(6, 24, 1, 250.00, '2025-05-16 20:01:53', 16, 73, NULL, 0.00),
(7, 24, 1, 250.00, '2025-05-16 20:51:53', 16, 73, 'cashbot1@yahoo.com', 0.00),
(8, 24, 2, 250.00, '2025-05-16 21:17:50', 16, 73, 'cashbot1@yahoo.com', 0.00),
(9, 25, 1, 5500.00, '2025-05-16 23:18:56', 16, 73, 'cashbot1@yahoo.com', 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `stock_thresholds`
--

CREATE TABLE `stock_thresholds` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `threshold` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `supplier_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`supplier_id`, `user_id`, `name`, `email`, `phone`, `address`, `city`) VALUES
(3, 45, 'poppy kuh', 'john89@yahoo.com', 'kkk', 'kkk', 'kkk'),
(4, 47, 'sup test', 'suptest@yahoo.com', '1234', 'ff', 'vvvv'),
(5, 62, 'sup1 sup1', 'sup1@yahoo.com', 'sss', 'sss', 'sss'),
(6, 63, 'sup2 sup2', 'sup2@yahoo.com', '1223', 'ff', 'ff'),
(7, 72, 'sup  bot1', 'supbot1@bing.com', '1234', 'Nusmat', 'Girne');

-- --------------------------------------------------------

--
-- Table structure for table `supplier_company`
--

CREATE TABLE `supplier_company` (
  `id` int(11) NOT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supplier_company`
--

INSERT INTO `supplier_company` (`id`, `supplier_id`, `company_id`) VALUES
(1, 72, 16);

-- --------------------------------------------------------

--
-- Table structure for table `supplier_products`
--

CREATE TABLE `supplier_products` (
  `id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `supply_price` decimal(10,2) DEFAULT NULL,
  `quantity_supplied` int(11) DEFAULT 0,
  `is_approved` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supplier_products`
--

INSERT INTO `supplier_products` (`id`, `supplier_id`, `product_id`, `company_id`, `supply_price`, `quantity_supplied`, `is_approved`) VALUES
(21, 62, 20, 10, NULL, 0, 1),
(23, 62, 22, 10, NULL, 0, 0),
(24, 62, 23, 10, NULL, 0, 0),
(25, 72, 24, 16, 300.00, 200, 1),
(26, 72, 25, 16, 6000.00, 31, 1),
(27, 72, 26, 16, 2200.00, 70, 1);

-- --------------------------------------------------------

--
-- Table structure for table `supply_requests`
--

CREATE TABLE `supply_requests` (
  `request_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `quantity_requested` int(11) NOT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `request_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `supply_price` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supply_requests`
--

INSERT INTO `supply_requests` (`request_id`, `product_id`, `company_id`, `admin_id`, `supplier_id`, `quantity_requested`, `status`, `request_date`, `supply_price`) VALUES
(1, 19, 10, 50, 62, 90, 'Pending', '2025-05-09 10:28:47', 0.00),
(2, 18, 10, 50, 62, 23, 'Pending', '2025-05-09 10:30:28', 0.00),
(3, 19, 10, 50, 62, 99, 'Pending', '2025-05-09 10:31:41', 0.00),
(4, 19, 10, 50, 62, 10, 'Pending', '2025-05-09 10:37:47', 0.00),
(5, 19, 10, 50, 62, 45, 'Pending', '2025-05-09 11:07:25', 0.00),
(6, 19, 10, 50, 62, 12, 'Pending', '2025-05-09 11:08:45', 0.00),
(7, 19, 10, 50, 62, 12, 'Pending', '2025-05-09 11:09:09', 0.00),
(8, 19, 10, 50, 62, 12, 'Pending', '2025-05-09 11:14:14', 0.00),
(9, 18, 10, 50, 62, 12, 'Pending', '2025-05-09 11:17:33', 0.00),
(10, 18, 10, 50, 62, 12, 'Pending', '2025-05-09 11:21:05', 0.00),
(11, 24, 16, 69, 72, 90, 'Approved', '2025-05-16 19:04:51', 300.00),
(12, 24, 16, 69, 72, 100, 'Approved', '2025-05-16 19:47:57', 300.00),
(13, 25, 16, 69, 72, 31, 'Approved', '2025-05-16 20:24:25', 6000.00),
(14, 26, 16, 69, 72, 40, 'Approved', '2025-05-16 20:54:58', 1500.00),
(15, 24, 16, 69, 72, 10, 'Approved', '2025-05-16 21:01:03', 300.00),
(16, 26, 16, 69, 72, 30, 'Approved', '2025-05-16 21:15:07', 2200.00);

-- --------------------------------------------------------

--
-- Table structure for table `supply_request_history`
--

CREATE TABLE `supply_request_history` (
  `history_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `old_quantity` int(11) DEFAULT NULL,
  `new_quantity` int(11) DEFAULT NULL,
  `old_price` decimal(10,2) DEFAULT NULL,
  `new_price` decimal(10,2) DEFAULT NULL,
  `status` enum('approved','rejected') NOT NULL,
  `request_time` datetime DEFAULT current_timestamp(),
  `rejection_reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supply_request_history`
--

INSERT INTO `supply_request_history` (`history_id`, `company_id`, `product_id`, `supplier_id`, `admin_id`, `old_quantity`, `new_quantity`, `old_price`, `new_price`, `status`, `request_time`, `rejection_reason`) VALUES
(2, 16, 24, 72, 69, 15, 25, 250.00, 300.00, 'approved', '2025-05-17 00:01:27', NULL),
(3, 16, 26, 72, 69, 70, 100, 1500.00, 2200.00, 'approved', '2025-05-17 00:16:24', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `transaction_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `company_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `total_cost` decimal(10,2) NOT NULL,
  `transaction_type` enum('Purchase','Sale') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `is_approved` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `temporary_password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `email`, `password_hash`, `role_id`, `is_approved`, `created_at`, `updated_at`, `first_name`, `last_name`, `company_id`, `phone`, `address`, `city`, `temporary_password`) VALUES
(16, 'superadmin@example.com', '$2y$10$XkuBk6ZruJ0CNbK3mnib4.lVgRZ0eJ.Esd/zQvOp0d89vDzrQYlL6', 5, 0, '2025-04-22 12:27:37', '2025-04-22 12:27:37', '', '', NULL, NULL, NULL, NULL, NULL),
(17, 'rexyc@yahoo.com', '$2y$10$wm4lsh5lnNynPkylMg.r7uWL7EjT76zD2YrnTvVmk5vQ2zIHNPika', 1, 0, '2025-04-22 12:48:42', '2025-04-22 12:48:42', 'rexy', 'checkss', 9, NULL, NULL, NULL, NULL),
(45, 'john89@yahoo.com', '$2y$10$HQoZH5O4N1WwltMNrcRDY..Dt9c6CW.iL7ZARUG.Fob2JMfm.Ua9i', 3, 0, '2025-04-24 07:02:34', '2025-04-24 07:04:35', 'poppy', 'kuh', 9, NULL, NULL, NULL, NULL),
(46, 'mikey15@yahoo.com', '$2y$10$yZv0HysJ3SlrsGXDu/tUDeSslhAapAIXPuxV9qjPP5WiQMOzKqxhi', 2, 0, '2025-04-24 07:06:57', '2025-04-24 07:06:57', 'qwert', 'asf', 9, NULL, NULL, NULL, NULL),
(47, 'suptest@yahoo.com', '$2y$10$ht0HNjGEEyU9zSFKunDbZ.4YFsy.A9LRseVHmaq.vKJuixbyjpt6i', 3, 0, '2025-05-05 08:52:35', '2025-05-05 08:52:35', 'sup', 'test', 9, NULL, NULL, NULL, NULL),
(50, 'testnow@gmail.com', '$2y$10$PQ3WvdLM5TMsVQDaItUh1uxwMNJaFnXvllM5rtDSyIWf5Uge1REI6', 1, 0, '2025-05-06 18:23:22', '2025-05-06 18:23:22', 'test ', 'now', 10, NULL, NULL, NULL, NULL),
(51, 'newadmin@example.com', '$2y$10$HR3Pjluq.5ZPtoyK33CQ5uk4S14xDhtyZOIKBvNfakS/wEzG/swpa', 5, 0, '2025-05-06 18:45:14', '2025-05-06 18:45:14', '', '', NULL, NULL, NULL, NULL, NULL),
(60, 'newadmin1@example.com', '$2y$10$.rkSdmTUzb/5I.pFzsnGg.Z/MHDb/smin/KELrlf8hbxl5w3Fl7Si', 5, 0, '2025-05-07 15:41:53', '2025-05-07 15:41:53', '', '', NULL, NULL, NULL, NULL, NULL),
(61, 'test1@gmail.com', '$2y$10$hL2Ne4DMvmxXySTwXCTDwu9Dl7g3993cjLD.PanTTBnVXFf/XN4ti', 1, 0, '2025-05-07 16:52:08', '2025-05-07 16:52:08', 'test1', 'checking', 11, NULL, NULL, NULL, NULL),
(62, 'sup1@yahoo.com', '$2y$10$U8s8l8Y/MD1bZ0SSfvkfMOwiQJE2yYL5P/56nkfR6m7UORHdsT/B2', 3, 0, '2025-05-07 16:57:00', '2025-05-09 09:27:14', 'sup1', 'sup1', 10, NULL, NULL, NULL, NULL),
(63, 'sup2@yahoo.com', '$2y$10$uKY7LmkarT/akK58LLXof.Cl8O.IMH3r3VlmDyD0To5xNmui8qB.S', 3, 0, '2025-05-07 17:16:14', '2025-05-07 17:16:14', 'sup2', 'sup2', 10, NULL, NULL, NULL, NULL),
(68, 'cash1@yahoo.com', '$2y$10$YdG9Tfq0M4rHgUuG2yq4j.fXfTvWg/t7CpOZzzAL7BO7ZUo7GsSCq', 2, 0, '2025-05-08 17:18:42', '2025-05-08 19:00:01', 'cash', 'cash1', 10, NULL, NULL, NULL, NULL),
(69, 'botacct1@yahoo.com', '$2y$10$B2A4gEhHtrNygfabRKwER.8n0339NxtqcTO2PAbDMD7qW/Ex3nVtu', 1, 0, '2025-05-16 13:17:19', '2025-05-16 13:17:19', 'bot', 'acct1', 16, NULL, NULL, NULL, NULL),
(70, 'hey89@yahoo.com', '$2y$10$WpL9ILsHwxa9TKapmze/5.nU4rz2z/kRgEp4pZbBKT2lqPDXwQ.yS', 1, 0, '2025-05-16 13:22:24', '2025-05-16 13:22:24', 'aaaa', 'yuiop', 17, NULL, NULL, NULL, NULL),
(71, 'hey11@yahoo.com', '$2y$10$UHuxP4K3rVu5OhxfPMzNP.nFSMjChpjcj8kUmhMeKTVWdyX1LtJFe', 1, 0, '2025-05-16 13:25:55', '2025-05-16 13:25:55', 'as', 'qw', 18, NULL, NULL, NULL, NULL),
(72, 'supbot1@bing.com', '$2y$10$OXApG00LYT0iL9SFjEAKVO00C2gnu78.uGB.j2bVraleJydwrYyC.', 3, 0, '2025-05-16 13:28:58', '2025-05-16 13:29:48', 'sup ', 'bot1', 16, NULL, NULL, NULL, NULL),
(73, 'cashbot1@yahoo.com', '$2y$10$3F2zVTuUUn28rHShoE6K1O5b0DlqTyCbhPDIFLkqTbfm.7ro3VLpm', 2, 0, '2025-05-16 13:31:00', '2025-05-16 13:31:36', 'cash', 'bot1', 16, NULL, NULL, NULL, NULL),
(74, 'books8@yahoo.com', '$2y$10$KgoYg210yvciyvrVOumwOOzPOUADYiuKY/PTfnAoTvlPmxOBG84Xi', 1, 0, '2025-05-16 13:33:50', '2025-05-16 13:33:50', 'bb', 'bb', 19, NULL, NULL, NULL, NULL),
(77, 'books9@yahoo.com', '$2y$10$c6Bz0bNL14aN1YDc6bKklubTa7F/fJlDyi91XptrugaC89QXpNMDC', 1, 0, '2025-05-16 13:34:06', '2025-05-16 13:34:06', 'bb', 'bb', 19, NULL, NULL, NULL, NULL),
(81, 'books89@yahoo.com', '$2y$10$pHS7FD9QxErd4kYf0AQ6IOuchI1E/XM9ZkHXGt39jL2Fq.TGpf30W', 1, 0, '2025-05-16 13:34:57', '2025-05-16 13:34:57', 'bb', 'bb', 19, NULL, NULL, NULL, NULL),
(83, 'books891@yahoo.com', '$2y$10$SQ8rhHZcru7Vf2zRgqDUDuy9ndl9kXuzcIammmowMvJupfHwFS/cG', 1, 0, '2025-05-16 13:35:26', '2025-05-16 13:35:26', 'bb', 'bb', 19, NULL, NULL, NULL, NULL),
(84, 'kpoppy@yahoo.com', '$2y$10$h.DUf67GLCw0d.gbBN6eLeaWwabk/UWKO9kMxVf2SlQyJymBc1UFK', 1, 0, '2025-05-16 13:41:32', '2025-05-16 13:41:32', 'kkop', 'pop', 20, NULL, NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD KEY `fk_company_id` (`company_id`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`company_id`),
  ADD KEY `approved_by` (`approved_by`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD UNIQUE KEY `barcode` (`barcode`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `fk_products_companies` (`company_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`sale_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `fk_sale_company` (`company_id`),
  ADD KEY `fk_sale_product` (`product_id`);

--
-- Indexes for table `stock_thresholds`
--
ALTER TABLE `stock_thresholds`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`supplier_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `supplier_company`
--
ALTER TABLE `supplier_company`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `supplier_id` (`supplier_id`,`company_id`);

--
-- Indexes for table `supplier_products`
--
ALTER TABLE `supplier_products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indexes for table `supply_requests`
--
ALTER TABLE `supply_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `supply_request_history`
--
ALTER TABLE `supply_request_history`
  ADD PRIMARY KEY (`history_id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `fk_supplier_user_id` (`supplier_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `fk_transactions_company` (`company_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_users_companies` (`company_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `company_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `sale_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `stock_thresholds`
--
ALTER TABLE `stock_thresholds`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `supplier_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `supplier_company`
--
ALTER TABLE `supplier_company`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `supplier_products`
--
ALTER TABLE `supplier_products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `supply_requests`
--
ALTER TABLE `supply_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `supply_request_history`
--
ALTER TABLE `supply_request_history`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `fk_company_id` FOREIGN KEY (`company_id`) REFERENCES `companies` (`company_id`) ON DELETE SET NULL;

--
-- Constraints for table `companies`
--
ALTER TABLE `companies`
  ADD CONSTRAINT `companies_ibfk_1` FOREIGN KEY (`approved_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_companies` FOREIGN KEY (`company_id`) REFERENCES `companies` (`company_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`);

--
-- Constraints for table `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `fk_sale_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`company_id`),
  ADD CONSTRAINT `fk_sale_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
  ADD CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
  ADD CONSTRAINT `sales_ibfk_2` FOREIGN KEY (`company_id`) REFERENCES `companies` (`company_id`),
  ADD CONSTRAINT `sales_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `stock_thresholds`
--
ALTER TABLE `stock_thresholds`
  ADD CONSTRAINT `stock_thresholds_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
  ADD CONSTRAINT `stock_thresholds_ibfk_2` FOREIGN KEY (`company_id`) REFERENCES `companies` (`company_id`);

--
-- Constraints for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD CONSTRAINT `suppliers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `supplier_products`
--
ALTER TABLE `supplier_products`
  ADD CONSTRAINT `supplier_products_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `supplier_products_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
  ADD CONSTRAINT `supplier_products_ibfk_3` FOREIGN KEY (`company_id`) REFERENCES `companies` (`company_id`);

--
-- Constraints for table `supply_requests`
--
ALTER TABLE `supply_requests`
  ADD CONSTRAINT `supply_requests_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
  ADD CONSTRAINT `supply_requests_ibfk_2` FOREIGN KEY (`company_id`) REFERENCES `companies` (`company_id`),
  ADD CONSTRAINT `supply_requests_ibfk_3` FOREIGN KEY (`admin_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `supply_requests_ibfk_4` FOREIGN KEY (`supplier_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `supply_request_history`
--
ALTER TABLE `supply_request_history`
  ADD CONSTRAINT `fk_supplier_user_id` FOREIGN KEY (`supplier_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `supply_request_history_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`company_id`),
  ADD CONSTRAINT `supply_request_history_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
  ADD CONSTRAINT `supply_request_history_ibfk_4` FOREIGN KEY (`admin_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `fk_transactions_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`company_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_companies` FOREIGN KEY (`company_id`) REFERENCES `companies` (`company_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`company_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
