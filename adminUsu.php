<?php
#inicia la sesion
#session_start();
include("conexion.php");
require_permission("usuarios.ver");

if (isset($_POST["crear"])) {
    require_permission("usuarios.crear");
    verify_csrf();
    $nuevoUsuario = clean_text($_POST["usuario"] ?? "", 100);
    $clave = (string) ($_POST["clave"] ?? "");
    $rolNuevo = clean_role($_POST["rol"] ?? 4);
    $clienteNuevo = is_general_admin() ? (int) ($_POST["cliente_id"] ?? 1) : current_client_id();
    $horarioInicio = $_POST["horario_inicio"] !== "" ? clean_text($_POST["horario_inicio"], 8) : null;
    $horarioFin = $_POST["horario_fin"] !== "" ? clean_text($_POST["horario_fin"], 8) : null;

    if ($nuevoUsuario !== "" && strlen($clave) >= 7) {
        $hash = password_hash($clave, PASSWORD_DEFAULT);
        db_query(
            "INSERT INTO usuarios (Usuario, password, rol_id, cliente_id, horario_inicio, horario_fin, activo) VALUES (?, ?, ?, ?, ?, ?, 1)",
            "ssiiss",
            $nuevoUsuario,
            $hash,
            $rolNuevo,
            $clienteNuevo,
            $horarioInicio,
            $horarioFin
        );
        audit_log("usuarios.crear", "usuarios", (int) mysqli_insert_id($conexion), "Usuario creado desde admin");
        flash("Usuario creado correctamente.", "success");
        header("Location: adminUsu.php");
        exit();
    }

    flash("Verifica el usuario y usa una contrasena de al menos 7 caracteres.", "warning");
}

?>
<html lang="es">
<!-- cabecera de la paguina web -->
<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/app.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- titulo de la pagina-->
    <title>Administrar Usuarios</title>
    <nav class="navbar navbar-dark parking-navbar justify-content-between">
        <a class="navbar-brand text-white" href="?">Administrar Usuarios</a>
    </nav>
</head>
<!-- cuerpo de la pagina-->
<body class="parking-app">

    <div class="container p-4">
        <?php if (isset($_SESSION['message'])) { ?>
            <div class="alert alert-<?php echo h($_SESSION['message_type']); ?>"><?php echo h($_SESSION['message']); ?></div>
        <?php unset($_SESSION['message'], $_SESSION['message_type']); } ?>
        <?php if (can("usuarios.crear")) { ?>
            <form class="card parking-card p-4 mb-4" method="POST" action="adminUsu.php">
                <?php echo csrf_field(); ?>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="usuario">Usuario</label>
                        <input class="form-control" name="usuario" id="usuario" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="clave">Contrasena</label>
                        <input class="form-control" type="password" name="clave" id="clave" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="rol">Rol</label>
                        <select class="form-control" name="rol" id="rol">
                            <?php foreach (db_all("SELECT rol_id, nombre FROM roles WHERE activo = 1 ORDER BY rol_id") as $rolRow) { ?>
                                <option value="<?php echo (int) $rolRow["rol_id"]; ?>"><?php echo h($rolRow["nombre"]); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="cliente_id">Cliente</label>
                        <select class="form-control" name="cliente_id" id="cliente_id" <?php echo is_general_admin() ? "" : "disabled"; ?>>
                            <?php foreach (db_all("SELECT id, nombre FROM clientes WHERE activo = 1 ORDER BY nombre") as $cliente) { ?>
                                <option value="<?php echo (int) $cliente["id"]; ?>"><?php echo h($cliente["nombre"]); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="horario_inicio">Horario inicio</label>
                        <input class="form-control" type="time" name="horario_inicio" id="horario_inicio">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="horario_fin">Horario fin</label>
                        <input class="form-control" type="time" name="horario_fin" id="horario_fin">
                    </div>
                </div>
                <button class="btn btn-parking" name="crear" type="submit">Crear usuario</button>
            </form>
        <?php } ?>
        <?php
        #se incluye la conexion
        #se hace un select
        $sql = "SELECT u.id, u.Usuario, r.nombre AS rol_nombre, c.nombre AS cliente_nombre, u.horario_inicio, u.horario_fin, u.activo
                  FROM usuarios u
                  INNER JOIN roles r ON r.rol_id = u.rol_id
                  LEFT JOIN clientes c ON c.id = u.cliente_id";
        $sql .= tenant_clause("u.cliente_id");
        $result = tenant_result($sql . " ORDER BY u.id");
        #se imprime la tabla
        echo "        
        <table class='table'>
        <thead class='thead-dark'>
            <th scope='col' colspan='7'>Tabla General</th>
        </thead>
        <tbody>
            <th scope='col'>ID</th>
            <th scope='col'>Nombre</th>
            <th scope='col'>Tipo de usuario</th>
            <th scope='col'>Cliente</th>
            <th scope='col'>Horario</th>
            <th scope='col'>Activo</th>
            <th scope='col'>Opciones</th>";
            #se hace un while para obtener todos los datos
        while ($row = mysqli_fetch_array($result)) {
            $id = (int) $row[0];
            $token = h(csrf_token());
            $horario = ($row['horario_inicio'] && $row['horario_fin']) ? $row['horario_inicio'] . ' - ' . $row['horario_fin'] : 'Siempre';
            echo "<tr>
			<td>" . h($row['id']) . "</td>
			<td>" . h($row['Usuario']) . "</td>
            <td>" . h($row['rol_nombre']) . "</td>
            <td>" . h($row['cliente_nombre'] ?? 'General') . "</td>
            <td>" . h($horario) . "</td>
            <td>" . ((int) $row['activo'] === 1 ? 'Si' : 'No') . "</td>
            <td>";
            if (can("usuarios.editar")) {
                echo "<a href='editUsu.php?id=$id' class='btn btn-outline-warning'>Editar</a> ";
            }
            if (can("usuarios.eliminar")) {
                echo "<a href='elimUsu.php?id=$id&csrf_token=$token' class='btn btn-outline-danger'>Remover</a>";
            }
            echo "</td>
        </tr>
        <tbody>";
        }
        #se cierra la tabla
        echo "</table>";
        ?>
        <!-- boton regresar -->
        <a class="btn btn-outline-secondary" href="index.php">Regresar</a>
    </div>

</body>

</html>
