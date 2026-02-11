-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 09, 2026 at 03:13 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_bukawarung`
--

-- --------------------------------------------------------

--
-- Table structure for table `tb_admin`
--

CREATE TABLE `tb_admin` (
  `admin_id` int(11) NOT NULL,
  `admin_name` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `admin_telp` varchar(20) NOT NULL,
  `admin_email` varchar(50) NOT NULL,
  `admin_address` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_admin`
--

INSERT INTO `tb_admin` (`admin_id`, `admin_name`, `username`, `password`, `admin_telp`, `admin_email`, `admin_address`) VALUES
(1, 'Rizqi', 'admin', '6512bd43d9caa6e02c990b0a82652dca', '+6281513120209', 'rizqi18@gmail.com', 'Jl. Asmin, Susukan, Ciracas, Jakarta Timur.');

-- --------------------------------------------------------

--
-- Table structure for table `tb_category`
--

CREATE TABLE `tb_category` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_category`
--

INSERT INTO `tb_category` (`category_id`, `category_name`) VALUES
(4, 'Laptop'),
(7, 'IPad'),
(8, 'Monitor'),
(14, 'Handphone');

-- --------------------------------------------------------

--
-- Table structure for table `tb_checkout`
--

CREATE TABLE `tb_checkout` (
  `checkout_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `product_price` decimal(15,2) DEFAULT NULL,
  `jumlah` int(11) DEFAULT NULL,
  `subtotal` decimal(15,2) DEFAULT NULL,
  `metode` varchar(100) DEFAULT NULL,
  `total` decimal(15,2) DEFAULT NULL,
  `tanggal` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_pembelian`
--

CREATE TABLE `tb_pembelian` (
  `pembelian_id` int(11) NOT NULL,
  `user_nama` varchar(100) DEFAULT NULL,
  `user_email` varchar(100) DEFAULT NULL,
  `user_telp` varchar(20) DEFAULT NULL,
  `user_alamat` text DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `jumlah` int(11) DEFAULT NULL,
  `metode` varchar(50) DEFAULT NULL,
  `tanggal` datetime DEFAULT NULL,
  `id_transaksi` varchar(50) DEFAULT NULL,
  `total_harga` double DEFAULT NULL,
  `catatan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_pembelian`
--

INSERT INTO `tb_pembelian` (`pembelian_id`, `user_nama`, `user_email`, `user_telp`, `user_alamat`, `product_id`, `jumlah`, `metode`, `tanggal`, `id_transaksi`, `total_harga`, `catatan`) VALUES
(1, 'Pembeli Guest', '-@gmail.com', '-5678', '-g', 31, 1, 'E-Wallet', '2025-10-20 11:19:56', 'TRX20251020061956', 25000000, NULL),
(2, 'Pembeli Guest', '-@gmail.com', '-5678', '-g', 32, 1, 'COD', '2025-10-20 11:21:19', 'TRX20251020062119', 30000000, NULL),
(3, 'Pembeli Guest', '-@gmail.com', '-5678', '-g', 32, 8, 'COD', '2025-10-20 11:21:55', 'TRX20251020062155', 240000000, NULL),
(4, 'Pembeli Guest', '-@gmail.com', '-5678', '-g', 32, 1, 'E-Wallet', '2025-10-20 11:22:14', 'TRX20251020062214', 30000000, NULL),
(5, 'Pembeli Guest', '-@gmail.com', '-5678', '-g', 32, 1, 'Transfer Bank', '2025-10-20 11:22:32', 'TRX20251020062232', 30000000, NULL),
(6, 'Pembeli Guest', '-@gmail.com', '-5678', '-g', 32, 1, 'E-Wallet', '2025-10-20 11:31:37', 'TRX20251020063137', 30000000, NULL),
(7, 'Pembeli Guest', '-@gmail.com', '-5678', '-g', 33, 1, 'E-Wallet', '2025-10-20 11:33:05', 'TRX20251020063305', 50000000, NULL),
(8, 'Pembeli Guest', '-@gmail.com', '-5678', '-g', 32, 1, 'E-Wallet', '2025-10-20 11:36:44', 'TRX20251020063644', 30000000, NULL),
(9, 'Pembeli Guest', '-@gmail.com', '-5678', '-g', 32, 1, 'COD', '2025-10-20 11:39:27', 'TRX20251020063927', 30000000, NULL),
(10, 'Pembeli Guest', '-@gmail.com', '-5678', '-g', 31, 1, 'E-Wallet', '2025-10-20 11:39:39', 'TRX20251020063939', 25000000, NULL),
(11, 'Pembeli Guest', '-@gmail.com', '-5678', '-g', 33, 1, 'E-Wallet', '2025-10-20 11:44:13', 'TRX20251020064413', 50000000, NULL),
(12, 'Pembeli Guest', '-@gmail.com', '-5678', '-g', 32, 1, 'E-Wallet', '2025-10-20 11:47:13', 'TRX20251020064713', 30000000, NULL),
(13, 'Pembeli Guest', '-@gmail.com', '-5678', '-g', 26, 1, 'COD', '2025-10-20 11:47:30', 'TRX20251020064730', 14000000, NULL),
(14, 'Pembeli Guest', '-@gmail.com', '-5678', '-g', 32, 1, 'Transfer Bank', '2025-10-20 11:48:07', 'TRX20251020064807', 30000000, NULL),
(15, 'Pembeli Guest', '-@gmail.com', '-5678', '-g', 33, 1, 'COD', '2025-10-20 11:49:10', 'TRX20251020064910', 50000000, NULL),
(16, 'Pembeli Guest', '-@gmail.com', '-5678', '-g', 32, 1, 'E-Wallet', '2025-10-20 11:49:24', 'TRX20251020064924', 30000000, NULL),
(17, 'Pembeli Guest', '-@gmail.com', '-5678', '-g', 30, 1, 'Transfer Bank', '2025-10-20 11:51:11', 'TRX20251020065111', 10000000, NULL),
(18, 'Pembeli Guest', '-@gmail.com', '-5678', '-g', 31, 1, 'E-Wallet', '2025-10-21 08:00:47', 'TRX20251021030047', 25000000, NULL),
(19, 'Pembeli Guest', '-@gmail.com', '-5678', '-g', 31, 1, 'E-Wallet', '2025-10-21 08:02:49', 'TRX20251021030249', 25000000, NULL),
(20, 'Pembeli Guest', '-@gmail.com', '-5678', '-g', 31, 1, 'COD', '2025-10-21 08:04:47', 'TRX20251021030447', 25000000, NULL),
(21, '', '-@gmail.com', '-5678', '-g', 32, 1, 'Tidak Ditetapkan', '2025-10-21 08:13:18', 'TRX20251021031318', 30000000, NULL),
(22, '', '-@gmail.com', '-5678', '-g', 33, 1, 'Tidak Ditetapkan', '2025-10-21 08:13:52', 'TRX20251021031352', 50000000, NULL),
(23, '', '-@gmail.com', '-5678', '-g', 31, 1, 'Tidak Ditetapkan', '2025-10-21 08:18:57', 'TRX20251021031857', 25000000, NULL),
(24, '', '-@gmail.com', '-5678', '-g', 33, 1, 'Tidak Ditetapkan', '2025-10-23 07:38:32', 'TRX20251023023832', 50000000, NULL),
(25, '', '-@gmail.com', '-5678', '-g', 32, 1000, 'Tidak Ditetapkan', '2025-10-23 07:39:03', 'TRX20251023023903', 30000000000, NULL),
(26, '', '-@gmail.com', '-5678', '-g', 32, 1, 'Tidak Ditetapkan', '2025-10-23 10:51:36', 'TRX20251023055136', 30000000, NULL),
(27, '', '-@gmail.com', '-5678', '-g', 32, 1, 'Tidak Ditetapkan', '2025-10-23 11:05:18', 'TRX20251023060518', 30000000, NULL),
(28, '', '-@gmail.com', '-5678', '-g', 31, 1, 'Tidak Ditetapkan', '2025-10-23 11:05:53', 'TRX20251023060553', 25000000, NULL),
(33, 'Pembeli Guest', '-@gmail.com', '-5678', '-g', 33, 1, 'E-Wallet', '2025-10-23 13:37:22', 'TRX2025102308372239', 50018000, NULL),
(34, 'Pembeli Guest', '-@gmail.com', '-5678', '-g', 33, 1, 'E-Wallet', '2025-10-23 13:41:30', 'TRX2025102308413063', 50018000, NULL),
(35, 'Pembeli Guest', '-@gmail.com', '-5678', '-g', 32, 1, 'E-Wallet', '2025-10-23 13:45:10', 'TRX2025102308451048', 30018000, NULL),
(36, 'Pembeli Guest', '-@gmail.com', '-5678', '-g', 33, 1, 'E-Wallet', '2025-10-23 13:48:27', 'TRX2025102308482777', 50010000, NULL),
(37, 'Pembeli Guest', '-@gmail.com', '-5678', '-g', 33, 1, 'E-Wallet', '2025-10-23 13:48:50', 'TRX2025102308485027', 50010000, NULL),
(38, 'Pembeli Guest', '-@gmail.com', '-5678', '-g', 32, 1, 'Transfer Bank', '2025-10-23 13:55:33', 'TRX2025102308553380', 30010000, NULL),
(39, 'Pembeli Guest', '-@gmail.com', '-5678', '-g', 32, 1, 'Transfer Bank', '2025-10-23 14:07:18', 'TRX2025102309071872', 30010000, NULL),
(40, 'Pembeli Guest', '-@gmail.com', '-5678', '-g', 25, 1, 'E-Wallet', '2025-10-23 14:09:53', 'TRX2025102309095331', 5010000, NULL),
(41, 'Pembeli Guest', '-@gmail.com', '-5678', '-g', 26, 5, 'Transfer Bank', '2025-10-23 14:15:07', 'TRX2025102309150794', 740220018000, NULL),
(42, 'Pembeli Guest', '-@gmail.com', '-5678', '-g', 30, 190, 'Transfer Bank', '2025-10-23 14:15:07', 'TRX2025102309150794', 740220018000, NULL),
(43, 'Pembeli Guest', '-@gmail.com', '-5678', '-g', 33, 14765, 'Transfer Bank', '2025-10-23 14:15:07', 'TRX2025102309150794', 740220018000, NULL),
(44, 'kiki', '-@gmail.com', '08012398099', 'Jl. Asmin', 31, 10, 'Transfer Bank', '2025-11-15 05:33:20', 'TRX2025111423332057', 250015000, NULL),
(45, 'kikaku', '-@gmail.com', '08012398099', 'Jl. Asmin', 30, 10, 'Transfer Bank', '2025-11-15 05:35:20', 'TRX2025111423352055', 100015000, NULL),
(46, 'kikaku', '-@gmail.com', '08012398099', 'Jl. Asmin', 31, 10, 'Transfer Bank', '2025-11-17 07:31:22', 'TRX2025111701312282', 250010000, NULL),
(47, 'pak haykal', 'tes1@gmail.com', '012973912463', 'makmur', 34, 2, 'E-Wallet', '2026-01-09 14:16:13', 'TRX2026010908161336', 40018000, NULL),
(48, 'kkkal', 'cuki@gmail.com', '012973912463', 'makmur', 35, 1, 'Transfer Bank', '2026-01-12 08:53:44', 'TRX2026011202534428', 9283345, NULL),
(49, 'Pembeli Guest', '-@gmail.com', 'agwe', 'ase', 33, 1, 'COD', '2026-01-14 12:10:00', 'TRX2026011406100060', 50000000, NULL),
(50, 'Pembeli Guest', '-@gmail.com', 'agwe', 'ase', 32, 1, 'COD', '2026-01-19 12:30:10', 'TRX2026011906301016', 30015000, NULL),
(51, 'iPhone 14', 'rizqi18@gmail.com', '456345y', '43q', 31, 1, 'E-Wallet', '2026-01-30 16:06:34', 'TRX2026013010063419', 85000000, NULL),
(52, 'iPhone 14', 'rizqi18@gmail.com', '456345y', '43q', 32, 2, 'E-Wallet', '2026-01-30 16:06:34', 'TRX2026013010063419', 85000000, NULL),
(53, 'Pembeli Guest', '-@gmail.com', 'awef', 'xcZxc', 33, 1, 'Transfer Bank', '2026-02-02 20:59:51', 'TRX2026020214595144', 50018000, NULL),
(54, 'Pembeli Guest', '-@gmail.com', 'awef', 'xcZxc', 33, 1, 'Transfer Bank', '2026-02-02 21:01:46', 'TRX2026020215014618', 50018000, NULL),
(55, 'Pembeli Guest', '-@gmail.com', 'awef', 'xcZxc', 33, 981, 'Transfer Bank', '2026-02-02 21:02:10', 'TRX2026020215021072', 49050018000, NULL),
(56, 'Pembeli Guest', '-@gmail.com', 'awef', 'xcZxc', 32, 1, 'Transfer Bank', '2026-02-02 21:02:25', 'TRX2026020215022518', 30018000, NULL),
(57, 'Pembeli Guest', '-@gmail.com', 'awef', 'xcZxc', 31, 294, 'Transfer Bank', '2026-02-02 21:05:26', 'TRX2026020215052694', 7350018000, NULL),
(58, 'Pembeli Guest', '-@gmail.com', 'awef', 'xcZxc', 33, 2, 'Transfer Bank', '2026-02-02 21:06:01', 'TRX2026020215060155', 100018000, NULL),
(59, 'Pembeli Guest', '-@gmail.com', 'awef', 'xcZxc', 33, 10, 'Transfer Bank', '2026-02-02 21:06:58', 'TRX2026020215065819', 500018000, NULL),
(60, 'Pembeli Guest', '-@gmail.com', 'awef', 'xcZxc', 27, 1, 'E-Wallet', '2026-02-04 07:48:41', 'TRX2026020401484120', 10010000, NULL),
(61, 'kkkal', 'kal@gmail.com', 'awef', 'xcZxc', 32, 1, 'E-Wallet', '2026-02-04 08:52:54', 'TRX2026020402525472', 30018000, NULL),
(62, 'kkkal', 'kal@gmail.com', 'awef', 'xcZxc', 32, 1, 'E-Wallet', '2026-02-04 08:53:33', 'TRX2026020402533358', 80018000, NULL),
(63, 'kkkal', 'kal@gmail.com', 'awef', 'xcZxc', 33, 1, 'E-Wallet', '2026-02-04 08:53:33', 'TRX2026020402533358', 80018000, NULL),
(64, 'kkkal', 'kal@gmail.com', 'awef', 'xcZxc', 33, 5, 'E-Wallet', '2026-02-04 08:58:25', 'TRX2026020402582522', 250018000, NULL),
(65, 'haikal', 'kal@gmail.com', '08012398099', 'Jalan Makmur, ytta', 32, 1, 'COD', '2026-02-04 09:00:42', 'TRX2026020403004273', 30018000, NULL),
(66, 'iPhone 14', 'rizqi.adi53@smk.belajar.id', 'q234', 'sadf', 32, 1, 'E-Wallet', '2026-02-04 09:09:03', 'TRX2026020403090342', 30018000, 'datang sore hari'),
(67, 'haikal', 'rizqi.adi53@smk.belajar.id', '8762497q254', 'sertghstrfgb', 31, 1, 'E-Wallet', '2026-02-05 07:59:32', 'TRX2026020501593262', 25018000, 'zdgthsethst'),
(68, '5yserth', 'rizkyadiprasetyo01@gmail.com', 'sdzfbsaerth', 'wEFAWEFV', 36, 1, 'E-Wallet', '2026-02-06 14:01:22', 'TRX2026020608012236', 12018000, 'ASDVAWEF'),
(69, '', '', '', '', 38, 1, 'Lainnya', '2026-02-09 09:12:37', 'TRX2026020903123717', 18000000, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tb_product`
--

