CREATE TABLE IF NOT EXISTS `secure-cloud`.`remember` (
  `htkn` VARCHAR(64) NOT NULL,
  `expires` DATETIME NOT NULL,
  `id_user` int NOT NULL
);