-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 12, 2026 at 04:03 AM
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
-- Database: `simoli`
--

-- --------------------------------------------------------

--
-- Table structure for table `alat_berat`
--

CREATE TABLE `alat_berat` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_pks` int(11) DEFAULT NULL COMMENT 'FK ke tabel pks',
  `kode_alat` varchar(30) NOT NULL,
  `nama_alat` varchar(100) NOT NULL,
  `jenis_alat` enum('Excavator','Wheel Loader','Bulldozer','Dump Truck','Compactor','Lainnya') NOT NULL DEFAULT 'Excavator',
  `merk_tipe` varchar(100) DEFAULT NULL,
  `tahun_pengadaan` year(4) DEFAULT NULL,
  `status` enum('Operational','Maintenance','Breakdown','Standby','Rolling') NOT NULL DEFAULT 'Operational',
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `alat_berat`
--

INSERT INTO `alat_berat` (`id`, `id_pks`, `kode_alat`, `nama_alat`, `jenis_alat`, `merk_tipe`, `tahun_pengadaan`, `status`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 9, 'EX-01', 'Excavator Komatsu PC200 Pengolahan Sludge', 'Excavator', 'Komatsu PC200-8', '2022', 'Standby', 'Unit utama pengerukan kolam anaerob dan pembersihan sedimentasi sludge.', '2026-08-05 18:08:45', '2026-08-11 02:52:36'),
(2, 9, 'WL-01', 'Wheel Loader CAT 924K Land Application', 'Wheel Loader', 'Caterpillar 924K', '2021', 'Rolling', 'Unit loading solid tankos & pendistribusian limbah ke lahan perkebunan.', '2026-08-05 18:08:45', '2026-08-11 01:59:08'),
(3, 9, 'DT-05', 'Dump Truck Hino 500 Transport Sludge', 'Dump Truck', 'Hino FM 260 TI', '2020', 'Breakdown', 'Perbaikan sistem hidrolik dump bak.', '2026-08-05 18:08:45', '2026-08-11 01:46:22'),
(4, 8, 'BL-01', 'Bulldozer Shantui SD16 Perbaikan Tanggul', 'Bulldozer', 'Shantui SD16', '2023', 'Operational', 'Perataan pematang kolam limbah.', '2026-08-05 18:08:45', '2026-08-05 18:08:45'),
(5, 9, 'CM-01', 'Compactor', 'Compactor', 'Compactor', '2024', 'Maintenance', NULL, '2026-08-10 00:47:22', '2026-08-11 01:57:03'),
(10, 9, 'EX-02', 'Excavator Sany SY215C Pengerukan Kolam 4', 'Excavator', 'Sany SY215C', '2024', 'Operational', 'Unit tambahan pengerukan dan perawatan tanggul kolam anaerob.', '2026-08-11 02:51:50', '2026-08-11 02:51:50');

-- --------------------------------------------------------

--
-- Table structure for table `bulan`
--

CREATE TABLE `bulan` (
  `id_bulan` int(11) NOT NULL,
  `nama_bulan` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=COMPACT;

--
-- Dumping data for table `bulan`
--

INSERT INTO `bulan` (`id_bulan`, `nama_bulan`) VALUES
(1, 'Januari'),
(2, 'Februari'),
(3, 'Maret'),
(4, 'April'),
(5, 'Mei'),
(6, 'Juni'),
(7, 'Juli'),
(8, 'Agustus'),
(9, 'September'),
(10, 'Oktober'),
(11, 'November'),
(12, 'Desember');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(5, '2026_08_05_000001_create_alat_berat_table', 1),
(6, '2026_08_05_000002_create_monitoring_alat_berat_table', 2),
(7, '2026_08_07_000001_add_coordinates_and_photos_to_monitoring_alat_berat_table', 3),
(8, '2026_08_07_000002_change_hm_awal_hm_akhir_to_timestamp_in_monitoring_alat_berat_table', 4),
(9, '2026_08_10_000001_add_jumlah_bed_to_monitoring_alat_berat_table', 5),
(10, '2026_08_11_000001_add_flat_bed_long_bed_to_monitoring_alat_berat_table', 6),
(11, '2026_08_11_000002_add_rolling_to_alat_berat_status_enum', 7),
(12, '2026_08_12_000001_add_start_end_coordinates_to_monitoring_alat_berat_table', 8);

-- --------------------------------------------------------

--
-- Table structure for table `monitoring_alat_berat`
--

CREATE TABLE `monitoring_alat_berat` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_pks` int(11) DEFAULT NULL COMMENT 'FK ke tabel pks',
  `alat_berat_id` bigint(20) UNSIGNED NOT NULL,
  `tanggal` date NOT NULL,
  `operator` varchar(100) NOT NULL,
  `kegiatan` varchar(150) NOT NULL COMMENT 'Jenis kegiatan misal Pembersihan Kolam Limbah, Land Application, dll',
  `lokasi_blok` varchar(100) DEFAULT NULL,
  `flat_bed` int(11) NOT NULL DEFAULT 0 COMMENT 'Jumlah flat bed yang dikerjakan',
  `long_bed` int(11) NOT NULL DEFAULT 0 COMMENT 'Jumlah long bed yang dikerjakan',
  `jumlah_bed` int(11) NOT NULL DEFAULT 0 COMMENT 'Jumlah bed yang dikerjakan',
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `latitude_awal` decimal(10,8) DEFAULT NULL,
  `longitude_awal` decimal(11,8) DEFAULT NULL,
  `latitude_akhir` decimal(10,8) DEFAULT NULL,
  `longitude_akhir` decimal(11,8) DEFAULT NULL,
  `hm_awal` timestamp NULL DEFAULT NULL,
  `hm_akhir` timestamp NULL DEFAULT NULL,
  `total_hm` decimal(8,2) NOT NULL DEFAULT 0.00,
  `bbm_liter` decimal(8,2) NOT NULL DEFAULT 0.00 COMMENT 'Konsumsi solar dalam Liter',
  `kondisi_alat` enum('Normal','Perlu Perbaikan','Breakdown') NOT NULL DEFAULT 'Normal',
  `foto_sebelum` varchar(255) DEFAULT NULL,
  `foto_sesudah` varchar(255) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `monitoring_alat_berat`
