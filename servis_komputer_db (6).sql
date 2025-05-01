-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 01 Bulan Mei 2025 pada 13.19
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
-- Struktur dari tabel `rating_teknisi`
--

CREATE TABLE `rating_teknisi` (
  `id_rating` int(11) NOT NULL,
  `id_teknisi` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_tiket` int(11) NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `komentar` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `rating_teknisi`
--

INSERT INTO `rating_teknisi` (`id_rating`, `id_teknisi`, `id_user`, `id_tiket`, `rating`, `komentar`, `created_at`) VALUES
(1, 2, 1, 2, 5, 'mantap proses nya cepet', '2025-05-01 10:24:14'),
(2, 2, 5, 3, 3, 'lumayan tapi kurang rapih pemasangan nya', '2025-05-01 11:13:42');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tiket_servis`
--

CREATE TABLE `tiket_servis` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `device_type` enum('hp','laptop','komputer') NOT NULL,
  `keluhan` text NOT NULL,
  `status` enum('pending','dikonfirmasi_admin','diproses_teknisi','selesai_diperbaiki','menunggu_pembayaran','lunas','servis_dibatalkan') DEFAULT 'pending',
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
(1, 1, 'hp', 'rusak layar pa', 'dikonfirmasi_admin', 0.00, NULL, NULL, '2025-04-27 19:05:38', NULL, 2),
(2, 1, 'komputer', 'vga nya rusak cik', 'lunas', 70000.00, 'ada cuma diganti komponen kecil nya aja pa', '6813466779860_Capture32.PNG', '2025-05-01 16:52:43', '2025-05-01 17:13:33', 2),
(3, 5, 'hp', 'pa hp saya rusakk', 'lunas', 190000.00, 'ini ganti layar pa', '681356b802e97_ttrCapture.PNG', '2025-05-01 18:01:46', '2025-05-01 18:13:07', 2);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `role` enum('admin','teknisi','pelanggan') NOT NULL,
  `spesialisasi` varchar(100) DEFAULT 'Umum',
  `lokasi_teknisi` varchar(100) DEFAULT NULL,
  `foto_profil` varchar(255) DEFAULT NULL,
  `total_servis` int(11) DEFAULT 0,
  `rata_rata_rating` decimal(3,2) DEFAULT 0.00,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_verified` tinyint(1) DEFAULT 0,
  `dokumen_keahlian` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `nama`, `email`, `no_hp`, `alamat`, `role`, `spesialisasi`, `lokasi_teknisi`, `foto_profil`, `total_servis`, `rata_rata_rating`, `deskripsi`, `created_at`, `is_verified`, `dokumen_keahlian`) VALUES
(1, 'ajy', 'ajy', 'billahi', NULL, '087654372813', NULL, 'pelanggan', 'Umum', NULL, NULL, 0, 0.00, NULL, '2025-04-27 02:13:38', 0, NULL),
(2, 'jasa', 'jasa', 'Billahi Robby', 'lahjujuraja@gmail.com', '085795203791', 'kota bandung kec kutawaringin', 'teknisi', 'Handphone', 'bandung', '../uploads/profil/Billahi Robby store (1).png', 5, 4.00, NULL, '2025-04-27 02:15:15', 1, NULL),
(3, 'admin', 'admin123', 'admin', NULL, '08978987756', NULL, 'admin', 'Umum', NULL, NULL, 0, 0.00, NULL, '2025-04-27 02:16:10', 0, NULL),
(4, 'jawa', 'jawa', 'billahi Jawa', 'lahjujuraja2@gmail.com', '0879675234', NULL, 'teknisi', 'Komputer', 'di bandung sayati', '../uploads/profil/ttrCapture.PNG', 0, 0.00, 'bisa merakit membetulkan dan servis software', '2025-05-01 10:58:05', 1, NULL),
(5, 'aidil', 'aidil', 'aidil hermawan putra', 'aidilmarhas123@gmail.com', '087687543219', NULL, 'pelanggan', 'Umum', NULL, NULL, 0, 0.00, NULL, '2025-05-01 11:01:01', 0, NULL);

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
-- Indeks untuk tabel `rating_teknisi`
--
ALTER TABLE `rating_teknisi`
  ADD PRIMARY KEY (`id_rating`),
  ADD KEY `id_teknisi` (`id_teknisi`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_tiket` (`id_tiket`);

--
-- Indeks untuk tabel `tiket_servis`
--
ALTER TABLE `tiket_servis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `teknisi_id` (`teknisi_id`),
  ADD KEY `idx_tiket_status` (`status`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_user_role` (`role`),
  ADD KEY `idx_teknisi_rating` (`rata_rata_rating`),
  ADD KEY `idx_teknisi_role` (`role`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `pesan`
--
ALTER TABLE `pesan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `rating_teknisi`
--
ALTER TABLE `rating_teknisi`
  MODIFY `id_rating` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
  ADD CONSTRAINT `pesan_ibfk_1` FOREIGN KEY (`tiket_id`) REFERENCES `tiket_servis` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pesan_ibfk_2` FOREIGN KEY (`pengirim_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `rating_teknisi`
--
ALTER TABLE `rating_teknisi`
  ADD CONSTRAINT `rating_teknisi_ibfk_1` FOREIGN KEY (`id_teknisi`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rating_teknisi_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rating_teknisi_ibfk_3` FOREIGN KEY (`id_tiket`) REFERENCES `tiket_servis` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tiket_servis`
--
ALTER TABLE `tiket_servis`
  ADD CONSTRAINT `tiket_servis_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tiket_servis_ibfk_2` FOREIGN KEY (`teknisi_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
