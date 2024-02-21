DROP TABLE IF EXISTS `email_verify`;
CREATE TABLE `email_verify` (
  `token_hash` varchar(64) NOT NULL,
  `expires` datetime NOT NULL,
  `email` varchar(64) DEFAULT NULL,
  `id_user` int(11) NOT NULL,
  KEY `id_user` (`id_user`),
  CONSTRAINT `email_verify_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


