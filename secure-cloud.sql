-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Mag 06, 2022 alle 11:53
-- Versione del server: 10.1.38-MariaDB
-- Versione PHP: 7.3.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `env`
--

CREATE TABLE `env` (
  `id` varchar(10) NOT NULL,
  `key` varchar(30) NOT NULL,
  `value` varchar(120) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `remember`
--

CREATE TABLE `remember` (
  `htkn` varchar(64) NOT NULL,
  `expires` datetime NOT NULL,
  `id_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `remember`
--

INSERT INTO `remember` (`htkn`, `expires`, `id_user`) VALUES
('c22df5c28ca0de641d244b9957e889dd5d697ec2ef069dc9843585ce7d5bde67', '2022-05-24 11:29:36', 1);

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `users`
--

INSERT INTO `users` (`id`, `email`, `pass`, `logged_with`, `2FA`, `verified`) VALUES
(10, 'gabrieledevs@gmail.com', '$2y$10$JNIyzdyzl5pVu288BzTspOq0WfE8hEw5aGu9qTTe.uhYnCnVZUlz.', 'EMAIL', 0, 0);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
