DROP TABLE IF EXISTS `account_verify`;
CREATE TABLE `account_verify` (
  `token_h` varchar(64) NOT NULL,
  `expires` date NOT NULL,
  `id_user` int(11) NOT NULL,
  KEY `id_user` (`id_user`),
  CONSTRAINT `account_verify_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) DEFAULT NULL,
  `surname` varchar(64) DEFAULT NULL,
  `email` varchar(64) NOT NULL,
  `pwd` varchar(64) NOT NULL,
  `2fa` tinyint(1) NOT NULL,
  `verified` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;