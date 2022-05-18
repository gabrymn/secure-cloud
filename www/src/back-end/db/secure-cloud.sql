
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE DATABASE IF NOT EXISTS `secure-cloud` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `secure-cloud`;

CREATE TABLE `account_recovery` (
  `id_user` int(11) NOT NULL,
  `htkn` varchar(80) NOT NULL,
  `expires` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `account_verify` (
  `id_user` int(11) NOT NULL,
  `htkn` varchar(64) NOT NULL,
  `expires` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `env` (
  `id` varchar(10) NOT NULL,
  `key` varchar(30) NOT NULL,
  `value` varchar(120) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `files` (
  `idf` varchar(16) NOT NULL,
  `fname` varchar(500) NOT NULL,
  `ref` varchar(1000) NOT NULL,
  `size` int(11) NOT NULL,
  `mime` varchar(30) NOT NULL,
  `id_user` int(11) NOT NULL,
   PRIMARY KEY(`idf`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `remember` (
  `htkn` varchar(64) NOT NULL,
  `expires` datetime NOT NULL,
  `id_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `sessions` (
  `id` varchar(20) NOT NULL,
  `ip` varchar(30) NOT NULL,
  `client` varchar(30) NOT NULL,
  `os` varchar(20) NOT NULL,
  `device` varchar(10) NOT NULL,
  `last_time` datetime NOT NULL,
  `session_status` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `rem_htkn` varchar(64) DEFAULT NULL,
   PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(50) NOT NULL,
  `pass` varchar(100) NOT NULL,
  `logged_with` varchar(20) NOT NULL,
  `2FA` int(11) NOT NULL,
  `verified` int(1) NOT NULL,
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `users` (`id`, `email`, `pass`, `logged_with`, `2FA`, `verified`) VALUES
(34, 'gabrieledevs@gmail.com', '$2y$10$/VyUb5nT6h1Ppl4HUfB9wujeeLm8SU61PnmtVwPkm2ulURDuq8OxC', 'EMAIL', 0, 1);

CREATE TABLE `transfers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tdate` datetime NOT NULL,
  `type` char(1) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_session` varchar(16) NOT NULL,
  `id_file` varchar(16) NOT NULL,
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


/* BEGIN USER-ADMIN */

CREATE USER 'USER_ADMIN'@'localhost';
GRANT ALL PRIVILEGES ON *.* TO `USER_ADMIN`@`localhost` WITH GRANT OPTION;
SET PASSWORD FOR 'USER_ADMIN'@'localhost' = PASSWORD('2YGBXYQ8y93dhguc728VXHbk2_h3g782iwkjapzsoj92njl');

/* END USER-ADMIN */

/* BEGIN USER-STANDARD */

CREATE USER 'USER_STD'@'localhost';
GRANT USAGE ON *.* TO `USER_STD`@`localhost`;
GRANT SELECT, INSERT ON `secure-cloud`.`transfers` TO `USER_STD`@`localhost`;
GRANT SELECT, INSERT, UPDATE ON `secure-cloud`.`sessions` TO `USER_STD`@`localhost`;
GRANT SELECT, INSERT ON `secure-cloud`.`remember` TO `USER_STD`@`localhost`;
GRANT SELECT, INSERT, UPDATE, DELETE ON `secure-cloud`.`users` TO `USER_STD`@`localhost`;
GRANT SELECT, INSERT ON `secure-cloud`.`files` TO `USER_STD`@`localhost`;
GRANT SELECT, INSERT ON `secure-cloud`.`account_recovery` TO `USER_STD`@`localhost`;
GRANT SELECT, INSERT ON `secure-cloud`.`account_verify` TO `USER_STD`@`localhost`;
SET PASSWORD FOR 'USER_STD'@'localhost' = PASSWORD('zqSxYvjck7e5ORpc9kg0');

/* END USER-STANDARD */