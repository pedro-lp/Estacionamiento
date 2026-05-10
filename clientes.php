<?php
include("conexion.php");
require_permission("clientes.ver");

function slugify_cliente(string $nombre): string
{
    $slug = strtolower(trim($nombre));
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    return trim($slug, '-') ?: 'cliente';
}

if (isset($_POST["crear"])) {
    require_permission("clientes.crear");
    verify_csrf();
    $nombre = clean_text($_POST["nombre"] ?? "", 120);
    $slug = slugify_cliente($_POST["slug"] ?: $nombre);

    if ($nombre !== "") {
        db_query("INSERT INTO clientes (nombre, slug, activo) VALUES (?, ?, 1)", "ss", $nombre, $slug);
        $clienteId = (int) mysqli_insert_id($conexion);
        for ($i = 1; $i <= 24; $i++) {
            $area = $i <= 16 ? "10m2" : "15m2";
            db_query("INSERT IGNORE INTO cajon (cliente_id, id, area, situacion) VALUES (?, ?, ?, 'disponible')", "iis", $clienteId, $i, $area);
        }
        audit_log("clientes.crear", "clientes", $clienteId, "Cliente creado: " . $nombre);
        flash("Cliente creado con sus 24 cajones.", "success");
        header("Location: clientes.php");
        exit();
    }
}

if (isset($_POST["toggle"])) {
    require_permission("clientes.editar");
    verify_csrf();
    $id = (int) $_POST["id"];
    $activo = (int) $_POST["activo"] === 1 ? 0 : 1;
    db_query("UPDATE clientes SET activo = ? WHERE id = ?", "ii", $activo, $id);
    audit_log("clientes.editar", "clientes", $id, "Cambio de estado del cliente");
    header("Location: clientes.php");
    exit();
}

$clientes = db_all("SELECT * FROM clientes ORDER BY nombre");
?>
<html lang="es">
<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/app.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes</title>
    <nav class="navbar navbar-dark parking-navbar justify-content-between">
        <a class="navbar-brand text-white" href="?">Clientes</a>
    </nav>
</head>
<body class="parking-app">
    <div class="container p-4">
        <?php if (isset($_SESSION['message'])) { ?>
            <div class="alert alert-<?php echo h($_SESSION['message_type']); ?>"><?php echo h($_SESSION['message']); ?></div>
        <?php unset($_SESSION['message'], $_SESSION['message_type']); } ?>

        <?php if (can("clientes.crear")) { ?>
            <form class="card parking-card p-4 mb-4" method="POST" action="clientes.php">
                <?php echo csrf_field(); ?>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="nombre">Nombre del cliente</label>
                        <input class="form-control" name="nombre" id="nombre" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="slug">Identificador</label>
                        <input class="form-control" name="slug" id="slug" placeholder="se genera si lo dejas vacio">
                    </div>
                </div>
                <button class="btn btn-parking" name="crear" type="submit">Crear cliente</button>
            </form>
        <?php } ?>

        <table class="table bg-white">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Slug</th>
                    <th>Activo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clientes as $cliente) { ?>
                    <tr>
                        <td><?php echo (int) $cliente["id"]; ?></td>
                        <td><?php echo h($cliente["nombre"]); ?></td>
                        <td><?php echo h($cliente["slug"]); ?></td>
                        <td><?php echo (int) $cliente["activo"] === 1 ? "Si" : "No"; ?></td>
                        <td>
                            <?php if (can("clientes.editar")) { ?>
                                <form class="d-inline" method="POST" action="clientes.php">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="id" value="<?php echo (int) $cliente["id"]; ?>">
                                    <input type="hidden" name="activo" value="<?php echo (int) $cliente["activo"]; ?>">
                                    <button class="btn btn-outline-secondary" name="toggle" type="submit">
                                        <?php echo (int) $cliente["activo"] === 1 ? "Desactivar" : "Activar"; ?>
                                    </button>
                                </form>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <a class="btn btn-outline-secondary" href="index.php">Regresar</a>
    </div>
</body>
</html>