--

INSERT INTO `monitoring_alat_berat` (`id`, `id_pks`, `alat_berat_id`, `tanggal`, `operator`, `kegiatan`, `lokasi_blok`, `flat_bed`, `long_bed`, `jumlah_bed`, `latitude`, `longitude`, `latitude_awal`, `longitude_awal`, `latitude_akhir`, `longitude_akhir`, `hm_awal`, `hm_akhir`, `total_hm`, `bbm_liter`, `kondisi_alat`, `foto_sebelum`, `foto_sesudah`, `foto`, `catatan`, `created_at`, `updated_at`) VALUES
(8, 9, 3, '2026-08-07', 'budi', 'Pengadukan Kolam Limbah / Anaerob', 'blok g', 0, 0, 0, 0.47015254, 101.42563785, NULL, NULL, NULL, NULL, '2026-08-07 03:22:00', '2026-08-07 03:23:00', 0.02, 90.00, 'Normal', '1786072978_sebelum_poster.jpeg', '1786072995_sesudah_poster.png', '1786072978_sebelum_poster.jpeg', NULL, '2026-08-07 03:22:58', '2026-08-10 01:56:59'),
(9, 9, 5, '2026-08-10', 'budi', 'Pembersihan & Pengerukan Kolam Limbah', 'blok g', 10, 20, 30, 0.47012046, 101.42583924, NULL, NULL, NULL, NULL, '2026-08-10 00:56:00', '2026-08-10 01:49:02', 0.88, 90.00, 'Normal', '1786323413_sebelum_poster.png', '1786326543_sesudah_poster.jpeg', '1786323413_sebelum_poster.png', NULL, '2026-08-10 00:56:53', '2026-08-11 02:00:08'),
(23, 9, 1, '2026-08-11', 'opt_terantam', 'Korek flatbed', 'B2', 0, 0, 0, 0.46998500, 101.42569400, NULL, NULL, NULL, NULL, '2026-08-11 09:45:00', '2026-08-11 09:58:00', 0.22, 75.00, 'Normal', 'sebelum_1786441558_4974.jpg', 'sesudah_1786442307_9904.png', NULL, NULL, '2026-08-11 09:45:58', '2026-08-11 09:58:27'),
(25, 9, 1, '2026-08-12', 'opt_terantam', 'Pembersihan & Pengerukan Kolam Limbah', 'blok g', 0, 0, 0, 0.47009800, 101.42582100, 0.47009800, 101.42582100, 0.47011832, 101.42585679, '2026-08-12 01:24:00', '2026-08-12 01:49:00', 0.42, 75.00, 'Normal', 'sebelum_1786497899_1524.jpg', '1786499399_sesudah_nv-app-gaming-overalay-790x376-v2[1].jpg', 'sebelum_1786497899_1524.jpg', NULL, '2026-08-12 01:24:59', '2026-08-12 01:49:59'),
(26, 9, 1, '2026-08-12', 'opt_terantam', 'gali', 'Blok g', 0, 0, 0, 0.47010467, 101.42586094, 0.47010467, 101.42586094, NULL, NULL, '2026-08-12 01:32:00', NULL, 0.00, 75.00, 'Normal', 'sebelum_1786498405_8408.jpg', NULL, NULL, NULL, '2026-08-12 01:33:25', '2026-08-12 01:33:25');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pemeliharaan`
--

CREATE TABLE `pemeliharaan` (
  `id` int(11) NOT NULL,
  `id_pks` int(11) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `blok` varchar(25) DEFAULT NULL,
  `no_bak` varchar(20) DEFAULT NULL,
  `flat_bed` int(11) DEFAULT NULL,
  `long_bed` int(11) DEFAULT NULL,
  `sebelum` varchar(50) DEFAULT NULL,
  `sesudah` varchar(50) DEFAULT NULL,
  `jumlah_hk` varchar(10) DEFAULT NULL,
  `keterangan` varchar(30) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `jenis_pemeliharaan` varchar(10) DEFAULT NULL COMMENT 'Pemeliharaan Mekanis=1 Pemeliharaan Manual=2'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=COMPACT;

--
-- Dumping data for table `pemeliharaan`
--

INSERT INTO `pemeliharaan` (`id`, `id_pks`, `tanggal`, `blok`, `no_bak`, `flat_bed`, `long_bed`, `sebelum`, `sesudah`, `jumlah_hk`, `keterangan`, `created_at`, `updated_at`, `jenis_pemeliharaan`) VALUES
(1, 9, '2025-12-01', '-', '-', 0, 0, '-', '-', '4', 'Perbaikan pipa pecah', '2025-12-06 01:49:48', '2026-06-27 02:18:08', '2'),
(2, 9, '2025-12-02', 'C18', '19', 26, 0, '-', '-', '4', '-', '2025-12-06 01:50:41', '2026-06-27 02:18:03', '2'),
(3, 9, '2025-12-03', 'B26', '1', 0, 18, '-', '-', '4', '-', '2025-12-06 01:51:38', '2026-06-27 02:17:59', '2'),
(4, 9, '2025-12-04', 'C26', '3', 0, 18, '-', '-', '4', '-', '2025-12-06 01:52:37', '2026-06-27 02:17:56', '2'),
(5, 9, '2025-12-05', 'C20', '6', 28, 0, '-', '-', '4', '-', '2025-12-06 01:53:45', '2026-06-27 02:17:53', '2'),
(6, 9, '2025-12-06', 'A22', '9', 24, 0, '-', '-', '4', '-', '2025-12-08 03:28:36', '2026-06-27 02:17:50', '2'),
(7, 9, '2025-12-07', 'A22', '10', 28, 0, '-', '-', '4', '-', '2025-12-08 03:29:34', '2026-06-27 02:17:45', '2'),
(8, 9, '2026-01-01', 'B26', '1', 0, 18, '-', '-', '4', '-', '2026-01-19 01:21:46', '2026-06-27 02:17:41', '2'),
(9, 9, '2026-01-02', 'C26', '3', 0, 18, '-', '-', '4', '-', '2026-01-19 01:23:09', '2026-06-27 02:17:38', '2'),
(10, 9, '2026-01-03', 'D22', '5', 24, 0, '-', '-', '4', '-', '2026-01-19 01:23:49', '2026-06-27 02:17:35', '2'),
(11, 9, '2026-01-04', 'C20', '6', 22, 0, '-', '-', '4', '-', '2026-01-19 01:24:43', '2026-06-27 02:17:32', '2'),
(12, 9, '2026-01-05', 'A22', '8', 24, 0, '-', '-', '4', '-', '2026-01-19 01:26:27', '2026-06-27 02:17:28', '2'),
(13, 9, '2026-01-06', 'A20', '9', 24, 0, '-', '-', '4', '-', '2026-01-19 01:27:02', '2026-06-27 02:17:23', '2'),
(14, 9, '2026-01-07', 'A20', '11', 22, 0, '-', '-', '4', '-', '2026-01-19 01:27:41', '2026-06-27 02:17:20', '2'),
(15, 9, '2026-01-08', 'A18', '14', 24, 0, '-', '-', '4', '-', '2026-01-19 01:28:36', '2026-06-27 02:17:16', '2'),
(16, 9, '2026-01-09', 'A18', '15', 26, 0, '-', '-', '4', '-', '2026-01-19 01:29:18', '2026-06-27 02:17:11', '2'),
(17, 9, '2026-01-10', 'C18', '19', 24, 0, '-', '-', '4', '-', '2026-01-19 01:30:02', '2026-06-27 02:17:08', '2'),
(18, 9, '2026-01-11', 'B26', '1', 0, 24, '-', '-', '4', '-', '2026-01-19 01:30:54', '2026-06-27 02:17:05', '2'),
(19, 9, '2026-01-12', 'C26', '3', 0, 18, '-', '-', '4', '-', '2026-01-19 01:31:51', '2026-06-27 02:17:01', '2'),
(20, 9, '2026-01-13', 'D22', '5', 6, 0, '-', '-', '4', '-', '2026-01-19 01:33:27', '2026-06-27 02:17:00', '2'),
(21, 9, '2026-01-14', 'A22', '7', 26, 0, '-', '-', '4', '-', '2026-01-19 01:35:01', '2026-06-27 02:16:57', '2'),
(22, 9, '2026-01-15', 'A20', '8', 24, 0, '-', '-', '4', '-', '2026-01-19 01:35:52', '2026-06-27 02:16:51', '2'),
(23, 9, '2026-01-16', 'A20', '10', 26, 0, '-', '-', '4', '-', '2026-01-19 01:36:41', '2026-06-27 02:16:46', '2'),
(27, 9, '2026-07-08', 'C6', '5', 123, 456, '02a7c0c61af9ab7ef2bbdbc0e47144a1.png', '385fbb6fa401a174c4ef71e72f4cdbed.png', '5', 'Test', '2026-07-07 21:50:38', '2026-07-07 21:56:23', '2'),
(28, 9, '2026-07-18', 'C8', '5', 2, 5, '1fff2a676d374954b42e363bea2c5f5f.jpg', 'b35d6c8486207c90a25983f41af87e26.png', '1', 'TESTING', '2026-07-18 04:14:23', '2026-07-18 04:14:23', '1'),
(29, 9, '2026-08-10', 'c5', '5', 5, 5, '64eeb92210a9e1a21b37294165294dca.png', '3538bed69201b56e38eaef0348e351f6.jpeg', '1', NULL, '2026-08-10 07:04:26', '2026-08-10 07:04:26', '2');

-- --------------------------------------------------------

--
-- Table structure for table `pengaliran`
--

CREATE TABLE `pengaliran` (
  `id_pengaliran` int(11) NOT NULL,
  `tanggal` date DEFAULT NULL,
  `jam_mulai` time DEFAULT NULL,
  `jam_selesai` time DEFAULT NULL,
  `no_bak` varchar(25) DEFAULT NULL,
  `blok` varchar(20) DEFAULT NULL,
  `flat_bed` varchar(25) DEFAULT NULL,
  `vol_limbah_dihasilkan` int(11) DEFAULT NULL,
  `vol_limbah_dialirkan` int(11) DEFAULT NULL,
  `luas_area` int(11) DEFAULT NULL,
  `rotasi` varchar(25) DEFAULT NULL,
  `keterangan` varchar(30) DEFAULT NULL,
  `id_pks` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `tags` varchar(25) DEFAULT NULL,
  `foto` varchar(25) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ROW_FORMAT=COMPACT;

--
-- Dumping data for table `pengaliran`
--

INSERT INTO `pengaliran` (`id_pengaliran`, `tanggal`, `jam_mulai`, `jam_selesai`, `no_bak`, `blok`, `flat_bed`, `vol_limbah_dihasilkan`, `vol_limbah_dialirkan`, `luas_area`, `rotasi`, `keterangan`, `id_pks`, `created_at`, `tags`, `foto`, `updated_at`) VALUES
(1, '2025-12-01', '00:00:00', '00:00:00', '-', '-', '0', 0, 0, 0, '-', 'tidak ada pengaliran', 9, '2025-12-06 01:54:04', '-', '-', '2026-06-26 15:33:09'),
(2, '2025-12-02', '07:00:00', '23:00:00', '19', 'C18', '420', 778, 972, 7, '1', '-', 9, '2025-12-06 01:38:31', '-', '-', '2026-06-26 15:35:05'),
(3, '2025-12-03', '07:00:00', '23:00:00', '1/2/3', 'C26', '480', 814, 616, 7, '1', '-', 9, '2025-12-06 01:39:19', NULL, NULL, '2026-06-26 15:37:00'),
(4, '2025-12-04', '07:00:00', '23:00:00', '3/4/5', 'C26/D22', '470', 719, 1058, 7, '1', '-', 9, '2025-12-06 01:40:18', NULL, NULL, '2026-06-26 15:38:29'),
(5, '2025-12-05', '07:00:00', '23:00:00', '6/7', 'C20/A22', '530', 773, 1179, 7, '1', '-', 9, '2025-12-06 01:40:57', NULL, NULL, '2026-06-26 15:40:48'),
(6, '2025-12-06', '07:00:00', '23:00:00', '8/9', 'A22', '460', 777, 1006, 7, '1', '-', 9, '2025-12-08 03:06:28', NULL, NULL, '2026-06-26 15:42:56'),
(7, '2025-12-07', '07:00:00', '23:00:00', '9/10', 'A22', '510', 757, 1231, 7, '1', '-', 9, '2025-12-08 03:27:42', NULL, NULL, '2026-06-26 15:44:46'),
(8, '2025-12-08', '07:00:00', '23:00:00', '11/12/13', 'A18', '490', 737, 913, 7, '1', '-', 9, '2025-12-09 07:46:31', NULL, NULL, '2026-06-26 15:46:24'),
(9, '2026-01-01', '07:00:00', '23:00:00', '1/2', 'B26', '360', 23, 713, 6, '1', '-', 9, '2026-01-19 01:08:31', NULL, NULL, '2026-06-26 15:48:20'),
(10, '2026-01-02', '07:00:00', '23:00:00', '3/4', 'C26', '470', 84, 1080, 7, '1', '-', 9, '2026-01-19 01:09:06', NULL, NULL, '2026-06-26 15:50:01'),
(11, '2026-01-03', '07:00:00', '23:00:00', '5/6', 'D22/C20', '440', 750, 1065, 7, '1', '-', 9, '2026-01-19 01:09:51', NULL, NULL, '2026-06-26 15:51:16'),
(12, '2026-01-04', '07:00:00', '23:00:00', '6/7', 'C20/D22', '430', 0, 1334, 7, '1', '-', 9, '2026-01-19 01:10:38', NULL, NULL, '2026-06-26 15:55:00'),
(13, '2026-01-05', '07:00:00', '23:00:00', '8', 'A22', '441', 640, 1069, 7, '1', '-', 9, '2026-01-19 01:11:14', NULL, NULL, '2026-06-26 15:56:33'),
(14, '2026-01-06', '07:00:00', '23:00:00', '9/10', 'A20', '420', 743, 795, 7, '1', '-', 9, '2026-01-19 01:11:48', NULL, NULL, '2026-06-26 15:59:59'),
(15, '2026-01-07', '07:00:00', '23:00:00', '11/12/13', 'A20/A18', '490', 701, 924, 7, '1', '-', 9, '2026-01-19 01:12:24', NULL, NULL, '2026-06-26 16:02:57'),
(16, '2026-01-08', '07:00:00', '23:00:00', '14/15', 'A18', '480', 840, 870, 7, '1', '-', 9, '2026-01-19 01:13:07', NULL, NULL, '2026-06-26 16:11:37'),
(17, '2026-01-09', '07:00:00', '23:00:00', '14/15', 'A18', '460', 782, 619, 7, '1', '-', 9, '2026-01-19 01:13:37', NULL, NULL, '2026-06-26 16:11:46'),
(18, '2026-01-10', '07:00:00', '23:00:00', '14/15', 'A18', '470', 736, 339, 7, '1', '-', 9, '2026-01-19 01:15:12', NULL, NULL, '2026-06-26 16:12:10'),
(19, '2026-01-11', '07:00:00', '23:00:00', '1/2', 'B26', '360', 0, 733, 7, '1', '-', 9, '2026-01-19 01:15:59', NULL, NULL, '2026-06-26 16:12:22'),
(20, '2026-01-12', '07:00:00', '23:00:00', '3/4', 'C26', '520', 957, 58, 7, '1', '-', 9, '2026-01-19 01:16:31', NULL, NULL, '2026-06-26 16:12:37'),
(21, '2026-01-13', '07:00:00', '23:00:00', '5/5', 'D22/C20', '490', 792, 854, 7, '1', '-', 9, '2026-01-19 01:17:10', NULL, NULL, '2026-06-26 16:12:45'),
(22, '2026-01-14', '07:00:00', '23:00:00', '7/8', 'A22', '530', 834, 775, 7, '1', '-', 9, '2026-01-19 01:17:52', NULL, NULL, '2026-06-26 16:12:59'),
(23, '2026-01-15', '07:00:00', '23:00:00', '8/9', 'A20', '440', 788, 851, 7, '1', '-', 9, '2026-01-19 01:18:34', NULL, NULL, '2026-06-26 16:13:07'),
(24, '2026-01-16', '07:00:00', '23:00:00', '10/11/12', 'A20', '490', 782, 978, 7, '1', '-', 9, '2026-01-19 01:19:12', NULL, NULL, '2026-06-26 16:13:18'),
(25, '2026-07-08', '07:00:00', '18:00:00', '5', 'C6', '325', 456, 10000, 7, '1', 'Test', 9, '2026-07-07 20:20:45', NULL, '1783480845_3x4 Pebrio.png', '2026-07-07 20:36:29'),
(26, '2026-06-01', '07:00:00', '19:00:00', '6/7', 'C20', '530', 365, 517, 7, '1', '-', 9, NULL, NULL, NULL, '2026-07-15 23:44:44'),
(27, '2026-06-02', '07:00:00', '19:00:00', '7/8', 'A22', '540', 676, 542, 7, '1', '-', 9, NULL, NULL, NULL, '2026-07-15 23:44:52'),
(28, '2026-06-03', '07:00:00', '19:00:00', '9/10', 'A20', '534', 736, 530, 7, '1', '-', 9, NULL, NULL, NULL, '2026-07-15 23:44:52'),
(29, '2026-06-04', '07:00:00', '19:00:00', '11/12', 'A20', '590', 744, 489, 7, '1', '-', 9, NULL, NULL, NULL, '2026-07-15 23:44:52'),
(30, '2026-06-05', '07:00:00', '19:00:00', '13', 'A18', '552', 689, 519, 7, '1', '-', 9, NULL, NULL, NULL, '2026-07-15 23:44:52'),
(31, '2026-06-06', '07:00:00', '19:00:00', '14', 'A18', '513', 780, 577, 7, '1', '-', 9, NULL, NULL, NULL, '2026-07-15 23:44:52'),
(32, '2026-06-07', '07:00:00', '19:00:00', '14/15', 'A18', '554', 0, 516, 7, '1', '-', 9, NULL, NULL, NULL, '2026-07-15 23:44:52'),
(33, '2026-06-08', '07:00:00', '19:00:00', '16', 'C20', '546', 703, 531, 7, '1', '-', 9, NULL, NULL, NULL, '2026-07-15 23:44:52'),
(34, '2026-06-09', '07:00:00', '19:00:00', '16/17', 'C20', '580', 678, 544, 7, '1', '-', 9, NULL, NULL, NULL, '2026-07-15 23:44:52'),
(35, '2026-06-10', '07:00:00', '19:00:00', '18', 'D18', '527', 774, 460, 7, '1', '-', 9, NULL, NULL, NULL, '2026-07-15 23:44:52'),
(36, '2026-06-11', '07:00:00', '19:00:00', '18/19', 'D18/C18', '640', 780, 545, 7, '1', '-', 9, NULL, NULL, NULL, '2026-07-15 23:44:52'),
(37, '2026-06-12', '07:00:00', '19:00:00', '1/2/2003', 'C26', '610', 681, 595, 7, '1', '-', 9, NULL, NULL, NULL, '2026-07-15 23:44:52'),
(38, '2026-06-13', '07:00:00', '19:00:00', '4/5', 'D22', '619', 709, 500, 7, '1', '-', 9, NULL, NULL, NULL, '2026-07-15 23:44:52'),
(39, '2026-06-14', '07:00:00', '19:00:00', '6/7', 'C20', '625', 309, 598, 7, '1', '-', 9, NULL, NULL, NULL, '2026-07-15 23:44:52'),
(40, '2026-07-18', '07:00:00', '18:00:00', '6', 'C8', '9', 4, 3, 7, '5', 'TESTING', 9, '2026-07-18 04:06:11', NULL, '1784372771_3x4 Pebrio.png', '2026-07-20 17:03:10'),
(41, '2026-07-21', '07:00:00', '18:00:00', '6', 'C8', '1', 2, 3, 4, '1', 'Testing', 9, '2026-07-20 17:04:18', NULL, '1784592258_3x4 Pebrio.png', '2026-07-20 17:04:18'),
(42, '2026-08-10', '07:00:00', '18:00:00', '5', 'c5', '5', 5, 5, 1, '7 hari', NULL, 9, '2026-08-10 07:03:18', NULL, '1786345398_poster.png', '2026-08-10 07:03:18');

-- --------------------------------------------------------

--
-- Table structure for table `pks`
--

CREATE TABLE `pks` (
  `id_pks` int(11) NOT NULL,
  `kode` varchar(10) NOT NULL,
  `nama` varchar(25) NOT NULL,
  `akro` varchar(10) NOT NULL,
  `manager` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pks`
