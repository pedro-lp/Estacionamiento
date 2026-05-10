<?php
include("conexion.php");
#Comprobar si la variable está definida
if (isset($_POST['enviar'])) {
    verify_csrf();
    $usuario = clean_text($_POST['usuario'] ?? '', 100);
    $clave = (string) ($_POST['clave'] ?? '');
    $mostrar = authenticate_user($usuario, $clave);
    #si se encotnro al menos un registro se pasa a lo demas
    if ($mostrar != null) {
        set_login_session($mostrar);
        audit_log("login", "usuarios", (int) $mostrar["id"], "Inicio de sesion");
        #se manda al index
        header("location: index.php");
        exit();
    } else {
        #se imprime un mensaje
        echo "<script>alert('LOS DATOS SON INCORRECTOS');</script>";
    }
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
    <title>Login con sesiones</title>
    <nav class="navbar navbar-dark parking-navbar justify-content-between">
        <a class="navbar-brand text-white" href="?">Gestionar Estacionamiento</a>
    </nav>
</head>

<!-- cuerpo de la pagina-->
<body class="parking-app">
    <div class="container p-4">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
            <h2 class="text-center mb-3">Inicio de Sesión</h2>
            <!-- imagen -->
            <div class="text-center">
                <img src="img/logo.png" class="parking-logo mb-3" alt="Logo" loading="lazy">
            </div> <br>
            <div class="card parking-card">
                <div class="card-body p-4">
                <form action="login.php" method="post">
                    <?php echo csrf_field(); ?>
                    <div class="form-group">
                        <label for="usuario">Usuario</label><br>
                        <input class="form-control" type="text" name="usuario" id="usuario" placeholder="Ingresar Usuario" required>
                    </div>
                    <div class="form-group">
                        <label for="clave">Contraseña</label><br>
                        <input class="form-control" type="password" name="clave" id="clave" placeholder="Ingresar Contraseña" required>
                    </div>
                        <!-- link para registrarse -->
                    <a class="d-block mb-3" href='registrarse.php'>¿No tienes cuenta? Registrate</a>
                        <!-- boton enviar -->
                    <div class="form-group">
                        <button class="btn btn-parking btn-block" name="enviar" id="enviar" type="submit">Iniciar Sesion</button>
                    </div>
                </form>
                </div>
            </div>
            </div>
        </div>
    </div>
</body>

</html>
