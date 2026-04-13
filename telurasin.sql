-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql113.infinityfree.com
-- Generation Time: Apr 13, 2026 at 09:05 AM
-- Server version: 11.4.10-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_41651168_telurasin125`
--

-- --------------------------------------------------------

--
-- Table structure for table `alokasi_offline`
--

CREATE TABLE `alokasi_offline` (
  `id` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `keterangan` varchar(100) NOT NULL,
  `waktu` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `alokasi_offline`
--

INSERT INTO `alokasi_offline` (`id`, `jumlah`, `keterangan`, `waktu`) VALUES
(1, 5, 'toko aceng berkah', '2026-04-05'),
(2, 267, 'toko aceng berkah', '2026-04-05'),
(3, 100, '', '2026-04-13'),
(4, 100, '', '2026-04-13');

-- --------------------------------------------------------

--
-- Table structure for table `alokasi_online`
--

CREATE TABLE `alokasi_online` (
  `id` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `keterangan` varchar(100) NOT NULL,
  `waktu` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `alokasi_online`
--

INSERT INTO `alokasi_online` (`id`, `jumlah`, `keterangan`, `waktu`) VALUES
(1, 167, 'stok marketplace', '2026-04-05'),
(2, 100, '', '2026-04-13'),
(3, 100, '', '2026-04-13');

-- --------------------------------------------------------

--
-- Table structure for table `produksi`
--

CREATE TABLE `produksi` (
  `id` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `keterangan` varchar(100) NOT NULL,
  `waktu` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `produksi`
--

INSERT INTO `produksi` (`id`, `jumlah`, `keterangan`, `waktu`) VALUES
(1, 8, 'pagi', '2026-03-09'),
(2, 10, '', '2026-04-05'),
(3, 5, '', '2026-04-05'),
(4, 2, '', '2026-04-05'),
(5, 250, '', '2026-04-13'),
(6, 264, '', '2026-04-13'),
(7, 400, '', '2026-04-13'),
(8, 200, '', '2026-04-13');

-- --------------------------------------------------------

--
-- Table structure for table `stokmentah`
--

CREATE TABLE `stokmentah` (
  `id` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `tidak_lolos` int(11) NOT NULL DEFAULT 0,
  `jenis` enum('masuk','keluar') NOT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `waktu` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stokmentah`
--

