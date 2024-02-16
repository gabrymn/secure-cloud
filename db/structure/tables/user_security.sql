DROP TABLE IF EXISTS `user_security`;
CREATE TABLE `user_security` (
  `pwd_hash` varchar(100) NOT NULL,
  `rkey_hash` varchar(100) NOT NULL,
  `rkey_c` varchar(200) NOT NULL,
  `ckey_c` varchar(200) NOT NULL,
  `secret_2fa_c` varchar(200) NOT  NULL,
  `dkey_salt` varchar(50) NOT NULL,
  `id_user` int(11) NOT NULL,
  KEY `id_user` (`id_user`),
  CONSTRAINT `user_security_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
