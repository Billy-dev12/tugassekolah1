-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 25, 2025 at 03:25 AM
-- Server version: 8.0.30
-- PHP Version: 8.2.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `servis_komputer_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `tiket_servis`
--

CREATE TABLE `tiket_servis` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `device_type` enum('hp','laptop','komputer') COLLATE utf8mb4_general_ci NOT NULL,
  `keluhan` text COLLATE utf8mb4_general_ci NOT NULL,
  `status` enum('pending','dikonfirmasi_admin','diproses_teknisi','selesai_diperbaiki','menunggu_pembayaran','lunas','servis_dibatalkan') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `biaya_servis` decimal(10,2) DEFAULT '0.00',
  `keterangan_teknisi` text COLLATE utf8mb4_general_ci,
  `bukti_pembayaran` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tanggal_dibuat` datetime DEFAULT CURRENT_TIMESTAMP,
  `tanggal_selesai` datetime DEFAULT NULL,
  `teknisi_id` int DEFAULT NULL,
  `keterangan_admin` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tiket_servis`
--

INSERT INTO `tiket_servis` (`id`, `user_id`, `device_type`, `keluhan`, `status`, `biaya_servis`, `keterangan_teknisi`, `bukti_pembayaran`, `tanggal_dibuat`, `tanggal_selesai`, `teknisi_id`, `keterangan_admin`) VALUES
(1, 4, 'laptop', 'engga bisa hurung pa', 'lunas', '400000.00', 'kerusakan nya harus ganti mesin pa', '680acfaee8dcf_Capture32.PNG', '2025-04-24 20:14:18', '2025-04-25 06:57:51', 3, ''),
(4, 4, 'hp', 'pak hp saya rusak tidak bisa di betulkan', 'menunggu_pembayaran', '8000000.00', 'beli hape baru lagi pak', '680af5a7dd8a3_Screenshot 2024-11-05 090416.png', '2025-04-25 08:59:49', NULL, 5, 'bukti pembayaran tidak ada'),
(5, 4, 'komputer', 'alamak', 'selesai_diperbaiki', '99999999.00', 'karena jawa itu mahal juga hama', NULL, '2025-04-25 09:25:21', NULL, 5, 'jawa itu bukan bukti pembayaran'),
(6, 4, 'komputer', 'mau rakit bang', 'lunas', '65656.00', 'jawa', '680afea2a3c1d_Screenshot 2024-11-07 092331.png', '2025-04-25 10:13:35', '2025-04-25 10:17:24', 5, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tiket_servis`
--
ALTER TABLE `tiket_servis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `teknisi_id` (`teknisi_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tiket_servis`
--
ALTER TABLE `tiket_servis`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tiket_servis`
--
ALTER TABLE `tiket_servis`
  ADD CONSTRAINT `tiket_servis_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `tiket_servis_ibfk_2` FOREIGN KEY (`teknisi_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
