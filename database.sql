-- =============================================================
--  Estacionamiento — schema multi-cliente
--  Uso:
--    mariadb -u estacionamiento_user -p estacionamiento_dev < database.sql
-- =============================================================

SET NAMES utf8mb4;
SET time_zone = '-06:00';

CREATE TABLE IF NOT EXISTS `clientes` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nombre`     VARCHAR(120) NOT NULL,
    `slug`       VARCHAR(80)  NOT NULL,
    `activo`     TINYINT(1)   NOT NULL DEFAULT 1,
    `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_clientes_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `clientes` (`id`, `nombre`, `slug`, `activo`) VALUES
    (1, 'Cliente demo', 'cliente-demo', 1)
ON DUPLICATE KEY UPDATE `nombre` = VALUES(`nombre`), `activo` = VALUES(`activo`);

CREATE TABLE IF NOT EXISTS `roles` (
    `rol_id`           TINYINT UNSIGNED NOT NULL,
    `nombre`           VARCHAR(60)      NOT NULL,
    `descripcion`      VARCHAR(180)     NULL,
    `es_admin_general` TINYINT(1)       NOT NULL DEFAULT 0,
    `activo`           TINYINT(1)       NOT NULL DEFAULT 1,
    PRIMARY KEY (`rol_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `roles` (`rol_id`, `nombre`, `descripcion`, `es_admin_general`, `activo`) VALUES
    (1, 'Administrador General', 'Acceso a todos los clientes y configuracion global', 1, 1),
    (2, 'Cajero', 'Operacion de caja, entradas, salidas y reportes del cliente', 0, 1),
    (3, 'Valet', 'Operacion de cajones y resguardos del cliente', 0, 1),
    (4, 'Cliente', 'Reservas y consulta limitada del cliente', 0, 1),
    (5, 'Administrador Cliente', 'Administra usuarios y operacion de su cliente', 0, 1)
ON DUPLICATE KEY UPDATE
    `nombre` = VALUES(`nombre`),
    `descripcion` = VALUES(`descripcion`),
    `es_admin_general` = VALUES(`es_admin_general`),
    `activo` = VALUES(`activo`);

CREATE TABLE IF NOT EXISTS `permisos` (
    `id`     SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `clave`  VARCHAR(80)       NOT NULL,
    `nombre` VARCHAR(120)      NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_permisos_clave` (`clave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `permisos` (`clave`, `nombre`) VALUES
    ('dashboard.ver', 'Ver tablero'),
    ('clientes.ver', 'Ver clientes'),
    ('clientes.crear', 'Crear clientes'),
    ('clientes.editar', 'Editar clientes'),
    ('usuarios.ver', 'Ver usuarios'),
    ('usuarios.crear', 'Crear usuarios'),
    ('usuarios.editar', 'Editar usuarios'),
    ('usuarios.eliminar', 'Eliminar usuarios'),
    ('vehiculos.ver', 'Ver vehiculos'),
    ('vehiculos.crear', 'Crear vehiculos'),
    ('vehiculos.editar', 'Editar vehiculos'),
    ('vehiculos.eliminar', 'Eliminar vehiculos'),
    ('cajones.ver', 'Ver cajones'),
    ('cajones.editar', 'Editar cajones'),
    ('resguardos.crear', 'Resguardar vehiculos'),
    ('resguardos.reservar', 'Reservar cajones'),
    ('resguardos.cobrar', 'Cobrar resguardos'),
    ('reportes.ver', 'Ver reportes'),
    ('chat.ver', 'Ver chat'),
    ('bitacora.ver', 'Ver bitacora')
ON DUPLICATE KEY UPDATE `nombre` = VALUES(`nombre`);

CREATE TABLE IF NOT EXISTS `rol_permisos` (
    `rol_id`     TINYINT UNSIGNED  NOT NULL,
    `permiso_id` SMALLINT UNSIGNED NOT NULL,
    PRIMARY KEY (`rol_id`, `permiso_id`),
    FOREIGN KEY (`rol_id`) REFERENCES `roles`(`rol_id`) ON DELETE CASCADE,
    FOREIGN KEY (`permiso_id`) REFERENCES `permisos`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `rol_permisos` (`rol_id`, `permiso_id`)
SELECT 1, id FROM permisos;

INSERT IGNORE INTO `rol_permisos` (`rol_id`, `permiso_id`)
SELECT 5, id FROM permisos
WHERE clave NOT IN ('clientes.ver', 'clientes.crear', 'clientes.editar');

INSERT IGNORE INTO `rol_permisos` (`rol_id`, `permiso_id`)
SELECT 2, id FROM permisos
WHERE clave IN (
    'dashboard.ver', 'vehiculos.ver', 'vehiculos.crear', 'vehiculos.editar',
    'cajones.ver', 'resguardos.crear', 'resguardos.reservar',
    'resguardos.cobrar', 'reportes.ver', 'chat.ver'
);

INSERT IGNORE INTO `rol_permisos` (`rol_id`, `permiso_id`)
SELECT 3, id FROM permisos
WHERE clave IN ('dashboard.ver', 'vehiculos.ver', 'cajones.ver', 'resguardos.crear', 'chat.ver');

INSERT IGNORE INTO `rol_permisos` (`rol_id`, `permiso_id`)
SELECT 4, id FROM permisos
WHERE clave IN ('dashboard.ver', 'resguardos.reservar', 'chat.ver');

CREATE TABLE IF NOT EXISTS `usuarios` (
    `id`             INT          NOT NULL AUTO_INCREMENT,
    `Usuario`        VARCHAR(100) NOT NULL,
    `password`       VARCHAR(255) NOT NULL,
    `rol_id`         TINYINT UNSIGNED NOT NULL DEFAULT 4,
    `cliente_id`     INT UNSIGNED NULL DEFAULT 1,
    `horario_inicio` TIME NULL DEFAULT NULL,
    `horario_fin`    TIME NULL DEFAULT NULL,
    `activo`         TINYINT(1) NOT NULL DEFAULT 1,
    `created_at`     DATETIME   NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_usuario` (`Usuario`),
    KEY `idx_usuarios_cliente` (`cliente_id`),
    FOREIGN KEY (`rol_id`) REFERENCES `roles`(`rol_id`),
    FOREIGN KEY (`cliente_id`) REFERENCES `clientes`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `usuarios` (`Usuario`, `password`, `rol_id`, `cliente_id`, `activo`) VALUES
    ('admin', '$2y$12$KrpkiliTR4c5XyjcbxXAQ.Tp5QgkJxon71Mps.5E6S52N/Dt0SQ32', 1, NULL, 1)
ON DUPLICATE KEY UPDATE `rol_id` = VALUES(`rol_id`), `cliente_id` = VALUES(`cliente_id`), `activo` = VALUES(`activo`);

CREATE TABLE IF NOT EXISTS `vehiculo` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `cliente_id` INT UNSIGNED NOT NULL DEFAULT 1,
    `marca`      VARCHAR(50) NOT NULL,
    `modelo`     VARCHAR(50) NOT NULL,
    `placas`     VARCHAR(20) NOT NULL,
    `color`      VARCHAR(30) NOT NULL,
    `tamano`     ENUM('Chico','Grande') NOT NULL DEFAULT 'Chico',
    `nombredue`  VARCHAR(100) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_cliente_placas` (`cliente_id`, `placas`),
    KEY `idx_vehiculo_cliente` (`cliente_id`),
    FOREIGN KEY (`cliente_id`) REFERENCES `clientes`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `cajon` (
    `cliente_id` INT UNSIGNED NOT NULL DEFAULT 1,
    `id`         TINYINT UNSIGNED NOT NULL,
    `area`       VARCHAR(10) NOT NULL DEFAULT '10m2',
    `situacion`  ENUM('disponible','ocupado','reservado') NOT NULL DEFAULT 'disponible',
    PRIMARY KEY (`cliente_id`, `id`),
    FOREIGN KEY (`cliente_id`) REFERENCES `clientes`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `cajon` (`cliente_id`, `id`, `area`, `situacion`) VALUES
    (1, 1, '10m2', 'disponible'), (1, 2, '10m2', 'disponible'),
    (1, 3, '10m2', 'disponible'), (1, 4, '10m2', 'disponible'),
    (1, 5, '10m2', 'disponible'), (1, 6, '10m2', 'disponible'),
    (1, 7, '10m2', 'disponible'), (1, 8, '10m2', 'disponible'),
    (1, 9, '10m2', 'disponible'), (1,10, '10m2', 'disponible'),
    (1,11, '10m2', 'disponible'), (1,12, '10m2', 'disponible'),
    (1,13, '10m2', 'disponible'), (1,14, '10m2', 'disponible'),
    (1,15, '10m2', 'disponible'), (1,16, '10m2', 'disponible'),
    (1,17, '15m2', 'disponible'), (1,18, '15m2', 'disponible'),
    (1,19, '15m2', 'disponible'), (1,20, '15m2', 'disponible'),
    (1,21, '15m2', 'disponible'), (1,22, '15m2', 'disponible'),
    (1,23, '15m2', 'disponible'), (1,24, '15m2', 'disponible');

CREATE TABLE IF NOT EXISTS `resguardo` (
    `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `cliente_id`    INT UNSIGNED NOT NULL DEFAULT 1,
    `placas`        VARCHAR(20) NOT NULL,
    `id_cajon`      TINYINT UNSIGNED NOT NULL,
    `hora_llegada`  TIME NOT NULL,
    `fecha`         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `hora_salida`   TIME NULL DEFAULT NULL,
    `pago`          DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `foto`          VARCHAR(255) NULL DEFAULT NULL,
    `lavado`        TINYINT(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    KEY `idx_resguardo_cliente_placas` (`cliente_id`, `placas`),
    KEY `idx_resguardo_cliente_cajon` (`cliente_id`, `id_cajon`),
    KEY `idx_resguardo_fecha` (`fecha`),
    FOREIGN KEY (`cliente_id`) REFERENCES `clientes`(`id`),
    FOREIGN KEY (`cliente_id`, `id_cajon`) REFERENCES `cajon`(`cliente_id`, `id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `bitacora` (
    `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `cliente_id`  INT UNSIGNED NULL,
    `usuario_id`  INT NULL,
    `accion`      VARCHAR(80) NOT NULL,
    `entidad`     VARCHAR(80) NULL,
    `entidad_id`  INT NULL,
    `descripcion` VARCHAR(255) NULL,
    `ip`          VARCHAR(45) NULL,
    `user_agent`  VARCHAR(255) NULL,
    `creado_en`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_bitacora_cliente_fecha` (`cliente_id`, `creado_en`),
    KEY `idx_bitacora_usuario_fecha` (`usuario_id`, `creado_en`),
    FOREIGN KEY (`cliente_id`) REFERENCES `clientes`(`id`),
    FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
