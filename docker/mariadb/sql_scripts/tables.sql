CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(64),
    cognome VARCHAR(64),
    nickname VARCHAR(64) NOT NULL
);

CREATE TABLE IF NOT EXISTS files (
    `name` VARCHAR(64) NOT NULL,
    `dir` VARCHAR(64) NOT NULL,
    `extension` VARCHAR(64),
    `size_value` INT NOT NULL,
    `size_unit` VARCHAR(64) NOT NULL,
    `mime` VARCHAR(64) NOT NULL,
    `user_id` INT NOT NULL
);