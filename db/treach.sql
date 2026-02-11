DROP DATABASE IF EXISTS treach;
CREATE DATABASE IF NOT EXISTS treach;

DROP TABLE IF EXISTS `cards`, `remember_tokens`;
DROP TABLE IF EXISTS`rarities`, `types`, `users`;

CREATE TABLE `types` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(50) NOT NULL,
    `url` varchar(255) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `rarities` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(50) NOT NULL,
    `url` varchar(255) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `cards` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `path` varchar(255) NOT NULL,
    `rarity_id` int(11) NULL,
    `type_id` int(11) NOT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_cards_rarity_id` (`rarity_id`),
    KEY `idx_cards_type_id` (`type_id`),
    CONSTRAINT `fk_cards_rarity`
        FOREIGN KEY (`rarity_id`) REFERENCES `rarities` (`id`)
        ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_cards_type`
        FOREIGN KEY (`type_id`) REFERENCES `types` (`id`)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `users` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `pseudo` varchar(50) NOT NULL UNIQUE,
    `email` varchar(100) NOT NULL UNIQUE,
    `password` varchar(255) NOT NULL,
    `is_admin` tinyint(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE remember_tokens (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `token_hash` CHAR(64) NOT NULL,
    `expires_at` DATETIME NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

INSERT INTO `types` (`id`, `name`, `url`) VALUES
(1, 'Seigneur', 'icon-ldr.png'),
(2, 'Gardien', 'icon-gdn.png'),
(3, 'Assassin', 'icon-ass.png'),
(4, 'Traître', 'icon-trt.png');

INSERT INTO `rarities` (`id`, `name`, `url`) VALUES
(1, 'Peu Commune', 'gris.png'),
(2, 'Rare', 'dore.png'),
(3, 'Mythique', 'orange.png'),
(4, 'Chaos', 'violet.png');

INSERT INTO `users` (`id`, `pseudo`, `email`, `password`, `is_admin`) VALUES
(1, 'Loïc', 'loictonneau@outlook.fr', '$2y$10$Ro6j28ZqLSmJbjG6McsfxukQK6F76qprB8TBLI/eOiIs0aIuknJYC', 1);
