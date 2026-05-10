-- =============================================================
--  Estacionamiento — Schema completo
--  Archivo: database.sql
--  Charset: utf8mb4
--
--  Tablas:
--    1. roles      — tipos de usuario (lookup)
--    2. usuarios   — cuentas del sistema
--    3. vehiculo   — vehículos registrados
--    4. cajon      — 24 cajones del estacionamiento
--    5. resguardo  — registros de entrada/salida y pagos
--
--  Uso:
--    mariadb -u estacionamiento_user -p estacionamiento_dev < database.sql
-- =============================================================

SET NAMES utf8mb4;
SET time_zone = '-06:00';

-- -------------------------------------------------------------
-- 1. ROLES
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `roles` (
    `rol_id`  TINYINT     UNSIGNED NOT NULL,
    `nombre`  VARCHAR(30) NOT NULL,
    PRIMARY KEY (`rol_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `roles` (`rol_id`, `nombre`) VALUES
    (1, 'Admin'),
    (2, 'Cajero'),
    (3, 'Valet'),
    (4, 'Cliente');

-- -------------------------------------------------------------
-- 2. USUARIOS
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `usuarios` (
    `id`       INT          NOT NULL AUTO_INCREMENT,
    `Usuario`  VARCHAR(100) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `rol_id`   TINYINT UNSIGNED NOT NULL DEFAULT 4,
    PRIMARY KEY (`id`),
    UNIQUE  KEY `uq_usuario` (`Usuario`),
    FOREIGN KEY (`rol_id`) REFERENCES `roles`(`rol_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Usuario admin por defecto — cambia la contraseña al levantar el sistema
INSERT INTO `usuarios` (`Usuario`, `password`, `rol_id`) VALUES
    ('admin', '$2y$12$KrpkiliTR4c5XyjcbxXAQ.Tp5QgkJxon71Mps.5E6S52N/Dt0SQ32', 1);

-- -------------------------------------------------------------
-- 3. VEHICULO
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `vehiculo` (
    `id`        INT         NOT NULL AUTO_INCREMENT,
    `marca`     VARCHAR(50) NOT NULL,
    `modelo`    VARCHAR(50) NOT NULL,
    `placas`    VARCHAR(20) NOT NULL,
    `color`     VARCHAR(30) NOT NULL,
    `tamano`    ENUM('Chico','Grande') NOT NULL DEFAULT 'Chico',
    `nombredue` VARCHAR(100) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_placas` (`placas`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 4. CAJON
--    24 cajones fijos (no AUTO_INCREMENT — IDs predefinidos)
--    Secciones: 1–8  área chica (10m2)
--               9–16 área chica (10m2)
--               17–24 área grande (15m2)
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cajon` (
    `id`        TINYINT     UNSIGNED NOT NULL,
    `area`      VARCHAR(10) NOT NULL DEFAULT '10m2',
    `situacion` ENUM('disponible','ocupado','reservado') NOT NULL DEFAULT 'disponible',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `cajon` (`id`, `area`, `situacion`) VALUES
    ( 1, '10m2', 'disponible'),
    ( 2, '10m2', 'disponible'),
    ( 3, '10m2', 'disponible'),
    ( 4, '10m2', 'disponible'),
    ( 5, '10m2', 'disponible'),
    ( 6, '10m2', 'disponible'),
    ( 7, '10m2', 'disponible'),
    ( 8, '10m2', 'disponible'),
    ( 9, '10m2', 'disponible'),
    (10, '10m2', 'disponible'),
    (11, '10m2', 'disponible'),
    (12, '10m2', 'disponible'),
    (13, '10m2', 'disponible'),
    (14, '10m2', 'disponible'),
    (15, '10m2', 'disponible'),
    (16, '10m2', 'disponible'),
    (17, '15m2', 'disponible'),
    (18, '15m2', 'disponible'),
    (19, '15m2', 'disponible'),
    (20, '15m2', 'disponible'),
    (21, '15m2', 'disponible'),
    (22, '15m2', 'disponible'),
    (23, '15m2', 'disponible'),
    (24, '15m2', 'disponible');

-- -------------------------------------------------------------
-- 5. RESGUARDO
--    Orden de columnas IMPORTANTE — el código PHP accede por
--    índice numérico ($mostrar2[7]=foto, $mostrar2[8]=lavado)
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `resguardo` (
    `id`           INT            NOT NULL AUTO_INCREMENT,    -- [0]
    `placas`       VARCHAR(20)    NOT NULL,                   -- [1]
    `id_cajon`     TINYINT UNSIGNED NOT NULL,                 -- [2]
    `hora_llegada` TIME           NOT NULL,                   -- [3]
    `fecha`        DATETIME       NOT NULL DEFAULT NOW(),     -- [4]
    `hora_salida`  TIME           NULL DEFAULT NULL,          -- [5]
    `pago`         DECIMAL(10,2)  NOT NULL DEFAULT 0.00,      -- [6]
    `foto`         VARCHAR(255)   NULL DEFAULT NULL,          -- [7]
    `lavado`       TINYINT(1)     NOT NULL DEFAULT 0,         -- [8]
    PRIMARY KEY (`id`),
    INDEX `idx_placas`   (`placas`),
    INDEX `idx_id_cajon` (`id_cajon`),
    INDEX `idx_fecha`    (`fecha`),
    FOREIGN KEY (`id_cajon`) REFERENCES `cajon`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
