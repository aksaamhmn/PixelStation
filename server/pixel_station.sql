-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 12, 2025 at 05:15 AM
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
(1, 'Sleeping Dogs', '2012', 'Open World', 'Single Player', 'game_6839a0e04fb039.64977600.jpg'),
(2, 'Tomb Raider', '2014', 'Adventure', 'Single Player', 'game_6839a0fc2f9924.05539523.jpg'),
(3, 'Watch Dogs: Legion', '2020', 'Action', 'Multi Player', 'game_6839a1150f1bd5.47666409.jpg'),
(4, 'Batman: Arkham City', '2011', 'Adventure', 'Single Player', 'game_6839a12c2171b0.28511285.jpg'),
(5, 'Rise of the Tomb Raider', '2015', 'Action', 'Single Player', 'game_6839a153d82a97.65666616.jpg'),
(6, 'Gotham Knights', '2022', 'RPG', 'Multi Player', 'game_6839a17ad403b8.90111892.jpg'),
(7, 'Pokémon Legends: Arceus', '2022', 'RPG', 'Single Player', 'game_6839a199eb6f55.64708709.jpg'),
(8, 'Hitman (2016)', '2016', 'Stealth', 'Single Player', 'game_6839a1aee2fb72.98745126.jpg'),
(9, 'The Walking Dead', '2012', 'Visual Novel', 'Single Player', 'game_6839a1c3afa9b1.07777842.jpg'),
(10, 'Watch Dogs 2', '2016', 'Open World', 'Multi Player', 'game_6839a1d8707375.57045221.jpg'),
(11, 'Mirror\'s Edge Catalyst', '2016', 'Platformer', 'Single Player', 'game_6839a1f244c442.33818199.jpg'),
(12, 'Death Stranding', '2019', 'Adventure', 'Single Player', 'game_6839a2033cf234.50060059.jpg'),
(13, 'Marvel\'s Guardians', '2021', 'Adventure', 'Multi Player', 'game_6839a21829d809.58887107.jpg'),
(14, 'Pokémon Omega Ruby', '2014', 'RPG', 'Single Player', 'game_6839a22f34edf6.61369132.jpg'),
(16, 'Pokémon Scarlet', '2022', 'RPG', 'Multi Player', 'game_6839a25f26daf9.70882365.jpg'),
(17, 'Pokémon HeartGold', '2009', 'RPG', 'Multi Player', 'game_6839a274e822b6.08950372.jpg'),
(18, 'Cyberpunk 2077', '2020', 'Action', 'Single Player', 'game_6839a28be91199.70124082.jpg'),
(19, 'Paper Mario', '2020', 'Adventure', 'Single Player', 'game_6839a2a9de7360.05116575.jpg'),
(20, 'Assassin\'s Creed', '2007', 'Stealth', 'Single Player', 'game_6839a2c1820093.91069019.jpg'),
(21, 'Grand Theft Auto V', '2013', 'Adventure', 'Multi Player', 'game_6839a2d6c344f0.51821553.jpg'),
(24, 'Mawudgesss', '505050', 'Action', 'Multi Player', 'game_68499fc114aea5.12730160.png');

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
(1, 52000.00, 'BRI', 'payment_1748616315.png', 'rejected', '2025-05-30 21:45:15', '2025-05-30 14:45:15'),
(2, 26000.00, 'GoPay', 'payment_1748617342.png', 'expired', '2025-05-30 22:02:22', '2025-05-30 15:02:22'),
(3, 20000.00, 'BCA', 'payment_1748618664.png', 'rejected', '2025-05-30 22:24:24', '2025-05-30 15:24:24'),
(4, 52000.00, 'BCA', 'payment_1748626127.png', 'pending', '2025-05-31 00:28:47', '2025-05-30 17:28:47'),
(5, 78000.00, 'BCA', 'payment_1748626194.png', 'confirmed', '2025-05-31 00:29:54', '2025-05-30 17:29:54'),
(6, 13000.00, 'BRI', 'payment_1748626220.jpeg', 'rejected', '2025-05-31 00:30:20', '2025-05-30 17:30:20'),
(7, 26000.00, 'BRI', 'payment_1748674563.jpeg', 'confirmed', '2025-05-31 13:56:03', '2025-05-31 06:56:03'),
(8, 75000.00, 'BCA', 'payment_1748675792.png', 'rejected', '2025-05-31 14:16:32', '2025-05-31 07:16:32'),
(9, 39000.00, 'Mandiri', 'payment_1749538892.png', 'confirmed', '2025-06-10 14:01:32', '2025-06-10 07:01:32');

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
  `end_time` time NOT NULL,
  `keterangan_penolakan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservasi`
