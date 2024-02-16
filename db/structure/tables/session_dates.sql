DROP TABLE IF EXISTS `session_dates`;
CREATE TABLE `session_dates` (
  `start` datetime NOT NULL,
  `end` datetime DEFAULT NULL,
  `recent_activity` datetime NOT NULL,
  `id_session` varchar(32) NOT NULL,
  KEY `id_session` (`id_session`),
  CONSTRAINT `sessions_ibfk_1` FOREIGN KEY (`id_session`) REFERENCES `sessions` (`id_session`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
