-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Mag 08, 2022 alle 23:55
-- Versione del server: 10.4.22-MariaDB
-- Versione PHP: 8.0.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `secure-cloud`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `account_recovery`
--

CREATE TABLE `account_recovery` (
  `id_user` int(11) NOT NULL,
  `htkn` varchar(80) NOT NULL,
  `expires` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `account_verify`
--

CREATE TABLE `account_verify` (
  `id_user` int(11) NOT NULL,
  `htkn` varchar(64) NOT NULL,
  `expires` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `env`
--

CREATE TABLE `env` (
  `id` varchar(10) NOT NULL,
  `key` varchar(30) NOT NULL,
  `value` varchar(120) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `files`
--

CREATE TABLE `files` (
  `id` int(11) NOT NULL,
  `fname` varchar(500) NOT NULL,
  `ref` varchar(1000) NOT NULL,
  `size` int(11) NOT NULL,
  `mime` varchar(30) NOT NULL,
  `id_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `files`
--

INSERT INTO `files` (`id`, `fname`, `ref`, `size`, `mime`, `id_user`) VALUES
(91, 'U2FsdGVkX1+eySjM7ukP5jvjdh6oC4kgbPNgtv9lGdNPALyqF4Hlp1cTfV9sWGO2', '../users/c0baeb7eadb69ccbfb43eca284fe9717/U2FsdGVkX1+eySjM7ukP5jvjdh6oC4kgbPNgtv9lGdNPALyqF4Hlp1cTfV9sWGO2', 128, 'arandom', 34),
(92, 'U2FsdGVkX19hXB6CK3y0qmJeDc5PT8cE0EGSOuyamKQ=', '../users/c0baeb7eadb69ccbfb43eca284fe9717/U2FsdGVkX19hXB6CK3y0qmJeDc5PT8cE0EGSOuyamKQ=', 408, 'mimetype', 34),
(93, 'U2FsdGVkX1+XnJlwXJKjDODpjGCATHkPMRBXdvulQrI=', '../users/c0baeb7eadb69ccbfb43eca284fe9717/U2FsdGVkX1+XnJlwXJKjDODpjGCATHkPMRBXdvulQrI=', 189184, 'mimetype', 34);

-- --------------------------------------------------------

--
-- Struttura della tabella `remember`
--

CREATE TABLE `remember` (
  `htkn` varchar(64) NOT NULL,
  `expires` datetime NOT NULL,
  `id_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `remember`
--

INSERT INTO `remember` (`htkn`, `expires`, `id_user`) VALUES
('f7decb918d44a14b111afebde41d5d6256f04fedd90b47eebabb52b69eee0288', '2022-05-23 10:29:56', 6);

-- --------------------------------------------------------

--
-- Struttura della tabella `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(20) NOT NULL,
  `ip` varchar(30) NOT NULL,
  `client` varchar(30) NOT NULL,
  `os` varchar(20) NOT NULL,
  `device` varchar(10) NOT NULL,
  `last_time` datetime NOT NULL,
  `session_status` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `rem_htkn` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `sessions`
--

INSERT INTO `sessions` (`id`, `ip`, `client`, `os`, `device`, `last_time`, `session_status`, `id_user`, `rem_htkn`) VALUES
('64ngoxaotmjjv674', '127.0.0.1', 'Chrome', 'Windows', 'Desktop', '2022-05-08 22:00:39', 0, 34, NULL),
('t01zvwjxuocwjsav', '127.0.0.1', 'Chrome', 'Windows', 'Desktop', '2022-05-08 23:54:27', 1, 34, NULL);

-- --------------------------------------------------------

--
-- Struttura della tabella `uploads`
--

CREATE TABLE `uploads` (
  `bytes` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `upload_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `uploads`
--

INSERT INTO `uploads` (`bytes`, `id_user`, `upload_date`) VALUES
(189184, 34, '2022-05-08 23:54:25');

-- --------------------------------------------------------

--
-- Struttura della tabella `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `pass` varchar(100) NOT NULL,
  `logged_with` varchar(20) NOT NULL,
  `2FA` int(11) NOT NULL,
  `verified` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `users`
--

INSERT INTO `users` (`id`, `email`, `pass`, `logged_with`, `2FA`, `verified`) VALUES
(34, 'gabrieledevs@gmail.com', '$2y$10$/VyUb5nT6h1Ppl4HUfB9wujeeLm8SU61PnmtVwPkm2ulURDuq8OxC', 'EMAIL', 0, 1);

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `files`
--
ALTER TABLE `files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=94;

--
-- AUTO_INCREMENT per la tabella `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
