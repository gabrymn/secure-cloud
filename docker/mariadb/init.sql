

CREATE DATABASE IF NOT EXISTS `secure_cloud` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `secure_cloud`;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

SET GLOBAL time_zone = 'Europe/Rome';

/* DOCKER-CONTAINER FILESYSTEM */

SOURCE ../db/structure/tables/users.sql;
SOURCE ../db/structure/tables/user_security.sql;
SOURCE ../db/structure/tables/email_verify.sql;
SOURCE ../db/structure/tables/sessions.sql;
SOURCE ../db/structure/tables/session_dates.sql;
SOURCE ../db/structure/tables/files.sql;
SOURCE ../db/structure/tables/file_transfers.sql;


SOURCE ../db/structure/users/u_select.sql;
SOURCE ../db/structure/users/u_update.sql;
SOURCE ../db/structure/users/u_delete.sql;
SOURCE ../db/structure/users/u_insert.sql;
SOURCE ../db/structure/users/u_admin.sql;

SOURCE ../db/structure/triggers/before_update_sessions.sql;
SOURCE ../db/structure/triggers/before_insert_session_dates.sql;
SOURCE ../db/structure/triggers/before_update_session_dates.sql;

COMMIT;
