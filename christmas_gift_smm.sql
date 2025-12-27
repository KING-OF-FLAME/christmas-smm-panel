-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: sdb-67.hosting.stackcp.net
-- Generation Time: Dec 27, 2025 at 07:18 AM
-- Server version: 10.6.18-MariaDB-log
-- PHP Version: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `christmas_gift_db-35303437de8c`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(2, 'admin1', '$2y$12$D8xHgwarE1SMnwxiWcLwMOS3RO4wz8RhE77ucgz7KzouVvs2R.w7a'),
(3, 'admin.2026', '$2y$12$YXubOd0bETNxLI6pArG9jei0I3KgVPFBKdNg8PPtP4wZpQFtU8.OG');

-- --------------------------------------------------------

--
-- Table structure for table `checkins`
--

CREATE TABLE `checkins` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `checkin_date` date NOT NULL,
  `coins_earned` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `checkins`
--

INSERT INTO `checkins` (`id`, `user_id`, `checkin_date`, `coins_earned`) VALUES
(1, 619, '2025-12-25', 10),
(2, 643, '2025-12-25', 10),
(3, 612, '2025-12-25', 10),
(4, 16, '2025-12-25', 10),
(5, 648, '2025-12-25', 10),
(6, 651, '2025-12-25', 10),
(7, 27, '2025-12-25', 10),
(8, 658, '2025-12-25', 10),
(9, 659, '2025-12-25', 10),
(10, 651, '2025-12-26', 10),
(11, 660, '2025-12-26', 10),
(12, 661, '2025-12-26', 10),
(13, 27, '2025-12-26', 10),
(14, 660, '2025-12-27', 10);

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE `coupons` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `reward_coins` int(11) DEFAULT 0,
  `max_uses` int(11) DEFAULT 100,
  `used_count` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `coupon_usage`
--

