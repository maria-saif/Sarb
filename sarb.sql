-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: May 04, 2026 at 09:04 AM
-- Server version: 8.0.40
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sarb`
--

-- --------------------------------------------------------

--
-- Table structure for table `analyses`
--

CREATE TABLE `analyses` (
  `id` int NOT NULL,
  `upload_id` int NOT NULL,
  `result` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `analysis_history`
--

CREATE TABLE `analysis_history` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `upload_id` int DEFAULT NULL,
  `filename` varchar(255) NOT NULL,
  `transcript` text,
  `suspicious_keywords` text,
  `audio_path` varchar(255) DEFAULT NULL,
  `result` varchar(50) DEFAULT 'Normal',
  `risk_level` float DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `analysis_history`
--

INSERT INTO `analysis_history` (`id`, `user_id`, `upload_id`, `filename`, `transcript`, `suspicious_keywords`, `audio_path`, `result`, `risk_level`, `created_at`) VALUES
(1, 0, NULL, 'fraud_advanced_ar.m4a', 'السلام عليكم ورحمة الله معك الملازم خالد من قسم الجرائم الالكترونية حياك الله رصدنا تحويل مالي غير مصرح به باسمك تطبيق البنك أيوه التحويل غير مصرح بس عشان نتحقق من هويتك قبل تجميد الحساب لازم ترسل للكوتا اللي وصلت عن ذاك المخرج أيوه أحسنت الله', '[\"\\u062a\\u062d\\u0642\\u0642 \\u0645\\u0646 \\u0647\\u0648\\u064a\\u062a\\u0643\",\"\\u062a\\u062c\\u0645\\u064a\\u062f \\u0627\\u0644\\u062d\\u0633\\u0627\\u0628\",\"\\u062d\\u0633\\u0627\\u0628\",\"\\u0627\\u0644\\u0628\\u0646\\u0643\",\"\\u062a\\u062d\\u0642\\u0642\",\"\\u062a\\u062d\\u0648\\u064a\\u0644\"]', '/Sarb/uploads/fraud_advanced_ar.m4a', '🟡 Low Risk', 32, '2026-05-03 18:29:34'),
(2, 0, NULL, 'fraud_basic_ar.m4a', 'مرحبا السلام عليكم معك من شركة الاتصالات جائزة نقدية قدره خمسمائة ريال عماني نعم فقط نحتاج رقم البطاقة البولسكية عشان الدم بلغ صورا لحسابك البنكي هو الرقم أيوه أحسنت', '[\"\\u062d\\u0633\\u0627\\u0628\",\"\\u062d\\u0633\\u0627\\u0628\\u0643\",\"\\u062d\\u0633\\u0627\\u0628\\u0643 \\u0627\\u0644\\u0628\\u0646\\u0643\\u064a\",\"\\u0627\\u0644\\u0628\\u0646\\u0643\"]', '/Sarb/uploads/fraud_basic_ar.m4a', '🟡 Low Risk', 20, '2026-05-03 18:48:40');

-- --------------------------------------------------------

--
-- Table structure for table `keywords`
--

CREATE TABLE `keywords` (
  `id` int NOT NULL,
  `keyword` varchar(100) NOT NULL,
  `weight` float DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `live_sessions`
--

CREATE TABLE `live_sessions` (
  `id` int NOT NULL,
  `session_name` varchar(255) DEFAULT NULL,
  `started_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `ended_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stats`
--

CREATE TABLE `stats` (
  `id` int NOT NULL,
  `metric_name` varchar(100) NOT NULL,
  `metric_value` float DEFAULT '0',
  `recorded_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `uploads`
--

CREATE TABLE `uploads` (
  `id` int NOT NULL,
  `user_id` int NOT NULL DEFAULT '0',
  `filename` varchar(255) NOT NULL,
  `filepath` varchar(255) NOT NULL,
  `uploaded_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `uploads`
--

INSERT INTO `uploads` (`id`, `user_id`, `filename`, `filepath`, `uploaded_at`) VALUES
(1, 0, 'fraud_advanced_ar.m4a', 'uploads/fraud_advanced_ar.m4a', '2026-04-30 13:00:02'),
(2, 0, 'fraud_basic_ar.m4a', 'uploads/fraud_basic_ar.m4a', '2026-04-30 13:03:39'),
(3, 0, 'fraud_medium_ar.m4a', 'uploads/fraud_medium_ar.m4a', '2026-04-30 14:17:07'),
(4, 0, 'fraud_advanced_ar.m4a', 'uploads/fraud_advanced_ar.m4a', '2026-05-03 08:43:38'),
(5, 0, 'fraud_advanced_ar.m4a', 'uploads/fraud_advanced_ar.m4a', '2026-05-03 09:48:39'),
(6, 0, 'fraud_basic_ar.m4a', 'uploads/fraud_basic_ar.m4a', '2026-05-03 10:04:37'),
(7, 0, 'fraud_advanced_ar.m4a', 'uploads/fraud_advanced_ar.m4a', '2026-05-03 10:57:02'),
(8, 0, 'fraud_medium_ar.m4a', 'uploads/fraud_medium_ar.m4a', '2026-05-03 10:57:21'),
(9, 0, 'fraud_advanced_ar.m4a', 'uploads/fraud_advanced_ar.m4a', '2026-05-03 11:17:56'),
(10, 0, 'fraud_advanced_ar.m4a', 'uploads/fraud_advanced_ar.m4a', '2026-05-03 14:06:18'),
(11, 0, 'fraud_medium_ar.m4a', 'uploads/fraud_medium_ar.m4a', '2026-05-03 14:06:44'),
(12, 0, 'fraud_basic_ar.m4a', 'uploads/fraud_basic_ar.m4a', '2026-05-03 14:07:06'),
(13, 0, 'fraud_advanced_ar.m4a', 'uploads/fraud_advanced_ar.m4a', '2026-05-03 14:29:27'),
(14, 0, 'fraud_basic_ar.m4a', 'uploads/fraud_basic_ar.m4a', '2026-05-03 14:48:34');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `analyses`
--
ALTER TABLE `analyses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `upload_id` (`upload_id`);

--
-- Indexes for table `analysis_history`
--
ALTER TABLE `analysis_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `keywords`
--
ALTER TABLE `keywords`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `live_sessions`
--
ALTER TABLE `live_sessions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stats`
--
ALTER TABLE `stats`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `uploads`
--
ALTER TABLE `uploads`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `analyses`
--
ALTER TABLE `analyses`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `analysis_history`
--
ALTER TABLE `analysis_history`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `keywords`
--
ALTER TABLE `keywords`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `live_sessions`
--
ALTER TABLE `live_sessions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stats`
--
ALTER TABLE `stats`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `uploads`
--
ALTER TABLE `uploads`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `analyses`
--
ALTER TABLE `analyses`
  ADD CONSTRAINT `analyses_ibfk_1` FOREIGN KEY (`upload_id`) REFERENCES `uploads` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
