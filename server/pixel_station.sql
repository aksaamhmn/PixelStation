-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 30, 2025 at 08:22 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pixel_station`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id_admin`, `username`, `password`) VALUES
(1, 'admin', '123');

-- --------------------------------------------------------

--
-- Table structure for table `games`
--

CREATE TABLE `games` (
  `id_game` int(11) NOT NULL,
  `nama` varchar(105) DEFAULT NULL,
  `tahun_rilis` varchar(50) DEFAULT NULL,
  `kategori` varchar(50) DEFAULT NULL,
  `mode_game` varchar(50) DEFAULT NULL,
  `gambar` varchar(205) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `games`
--

INSERT INTO `games` (`id_game`, `nama`, `tahun_rilis`, `kategori`, `mode_game`, `gambar`) VALUES
(1, 'Genshin Impact', '2010', 'RPG', 'Multi Player', 'game_6835c51c34d888.85296515.png'),
(2, 'Into Will', '2020', 'Strategy', 'Multi Player', 'game_68299c0a223fc8.32239642.jpg'),
(3, 'It Takes Two', '2021', 'Music/Rhythm', 'Single Player', 'game_6829a0bf29ed62.68198536.jpg'),
(4, 'Kaisen', '2000', 'Fighting', 'Single Player', 'game_6829a0d6d54670.52049804.jpg'),
(5, 'Citilazation', '2013', 'Sports', 'Multi Player', 'game_6829a5d6ed0420.67478389.jpg'),
(6, 'Horizon', '2020', 'Fighting', 'Single Player', 'game_6829a5ecae41e3.79371909.jpg'),
(7, 'Valhala', '2013', 'Stealth', 'Single Player', 'game_6829a60c4aa580.35418919.jpg'),
(8, 'Gta VI', '2017', 'Strategy', 'Single Player', 'game_6829a62454b317.82657021.jpg'),
(9, 'Emots111', '2014', 'Strategy', 'Single Player', 'game_6829a637736e57.68133811.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id_payments` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(20) NOT NULL,
  `payment_proof` varchar(255) DEFAULT NULL,
  `payment_status` varchar(20) NOT NULL DEFAULT 'pending',
  `payment_date` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id_payments`, `amount`, `payment_method`, `payment_proof`, `payment_status`, `payment_date`, `created_at`) VALUES
(39, 39000.00, 'BCA', 'payment_1748583325.png', 'rejected', '2025-05-30 12:35:25', '2025-05-30 05:35:25'),
(40, 52000.00, 'BRI', 'payment_1748583377.png', 'expired', '2025-05-30 12:36:17', '2025-05-30 05:36:17'),
(41, 39000.00, 'BCA', 'payment_1748583823.png', 'pending', '2025-05-30 12:43:43', '2025-05-30 05:43:43'),
(42, 15000.00, 'BCA', 'payment_1748584387.png', 'expired', '2025-05-30 12:53:07', '2025-05-30 05:53:07'),
(43, 13000.00, 'BCA', 'payment_1748584533.png', 'expired', '2025-05-30 12:55:33', '2025-05-30 05:55:33'),
(44, 13000.00, 'BCA', 'payment_1748584609.png', 'expired', '2025-05-30 12:56:49', '2025-05-30 05:56:49');

-- --------------------------------------------------------

--
-- Table structure for table `reservasi`
--

CREATE TABLE `reservasi` (
  `id_reservasi` int(11) NOT NULL,
  `id_room` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_payments` int(11) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `telp` varchar(50) NOT NULL,
  `reservation_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservasi`
--

INSERT INTO `reservasi` (`id_reservasi`, `id_room`, `id_user`, `id_payments`, `nama`, `username`, `telp`, `reservation_date`, `start_time`, `end_time`) VALUES
(39, 2, 1, 39, 'Aqsha Muhaimin', 'aksa.jr', '+6282177889149', '2025-05-31', '08:00:00', '11:00:00'),
(40, 2, 1, 40, 'Aqsha Muhaimin', 'aksa.jr', '+62', '2025-05-31', '11:45:00', '15:45:00'),
(41, 2, 3, 41, 'haris', 'haris.nst', '+62', '2025-05-31', '08:00:00', '11:00:00'),
(42, 7, 1, 42, 'Aqsha Muhaimin', 'aksa.jr', '+62', '2025-05-31', '08:00:00', '09:00:00'),
(43, 3, 1, 43, 'Aqsha Muhaimin', 'aksa.jr', '+62', '2025-05-30', '08:00:00', '09:00:00'),
(44, 4, 1, 44, 'Aqsha Muhaimin', 'aksa.jr', '+62', '2025-05-30', '08:00:00', '09:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

CREATE TABLE `review` (
  `id_review` int(11) NOT NULL,
  `id_reservasi` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `review_text` text NOT NULL,
  `rating` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `review`
--

INSERT INTO `review` (`id_review`, `id_reservasi`, `id_user`, `review_text`, `rating`, `created_at`) VALUES
(6, 40, 1, 'benullll', 3, '2025-05-30 06:15:50'),
(7, 44, 1, 'jelek', 1, '2025-05-30 06:03:10');

-- --------------------------------------------------------

--
-- Table structure for table `room`
--

CREATE TABLE `room` (
  `id_room` int(11) NOT NULL,
  `section_room` varchar(50) NOT NULL,
  `type_room` varchar(50) NOT NULL,
  `harga` int(11) NOT NULL,
  `keterangan` varchar(325) NOT NULL,
  `gambar` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room`
--

INSERT INTO `room` (`id_room`, `section_room`, `type_room`, `harga`, `keterangan`, `gambar`) VALUES
(1, 'reguler 1', 'reguler', 13000, '', 'room.png'),
(2, 'reguler 2', 'reguler', 13000, '', 'room.png'),
(3, 'reguler 3', 'reguler', 13000, '', 'room.png'),
(4, 'reguler 4', 'reguler', 13000, '', 'room.png'),
(5, 'reguler 5', 'reguler', 13000, '', 'room.png'),
(6, 'vip 1', 'vip', 15000, '', 'room.png'),
(7, 'vip 2', 'vip', 15000, '', 'room.png'),
(8, 'vip 3', 'vip', 15000, 'TIDAK BOLEH MEROKOK TIDAK BOLEH MEROKOK ', 'room.png'),
(9, 'private 1', 'private', 20000, '', 'room.png');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `nama`, `username`, `email`, `password`) VALUES
(1, 'Aqsha Muhaimin', 'aksa.jr', 'aqsha@gmail.com', '123'),
(2, 'aksa', 'muhammad.aqsha', 'haris@gmail.com', '123'),
(3, 'haris', 'haris.nst', 'haris1@gmail.com', '123'),
(4, 'aksa', 'haris.sr', 'haris5@gmail.com', '123'),
(5, 'aksa', 'haris.s', 'haris6@gmail.com', '1'),
(6, 'haris', 'aksa.j', 'asas@gmail.com', '123'),
(7, 'Riyan Nurizqy', 'riyan.ky', 'riyannuriz@gmail.com', 'riyan123'),
(8, 'melsaamanda', 'melsa.amanda', 'melsa@gmail.com', '123'),
(9, 'zidan', 'zidan', 'zidan@gmail.com', '123'),
(10, 'yajid', 'yajid.salman', 'yajid.salman@gmail.com', '123'),
(11, 'sukma', 'sukma', 'sukma@gmail.com', '123'),
(12, 'ibay', 'ibay', 'ibay@gmail.com', '123'),
(13, 'aaaa', 'aaaa', 'aaaa@gmail.com', '123'),
(14, 'bbb', 'bbbb', 'bbb@gmail.com', '123'),
(15, 'iqbal', 'iqbal', 'iqbal@gmail.com', '111'),
(16, 'dia', 'dia', 'dia@gail.com', '123'),
(17, 'Haris Nst', 'apis.nst', 'apisbos@gmail.com', '123'),
(18, 'apis', 'apis.jr', 'apisssa@gmail.com', '123');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`);

--
-- Indexes for table `games`
--
ALTER TABLE `games`
  ADD PRIMARY KEY (`id_game`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id_payments`);

--
-- Indexes for table `reservasi`
--
ALTER TABLE `reservasi`
  ADD PRIMARY KEY (`id_reservasi`) USING BTREE,
  ADD KEY `id_room` (`id_room`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_payments` (`id_payments`);

--
-- Indexes for table `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`id_review`),
  ADD KEY `id_reservasi` (`id_reservasi`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `room`
--
ALTER TABLE `room`
  ADD PRIMARY KEY (`id_room`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`) USING BTREE,
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
-- AUTO_INCREMENT for table `games`
--
ALTER TABLE `games`
  MODIFY `id_game` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id_payments` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `reservasi`
--
ALTER TABLE `reservasi`
  MODIFY `id_reservasi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `review`
--
ALTER TABLE `review`
  MODIFY `id_review` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `room`
--
ALTER TABLE `room`
  MODIFY `id_room` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `reservasi`
--
ALTER TABLE `reservasi`
  ADD CONSTRAINT `reservasi_ibfk_1` FOREIGN KEY (`id_room`) REFERENCES `room` (`id_room`),
  ADD CONSTRAINT `reservasi_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`),
  ADD CONSTRAINT `reservasi_ibfk_3` FOREIGN KEY (`id_payments`) REFERENCES `payments` (`id_payments`);

--
-- Constraints for table `review`
--
ALTER TABLE `review`
  ADD CONSTRAINT `FK_review_reservasi` FOREIGN KEY (`id_reservasi`) REFERENCES `reservasi` (`id_reservasi`),
  ADD CONSTRAINT `FK_review_users` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