--

INSERT INTO `pks` (`id_pks`, `kode`, `nama`, `akro`, `manager`) VALUES
(1, '05.PKS.TPU', 'TANAH PUTIH', 'TPU', 'Taufik Susanto'),
(2, '05.PKS.TME', 'TANJUNG MEDAN', 'TME', 'Ir.Alboin Sinambela'),
(3, '05.PKS.SGO', 'SEI GARO', 'SGO', 'Triana Anggraini'),
(4, '05.PKS.SPA', 'SEI PAGAR', 'SPA', 'Choiri'),
(5, '05.PKS.SBT', 'SEI BUATAN', 'SBT', 'B. M. Rizqi'),
(6, '05.PKS.LDA', 'LUBUK DALAM', 'LDA', 'Ten Novrian'),
(7, '05.PKS.SGH', 'SEI GALUH', 'SGH', 'M.Fadhailul Anam'),
(8, '05.PKS.TAN', 'TANDUN', 'TAN', 'Ir.Rulianta Ginting'),
(9, '05.PKS.TER', 'TERANTAM', 'TER', 'Salman Hari Budiman'),
(10, '05.PKS.STA', 'SEI TAPUNG', 'STA', 'M.SAMSIR.SEMBIRING'),
(11, '05.PKS.SRO', 'SEI ROKAN', 'SRO', 'Eisyen Firdausman ST.'),
(12, '05.PKS.SIN', 'SEI INTAN', 'SIN', 'Aswar Batubara'),
(13, '05.TEKPOL', 'TEKNIK DAN PENGOLAHAN', 'TEP', '-'),
(14, '05.DTM', 'DISTRICT TIMUR', 'DTM', '-'),
(15, '05.DBR', 'DISTRICT BARAT', 'DBR', '-');

