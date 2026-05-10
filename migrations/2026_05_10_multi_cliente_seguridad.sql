-- Ejecutar sobre una base existente antes de usar la version multi-cliente.
-- Haz respaldo primero.

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS clientes (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(120) NOT NULL,
    slug VARCHAR(80) NOT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_clientes_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO clientes (id, nombre, slug, activo)
VALUES (1, 'Cliente demo', 'cliente-demo', 1)
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre), activo = VALUES(activo);

ALTER TABLE roles
    ADD COLUMN IF NOT EXISTS descripcion VARCHAR(180) NULL,
    ADD COLUMN IF NOT EXISTS es_admin_general TINYINT(1) NOT NULL DEFAULT 0,
    ADD COLUMN IF NOT EXISTS activo TINYINT(1) NOT NULL DEFAULT 1;

INSERT INTO roles (rol_id, nombre, descripcion, es_admin_general, activo) VALUES
    (1, 'Administrador General', 'Acceso a todos los clientes y configuracion global', 1, 1),
    (2, 'Cajero', 'Operacion de caja, entradas, salidas y reportes del cliente', 0, 1),
    (3, 'Valet', 'Operacion de cajones y resguardos del cliente', 0, 1),
    (4, 'Cliente', 'Reservas y consulta limitada del cliente', 0, 1),
    (5, 'Administrador Cliente', 'Administra usuarios y operacion de su cliente', 0, 1)
ON DUPLICATE KEY UPDATE
    nombre = VALUES(nombre),
    descripcion = VALUES(descripcion),
    es_admin_general = VALUES(es_admin_general),
    activo = VALUES(activo);

CREATE TABLE IF NOT EXISTS permisos (
    id SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
    clave VARCHAR(80) NOT NULL,
    nombre VARCHAR(120) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_permisos_clave (clave)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO permisos (clave, nombre) VALUES
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
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre);

CREATE TABLE IF NOT EXISTS rol_permisos (
    rol_id TINYINT UNSIGNED NOT NULL,
    permiso_id SMALLINT UNSIGNED NOT NULL,
    PRIMARY KEY (rol_id, permiso_id),
    FOREIGN KEY (rol_id) REFERENCES roles(rol_id) ON DELETE CASCADE,
    FOREIGN KEY (permiso_id) REFERENCES permisos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO rol_permisos (rol_id, permiso_id) SELECT 1, id FROM permisos;
INSERT IGNORE INTO rol_permisos (rol_id, permiso_id)
SELECT 5, id FROM permisos WHERE clave NOT IN ('clientes.ver', 'clientes.crear', 'clientes.editar');
INSERT IGNORE INTO rol_permisos (rol_id, permiso_id)
SELECT 2, id FROM permisos WHERE clave IN ('dashboard.ver', 'vehiculos.ver', 'vehiculos.crear', 'vehiculos.editar', 'cajones.ver', 'resguardos.crear', 'resguardos.reservar', 'resguardos.cobrar', 'reportes.ver', 'chat.ver');
INSERT IGNORE INTO rol_permisos (rol_id, permiso_id)
SELECT 3, id FROM permisos WHERE clave IN ('dashboard.ver', 'vehiculos.ver', 'cajones.ver', 'resguardos.crear', 'chat.ver');
INSERT IGNORE INTO rol_permisos (rol_id, permiso_id)
SELECT 4, id FROM permisos WHERE clave IN ('dashboard.ver', 'resguardos.reservar', 'chat.ver');

ALTER TABLE usuarios
    ADD COLUMN IF NOT EXISTS cliente_id INT UNSIGNED NULL DEFAULT 1,
    ADD COLUMN IF NOT EXISTS horario_inicio TIME NULL DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS horario_fin TIME NULL DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS activo TINYINT(1) NOT NULL DEFAULT 1,
    ADD COLUMN IF NOT EXISTS created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;

UPDATE usuarios SET cliente_id = NULL WHERE rol_id = 1;
UPDATE usuarios SET cliente_id = 1 WHERE cliente_id IS NULL AND rol_id <> 1;

ALTER TABLE vehiculo ADD COLUMN IF NOT EXISTS cliente_id INT UNSIGNED NOT NULL DEFAULT 1;
ALTER TABLE cajon ADD COLUMN IF NOT EXISTS cliente_id INT UNSIGNED NOT NULL DEFAULT 1 FIRST;
ALTER TABLE resguardo ADD COLUMN IF NOT EXISTS cliente_id INT UNSIGNED NOT NULL DEFAULT 1 AFTER id;

CREATE TABLE IF NOT EXISTS bitacora (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    cliente_id INT UNSIGNED NULL,
    usuario_id INT NULL,
    accion VARCHAR(80) NOT NULL,
    entidad VARCHAR(80) NULL,
    entidad_id INT NULL,
    descripcion VARCHAR(255) NULL,
    ip VARCHAR(45) NULL,
    user_agent VARCHAR(255) NULL,
    creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_bitacora_cliente_fecha (cliente_id, creado_en),
    KEY idx_bitacora_usuario_fecha (usuario_id, creado_en)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
