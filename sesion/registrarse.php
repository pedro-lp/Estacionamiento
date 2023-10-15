<?php
if (isset($_POST['enviar'])) {
    include("conexion.php");
    #session_start();
    echo $usuario = $_POST['usuario'];
    echo $clave = $_POST['clave'];
    mysqli_query($conexion, "INSERT INTO usuarios (Usuario, password, rol_id) VALUES ('$usuario','$clave','4')");
    $q = "SELECT COUNT(*) as contar from usuarios where Usuario='$usuario' and password ='$clave'";
    $consulta = mysqli_query($conexion, $q);
    $array = mysqli_fetch_array($consulta);
    mysqli_close($conexion);
    if ($array['contar'] > 0) {
        $_SESSION['Usuario'] = $usuario;
        $_SESSION['rol_id'] = 4;
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
    <title>Pagina para poder registrarse en el sistema</title>
    <nav class="navbar navbar-light bg-info justify-content-between">
        <a class="navbar-brand text-white" href="?">Gestionar Estacionamiento</a>
    </nav>
</head>

<body style="background: linear-gradient(180deg, #C6FCFC, #A7FC97);">
    <div class="container p-4">
        <center>
            <h2>Registrar Usuario Nuevo</h2>
            <div>
                <img src="../img/logo.png" width="100" class="d-inline-block align-top" alt="" loading="lazy">
            </div> <br>
            <div class="p-3 mb-2 bg-secondary text-white w-50">
                <form action="registrarse.php" method="POST" onsubmit="return validarDatos()">
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
                        <a class="navbar-brand text-white" href="login.php">
                            <h6>Regresar</h6>
                        </a>
                        <button class="btn btn-primary" name="enviar" id="enviar" type="submit">Registrarse</button>
                    </div>
                </form>
            </div>
        </center>
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
            if (aux.length > 4) {
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