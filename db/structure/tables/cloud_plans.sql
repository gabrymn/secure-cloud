DROP TABLE IF EXISTS `cloud_plans`;
CREATE TABLE `cloud_plans` (
  `id_plan` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `capacity_gb` int NOT NULL,
  `price_usd` int NOT NULL,
  PRIMARY KEY (`id_plan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;