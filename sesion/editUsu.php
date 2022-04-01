<?php
session_start();
#Comprobar si la variable está definida
if (isset($_POST['enviar'])) {
    include("conexion.php");
    #asigna el id a la variable convirtiendolo a Int
    $id = (int) $_POST['id'];
    if ($_POST['clave']) {
        mysqli_query($conexion, "UPDATE usuarios SET username='" . $_POST['usuario'] . "', password='" . $_POST['clave'] . "', rol_id='" . $_POST['rol'] . "' WHERE id='$id'");
    } else {
        mysqli_query($conexion, "UPDATE usuarios SET username='" . $_POST['usuario'] . "', rol_id='" . $_POST['rol'] . "' WHERE id='$id'");
    }
    mysqli_close($conexion);
    header("location: adminUsu.php");
} else {
    include("conexion.php");
    #se recibe el id que manda el usuario, y se buscan los demas atributos
    $id = (int) $_REQUEST['id'];
    $result = mysqli_query($conexion, "SELECT username, rol_id from usuarios where id='$id'");
    $row = mysqli_fetch_array($result);
    $nombre = $row[0];
    $rol = $row[1];
}
?>
<html lang="es">

<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
    <nav class="navbar navbar-light bg-info justify-content-between">
        <a class="navbar-brand text-white" href="?">Editar Usuario</a>
    </nav>
</head>

<body style="background: linear-gradient(180deg, #C6FCFC, #A7FC97);">
    <div class="container p-4">
        <center>
            <h2>Editar Usuario</h2>
            <div>
                <img src="../img/logo.png" width="100" class="d-inline-block align-top" alt="" loading="lazy">
            </div> <br>
            <div class="p-3 mb-2 bg-secondary text-white w-50">
                <form action="editUsu.php" method="POST">
                    <div class="form-group">
                        <input type="hidden" name="id" id="id" value="<?php echo $id; ?>">
                        <label for="usuario">Nombre de Usuario</label><br>
                        <input class="form-control" type="text" name="usuario" id="usuario" value="<?php echo $nombre; ?>" placeholder="Ingresar Usuario" required>
                    </div>
                    <div class="form-group">
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
                    <div class="form-group">
                        <a class="navbar-brand text-white" href="adminUsu.php">
                            <h6>Regresar</h6>
                        </a>
                        <button class="btn btn-primary" name="enviar" id="enviar" type="submit">Modificar Usuario</button>
                    </div>
                </form>
            </div>
        </center>
    </div>
</body>

</html>