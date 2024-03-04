DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `session_token` varchar(255) NOT NULL,
  `ip` varchar(255) NOT NULL,
  `os` varchar(255) NOT NULL,
  `browser` varchar(255) NOT NULL,
  `expired` tinyint(1) NOT NULL CHECK (`expired` IN (0, 1)),
  `id_user` int(11) NOT NULL,
  PRIMARY KEY (`session_token`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

