DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id_session` varchar(32) NOT NULL,
  `ip` varchar(64) NOT NULL,
  `os` varchar(64) NOT NULL,
  `browser` varchar(64) NOT NULL,
  `id_user` int(11) NOT NULL,
  PRIMARY KEY (`id_session`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

