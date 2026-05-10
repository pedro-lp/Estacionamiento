<?php
include("conexion.php");
require_permission("bitacora.ver");

$sql = "SELECT b.*, u.Usuario, c.nombre AS cliente_nombre
          FROM bitacora b
          LEFT JOIN usuarios u ON u.id = b.usuario_id
          LEFT JOIN clientes c ON c.id = b.cliente_id";
$sql .= tenant_clause("b.cliente_id");
$result = tenant_result($sql . " ORDER BY b.creado_en DESC LIMIT 200");
?>
<html lang="es">
<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/app.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bitacora</title>
    <nav class="navbar navbar-dark parking-navbar justify-content-between">
        <a class="navbar-brand text-white" href="?">Bitacora</a>
    </nav>
</head>
<body class="parking-app">
    <div class="container-fluid p-4">
        <div class="table-responsive">
            <table class="table table-striped bg-white">
                <thead class="thead-dark">
                    <tr>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>Usuario</th>
                        <th>Accion</th>
                        <th>Entidad</th>
                        <th>Detalle</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td><?php echo h($row["creado_en"]); ?></td>
                            <td><?php echo h($row["cliente_nombre"] ?? "General"); ?></td>
                            <td><?php echo h($row["Usuario"] ?? "Sistema"); ?></td>
                            <td><?php echo h($row["accion"]); ?></td>
                            <td><?php echo h(trim(($row["entidad"] ?? "") . " #" . ($row["entidad_id"] ?? ""))); ?></td>
                            <td><?php echo h($row["descripcion"] ?? ""); ?></td>
                            <td><?php echo h($row["ip"] ?? ""); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <a class="btn btn-outline-secondary" href="index.php">Regresar</a>
    </div>
</body>
</html>
