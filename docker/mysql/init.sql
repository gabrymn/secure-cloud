SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

CREATE DATABASE IF NOT EXISTS `secure_cloud` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `secure_cloud`;

SOURCE ../db/structure/tables/users.sql;
SOURCE ../db/structure/tables/user_security.sql;
SOURCE ../db/structure/tables/email_verify.sql;
SOURCE ../db/structure/tables/sessions.sql;
SOURCE ../db/structure/tables/session_dates.sql;
SOURCE ../db/structure/tables/files.sql;
SOURCE ../db/structure/tables/file_transfers.sql;

SOURCE ../db/structure/triggers/before_update_sessions.sql;
SOURCE ../db/structure/triggers/before_insert_session_dates.sql;
SOURCE ../db/structure/triggers/before_update_session_dates.sql;

COMMIT;
