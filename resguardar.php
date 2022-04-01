<?php
//header('Cache-Control: no cache'); //no cache
//session_cache_limiter('private_no_expire'); // works
//session_cache_limiter('public'); // works too
session_start(); ?>

<html lang="es">

<!-- cabecera de la pagina web -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- titulo de la pagina-->
    <title>Resguardar Vehiculo</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>

    <nav class="navbar navbar-light bg-info justify-content-between">
        <a class="navbar-brand text-white" href="?">Resguardar Vehiculo</a>
        <!-- Search form
        <form class="form-inline d-flex justify-content-center md-form form-sm" action="index.php" method="POST">
            <input class="form-control form-control-sm mr-3 w-75" type="text" placeholder="buscar ID" name="buscar" id="buscar" aria-label="Search">
            <i class="fas fa-search" aria-hidden="true"></i>
        </form> -->
    </nav>

</head>

<!-- cuerpo de la pagina-->
<body style="background: linear-gradient(180deg, white, #FFF3B0);">

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
            <?php session_unset();
            } ?>

            <!-- formulario que verifica la existencia de la placa -->
            <form action="resguardar.php" method="POST">
                <div class="form-group w-50">
                    placas:<input type="text" name="placas" id="placas" class="form-control" required>
                </div>
                <a class="btn btn-warning" href="index.php">regresar</a>
                <input type="submit" class="btn btn-info" name="verificar" value="Verificar registro">
            </form>
            <!-- formulario de registro -->
            <div id="formulario1" style="display:none">
                <form action="resguardar.php" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <input type="hidden" name="placas" class="form-control" value="<?php echo ($_SESSION['placas']); ?>" required>
                        <div class="col-lg-6 col-sm-12 form-group">
                            <!-- imprime los cajones que estan disponibles -->
                            id cajon:<select class="custom-select" name="id_cajon">
                                <option value="" disabled="disabled">---Disponibles---</option>
                                <?php
                                include("conexion.php");
                                $result = mysqli_query($conexion, "SELECT * from cajon WHERE situacion = 'disponible'");
                                while ($valores = mysqli_fetch_array($result)) {
                                    echo '<option value="' . $valores[0] . '">' . 'Cajón Num. ' . $valores[0] . '</option>';
                                } ?>
                                <option value="" disabled="disabled">---Reservados---</option>
                                <?php $result = mysqli_query($conexion, "SELECT * from cajon WHERE situacion = 'reservado'");
                                while ($valores = mysqli_fetch_array($result)) {
                                    echo '<option value="' . $valores[0] . '">' . 'Cajón Num. ' . $valores[0] . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-lg-6 col-sm-12 form-group">
                            fecha:<input type="text" name="fecha" id="fecha" class="form-control" value="<?php date_default_timezone_set('America/Monterrey');
                                                                                                            echo date("d-m-Y"); ?>" readonly>
                        </div>
                        <div class="col-lg-6 col-sm-12 form-group">
                            hora llegada:<input type="text" name="hora_llegada" id="hora_llegada" class="form-control" value="<?php echo date("H:i:s"); ?>" readonly>
                        </div>
                        <div class="col-lg-6 col-sm-12 form-group">
                            Servicio de lavado de auto
                            <select class="form-control" name="lavado" id="lavado">
                                <option value="0">No</option>
                                <option value="1">Si</option>
                            </select>
                        </div>
                        <!-- se hace un div de tipo columna -->
                        <div class="col-lg-4 col-sm-12 form-group"><br>
                            selecciona una foto:<input type="file" name="foto" id="foto" class="custom-file" accept="image/png, .jpeg, .jpg, image/gif">
                        </div>
                    </div>
                    <br><br>
                    <!-- boton enviar -->
                    <input type="submit" class="btn btn-success" name="agregar" value="Resguardar Vehiculo">
                </form>
            </div>
        </div>
</body>
<?php
#muestra la hora actual
date_default_timezone_set('America/Monterrey');
echo date('h:i A');
?>

</html>
<?php
#verifica si el metodo post trae algo
if (isset($_POST['agregar'])) {
    #se incluiye la conexion
    include("conexion.php");
    $_SESSION['cajon'] = $_POST['id_cajon'];
    $_SESSION['horaEntrada'] = $_POST['hora_llegada'];
    echo $_POST['lavado'];
    $foto = $_FILES['foto']['tmp_name'];
    #se copia la fotografia 
    $destino = "img/" . $_FILES['foto']['name'];
    #se verifica  si se movio la foto
    if (!move_uploaded_file($foto, $destino)) {
        $destino = "";
    }

    #se hace la actualizacion en la base de datos
    mysqli_query($conexion, "UPDATE cajon SET situacion='ocupado' WHERE id='" . $_SESSION['cajon'] . "'");
    mysqli_query($conexion, "INSERT INTO resguardo (placas, id_cajon, hora_llegada, fecha, lavado, foto) 
    VALUES ('" . $_SESSION['placas'] . "', '" . $_SESSION['cajon'] . "', '" . $_SESSION['horaEntrada'] . "', now(),'" . $_POST['lavado'] . "','" . $destino . "')");
    mysqli_close($conexion);
    echo "<script> window.open('pdfresguardo.php', '_blank'); </script>";
    //header("location:pdfresguardo.php");
    //header("Location:index.php");
    #se dirige al index
    echo "<script> window.location='index.php'; </script>";
}
#se verifica que el metodo trae algo
if (isset($_POST['verificar'])) {
    include("conexion.php");
    $placas = $_POST['placas'];
    #se recibe el id que manda el usuario, y se buscan los demas atributos
    $result = mysqli_query($conexion, "SELECT * from vehiculo where placas='$placas'");
    $mostrar = mysqli_fetch_array($result);
    #si se encontro algo se pasa a lo siguiente
    if ($mostrar != null) {
        #se asignan atributos
        $_SESSION['id'] = $mostrar['id'];
        $_SESSION['marca'] = $mostrar['marca'];
        $_SESSION['modelo'] = $mostrar['modelo'];
        $_SESSION['placas'] = $mostrar['placas'];
        $_SESSION['color'] = $mostrar['color'];
        $_SESSION['tamaño'] = $mostrar['tamaño'];
        $_SESSION['nombredue'] = $mostrar['nombredue'];
            #se imprime un mensaje
        echo ('<br><h3>La placa: ' . $_SESSION['placas'] . ' tiene el id: ' . $_SESSION['id'] . ' y pertenece a: ' . $_SESSION['nombredue'] . '<h3>');
        echo ("<script>document.getElementById('placas').value = '" . $_SESSION['placas'] . "'; document.getElementById('formulario1').style.display = 'block';</script>");
    } else {
            #se imprime un mensaje
        echo ('<br><h3>No se encontro el Id<h3> <a class="btn btn-warning" href="regvehiculo.php">Registrar Vehiculo</a>');
    }


    //mysqli_close($conexion);
    //<div class="col-lg-6 col-sm-12 form-group">
    //hora salida:<input type="time" name="hora_salida" class="form-control" required>
    //</div>

    /*hora llegada:<input type="text" name="hora_llegada" class="form-control" value="<?php date_default_timezone_set('America/Monterrey'); echo date('h:i:sA'); ?>" disabled>*/
}
?>