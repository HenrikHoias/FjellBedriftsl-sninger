-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 12. Jun, 2024 09:43 AM
-- Tjener-versjon: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fjell_bedriftsloosninger`
--

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `bruker`
--

CREATE TABLE `bruker` (
  `id` int(11) NOT NULL,
  `fornavn` varchar(100) DEFAULT NULL,
  `e_post` varchar(320) NOT NULL,
  `pwd` varchar(500) NOT NULL,
  `tilgang` varchar(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dataark for tabell `bruker`
--

INSERT INTO `bruker` (`id`, `fornavn`, `e_post`, `pwd`, `tilgang`) VALUES
(1, 'Admin', 'admin@fjellpost.no', 'c1c224b03cd9bc7b6a86d77f5dace40191766c485cd55dc48caf9ac873335d6f', '*'),
(2, 'Sigurd', 'sigurd@agersborg.no', '9b82429a5e7e758939bfe689e83b75cf3793a01d4917bbe5859efa1ad184bbd9', ''),
(3, 'Adrian', 'adrian@osloskolen.no', 'c153ae91ed7a0ca4cf353f6d59fd3adf765037540b91453f7fd672cad7dbbddd', ''),
(4, 'Tobias', 'tobias@wellwell.com', '55f93722f93fa31b64acfad47044bbbd50d0fd5321cc3e5b670c4a7d1f8a9997', ''),
(5, 'Henrik', 'henrik.hoias@gmail.com', '0b4aeb7299d551d4366bbe8f7bc91d6b29b603dd6daf19b5bbad5dc8fb703312', '');

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `henvendelser`
--

CREATE TABLE `henvendelser` (
  `id` int(11) NOT NULL,
  `bruker_id` int(11) NOT NULL,
  `statuser_id` int(11) DEFAULT NULL,
  `kategori_id` int(11) DEFAULT NULL,
  `tidspunkt_opprettet` timestamp NOT NULL DEFAULT current_timestamp(),
  `beskrivelse` longtext DEFAULT NULL,
  `loosning_beskrivelse` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dataark for tabell `henvendelser`
--

INSERT INTO `henvendelser` (`id`, `bruker_id`, `statuser_id`, `kategori_id`, `tidspunkt_opprettet`, `beskrivelse`, `loosning_beskrivelse`) VALUES
(3, 2, 0, 0, '2024-03-22 09:55:24', 'hei jeg har problemer med maskinen min jeg vet ikke hva som skjer men den vil ikke starte opp! :(', ''),
(4, 2, 1, 0, '2024-03-22 10:11:42', 'hei jeg har et problem med pcen min som IKKE funker ordentklig', 'prøver å finne ut av det nå '),
(5, 4, 0, 0, '2024-03-22 10:31:31', 'Feilmeldinger vises under bruk av søkefunksjonen', 'owwww'),
(10, 3, 2, 1, '2024-03-22 10:51:44', 'Trenger hjelp til å tilbakestille et glemt brukernavn', ''),
(12, 3, 3, 2, '2024-04-04 09:58:57', 'Dette er en eksempel testhenvendelse.', '(var bare en test)'),
(17, 5, 3, 3, '2024-06-05 11:15:36', 'jejdekdekd', 'tror du skrev feil vi kan ikke hjelpe deg med jejdekdekd fordi vi har ikke nok personal som kan fikse opp i det problemet akkuratt nå for øyeblikket'),
(21, 3, 0, 0, '2024-06-12 07:35:55', 'efefsefsfsf', '');

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `kategori`
--

CREATE TABLE `kategori` (
  `id` int(11) NOT NULL,
  `kategori` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dataark for tabell `kategori`
--

INSERT INTO `kategori` (`id`, `kategori`) VALUES
(0, 'Ingen'),
(1, 'Support'),
(2, 'Programvarelisens'),
(3, 'Vedlikehold'),
(4, 'Faktura');

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `statuser`
--

CREATE TABLE `statuser` (
  `id` int(11) NOT NULL,
  `status` varchar(45) NOT NULL,
  `farge` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dataark for tabell `statuser`
--

INSERT INTO `statuser` (`id`, `status`, `farge`) VALUES
(0, 'Ny', 'bg-red-500'),
(1, 'Fremgang', 'bg-yellow-400'),
(2, 'Fullført', 'bg-green-500'),
(3, 'Kansellert', 'bg-gray-500');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bruker`
--
ALTER TABLE `bruker`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `henvendelser`
--
ALTER TABLE `henvendelser`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_kundehenvendelser_bruker_idx` (`bruker_id`),
  ADD KEY `fk_henvendelser_statuser1_idx` (`statuser_id`),
  ADD KEY `fk_henvendelser_kategori1_idx` (`kategori_id`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `statuser`
--
ALTER TABLE `statuser`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bruker`
--
ALTER TABLE `bruker`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `henvendelser`
--
ALTER TABLE `henvendelser`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Begrensninger for dumpede tabeller
--

--
-- Begrensninger for tabell `henvendelser`
--
ALTER TABLE `henvendelser`
  ADD CONSTRAINT `fk_henvendelser_kategori1` FOREIGN KEY (`kategori_id`) REFERENCES `kategori` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_henvendelser_statuser1` FOREIGN KEY (`statuser_id`) REFERENCES `statuser` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_kundehenvendelser_bruker` FOREIGN KEY (`bruker_id`) REFERENCES `bruker` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
