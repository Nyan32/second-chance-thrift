-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 13, 2024 at 10:07 PM
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
-- Database: `second_chance_thrift`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `remove_expired_cart` ()   BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE id_produk_i INT;
    DECLARE jumlah_beli_i BIGINT;

    DECLARE cur CURSOR FOR SELECT id_produk, jumlah_beli FROM keranjang WHERE waktu_keranjang <= NOW() - INTERVAL 1 HOUR;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    OPEN cur;

    product_loop: LOOP
        FETCH cur INTO id_produk_i, jumlah_beli_i;
        IF done THEN
            LEAVE product_loop;
        END IF;

        UPDATE produk SET stok = stok + jumlah_beli_i WHERE id_produk = id_produk_i;
    END LOOP;

    DELETE FROM keranjang WHERE waktu_keranjang <= NOW() - INTERVAL 1 HOUR;
    CLOSE cur;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `remove_expired_transaction` ()   BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE id_produk_i INT;
    DECLARE jumlah_beli_i BIGINT;

    DECLARE cur CURSOR FOR SELECT id_produk, SUM(jumlah_beli) FROM riwayat_transaksi WHERE waktu_transaksi <= NOW() - INTERVAL 15 MINUTE AND status='waiting' GROUP BY id_produk;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    OPEN cur;

    product_loop: LOOP
        FETCH cur INTO id_produk_i, jumlah_beli_i;
        IF done THEN
            LEAVE product_loop;
        END IF;

        UPDATE produk SET stok = stok + jumlah_beli_i WHERE id_produk = id_produk_i;
    END LOOP;

    UPDATE riwayat_transaksi SET status='fail' WHERE waktu_transaksi <= NOW() - INTERVAL 15 MINUTE AND status='waiting';
    CLOSE cur;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `akun`
--

CREATE TABLE `akun` (
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `alamat` mediumtext NOT NULL,
  `nomor_telepon` varchar(13) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `akun`
--

INSERT INTO `akun` (`email`, `password`, `nama`, `alamat`, `nomor_telepon`) VALUES
('budi_santoso@gmail.com', 'budi12345', 'Budi Santoso', 'Jalan Mangga Manis', '081288992211'),
('coba@gmail.com', 'coba12345', 'Coba', 'Jalan Pisang Lama', '088112341234');

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int(10) UNSIGNED NOT NULL,
  `nama` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `nama`) VALUES
(1, 'Fashion Pria'),
(2, 'Fashion Wanita'),
(3, 'Fashion Anak'),
(4, 'Fashion Muslim');

-- --------------------------------------------------------

--
-- Table structure for table `keranjang`
--

CREATE TABLE `keranjang` (
  `id_keranjang` int(10) NOT NULL,
  `id_produk` int(10) UNSIGNED NOT NULL,
  `jumlah_beli` int(10) NOT NULL,
  `email` varchar(255) NOT NULL,
  `waktu_keranjang` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id_produk` int(10) UNSIGNED NOT NULL,
  `nama` varchar(50) NOT NULL,
  `deskripsi` text NOT NULL,
  `harga` bigint(20) UNSIGNED NOT NULL,
  `stok` int(10) NOT NULL,
  `berat` int(10) UNSIGNED NOT NULL,
  `gambar` varchar(255) NOT NULL,
  `jumlah_dibeli` int(10) UNSIGNED NOT NULL,
  `diskon` bigint(20) UNSIGNED NOT NULL,
  `id_kategori` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id_produk`, `nama`, `deskripsi`, `harga`, `stok`, `berat`, `gambar`, `jumlah_dibeli`, `diskon`, `id_kategori`) VALUES
(1, 'Kemeja Kotak-Kotak Lengan Panjang', 'Kemeja kotak-kotak lengan panjang yang trendy', 75000, 6, 350, 'produk-1.jpg', 4, 0, 1),
(2, 'Kaos Hitam Animasi Naruto', 'Kaos hitam animasi naruto dengan berbagai karakter seperti naruto, sasuke, sakura, kakashi, itachi dan banyak lagi.', 30000, 11, 100, 'produk-2.jpg', 5, 0, 3);

-- --------------------------------------------------------

--
-- Table structure for table `riwayat_transaksi`
--

