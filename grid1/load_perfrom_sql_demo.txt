DROP DATABASE calls_billing1;
CREATE DATABASE calls_billing1;
USE calls_billing1;
CREATE TABLE IF NOT EXISTS `billing` (
  `b_c_id` int UNSIGNED NOT NULL,
  `yearmonth` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '2017-08',
  `total` int NOT NULL,
  `discount` int NOT NULL,
  `to_pay` int NOT NULL,
  PRIMARY KEY (`b_c_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";