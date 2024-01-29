DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) DEFAULT NULL,
  `surname` varchar(64) DEFAULT NULL,
  `email` varchar(64) NOT NULL UNIQUE,
  `2fa` tinyint(1) NOT NULL,
  `verified` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `user_security`;
CREATE TABLE `user_security` (
  `pwd_hash` varchar(100) NOT NULL,
  `rkey_hash` varchar(100) NOT NULL,
  `rkey_c` varchar(200) NOT NULL,
  `ckey_c` varchar(200) NOT NULL,
  `rkey_iv` varchar(50) NOT NULL,
  `ckey_iv` varchar(50) NOT NULL,
  `secret_2fa` varchar(50) NOT  NULL,
  `id_user` int(11) NOT NULL,
  KEY `id_user` (`id_user`),
  CONSTRAINT `user_security_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `email_verify`;
CREATE TABLE `email_verify` (
  `token` varchar(64) NOT NULL,
  `expires` datetime NOT NULL,
  `id_user` int(11) NOT NULL,
  `email` varchar(64) DEFAULT NULL UNIQUE,
  KEY `id_user` (`id_user`),
  CONSTRAINT `email_verify_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


