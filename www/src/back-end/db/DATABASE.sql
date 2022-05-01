-- phpMyAdmin SQL Dump
-- version 4.1.7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mag 01, 2022 alle 00:49
-- Versione del server: 8.0.26
-- PHP Version: 5.6.40

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
--

-- --------------------------------------------------------

--
--


CREATE DATABASE IF NOT EXISTS `secure-cloud`;

CREATE TABLE IF NOT EXISTS `secure-cloud`.`remember` (
  `htkn` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `expires` DATETIME NOT NULL,
  `id_user` int NOT NULL,
);

CREATE TABLE IF NOT EXISTS `secure-cloud`.`account_recovery` (
  `id_user` INT NOT NULL,
  `htkn` VARCHAR(80) NOT NULL,
  `expires` DATETIME NOT NULL,
);

CREATE TABLE IF NOT EXISTS `secure-cloud`.`users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(50) NOT NULL,
  `pass` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `logged_with` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `secure-cloud`.`env` (
  `id` varchar(10) NOT NULL,
  `key` varchar(30) NOT NULL,
  `value` varchar(120) NOT NULL
);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
