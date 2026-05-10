<?php
//header('Cache-Control: no cache'); //no cache
//session_cache_limiter('private_no_expire'); // works
#session_cache_limiter('public'); // works too
/*session_start();
session_unset();
$_SESSION['usuario'] = $usuario;
$_SESSION['rol'] = $rol;*/
include("conexion.php");
if (!isset($_SESSION['usuario']) || !isset($_SESSION['rol'])) {
    //si no tiene sesion iniciada se manda a login
    header("location: login.php");
    exit();
} else {
    $rol = (int) $_SESSION['rol'];
    //si no tiene permiso se le pide que acceda con otro usuario
    if ($rol != 1 && $rol != 2 && $rol != 3 && $rol != 4) {
        echo ("<div align='center'><a href='/sesion/login.php'><h4>No tienes permiso para acceder a esta seccion</h4></a><br></div>");
    }
}
if (isset($_POST['add'])) {
    $id = (int) $_SESSION['id'];
    $area = clean_text($_POST['area'] ?? '', 10);
    $situacion = in_array($_POST['situacion'] ?? '', ['disponible', 'ocupado', 'reservado'], true) ? $_POST['situacion'] : 'disponible';
    db_query("UPDATE cajon SET area = ?, situacion = ? WHERE id = ?", "ssi", $area, $situacion, $id);
    mysqli_close($conexion);
    header("location:index.php");    
}
?>

<html lang="es">

<head>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/app.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <nav class="navbar navbar-dark parking-navbar justify-content-between">
        <a class="navbar-brand text-white">
        <!-- se imprime el nombre de usuario y el tipo de usuario que es -->
            <h6>Usuario: <?php echo h($_SESSION['usuario']);
                            switch ($rol) {
                                case 1:
                                    echo " Tipo: Admin";
                                    break;
                                case 2:
                                    echo " Tipo: Cajero";
                                    break;
                                case 3:
                                    echo " Tipo: Valet";
                                    break;
                                case 4:
                                    echo " Tipo: Cliente";
                                    break;
                            } ?></h6>
        </a>
        <a class="navbar-brand text-white" href="?">
            <h4>Gestionar Estacionamiento</h4>
        </a>
        <!-- boton para cerrar la sesion -->
        <a class="navbar-brand text-white" href="logout.php">
            <h6>Cerrar Sesion</h6>
        </a>
    </nav>
</head>

