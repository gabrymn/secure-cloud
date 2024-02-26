DROP TABLE IF EXISTS `user_security`;
CREATE TABLE `user_security` (
  `password_hash` varchar(100) NOT NULL,
  `recoverykey_hash` varchar(100) NOT NULL,
  `recoverykey_encrypted` varchar(200) NOT NULL,
  `cipherkey_encrypted` varchar(200) NOT NULL,
  `secret2fa_encrypted` varchar(200) NOT  NULL,
  `masterkey_salt` varchar(50) NOT NULL,
  `id_user` int(11) NOT NULL,
  KEY `id_user` (`id_user`),
  CONSTRAINT `user_security_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
