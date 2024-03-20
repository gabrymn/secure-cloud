#!/bin/bash

if [ -f .env ]; then
    # INCLUDE ENV VARS
    source .env
else
    echo ".env FILE NOT FOUND"
    exit 1
fi


# VARS 
# ---------------------------------------------------------------------------------------------------------------

dest_file="/docker-entrypoint-initdb.d/init.sql"

db_creation_qry=$(cat <<EOF
CREATE DATABASE IF NOT EXISTS $DATABASE_NAME DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE $DATABASE_NAME;
EOF
)

user_select_creation_qry=$(cat <<EOF
CREATE USER $USER_SELECT_USERNAME@'%' IDENTIFIED BY "$USER_SELECT_PASSWORD";
GRANT SELECT ON $DATABASE_NAME.* TO $USER_SELECT_USERNAME@'%';
FLUSH PRIVILEGES;
EOF
)

user_edit_creation_qry=$(cat <<EOF
CREATE USER $USER_EDIT_USERNAME@'%' IDENTIFIED BY "$USER_EDIT_PASSWORD";
GRANT SELECT, INSERT, UPDATE, DELETE ON $DATABASE_NAME.* TO $USER_EDIT_USERNAME@'%';
FLUSH PRIVILEGES;
EOF
)

# ---------------------------------------------------------------------------------------------------------------



# SCRIPT
# ---------------------------------------------------------------------------------------------------------------

echo "START TRANSACTION;" > "$dest_file"

echo "SET NAMES utf8;" >> "$dest_file"
echo "SET time_zone = '+00:00';" >> "$dest_file"
echo "SET foreign_key_checks = 0;" >> "$dest_file"
echo "SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';" >> "$dest_file"
echo "SET NAMES utf8mb4;" >> "$dest_file"

echo "$db_creation_qry" >> "$dest_file"

echo "SOURCE ../dbstruct/tables/users.sql;" >> "$dest_file"
echo "SOURCE ../dbstruct/tables/user_secrets.sql;" >> "$dest_file"
echo "SOURCE ../dbstruct/tables/email_verify.sql;" >> "$dest_file"
echo "SOURCE ../dbstruct/tables/sessions.sql;" >> "$dest_file"
echo "SOURCE ../dbstruct/tables/session_dates.sql;" >> "$dest_file"
echo "SOURCE ../dbstruct/tables/files.sql;" >> "$dest_file"
echo "SOURCE ../dbstruct/tables/file_transfers.sql;" >> "$dest_file"

echo "$user_select_creation_qry" >> "$dest_file"
echo "$user_edit_creation_qry" >> "$dest_file"

echo "COMMIT;" >> "$dest_file"

# ---------------------------------------------------------------------------------------------------------------