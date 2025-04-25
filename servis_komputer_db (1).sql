-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 25, 2025 at 04:28 AM
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
-- Table structure for table `pesan`
--

CREATE TABLE `pesan` (
  `id` int NOT NULL,
  `tiket_id` int NOT NULL,
  `pengirim_id` int NOT NULL,
  `isi_pesan` text COLLATE utf8mb4_general_ci NOT NULL,
  `waktu_kirim` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(5, 4, 'komputer', 'alamak', 'menunggu_pembayaran', '99999999.00', 'karena jawa itu mahal juga hama', '680b04e9e4a26_PPLG.jpg', '2025-04-25 09:25:21', NULL, 5, 'jawa itu bukan bukti pembayaran'),
(6, 4, 'komputer', 'mau rakit bang', 'lunas', '65656.00', 'jawa', '680afea2a3c1d_Screenshot 2024-11-07 092331.png', '2025-04-25 10:13:35', '2025-04-25 10:17:24', 5, NULL),
(7, 4, 'hp', 'ganti kontol', 'selesai_diperbaiki', '400000.00', 'hai ini rusak nya di semua sparepart', NULL, '2025-04-25 11:04:48', NULL, 3, NULL),
(8, 6, 'hp', 'mobil bugati saya rusak', 'diproses_teknisi', '0.00', NULL, NULL, '2025-04-25 11:17:19', NULL, 5, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `no_hp` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('admin','teknisi','pelanggan') COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `nama`, `no_hp`, `role`) VALUES
(1, 'admin', 'admin123', 'Admin', '08123456789', 'admin'),
(3, 'teknik', 'taknik', 'alhamdulilah', '0123456789', 'teknisi'),
(4, 'ajy', 'ajy', 'alamak', '09787654', 'pelanggan'),
(5, 'jasa', 'jasa', 'billahi robby', '085795203791', 'teknisi'),
(6, 'bugati', 'bugati', 'yandi oktavian', '08952348953', 'pelanggan');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pesan`
--
ALTER TABLE `pesan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tiket_id` (`tiket_id`),
  ADD KEY `pengirim_id` (`pengirim_id`);

--
-- Indexes for table `tiket_servis`
--
ALTER TABLE `tiket_servis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `teknisi_id` (`teknisi_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pesan`
--
ALTER TABLE `pesan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tiket_servis`
--
ALTER TABLE `tiket_servis`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `pesan`
--
ALTER TABLE `pesan`
  ADD CONSTRAINT `pesan_ibfk_1` FOREIGN KEY (`tiket_id`) REFERENCES `tiket_servis` (`id`),
  ADD CONSTRAINT `pesan_ibfk_2` FOREIGN KEY (`pengirim_id`) REFERENCES `users` (`id`);

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
