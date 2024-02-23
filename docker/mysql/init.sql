SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

CREATE DATABASE IF NOT EXISTS `secure_cloud` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `secure_cloud`;

SOURCE ../database/structure/tables/users.sql;
SOURCE ../database/structure/tables/user_security.sql;
SOURCE ../database/structure/tables/email_verify.sql;
SOURCE ../database/structure/tables/sessions.sql;
SOURCE ../database/structure/tables/session_dates.sql;
SOURCE ../database/structure/tables/files.sql;
SOURCE ../database/structure/tables/file_transfers.sql;

SOURCE ../database/structure/triggers/before_insert_session_dates.sql;
SOURCE ../database/structure/triggers/before_update_session_dates.sql;

COMMIT;