--

INSERT INTO `reservasi` (`id_reservasi`, `id_room`, `id_user`, `id_payments`, `nama`, `username`, `telp`, `reservation_date`, `start_time`, `end_time`, `keterangan_penolakan`) VALUES
(1, 4, 1, 1, 'Aqsha Muhaimin', 'aksa.jr', '+6282177889149', '2025-05-31', '08:00:00', '12:00:00', NULL),
(2, 4, 3, 2, 'haris', 'haris.nst', '+62123456789', '2025-05-31', '12:15:00', '14:15:00', NULL),
(3, 9, 3, 3, 'haris', 'haris.nst', '+62123', '2025-06-03', '08:00:00', '09:00:00', 'muka lu jelek'),
(4, 2, 3, 4, 'haris', 'haris.nst', '+62123', '2025-06-02', '08:00:00', '12:00:00', NULL),
(5, 2, 3, 5, 'haris', 'haris.nst', '+62123', '2025-06-03', '08:00:00', '14:00:00', NULL),
(6, 2, 3, 6, 'haris', 'haris.nst', '+62', '2025-06-03', '14:15:00', '15:15:00', 'pembayaran tidak valid'),
(7, 5, 1, 7, 'Aqsha Muhaimin', 'aksa.jr', '+62123', '2025-05-31', '08:00:00', '10:00:00', NULL),
(8, 7, 12, 8, 'ibay', 'ibay', '+62123', '2025-06-01', '08:00:00', '13:00:00', NULL),
(9, 3, 12, 9, 'ibay', 'ibay', '+622345', '2025-06-11', '09:15:00', '12:15:00', NULL);

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
(1, 2, 3, 'tempatnya jelek, tapi untung websitenya bagus', 4, '2025-05-30 15:19:01');

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
(1, 'reguler 1', 'reguler', 13000, 'Paket standar PlayStation 5 dengan koleksi game digital lengkap yang cocok untuk bermain bersama teman. Ruangan ini dilengkapi dengan PS5 Slim, dua stick controller, dan berbagai game digital. Dapat digunakan oleh 1–2 pemain dengan tarif sewa Rp 13.000 per jam.', 'room_6839a4a21ef8a1.56912667.jpeg'),
(2, 'reguler 2', 'reguler', 13000, 'Paket standar PlayStation 5 dengan koleksi game digital lengkap yang cocok untuk bermain bersama teman. Ruangan ini dilengkapi dengan PS5 Slim, dua stick controller, dan berbagai game digital. Dapat digunakan oleh 1–2 pemain dengan tarif sewa Rp 13.000 per jam.', 'room_6839a4b28a3886.21484065.jpeg'),
(3, 'reguler 3', 'reguler', 13000, 'Paket standar PlayStation 5 dengan koleksi game digital lengkap yang cocok untuk bermain bersama teman. Ruangan ini dilengkapi dengan PS5 Slim, dua stick controller, dan berbagai game digital. Dapat digunakan oleh 1–2 pemain dengan tarif sewa Rp 13.000 per jam.', 'room_6839a4bc77d827.21584428.jpeg'),
(4, 'reguler 4', 'reguler', 13000, 'Paket standar PlayStation 5 dengan koleksi game digital lengkap yang cocok untuk bermain bersama teman. Ruangan ini dilengkapi dengan PS5 Slim, dua stick controller, dan berbagai game digital. Dapat digunakan oleh 1–2 pemain dengan tarif sewa Rp 13.000 per jam.', 'room_6839a4c5dc9482.40787345.jpeg'),
(5, 'reguler 5', 'reguler', 13000, 'Paket standar PlayStation 5 dengan koleksi game digital lengkap yang cocok untuk bermain bersama teman. Ruangan ini dilengkapi dengan PS5 Slim, dua stick controller, dan berbagai game digital. Dapat digunakan oleh 1–2 pemain dengan tarif sewa Rp 13.000 per jam.', 'room_6839a4d016eb77.87870507.jpeg'),
(6, 'vip 1', 'vip', 15000, 'Nikmati pengalaman gaming next-gen dengan PlayStation 5 dan TV 4K untuk kualitas visual terbaik. Fasilitas yang tersedia meliputi PS5, DualSense Controller, TV 4K, dan koleksi game PS5. Cocok untuk 2–3 pemain dengan harga sewa Rp 15.000 per jam.', 'room_6839a4e522a378.43728315.jpeg'),
(7, 'vip 2', 'vip', 15000, 'Nikmati pengalaman gaming next-gen dengan PlayStation 5 dan TV 4K untuk kualitas visual terbaik. Fasilitas yang tersedia meliputi PS5, DualSense Controller, TV 4K, dan koleksi game PS5. Cocok untuk 2–3 pemain dengan harga sewa Rp 15.000 per jam.', 'room_6839a4efb7f9b1.85153842.jpeg'),
(8, 'vip 3', 'vip', 15000, 'Nikmati pengalaman gaming next-gen dengan PlayStation 5 dan TV 4K untuk kualitas visual terbaik. Fasilitas yang tersedia meliputi PS5, DualSense Controller, TV 4K, dan koleksi game PS5. Cocok untuk 2–3 pemain dengan harga sewa Rp 15.000 per jam.', 'room_6839a4fa34f7e7.57716925.jpeg'),
(9, 'private 1', 'private', 20000, 'Ruang privat ideal untuk sesi gaming eksklusif atau acara gathering kecil. Dilengkapi dengan PS4/PS5, 4 controller, smart TV 55 inci, sofa yang nyaman, serta snack & minuman. Direkomendasikan untuk 4–6 orang, dengan tarif Rp 20.000 per jam.', 'room_6839a61f516045.36833343.png');

