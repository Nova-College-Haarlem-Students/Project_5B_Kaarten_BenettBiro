-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: MariaDB
-- Gegenereerd op: 27 okt 2026 om 11:00
-- Serverversie: 10.11.18-MariaDB-ubu2204
-- PHP-versie: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `testdb`
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `Camping`
--

CREATE TABLE `Camping` (
  `Camping_id` int(11) NOT NULL,
  `soort_id` int(11) NOT NULL,
  `nummer` int(11) NOT NULL,
  `Grootte` double NOT NULL,
  `Is_stroom` tinyint(1) NOT NULL,
  `Aan_water` tinyint(1) NOT NULL,
  `Foto_Url` varchar(255) NOT NULL,
  `Max_personen` int(11) NOT NULL,
  `prijs` decimal(10,2) NOT NULL,
  `actief` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `Camping`
--

INSERT INTO `Camping` (`Camping_id`, `soort_id`, `nummer`, `Grootte`, `Is_stroom`, `Aan_water`, `Foto_Url`, `Max_personen`, `prijs`, `actief`) VALUES
(1, 1, 1, 60, 0, 1, 'img/plekken/tent-01.jpg', 4, 18.00, 1),
(2, 1, 2, 55, 0, 0, 'img/plekken/tent-02.jpg', 4, 16.00, 1),
(3, 1, 3, 70, 1, 0, 'img/plekken/tent-03.jpg', 5, 22.00, 1),
(4, 1, 4, 75, 1, 1, 'img/plekken/tent-04.jpg', 6, 25.00, 1),
(5, 1, 5, 50, 0, 1, 'img/plekken/tent-05.jpg', 3, 17.00, 1),
(6, 1, 6, 65, 0, 0, 'img/plekken/tent-06.jpg', 4, 16.00, 0),
(7, 2, 7, 90, 1, 0, 'img/plekken/caravan-07.jpg', 4, 28.00, 1),
(8, 2, 8, 100, 1, 1, 'img/plekken/caravan-08.jpg', 5, 32.00, 1),
(9, 2, 9, 110, 1, 0, 'img/plekken/caravan-09.jpg', 6, 30.00, 1),
(10, 2, 10, 95, 1, 1, 'img/plekken/caravan-10.jpg', 5, 34.00, 1),
(11, 2, 11, 85, 0, 0, 'img/plekken/caravan-11.jpg', 4, 22.00, 1),
(12, 2, 12, 120, 1, 1, 'img/plekken/caravan-12.jpg', 6, 36.00, 0),
(13, 3, 13, 80, 1, 1, 'img/plekken/chalet-13.jpg', 6, 95.00, 1),
(14, 3, 14, 75, 1, 0, 'img/plekken/chalet-14.jpg', 4, 80.00, 1),
(15, 3, 15, 90, 1, 1, 'img/plekken/chalet-15.jpg', 8, 125.00, 1);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `Reservering`
--

CREATE TABLE `Reservering` (
  `Reservering_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `Camping_id` int(11) NOT NULL,
  `aankomstdatum` date DEFAULT NULL,
  `vertrekdatum` date DEFAULT NULL,
  `aantal_personen` int(11) DEFAULT NULL,
  `opmerking` varchar(255) DEFAULT NULL,
  `kenteken` varchar(255) DEFAULT NULL,
  `totaalprijs` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `Reservering`
--

INSERT INTO `Reservering` (`Reservering_id`, `user_id`, `Camping_id`, `aankomstdatum`, `vertrekdatum`, `aantal_personen`, `opmerking`, `kenteken`, `totaalprijs`) VALUES
(1, 4, 1, '2026-06-05', '2026-06-08', 2, 'We komen pas laat aan', 'AB-123-C', 54.00),
(2, 5, 2, '2026-06-12', '2026-06-19', 4, NULL, NULL, 112.00),
(3, 6, 3, '2026-06-20', '2026-06-23', 3, NULL, 'XY-45-ZZ', 66.00),
(4, 7, 4, '2026-07-01', '2026-07-08', 5, 'Graag een plek in de schaduw', '12-ABC-3', 175.00),
(5, 8, 5, '2026-07-10', '2026-07-14', 2, NULL, NULL, 68.00),
(6, 4, 1, '2026-07-15', '2026-07-22', 4, NULL, 'AB-123-C', 126.00),
(7, 9, 3, '2026-07-25', '2026-07-30', 2, NULL, NULL, 110.00),
(8, 10, 4, '2026-08-02', '2026-08-09', 6, 'We hebben een hond bij ons', '9-KLM-45', 175.00),
(9, 11, 2, '2026-08-05', '2026-08-10', 3, NULL, NULL, 80.00),
(10, 5, 5, '2026-08-12', '2026-08-16', 3, NULL, 'GH-78-JK', 68.00),
(11, 6, 1, '2026-08-17', '2026-08-21', 2, NULL, NULL, 72.00),
(12, 8, 3, '2026-08-22', '2026-08-26', 4, 'Vroege check-in als dat kan', NULL, 88.00),
(13, 7, 7, '2026-06-15', '2026-06-22', 4, NULL, '45-RST-6', 196.00),
(14, 9, 8, '2026-07-04', '2026-07-11', 4, 'We komen pas laat aan', 'TT-222-B', 224.00),
(15, 10, 9, '2026-07-18', '2026-07-25', 5, NULL, '77-PQR-8', 210.00),
(16, 11, 10, '2026-08-01', '2026-08-08', 5, NULL, '3-XYZ-99', 238.00),
(17, 4, 8, '2026-08-10', '2026-08-17', 3, NULL, 'AB-123-C', 224.00),
(18, 5, 13, '2026-07-06', '2026-07-13', 6, 'Verjaardag, graag een mooie ontvangst', NULL, 665.00),
(19, 6, 15, '2026-07-20', '2026-07-27', 8, NULL, 'DD-55-EE', 875.00),
(20, 8, 13, '2026-08-08', '2026-08-12', 4, NULL, NULL, 380.00),
(21, 4, 1, '2025-07-10', '2025-07-15', 2, NULL, 'AB-123-C', 80.00),
(22, 7, 4, '2025-08-04', '2025-08-08', 4, NULL, NULL, 92.00);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `roles`
--

INSERT INTO `roles` (`id`, `name`) VALUES
(3, 'admin'),
(1, 'gast'),
(2, 'medewerker');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `Soort_plek`
--

CREATE TABLE `Soort_plek` (
  `soort_id` int(11) NOT NULL,
  `naam` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `Soort_plek`
--

INSERT INTO `Soort_plek` (`soort_id`, `naam`) VALUES
(1, 'Tentplaats'),
(2, 'Caravanplaats'),
(3, 'Chalet');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `naam` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `wachtwoord` varchar(255) NOT NULL,
  `telefoonnummer` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `users`
--

INSERT INTO `users` (`id`, `role_id`, `naam`, `email`, `wachtwoord`, `telefoonnummer`) VALUES
(1, 3, 'Jan de Vries', 'jan@camping.nl', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0612345678'),
(2, 2, 'Sanne Bakker', 'sanne@camping.nl', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0623456789'),
(3, 2, 'Mohamed El Amrani', 'mohamed@camping.nl', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL),
(4, 1, 'Pieter Jansen', 'pieter@voorbeeld.nl', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0634567890'),
(5, 1, 'Lisa Visser', 'lisa@voorbeeld.nl', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0645678901'),
(6, 1, 'Tom Smit', 'tom@voorbeeld.nl', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL),
(7, 1, 'Emma Meijer', 'emma@voorbeeld.nl', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0656789012'),
(8, 1, 'Daan Mulder', 'daan@voorbeeld.nl', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0667890123'),
(9, 1, 'Sophie de Boer', 'sophie@voorbeeld.nl', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL),
(10, 1, 'Ruben Dekker', 'ruben@voorbeeld.nl', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0678901234'),
(11, 1, 'Fleur Hendriks', 'fleur@voorbeeld.nl', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0689012345'),
(12, 3, 'Benett Biro', 'Benettb24@gmail.com', '$2y$12$HD7dNy8M5zKdCpL9j2Qs3.tkOL8Qr6QwCoTQiIay0yhe7X3xgHmUa', '072570795');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `Wijziging`
--

CREATE TABLE `Wijziging` (
  `Wijziging_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `Camping_id` int(11) NOT NULL,
  `tijdstip` datetime DEFAULT NULL,
  `soort_wijziging` varchar(255) DEFAULT NULL,
  `oude_waarde` varchar(255) DEFAULT NULL,
  `nieuwe_waarde` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `Wijziging`
--

INSERT INTO `Wijziging` (`Wijziging_id`, `user_id`, `Camping_id`, `tijdstip`, `soort_wijziging`, `oude_waarde`, `nieuwe_waarde`) VALUES
(1, 2, 6, '2026-03-10 09:15:00', 'uitgezet', 'actief = 1', 'actief = 0'),
(2, 2, 12, '2026-03-10 09:20:00', 'uitgezet', 'actief = 1', 'actief = 0'),
(3, 3, 4, '2026-04-01 10:00:00', 'prijs aangepast', '23.00', '25.00'),
(4, 3, 13, '2026-04-01 10:05:00', 'prijs aangepast', '90.00', '95.00'),
(5, 1, 15, '2026-04-02 14:30:00', 'prijs aangepast', '120.00', '125.00'),
(6, 2, 14, '2026-09-20 08:45:00', 'uitgezet', 'actief = 1', 'actief = 0'),
(7, 2, 14, '2026-09-22 16:10:00', 'aangezet', 'actief = 0', 'actief = 1');

--
-- Indexen voor geëxporteerde tabellen
--

--
-- Indexen voor tabel `Camping`
--
ALTER TABLE `Camping`
  ADD PRIMARY KEY (`Camping_id`),
  ADD KEY `soort_id` (`soort_id`);

--
-- Indexen voor tabel `Reservering`
--
ALTER TABLE `Reservering`
  ADD PRIMARY KEY (`Reservering_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `Camping_id` (`Camping_id`);

--
-- Indexen voor tabel `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexen voor tabel `Soort_plek`
--
ALTER TABLE `Soort_plek`
  ADD PRIMARY KEY (`soort_id`);

--
-- Indexen voor tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`);

--
-- Indexen voor tabel `Wijziging`
--
ALTER TABLE `Wijziging`
  ADD PRIMARY KEY (`Wijziging_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `Camping_id` (`Camping_id`);

--
-- AUTO_INCREMENT voor geëxporteerde tabellen
--

--
-- AUTO_INCREMENT voor een tabel `Camping`
--
ALTER TABLE `Camping`
  MODIFY `Camping_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT voor een tabel `Reservering`
--
ALTER TABLE `Reservering`
  MODIFY `Reservering_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT voor een tabel `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT voor een tabel `Soort_plek`
--
ALTER TABLE `Soort_plek`
  MODIFY `soort_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT voor een tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT voor een tabel `Wijziging`
--
ALTER TABLE `Wijziging`
  MODIFY `Wijziging_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Beperkingen voor geëxporteerde tabellen
--

--
-- Beperkingen voor tabel `Camping`
--
ALTER TABLE `Camping`
  ADD CONSTRAINT `Camping_ibfk_1` FOREIGN KEY (`soort_id`) REFERENCES `Soort_plek` (`soort_id`);

--
-- Beperkingen voor tabel `Reservering`
--
ALTER TABLE `Reservering`
  ADD CONSTRAINT `Reservering_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `Reservering_ibfk_2` FOREIGN KEY (`Camping_id`) REFERENCES `Camping` (`Camping_id`);

--
-- Beperkingen voor tabel `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);

--
-- Beperkingen voor tabel `Wijziging`
--
ALTER TABLE `Wijziging`
  ADD CONSTRAINT `Wijziging_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `Wijziging_ibfk_2` FOREIGN KEY (`Camping_id`) REFERENCES `Camping` (`Camping_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