-- --------------------------------------------------------

--
-- Table structure for table `rencana`
--

CREATE TABLE `rencana` (
  `id` int(11) NOT NULL,
  `id_pks` int(11) DEFAULT NULL,
  `flat_bed` int(11) DEFAULT NULL,
  `long_bed` int(11) DEFAULT NULL,
  `tahun` year(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rencana`
--

INSERT INTO `rencana` (`id`, `id_pks`, `flat_bed`, `long_bed`, `tahun`) VALUES
(1, 9, 123, 456, '2025'),
(2, 9, 234, 567, '2027'),
(3, 9, 987, 543, '2026');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('cgDQAJuKeYnrDjvGty0c9Cc0IsA9V5u9pgEJ784r', 13, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiQTBQWEVpaVFmSXJXODJRZ1dXS1c3NTFmdlZObldJUkR4ZmNBdndvcSI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjQwOiJodHRwOi8vbG9jYWxob3N0L3NpbW9saWkvcHVibGljL3BlbmdndW5hIjtzOjU6InJvdXRlIjtzOjE0OiJwZW5nZ3VuYS5pbmRleCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjEzO30=', 1786500086),
('Ojfxfc28Tugt8cZpfeY3tOholFPVVweOzaArN95h', 9, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoid25WQzc5VEtYWmJLWWZ5VEl6SDNPc0ZudUVFbmVMeVJQVXhSZzR5QiI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjQzOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvbW9uaXRvcmluZy1hbGF0LWJlcmF0IjtzOjU6InJvdXRlIjtzOjI3OiJtb25pdG9yaW5nLWFsYXQtYmVyYXQuaW5kZXgiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTo5O30=', 1786499399),
('RhoHKl2necVfNHLBStTOYCeWtH8umOXXcKYKLKFO', 20, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoianZEY1U4VTFaUkV0b05TU1BxalJtUU9QSUNoTDdZekNJUk1mWTlXSCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly9sb2NhbGhvc3Qvc2ltb2xpaS9wdWJsaWMvb3BlcmF0b3IiO3M6NToicm91dGUiO3M6MTQ6Im9wZXJhdG9yLmluZGV4Ijt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MjA7fQ==', 1786500172);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `ID` int(11) NOT NULL,
  `username` varchar(25) NOT NULL,
  `password` varchar(5) DEFAULT NULL,
  `level_akses` varchar(10) NOT NULL,
  `id_pks` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`ID`, `username`, `password`, `level_akses`, `id_pks`) VALUES
(1, 'tpu', 'tpu', 'unit', 1),
(2, 'tme', 'tme', 'unit', 2),
(3, 'sgo', 'sgo', 'unit', 3),
(4, 'spa', 'spa', 'unit', 4),
(5, 'sbt', 'sbt', 'unit', 5),
(6, 'lda', 'lda', 'unit', 6),
(7, 'sgh', 'sgh', 'unit', 7),
(8, 'tan', 'tan', 'unit', 8),
(9, 'ter', 'ter', 'unit', 9),
(10, 'sta', 'sta', 'unit', 10),
(11, 'sro', 'sro', 'unit', 11),
(12, 'sin', 'sin', 'unit', 12),
(13, 'tep', 'tep', 'admin', 13),
(15, 'dbr', 'dbr', 'unit', 15),
(14, 'dtm', 'dtm', 'unit', 14),
(18, 'opt_terantam', '12345', 'operator', 9),
(19, 'opt_tpu', '12345', 'operator', 1),
(20, 'mandor_ter', '12345', 'mandor', 9);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `alat_berat`
--
ALTER TABLE `alat_berat`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bulan`
--
ALTER TABLE `bulan`
  ADD PRIMARY KEY (`id_bulan`) USING BTREE;

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `monitoring_alat_berat`
--
ALTER TABLE `monitoring_alat_berat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `monitoring_alat_berat_alat_berat_id_foreign` (`alat_berat_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `pemeliharaan`
--
ALTER TABLE `pemeliharaan`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `pengaliran`
--
ALTER TABLE `pengaliran`
  ADD PRIMARY KEY (`id_pengaliran`) USING BTREE;

--
-- Indexes for table `pks`
--
ALTER TABLE `pks`
  ADD PRIMARY KEY (`id_pks`);

--
-- Indexes for table `rencana`
--
ALTER TABLE `rencana`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`ID`) USING BTREE,
  ADD KEY `fk_user_pks` (`id_pks`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `alat_berat`
--
ALTER TABLE `alat_berat`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `bulan`
--
ALTER TABLE `bulan`
  MODIFY `id_bulan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `monitoring_alat_berat`
--
ALTER TABLE `monitoring_alat_berat`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `pemeliharaan`
--
ALTER TABLE `pemeliharaan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `pengaliran`
--
ALTER TABLE `pengaliran`
  MODIFY `id_pengaliran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `rencana`
--
ALTER TABLE `rencana`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `monitoring_alat_berat`
--
ALTER TABLE `monitoring_alat_berat`
  ADD CONSTRAINT `monitoring_alat_berat_alat_berat_id_foreign` FOREIGN KEY (`alat_berat_id`) REFERENCES `alat_berat` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
