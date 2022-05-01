CREATE TABLE IF NOT EXISTS `secure-cloud`.`users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(50) NOT NULL,
  `pass` VARCHAR(100) NOT NULL,
  `logged_with` VARCHAR(20) NOT NULL,
  PRIMARY KEY (`id`)
);