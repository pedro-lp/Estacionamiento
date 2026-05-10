<?php
include("conexion.php");
#verifica si el metodo post trae algo
if (isset($_POST['enviar'])) {
    verify_csrf();
    $usuario = clean_text($_POST['usuario'] ?? '', 100);
    $clave = (string) ($_POST['clave'] ?? '');
    $clave2 = (string) ($_POST['clave2'] ?? '');

    if ($usuario === '' || strlen($clave) < 7 || $clave !== $clave2) {
        echo "<script>alert('Verifica usuario y contraseña');history.back();</script>";
        exit();
    }

    $existe = db_one("SELECT id FROM usuarios WHERE Usuario = ?", "s", $usuario);
    if (!$existe) {
        $hash = password_hash($clave, PASSWORD_DEFAULT);
        db_query("INSERT INTO usuarios (Usuario, password, rol_id, cliente_id) VALUES (?, ?, 4, 1)", "ss", $usuario, $hash);
        $nuevo = authenticate_user($usuario, $clave);
        if ($nuevo) {
            set_login_session($nuevo);
            audit_log("usuarios.crear", "usuarios", (int) $nuevo["id"], "Registro publico de usuario");
        }
        #se regresa al index.php
        header("location: index.php");
    } else {
        #se imprime un mensaje
        echo "<script>alert('El usuario ya existe');history.back();</script>";
    }
}
?>
<html lang="es">

<!-- cabecera de la pagina web -->
<head>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/app.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- titulo de la pagina-->
    <title>Pagina para poder registrarse en el sistema</title>
    <nav class="navbar navbar-dark parking-navbar justify-content-between">
        <a class="navbar-brand text-white" href="?">Gestionar Estacionamiento</a>
    </nav>
</head>

<!-- cuerpo de la pagina-->
<body class="parking-app">
    <div class="container p-4">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
            <h2 class="text-center mb-3">Registrar Usuario Nuevo</h2>
            <div>
                <!-- imagen -->
                <img src="img/logo.png" class="parking-logo d-block mx-auto mb-3" alt="Logo" loading="lazy">
            </div> <br>
            <div class="card parking-card">
                <div class="card-body p-4">
                <!-- formulario de registro -->                
                <form action="registrarse.php" method="POST" onsubmit="return validarDatos()">
                    <?php echo csrf_field(); ?>
                    <div class="form-group">
                        <label for="usuario">Usuario</label><br>
                        <input class="form-control" type="text" name="usuario" id="usuario" placeholder="Ingresar Usuario" required>
                    </div>
                    <div class="form-group">
                        <label for="clave">Contraseña</label><br>
                        <input class="form-control" type="password" name="clave" id="clave" placeholder="Ingresar Contraseña" required>
                    </div>
                    <div class="form-group">
                        <label for="clave2">Repetir Contraseña</label><br>
                        <input class="form-control" type="password" name="clave2" id="clave2" placeholder="Repetir Contraseña" required>
                    </div>
                    <div class="form-group">
                        <!-- boton regresar -->
                        <a class="btn btn-outline-secondary" href="login.php">Regresar</a>
                        <!-- boton enviar -->
                        <button class="btn btn-parking" name="enviar" id="enviar" type="submit">Registrarse</button>
                    </div>
                </form>
                </div>
            </div>
            </div>
        </div>
    </div>
</body>

</html>
<script>
    //metodo validarDatos() que regresa verdadero falso segun la longitud de la clave, la cual debe ser de 4 caracteres
    function validarDatos() {
        //asigna el valor del input clave a una variable aux
        var aux = document.getElementById("clave").value;
        var aux2 = document.getElementById("clave2").value;
        //evalua que la longitus sea de 4
        if (aux == aux2) {
            if (aux.length > 6) {
                return true;
            } else {
                //en caso de no ser muy corta regresa una alerta
                alert('Ingrese una contraseña mas larga');
                return false;
            }

        } else {
            //en caso de no ser verdadero regresa falso y una alerta
            alert('Claves diferentes');
            return false;
        }
    }
</script>
