SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Table référencée d'abord
CREATE TABLE `type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `url` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Puis la table qui référence `type`
CREATE TABLE `cards` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `path` varchar(255) DEFAULT NULL,
  `rarity` varchar(100) NOT NULL,
  `type_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_cards_type_id` (`type_id`),
  CONSTRAINT `fk_cards_type`
    FOREIGN KEY (`type_id`) REFERENCES `type` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pseudo` varchar(50) NOT NULL UNIQUE,
  `email` varchar(100) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `is-admin` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `type` (`id`, `name`, `url`) VALUES
(1, 'Seigneur', "icon-ldr.png"),
(2, 'Gardien', "icon-gdn.png"),
(3, 'Assassin', "icon-ass.png"),
(4, 'Traître', "icon-trt.png");

INSERT INTO `users` (`id`, `pseudo`, `email`, `password`, `is-admin`) VALUES
(1, 'Loic', 'loictonneau@outlook.fr', '$2y$10$Ro6j28ZqLSmJbjG6McsfxukQK6F76qprB8TBLI/eOiIs0aIuknJYC', 1);

COMMIT;