CREATE TABLE `coupon_usage` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `coupon_id` int(11) NOT NULL,
  `used_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `link` varchar(255) NOT NULL,
  `cost` int(11) DEFAULT 0,
  `smm_order_id` varchar(50) DEFAULT NULL,
  `status` enum('pending','completed','error') NOT NULL DEFAULT 'pending',
  `response_log` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `api_order_id` int(11) DEFAULT NULL,
  `remains` int(11) DEFAULT 0,
  `is_manual_approved` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `service_id`, `link`, `cost`, `smm_order_id`, `status`, `response_log`, `created_at`, `api_order_id`, `remains`, `is_manual_approved`) VALUES
(1, 1, 1, 'https://www.instagram.com/p/DKxTn6nI_eQ/', 0, '944004', 'completed', '{\"order\":944004}', '2025-12-23 05:35:13', NULL, 0, 0),
(2, 10, 1, 'https://www.instagram.com/p/DSmPYMwiKo1', 0, '944123', 'completed', '{\"order\":944123}', '2025-12-23 09:30:58', NULL, 0, 0),
(3, 23, 1, 'https://www.instagram.com/reel/DSaF_R_E_A3/?igsh=NTc4MTIwNjQ2YQ==', 0, '944134', 'completed', '{\"order\":944134}', '2025-12-23 09:54:18', NULL, 0, 0),
(4, 18, 3, 'https://www.instagram.com/pr3mog?igsh=NXZvdDg4cG1veW96', 0, '944135', 'completed', '{\"order\":944135}', '2025-12-23 10:00:35', NULL, 0, 0),
(5, 18, 3, 'https://www.instagram.com/pr3mog?igsh=NXZvdDg4cG1veW96', 0, '944153', 'completed', '{\"order\":944153}', '2025-12-23 10:40:33', NULL, 0, 0),
(6, 40, 2, 'https://www.instagram.com/reel/DSjhZBSjNsX/?igsh=MXUyYng2bnN4bDY4NA==', 0, NULL, 'error', '{\"error\":\"Quantity less than minimal 100\"}', '2025-12-23 10:44:07', NULL, 0, 0),
(7, 41, 2, 'https://www.instagram.com/reel/DSjhZBSjNsX/?igsh=MXUyYng2bnN4bDY4NA==', 0, NULL, 'error', '{\"error\":\"Quantity less than minimal 100\"}', '2025-12-23 10:46:09', NULL, 0, 0),
(8, 39, 4, 'https://www.instagram.com/shahid_zx786?igsh=MXZhYW1iYTUzbGk3cg==', 0, '944157', 'completed', '{\"order\":944157}', '2025-12-23 10:47:20', NULL, 0, 0),
(9, 39, 2, 'https://www.instagram.com/p/DSioUTVE4xK/?igsh=bndyemRvcTVmM2pz', 0, NULL, 'error', '{\"error\":\"Quantity less than minimal 100\"}', '2025-12-23 10:51:10', NULL, 0, 0),
(10, 39, 4, 'https://www.instagram.com/shahid_zx786?igsh=MXZhYW1iYTUzbGk3cg==', 0, '944169', 'completed', '{\"order\":944169}', '2025-12-23 10:57:45', NULL, 0, 0),
(11, 39, 4, 'https://www.instagram.com/baby__haniya__?igsh=MXg0NjA0dzRyeHo4bQ==', 0, '944173', 'completed', '{\"order\":944173}', '2025-12-23 11:04:52', NULL, 0, 0),
(12, 39, 4, 'https://www.instagram.com/shahid_zx786?igsh=MXZhYW1iYTUzbGk3cg==', 0, '944178', 'completed', '{\"order\":944178}', '2025-12-23 11:09:34', NULL, 0, 0),
(13, 39, 4, 'https://www.instagram.com/shahid_zx786?igsh=MXZhYW1iYTUzbGk3cg==', 0, '944180', 'completed', '{\"order\":944180}', '2025-12-23 11:15:06', NULL, 0, 0),
(14, 39, 4, 'https://www.instagram.com/baby__haniya__?igsh=MXg0NjA0dzRyeHo4bQ==', 0, '944184', 'completed', '{\"order\":944184}', '2025-12-23 11:20:42', NULL, 0, 0),
(15, 39, 4, 'https://www.instagram.com/shahid_zx786?igsh=MXZhYW1iYTUzbGk3cg==', 0, '944186', 'completed', '{\"order\":944186}', '2025-12-23 11:35:01', NULL, 0, 0),
(16, 62, 1, 'https://www.instagram.com/p/DSg_VVpDxVw/?igsh=MXhndjlsYTVlaTg0aw==', 0, '944196', 'completed', '{\"order\":944196}', '2025-12-23 12:00:48', NULL, 0, 0),
(17, 39, 3, 'https://www.instagram.com/shahid_zx786?igsh=MXZhYW1iYTUzbGk3cg==', 0, '944199', 'completed', '{\"order\":944199}', '2025-12-23 12:13:33', NULL, 0, 0),
(18, 39, 4, 'https://www.instagram.com/mastan_shaikhh?igsh=MWxxOXg5dHUwdDY4Mg==', 0, '944200', 'completed', '{\"order\":944200}', '2025-12-23 12:19:12', NULL, 0, 0),
(19, 39, 4, 'https://www.instagram.com/shahid_zx786?igsh=MXZhYW1iYTUzbGk3cg==', 0, '944201', 'completed', '{\"order\":944201}', '2025-12-23 12:19:37', NULL, 0, 0),
(20, 78, 2, 'https://www.instagram.com/reel/DSeR4ASDLQh/?igsh=Nnprb253djhwZG8w', 0, NULL, 'error', '{\"error\":\"Quantity less than minimal 100\"}', '2025-12-23 12:25:29', NULL, 0, 0),
(21, 91, 2, 'https://www.instagram.com/reel/DSeR4ASDLQh/?igsh=Nnprb253djhwZG8w', 0, NULL, 'error', '{\"error\":\"Quantity less than minimal 100\"}', '2025-12-23 12:29:43', NULL, 0, 0),
(22, 39, 4, 'https://www.instagram.com/shahid_zx786?igsh=MXZhYW1iYTUzbGk3cg==', 0, '944204', 'completed', '{\"order\":944204}', '2025-12-23 12:30:08', NULL, 0, 0),
(23, 39, 4, 'https://www.instagram.com/shahid_zx786?igsh=MXZhYW1iYTUzbGk3cg==', 0, '944207', 'completed', '{\"order\":944207}', '2025-12-23 12:30:23', NULL, 0, 0),
(24, 39, 4, 'https://www.instagram.com/shahid_zx786?igsh=MXZhYW1iYTUzbGk3cg==', 0, '944223', 'completed', '{\"order\":944223}', '2025-12-23 13:04:07', NULL, 0, 0),
(25, 39, 4, 'https://www.instagram.com/shahid_zx786?igsh=MXZhYW1iYTUzbGk3cg==', 0, '944226', 'completed', '{\"order\":944226}', '2025-12-23 13:08:54', NULL, 0, 0),
(26, 39, 4, 'https://www.instagram.com/shahid_zx786?igsh=MXZhYW1iYTUzbGk3cg==', 0, '944239', 'completed', '{\"order\":944239}', '2025-12-23 13:24:04', NULL, 0, 0),
(27, 39, 4, 'https://www.instagram.com/shahid_zx786?igsh=MXZhYW1iYTUzbGk3cg==', 0, '944241', 'completed', '{\"order\":944241}', '2025-12-23 13:25:03', NULL, 0, 0),
(28, 39, 4, 'https://www.instagram.com/shahid_zx786?igsh=MXZhYW1iYTUzbGk3cg==', 0, '944244', 'completed', '{\"order\":944244}', '2025-12-23 13:35:54', NULL, 0, 0),
(29, 39, 4, 'https://www.instagram.com/shahid_zx786?igsh=MXZhYW1iYTUzbGk3cg==', 0, '944245', 'completed', '{\"order\":944245}', '2025-12-23 13:36:13', NULL, 0, 0),
(30, 39, 4, 'https://www.instagram.com/shahid_zx786?igsh=MXZhYW1iYTUzbGk3cg==', 0, '944246', 'completed', '{\"order\":944246}', '2025-12-23 13:36:25', NULL, 0, 0),
(31, 39, 4, 'https://www.instagram.com/shahid_zx786?igsh=MXZhYW1iYTUzbGk3cg==', 0, '944247', 'completed', '{\"order\":944247}', '2025-12-23 13:36:35', NULL, 0, 0),
(32, 39, 8, 'https://www.instagram.com/shahid_zx786?igsh=MXZhYW1iYTUzbGk3cg==', 0, '944253', 'completed', '{\"order\":944253}', '2025-12-23 13:53:01', NULL, 0, 0),
(33, 1, 5, 'https://www.instagram.com/p/CknbcGkrQbp/?igsh=MTBjM2hlanYzeTUwMg==', 0, NULL, 'error', '{\"error\":\"Quantity less than minimal 100\"}', '2025-12-23 13:59:26', NULL, 0, 0),
(34, 1, 6, 'https://www.instagram.com/p/CknbcGkrQbp/?igsh=MTBjM2hlanYzeTUwMg==', 0, '944254', 'completed', '{\"order\":944254}', '2025-12-23 13:59:36', NULL, 0, 0),
(35, 1, 5, 'https://www.instagram.com/p/CknbcGkrQbp/?igsh=MTBjM2hlanYzeTUwMg==', 0, NULL, 'error', '{\"error\":\"Quantity less than minimal 100\"}', '2025-12-23 13:59:52', NULL, 0, 0),
(36, 1, 9, 'https://www.instagram.com/p/CknbcGkrQbp/?igsh=MTBjM2hlanYzeTUwMg==', 0, '944255', 'completed', '{\"order\":944255}', '2025-12-23 13:59:58', NULL, 0, 0),
(37, 39, 9, 'https://www.instagram.com/shahid_zx786?igsh=MXZhYW1iYTUzbGk3cg==', 0, '944256', 'completed', '{\"order\":944256}', '2025-12-23 14:00:45', NULL, 0, 0),
(38, 1, 11, 'https://www.instagram.com/p/CknbcGkrQbp/?igsh=MTBjM2hlanYzeTUwMg==', 0, '944257', 'completed', '{\"order\":944257}', '2025-12-23 14:04:22', NULL, 0, 0),
(39, 39, 8, 'https://www.instagram.com/shahid_zx786?igsh=MXZhYW1iYTUzbGk3cg==', 0, '944258', 'completed', '{\"order\":944258}', '2025-12-23 14:05:45', NULL, 0, 0),
(40, 39, 9, 'https://www.instagram.com/shahid_zx786?igsh=MXZhYW1iYTUzbGk3cg==', 0, '944259', 'completed', '{\"order\":944259}', '2025-12-23 14:05:55', NULL, 0, 0),
(41, 39, 9, 'https://www.instagram.com/shahid_zx786?igsh=MXZhYW1iYTUzbGk3cg==', 0, '944260', 'completed', '{\"order\":944260}', '2025-12-23 14:06:04', NULL, 0, 0),
(42, 39, 9, 'https://www.instagram.com/shahid_zx786?igsh=MXZhYW1iYTUzbGk3cg==', 0, '944261', 'completed', '{\"order\":944261}', '2025-12-23 14:06:12', NULL, 0, 0),
(43, 39, 9, 'https://www.instagram.com/shahid_zx786?igsh=MXZhYW1iYTUzbGk3cg==', 0, '944263', 'completed', '{\"order\":944263}', '2025-12-23 14:06:19', NULL, 0, 0),
(44, 39, 9, 'https://www.instagram.com/shahid_zx786?igsh=MXZhYW1iYTUzbGk3cg==', 0, '944264', 'completed', '{\"order\":944264}', '2025-12-23 14:06:26', NULL, 0, 0),
(45, 39, 9, 'https://www.instagram.com/shahid_zx786?igsh=MXZhYW1iYTUzbGk3cg==', 0, '944266', 'completed', '{\"order\":944266}', '2025-12-23 14:06:33', NULL, 0, 0),
(46, 39, 9, 'https://www.instagram.com/shahid_zx786?igsh=MXZhYW1iYTUzbGk3cg==', 0, '944267', 'completed', '{\"order\":944267}', '2025-12-23 14:06:40', NULL, 0, 0),
(47, 39, 9, 'https://www.instagram.com/shahid_zx786?igsh=MXZhYW1iYTUzbGk3cg==', 0, '944268', 'completed', '{\"order\":944268}', '2025-12-23 14:06:47', NULL, 0, 0),
(48, 39, 8, 'https://www.instagram.com/shahid_zx786?igsh=MXZhYW1iYTUzbGk3cg==', 0, '944285', 'completed', '{\"order\":944285}', '2025-12-23 14:39:27', NULL, 0, 0),
(49, 39, 9, 'https://www.instagram.com/baby__haniya__?igsh=MXg0NjA0dzRyeHo4bQ==', 0, '944287', 'completed', '{\"order\":944287}', '2025-12-23 14:45:32', NULL, 0, 0),
(50, 39, 8, 'https://www.instagram.com/shahid_zx786?igsh=MXZhYW1iYTUzbGk3cg==', 0, '944288', 'completed', '{\"order\":944288}', '2025-12-23 14:46:34', NULL, 0, 0),
(51, 39, 8, 'https://www.instagram.com/shahid_zx786?igsh=MXZhYW1iYTUzbGk3cg==', 0, '944293', 'completed', '{\"order\":944293}', '2025-12-23 14:57:11', NULL, 0, 0),
(52, 39, 6, 'https://www.instagram.com/reel/DSm_EqRDBmI/?igsh=MXZseWhhaGR3M2ZhcQ==', 0, '944294', 'completed', '{\"order\":944294}', '2025-12-23 14:57:52', NULL, 0, 0),
(53, 39, 8, 'https://www.instagram.com/shahid_zx786?igsh=MXZhYW1iYTUzbGk3cg==', 0, '944305', 'completed', '{\"order\":944305}', '2025-12-23 15:16:03', NULL, 0, 0),
(54, 39, 6, 'https://www.instagram.com/shahid_zx786?igsh=MXZhYW1iYTUzbGk3cg==', 0, '944306', 'completed', '{\"order\":944306}', '2025-12-23 15:16:12', NULL, 0, 0),
(55, 39, 6, 'https://www.instagram.com/shahid_zx786?igsh=MXZhYW1iYTUzbGk3cg==', 0, '944308', 'completed', '{\"order\":944308}', '2025-12-23 15:16:19', NULL, 0, 0),
(56, 39, 6, 'https://www.instagram.com/shahid_zx786?igsh=MXZhYW1iYTUzbGk3cg==', 0, '944309', 'completed', '{\"order\":944309}', '2025-12-23 15:16:25', NULL, 0, 0),
(57, 39, 6, 'https://www.instagram.com/shahid_zx786?igsh=MXZhYW1iYTUzbGk3cg==', 0, '944310', 'completed', '{\"order\":944310}', '2025-12-23 15:16:30', NULL, 0, 0),
(58, 39, 6, 'https://www.instagram.com/shahid_zx786?igsh=MXZhYW1iYTUzbGk3cg==', 0, '944311', 'completed', '{\"order\":944311}', '2025-12-23 15:16:36', NULL, 0, 0),
(59, 39, 6, 'https://www.instagram.com/shahid_zx786?igsh=MXZhYW1iYTUzbGk3cg==', 0, '944312', 'completed', '{\"order\":944312}', '2025-12-23 15:16:58', NULL, 0, 0),
(60, 39, 8, 'https://www.instagram.com/shahid_zx786?igsh=MXZhYW1iYTUzbGk3cg==', 0, '944313', 'completed', '{\"order\":944313}', '2025-12-23 15:17:23', NULL, 0, 0),
(61, 39, 8, 'https://www.instagram.com/shahid_zx786?igsh=MXZhYW1iYTUzbGk3cg==', 0, '944319', 'completed', '{\"order\":944319}', '2025-12-23 15:27:36', NULL, 0, 0),
(62, 39, 8, 'https://www.instagram.com/shahid_zx786?igsh=MXZhYW1iYTUzbGk3cg==', 0, '944321', 'completed', '{\"order\":944321}', '2025-12-23 15:28:37', NULL, 0, 0),
(63, 39, 8, 'https://www.instagram.com/shahid_zx786?igsh=MXZhYW1iYTUzbGk3cg==', 0, '944322', 'completed', '{\"order\":944322}', '2025-12-23 15:28:51', NULL, 0, 0),
(64, 39, 8, 'https://www.instagram.com/shahid_zx786?igsh=MXZhYW1iYTUzbGk3cg==', 0, '944323', 'completed', '{\"order\":944323}', '2025-12-23 15:28:58', NULL, 0, 0),
(65, 39, 8, 'https://www.instagram.com/shahid_zx786?igsh=MXZhYW1iYTUzbGk3cg==', 0, '944324', 'completed', '{\"order\":944324}', '2025-12-23 15:29:05', NULL, 0, 0),
(66, 39, 8, 'https://www.instagram.com/shahid_zx786?igsh=MXZhYW1iYTUzbGk3cg==', 0, '944325', 'completed', '{\"order\":944325}', '2025-12-23 15:29:11', NULL, 0, 0),
(67, 39, 8, 'https://www.instagram.com/shahid_zx786?igsh=MXZhYW1iYTUzbGk3cg==', 0, '944326', 'completed', '{\"order\":944326}', '2025-12-23 15:29:18', NULL, 0, 0),
(68, 39, 8, 'https://www.instagram.com/shahid_zx786?igsh=MXZhYW1iYTUzbGk3cg==', 0, '944327', 'completed', '{\"order\":944327}', '2025-12-23 15:29:27', NULL, 0, 0),
(69, 39, 8, 'https://www.instagram.com/shahid_zx786?igsh=MXZhYW1iYTUzbGk3cg==', 0, '944328', 'completed', '{\"order\":944328}', '2025-12-23 15:29:33', NULL, 0, 0),
(70, 39, 8, 'https://www.instagram.com/shahid_zx786?igsh=MXZhYW1iYTUzbGk3cg==', 0, '944329', 'completed', '{\"order\":944329}', '2025-12-23 15:29:46', NULL, 0, 0),
(71, 39, 8, 'https://www.instagram.com/shahid_zx786?igsh=MXZhYW1iYTUzbGk3cg==', 0, '944330', 'completed', '{\"order\":944330}', '2025-12-23 15:29:59', NULL, 0, 0),
(72, 39, 8, 'https://www.instagram.com/umar___qureshi02?igsh=N2Rpc24wN2Z6dmQ3', 0, '944331', 'completed', '{\"order\":944331}', '2025-12-23 15:32:18', NULL, 0, 0),
(73, 39, 8, 'https://www.instagram.com/umar___qureshi02?igsh=N2Rpc24wN2Z6dmQ3', 0, '944332', 'completed', '{\"order\":944332}', '2025-12-23 15:32:40', NULL, 0, 0),
(74, 39, 8, 'https://www.instagram.com/umar___qureshi02?igsh=N2Rpc24wN2Z6dmQ3', 0, '944340', 'completed', '{\"order\":944340}', '2025-12-23 15:42:48', NULL, 0, 0),
(75, 39, 8, 'https://www.instagram.com/umar___qureshi02?igsh=N2Rpc24wN2Z6dmQ3', 0, '944349', 'completed', '{\"order\":944349}', '2025-12-23 15:57:32', NULL, 0, 0),
(76, 39, 8, 'https://www.instagram.com/umar___qureshi02?igsh=N2Rpc24wN2Z6dmQ3', 0, '944352', 'completed', '{\"order\":944352}', '2025-12-23 16:03:01', NULL, 0, 0),
(77, 39, 8, 'https://www.instagram.com/umar___qureshi02?igsh=N2Rpc24wN2Z6dmQ3', 0, '944355', 'completed', '{\"order\":944355}', '2025-12-23 16:08:42', NULL, 0, 0),
(78, 39, 13, 'https://www.instagram.com/umar___qureshi02?igsh=N2Rpc24wN2Z6dmQ3', 0, '944445', 'completed', '{\"order\":944445}', '2025-12-23 18:10:00', NULL, 0, 0),
(79, 39, 13, 'https://www.instagram.com/shahid_zx786?igsh=MXZhYW1iYTUzbGk3cg==', 0, '944446', 'completed', '{\"order\":944446}', '2025-12-23 18:11:12', NULL, 0, 0),
(80, 39, 13, 'https://www.instagram.com/shahid_zx786?igsh=MXZhYW1iYTUzbGk3cg==', 0, '944447', 'completed', '{\"order\":944447}', '2025-12-23 18:11:23', NULL, 0, 0),
(81, 39, 13, 'https://www.instagram.com/shahid_zx786?igsh=MXZhYW1iYTUzbGk3cg==', 0, '944448', 'completed', '{\"order\":944448}', '2025-12-23 18:11:32', NULL, 0, 0),
(82, 39, 13, 'https://www.instagram.com/shahid_zx786?igsh=MXZhYW1iYTUzbGk3cg==', 0, '944449', 'completed', '{\"order\":944449}', '2025-12-23 18:11:42', NULL, 0, 0),
(83, 39, 13, 'https://www.instagram.com/umar___qureshi02?igsh=N2Rpc24wN2Z6dmQ3', 0, '944450', 'completed', '{\"order\":944450}', '2025-12-23 18:11:53', NULL, 0, 0),
(84, 39, 13, 'https://www.instagram.com/shahid_zx786?igsh=MXZhYW1iYTUzbGk3cg==', 0, '944457', 'completed', '{\"order\":944457}', '2025-12-23 18:18:29', NULL, 0, 0),
(85, 39, 13, 'https://www.instagram.com/umar___qureshi02?igsh=N2Rpc24wN2Z6dmQ3', 0, '944459', 'completed', '{\"order\":944459}', '2025-12-23 18:18:47', NULL, 0, 0),
(86, 39, 13, 'https://www.instagram.com/umar___qureshi02?igsh=N2Rpc24wN2Z6dmQ3', 0, '944460', 'completed', '{\"order\":944460}', '2025-12-23 18:19:09', NULL, 0, 0),
(87, 39, 13, 'https://www.instagram.com/umar___qureshi02?igsh=N2Rpc24wN2Z6dmQ3', 0, '944462', 'completed', '{\"order\":944462}', '2025-12-23 18:22:28', NULL, 0, 0),
(88, 39, 13, 'https://www.instagram.com/shahid_zx786?igsh=MXZhYW1iYTUzbGk3cg==', 0, '944464', 'completed', '{\"order\":944464}', '2025-12-23 18:23:25', NULL, 0, 0),
(89, 39, 13, 'https://www.instagram.com/umar___qureshi02?igsh=N2Rpc24wN2Z6dmQ3', 0, '944465', 'completed', '{\"order\":944465}', '2025-12-23 18:23:34', NULL, 0, 0),
(90, 39, 13, 'https://www.instagram.com/umar___qureshi02?igsh=N2Rpc24wN2Z6dmQ3', 0, '944476', 'completed', '{\"order\":944476}', '2025-12-23 18:37:43', NULL, 0, 0),
(91, 39, 13, 'https://www.instagram.com/umar___qureshi02?igsh=N2Rpc24wN2Z6dmQ3', 0, '944477', 'completed', '{\"order\":944477}', '2025-12-23 18:37:55', NULL, 0, 0),
(92, 39, 13, 'https://www.instagram.com/umar___qureshi02?igsh=N2Rpc24wN2Z6dmQ3', 0, '944482', 'completed', '{\"order\":944482}', '2025-12-23 18:44:13', NULL, 0, 0),
(93, 39, 13, 'https://www.instagram.com/shahid_zx786?igsh=MXZhYW1iYTUzbGk3cg==', 0, '944483', 'completed', '{\"order\":944483}', '2025-12-23 18:45:20', NULL, 0, 0),
(94, 39, 13, 'https://www.instagram.com/umar___qureshi02?igsh=N2Rpc24wN2Z6dmQ3', 0, '944484', 'completed', '{\"order\":944484}', '2025-12-23 18:46:11', NULL, 0, 0),
(95, 39, 13, 'https://www.instagram.com/shahid_zx786?igsh=MXZhYW1iYTUzbGk3cg==', 0, '944485', 'completed', '{\"order\":944485}', '2025-12-23 18:46:59', NULL, 0, 0),
(96, 39, 13, 'https://www.instagram.com/umar___qureshi02?igsh=N2Rpc24wN2Z6dmQ3', 0, '944487', 'completed', '{\"order\":944487}', '2025-12-23 18:49:32', NULL, 0, 0),
(97, 39, 13, 'https://www.instagram.com/umar___qureshi02?igsh=N2Rpc24wN2Z6dmQ3', 0, '944488', 'completed', '{\"order\":944488}', '2025-12-23 18:49:54', NULL, 0, 0),
(98, 39, 13, 'https://www.instagram.com/umar___qureshi02?igsh=N2Rpc24wN2Z6dmQ3', 0, '944490', 'completed', '{\"order\":944490}', '2025-12-23 18:50:47', NULL, 0, 0),
(99, 39, 13, 'https://www.instagram.com/shahid_zx786?igsh=MXZhYW1iYTUzbGk3cg==', 0, '944491', 'completed', '{\"order\":944491}', '2025-12-23 18:50:55', NULL, 0, 0),
(100, 39, 13, 'https://www.instagram.com/umar___qureshi02?igsh=N2Rpc24wN2Z6dmQ3', 0, '944499', 'completed', '{\"order\":944499}', '2025-12-23 19:00:15', NULL, 0, 0),
(101, 39, 13, 'https://www.instagram.com/shahid_zx786?igsh=MXZhYW1iYTUzbGk3cg==', 0, '944501', 'completed', '{\"order\":944501}', '2025-12-23 19:00:57', NULL, 0, 0),
(102, 39, 13, 'https://www.instagram.com/umar___qureshi02?igsh=N2Rpc24wN2Z6dmQ3', 0, '944502', 'completed', '{\"order\":944502}', '2025-12-23 19:01:05', NULL, 0, 0),
(103, 39, 13, 'https://www.instagram.com/umar___qureshi02?igsh=N2Rpc24wN2Z6dmQ3', 0, '944505', 'completed', '{\"order\":944505}', '2025-12-23 19:03:15', NULL, 0, 0),
(104, 39, 13, 'https://www.instagram.com/umar___qureshi02?igsh=N2Rpc24wN2Z6dmQ3', 0, '944514', 'completed', '{\"order\":944514}', '2025-12-23 19:13:14', NULL, 0, 0),
(105, 39, 13, 'https://www.instagram.com/umar___qureshi02?igsh=N2Rpc24wN2Z6dmQ3', 0, '944515', 'completed', '{\"order\":944515}', '2025-12-23 19:13:47', NULL, 0, 0),
(106, 39, 13, 'https://www.instagram.com/umar___qureshi02?igsh=N2Rpc24wN2Z6dmQ3', 0, '944516', 'completed', '{\"order\":944516}', '2025-12-23 19:17:44', NULL, 0, 0),
(107, 39, 13, 'https://www.instagram.com/shahid_zx786?igsh=MXZhYW1iYTUzbGk3cg==', 0, '944517', 'completed', '{\"order\":944517}', '2025-12-23 19:17:52', NULL, 0, 0),
(108, 39, 13, 'https://www.instagram.com/umar___qureshi02?igsh=N2Rpc24wN2Z6dmQ3', 0, '944522', 'completed', '{\"order\":944522}', '2025-12-23 19:23:12', NULL, 0, 0),
(109, 39, 13, 'https://www.instagram.com/shahid_zx786?igsh=MXZhYW1iYTUzbGk3cg==', 0, '944523', 'completed', '{\"order\":944523}', '2025-12-23 19:23:23', NULL, 0, 0),
(110, 39, 13, 'https://www.instagram.com/umar___qureshi02?igsh=N2Rpc24wN2Z6dmQ3', 0, '944525', 'completed', '{\"order\":944525}', '2025-12-23 19:25:15', NULL, 0, 0),
(111, 39, 13, 'https://www.instagram.com/umar___qureshi02?igsh=N2Rpc24wN2Z6dmQ3', 0, '944526', 'completed', '{\"order\":944526}', '2025-12-23 19:26:47', NULL, 0, 0),
(112, 1, 14, 'https://www.instagram.com/p/DQEu9MDjNwS/', 100, NULL, '', NULL, '2025-12-24 18:53:12', NULL, 0, 0),
(113, 1, 14, 'https://www.instagram.com/p/DQEu9MDjNwS/', 100, NULL, '', NULL, '2025-12-24 19:14:37', NULL, 0, 0),
(114, 1, 16, 'yash.developer', 1000, NULL, '', NULL, '2025-12-24 19:15:25', NULL, 0, 0),
(115, 1, 17, 'https://www.instagram.com/p/DQEu9MDjNwS/', 100, NULL, 'completed', NULL, '2025-12-24 19:27:43', 945057, 0, 0),
(116, 619, 17, 'https://www.instagram.com/reel/DSgtZRRkjPq/?igsh=eDFydGcyMHh5bTZu', 100, NULL, 'completed', NULL, '2025-12-24 19:35:42', 945058, 0, 0),
(117, 619, 17, 'https://www.instagram.com/reel/DSgtZRRkjPq/?igsh=eDFydGcyMHh5bTZu', 100, NULL, 'completed', NULL, '2025-12-24 19:36:01', 945059, 0, 0),
(118, 619, 17, 'https://www.instagram.com/reel/DSgtZRRkjPq/?igsh=eDFydGcyMHh5bTZu', 100, NULL, 'completed', NULL, '2025-12-24 19:36:15', 945069, 0, 0),
(119, 619, 17, 'https://www.instagram.com/reel/DSgtZRRkjPq/?igsh=eDFydGcyMHh5bTZu', 100, NULL, 'completed', NULL, '2025-12-24 19:36:18', 945068, 0, 0),
(120, 619, 17, 'https://www.instagram.com/reel/DSgtZRRkjPq/?igsh=eDFydGcyMHh5bTZu', 100, NULL, 'completed', NULL, '2025-12-24 19:36:26', 945067, 0, 0),
(121, 619, 17, 'https://www.instagram.com/reel/DSgtZRRkjPq/?igsh=eDFydGcyMHh5bTZu', 100, NULL, 'completed', NULL, '2025-12-24 19:36:46', 945066, 0, 0),
(122, 619, 17, 'https://www.instagram.com/reel/DSgtZRRkjPq/?igsh=eDFydGcyMHh5bTZu', 100, NULL, 'completed', NULL, '2025-12-24 19:36:55', 945065, 0, 0),
(123, 619, 17, 'https://www.instagram.com/reel/DSgtZRRkjPq/?igsh=eDFydGcyMHh5bTZu', 100, NULL, 'completed', NULL, '2025-12-24 19:37:05', 945064, 0, 0),
(124, 619, 17, 'https://www.instagram.com/reel/DSgtZRRkjPq/?igsh=eDFydGcyMHh5bTZu', 100, NULL, 'completed', NULL, '2025-12-24 19:37:18', 945063, 0, 0),
(125, 619, 17, 'https://www.instagram.com/reel/DSgtZRRkjPq/?igsh=eDFydGcyMHh5bTZu', 100, NULL, 'completed', NULL, '2025-12-24 19:38:45', 945062, 0, 0),
(126, 619, 17, 'https://www.instagram.com/reel/DSgtZRRkjPq/?igsh=eDFydGcyMHh5bTZu', 100, NULL, 'completed', NULL, '2025-12-24 19:38:54', 945061, 0, 0),
(127, 1, 17, 'https://www.instagram.com/p/DSARsXnDUfP/', 100, NULL, 'completed', NULL, '2025-12-24 19:40:24', 945060, 0, 0),
(128, 619, 20, 'https://www.instagram.com/thefarmanpoet_?igsh=M2RkM3Q1MDBjMWg3', 2000, NULL, 'completed', NULL, '2025-12-24 20:11:57', 945096, 0, 0),
(129, 619, 20, 'https://www.instagram.com/thefarmanpoet_?igsh=M2RkM3Q1MDBjMWg3', 2000, NULL, 'completed', NULL, '2025-12-24 20:12:18', 945095, 0, 0),
(130, 619, 20, 'https://www.instagram.com/thefarmanpoet_?igsh=M2RkM3Q1MDBjMWg3', 2000, NULL, 'completed', NULL, '2025-12-24 20:12:40', 945094, 0, 0),
(131, 619, 19, 'https://www.instagram.com/thefarmanpoet_?igsh=M2RkM3Q1MDBjMWg3', 1000, NULL, 'completed', NULL, '2025-12-24 20:18:56', 945093, 0, 0),
(132, 643, 17, 'https://www.instagram.com/reel/DSpwVlNEr1A/?igsh=NXM3YWVrdGRqcnF4', 100, NULL, 'completed', NULL, '2025-12-24 21:51:49', 945086, 0, 0),
(133, 27, 19, 'https://www.Instagram.com/bssarkar_11?igsh=eWJmYjE1ZmdjaGRK', 1000, NULL, 'completed', NULL, '2025-12-25 09:04:09', 945325, 0, 0),
(134, 16, 19, 'dkx.edx7', 1000, NULL, 'completed', NULL, '2025-12-25 10:22:34', 945348, 0, 0),
(135, 27, 19, 'https://www.instagram.com/professor_of_web?igsh=MTE0bGt1Mnc4MXIzbA==', 1000, NULL, 'completed', NULL, '2025-12-25 11:58:41', 945380, 0, 0),
(136, 27, 19, 'https://www.Instagram.com/oye_adi_0001?igsh=eWJmYjE1ZmdjaGRK', 1000, NULL, 'completed', NULL, '2025-12-25 14:36:37', 945470, 0, 0),
(137, 22, 17, 'https://www.instagram.com/p/DRtZ5rJkWf_/?igsh=MXdjNHUydTNpdHF1aQ==', 100, NULL, 'completed', NULL, '2025-12-25 19:39:53', 945593, 0, 0),
(138, 22, 17, 'https://www.instagram.com/p/DRtZ5rJkWf_/?igsh=MXdjNHUydTNpdHF1aQ==', 100, NULL, 'completed', NULL, '2025-12-25 19:39:55', 945594, 0, 0),
(139, 651, 19, 'https://www.instagram.com/4nsh._.x0?igsh=MXdhM3ZzZHcyZjBvdg==', 1000, NULL, '', NULL, '2025-12-26 14:04:03', 945923, 0, 0),
(140, 660, 20, 'https://www.instagram.com/4nsh._.x0?igsh=MXdhM3ZzZHcyZjBvdg==', 2000, NULL, '', NULL, '2025-12-26 15:25:00', 945959, 0, 0),
(141, 660, 19, 'https://www.instagram.com/4nsh._.x0?igsh=MXdhM3ZzZHcyZjBvdg==', 1000, NULL, '', NULL, '2025-12-26 15:45:16', 945969, 0, 0),
(142, 660, 19, 'https://www.instagram.com/4nsh._.x0?igsh=MWRtamkzMDY5cXU1bQ==', 1000, NULL, 'pending', NULL, '2025-12-27 04:17:12', NULL, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `smm_service_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `category` varchar(100) DEFAULT 'General',
  `type` enum('likes','followers') NOT NULL,
  `quantity` int(11) NOT NULL,
  `cost_coins` int(11) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `api_service_id` int(11) DEFAULT NULL,
  `min_qty` int(11) DEFAULT 100,
  `max_qty` int(11) DEFAULT 10000
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `smm_service_id`, `name`, `category`, `type`, `quantity`, `cost_coins`, `active`, `api_service_id`, `min_qty`, `max_qty`) VALUES
(17, 0, '10 Instagram Likes', 'Manual Add', 'likes', 0, 100, 1, 51259, 10, 10),
(18, 0, '20 Instagram Likes', 'Manual Add', 'likes', 0, 200, 1, 51333, 20, 20),
(19, 0, '100 Instagram Followers', 'Manual Add', 'likes', 0, 1000, 1, 51342, 100, 100),
(20, 0, '200 Instagram Followers', 'Manual Add', 'likes', 0, 2000, 1, 51342, 200, 200);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('ad_code_monetag', '<script src=\"https://quge5.com/88/tag.min.js\" data-zone=\"195403\" async data-cfasync=\"false\"></script>'),
('auto_order_limit', '2'),
('banner_image', 'https://gift.iamyashraj.com/assets/images/banner.png'),
('referral_bonus', '300'),
('site_name', 'Christmas Gift SMM'),
('smm_api_key', '8c5ea0fa9bc380b8fcd81db367a04440'),
('smm_api_url', 'https://indiansmmservices.com/api/v2');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `whatsapp` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `coins` int(11) DEFAULT 0,
  `referral_code` varchar(20) NOT NULL,
  `referred_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `spins_left` int(11) DEFAULT 5,
  `last_spin_time` datetime DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `device_fingerprint` varchar(100) DEFAULT NULL,
  `is_banned` tinyint(1) DEFAULT 0,
  `last_checkin` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `whatsapp`, `email`, `password`, `coins`, `referral_code`, `referred_by`, `created_at`, `spins_left`, `last_spin_time`, `ip_address`, `device_fingerprint`, `is_banned`, `last_checkin`) VALUES
(1, 'Yash raj', '8969810277', 'admin@admin.com', '$2y$10$Pxpm/QnbZqLrJpngm6.BE.nZfr1rckTVi5nG2hTydIbkVysY7PPy.', 2147481362, '42D7546B', NULL, '2025-12-23 05:22:54', 3, '2025-12-24 20:08:03', NULL, NULL, 0, NULL),
(3, 'Sunny', '9821701530', 'sunnysingh8762@gmail.com', '$2y$12$A8c8JuJIcrsOWsGFtZRgseQqyQvWfR7ZG6ye/s3JlSOaqT3kLYcfS', 30, 'ADDAEA35', NULL, '2025-12-23 09:11:36', 0, '2025-12-23 14:43:41', NULL, NULL, 0, NULL),
(4, 'Gaurav Borah', '9394591399', 'gborah063@gmail.com', '$2y$12$bCLwmcsnvFj/vQ8C4sV/xun6QJxjnZjKxTFeaKiQBN9uHd9XfQP0S', 15, 'C26EED44', NULL, '2025-12-23 09:11:44', 0, '2025-12-23 14:42:18', NULL, NULL, 0, NULL),
(5, 'Altaf Ahmad', '8303773453', 'altaf7860037074@gmail.com', '$2y$12$VfKCiIs.L8yCDOcOud9M8u28ySxSN3sAp2cCemMTysWhKd6UTjNhm', 35, '1F3B0213', NULL, '2025-12-23 09:12:30', 0, '2025-12-23 14:43:07', NULL, NULL, 0, NULL),
(6, 'Sandeep tomar', '7834929267', 'sandeeprealme7834@gmail.com', '$2y$12$7Dsrjo9pQf2xaJEl7jzd2us/gTAvvZ8sCpawZnia3oPPF16B9d0Fa', 35, 'D07ACD1D', NULL, '2025-12-23 09:13:37', 0, '2025-12-23 14:45:17', NULL, NULL, 0, NULL),
(7, 'J I G A R', 'jdthe530@gmail.com', 'jdthe530@gmail.com', '$2y$12$KTe..3mZS361mGrcTbTs4u8q/ulmvWmXB4mxBUbEZlp2.TudlMy.S', 30, '8B16D564', NULL, '2025-12-23 09:13:40', 0, '2025-12-23 14:44:48', NULL, NULL, 0, NULL),
(8, 'Pankaj', '9571659559', 'unknownbande25@gmail.com', '$2y$12$f.XTZ9OtbY..QaZ1gDftxegf9e26kBdoU5j3tp.yA378Je6h9n9f.', 10, '91260734', NULL, '2025-12-23 09:14:16', 0, '2025-12-23 14:44:53', NULL, NULL, 0, NULL),
(9, 'MrBiswas', '8584866281', 'sumitbiswas851@gmail.com', '$2y$12$2Fm0wv570NodFUzcogkpZuNJCAINfCzHSu.Sn.J.eYGtkYDnWFn4S', 20, 'DDDF8A5A', NULL, '2025-12-23 09:15:31', 0, '2025-12-23 14:46:11', NULL, NULL, 0, NULL),
(10, 'Kalpanik', '9004538025', 'kalpanikfilms@gmail.com', '$2y$12$aHXSoo32YWovC5Crl9s7mOa81WXeWUvrnuha/uazb6..j9uR8J6Uu', 85, '10E5BA8E', NULL, '2025-12-23 09:27:40', 0, '2025-12-23 19:20:10', NULL, NULL, 0, NULL),
(12, 'Shibin', '+96890727304', 'shibinpu04@gmail.com', '$2y$12$csYXR.VJ4Womg/MomzgFGOMA5J9hYqA1SDrdHAK151m4YHjdzpmgy', 5, '75A0F22F', NULL, '2025-12-23 09:33:06', 4, '2025-12-23 15:03:10', NULL, NULL, 0, NULL),
(13, 'Ankit Sharma', '9996849913', 'askp42835@gmail.com', '$2y$12$pOMzJwq7w/LK0LE8hxgC0eAMwiGGtE5qZuVfNtbae2sErQBBW1hWG', 35, 'E45B7F25', NULL, '2025-12-23 09:35:01', 0, '2025-12-23 15:05:53', NULL, NULL, 0, NULL),
(14, 'Bharam', '8073255916', 'nagubharam@gmail.com', '$2y$12$IEtKub8P3t89NOJI5JGO5OKxdgUbah8pDpNu3QCjgJe0KJXLBJdeK', 10, '785B8CAD', NULL, '2025-12-23 09:37:04', 4, '2025-12-23 15:07:15', NULL, NULL, 0, NULL),
(15, 'Asman sk', '9734681406', 'asmansk434@gmail.com', '$2y$12$RSqxefMfzphvzmhh54nZBeYFWbVT4ug2np1DLpWh4pF5wobxkduNG', 25, '82822405', NULL, '2025-12-23 09:39:15', 0, '2025-12-23 15:10:06', NULL, NULL, 0, NULL),
(16, 'Deepak Kumar', '7855812607', 'dk02692335@gmail.com', '$2y$12$DIHo.T5YW/Yy77E32UQoCeKLrht4wcgJK9qrYoFYPjmm1a1eDVkLW', 150, 'F2F96381', NULL, '2025-12-23 09:41:23', 0, '2025-12-25 10:22:03', NULL, NULL, 0, NULL),
(17, 'Arsh', '6203786385', 'dg5340415@gmail.com', '$2y$12$1X3GFqSWJ1nciu/l4gRN4e9ajhQHMJzkbyR0LXi3/KjTboKi/botm', 35, 'D5CF8217', NULL, '2025-12-23 09:42:54', 0, '2025-12-23 15:13:53', NULL, NULL, 0, NULL),
(18, 'Prem Bhandakkar', '7020748677', 'prembhandakkar368@gmail.com', '$2y$12$Mm1OJPHKU.9Ak01IQIqskuyMuMKoZAgdi8wLNWevhcIIZbZgJfNtK', 0, '87BD502E', NULL, '2025-12-23 09:43:41', 4, '2025-12-24 19:45:09', NULL, NULL, 0, NULL),
(19, 'Sukanta das', '6296713588', 'sukantad407@gmail.com', '$2y$12$tvDWxQ4dBLMfQTo5a8tzZ.F7ASsSFK9ftVoGX93x2SAUVDXYNzJaC', 0, '4C08FB16', NULL, '2025-12-23 09:45:19', 5, NULL, NULL, NULL, 0, NULL),
(20, 'Gufran', '89388 60197', 'naqvigufran2@gmail.com', '$2y$12$MFwzYjC7EmsxM4uAmBOd2urKagt1bloC6u9z9BIeR/5QEXgI6WZZa', 5, '20DC4F12', NULL, '2025-12-23 09:46:29', 3, '2025-12-23 15:16:44', NULL, NULL, 0, NULL),
(21, 'Raghansh Gulati', '7696293373', 'purugulati683@gmail.com', '$2y$12$oWzPecHtW3XLyOhwE6XrCeklddl5te04zmyuQ8NvgwgS08TOL.BB6', 15, '8FEBA2CA', NULL, '2025-12-23 09:48:01', 0, '2025-12-23 15:18:51', NULL, NULL, 0, NULL),
(22, 'Rishirajsingh', '6388568396', 'singhtejash84@gmail.com', '$2y$12$nnxvXNE/OUOBp4bS7yhdK.Nb6VoyQtkeJFvx3lgKHumOuJmQDnKDC', 190, '2C6B148A', NULL, '2025-12-23 09:49:57', 0, '2025-12-25 19:38:12', NULL, NULL, 0, NULL),
(23, 'Deepakrojh11', '7849846739', 'bigdonstar09@gmail.com', '$2y$12$UYpwQXSl2/KsFOnkSIn4Qu9fAMX9oW8zAfW/ufjSiGlzelW3SbUmy', 45, '485B7B0C', NULL, '2025-12-23 09:50:30', 0, '2025-12-24 13:39:18', NULL, NULL, 0, NULL),
(24, 'Aayush Vats', '9971484744', 'aayushvats240@gmail.com', '$2y$12$lSCLiFCr96lbZcwcktqwpu8T4MfJoURDL9bL/gibReQy8/oXo2BEe', 25, 'AF26BB96', NULL, '2025-12-23 09:51:13', 0, '2025-12-23 15:42:16', NULL, NULL, 0, NULL),
(25, 'Vibhuti Baibhav', '8676837552', 'vibhutibaibhav6@gmail.com', '$2y$12$48D1vwBC.RGiTE/TsXTuYuDIwA9EvIDWCOEHzGTx4q/Y4COHGtdGS', 30, 'A5F242B5', NULL, '2025-12-23 09:52:52', 0, '2025-12-23 15:24:31', NULL, NULL, 0, NULL),
(26, 'snta', '7827754293', 'tradingarnav01@gmail.com', '$2y$12$0xcsWCQNcRsQPzt1VHzTC.C0ungyhTo9p3NrltnUJFWLkXzr/0dk6', 0, '2C29D893', NULL, '2025-12-23 09:55:35', 5, NULL, NULL, NULL, 0, NULL),
(27, 'Bs sarkar', '9351292319', 'shantisharmashanti8@gmail.com', '$2y$12$XTRXf9O9Ii8Vxe6YMahVqO8is3xAwBwSzeNmBguMsivwNzIW.WhK2', 5585, '4F41C9BA', NULL, '2025-12-23 09:58:07', 0, '2025-12-26 15:53:40', NULL, NULL, 1, NULL),
(28, 'Jerry', '9355773595', 'workforjerryyy@gmail.com', '$2y$12$NeFCQpLaJs3mdlr6nlHzYOD9iSbmIjsrith10LV7iLoUcoDEyHxOa', 25, '432E5175', NULL, '2025-12-23 09:59:50', 0, '2025-12-23 15:31:10', NULL, NULL, 0, NULL),
(29, 'RONAKKHAN', '7064438752', 'begumtara749@gmail.com', '$2y$12$.tQvSGmlfrkCvNLlHCXm8.SJTWaZsyl/PFzEVFf64qPHR57VKviKS', 20, '476CEC68', NULL, '2025-12-23 10:00:48', 0, '2025-12-23 15:32:31', NULL, NULL, 0, NULL),
(30, 'Avi kapoor', '7417777079', 'avikapoor7777@gmail.com', '$2y$12$DYnINb1aBatFdeuJsL0uWuWPvgUrfXRiUqVkj1/NjUYCaENiVC0sG', 5, '9800A3E4', NULL, '2025-12-23 10:06:49', 4, '2025-12-23 15:36:55', NULL, NULL, 0, NULL),
(31, 'Lord bs', '6376041150', 'babhsharmagaming007@gmail.com', '$2y$12$56921Bpsz2t.2ZBnBZXEveYTg1RNWeO5O9l8F9OOqLYwPwzTyCPZW', 15, '5F347F26', NULL, '2025-12-23 10:07:55', 4, '2025-12-23 15:38:09', NULL, NULL, 0, NULL),
(32, 'Girish', '9999999989', 'tempmailgirish1@gmail.com', '$2y$12$xpmiNxAU2qhfE3U7QNz1eeMIfW9N18m39phtk3CnfFTy/h.ZzBKlS', 30, '7E9A6D86', NULL, '2025-12-23 10:10:41', 0, '2025-12-23 15:41:17', NULL, NULL, 0, NULL),
(33, 'Sheikhmajaz', '9596433023', 'mrmalang97@gmail.com', '$2y$12$yOb.3PfzOdaWgu8.GsM0AuOhTFu0MM5skWVhbcnOBv8tTH7L3embO', 15, '5601396F', NULL, '2025-12-23 10:17:06', 0, '2025-12-23 15:48:09', NULL, NULL, 0, NULL),
(34, 'Sameer Singh', '8447424675', 'risingking.land@gmail.com', '$2y$12$dAUCXSH.MkKX7o39kcDEEOrIX/hkqs3x/7QwGh0WICW0kUwqd08om', 35, '1A48B206', NULL, '2025-12-23 10:20:08', 0, '2025-12-23 15:51:00', NULL, NULL, 0, NULL),
(35, 'Ayaaz', '9021872713', 'shaikhayaaz408@gmail.com', '$2y$12$LxeNT5iD/aP9ddVwRAHHveRk28GKt2GW6dcOgUwPupX06uGGtYNf2', 35, 'AYAA3602', NULL, '2025-12-23 10:27:23', 0, '2025-12-23 15:58:16', NULL, NULL, 0, NULL),
(36, 'Gaurav', '8708734897', 'gouravsingh4365@gmail.com', '$2y$12$lvCqSza.83miuUVzF3z9t.Ml.kld5xikt0c09.vAWPVM9At348AVW', 30, 'GAUR2729', NULL, '2025-12-23 10:28:34', 0, '2025-12-23 15:59:23', NULL, NULL, 0, NULL),
(37, 'Aadit', '8961282057', 'aaditahir239@gmail.com', '$2y$12$Jh4STUWEtin5ibpP8HJwcufhakY/Teg7fHqFviuicGt4TVEZLLzpO', 0, 'AADI2521', NULL, '2025-12-23 10:31:52', 5, NULL, NULL, NULL, 0, NULL),
(38, 'Ak', '9151499025', 'Amankhan1847ak47@gmail.com', '$2y$12$VYKh49N25A/MwUFhFFKcWuenxNT/BlApAXzKZS79bf5/.KvHSfxFm', 0, 'AK1504', NULL, '2025-12-23 10:33:58', 5, NULL, NULL, NULL, 0, NULL),
(40, 'khanarbaz', '8061639462', 'khanajaz5zz@gmail.con', '$2y$12$ZT.uAFHajBbdv.1sGmCYaOocg9mheJGQ/EGKODxkj14CUMFjPrdsC', 0, 'KHAN8369', 39, '2025-12-23 10:42:46', 5, NULL, NULL, NULL, 0, NULL),
(41, 'ajaz ansari', '9726294535', 'aniyana@gmail.com', '$2y$12$/i7hpMdapckukZYafdDUOO4kMV5OllPBdeTYq9emT.7yrugCpZqha', 0, 'AJAZ7712', 39, '2025-12-23 10:45:33', 5, NULL, NULL, NULL, 0, NULL),
(42, 'Lakshay gahlyan', '8168395376', 'lakshayghalyan01@gmail.com', '$2y$12$oF3f3aIPnuO3f6h9u4oI1uGWklQa67dzmrU.ourN40a7ivo9Z9ljm', 15, 'LAKS1069', NULL, '2025-12-23 10:51:32', 0, '2025-12-23 16:22:32', NULL, NULL, 0, NULL),
(43, 'khan khalil', '8162836282', 'khalillkhan@gmail.com', '$2y$12$n3jt8gYj9rjvCteKKGJekeceNycGJ8qUjXywea2Dvwut4x1cF6cJK', 100, 'KHAN1214', 39, '2025-12-23 10:52:57', 5, NULL, NULL, NULL, 0, NULL),
(44, 'Ramkumar', '+91 78268-21130', 'ramvj2005@gmail.com', '$2y$12$HXteSSZlAxj2h1OS3Y/3def8ujl0XX0Moqs0wII6w/Vr2KLp8ZW6a', 30, 'RAMK2543', NULL, '2025-12-23 10:53:37', 2, '2025-12-23 16:24:40', NULL, NULL, 0, NULL),
(45, 'shaikh khalil', '7646183692', 'khanjjjhan268@yahoo.com', '$2y$12$OpjU54LRls/4nlpxACOj8O5kFHkfV/Jdnzk6Rcu6wOtr/ZI/nYo52', 100, 'SHAI3642', 39, '2025-12-23 10:53:59', 5, NULL, NULL, NULL, 0, NULL),
(46, 'Analya shaikh', '8080110758', 'khalilkhan211@gmail.com', '$2y$12$lUa2sq4PpgfX45om5kwhDek8NDE2W.ZyoAzUWwgt0585zp2l3GpB2', 100, 'ANAL7461', 39, '2025-12-23 10:54:52', 5, NULL, NULL, NULL, 0, NULL),
(54, 'Uma Baruah', '8876392267', 'umabaruah0101@gmail.com', '$2y$12$csTLSEF6DCUGrLOz8z2sYusEmSquKTEAMSkgjSQ2/2wkQIJW9P/4K', 15, 'UMAB3889', NULL, '2025-12-23 10:59:21', 3, '2025-12-23 16:30:06', NULL, NULL, 0, NULL),
(55, 'Ijj', '67864678865', 'idhussainfake@gmail.com', '$2y$12$yzJsGAXNruaJx/y4J24gNObzkudnBXL/sVCEUOgheBSGHXpCV6nVy', 30, 'IJJ2187', NULL, '2025-12-23 10:59:29', 1, '2025-12-23 16:30:36', NULL, NULL, 0, NULL),
(56, 'hhdthe', '2655772449', 'yashh12@gmail.com', '$2y$12$TP03g6iclC/QTroPnWOFO.AeRVv.bWDNXjylWdRbQdjyZ23BxtBkK', 0, 'HHDT5943', NULL, '2025-12-23 11:08:24', 5, NULL, NULL, NULL, 0, NULL),
(57, 'Jashanror55', '9588591616', 'jashanror55@gmail.com', '$2y$12$u68nFPAfKRqzgsxIeuimhOcY2HM.6Gj7GynD/hxYOHfPCQOiFe9IG', 0, 'JASH4473', NULL, '2025-12-23 11:15:46', 5, NULL, NULL, NULL, 0, NULL),
(58, 'Abhishek kumar Gour', '7003596402', 'abhishekkumargourtkd@gmail.com', '$2y$12$gENJZfOm4Bws0IJOfibbPeZUizVhhxzVDSJZfcI2PYuEUgKl4AIVu', 20, 'ABHI4581', NULL, '2025-12-23 11:29:03', 0, '2025-12-23 17:00:41', NULL, NULL, 0, NULL),
(59, 'Karan_grewal', '8984232004', 'karan.int.grewall@gmail.com', '$2y$12$BBkd1fFALWH7H6P9m2hvHOIZI6ot/LlkPNifvh/xzZoNLwOLW1Q6u', 0, 'KARA7785', NULL, '2025-12-23 11:34:07', 5, NULL, NULL, NULL, 0, NULL),
(60, 'Lone Wolf', '8806498487', 'xopanih642@supdrop.com', '$2y$12$fMS.9zMZ3jhdirkriYvGT.prNmScc/8ogtLcC9A6DlpJ0gpYY0d76', 40, 'LONE9056', NULL, '2025-12-23 11:34:44', 0, '2025-12-23 17:05:41', NULL, NULL, 0, NULL),
(61, 'Sp', '61616166q6qy1y', 'hahhwhwhhwbh@gmail.com', '$2y$12$UVvXhPfItre1mVjJjTWw0eRxQm/i/8oifZBKr.PteCNdQUH3UiJLO', 0, 'SP8081', NULL, '2025-12-23 11:46:59', 5, NULL, NULL, NULL, 0, NULL),
(62, 'Sahil', '7709564524', 'sahil131421@gmail.com', '$2y$12$WYIYL8ZLZxYpO2sXAEUD/OvjKnx3EP7mNT0WcZgEPAhViTLMUorq2', 150, 'SAHI5870', NULL, '2025-12-23 11:48:59', 0, '2025-12-23 17:19:56', NULL, NULL, 0, NULL),
(63, 'khan ajayeza', '7957446567', 'khanaj8888@gmail.com', '$2y$12$.HI0YVbGCrYfmQxtMhJ/eOUiojQ3J9X2M15yPhb6ua6bveAM2rdwG', 100, 'KHAN2029', 39, '2025-12-23 11:58:50', 5, NULL, NULL, NULL, 0, NULL),
(71, 'Kasim Sayyed', '9326404133', 'khanfaisal404133@gmail.com', '$2y$12$YbZcg9Eqc9UEGSyX4hY/pOHwTeVFDAit5cmAkTGHq2bxNjRtyXESO', 30, 'KASI3818', NULL, '2025-12-23 12:05:01', 0, '2025-12-23 17:35:30', NULL, NULL, 0, NULL),
(72, 'kureshi aryan', '7584864364', 'ayenkureshi@gmail.com', '$2y$12$rOup.z5ukhpouB14kthqz.srW34V0cZiWRqGIHvpMMGR3y4RjFcMO', 100, 'KURE2871', 39, '2025-12-23 12:08:11', 5, NULL, NULL, NULL, 0, NULL),
(78, 'kailash kuwari', '2682638363', 'kailashahikiki@gmail.com', '$2y$12$7JNfVgBCMRPC7kBQ/iCvCuDyx41oSu0vMDZlR45LwoYhNZwDGzSFK', 0, 'KAIL8447', 39, '2025-12-23 12:25:19', 5, NULL, NULL, NULL, 0, NULL),
(92, 'Anaam Babh', '9797058212', 'anaamsaaat@gmail.com', '$2y$12$LJxdpOE0G3aD0WZh8XSrHOq/id2hQntNXzLHHGhv0m.oLDCdYIRy2', 0, 'ANAA8629', NULL, '2025-12-23 12:44:24', 5, NULL, NULL, NULL, 0, NULL),
(93, 'Armankhan', '9737517872', 'khanbhaipathan62@gmail.com', '$2y$12$utT25dL4s0RUKIRXkXswY.Eu56gDASKoHMVq4W5EgUNKpQ2yCS37u', 35, 'ARMA5765', NULL, '2025-12-23 12:57:41', 0, '2025-12-23 18:29:54', NULL, NULL, 0, NULL),
(94, 'jelly jelly', '7474258965', 'jellyyhelly@gmail.com', '$2y$12$5Ba6ZUSweDblfTAb1CjI2eDr5de1LsKyq5nJw5ZUZrZ6smBuS3wZK', 100, 'JELL7948', 39, '2025-12-23 13:32:36', 5, NULL, NULL, NULL, 0, NULL),
(114, 'Nischay Malhan', '9986808767', 'malhanruchika@67gmail.com', '$2y$12$5vd6Eqyc6XkieeTGByhCSuIQBznJmVo2y/UATIMg5TgIjhhTeQY92', 100, 'NISC9500', 16, '2025-12-23 13:35:12', 5, NULL, NULL, NULL, 0, NULL),
(115, 'Aryan Khan', '9875873456', 'ruchikaaryan@67gmail.com', '$2y$12$EuqrLriogVzmWSk613FXLOkiELX4czgSwzstzPu9dRMsmaLIyyYP.', 130, 'ARYA9879', 16, '2025-12-23 13:37:44', 0, '2025-12-23 19:08:21', NULL, NULL, 0, NULL),
(116, 'jelly fish', '5353646356', 'jellfishing@gmail.com', '$2y$12$PCWZpC25n9Dq002x/LcBA.jLxqpo7YpaEBvgvAbTsW0eDTWIPKJci', 100, 'JELL4711', 39, '2025-12-23 13:43:37', 5, NULL, NULL, NULL, 0, NULL),
(144, 'khan jayez', '7262826398', 'khanajaz996@gmail.com', '$2y$12$BiwZWbKyDJvMqoCVXIDEQuHtDS/azGA2XKbfvKfrsDNghXD6gaRH2', 100, 'KHAN9174', 39, '2025-12-23 13:54:31', 5, NULL, NULL, NULL, 0, NULL),
(204, 'king jhan', '8979797969', 'kha1116@gmail.com', '$2y$12$JUbDj5V88vCwty9oiRaSd.PKldrpj4btp5LAYH6q6ZEV93gj0J2y2', 100, 'KING2010', 39, '2025-12-23 14:40:31', 5, NULL, NULL, NULL, 0, NULL),
(234, 'Zaid khan', '6293639463', 'umarqs3453@gmail.com', '$2y$12$fB8nLXhMpOgW28ZfZBUIteflolHFZMGAA7Abi9CDDWmOFeYhay66G', 100, 'ZAID4893', 39, '2025-12-23 15:09:48', 5, NULL, NULL, NULL, 0, NULL),
(237, 'Khan ajaz', '7851836822', 'umarjq3453@gmail.com', '$2y$12$nhWmAryAYg/wfqS.cdETGurViu.G2VcbClUImdevE28/oE9BDgaWS', 100, 'KHAN7549', 39, '2025-12-23 15:11:37', 5, NULL, NULL, NULL, 0, NULL),
(254, 'Shsjjshs', '7373738373', '1umasrqsh3453@gmail.com', '$2y$12$.kqjDsekaeYdhTLVG1ztKuw1bMEFbp6pH9S1me/bvbjfLHp0OiqN2', 100, 'SHSJ6747', 39, '2025-12-23 15:20:34', 5, NULL, NULL, NULL, 0, NULL),
(382, 'khan', '7282828282', 'djdjaz@gmail.com', '$2y$12$tJ7MzrHNtjNLk5motrS.TO3aqzAZ.78sdpxIE0s9OlJi.aIhMN0AS', 100, 'KHAN1823', 39, '2025-12-23 16:59:53', 5, NULL, NULL, NULL, 0, NULL),
(611, 'Vivek', '9143011013', 'mukeshmondal369@gmail.com', '$2y$12$ggWJvcyQ5T48IrMQF5ckMOmyrYJtE8n082ciYZ58L3Q8J7vLj.by2', 40, 'VIVE3559', NULL, '2025-12-23 19:44:39', 0, '2025-12-24 01:15:14', NULL, NULL, 0, NULL),
(612, 'Tejash Gupta', '7903906426', 'hkr22244@gmail.com', '$2y$12$n7KmxrxukY8FSm7k/LYLv.zradefPjeUBT4pi7cdRHv2NqIqDWsX.', 160, 'TEJA9081', NULL, '2025-12-23 22:58:36', 0, '2025-12-25 10:25:09', NULL, NULL, 0, NULL),
(613, 'Shankar Kumar Swarnkar', '9117320597', 'shankarkumarswarnkar8@gmail.com', '$2y$12$kGJpr5QAt8yIV8xM8pXvQuHn7zx45ZkEWVQKVhjPMp.cBjZw08R2u', 15, 'SHAN2915', NULL, '2025-12-24 00:48:47', 0, '2025-12-24 06:19:19', NULL, NULL, 0, NULL),
(614, 'Aadil', '7622840471', 'patelsaid03@gmail.com', '$2y$12$Af/eCOmw0LbUg1VCoZ364uCR5u9MBdZgqSX8BNAljBfIeO11cZqEy', 25, 'AADI5262', NULL, '2025-12-24 03:30:40', 0, '2025-12-24 09:01:13', NULL, NULL, 0, NULL),
(615, 'Abu', '9100281695', 'smohdabu7865@gmail.com', '$2y$12$HtkswVFogH8D6n62zbTn0ufKxW189v3FTS.1wXorUD57CGYvr2aeG', 60, 'ABU6291', NULL, '2025-12-24 07:22:25', 0, '2025-12-24 17:47:20', NULL, NULL, 0, NULL),
(616, 'Yash', '7349727756', 'fakea0893@gmail.com', '$2y$12$298aWhmd9xYWXLZzKTvP/OsOAg3WFlSb4ZOumb3WdmchZcFRMrALy', 45, 'YASH4543', NULL, '2025-12-24 08:40:44', 0, '2025-12-24 14:11:45', NULL, NULL, 0, NULL),
(617, 'goutam', '8260113922', 'dasg34608@gmail.com', '$2y$12$7QOKqY2C.DGvkcuePHzBzuswbtv6RKBca0zvZyXWZ0KdjY27RdAxO', 80, 'GOUT6512', NULL, '2025-12-24 11:07:07', 0, '2025-12-24 16:37:55', NULL, NULL, 0, NULL),
(618, 'Salman khan', '8264669115', 'salmankhan46512@gmail.com', '$2y$12$Zy8Xf7IOkN8RMhiaqN6uDOO1VFabKsVuKnW7ycaWf6Z/SdLk/9IU.', 5, 'SALM4779', NULL, '2025-12-24 12:01:36', 2, '2025-12-24 17:32:30', NULL, NULL, 0, NULL),
(619, 'Farman', '9027730711', 'samxfarmanmalik@gmail.com', '$2y$12$R1ww.HrEsukRMV0UBgOg7earmUl.UFlHGWEjQzuOYTZrHazpqNsQm', 255, 'FARM2345', NULL, '2025-12-24 16:40:02', 45, '2025-12-25 11:10:33', NULL, NULL, 0, NULL),
(621, 'Farman', '9027730711', 'canva731@oxaam.com', '$2y$12$KJkZKuO2UfJRdeT6gJugy.rZtGoeXd/5v.MNVimRBp6D8TammvJwC', 140, 'FARM7004', 619, '2025-12-24 17:17:30', 0, '2025-12-24 22:48:09', NULL, NULL, 0, NULL),
(622, 'Farman', '9027730711', 'oxaamcgn923@oxaam.com', '$2y$12$269B1M3kjx58GBIQOSEIputNGNtyvalMeEvN/TZjaKwMf7QMtnTZS', 100, 'FARM4730', 619, '2025-12-24 17:19:19', 5, NULL, NULL, NULL, 0, NULL),
(623, 'farman_malik_420', '9027730711', 'sikandarbhartiproduc@gmail.com', '$2y$12$HBaWE8fJbKOoo/EZHKTniOwy5KadU87p/dDSQ3qw./VNmDvUTDx5u', 100, 'FARM6479', 619, '2025-12-24 17:19:45', 5, NULL, NULL, NULL, 0, NULL),
(624, 'Zjsj', 'Jajajaj', 'sikandarbhartipr@gmail.com', '$2y$12$Dd33IDgHf8NFDg66clvqs.CmAfV3GAmAqkhAEp3bDGKxpw1NI1qKK', 100, 'ZJSJ1262', 619, '2025-12-24 17:20:12', 5, NULL, NULL, NULL, 0, NULL),
(625, 'Farman', '9027730711', 'sikandarbharti@gmail.com', '$2y$12$fhf/IWUms0BoPD.uiltE7.ItLQpweatgpzx/0soZTAEef4D1dOywG', 100, 'FARM7794', 619, '2025-12-24 17:22:09', 5, NULL, NULL, NULL, 0, NULL),
(626, 'Farman', '9027730711', 'a@gmail.com', '$2y$12$ctE2eRYYYKD4MXwy5iUmMemCf2Ksc69dHz.321/qx4NVtgC2A8ymK', 100, 'FARM5617', 619, '2025-12-24 17:22:22', 5, NULL, NULL, NULL, 0, NULL),
(627, 'Farman', '9027730711', 'aw@gmail.com', '$2y$12$FXeK6fiSYlJtOrQD8.CXlObD4WbCw/iy1WLehMXmGGMHEzhkzY8aq', 100, 'FARM7570', 619, '2025-12-24 17:22:28', 5, NULL, NULL, NULL, 0, NULL),
(628, 'Farman', '9027730711', 'aaw@gmail.com', '$2y$12$I8bMpKGyxhj7HOZo3wDAau7S/525uC4YPv2FkwYnasHuycUUGQV82', 100, 'FARM6878', 619, '2025-12-24 17:22:44', 5, NULL, NULL, NULL, 0, NULL),
(629, 'Farman', '9027730711', 'akaw@gmail.com', '$2y$12$Eeh6rwR//UqUqKIysDblaOXGf09kn0IphBUqYicPiqKN5YJ.mtZ.e', 100, 'FARM3949', 619, '2025-12-24 17:22:55', 5, NULL, NULL, NULL, 0, NULL),
(630, 'Farman', '9027730711', 'akawa@gmail.com', '$2y$12$OQScF4sLyqVhNXkQS0L6beQTC2WBzUQMNA5qys/FdqrbxTkyKnHDS', 100, 'FARM4022', 619, '2025-12-24 17:23:00', 5, NULL, NULL, NULL, 0, NULL),
(631, 'Farman', '9027730711', 'akawaa@gmail.com', '$2y$12$G9iCUYSPWycBI/5XftCVief20fZSSza3e/dhfNMpcXruXTY4VeY0a', 100, 'FARM8600', 619, '2025-12-24 17:23:05', 5, NULL, NULL, NULL, 0, NULL),
(632, 'Farman', '9027730711', 'akawaqa@gmail.com', '$2y$12$1W2T7R3jMvX1TB5kjOeAFuoaRLemgoCcix18TK0FYYYrdao4FRoW.', 100, 'FARM6773', 619, '2025-12-24 17:23:11', 5, NULL, NULL, NULL, 0, NULL),
(633, 'Farman', '9027730711', 'akawaaqa@gmail.com', '$2y$12$OdLWkyx/fio8.jqA6oYTluMX7/xLZ8adc/wFjIxxrLxPukSt5NleG', 100, 'FARM6555', 619, '2025-12-24 17:23:15', 5, NULL, NULL, NULL, 0, NULL),
(634, 'Farman', '9027730711', 'sjsj@gmail.com', '$2y$12$W4OnxtTLXzR5FbFm.AuElOiFGPjtfvimP.YbAvfHcaL1t/BzslkKS', 100, 'FARM5939', 619, '2025-12-24 17:23:38', 5, NULL, NULL, NULL, 0, NULL),
(635, 'Farman', '9027730711', 'sjsjs@gmail.com', '$2y$12$C1BL1XXQxYUsZKLjIHjn9OSDHCVe5yh0GpnHb.EX.SUCSThZY9GFG', 100, 'FARM9293', 619, '2025-12-24 17:23:44', 5, NULL, NULL, NULL, 0, NULL),
(636, 'Farman', '9027730711', 'sjsjsss@gmail.com', '$2y$12$3QL3CPttFi3exslJIWrZwuJBk6B8sqxYTdoE1Ym.xHjdND0/3Ybhi', 100, 'FARM8425', 619, '2025-12-24 17:23:54', 5, NULL, NULL, NULL, 0, NULL),
(637, 'Farman', '9027730711', 'sjsjsssss@gmail.com', '$2y$12$qESNm5RZvzVZ0LV/1DGfXOSKgm8nF1xvifkRp2xVLZTipb5nh5c3y', 100, 'FARM3159', 619, '2025-12-24 17:23:59', 5, NULL, NULL, NULL, 0, NULL),
(638, 'Farman', '9027730711', 'sjsjssssjsjss@gmail.com', '$2y$12$YyLXiGGTT1tJuJZQB6djy.zBMaoTa4fUGK3KMInQHrxOcmDqw6AZa', 100, 'FARM6376', 619, '2025-12-24 17:24:05', 5, NULL, NULL, NULL, 0, NULL),
(639, 'Farman', '9027730711', 'sjsjsssjsjssjsjss@gmail.com', '$2y$12$5EHpoGhsRUY3mOC60BlW3.D5SV1XIyOXckoyuf1jZd6IfVu0sl3Aa', 100, 'FARM7159', 619, '2025-12-24 17:24:10', 5, NULL, NULL, NULL, 0, NULL),
(640, 'Farman', '9027730711', 'sjsjsssjsjssjjssjsjss@gmail.com', '$2y$12$DnYbgzrGosqdgeF/TgWde.JkfSSODfKqHsnoHV.aamAvYEOA2PvIG', 100, 'FARM5978', 619, '2025-12-24 17:24:18', 5, NULL, NULL, NULL, 0, NULL),
(641, 'Farman', '9027730711', 'sjsjsssjsjssjjjsjsssjsjss@gmail.com', '$2y$12$09FB.y9NJPMlvKLl/b1Dh.3Tv7fRipQHD5ja5GkfGf0HYESEMzFme', 100, 'FARM3692', 619, '2025-12-24 17:24:23', 5, NULL, NULL, NULL, 0, NULL),
(642, 'kureshi sahil', '2882827384', 'obikarbaz@gmail.com', '$2y$12$So/rahvzo.ouypSGR9.HHeOCTRz1KwR1d6qPTrQQha43RLkcoE992', 0, 'KURE3613', NULL, '2025-12-24 17:49:19', 5, NULL, '103.174.118.245', NULL, 0, NULL),
(643, 'kureshi azad', '8080110755', '31arbaz@gmail.com', '$2y$12$swGkhUKH2AL92TdgYCwQye96ovKrWrWCzpZ0uXU4IOrBi7y6DOWH.', 125, 'KURE7883', NULL, '2025-12-24 17:54:17', 2, '2025-12-24 21:50:27', '2a0d:5600:4d:100c:a488:1407:a7fc:3830', NULL, 0, NULL),
(644, 'ajaz khan', '7272727272', 'khanajdjjazz596@gmail.com', '$2y$12$KRoWcs1lhTlbVu9b7oUUEuOpHtdN7W5JScjV8rZ53Kf9Y6I6UWsZm', 10, 'AJAZ7674', 643, '2025-12-24 17:56:19', 5, NULL, '2001:ac8:22:97:fb08:8e91:5952:9166', NULL, 0, NULL),
(645, 'hassi khan', '7878562573', 'khanjarkhand@gmail.com', '$2y$12$b1uhFyl2VSkSsZTQy.Eeiey4gl01s.As/Gi20daeR76MDAg38oajC', 10, 'HASS5879', 643, '2025-12-24 17:58:03', 5, NULL, '2a0d:5600:4d:100b:ae18:928:6234:abfb', NULL, 0, NULL),
(646, 'Farman', '9027730711', 'samxfarman@gmail.com', '$2y$12$6QsN05v80hNM6NPyBUaEL.DiSMwWOJI9K72TYURAHKfpyUiv//Fay', 10, 'FARM9251', 619, '2025-12-24 20:14:59', 5, NULL, '2409:4085:9c93:67f0:b893:d8ff:fec6:f0a9', NULL, 0, NULL),
(647, 'Cyber red', '9501138511', 'cyberxredx@gmail.com', '$2y$12$SOO9PMemA/tfAT3DfOaWgusd0VwUkCtE81.IpCWGwbWzcQxAO/Som', 10, 'CYBE5870', NULL, '2025-12-24 23:08:37', 2, '2025-12-24 23:09:26', '2401:4900:46cc:6799:0:37:73db:5c01', NULL, 0, NULL),
(648, 'Hei Hoys', '7205348850', 'kumabhi7i7@gmail.com', '$2y$12$HP2xOm8sHQkLCP3tdzz4bu06yqUNJFSIpMUiFUWOCbMC3FnAE/n5a', 60, 'HEIH8064', 16, '2025-12-25 05:51:29', 0, '2025-12-25 05:52:36', '2405:201:a000:b068:81ab:2ae8:4558:5bce', NULL, 0, NULL),
(649, 'Sumit Pal', '9478330031', 'musicsteam1@gmail.com', '$2y$12$8KDBbgdi2ghKhQKSD9W2l.KJ2hm66Kihco9OL3RYx79mYrwIEu4mu', 0, 'SUMI3246', NULL, '2025-12-25 05:57:06', 5, NULL, '2409:40d1:1b:9ac0:8000::', NULL, 0, NULL),
(650, 'Satyam Verma', '9335029684', 'satyamverma9932@gmail.com', '$2y$12$0mDt1b2UqpyE3z4vS.OA6ePKC53/Ig4AcAFlCXKWBDVfDrmkVVAue', 5, 'SATY9282', NULL, '2025-12-25 06:20:19', 5, '2025-12-25 06:21:50', '2409:40e3:101f:7432:2444:1eff:fedc:11f3', NULL, 0, NULL),
(651, 'Ansh solanki', '8962987489', 'anshpratapsinghsolanki39@gmail.com', '$2y$12$TIwmR/2GAJ.ApmNiXeclBexolLPYHIa6bDb2ah7T71944gwHM41Nm', 240, 'ANSH8441', NULL, '2025-12-25 08:02:23', 2, '2025-12-26 14:02:15', '2401:4900:313a:e8fd:b1de:9df3:6cfe:301e', NULL, 0, NULL),
(652, 'Sufi', '8275664633', 'sufiyankhan3091@gamil.com', '$2y$12$NohjU.4.Gv5sY6CY3mG00uuroLUlNi.Y3644gIBYCydFB9kqKfjwq', 50, 'SUFI9036', NULL, '2025-12-25 08:19:28', 0, '2025-12-25 08:20:59', '2409:40c2:21:55ec:1460:97ff:fede:ddde', NULL, 0, NULL),
(653, 'Aditya Brar', '9306935462', 'adityabrar123@gmail.com', '$2y$12$3M5qG7HqjbVTs2LikSpfXuMT6eu8EgYmZ.FO97k0VFrKwMVphCYda', 40, 'ADIT5468', NULL, '2025-12-25 08:35:55', 0, '2025-12-25 08:36:54', '2409:40d6:10:1270:1a9b:6882:f14e:c0f9', NULL, 0, NULL),
(654, 'Zombieverse season 2', 'FARM2345', 'sm9816519@gmail.com', '$2y$12$9HbrBZQNuVxuRKM0OIgqduDS0RlH1Y9o6HxRIsutW4F.vr5vDNmWK', 10, 'ZOMB5000', 619, '2025-12-25 11:11:44', 5, NULL, '2409:40d2:1022:c0e5:548d:97ff:fe68:47a9', NULL, 0, NULL),
(655, 'Rakhi', '7014991552', 'rakhirangilii@gmail.com', '$2y$12$VgMQNmwsdDNPp2H3Bw9JQOWF8wV8r0z53UXZNShrBY2/cmWZAbpGS', 580, 'RAKH5722', NULL, '2025-12-25 11:59:48', 0, '2025-12-26 16:10:41', '2409:40d4:11e4:33da:8000::', NULL, 0, NULL),
(656, 'Dipankar Banik', '7585872879', 'banikdipankar64@gmail.com', '$2y$12$XLAR4uYP2lzvLfqWoWZXheXrK0YiGFZ1PB.3iae0W1CawXnMuu9ty', 0, 'DIPA6272', NULL, '2025-12-25 12:58:26', 5, NULL, '2401:4900:88ad:f967:de6:c2cc:fc58:c3af', NULL, 0, NULL),
(657, 'Jashanchodey09191', '7015305914', 'jashanror59@gmail.com', '$2y$12$5ehgjsRx8a8JDEu9DdiicuRTParGlewbcwwWKzHMa35ZMvLcbXRu.', 15, 'JASH8959', NULL, '2025-12-25 13:50:06', 4, '2025-12-25 13:51:28', '2001:569:6e32:86bc:19e7:9e2b:5c2e:aada', NULL, 0, NULL),
(658, 'Pappu sharma', '8278688006', 'pappulalsharma290@gmail.com', '$2y$12$8UKhcM.zZkruPo1V.LUwXeC/ynabQgBPs5zK3KL90.x9mV1Xplgy6', 120, 'PAPP3035', 27, '2025-12-25 14:40:49', 2, '2025-12-25 14:44:32', '2401:4900:aeeb:8688:d5f1:d102:7e11:19a', NULL, 0, NULL),
(659, 'Sahaj', '9510688926', 'varnirajgate@gmail.com', '$2y$12$NqMmrSb/gR7LK4lFO1GaAe1Zf7Es5bSfW3BwNJs5jmWvyowMA/FUW', 100, 'SAHA8958', NULL, '2025-12-25 16:15:57', 0, '2025-12-25 16:20:11', '150.107.232.249', NULL, 0, NULL),
(660, 'Ansh solanki', '8962987489', 'solankisarojsingh@gmail.com', '$2y$12$B5iu0rC0EeOVUGOCutlPZ.vy.Ou00Ebrp68CsiHiBAZnKp9H2HBBO', 65, 'ANSH2129', 651, '2025-12-26 14:05:14', 2, '2025-12-27 04:17:59', '2401:4900:a825:b5cf:993d:9b3:589f:6c4c', NULL, 0, NULL),
(661, 'Ansh', '9755347203', 'shshshsnzn7337@gmail.com', '$2y$12$pFZlQu/3XAanq/TkOE7qr.6oG68wENRO8LBQd7aHGRW0xH.v2yYoC', 20, 'ANSH7209', 651, '2025-12-26 14:13:55', 4, '2025-12-26 14:14:16', '2409:40c4:f:85dd:e1a8:1d2:e5e0:be2e', NULL, 0, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `checkins`
--
ALTER TABLE `checkins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `coupon_usage`
--
ALTER TABLE `coupon_usage`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_usage` (`user_id`,`coupon_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`setting_key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `referral_code` (`referral_code`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `checkins`
--
ALTER TABLE `checkins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `coupon_usage`
--
ALTER TABLE `coupon_usage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=143;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=662;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
