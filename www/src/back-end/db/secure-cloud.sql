
CREATE DATABASE IF NOT EXISTS `secure-cloud` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `secure-cloud`;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `account_recovery` (
  `htkn` varchar(80) NOT NULL,
  `expires` datetime NOT NULL,
  `id_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `account_verify` (
  `htkn` varchar(64) NOT NULL,
  `expires` datetime NOT NULL,
  `id_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `files` (
  `idf` varchar(16) NOT NULL,
  `fname` varchar(500) NOT NULL,
  `ref` varchar(1000) NOT NULL,
  `size` int(11) NOT NULL,
  `mime` varchar(200) NOT NULL,
  `id_user` int(11) NOT NULL,
  `view` int(1) DEFAULT 1,
   PRIMARY KEY(`idf`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `remember` (
  `htkn` varchar(64) NOT NULL,
  `expires` datetime NOT NULL,
  `id_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `plans`(
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL UNIQUE,
  `gb` int NOT NULL,
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `plans` (`name`, `gb`)
VALUES
("Standard", 10),
("Premium", 20),

CREATE TABLE `sessions` (
  `id` varchar(20) NOT NULL,
  `ip` varchar(30) NOT NULL,
  `client` varchar(30) NOT NULL,
  `os` varchar(20) NOT NULL,
  `device` varchar(10) NOT NULL,
  `last_time` datetime NOT NULL,
  `session_status` int(11) NOT NULL,
  `rem_htkn` varchar(64) DEFAULT NULL,
  `id_user` int(11) NOT NULL,
   PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(40) NOT NULL,
  `surname` varchar(40) NOT NULL,
  `email` varchar(50) NOT NULL,
  `notes` text DEFAULT NULL,
  `pass` varchar(64) NOT NULL,
  `2FA` int(1) NOT NULL,
  `verified` int(1) NOT NULL,
  `joined` date NOT NULL,
  `id_plan` int(1) DEFAULT 1,
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `transfers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tdate` datetime NOT NULL,
  `type` char(1) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_session` varchar(16) NOT NULL,
  `id_file` varchar(16) NOT NULL,
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE USER 'USER_ADMIN'@'localhost';
GRANT ALL PRIVILEGES ON *.* TO `USER_ADMIN`@`localhost` WITH GRANT OPTION;
SET PASSWORD FOR 'USER_ADMIN'@'localhost' = PASSWORD('2YGBXYQ8y93dhguc728VXHbk2_h3g782iwkjapzsoj92njl');

CREATE USER 'USER_STD_SEL'@'localhost';
GRANT USAGE ON *.* TO `USER_STD_SEL`@`localhost`;
GRANT SELECT ON `secure-cloud`.`remember` TO `USER_STD_SEL`@`localhost`;
GRANT SELECT ON `secure-cloud`.`transfers` TO `USER_STD_SEL`@`localhost`;
GRANT SELECT ON `secure-cloud`.`account_verify` TO `USER_STD_SEL`@`localhost`;
GRANT SELECT ON `secure-cloud`.`account_recovery` TO `USER_STD_SEL`@`localhost`;
GRANT SELECT ON `secure-cloud`.`sessions` TO `USER_STD_SEL`@`localhost`;
GRANT SELECT ON `secure-cloud`.`plans` TO `USER_STD_SEL`@`localhost`;
GRANT SELECT ON `secure-cloud`.`users` TO `USER_STD_SEL`@`localhost`;
GRANT SELECT ON `secure-cloud`.`files` TO `USER_STD_SEL`@`localhost`;
SET PASSWORD FOR 'USER_STD_SEL'@'localhost' = PASSWORD('stduserzqSxYvjck7e5ORpc9kg0');

CREATE USER 'USER_STD_DEL'@'localhost';
GRANT USAGE ON *.* TO `USER_STD_DEL`@`localhost`;
GRANT SELECT, UPDATE, DELETE ON `secure-cloud`.`remember` TO `USER_STD_DEL`@`localhost`;
GRANT SELECT, UPDATE, DELETE ON `secure-cloud`.`sessions` TO `USER_STD_DEL`@`localhost`;
GRANT SELECT, UPDATE, DELETE ON `secure-cloud`.`files` TO `USER_STD_DEL`@`localhost`;
GRANT SELECT, UPDATE, DELETE ON `secure-cloud`.`account_recovery` TO `USER_STD_DEL`@`localhost`;
GRANT SELECT, UPDATE, DELETE ON `secure-cloud`.`transfers` TO `USER_STD_DEL`@`localhost`;
GRANT SELECT, UPDATE, DELETE ON `secure-cloud`.`users` TO `USER_STD_DEL`@`localhost`;
GRANT SELECT, UPDATE, DELETE ON `secure-cloud`.`account_verify` TO `USER_STD_DEL`@`localhost`;
SET PASSWORD FOR 'USER_STD_DEL'@'localhost' = PASSWORD('stduserwicpjo0dowijckxwn');

CREATE USER 'USER_STD_INS'@'localhost';
GRANT USAGE ON *.* TO `USER_STD_INS`@`localhost`;
GRANT INSERT ON `secure-cloud`.`remember` TO `USER_STD_INS`@`localhost`;
GRANT INSERT ON `secure-cloud`.`transfers` TO `USER_STD_INS`@`localhost`;
GRANT INSERT ON `secure-cloud`.`account_verify` TO `USER_STD_INS`@`localhost`;
GRANT INSERT ON `secure-cloud`.`account_recovery` TO `USER_STD_INS`@`localhost`;
GRANT INSERT ON `secure-cloud`.`sessions` TO `USER_STD_INS`@`localhost`;
GRANT INSERT ON `secure-cloud`.`users` TO `USER_STD_INS`@`localhost`;
GRANT INSERT ON `secure-cloud`.`files` TO `USER_STD_INS`@`localhost`;
SET PASSWORD FOR 'USER_STD_INS'@'localhost' = PASSWORD('stdusercw8hic3hujxn8y3xbsaq');

CREATE USER 'USER_STD_UPD'@'localhost';
GRANT USAGE ON *.* TO `USER_STD_UPD`@`localhost`;
GRANT SELECT, UPDATE ON `secure-cloud`.`sessions` TO `USER_STD_UPD`@`localhost`;
GRANT SELECT, UPDATE ON `secure-cloud`.`plans` TO `USER_STD_UPD`@`localhost`;
GRANT SELECT, UPDATE ON `secure-cloud`.`files` TO `USER_STD_UPD`@`localhost`;
GRANT SELECT, UPDATE ON `secure-cloud`.`users` TO `USER_STD_UPD`@`localhost`;
SET PASSWORD FOR 'USER_STD_UPD'@'localhost' = PASSWORD('stduser823NXWhd2hxiwkl3');
