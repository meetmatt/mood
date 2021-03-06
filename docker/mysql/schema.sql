CREATE SCHEMA IF NOT EXISTS `mood`;

USE `mood`;

CREATE TABLE IF NOT EXISTS `team` (
  `id` VARCHAR(40) NOT NULL,
  `name` VARCHAR(40) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `feedback` (
  `id` VARCHAR(40) NOT NULL,
  `team_id` VARCHAR(40) NOT NULL,
  `date` DATE NOT NULL,
  `rating` TINYINT(1) DEFAULT NULL,
  `comment` VARCHAR(1000) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_team_id_date` (`team_id`, `date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `email` (
  `team_id` VARCHAR(40) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  KEY (`team_id`),
  UNIQUE KEY `uk_team_id_email` (`team_id`,`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
