-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Czas generowania: 03 Gru 2018, 19:29
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
-- Struktura tabeli dla tabeli `dyzury`
--

CREATE TABLE `dyzury` (
  `id_dyzuru` int(20) NOT NULL,
  `tytul_dyzuru` text COLLATE utf8_unicode_ci,
  `data_dyzuru` date NOT NULL,
  `godzina_rozpoczecia` time NOT NULL,
  `dlugosc_dyzuru` int(11) NOT NULL,
  `ilosc_miejsc` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Zrzut danych tabeli `dyzury`
--

INSERT INTO `dyzury` (`id_dyzuru`, `tytul_dyzuru`, `data_dyzuru`, `godzina_rozpoczecia`, `dlugosc_dyzuru`, `ilosc_miejsc`) VALUES
(1, 'Dyżur 1', '2018-11-15', '09:00:00', 8, 6),
(2, 'Dyżur 2', '2018-11-16', '08:30:00', 8, 4),
(3, 'Dyżur 3', '2018-11-20', '09:30:00', 7, 5),
(4, 'Bez tytułu', '2018-11-28', '09:00:00', 8, 3),
(5, 'Bez tytułu', '2018-11-29', '09:45:00', 6, 6),
(6, '12345678', '2018-11-29', '08:00:00', 2, 2),
(7, 'No i tak', '2018-11-30', '09:00:00', 10, 14),
(8, 'Że gdzie?', '2018-12-05', '09:05:00', 9, 9),
(9, 'Bez tytułu', '2018-12-20', '06:00:00', 5, 6),
(10, 'Bez tytułu', '2018-11-29', '07:00:00', 7, 8),
(11, 'Bez tytułu', '2018-12-20', '06:00:00', 5, 8),
(12, 'no tak właśnie', '2018-12-19', '15:50:00', 7, 9),
(13, 'Bez tytułu', '2019-01-02', '08:00:00', 6, 6),
(14, 'dff', '2019-01-03', '09:00:00', 7, 7);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `dyzury_pracownikow`
--

CREATE TABLE `dyzury_pracownikow` (
  `id_dyzuru` int(11) NOT NULL,
  `id_pracownika` int(11) NOT NULL,
  `potwierdzone` tinyint(1) NOT NULL,
  `zakonczone` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `pracownicy`
--

CREATE TABLE `pracownicy` (
  `id_pracownika` int(20) NOT NULL,
  `imie` text COLLATE utf8_unicode_ci NOT NULL,
  `nazwisko` text COLLATE utf8_unicode_ci NOT NULL,
  `data_urodzenia` date DEFAULT NULL,
  `adres_email` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `numer_telefonu` char(12) COLLATE utf8_unicode_ci DEFAULT NULL,
  `login` text COLLATE utf8_unicode_ci NOT NULL,
  `haslo` text COLLATE utf8_unicode_ci NOT NULL,
  `admin` tinyint(1) NOT NULL,
  `ostatnie_logowanie` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Zrzut danych tabeli `pracownicy`
--

INSERT INTO `pracownicy` (`id_pracownika`, `imie`, `nazwisko`, `data_urodzenia`, `adres_email`, `numer_telefonu`, `login`, `haslo`, `admin`, `ostatnie_logowanie`) VALUES
(1, 'Mateusz', 'Brodziak', '1996-06-21', 'mateusz.brodziak@gmail.com', '782840454', 'mateusz.brodziak', '$2y$10$xcHczYnNxjo7BouDBt8ZE.wP5JkhrNe1WGDEgkb4Q2tNgSsUzz7pG', 1, '2018-12-03 18:44:13'),
(2, 'Jan ', 'Kowalski', '1993-08-18', 'jan.kowalski@gmail.com', '678985345', 'jan.kowalski', '$2y$10$/DQ9J03pXpKzrLTdjyPAXOcG6SmSiHSl4sOuEkk/BcxZTIdDRKg6S', 0, '2018-11-29 19:00:02'),
(3, 'Kasia', 'Kowalska', '1996-11-12', 'kasia.kowalska@gmail.com', '746947123', 'kasia.kowalska', '$2y$10$DOqkCeOjtfhU7nIO8J/rP.XisuR8HJyiJJTFPH2/tRqksbqci2jwq', 0, '0000-00-00 00:00:00'),
(4, 'Andrzej ', 'Nowak', '1995-01-13', 'andrzej.nowak@gmail.com', '567065987', 'andrzej.nowak', '$2y$10$bLlZFwBEHDXR7RdUKZuHr.V7n5cVrSHvHNfe29sX4LL7BpqWReOJa', 0, '2018-11-16 12:38:41'),
(5, 'Magda', 'Łuczańska', '1997-04-05', 'magda.luczanska@gmail.com', '672689272', 'magda.luczanska', '$2y$10$ILSaO3hR6B8WmAhtzQ2ZA.6ez5jn6a6IqoWiln3kqCnj0dVQhcmxK', 1, '0000-00-00 00:00:00'),
(6, 'Dariusz', 'Nowakowski', '1989-04-20', 'dariusz.nowakowski@gmail.com', '832847443', 'dariusz.nowakowski', '$2y$10$i13QaCCIZleXR3qcqiSt4OHYqpM6XXFlrTxUS/0hBYcyj6juV9mJi', 1, '2018-11-27 19:03:07'),
(7, 'Anna', 'Malinowska', '1994-05-17', 'anna.malinowska@gmail.com', '000000000', 'anna.malinowska', '$2y$10$0ozEIMGU93metIe1xsnace0uOTwKRVpCYmnsOjGepK7xwFQ8AzfYK', 0, '0000-00-00 00:00:00'),
(8, 'Magdelena ', 'Mróz', '0000-00-00', 'magdalena.mroz@gmail.com', '000000000', 'magdalena.mroz', '$2y$10$pYkTuoiBW/ez3v6xfwLR7eqmAZYjqtEhSOh5CsL5F3gRAZIP/9hAq', 1, '2018-11-27 00:07:14'),
(9, 'Henryk', 'Bojanowski', '1991-02-21', 'henryk.bojanowski@gmail.com', '000000000', 'henryk.bojanowski', '$2y$10$.EyMLIoA124.yBEeOSEXMePo7FdJsLUEfMtvq0pm.STXdLgpt0NeO', 0, '0000-00-00 00:00:00'),
(10, 'Bogdan', 'Zając', '1990-12-19', 'bogdan.zajac@gmail.com', '637428344', 'bogdan.zajac', '$2y$10$pqkQOeQZYcwCugHxEKipVO/u3gTFKIfMJNbMwzjK.qI1aIvYyHKRm', 1, '2018-11-29 19:00:14'),
(11, 'Dariusz', 'Brodziak', '1965-07-02', 'dariusz.brodziak@gmail.com', '5925665986', 'dariusz.brodziak', '$2y$10$IFWKi9Sno2Pjp.T4zuyeMOgPwUR/dHqcZQAHBrmkRJh36WZ0uKTBG', 1, '2018-11-27 15:27:45'),
(12, 'Władysław', 'Sienkiewicz', '0000-00-00', 'wladyslaw.sienkiewicz@gmai.com', '000000000', 'wladyslaw.sienkiewicz', '$2y$10$InMLL/gUxl81TwRt7JSHmOfEEx0o0Kqlq2lC.CYzpqFYuJRM4hUta', 0, '0000-00-00 00:00:00');

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `dyzury`
--
ALTER TABLE `dyzury`
  ADD PRIMARY KEY (`id_dyzuru`) USING BTREE;

--
-- Indeksy dla tabeli `dyzury_pracownikow`
--
ALTER TABLE `dyzury_pracownikow`
  ADD PRIMARY KEY (`id_dyzuru`,`id_pracownika`),
  ADD KEY `id_pracownika` (`id_pracownika`);

--
-- Indeksy dla tabeli `pracownicy`
--
ALTER TABLE `pracownicy`
  ADD PRIMARY KEY (`id_pracownika`) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT dla tabeli `dyzury`
--
ALTER TABLE `dyzury`
  MODIFY `id_dyzuru` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT dla tabeli `pracownicy`
--
ALTER TABLE `pracownicy`
  MODIFY `id_pracownika` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
