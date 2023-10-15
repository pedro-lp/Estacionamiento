<?php
#session_start();
$usuario = $_SESSION['usuario'];
$rol = (int) $_SESSION['rol'];
if (!isset($usuario)) {
    //si no tiene sesion iniciada se manda a login
    header("location: login.php");
} else {
    //si no tiene permiso se le pide que acceda con otro usuario
    if ($rol != 1 && $rol != 2 && $rol != 3 && $rol != 4) {
        echo ("<div align='center'><a href='login.php'><h4>No tienes permiso para acceder a esta seccion</h4></a><br></div>");
    }
}
?>
<html lang="es">

<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagina principal</title>
    <nav class="navbar navbar-light bg-info justify-content-between">
        <a class="navbar-brand text-white">
            <h6>Usuario: <?php echo $usuario;
                            switch ($rol) {
                                case 1:
                                    echo " tipo: Admin";
                                    break;
                                case 2:
                                    echo " tipo: Cajero";
                                    break;
                                case 3:
                                    echo " tipo: Valet";
                                    break;
                                case 4:
                                    echo " tipo: Cliente";
                                    break;
                            } ?></h6>
        </a>
        <a class="navbar-brand text-white" href="?">
            <h4>Gestionar Estacionamiento</h4>
        </a>
        <a class="navbar-brand text-white" href="logout.php">
            <h6>Cerrar Sesion</h6>
        </a>
    </nav>
</head>

<body style="background: linear-gradient(180deg, #C6FCFC, #A7FC97);">
    <div class="container p-4">
        <center>
            <div class="col">
                <a class="btn btn-info" href="adminUsu.php">Administrar Usuarios</a>
                <a class="btn btn-info" href="adminCon.php">Administrar Conductores</a>
            </div>
        </center>
    </div>
</body>

</html>