CREATE TABLE `tb_product` (
  `product_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `product_price` int(11) NOT NULL,
  `product_discount` int(11) NOT NULL DEFAULT 0,
  `product_discount_price` decimal(12,2) DEFAULT NULL,
  `product_description` text NOT NULL,
  `product_image` varchar(100) NOT NULL,
  `product_status` tinyint(1) NOT NULL,
  `data_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `product_stock` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_product`
--

INSERT INTO `tb_product` (`product_id`, `category_id`, `product_name`, `product_price`, `product_discount`, `product_discount_price`, `product_description`, `product_image`, `product_status`, `data_created`, `product_stock`) VALUES
(23, 14, 'iPhone 14', 20000000, 20, NULL, '<p>iPhone 14 adalah&nbsp;seri smartphone yang dirilis oleh Apple pada tahun 2022. Seri ini terdiri dari iPhone 14, iPhone 14 Plus, iPhone 14 Pro, dan iPhone 14 Pro Max.&nbsp;iPhone 14 dan 14 Plus memiliki layar Super Retina XDR, sedangkan Pro dan Pro Max memiliki Dynamic Island dan layar Always-On.&nbsp;</p>\r\n\r\n<p>Fitur Utama iPhone 14:</p>\r\n\r\n<ul>\r\n	<li><strong>Chip A15 Bionic:</strong>&nbsp;Chip yang sama dengan iPhone 13, memberikan kinerja yang cepat dan efisien.&nbsp;</li>\r\n</ul>\r\n\r\n<ul>\r\n	<li><strong>Deteksi Tabrakan:</strong>&nbsp;Fitur keselamatan baru yang memanggil bantuan secara otomatis setelah kecelakaan.&nbsp;</li>\r\n</ul>\r\n\r\n<ul>\r\n	<li><strong>Daya Baterai Sehari-hari:</strong>&nbsp;Baterai yang dirancang untuk penggunaan seharian penuh.&nbsp;</li>\r\n</ul>\r\n\r\n<ul>\r\n	<li><strong>Tahanan Air dan Debu:</strong>&nbsp;iPhone 14 memiliki sertifikasi IP68, tahan cipratan, air, dan debu.&nbsp;</li>\r\n</ul>\r\n\r\n<ul>\r\n	<li><strong>Layar Super Retina XDR:</strong>&nbsp;Layar 6,1 inci pada iPhone 14 (dan 6,7 inci pada iPhone 14 Plus) dengan teknologi OLED, HDR, dan resolusi 2532 x 1170 piksel.&nbsp;</li>\r\n	<li><strong>Sistem Kamera Ganda:</strong>&nbsp;Sistem kamera 12MP dengan lensa utama dan lensa ultrawide, serta peningkatan dalam pemrosesan gambar dan kemampuan mode Potret.&nbsp;</li>\r\n</ul>', 'produk1747720931.jpg', 1, '2025-05-20 05:44:43', 10),
(24, 14, 'Samsung s25 Ultra', 25000000, 0, NULL, '<p>Samsung Galaxy S25 Ultra merupakan&nbsp;smartphone flagship terbaru dari Samsung, yang hadir dengan spesifikasi tinggi dan fitur-fitur canggih.&nbsp;Perangkat ini menawarkan layar Dynamic AMOLED 2X 6.9 inci dengan refresh rate adaptif 1Hz hingga 120Hz, prosesor Snapdragon 8 Elite for Galaxy, RAM 12GB, penyimpanan 1TB, dan sistem kamera quad 200MP.&nbsp;</p>\r\n\r\n<p>Spesifikasi Utama:</p>\r\n\r\n<ul>\r\n	<li><strong>Layar:</strong>&nbsp;6.9 inci Dynamic AMOLED 2X, QHD+ (3120 x 1440 piksel), 120Hz.</li>\r\n	<li><strong>Prosesor:</strong>&nbsp;Snapdragon 8 Elite for Galaxy.</li>\r\n	<li><strong>RAM:</strong>&nbsp;12GB.</li>\r\n	<li><strong>Penyimpanan:</strong>&nbsp;1TB.</li>\r\n	<li><strong>Kamera Belakang:</strong>&nbsp;Quad 200 MP + 50 MP + 10 MP + 50 MP.</li>\r\n	<li><strong>Kamera Depan:</strong>&nbsp;12 MP.</li>\r\n	<li><strong>Baterai:</strong>&nbsp;5000 mAh, pengisian cepat 45W.</li>\r\n	<li><strong>Sistem Operasi:</strong>&nbsp;Android 15 One UI 7.&nbsp;</li>\r\n</ul>', 'produk1747720368.webp', 1, '2025-05-20 05:52:48', 10),
(25, 14, 'Poco X7 Pro 5G', 5000000, 10, NULL, '<p><strong>POCO X7 Pro 5G</strong> merupakan smartphone kelas menengah atas terbaru dari POCO (sub-brand Xiaomi) yang hadir dengan spesifikasi tinggi dan fitur-fitur canggih. Perangkat ini ditujukan bagi pengguna yang menginginkan performa tinggi, tampilan layar berkualitas, dan daya tahan baterai besar dengan harga yang tetap kompetitif.</p>\r\n\r\n<hr />\r\n<h3><strong>Spesifikasi Utama:</strong></h3>\r\n\r\n<ul>\r\n	<li>\r\n	<p><strong>Layar</strong>: 6.67 inci AMOLED CrystalRes, resolusi 1.5K (2712 x 1220 piksel), refresh rate 120Hz, kecerahan hingga 3200 nits.</p>\r\n	</li>\r\n	<li>\r\n	<p><strong>Prosesor</strong>: MediaTek Dimensity 8400 Ultra (4nm).</p>\r\n	</li>\r\n	<li>\r\n	<p><strong>RAM</strong>: 12GB LPDDR5X.</p>\r\n	</li>\r\n	<li>\r\n	<p><strong>Penyimpanan</strong>: 512GB UFS 4.0.</p>\r\n	</li>\r\n	<li>\r\n	<p><strong>Kamera Belakang</strong>: Dual kamera 50 MP (f/1.5, OIS) + 8 MP ultrawide.</p>\r\n	</li>\r\n	<li>\r\n	<p><strong>Kamera Depan</strong>: 20 MP.</p>\r\n	</li>\r\n	<li>\r\n	<p><strong>Baterai</strong>: 6000 mAh, pengisian cepat 90W (100% dalam 42 menit).</p>\r\n	</li>\r\n	<li>\r\n	<p><strong>Sistem Operasi</strong>: Android 15 dengan antarmuka HyperOS 2.</p>\r\n	</li>\r\n	<li>\r\n	<p><strong>Fitur Tambahan</strong>: Sertifikasi IP68 tahan air dan debu, NFC, Wi-Fi 6, Bluetooth 6.0, sensor sidik jari di layar, Face Unlock.</p>\r\n	</li>\r\n</ul>', 'produk1747720637.webp', 1, '2025-05-20 05:57:17', 10),
(26, 14, 'Oppo find x8 Pro', 14000000, 0, NULL, '<p><strong>OPPO Find X8 Pro</strong> adalah smartphone flagship terbaru dari OPPO yang hadir dengan spesifikasi tinggi dan fitur-fitur canggih. Perangkat ini menawarkan layar AMOLED 6,78 inci dengan refresh rate adaptif 1Hz hingga 120Hz, prosesor MediaTek Dimensity 9400, RAM 16GB, penyimpanan 512GB, dan sistem kamera quad 50MP.</p>\r\n\r\n<hr />\r\n<h3><strong>Spesifikasi Utama:</strong></h3>\r\n\r\n<ul>\r\n	<li>\r\n	<p><strong>Layar</strong>: 6,78 inci AMOLED, resolusi FHD+ (2780 &times; 1264 piksel), refresh rate adaptif 1Hz&ndash;120Hz, touch sampling rate hingga 240Hz, kecerahan puncak hingga 4500 nit, dilindungi Corning Gorilla Glass 7i.<a href=\"https://tekno.kompas.com/read/2024/11/22/12350057/spesifikasi-dan-harga-oppo-find-x8-pro-di-indonesia?utm_source=chatgpt.com\" target=\"_blank\">OPPO+2KOMPAS.com+2OPPO+2</a></p>\r\n	</li>\r\n	<li>\r\n	<p><strong>Prosesor</strong>: MediaTek Dimensity 9400 (3nm), CPU octa-core hingga 3,63 GHz, GPU Immortalis-G925 MC12.<a href=\"https://tekno.kompas.com/read/2024/11/22/12350057/spesifikasi-dan-harga-oppo-find-x8-pro-di-indonesia?utm_source=chatgpt.com\" target=\"_blank\">news+3KOMPAS.com+3GSMArena+3</a></p>\r\n	</li>\r\n	<li>\r\n	<p><strong>RAM</strong>: 16GB LPDDR5X.</p>\r\n	</li>\r\n	<li>\r\n	<p><strong>Penyimpanan</strong>: 512GB UFS 4.0.</p>\r\n	</li>\r\n	<li>\r\n	<p><strong>Kamera Belakang</strong>:</p>\r\n\r\n	<ul>\r\n		<li>\r\n		<p>50 MP utama (f/1.6, OIS, sensor 1/1.4&quot;, panjang fokus 23mm).</p>\r\n		</li>\r\n		<li>\r\n		<p>50 MP ultra-wide (f/2.0, panjang fokus 15mm).</p>\r\n		</li>\r\n		<li>\r\n		<p>50 MP telefoto periskop 6x (f/4.3, panjang fokus 135mm).</p>\r\n		</li>\r\n		<li>\r\n		<p>50 MP telefoto periskop 3x.<a href=\"https://www.oppo.com/id/smartphones/series-find-x/find-x8-pro/?utm_source=chatgpt.com\" target=\"_blank\">OPPO</a></p>\r\n		</li>\r\n	</ul>\r\n	</li>\r\n	<li>\r\n	<p><strong>Kamera Depan</strong>: 32 MP (f/2.4).<a href=\"https://tekno.kompas.com/read/2024/11/22/12350057/spesifikasi-dan-harga-oppo-find-x8-pro-di-indonesia?utm_source=chatgpt.com\" target=\"_blank\">KOMPAS.com</a></p>\r\n	</li>\r\n	<li>\r\n	<p><strong>Baterai</strong>: 5910 mAh, pengisian cepat 80W SuperVOOC dan 50W AirVOOC.<a href=\"https://www.news.com.au/technology/gadgets/oppo-find-x8-pro-arrives-in-australia-insanely-good-ai-and-camera/news-story/6c27dbc8bb8fe92c1b82a06079668b7f?utm_source=chatgpt.com\" target=\"_blank\">KOMPAS.com+3news+3OPPO+3</a></p>\r\n	</li>\r\n	<li>\r\n	<p><strong>Sistem Operasi</strong>: Android 15 dengan antarmuka ColorOS 15.<a href=\"https://tekno.kompas.com/read/2024/11/22/12350057/spesifikasi-dan-harga-oppo-find-x8-pro-di-indonesia?utm_source=chatgpt.com\" target=\"_blank\">news+2KOMPAS.com+2The Sun+2</a></p>\r\n	</li>\r\n	<li>\r\n	<p><strong>Fitur Tambahan</strong>: Sertifikasi IP68 dan IP69 tahan air dan debu, NFC, Wi-Fi 7, Bluetooth 5.4, USB-C, IR Blaster, Alert Slider, sensor sidik jari di layar, Face Unlock.<a href=\"https://tekno.kompas.com/read/2024/11/22/12350057/spesifikasi-dan-harga-oppo-find-x8-pro-di-indonesia?utm_source=chatgpt.com\" target=\"_blank\">KOMPAS.com</a></p>\r\n	</li>\r\n	<li>\r\n	<p><strong>Dimensi dan Berat</strong>: 162,27 x 76,67 x 8,24 mm (Space Black) / 8,34 mm (Pearl White), berat sekitar 215 gram</p>\r\n	</li>\r\n</ul>', 'produk1747720821.jpg', 1, '2025-05-20 06:00:21', 10),
(27, 8, 'Monitor LCD Full HD', 10000000, 0, NULL, '<p>Gambar jelas dan tajam, di seluruh bagian layar &nbsp;Layar serbaguna sempurna dengan gambar yang memukau di seluruh bagian, sehingga nyaman di mata, dengan desain ramping yang ringkas.</p>', 'produk1747721061.webp', 1, '2025-05-20 06:04:21', 10),
(28, 8, 'Monitor LED Samsung SA33 19″ HD with Eye-saving', 12000000, 0, NULL, '<p>Spesifikasi<br />\r\nDisplay<br />\r\n&ndash; Screen Size (Class) : 19<br />\r\n&ndash; Flat / Curved Flat<br />\r\n&ndash; Active Display Size (HxV) (mm) : 409.8mm x 230.4mm<br />\r\n&ndash; Screen Curvature : N/A<br />\r\n&ndash; Aspect Ratio : 16:9<br />\r\n&ndash; Panel Type : TN<br />\r\n&ndash; Brightness (Typical) : 250cd/<br />\r\n&ndash; Peak Brightness (Typical) : N/A<br />\r\n&ndash; Brightness (Min) : 200cd/<br />\r\n&ndash; Contrast Ratio Static : 600:1(Typical)<br />\r\n&ndash; Dynamic Contrast Ratio Mega<br />\r\n&ndash; HDR(High Dynamic Range) : N/A<br />\r\n&ndash; HDR10+ N/A<br />\r\n&ndash; Resolution : 1,366 x 768<br />\r\n&ndash; Response Time : 5 (GTG<br />\r\n&ndash; Viewing Angle (H/V) : 90/65<br />\r\n&ndash; Color Support Max : 16.7M<br />\r\n&ndash; Color Gamut (NTSC 1976) : 72%<br />\r\n&ndash; Color Gamut (DCI Coverage) : N/A<br />\r\n&ndash; sRGB Coverage : N/A<br />\r\n&ndash; Adobe RGB Coverage : N/A<br />\r\n&ndash; Refresh Rate Max : 60Hz</p>', 'produk1747721143.png', 1, '2025-05-20 06:05:43', 10),
(29, 8, 'Philips Monitor 32E1N3100LA 32 Inch VA 4ms 75Hz FHD HDMI', 8000000, 15, NULL, '<table>\r\n	<tbody>\r\n		<tr>\r\n			<th>Tipe Display</th>\r\n			<th><strong>VA</strong>&nbsp;LCD</th>\r\n		</tr>\r\n		<tr>\r\n			<td>Ukuran Layar</td>\r\n			<td>31.5&nbsp;<strong>inch</strong>&nbsp;/ 80 cm</td>\r\n		</tr>\r\n		<tr>\r\n			<td>Resolusi Layar</td>\r\n			<td>1920 x 1080 @ 75 Hz</td>\r\n		</tr>\r\n		<tr>\r\n			<td>Rasio Kontras</td>\r\n			<td>3000:1</td>\r\n		</tr>\r\n		<tr>\r\n			<td>Brightness</td>\r\n			<td>300 cd/m&sup2;</td>\r\n		</tr>\r\n	</tbody>\r\n</table>', 'produk1747721285.webp', 1, '2025-05-20 06:08:05', 10),
(30, 8, 'Xiaomi a24i', 10000000, 0, NULL, '<p>Specifications</p>\r\n\r\n<p>Product ModelP24FBA-RAGL</p>\r\n\r\n<p>Aspect Ratio16:9</p>\r\n\r\n<p>Power Input12V⎓2A</p>\r\n\r\n<p>Response Time6ms (typical)</p>\r\n\r\n<p>Rated Power24W</p>\r\n\r\n<p>Maximum Resolution1920 x 1080</p>\r\n\r\n<p>Display Size23.8&quot;</p>\r\n\r\n<p>Recommended Resolution1920 x 1080</p>\r\n\r\n<p>Product Dimensions539.9 x 411.4 x 170mm (base included)</p>\r\n\r\n<p>Product Net Weight2.98kg</p>\r\n\r\n<p>Brightness250cd/㎡ (typical)</p>\r\n\r\n<p>Operating Temperature0&deg;C&ndash;40&deg;C</p>\r\n\r\n<p>Contrast Ratio1000:1 (typical)</p>\r\n\r\n<p>Operating Humidity20%&ndash;80%RH</p>\r\n\r\n<p>Colors16.7 million</p>\r\n\r\n<p>Atmospheric Pressure86kPa&ndash;106kPa</p>', 'produk1747721443.png', 1, '2025-05-20 06:10:43', 10),
(31, 7, 'Apple iPad Pro', 25000000, 25, NULL, '<h3>Sistem Operasi</h3>\r\n\r\n<p><strong>iOS 10</strong></p>\r\n\r\n<p>Dengan desain dan fitur yang sepenuhnya baru, iOS 10 adalah OS seluler tercanggih di dunia. Menghadirkan iPad dengan cara yang lebih cerdas dan ekspresif dibanding sebelumnya.&nbsp;<a href=\"http://www.apple.com/id/ios/ios-10/\">Lihat apa yang baru di iOS 10</a></p>\r\n\r\n<p>iOS 10 mencakup:</p>\r\n\r\n<ul>\r\n	<li>\r\n	<p>AirDrop</p>\r\n	</li>\r\n	<li>\r\n	<p>AirPlay</p>\r\n	</li>\r\n	<li>\r\n	<p>AirPrint</p>\r\n	</li>\r\n	<li>\r\n	<p>Pusat Kontrol</p>\r\n	</li>\r\n	<li>\r\n	<p>Pusat Pemberitahuan</p>\r\n	</li>\r\n	<li>\r\n	<p>Pencarian Spotlight</p>\r\n	</li>\r\n	<li>\r\n	<p>Apple Music</p>\r\n	</li>\r\n	<li>\r\n	<p>Keluarga Berbagi</p>\r\n	</li>\r\n	<li>\r\n	<p>iCloud Drive</p>\r\n	</li>\r\n	<li>\r\n	<p>Multitasking</p>\r\n	</li>\r\n	<li>\r\n	<p>Papan Ketik QuickType</p>\r\n	</li>\r\n	<li>\r\n	<p>Night Shift</p>\r\n	</li>\r\n</ul>', 'produk1747721652.webp', 1, '2025-05-20 06:14:12', 9),
(32, 4, 'ADVAN Laptop 2 in 1 EVO-X', 30000000, 10, NULL, '<p><strong>Spesifikasi :</strong></p>\r\n\r\n<p><em>&middot;&nbsp;Display : 16&rdquo; IPS 2560*1600</em></p>\r\n\r\n<p><em>&middot;&nbsp;CPU : Intel&reg; Core i3-1215U</em></p>\r\n\r\n<p><em>&middot;&nbsp;GPU : Intel&reg; UHD Graphics</em></p>\r\n\r\n<p><em>&middot;&nbsp;Memory : 12GB LPDDR5</em></p>\r\n\r\n<p><em>&middot;&nbsp;Storage : 512GB NVMe</em></p>\r\n\r\n<p><em>&middot;&nbsp;Camera : HD Cameras on front and rear</em></p>\r\n\r\n<p><em>&middot;&nbsp;Connection : Wifi 802.11 b/g/n/ac 2.4GHz/5GHz</em></p>\r\n\r\n<p><em>&middot;&nbsp;Bluetooth 5.0</em></p>\r\n\r\n<p><em>&middot;&nbsp;Battery : 6000mAH 7.6V</em></p>\r\n\r\n<p><em>&middot;&nbsp;Keyboard with Magnetic Connection</em></p>\r\n\r\n<p><em>&middot;&nbsp;Port Variants :</em></p>\r\n\r\n<p><em>-1* DC port</em></p>\r\n\r\n<p><em>-2*&nbsp;USB 3.2 Gen 1</em></p>\r\n\r\n<p><em>-1* Audio port</em></p>\r\n\r\n<p><em>-2* Type-C</em></p>', 'produk1747721768.webp', 1, '2025-05-20 06:16:08', 8),
(33, 4, 'Asus ROG', 50000000, 0, 2500000.00, '<p>ROG Flow Z13 didukung oleh CPU hingga&nbsp;<strong>Intel&reg; Core&trade; i9-12900H</strong>, yang menggunakan 14 inti dan 20 thread untuk menghadirkan pengalaman gaming generasi berikutnya.</p>', 'produk1747721889.jpg', 1, '2025-05-20 06:18:09', 5),
(36, 4, 'Asus VIvobook S14', 12000000, 10, NULL, '<ul>\r\n	<li>Snapdragon&reg; X X1 26 100 Processor</li>\r\n	<li>16GB LPDDR5X on board</li>\r\n	<li>512GB M.2 NVMe&trade; PCIe&reg; 4.0 SSD</li>\r\n	<li>14.0-inch, FHD (1920 x 1200) OLED 16:10 aspect ratio</li>\r\n	<li>Windows 11 Home</li>\r\n</ul>', 'produk_1770257369_558.png', 1, '2026-02-05 02:09:29', 9),
(37, 4, 'Lenovo IdeaPad', 10000000, 0, NULL, '<ul>\r\n	<li>\r\n	<p>Teknologi CPU&nbsp;<a href=\"https://www.erablue.id/blog/apa-itu-core-i5-1574272\" target=\"_blank\">Intel Core i5</a></p>\r\n	</li>\r\n	<li><a href=\"https://www.erablue.id/blog/daftar-prosesor-laptop-terbaik-1569703\" target=\"_blank\">Jenis CPU</a>&nbsp;1155G7</li>\r\n	<li>\r\n	<p>Jumlah core CPU 4</p>\r\n	</li>\r\n	<li>\r\n	<p>Jumlah thread CPU 8</p>\r\n	</li>\r\n	<li>\r\n	<p>Kecepatan CPU 1.00 GHz</p>\r\n	</li>\r\n	<li>\r\n	<p>Kecepatan maksimum 2.5 GHz</p>\r\n	</li>\r\n	<li><a href=\"https://www.erablue.id/blog/cache-adalah-data-temporer-1571258\" target=\"_blank\">Cache</a>&nbsp;8 MB</li>\r\n	<li><a href=\"https://www.erablue.id/blog/fungsi-ram-hp-1571124\" target=\"_blank\">RAM</a>&nbsp;16 GB</li>\r\n	<li><a href=\"https://www.erablue.id/blog/jenis-fungsi-ram-1571566\" target=\"_blank\">Jenis RAM</a><a href=\"https://www.erablue.id/blog/teknologi-ddr4-1580787\" target=\"_blank\">DDR4</a></li>\r\n	<li>\r\n	<p>Kecepatan Bus RAM 3200 MHz</p>\r\n	</li>\r\n	<li>\r\n	<p>Dukungan RAM maksimum Up to 16GB</p>\r\n	</li>\r\n</ul>', 'produk_1770257550_529.jpg', 1, '2026-02-05 02:12:30', 10),
(38, 7, 'iPad Mini', 18000000, 0, NULL, '<ul>\r\n	<li><strong>Chip Pemrosesan</strong>: Ditenagai oleh&nbsp;<strong>chip Apple A17 Pro</strong>, yang menampilkan CPU 6-inti, GPU 5-inti, dan Neural Engine 16-inti, menawarkan performa mumpuni untuk&nbsp;<em>gaming</em>&nbsp;dan tugas berat.</li>\r\n	<li><strong>Layar</strong>: Menggunakan layar&nbsp;<strong>Liquid Retina 8,3 inci</strong>&nbsp;IPS LCD dengan resolusi tinggi, kecerahan 500 nits, dan teknologi True Tone.&nbsp;<em>Jelly scrolling</em>&nbsp;dilaporkan telah diperbaiki.</li>\r\n	<li><strong>Kamera</strong>:\r\n	<ul>\r\n		<li>Kamera belakang&nbsp;<strong>12 megapiksel</strong>&nbsp;yang mendukung HDR4.</li>\r\n		<li>Kamera depan&nbsp;<strong>12 megapiksel</strong>&nbsp;<em>ultra wide</em>&nbsp;dengan fitur&nbsp;<em>Center Stage</em>.</li>\r\n	</ul>\r\n	</li>\r\n	<li><strong>Konektivitas</strong>: Port&nbsp;<strong>USB-C 3.1 Gen 2</strong>&nbsp;untuk transfer data yang lebih cepat (hingga 10 Gbit/s), dukungan&nbsp;<strong>Wi-Fi 6E</strong>, dan konektivitas 5G pada model seluler (menggunakan e-SIM, tanpa SIM fisik).</li>\r\n	<li><strong>Aksesori</strong>: Mendukung&nbsp;<strong>Apple Pencil Pro</strong>, termasuk fitur&nbsp;<em>Taptic Engine</em>&nbsp;dan&nbsp;<em>hover</em>.</li>\r\n	<li><strong>Penyimpanan</strong>: Tersedia dalam opsi penyimpanan&nbsp;<strong>128 GB, 256 GB, dan 512 GB</strong>.</li>\r\n	<li><strong>Sistem Operasi</strong>: Diluncurkan dengan iPadOS 18.0 dan kompatibel dengan Apple Intelligence.&nbsp;</li>\r\n</ul>', 'produk_1770257722_372.jpg', 1, '2026-02-05 02:15:22', 0);

-- --------------------------------------------------------

--
-- Table structure for table `tb_user`
--

CREATE TABLE `tb_user` (
  `user_id` int(11) NOT NULL,
  `user_nama` varchar(100) DEFAULT NULL,
  `user_email` varchar(100) DEFAULT NULL,
  `user_password` varchar(255) DEFAULT NULL,
  `user_telp` varchar(20) DEFAULT NULL,
  `user_alamat` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `user_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_user`
--

INSERT INTO `tb_user` (`user_id`, `user_nama`, `user_email`, `user_password`, `user_telp`, `user_alamat`, `created_at`, `user_image`) VALUES
(6, 'tes', 'tes1@gmail.com', '$2y$10$rDONjdFsuv7JSUbi42VMUOUm1ucyGSfp0snqTLFfAtqoahLH0yjvm', '000000', 'jl.makmur', '2025-10-17 20:39:05', NULL),
(9, 'kkkal', 'kal@gmail.com', '$2y$10$/1.IecTPwgE6cxPsAw4g1.0EGx6lPb1wEm9imzEVRvYQ.WdpDOL3q', '888888888888', 'makmoer', '2026-02-04 07:49:26', NULL),
(10, 'kkkal', 'kal2@gmail.com', '$2y$10$RRkrQWj0xFu2Nra6..8rouu25CdCB5SHs6kS/7k8VEb31nTK3Q.qe', '1376518763', 'Jl. Asmin', '2026-02-04 10:01:39', 'user_10_1770255265.JPG');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_admin`
--
ALTER TABLE `tb_admin`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `tb_category`
--
ALTER TABLE `tb_category`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `tb_checkout`
--
ALTER TABLE `tb_checkout`
  ADD PRIMARY KEY (`checkout_id`);

--
-- Indexes for table `tb_pembelian`
--
ALTER TABLE `tb_pembelian`
  ADD PRIMARY KEY (`pembelian_id`);

--
-- Indexes for table `tb_product`
--
ALTER TABLE `tb_product`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `tb_user`
--
ALTER TABLE `tb_user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `user_email` (`user_email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_admin`
--
ALTER TABLE `tb_admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tb_category`
--
ALTER TABLE `tb_category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `tb_checkout`
--
ALTER TABLE `tb_checkout`
  MODIFY `checkout_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tb_pembelian`
--
ALTER TABLE `tb_pembelian`
  MODIFY `pembelian_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `tb_product`
--
ALTER TABLE `tb_product`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `tb_user`
--
ALTER TABLE `tb_user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
