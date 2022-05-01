CREATE TABLE IF NOT EXISTS `secure-cloud`.`account_recovery` (
  `id_user` INT NOT NULL,
  `htkn` VARCHAR(80) NOT NULL,
  `expires` DATETIME NOT NULL
);