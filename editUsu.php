<?php
#session_start();
#Comprobar si la variable está definida
if (isset($_POST['enviar'])) {
    #si no tiene sesion iniciada se manda a login
    include("conexion.php");
    #asigna el id a la variable convirtiendolo a Int
    $id = (int) $_POST['id'];
    $usuario = clean_text($_POST['usuario'] ?? '', 100);
    $rol = clean_role($_POST['rol'] ?? 4);
    $clave = (string) ($_POST['clave'] ?? '');
    if ($clave !== '') {
        #se hace un update con clave nueva
        $hash = password_hash($clave, PASSWORD_DEFAULT);
        db_query("UPDATE usuarios SET Usuario = ?, password = ?, rol_id = ? WHERE id = ?", "ssii", $usuario, $hash, $rol, $id);
    } else {
        #se hace un update sin clave nueva
        db_query("UPDATE usuarios SET Usuario = ?, rol_id = ? WHERE id = ?", "sii", $usuario, $rol, $id);
    }
    mysqli_close($conexion);
    #se manda ala pagina de administrar
    header("location: adminUsu.php");
} else {
    include("conexion.php");
    #se recibe el id que manda el usuario, y se buscan los demas atributos
    $id = (int) $_REQUEST['id'];
    $row = db_one("SELECT Usuario, rol_id FROM usuarios WHERE id = ?", "i", $id);
    if (!$row) {
        header("location: adminUsu.php");
        exit();
    }
    #se asignan atributos
    $nombre = $row['Usuario'];
    $rol = $row['rol_id'];
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
                    <div class="form-group">
                        <input type="hidden" name="id" id="id" value="<?php echo $id; ?>">
                        <label for="usuario">Nombre de Usuario</label><br>
                        <input class="form-control" type="text" name="usuario" id="usuario" value="<?php echo h($nombre); ?>" placeholder="Ingresar Usuario" required>
                    </div>
                    <div class="form-group">
                    <!-- segun el tipo de usuario que trae la base de datos es el que se selecciona -->
                        <label for="clave2">Tipo de Usuario</label><br>
                        <select class="form-control" name="rol" id="rol" required>
                            <option value="1" <?php if ($rol == 1) {
                                                    echo "selected";
                                                } ?>>Admin</option>
                            <option value="2" <?php if ($rol == 2) {
                                                    echo "selected";
                                                } ?>>Cajero</option>
                            <option value="3" <?php if ($rol == 3) {
                                                    echo "selected";
                                                } ?>>Valet</option>
                            <option value="4" <?php if ($rol == 4) {
                                                    echo "selected";
                                                } ?>>Conductor</option>
                        </select>
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
