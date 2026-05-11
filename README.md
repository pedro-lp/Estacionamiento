# Estacionamiento

Sistema web en PHP para administrar estacionamientos por cliente.

El proyecto permite registrar usuarios, vehiculos, cajones, resguardos, reservaciones, pagos, reportes, graficas, bitacora y generacion de PDFs. La version actual separa la informacion por cliente para que un cliente no vea datos de otro, salvo usuarios con rol de administrador general.

## Stack

- PHP
- MySQL
- Bootstrap en vistas existentes
- FPDF para reportes PDF
- Firebase Realtime Database para chat

## Modulos Principales

- Login y registro de usuarios.
- Administracion de clientes.
- Administracion de usuarios por rol, cliente y horario.
- Registro y edicion de vehiculos.
- Control de cajones disponibles, ocupados y reservados.
- Resguardo y reserva de vehiculos.
- Calculo de pagos.
- Reportes, graficas y PDFs.
- Bitacora de acciones por usuario.

## Configuracion

La conexion a MySQL ya no debe editarse directo en `conexion.php`. Usa variables de entorno:

```bash
DB_HOST=localhost
DB_NAME=estacionamiento_dev
DB_USER=estacionamiento_user
DB_PASSWORD=tu-clave
```

Para desarrollo local tambien puedes crear `config.local.php` tomando como base `config.local.example.php`. Ese archivo esta ignorado por Git.

Para el chat, crea `chat/firebase.config.js` a partir de `chat/firebase.config.example.js`. Tambien esta ignorado por Git.

## Base de Datos

Para una instalacion nueva:

```bash
mysql -u estacionamiento_user -p estacionamiento_dev < database.sql
```

Para una base existente, primero respalda la base y despues ejecuta:

```bash
mysql -u estacionamiento_user -p estacionamiento_dev < migrations/2026_05_10_multi_cliente_seguridad.sql
```

El esquema incluye `clientes`, permisos dinamicos, horarios por usuario, aislamiento por `cliente_id` y `bitacora`.

## Archivos Relevantes

- `database.sql`: esquema inicial de base de datos.
- `migrations/2026_05_10_multi_cliente_seguridad.sql`: migracion desde una base anterior.
- `conexion.php`: conexion principal a MySQL y helpers de seguridad.
- `index.php`: vista principal de cajones.
- `clientes.php`: alta y estado de clientes.
- `bitacora.php`: acciones realizadas por usuarios.
- `pagar.php` y `pagaradd.php`: flujo de cobro.
- `generarpdf.php`, `pdfresguardo.php`, `reservarpdf.php`: reportes PDF.

## Nota de Mantenimiento

Antes de rentarlo en produccion, revisa reglas de Firebase, certificados HTTPS, respaldos automaticos, rotacion de contrasenas y politicas de retencion de bitacora.
