DROP TABLE IF EXISTS `session_dates`;
CREATE TABLE `session_dates` (
  `start` datetime NOT NULL,
  `end` datetime NOT NULL,
  `last_activity` datetime NOT NULL,
  `session_token` varchar(255) NOT NULL,
  KEY `session_token` (`session_token`),
  CONSTRAINT `sessions_ibfk_1` FOREIGN KEY (`session_token`) REFERENCES `sessions` (`session_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
