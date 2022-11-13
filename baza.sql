-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 05, 2022 at 08:35 PM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `morawski_4a`
--

-- --------------------------------------------------------

--
-- Table structure for table `albumy`
--

CREATE TABLE `albumy` (
  `id` int(11) NOT NULL,
  `tytul` int(11) NOT NULL,
  `data` int(11) NOT NULL,
  `id_uzytkownika` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `uzytkownicy`
--

CREATE TABLE `uzytkownicy` (
  `id` int(11) NOT NULL,
  `login` varchar(16) COLLATE utf8_polish_ci NOT NULL,
  `haslo` varchar(32) COLLATE utf8_polish_ci NOT NULL,
  `email` varchar(128) COLLATE utf8_polish_ci NOT NULL,
  `zarejestrowany` date NOT NULL,
  `uprawnienia` enum('użytkownik','moderator','administrator') COLLATE utf8_polish_ci NOT NULL,
  `aktywny` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `zdjecia`
--

CREATE TABLE `zdjecia` (
  `id` int(11) NOT NULL,
  `opis` varchar(255) COLLATE utf8_polish_ci NOT NULL,
  `id_albumu` int(11) NOT NULL,
  `data` datetime NOT NULL,
  `zaakceptowane` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `zdjecia_komentarze`
--

CREATE TABLE `zdjecia_komentarze` (
  `id` int(11) NOT NULL,
  `id_zdjecia` int(11) NOT NULL,
  `id_uzytkownika` int(11) NOT NULL,
  `data` int(11) NOT NULL,
  `komentarz` text COLLATE utf8_polish_ci NOT NULL,
  `zaakceptowany` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `zdjecia_oceny`
--

CREATE TABLE `zdjecia_oceny` (
  `id_zdjecia` int(11) NOT NULL,
  `id_uzytkownika` int(11) NOT NULL,
  `ocena` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `albumy`
--
ALTER TABLE `albumy`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_uzytkownika` (`id_uzytkownika`);

--
-- Indexes for table `uzytkownicy`
--
ALTER TABLE `uzytkownicy`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `zdjecia`
--
ALTER TABLE `zdjecia`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_albumu` (`id_albumu`);

--
-- Indexes for table `zdjecia_komentarze`
--
ALTER TABLE `zdjecia_komentarze`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_zdjecia` (`id_zdjecia`),
  ADD KEY `id_uzytkownika` (`id_uzytkownika`);

--
-- Indexes for table `zdjecia_oceny`
--
ALTER TABLE `zdjecia_oceny`
  ADD KEY `id_zdjecia` (`id_zdjecia`),
  ADD KEY `id_uzytkownika` (`id_uzytkownika`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `albumy`
--
ALTER TABLE `albumy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `uzytkownicy`
--
ALTER TABLE `uzytkownicy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `zdjecia`
--
ALTER TABLE `zdjecia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `zdjecia_komentarze`
--
ALTER TABLE `zdjecia_komentarze`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `albumy`
--
ALTER TABLE `albumy`
  ADD CONSTRAINT `albumy_ibfk_1` FOREIGN KEY (`id_uzytkownika`) REFERENCES `uzytkownicy` (`id`);

--
-- Constraints for table `zdjecia`
--
ALTER TABLE `zdjecia`
  ADD CONSTRAINT `zdjecia_ibfk_1` FOREIGN KEY (`id_albumu`) REFERENCES `albumy` (`id`);

--
-- Constraints for table `zdjecia_komentarze`
--
ALTER TABLE `zdjecia_komentarze`
  ADD CONSTRAINT `zdjecia_komentarze_ibfk_1` FOREIGN KEY (`id_zdjecia`) REFERENCES `zdjecia` (`id`),
  ADD CONSTRAINT `zdjecia_komentarze_ibfk_2` FOREIGN KEY (`id_uzytkownika`) REFERENCES `uzytkownicy` (`id`);

--
-- Constraints for table `zdjecia_oceny`
--
ALTER TABLE `zdjecia_oceny`
  ADD CONSTRAINT `zdjecia_oceny_ibfk_1` FOREIGN KEY (`id_zdjecia`) REFERENCES `zdjecia` (`id`),
  ADD CONSTRAINT `zdjecia_oceny_ibfk_2` FOREIGN KEY (`id_uzytkownika`) REFERENCES `uzytkownicy` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
