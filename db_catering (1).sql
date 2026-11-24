-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 24, 2026 at 01:59 AM
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
-- Database: `db_catering`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_admin` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id_admin`, `username`, `password`, `nama_admin`) VALUES
(1, 'admin', 'admin123', 'Administrator');

-- --------------------------------------------------------

--
-- Table structure for table `bahan_baku`
--

CREATE TABLE `bahan_baku` (
  `id_bahan` int(11) NOT NULL,
  `nama_bahan` varchar(100) NOT NULL,
  `kategori` varchar(50) DEFAULT 'Bahan Basah',
  `stok` decimal(10,2) NOT NULL DEFAULT 0.00,
  `satuan` varchar(20) NOT NULL DEFAULT 'kg',
  `stok_minimal` decimal(10,2) NOT NULL DEFAULT 5.00,
  `harga_satuan` decimal(12,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bahan_baku`
--

INSERT INTO `bahan_baku` (`id_bahan`, `nama_bahan`, `kategori`, `stok`, `satuan`, `stok_minimal`, `harga_satuan`) VALUES
(1, 'Ayam ', 'Daging & Unggas', 0.00, 'kg', 5.00, 40000.00),
(2, 'Beras Premium', 'Bahan Pokok', 0.00, 'liter', 10.00, 14000.00),
(3, 'Minyak Goreng', 'Bahan Pokok', 32.98, 'liter', 5.00, 16000.00),
(4, 'Box Catering', 'Kemasan', 0.00, 'pcs', 50.00, 1500.00),
(5, 'Telur Ayam', 'Daging & Unggas', 0.00, 'kg', 5.00, 28000.00),
(6, 'cabe rawit', 'Bahan Basah', 28.96, 'kg', 5.00, 60000.00),
(8, 'lalapan', 'Bahan Basah', 28.96, 'kg', 5.00, 15000.00),
(9, 'mie telor', 'Bahan Basah', 50.00, 'pcs', 5.00, 5000.00),
(12, 'kulit lumpia', 'Bahan Basah', 49.00, 'kg', 5.00, 17000.00),
(13, 'kentang', 'Bahan Basah', 48.90, 'kg', 5.00, 10000.00),
(14, 'wortel', 'Bahan Basah', 48.90, 'kg', 5.00, 10000.00),
(15, 'oncom ', 'Bahan Basah', 50.00, 'pcs', 5.00, 4000.00),
(16, 'tepung terigu', 'Bahan Basah', 48.90, 'kg', 5.00, 15000.00),
(17, 'singkong', 'Bahan Basah', 50.00, 'kg', 5.00, 6000.00),
(18, 'kacang tanah', 'Bahan Basah', 48.50, 'kg', 5.00, 17000.00),
(19, 'baso goreng', 'Bahan Basah', 50.00, 'bungkus', 5.00, 11000.00),
(20, 'coklat', 'Bahan Basah', 50.00, 'kg', 5.00, 14000.00),
(21, 'tepung beras', 'Bahan Basah', 0.00, 'kg', 5.00, 14000.00);

-- --------------------------------------------------------

--
-- Table structure for table `belanja_bahan`
--

CREATE TABLE `belanja_bahan` (
  `id_belanja` int(11) NOT NULL,
  `tanggal_belanja` date NOT NULL,
  `id_bahan` int(11) NOT NULL,
  `jumlah_beli` decimal(10,2) NOT NULL,
  `total_biaya` decimal(12,2) NOT NULL,
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `catat_belanja`
--

CREATE TABLE `catat_belanja` (
  `id_belanja` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `keperluan` varchar(255) NOT NULL,
  `total_biaya` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `catat_belanja`
--

INSERT INTO `catat_belanja` (`id_belanja`, `tanggal`, `keperluan`, `total_biaya`) VALUES
(1, '2026-10-11', 'Modal Otomatis Pesanan ID: MC-1006 (100 Porsi)', 840000.00),
(2, '2026-10-11', 'Modal Otomatis Pesanan ID: MC-1007 (1 Porsi)', 8720.00),
(3, '2026-10-11', 'Modal Otomatis Pesanan ID: MC-1008 (Multi-Menu)', 8400.00),
(4, '2026-10-11', 'Modal Otomatis Pesanan ID: MC-1009 (Multi-Menu)', 1744000.00),
(5, '2026-10-24', 'Modal Otomatis Pesanan ID: MC-1010 (Multi-Menu)', 948000.00),
(6, '2026-10-24', 'Modal Otomatis Pesanan ID: MC-1021 (Multi-Menu)', 436000.00),
(7, '2026-10-31', 'Modal Otomatis Pesanan ID: MC-1024 (Multi-Menu)', 905000.00),
(8, '2026-10-31', 'Modal Otomatis Pesanan ID: MC-1025 (Multi-Menu)', 893000.00),
(9, '2026-11-03', 'Modal Otomatis Pesanan ID: MC-1028 (Multi-Menu)', 872000.00),
(10, '2026-11-06', 'Modal Otomatis Pesanan ID: MC-1030 (Multi-Menu)', 872000.00),
(11, '2026-11-18', 'Belanja Pasar: belanja ayam, beras,cabe,minyak dll', 700.00),
(12, '2026-11-18', 'Belanja Pasar: belanja ayam, beras,cabe,minyak dll', 500000.00),
(13, '2026-11-24', 'Modal Otomatis Pesanan ID: MC-1036 (Multi-Menu)', 872000.00);

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `id_menu` int(11) NOT NULL,
  `nama_menu` varchar(100) NOT NULL,
  `kategori` enum('makanan-berat','makanan-ringan','snack','dessert','drink') NOT NULL,
  `harga` int(11) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `foto` varchar(255) DEFAULT 'default.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`id_menu`, `nama_menu`, `kategori`, `harga`, `deskripsi`, `foto`) VALUES
(1, 'Nasi Ayam Bakar', 'makanan-berat', 25000, 'Nasi ayam bakar komplit dengan sayuran, tahu, tempe dan sambal.', 'bakar.jpeg'),
(2, 'Nasi Ayam Goreng', 'makanan-berat', 28000, 'Nasi dengan ayam goreng kremes, sambal dan sayuran.', 'goreng.jpeg'),
(3, 'Nasi Kuning Ayam Goreng', 'makanan-berat', 22000, 'Nasi kuning dengan ayam goreng, orek tempe, sambal, mie goreng, lalapan.', 'Nasi kuning.jpg'),
(4, 'Ayam Geprek', 'makanan-berat', 22000, 'Ayam crispy dengan sambal geprek pedas dan lalapan.', 'ayam geprek.jpeg'),
(5, 'Martabak Telur', 'makanan-ringan', 2500, 'Martabak telur gurih dengan cabai rawit.', 'malor.jpeg'),
(6, 'Pastel', 'makanan-ringan', 2500, 'Pastel dengan isian daging dan sayuran segar.', 'pasel.jpeg'),
(7, 'Sosis Solo', 'makanan-ringan', 2500, 'Sosis solo lembut isi daging ayam cincang.', 'sosis.jpeg'),
(8, 'Lontong', 'makanan-ringan', 2500, 'Lontong gurih dengan isi oncom dan sayuran.', 'lonsong.jpeg'),
(9, 'Keripik Singkong', 'snack', 18000, 'Keripik singkong varian rasa asin, pedas, keju dll.', 'kripik singkong.jpg'),
(10, 'Peyek Renyah', 'snack', 50000, 'Peyek renyah varian kacang dan ikan asin.', 'peek.jpeg'),
(11, 'Kacang Bawang', 'snack', 25000, 'Kacang bawang renyah dan gurih khas rumahan.', 'kacang.jpeg'),
(12, 'Basreng Pedas', 'snack', 12000, 'Bakso goreng renyah dengan bumbu pedas manis.', 'basreng.jpeg'),
(13, 'Donat Gula Salju', 'dessert', 3500, 'Donat lembut dengan gula salju manis pas.', 'donpung.jpeg'),
(14, 'Bolu Coklat Kukus', 'dessert', 35000, 'Bolu coklat kukus lembut dan lumer.', 'boluu.jpeg'),
(15, 'Dadar Gulung', 'dessert', 3500, 'Dadar gulung lembut dengan isian coklat/kelapa.', 'darlung.jpeg'),
(16, 'Brownies Coklat', 'dessert', 15000, 'Brownies coklat lezat dan legit.', 'brow.webp'),
(17, 'Sop Buah Segar', 'drink', 18000, 'Sop buah segar dengan isian buah pilihan.', 'sopbuah.jpg'),
(18, 'Aneka Jus Buah', 'drink', 17000, 'Jus segar mangga, alpukat, jambu, buah naga dll.', 'Anekajus.png'),
(19, 'Es Teh Manis', 'drink', 7000, 'Es teh manis yang menyegarkan.', 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?auto=format&fit=crop&w=600&q=80'),
(20, 'Es Jeruk Peras', 'drink', 10000, 'Minuman es jeruk peras asli dan segar.', 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?auto=format&fit=crop&w=600&q=80');

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

CREATE TABLE `pesanan` (
  `id_pesanan` int(11) NOT NULL,
  `nama_pemesan` varchar(100) NOT NULL,
  `nomor_telepon` varchar(20) NOT NULL,
  `alamat` text NOT NULL,
  `detail_pesanan` text NOT NULL,
  `total_harga` int(11) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Pending',
  `tanggal_pesan` timestamp NOT NULL DEFAULT current_timestamp(),
  `metode_pembayaran` varchar(50) NOT NULL DEFAULT 'Transfer Bank BCA',
  `bukti_pembayaran` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pesanan`
--

INSERT INTO `pesanan` (`id_pesanan`, `nama_pemesan`, `nomor_telepon`, `alamat`, `detail_pesanan`, `total_harga`, `status`, `tanggal_pesan`, `metode_pembayaran`, `bukti_pembayaran`) VALUES
(1, 'bagus', '543219876', 'bekasi, Kel. bahagia, Kec. babelan 56789', 'Nasi Kuning Ayam Goreng (1x) | Catatan: sambal pedas', 22000, 'Selesai', '2026-10-03 17:00:00', 'Transfer Bank BCA', NULL),
(2, 'jaka', '08987654321', 'bekasi, Kel. bahagia, Kec. babelan 54321', 'Ayam Geprek (150x) | Catatan: sambal pedas', 2805000, 'Selesai', '2026-10-09 17:00:00', 'Transfer Bank BCA', NULL),
(3, 'judi', '18987651234', 'bekasi, Kel. bahagia, Kec. babelan 12354', 'Nasi Ayam Bakar (100x), Sosis Solo (100x), Peyek Renyah (100x), Donat Gula Salju (1x) | Catatan: sambal pedas', 6202800, 'Selesai', '2026-10-09 17:00:00', 'Transfer Bank BCA', NULL),
(4, 'jale', '08129876543', 'bekasi, Kel. bahagia, Kec. babelan 12354', 'Nasi Ayam Goreng (100x) | Catatan: sambal agak pedas', 2520000, 'Selesai', '2026-10-09 17:00:00', 'Transfer Bank BCA', NULL),
(5, 'jale', '0812543219876', 'bekasi, Kel. bahagia, Kec. babelan 12345', 'Nasi Ayam Bakar (100x) | Catatan: sambal pedas', 2250000, 'Selesai', '2026-10-16 17:00:00', 'Transfer Bank BCA', NULL),
(6, 'jale', '08123456789', 'bekasi, Kel. bahagia, Kec. babelan 12354', 'Ayam Geprek (150x) | Catatan: sambal pedas', 2805000, 'Selesai', '2026-10-17 17:00:00', 'Transfer Bank BCA', NULL),
(7, 'jale', '08123456789', 'bekasi, Kel. bahagia, Kec. babelan 12354', 'Nasi Ayam Goreng (50x), Martabak Telur (50x), Peyek Renyah (50x) | Catatan: untuk acara jam 12 siang', 3421250, 'Diproses', '2026-10-16 17:00:00', 'Transfer Bank BCA', NULL),
(8, 'jale', '08123456789', 'bekasi, Kel. bahagia, Kec. babelan 12354', 'Nasi Ayam Bakar (1x) | Catatan: sambal pedas', 25000, 'Diproses', '2026-10-15 17:00:00', 'Transfer Bank BCA', NULL),
(9, 'jale', '08123456789', 'bekasi, Kel. bahagia, Kec. babelan 12354', 'Nasi Ayam Bakar (100x), Nasi Ayam Goreng (100x) | Catatan: sambal agak pedas', 4240000, 'Siap Dikirim', '2026-10-15 17:00:00', 'GoPay', NULL),
(10, 'rijal', '081389280741', 'Raya Bogor, Kel. contoh, Kec. babelan 56789', 'Nasi Ayam Bakar (100x), Martabak Telur (50x), Peyek Renyah (100x) | Catatan: P', 6100000, 'Diproses', '2026-10-27 17:00:00', 'GoPay', NULL),
(11, 'rijal', '08987654321', 'Cimanggis, Kel. bahagia, Kec. babelan 12345', 'Nasi Ayam Bakar (100x) | Catatan: p', 2250000, 'Dibatalkan', '2026-10-27 17:00:00', 'QRIS', NULL),
(12, 'rijal', '08123456789', 'nk, Kel. bahagia, Kec. babelan 12354', 'Nasi Ayam Bakar (1x) | Catatan: huhu', 25000, 'Dibatalkan', '2026-10-18 17:00:00', 'QRIS', NULL),
(13, 'rijal', '08123456789', 'nk, Kel. bahagia, Kec. babelan 12354', 'Nasi Ayam Bakar (1x) | Catatan: huhu', 25000, 'Dibatalkan', '2026-10-18 17:00:00', 'Transfer Bank BCA', NULL),
(14, 'rijal', '08123456789', 'nk, Kel. bahagia, Kec. babelan 12354', 'Nasi Ayam Bakar (1x) | Catatan: huhu', 25000, 'Dibatalkan', '2026-10-18 17:00:00', 'Transfer Bank BCA', NULL),
(15, 'rijal', '08123456789', 'bekasi, Kel. contoh, Kec. contoh 12354', 'Nasi Ayam Bakar (100x) | Catatan: mantap', 2250000, 'Dibatalkan', '2026-10-17 17:00:00', 'QRIS', NULL),
(16, 'rijal', '08123456789', 'bekasi, Kel. contoh, Kec. contoh 12354', 'Nasi Ayam Goreng (100x) | Catatan: contoh', 2520000, 'Pending', '2026-10-24 17:00:00', 'GoPay', NULL),
(17, 'rijal', '08123456789', 'bekasi, Kel. contoh, Kec. contoh 12354', 'Nasi Ayam Goreng (100x) | Catatan: contoh', 2520000, 'Pending', '2026-10-24 17:00:00', 'GoPay', NULL),
(18, 'rijal', '08987654321', 'ewqe, Kel. bahagia, Kec. babelan 56789', 'Nasi Ayam Bakar (100x) | Catatan: s', 2250000, 'Pending', '2026-10-26 17:00:00', 'DANA', NULL),
(19, 'rijal', '08123456789', 'bekasi, Kel. contoh, Kec. contoh 12354', 'Nasi Ayam Bakar (1x) | Catatan: contoh', 25000, 'Pending', '2026-10-24 17:00:00', 'GoPay', NULL),
(20, 'rijal', '08123456789', 'bekasi, Kel. contoh, Kec. contoh 12354', 'Nasi Ayam Bakar (100x) | Catatan: contoh', 2250000, 'Pending', '2026-10-24 17:00:00', 'QRIS', NULL),
(21, 'rijal', '08123456789', 'bekasi, Kel. contoh, Kec. contoh 12354', 'Nasi Ayam Bakar (50x) | Catatan: contoh', 1250000, 'Diproses', '2026-10-24 17:00:00', 'QRIS', NULL),
(22, 'rijal', '08123456789', 'bekasi, Kel. contoh, Kec. contoh 12354', 'Nasi Ayam Goreng (100x) | Catatan: contoh', 2520000, 'Pending', '2026-10-24 17:00:00', 'Transfer Bank BCA', NULL),
(23, 'rijal', '08123456789', 'bekasi, Kel. contoh, Kec. contoh 12354', 'Nasi Kuning Ayam Goreng (100x) | Catatan: contoh', 1980000, 'Pending', '2026-10-30 17:00:00', 'GoPay', NULL),
(24, 'lukas', '08123456789', 'bekasi, Kel. contoh, Kec. contoh 12354', 'Nasi Kuning Ayam Goreng (100x), Sosis Solo (50x), Peyek Renyah (50x) | Catatan: contoh', 3860000, 'Diproses', '2026-10-31 17:00:00', 'QRIS', NULL),
(25, 'lukas', '08123456789', 'jakarta, Kel. contoh, Kec. contoh 12354', 'Nasi Ayam Goreng (100x), Pastel (60x), Basreng Pedas (50x) | Catatan: contoh', 2840000, 'Diproses', '2026-11-06 17:00:00', 'GoPay', NULL),
(26, 'lukas', '08123456789', 'jakarta, Kel. contoh, Kec. contoh 12354', 'Ayam Geprek (100x) | Catatan: contoh', 1980000, 'Pending', '2026-10-31 17:00:00', 'DANA', NULL),
(27, 'lukas', '08123456789', 'jakarta, Kel. contoh, Kec. contoh 12354', 'Martabak Telur (100x) | Catatan: contoh', 225000, 'Pending', '2026-10-31 17:00:00', 'Transfer Bank BCA', NULL),
(28, 'lukas', '08123456789', 'bogor, Kel. contoh, Kec. contoh 12354', 'Nasi Ayam Bakar (100x) | Catatan: contoh', 2250000, 'Diproses', '2026-11-06 17:00:00', 'QRIS', NULL),
(29, 'lukas', '08123456789', 'bogor, Kel. contoh, Kec. contoh 12354', 'Nasi Ayam Bakar (100x) | Catatan: contoh', 2250000, 'Pending', '2026-11-07 17:00:00', 'Transfer Bank BCA', NULL),
(30, 'lukas', '08987654321', 'cakung, Kel. contoh, Kec. contoh 56789', 'Nasi Ayam Goreng (100x) | Catatan: contoh', 2520000, 'Diproses', '2026-11-13 17:00:00', 'GoPay', NULL),
(31, 'lukas', '08123456789', 'kranji, Kel. contoh, Kec. contoh 56789', 'Martabak Telur (100x) | Catatan: contoh', 225000, 'Pending', '2026-11-07 17:00:00', 'Transfer Bank BCA', NULL),
(32, 'lukas', '08123456789', 'klender, Kel. contoh, Kec. contoh 12354', 'Pastel (100x) | Catatan: contoh', 225000, 'Pending', '2026-11-07 17:00:00', 'DANA', NULL),
(33, 'lukas', '18987651234', 'grand duta city, Kel. contoh, Kec. contoh 12354', 'Nasi Ayam Bakar (100x) | Catatan: contoh', 2250000, 'Pending', '2026-11-13 17:00:00', 'Transfer Bank BCA', NULL),
(34, 'lukas', '08123456789', 'marakash, Kel. contoh, Kec. contoh 12354', 'Martabak Telur (100x) | Catatan: contoh', 225000, 'Pending', '2026-11-07 17:00:00', 'DANA', NULL),
(35, 'lukas', '08123456789', 'sanur, Kel. contoh, Kec. contoh 12354', 'Nasi Ayam Bakar (100x) | Catatan: contoh', 2250000, 'Pending', '2026-11-13 17:00:00', 'GoPay', 'bukti_6aedd397e23ca.jpg'),
(36, 'Ahmad Rizal Zidane Nugroho', '08123456789', 'bekasi utara, Kel. bahagia, Kec. babelan 12354', 'Nasi Ayam Bakar (100x) | Catatan: acara dimulai jam 12 siang', 2250000, 'Diproses', '2026-11-27 17:00:00', 'QRIS', 'bukti_6b04d9382f2e8.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id_produk` int(11) NOT NULL,
  `nama_produk` varchar(255) NOT NULL,
  `kategori` varchar(100) NOT NULL,
  `harga` int(11) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `promo`
--

CREATE TABLE `promo` (
  `id_promo` int(11) NOT NULL,
  `kode_promo` varchar(50) NOT NULL,
  `diskon` int(11) NOT NULL,
  `min_transaksi` int(11) NOT NULL,
  `tgl_berakhir` date NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `status` enum('Aktif','Nonaktif') DEFAULT 'Aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `promo`
--

INSERT INTO `promo` (`id_promo`, `kode_promo`, `diskon`, `min_transaksi`, `tgl_berakhir`, `deskripsi`, `status`) VALUES
(1, 'PROMO HEMAT', 10, 100, '2026-12-31', 'Dapatkan potongan harga 10% untuk pemesanan minimal 100 box.', 'Aktif'),
(2, 'JUMAT BERKAH', 15, 150, '2026-12-31', 'Khusus pemesanan setiap hari Jumat dengan minimal 150 box.', 'Aktif'),
(3, 'WEDDING SPECIAL', 20, 200, '2026-12-31', 'Harga spesial untuk pemesanan catering acara pernikahan.', 'Aktif'),
(4, 'sepecial hari ulang tahun', 5, 125, '2026-10-11', 'untuk anda yang ulang tahun sampai tanggal 11 bulan oktober, anda bisa mendapatkan sepecial promo ini', 'Nonaktif'),
(5, 'sepecial hari ulang tahun', 3, 50, '2026-11-22', 'special hari ulang tahun anda bisa mendapatkan diskon 3%', 'Aktif');

-- --------------------------------------------------------

--
-- Table structure for table `resep_menu`
--

CREATE TABLE `resep_menu` (
  `id_resep` int(11) NOT NULL,
  `id_menu` int(11) NOT NULL,
  `id_bahan` int(11) NOT NULL,
  `takaran` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `nama`, `email`, `password`, `created_at`) VALUES
(1, 'jale', 'jale@gmail.com', '$2y$10$eDgsdybQj4TRr40.Pond1.i3ud2gnuQMxGsuRrYl0.timVXAxAyW2', '2026-10-04 05:13:23'),
(2, 'rijal', 'rizalzidan123@gmail.com', '$2y$10$543WouoEdRh3umlvdqS6Y.Z3xVr5eSvsywDUikOwyxqNCh8CMMt1G', '2026-10-24 03:46:33'),
(3, 'lukas', 'lukas@gmail.com', '$2y$10$A2baicYP2SmXmdgK6RLJsuHLmfsI8AkN9tY8UWTpLoZHFv02QClAS', '2026-10-31 02:38:58'),
(4, 'Ahmad Rizal Zidane Nugroho', 'ahmad@gmail.com', '$2y$10$9loK6XMeuifRSXnS96lP0ebtk59Y7DE7oJfusiPX.lO5l6ttZMOye', '2026-11-24 00:21:40');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `bahan_baku`
--
ALTER TABLE `bahan_baku`
  ADD PRIMARY KEY (`id_bahan`);

--
-- Indexes for table `belanja_bahan`
--
ALTER TABLE `belanja_bahan`
  ADD PRIMARY KEY (`id_belanja`);

--
-- Indexes for table `catat_belanja`
--
ALTER TABLE `catat_belanja`
  ADD PRIMARY KEY (`id_belanja`);

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id_menu`);

--
-- Indexes for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`id_pesanan`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id_produk`);

--
-- Indexes for table `promo`
--
ALTER TABLE `promo`
  ADD PRIMARY KEY (`id_promo`);

--
-- Indexes for table `resep_menu`
--
ALTER TABLE `resep_menu`
  ADD PRIMARY KEY (`id_resep`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bahan_baku`
--
ALTER TABLE `bahan_baku`
  MODIFY `id_bahan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `belanja_bahan`
--
ALTER TABLE `belanja_bahan`
  MODIFY `id_belanja` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `catat_belanja`
--
ALTER TABLE `catat_belanja`
  MODIFY `id_belanja` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `id_menu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `id_pesanan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id_produk` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `promo`
--
ALTER TABLE `promo`
  MODIFY `id_promo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `resep_menu`
--
ALTER TABLE `resep_menu`
  MODIFY `id_resep` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
