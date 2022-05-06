-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Mag 06, 2022 alle 21:13
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
-- Struttura della tabella `uploads`
--

CREATE TABLE `uploads` (
  `id_file` varchar(64) NOT NULL,
  `id_user` int(11) NOT NULL,
  `size` int(11) NOT NULL,
  `datet` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
(31, 'gabrieledevs@gmail.com', '$2y$10$arfSJGV6rikJKw5mCxlKPuuDc7xc5JbmzBWcK2m6aaqU3Nml5TxbG', 'EMAIL', 0, 1);

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