INSERT INTO `stokmentah` (`id`, `jumlah`, `tidak_lolos`, `jenis`, `keterangan`, `waktu`) VALUES
(1, 145, 13, 'masuk', 'dari supplier a', '2026-02-04'),
(2, 23, 0, 'masuk', 'abeng', '2026-02-23'),
(3, 23, 0, 'masuk', 'abeng', '2026-02-23'),
(4, 23, 0, 'masuk', 'abeng', '2026-02-23'),
(6, 12, 0, 'masuk', 'abeng', '2026-02-24'),
(7, 23, 2, 'masuk', 'tatang egg', '2026-03-02'),
(8, 25, 4, 'masuk', 'tatang egg', '2026-03-02'),
(9, 35, 6, 'masuk', 'abeng', '2026-03-02'),
(10, 1, 0, 'keluar', 'ditilep asep buat makan', '2026-03-02'),
(11, 8, 0, 'keluar', 'Produksi: pagi (Gagal QC: 0)', '2026-03-09'),
(12, 10, 0, 'keluar', 'Produksi:  (Gagal QC: 0)', '2026-04-05'),
(13, 5, 0, 'keluar', 'Produksi:  (Gagal QC: 0)', '2026-04-05'),
(14, 2, 0, 'keluar', 'Produksi:  (Gagal QC: 0)', '2026-04-05'),
(15, 172, 10, 'masuk', '', '2026-04-13'),
(16, 20, 0, 'keluar', '', '2026-04-13'),
(17, 120, 20, 'masuk', '', '2026-04-13'),
(18, 300, 0, 'keluar', 'Produksi:  (Gagal QC: 50)', '2026-04-13'),
(19, 264, 0, 'masuk', '', '2026-04-13'),
(20, 264, 0, 'keluar', 'Produksi:  (Gagal QC: 0)', '2026-04-13'),
(21, 400, 0, 'masuk', '', '2026-04-13'),
(22, 400, 0, 'keluar', 'Produksi:  (Gagal QC: 0)', '2026-04-13'),
(23, 200, 0, 'masuk', '', '2026-04-13'),
(24, 200, 0, 'keluar', 'Produksi:  (Gagal QC: 0)', '2026-04-13');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id_transaksi` int(11) NOT NULL,
  `tanggal` datetime NOT NULL DEFAULT current_timestamp(),
  `platform` enum('online','offline') NOT NULL,
  `jumlah_butir` int(11) NOT NULL,
  `total_harga` decimal(10,2) NOT NULL,
  `metode_bayar` varchar(50) DEFAULT NULL,
  `id_user` int(11) NOT NULL,
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id_transaksi`, `tanggal`, `platform`, `jumlah_butir`, `total_harga`, `metode_bayar`, `id_user`, `keterangan`) VALUES
(1, '2026-04-05 20:33:49', 'offline', 2, '6000.00', 'Tunai', 4, ''),
(2, '2026-04-05 20:57:45', 'offline', 1, '3000.00', 'Tunai', 4, ''),
(3, '2026-04-05 21:01:30', 'online', 1, '3000.00', 'QRIS', 5, '[SHIPPED] '),
(4, '2026-04-05 21:09:36', 'online', 10, '30000.00', 'QRIS', 5, 'Shopee - QWERTY12345'),
(5, '2026-03-16 21:26:30', 'offline', 5, '15000.00', 'Tunai', 4, NULL),
(6, '2026-04-13 05:22:29', 'online', 10, '30000.00', 'Transfer', 5, '[SHIPPED] '),
(7, '2026-04-13 05:23:35', 'online', 10, '30000.00', 'Transfer', 5, '[SHIPPED] '),
(8, '2026-04-13 05:23:54', 'online', 10, '30000.00', 'Transfer', 5, NULL),
(9, '2026-04-13 05:23:54', 'online', 10, '30000.00', 'Transfer', 5, NULL),
(10, '2026-04-13 05:23:54', 'online', 10, '30000.00', 'Transfer', 5, NULL),
(11, '2026-04-13 05:25:07', 'offline', 20, '60000.00', 'Tunai', 4, ''),
(12, '2026-04-10 00:00:00', 'offline', 10, '30000.00', 'Tunai', 4, NULL),
(13, '2026-04-10 00:00:00', 'offline', 10, '30000.00', 'Tunai', 4, NULL),
(14, '2026-04-10 00:00:00', 'offline', 10, '30000.00', 'Tunai', 4, NULL),
(15, '2026-04-10 00:00:00', 'offline', 10, '30000.00', 'Tunai', 4, NULL),
(16, '2026-04-10 00:00:00', 'offline', 10, '30000.00', 'Tunai', 4, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(100) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(20) NOT NULL,
  `role` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `email`, `password`, `role`) VALUES
(1, 'owner@admin.com', '123', 'owner'),
(2, 'inventaris@admin.com', '123', 'inventaris'),
(4, 'adminoff@admin.com', '123', 'offline'),
(5, 'adminon@admin.com', '123', 'online');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `alokasi_offline`
--
ALTER TABLE `alokasi_offline`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `alokasi_online`
--
ALTER TABLE `alokasi_online`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `produksi`
--
ALTER TABLE `produksi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stokmentah`
--
ALTER TABLE `stokmentah`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD KEY `fk_user` (`id_user`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `alokasi_offline`
--
ALTER TABLE `alokasi_offline`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `alokasi_online`
--
ALTER TABLE `alokasi_online`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `produksi`
--
ALTER TABLE `produksi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `stokmentah`
--
ALTER TABLE `stokmentah`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id_transaksi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `fk_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
