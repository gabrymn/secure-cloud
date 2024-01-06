CREATE USER 'xyz'@'%';
GRANT ALL PRIVILEGES ON `secure\_cloud`.* TO 'xyz'@'%' WITH GRANT OPTION;
SET PASSWORD FOR 'xyz'@'%' = PASSWORD('ciao');