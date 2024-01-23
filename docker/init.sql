

CREATE DATABASE IF NOT EXISTS `secure_cloud` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `secure_cloud`;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

SET GLOBAL time_zone = 'Europe/Rome';

/* DOCKER-CONTAINER FILESYSTEM */
SOURCE ../sql_scripts/tables.sql;
SOURCE ../sql_scripts/users.sql;

COMMIT;
