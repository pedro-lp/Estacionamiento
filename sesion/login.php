<?php
if (isset($_POST['enviar'])) {
    include("conexion.php");
    #session_start();
    $usuario = $_POST['usuario'];
    $clave = $_POST['clave'];
    $result = mysqli_query($conexion, "SELECT * from usuarios where Usuario='$usuario' and password='$clave'");
    $mostrar = mysqli_fetch_array($result);
    if ($mostrar != null) {
        $_SESSION['usuario'] = $mostrar['Usuario'];
        $_SESSION['rol'] = $mostrar['rol_id'];
        header("location: index.php");
    } else {
        echo "<script>alert('LOS DATOS SON INCORRECTOS');</script>";
    }
}
?>
<html lang="es">

<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login con sesiones</title>
    <nav class="navbar navbar-light bg-info justify-content-between">
        <a class="navbar-brand text-white" href="?">Gestionar Estacionamiento</a>
    </nav>
</head>

<body style="background: linear-gradient(180deg, #C6FCFC, #A7FC97);">
    <div class="container p-4">        
        <center>
            <h2>Inicio de Sesión</h2>
            <div>
                <img src="../img/logo.png" width="100" class="d-inline-block align-top" alt="" loading="lazy">
            </div> <br>
            <div class="p-3 mb-2 bg-secondary text-white w-50">
                <form action="login.php" method="post">
                    <div class="form-group">
                        <label for="usuario">Usuario</label><br>
                        <input class="form-control w-75" type="text" name="usuario" id="usuario" placeholder="Ingresar Usuario">
                    </div>
                    <div class="form-group">
                        <label for="clave">Contraseña</label><br>
                        <input class="form-control w-75" type="password" name="clave" id="clave" placeholder="Ingresar Contraseña">
                    </div>
                    <a class="text-white" href='registrarse.php'>
                        <h5>¿no tienes cuenta? Registrate</h5>
                    </a><br>
                    <div class="form-group">
                        <button class="btn btn-primary" name="enviar" id="enviar" type="submit">Iniciar Sesion</button>
                    </div>
                </form>
            </div>
        </center>
    </div>
</body>

</html>