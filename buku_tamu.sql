-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 11, 2025 at 03:23 PM
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
-- Database: `buku_tamu`
--

-- --------------------------------------------------------

--
-- Table structure for table `data_tamu`
--

CREATE TABLE `data_tamu` (
  `id` int(11) NOT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `no_id` varchar(20) DEFAULT NULL,
  `institusi` varchar(100) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `no_wa` varchar(15) DEFAULT NULL,
  `keperluan` text DEFAULT NULL,
  `jenis_pengguna` varchar(20) DEFAULT 'mahasiswa',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `data_tamu`
--

INSERT INTO `data_tamu` (`id`, `nama_lengkap`, `no_id`, `institusi`, `alamat`, `no_wa`, `keperluan`, `jenis_pengguna`, `created_at`) VALUES
(1, 'Galeh Wibisono', '654654', 'PT blabla', 'jalan yuk', '654654', 'asasas', 'instansi', '2025-12-09 15:24:19'),
(2, 'Galeh Wibisono', '564', 'PT blabla', 'jalan yuk', '4154', 'asas', 'instansi', '2025-12-09 15:24:40'),
(3, 'Galeh Wibisono', '564', 'PT blabla', 'jalan yuk', '4154', 'asas', 'instansi', '2025-12-09 15:25:52'),
(4, 'Galeh Wibisono', '654654', 'PT blabla', 'jalan yuk', '654654', 'asasas', 'instansi', '2025-12-09 15:26:56'),
(5, 'Galeh Wibisono', '654654', 'PT blabla', 'jalan yuk', '654654', 'asasas', 'instansi', '2025-12-09 15:29:00'),
(6, 'asdasd', '545', 'asdasd', 'sadasd', '655465', 'asdasdas', 'instansi', '2025-12-09 16:42:52');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `no_id` varchar(50) DEFAULT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` enum('mahasiswa','instansi','admin') DEFAULT NULL,
  `institusi` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `no_id`, `nama_lengkap`, `email`, `role`, `institusi`, `password`, `created_at`) VALUES
(1, '3312511092', 'Galeh Wibisono', 'galehwibisono270101@gmail.com', 'instansi', 'poltek', '$2y$10$q1BwH0jIB644iCqKU4io5usiiYxH2YAc3H7/Pck3oEvwv4gyJA72W', '2025-12-04 14:08:52'),
(8, '3312511093', 'Galeh Wibisono', 'galehwibisono270101@gmail.com', 'mahasiswa', 'poltek', '$2y$10$q1BwH0jIB644iCqKU4io5usiiYxH2YAc3H7/Pck3oEvwv4gyJA72W', '2025-12-04 14:08:52');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `data_tamu`
--
ALTER TABLE `data_tamu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `data_tamu`
--
ALTER TABLE `data_tamu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
