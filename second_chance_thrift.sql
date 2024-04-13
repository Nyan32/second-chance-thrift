-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 12, 2024 at 11:30 PM
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
  `waktu_keranjang` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
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
(1, 'Kemeja Kotak-Kotak Lengan Panjang', 'Kemeja kotak-kotak lengan panjang yang trendy', 75000, 10, 350, 'produk-1.jpg', 0, 0, 1),
(2, 'Kaos Hitam Animasi Naruto', 'Kaos hitam animasi naruto dengan berbagai karakter seperti naruto, sasuke, sakura, kakashi, itachi dan banyak lagi.', 30000, 20, 100, 'produk-2.jpg', 1, 0, 3);

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
  MODIFY `id_keranjang` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id_produk` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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

DELIMITER $$
--
-- Events
--
CREATE DEFINER=`root`@`localhost` EVENT `remove_expired_cart` ON SCHEDULE EVERY 1 MINUTE STARTS '2024-04-12 16:07:04' ENDS '2034-04-12 16:07:04' ON COMPLETION NOT PRESERVE ENABLE DO CALL remove_expired_cart()$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
