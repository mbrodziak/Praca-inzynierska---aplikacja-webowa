-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Czas generowania: 19 Gru 2018, 00:07
-- Wersja serwera: 10.1.36-MariaDB
-- Wersja PHP: 7.2.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `dyzury_pracownikow`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `dyzury_pracownikow`
--

CREATE TABLE `dyzury_pracownikow` (
  `id` int(11) NOT NULL,
  `id_dyzuru` int(11) NOT NULL,
  `id_pracownika` int(11) NOT NULL,
  `potwierdzone` tinyint(1) NOT NULL,
  `zarejestrowanie` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Zrzut danych tabeli `dyzury_pracownikow`
--

INSERT INTO `dyzury_pracownikow` (`id`, `id_dyzuru`, `id_pracownika`, `potwierdzone`, `zarejestrowanie`) VALUES
(3, 10, 1, 1, 1),
(9, 2, 7, 1, 1),
(11, 1, 2, 1, 1),
(12, 1, 7, 1, 1),
(13, 1, 11, 1, 1),
(14, 1, 8, 1, 1),
(16, 2, 4, 0, 1),
(17, 1, 3, 0, 0),
(18, 1, 4, 0, 1),
(20, 7, 3, 0, 1),
(22, 6, 1, 1, 1);

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `dyzury_pracownikow`
--
ALTER TABLE `dyzury_pracownikow`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT dla tabeli `dyzury_pracownikow`
--
ALTER TABLE `dyzury_pracownikow`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
