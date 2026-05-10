<?php
#session_start();
#Comprobar si la variable está definida
if (isset($_POST['enviar'])) {
    #si no tiene sesion iniciada se manda a login
    include("conexion.php");
    require_permission("usuarios.editar");
    verify_csrf();
    #asigna el id a la variable convirtiendolo a Int
    $id = (int) $_POST['id'];
    $usuario = clean_text($_POST['usuario'] ?? '', 100);
    $rol = clean_role($_POST['rol'] ?? 4);
    $clienteId = is_general_admin() ? (int) ($_POST["cliente_id"] ?? 1) : current_client_id();
    $horarioInicio = $_POST["horario_inicio"] !== "" ? clean_text($_POST["horario_inicio"], 8) : null;
    $horarioFin = $_POST["horario_fin"] !== "" ? clean_text($_POST["horario_fin"], 8) : null;
    $activo = isset($_POST["activo"]) ? 1 : 0;
    $clave = (string) ($_POST['clave'] ?? '');
    if ($clave !== '') {
        #se hace un update con clave nueva
        $hash = password_hash($clave, PASSWORD_DEFAULT);
        db_query("UPDATE usuarios SET Usuario = ?, password = ?, rol_id = ?, cliente_id = ?, horario_inicio = ?, horario_fin = ?, activo = ? WHERE id = ?", "ssiissii", $usuario, $hash, $rol, $clienteId, $horarioInicio, $horarioFin, $activo, $id);
    } else {
        #se hace un update sin clave nueva
        db_query("UPDATE usuarios SET Usuario = ?, rol_id = ?, cliente_id = ?, horario_inicio = ?, horario_fin = ?, activo = ? WHERE id = ?", "siissii", $usuario, $rol, $clienteId, $horarioInicio, $horarioFin, $activo, $id);
    }
    audit_log("usuarios.editar", "usuarios", $id, "Usuario actualizado");
    mysqli_close($conexion);
    #se manda ala pagina de administrar
    header("location: adminUsu.php");
} else {
    include("conexion.php");
    require_permission("usuarios.editar");
    #se recibe el id que manda el usuario, y se buscan los demas atributos
    $id = (int) $_REQUEST['id'];
    if (is_general_admin()) {
        $row = db_one("SELECT Usuario, rol_id, cliente_id, horario_inicio, horario_fin, activo FROM usuarios WHERE id = ?", "i", $id);
    } else {
        $row = db_one("SELECT Usuario, rol_id, cliente_id, horario_inicio, horario_fin, activo FROM usuarios WHERE id = ? AND cliente_id = ?", "ii", $id, current_client_id());
    }
    if (!$row) {
        header("location: adminUsu.php");
        exit();
    }
    #se asignan atributos
    $nombre = $row['Usuario'];
    $rol = $row['rol_id'];
    $clienteId = (int) ($row["cliente_id"] ?? 1);
    $horarioInicio = $row["horario_inicio"] ?? "";
    $horarioFin = $row["horario_fin"] ?? "";
    $activo = (int) ($row["activo"] ?? 1);
}
?>
<html lang="es">

<!-- cabecera de la pagina web -->
<head>
    <!-- importacion de bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/app.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- titulo de la pagina-->
    <title>Editar Usuario</title>
    <nav class="navbar navbar-dark parking-navbar justify-content-between">
        <a class="navbar-brand text-white" href="?">Editar Usuario</a>
    </nav>
</head>

<!-- cuerpo de la pagina-->
<body class="parking-app">
    <div class="container p-4">
        <center>
            <h2>Editar Usuario</h2>
            <!-- imagen -->
            <div>
                <img src="img/logo.png" width="100" class="d-inline-block align-top" alt="" loading="lazy">
            </div> <br>
            <div class="card parking-card col-lg-6 col-md-8 mx-auto">
                <div class="card-body p-4">
                <!-- formulario que contiene los datos que previamente se sacaron de la base de datos -->
                <form action="editUsu.php" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="form-group">
                        <input type="hidden" name="id" id="id" value="<?php echo $id; ?>">
                        <label for="usuario">Nombre de Usuario</label><br>
                        <input class="form-control" type="text" name="usuario" id="usuario" value="<?php echo h($nombre); ?>" placeholder="Ingresar Usuario" required>
                    </div>
                    <div class="form-group">
                    <!-- segun el tipo de usuario que trae la base de datos es el que se selecciona -->
                        <label for="clave2">Tipo de Usuario</label><br>
                        <select class="form-control" name="rol" id="rol" required>
                            <?php foreach (db_all("SELECT rol_id, nombre FROM roles WHERE activo = 1 ORDER BY rol_id") as $rolRow) { ?>
                                <option value="<?php echo (int) $rolRow["rol_id"]; ?>" <?php echo (int) $rolRow["rol_id"] === (int) $rol ? "selected" : ""; ?>>
                                    <?php echo h($rolRow["nombre"]); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="cliente_id">Cliente</label><br>
                        <select class="form-control" name="cliente_id" id="cliente_id" <?php echo is_general_admin() ? "" : "disabled"; ?>>
                            <?php foreach (db_all("SELECT id, nombre FROM clientes WHERE activo = 1 ORDER BY nombre") as $cliente) { ?>
                                <option value="<?php echo (int) $cliente["id"]; ?>" <?php echo (int) $cliente["id"] === $clienteId ? "selected" : ""; ?>>
                                    <?php echo h($cliente["nombre"]); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="horario_inicio">Horario inicio</label>
                            <input class="form-control" type="time" name="horario_inicio" id="horario_inicio" value="<?php echo h($horarioInicio); ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="horario_fin">Horario fin</label>
                            <input class="form-control" type="time" name="horario_fin" id="horario_fin" value="<?php echo h($horarioFin); ?>">
                        </div>
                    </div>
                    <div class="form-group form-check text-left">
                        <input class="form-check-input" type="checkbox" name="activo" id="activo" <?php echo $activo === 1 ? "checked" : ""; ?>>
                        <label class="form-check-label" for="activo">Usuario activo</label>
                    </div>
                    <div class="form-group">
                        <label for="clave">si lo requieres ingresa una Nueva Contraseña</label><br>
                        <input class="form-control" type="password" name="clave" id="clave" placeholder="Ingresar Contraseña">
                    </div>
                    <!-- boton regresar -->
                    <div class="form-group">
                        <a class="btn btn-outline-secondary" href="adminUsu.php">Regresar</a>
                        <!-- boton enviar -->
                        <button class="btn btn-parking" name="enviar" id="enviar" type="submit">Modificar Usuario</button>
                    </div>
                </form>
                </div>
            </div>
        </center>
    </div>
</body>

</html>
