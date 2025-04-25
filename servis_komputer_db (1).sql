-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 25 Apr 2025 pada 02.27
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

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
-- Struktur dari tabel `pesan`
--

CREATE TABLE `pesan` (
  `id` int(11) NOT NULL,
  `tiket_id` int(11) NOT NULL,
  `pengirim_id` int(11) NOT NULL,
  `isi_pesan` text NOT NULL,
  `waktu_kirim` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tiket_servis`
--

CREATE TABLE `tiket_servis` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `device_type` enum('hp','laptop','komputer') NOT NULL,
  `keluhan` text NOT NULL,
  `status` enum('pending','dikonfirmasi_admin','diproses_teknisi','selesai_diperbaiki','menunggu_pembayaran','lunas') DEFAULT 'pending',
  `biaya_servis` decimal(10,2) DEFAULT 0.00,
  `keterangan_teknisi` text DEFAULT NULL,
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `tanggal_dibuat` datetime DEFAULT current_timestamp(),
  `tanggal_selesai` datetime DEFAULT NULL,
  `teknisi_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tiket_servis`
--

INSERT INTO `tiket_servis` (`id`, `user_id`, `device_type`, `keluhan`, `status`, `biaya_servis`, `keterangan_teknisi`, `bukti_pembayaran`, `tanggal_dibuat`, `tanggal_selesai`, `teknisi_id`) VALUES
(1, 4, 'laptop', 'engga bisa hurung pa', 'lunas', 400000.00, 'kerusakan nya harus ganti mesin pa', '680acfaee8dcf_Capture32.PNG', '2025-04-24 20:14:18', '2025-04-25 06:57:51', 3),
(2, 4, 'hp', 'alamak', 'lunas', 4000.00, '5000', '680ac5cf77c08_Billahi Robby store (1).png', '2025-04-24 20:17:48', '2025-04-25 06:17:31', 3),
(3, 4, 'laptop', 'hai ini tidak bisa hurung', 'selesai_diperbaiki', 50000.00, 'anjay', NULL, '2025-04-25 07:19:18', NULL, 5);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `role` enum('admin','teknisi','pelanggan') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `nama`, `no_hp`, `role`) VALUES
(1, 'admin', 'admin123', 'Admin', '08123456789', 'admin'),
(3, 'teknik', 'taknik', 'alhamdulilah', '0123456789', 'teknisi'),
(4, 'ajy', 'ajy', 'alamak', '09787654', 'pelanggan'),
(5, 'jasa', 'jasa', 'billahi robby', '085795203791', 'teknisi');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `pesan`
--
ALTER TABLE `pesan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tiket_id` (`tiket_id`),
  ADD KEY `pengirim_id` (`pengirim_id`);

--
-- Indeks untuk tabel `tiket_servis`
--
ALTER TABLE `tiket_servis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `teknisi_id` (`teknisi_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `pesan`
--
ALTER TABLE `pesan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tiket_servis`
--
ALTER TABLE `tiket_servis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `pesan`
--
ALTER TABLE `pesan`
  ADD CONSTRAINT `pesan_ibfk_1` FOREIGN KEY (`tiket_id`) REFERENCES `tiket_servis` (`id`),
  ADD CONSTRAINT `pesan_ibfk_2` FOREIGN KEY (`pengirim_id`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `tiket_servis`
--
ALTER TABLE `tiket_servis`
  ADD CONSTRAINT `tiket_servis_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `tiket_servis_ibfk_2` FOREIGN KEY (`teknisi_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
