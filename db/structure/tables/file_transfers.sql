DROP TABLE IF EXISTS `file_transfers`;
CREATE TABLE `file_transfers` (
    `transfer_date` datetime NOT NULL,
    `transfer_type` enum('upload','download') NOT NULL,
    `id_file` uuid NOT NULL,
    KEY `id_file` (`id_file`),
    CONSTRAINT `file_transfers_ibfk_1` FOREIGN KEY (`id_file`) REFERENCES `files` (`id_file`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;