-- --------------------------------------------------------

--
-- Table structure for table `trending_games`
--

CREATE TABLE `trending_games` (
  `id` int(11) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `gambar` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trending_games`
--

INSERT INTO `trending_games` (`id`, `nama`, `gambar`) VALUES
(1, 'Fifa', 'trending_6849b1abca2916.37394046.avif'),
(7, 'Gta', 'trending_6849b7f3d70e10.75899409.avif'),
(8, 'Wutharing Wave', 'trending_6849b80d9a1da0.47958323.avif'),
(9, 'Genshin Impact', 'trending_6849b81ea187f9.80705282.avif'),
(10, 'Nba', 'trending_6849b841a64b99.15804951.avif');

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
(18, 'apis', 'apis.jr', 'apisssa@gmail.com', '123'),
(19, 'mujaer', 'muju', 'asoy@gmail.com', '123');

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
-- Indexes for table `trending_games`
--
ALTER TABLE `trending_games`
  ADD PRIMARY KEY (`id`);

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
  MODIFY `id_game` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id_payments` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `reservasi`
--
ALTER TABLE `reservasi`
  MODIFY `id_reservasi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `review`
--
ALTER TABLE `review`
  MODIFY `id_review` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `room`
--
ALTER TABLE `room`
  MODIFY `id_room` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `trending_games`
--
ALTER TABLE `trending_games`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

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
