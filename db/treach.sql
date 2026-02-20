DROP DATABASE IF EXISTS treachery;
CREATE DATABASE IF NOT EXISTS treachery;
USE treachery;

DROP TABLE IF EXISTS `cards`, `remember_tokens`;
DROP TABLE IF EXISTS`rarities`, `roles`, `users`;

CREATE TABLE `roles` (
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

CREATE TABLE `users` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `pseudo` varchar(50) NOT NULL UNIQUE,
    `email` varchar(100) NOT NULL UNIQUE,
    `password` varchar(255) NOT NULL,
    `is_admin` tinyint(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `cards` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `path` varchar(255) NOT NULL,
    `rarity_id` int(11) NULL,
    `role_id` int(11) NOT NULL,
    `added_by` int(11) NOT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_cards_rarity_id` (`rarity_id`),
    KEY `idx_cards_type_id` (`role_id`),
    KEY `idx_added_by` (`added_by`),
    CONSTRAINT `fk_cards_rarity`
        FOREIGN KEY (`rarity_id`) REFERENCES `rarities` (`id`)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_cards_role`
        FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_user`
        FOREIGN KEY (`added_by`) REFERENCES `users` (`id`)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE remember_tokens (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `added_by` INT NOT NULL,
    `token_hash` CHAR(64) NOT NULL,
    `expires_at` DATETIME NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (added_by) REFERENCES users(id) ON DELETE CASCADE
);

INSERT INTO `roles` (`id`, `name`, `url`) VALUES
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

INSERT INTO `cards` (`id`, `path`, `rarity_id`, `role_id`, `user_id`) VALUES
(62, 'c48ae0d53da8c89132ab3f01146b28a7.png', 1, 1, 1),
(61, '46691d6621c5deefa445e4e8620f96bb.png', 3, 1, 1),
(60, '04bed6abf2822a302242315bfcba9953.png', 1, 1, 1),
(59, 'c921cff6dd3f2c1a025b2526bc69f400.png', 2, 1, 1),
(58, '7fc2d489fbec660fc42a1dbcab41da6a.png', 1, 1, 1),
(57, 'c30ab1e0007c3c9e857ce53234e1afe0.png', 1, 1, 1),
(56, '7ce4e771d3d6302c4e8d63e70522612a.png', 2, 1, 1),
(55, 'a9029767b39eecc57b553f4dea002a54.png', 2, 1, 1),
(54, 'c01074588411ff274e878ea1c8d53648.png', 3, 1, 1),
(53, 'fa285c0c022898858c8b01c76eb63f42.png', 3, 1, 1),
(52, 'a3e894864f89b11a547852cc325614ee.png', 3, 1, 1),
(51, 'a0eb06a7fc5b93422588165ab62c35e3.png', 3, 1, 1),
(50, 'a83010ed07367cba26667e3473268c11.png', 1, 1, 1),
(49, '0e867d74bc2b77384ac5a10d2317ec77.png', 3, 3, 1),
(48, '7bca02a22801534cd4066e3de6f9ebb2.png', 2, 3, 1),
(47, '0fd57f08d078f90481cda6d49f946f93.png', 2, 3, 1),
(46, '358f691490cdaa2c00b52df94f7467be.png', 2, 3, 1),
(45, 'a241bb11f6cc963cfd587cc8158cadb6.png', 1, 3, 1),
(44, 'e1b32fcfb6f84140c021ab026ad52ec2.png', 2, 3, 1),
(43, '4e05d70804f437920b2b44684ea077a9.png', 2, 3, 1),
(42, '6033754bca05d8a83b004ddde7339e20.png', 2, 3, 1),
(41, 'd1d63f716f194c2af0be2a8c4d496b3e.png', 1, 3, 1),
(40, '1a4c06f646cdf0a2c3569f80ad91e3a7.png', 1, 3, 1),
(39, '3fbed281f1fba759db94e3a34149f3e7.png', 2, 3, 1),
(38, 'd405d7622cb03bd43e3893ceb4a30265.png', 1, 3, 1),
(37, '7449884855b1cad8ec19a7bda9016dbd.png', 1, 3, 1),
(36, 'eee6da798df546719fca4e844763a1e4.png', 1, 3, 1),
(35, '7867226ee2ee1519fb3b356f3528786c.png', 3, 3, 1),
(34, 'f1b17224f1ae620947c55af8492a0755.png', 1, 3, 1),
(33, 'e0d094f81e1108e3acd1a40a2cc9b9dd.png', 2, 3, 1),
(32, 'e7c400386a909117719ce10a37693def.png', 3, 3, 1),
(31, '256e94bc562ee5620c83a3dde464507f.png', 1, 4, 1),
(30, '0250b7628c2407561027ef4bd6512a07.png', 4, 4, 1),
(29, '1b530cff5d6e6922d0b217c348440ccd.png', 2, 4, 1),
(28, 'a785d9fd351d24e15aaefc8ee1e468c8.png', 3, 4, 1),
(27, '7e0bc545488fba71e71541206f9e62ed.png', 2, 4, 1),
(26, 'a902e184d613f0390b1133e54fb57194.png', 4, 4, 1),
(25, 'b38a32074cb6fde9d7c3d902169b0839.png', 4, 4, 1),
(24, 'e9cef9c2ce5c26c5005910bea1ca4456.png', 1, 4, 1),
(23, '5e8d93c1377caa38f7b5bd8238a4ed0b.png', 1, 4, 1),
(22, '3f1c6ffb7612323fec3b5bbda583ada9.png', 1, 4, 1),
(21, '907c0f068bffdea54bafeb36191f4ca2.png', 2, 4, 1),
(20, '43429b6d52fb6d4c57366786d7223a1d.png', 4, 4, 1),
(19, '2ba929354776ffa2a11a84abba177012.png', 3, 4, 1),
(18, 'c1473922cdd2f745c866cf84a6818495.png', 3, 2, 1),
(17, '7d22a6ef00a71121ae77170b249519c0.png', 2, 2, 1),
(16, '9849f34a8e6a81fe5e798b632f3ac53a.png', 1, 2, 1),
(15, '1a2c8ecf7843d210012d9e1f083f92b8.png', 1, 2, 1),
(14, '7185bfe190c1f7b8e465efee5d257c5d.png', 3, 2, 1),
(13, 'ea10caaca66c89a51e7e1415108f55bf.png', 2, 2, 1),
(12, 'dd36c217deb790c8f29fcf37c5e4fe8b.png', 2, 2, 1),
(11, 'd773c3ea40ace6982d675654d379dac3.png', 1, 2, 1),
(10, '7a3a3ce71bf90b7c0c4a366479e3c45d.png', 1, 2, 1),
(9, 'eb300b4dbf5bbea78a3deda1ce2bc672.png', 3, 2, 1),
(8, 'abedd2658421d5990f30163bd47c630c.png', 1, 2, 1),
(7, '0337a21b8379416a39fe5f9a918bce35.png', 2, 2, 1),
(6, '5746754de0124c0b137f4b4d3573fd5e.png', 2, 2, 1),
(5, '94735d862cbb6572071f8a0490c44a54.png', 1, 2, 1),
(4, 'f69f12d8733a1e3a65cd7ff3cd65b27d.png', 1, 2, 1),
(3, 'c239a55a95b764142e708cd8c7a33d73.png', 3, 2, 1),
(2, '4e39ae829d7b7456f09fada24b0a77e0.png', 2, 2, 1),
(1, '076e1da16e52bb0c4420da407b694986.png', 2, 2, 1);
