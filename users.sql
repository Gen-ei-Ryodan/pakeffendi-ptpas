-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 03, 2026 at 08:32 PM
-- Server version: 10.6.27-MariaDB-cll-lve
-- PHP Version: 8.4.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ptpasonl_web`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `email` varchar(191) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `role` varchar(191) NOT NULL DEFAULT 'admin',
  `photo_path` varchar(191) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `role`, `photo_path`) VALUES
(1, 'Super Admin', 'admin@pas.local', NULL, '$2y$12$R3EOqA584nuHDlAmYOmPTeRmDkZHiFrXHx7GbEPnJqzxBWFfKZA2W', NULL, '2026-05-07 00:30:21', '2026-05-07 00:30:21', 'super admin', NULL),
(2, 'Sales Demo', 'sales@example.com', NULL, '$2y$12$z8zHhiAFj6ufNtFf7/2vGOPjk/wT8/7bCL2xX9MOCAlSnJc/Q0ZKe', NULL, '2026-05-07 00:30:21', '2026-05-07 00:30:21', 'sales', NULL),
(4, 'Tomo', 'tomo@ptpasonline.com', NULL, '$2y$12$BppwW6nzn7dL9QXfstV0rePve/XpIhMCYM.uQJcaIYW7Mm1xldIgi', NULL, '2026-06-17 04:50:03', '2026-06-17 04:50:03', 'sales', NULL),
(5, 'Effendi', 'effendias@gmail.com', NULL, '$2y$12$SKXuPdhQDBtck34fXYMsbOKTfk7LB7Z/CAA1gzfMlYWJ7c1PLYyRa', NULL, '2026-06-17 04:50:40', '2026-06-17 04:53:44', 'super admin', NULL),
(6, 'sendy', 'sendy@ptpasonline.com', NULL, '$2y$12$NakHtiVFe.1UPVPv1zc8E.fZWq81WVY4fUa.V0AV8QlO0XWWr.nTW', 'ovaC453AxNXwg0EtogdwQYg0wkoUeLk2KVJ626ZtxPx93FmPgRSE96k1KeA9', '2026-06-17 04:53:26', '2026-06-17 04:56:02', 'sales', NULL),
(7, 'Mertha', 'mertha@ptpasonline.com', NULL, '$2y$12$qF570o9Uljh3qcyw2cX/8eiwT4jOc01lZo4ZSBxS16wPua5/h9yta', 'JsjqsQ2SjD8cv8NR7mLxy87jUgwPESJfEjNja7FmJ3OOQkgSGPZrFjrLo2Gf', '2026-06-17 04:59:58', '2026-06-17 04:59:58', 'sales', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_role_index` (`role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