<!-- cuerpo de la pagina-->
<body class="parking-app">
    <div class="container p-4">
        <br>
        <div align="center">
            <?php if (isset($_SESSION['message'])) { ?>
                <div class="alert alert-<?= $_SESSION['message_type']; ?> alert-dismissible fade show" role="alert">
                    <?= $_SESSION['message'] ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php unset($_SESSION['message'], $_SESSION['message_type']);
            } ?>
            <!-- dependiendo el tipo de usuario se mostraran las diferentes opciones-->
            <div class="row p-3 mb-2 bg-white parking-card parking-actions">
                <?php if ($rol == 1 || $rol == 2) {#dependiendo el tipo de usuario se muestra o no
                ?>
                    <div class="col">
                        <a class="btn btn-parking" href="regvehiculo.php">Registrar Vehiculo</a>
                    </div>
                <?php
                }
                if ($rol == 1 || $rol == 2) {#dependiendo el tipo de usuario se muestra o no
                ?>
                    <div class="col">
                        <a class="btn btn-secondary" href="resguardar.php">Resguardar Vehiculo</a>
                    </div>
                <?php
                }
                if ($rol == 2) {#dependiendo el tipo de usuario se muestra o no
                ?>
                    <div class="col">
                        <a class="btn btn-success" href="pagar.php">Sacar Vehiculo</a>
                    </div>
                <?php
                }
                if ($rol == 1) {#dependiendo el tipo de usuario se muestra o no
                ?>
                    <div class="col">
                        <a class="btn btn-danger" href="reporteFecGraf.php">Ocupación por cajón</a>
                    </div>
                <?php
                }
                ?>
            </div><br>
            <div class="row p-3 mb-2 bg-white parking-card parking-actions">
                <?php
                if ($rol == 1 || $rol == 2) {#dependiendo el tipo de usuario se muestra o no
                ?>
                    <div class="col">
                        <a class="btn btn-accent" href="adminCon.php">Administrar Conductores</a>
                    </div>
                <?php
                }
                if ($rol == 1) {#dependiendo el tipo de usuario se muestra o no
                ?>
                    <div class="col">
                        <a class="btn btn-info" href="adminUsu.php">Administrar Usuarios</a>
                    </div>
                <?php
                }
                if ($rol == 1 || $rol == 2) {#dependiendo el tipo de usuario se muestra o no
                ?>
                    <div class="col">
                        <a class="btn btn-dark" href="reporte.php">Corte de caja</a>
                    </div>
                <?php
                }
                if ($rol == 2 || $rol == 4) {#dependiendo el tipo de usuario se muestra o no
                ?>
                    <div class="col">
                        <a class="btn btn-primary" href="chat/index.php">Chat grupal</a>
                    </div>
                <?php
                }
                if ($rol == 2 || $rol == 4) {#dependiendo el tipo de usuario se muestra o no
                ?>
                    <div class="col">
                        <a class="btn btn-info" href="resguardarRes.php">Reservar Vehiculo</a>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>
    </div>
    <br>
    <div><?php
        if ($rol != 4) {#dependiendo el tipo de usuario se muestra o no
        ?>
        <div>
            <!-- tabla donde se muestran los cajones del estacionamiento -->
            <table class="table table-parking">
                <thead class="thead-dark text-center">
                    <th colspan="8">Estado de los Cajones</th>
                </thead>
                <tbody>
                    <tr>
                        <?php #se hace un ciclo de repeticion de 8 registros
                        #se hace un ciclo de repeticion de 8 registros
                        for ($i = 1; $i <= 8; $i++) {
                            #se hace un select
                            $mostrar = db_one("SELECT * FROM cajon WHERE id = ?", "i", $i);
                            #se hace un select de los autos que aun no han salido
                            $mostrar2 = db_one("SELECT placas FROM resguardo WHERE id_cajon = ? AND pago = 0", "i", $i);
                            #dependiendo la situacion es el color de fondo que asignara?>
                            <td class="parking-space text-white <?php echo h($mostrar['situacion']); ?>">

                                <?php echo "Id Cajon = " . h($mostrar['id']) . "<br>";#imprime el cajon
                                echo "Situacion = " . h($mostrar['situacion']) . "<br>";#imprime la situacion
                                if ($mostrar['situacion'] == 'ocupado') {
                                    if ($mostrar2 != null) {#si el cajon esta ocupado lo que hace es mostrar la placa
                                        echo "Placa = " . h($mostrar2['placas']);
                                    }
                                    echo ("<br><img src='img/carro.png' width='100'/>");#se muestra la imagen de un auto
                                }
                                //<a href="editar.php?id=<?php echo $mostrar['id'] ? >" class="btn btn-outline-warning">Editar</a>
                                ?>

                            </td>
                        <?php
                        }
                        ?>
                    </tr>
                    <tr>
                        <?php #se hace un ciclo de repeticion de 8 registros
                        #se hace un ciclo de repeticion de 8 registros
                        for ($i = 9; $i <= 16; $i++) {
                            #se hace un select
                            $mostrar = db_one("SELECT * FROM cajon WHERE id = ?", "i", $i);
                            #se hace un select de los autos que aun no han salido
                            $mostrar2 = db_one("SELECT placas FROM resguardo WHERE id_cajon = ? AND pago = 0", "i", $i);
                            #dependiendo la situacion es el color de fondo que asignara?>
                            <td class="parking-space text-white <?php echo h($mostrar['situacion']); ?>">
                                <?php echo "Id Cajon = " . h($mostrar['id']) . "<br>";#imprime el cajon
                                echo "Situacion = " . h($mostrar['situacion']) . "<br>";#imprime la situacion
                                if ($mostrar['situacion'] == 'ocupado') {
                                    if ($mostrar2 != null) {#si el cajon esta ocupado lo que hace es mostrar la placa
                                        echo "Placa = " . h($mostrar2['placas']);
                                    }
                                    echo ("<br><img src='img/carro.png' width='100'/>");#se muestra la imagen de un auto
                                }
                                //<a href="editar.php?id=<?php echo $mostrar['id'] ? >" class="btn btn-outline-warning">Editar</a>
                                ?>

                            </td>
                        <?php
                        }
                        ?>
                    </tr>
                    <tr>
                        <?php #se hace un ciclo de repeticion de 8 registros
                        #se hace un ciclo de repeticion de 8 registros
                        for ($i = 17; $i <= 24; $i++) {
                            #se hace un select
                            $mostrar = db_one("SELECT * FROM cajon WHERE id = ?", "i", $i);
                            #se hace un select de los autos que aun no han salido
                            $mostrar2 = db_one("SELECT placas FROM resguardo WHERE id_cajon = ? AND pago = 0", "i", $i);
                            #dependiendo la situacion es el color de fondo que asignara?>
                            <td class="parking-space text-white <?php echo h($mostrar['situacion']); ?>">
                                <?php echo "Id Cajon = " . h($mostrar['id']) . "<br>";#imprime el cajon
                                echo "Situacion = " . h($mostrar['situacion']) . "<br>";#imprime la situacion
                                if ($mostrar['situacion'] == 'ocupado') {
                                    if ($mostrar2 != null) {#si el cajon esta ocupado lo que hace es mostrar la placa
                                        echo "Placa = " . h($mostrar2['placas']);
                                    }
                                    echo ("<br><img src='img/carro.png' width='100'/>");#se muestra la imagen de un auto
                                }
                                //<a href="editar.php?id=<?php echo $mostrar['id'] ? >" class="btn btn-outline-warning">Editar</a>
                                ?>

                            </td>
                        <?php
                        }
                        ?>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php
        }
        ?>
    </div>
    <!-- JavaScript Script-->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
</body>
<footer>
    <?php
    #se incluye el contador de visitas del sitio web
    include("contador/contador_visitas.php");
    ?>
</footer>

</html>