CREATE TABLE `riwayat_transaksi` (
  `id_riwayat_transaksi` int(10) NOT NULL,
  `email` varchar(255) NOT NULL,
  `id_produk` int(10) UNSIGNED NOT NULL,
  `waktu_transaksi` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(10) NOT NULL,
  `kode_transaksi` varchar(255) NOT NULL,
  `bukti_transaksi` varchar(255) NOT NULL,
  `jumlah_beli` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `riwayat_transaksi`
--

INSERT INTO `riwayat_transaksi` (`id_riwayat_transaksi`, `email`, `id_produk`, `waktu_transaksi`, `status`, `kode_transaksi`, `bukti_transaksi`, `jumlah_beli`) VALUES
(11, 'coba@gmail.com', 2, '2024-04-13 19:18:04', 'validating', 'cd3257643f189eab0d87e697841e776b9b6b7f6ca2d23f802df8338b4a2d7a88', '', 1),
(12, 'budi_santoso@gmail.com', 1, '2024-04-13 19:33:04', 'fail', '4063b7755c301a7f9321ce38c691e07cc905797ecbcd34946fa20423ec7107c2', '', 1),
(13, 'coba@gmail.com', 2, '2024-04-13 19:00:13', 'fail', '356498b3bec33d5982c9f7afea79100207f32329b7ddd4aab61da76c3fe16c01', '', 5),
(14, 'budi_santoso@gmail.com', 1, '2024-04-13 19:49:29', 'success', '2c78f77f88b4756b7aee4033879d0901a97de5123b384fa2ef3a18685c7d290c', '', 4),
(15, 'budi_santoso@gmail.com', 2, '2024-04-13 19:49:29', 'success', '2c78f77f88b4756b7aee4033879d0901a97de5123b384fa2ef3a18685c7d290c', '', 5),
(16, 'budi_santoso@gmail.com', 2, '2024-04-13 19:00:44', 'fail', '772ee6ea27b45f2e782207a2f17a5a5a2d6305fbc6632446c862320ffb248290', '', 6),
(17, 'budi_santoso@gmail.com', 2, '2024-04-13 19:00:31', 'validating', 'f51ebb7dc470883272bdfc5fdf5ea12435b540d0bbdce5bc110346d3654dc028', '', 3);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `akun`
--
ALTER TABLE `akun`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indexes for table `keranjang`
--
ALTER TABLE `keranjang`
  ADD PRIMARY KEY (`id_keranjang`),
  ADD KEY `produk_keranjang_rel` (`id_produk`),
  ADD KEY `akun_keranjang_rel` (`email`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id_produk`),
  ADD KEY `kategori_produk_rel` (`id_kategori`);

--
-- Indexes for table `riwayat_transaksi`
--
ALTER TABLE `riwayat_transaksi`
  ADD PRIMARY KEY (`id_riwayat_transaksi`),
  ADD KEY `akun_riwayat_transaksi_rel` (`email`),
  ADD KEY `produk_riwayat_transaksi_rel` (`id_produk`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `keranjang`
--
ALTER TABLE `keranjang`
  MODIFY `id_keranjang` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id_produk` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `riwayat_transaksi`
--
ALTER TABLE `riwayat_transaksi`
  MODIFY `id_riwayat_transaksi` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `keranjang`
--
ALTER TABLE `keranjang`
  ADD CONSTRAINT `akun_keranjang_rel` FOREIGN KEY (`email`) REFERENCES `akun` (`email`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `produk_keranjang_rel` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `produk`
--
ALTER TABLE `produk`
  ADD CONSTRAINT `kategori_produk_rel` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `riwayat_transaksi`
--
ALTER TABLE `riwayat_transaksi`
  ADD CONSTRAINT `akun_riwayat_transaksi_rel` FOREIGN KEY (`email`) REFERENCES `akun` (`email`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `produk_riwayat_transaksi_rel` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`);

DELIMITER $$
--
-- Events
--
CREATE DEFINER=`root`@`localhost` EVENT `remove_expired_cart` ON SCHEDULE EVERY 1 MINUTE STARTS '2024-04-12 16:07:04' ENDS '2034-04-12 16:07:04' ON COMPLETION NOT PRESERVE ENABLE DO CALL remove_expired_cart()$$

CREATE DEFINER=`root`@`localhost` EVENT `remove_expired_transaction` ON SCHEDULE EVERY 15 SECOND STARTS '2024-04-12 16:07:04' ENDS '2034-04-12 16:07:04' ON COMPLETION NOT PRESERVE ENABLE DO CALL remove_expired_transaction()$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
