<?php
//header('Cache-Control: no cache'); //no cache
//session_cache_limiter('private_no_expire'); // works
//session_cache_limiter('public'); // works too
#session_start(); ?>

<html lang="es">

<!-- cabecera de la pagina web -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- titulo de la pagina-->
    <title>Reservar Vehiculo</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>

    <nav class="navbar navbar-light bg-info justify-content-between">
        <a class="navbar-brand text-white" href="?">Reservar Vehiculo</a>
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
            <form action="resguardarRes.php" method="POST">
                <div class="form-group w-50">
                    placas:<input type="text" name="placas" id="placas" class="form-control" required>
                </div>
                <input type="submit" class="btn btn-info" name="verificar" value="Verificar registro">
            </form>
            <!-- formulario de registro -->
            <div id="formulario1" style="display:none">
                <form action="resguardarRes.php" method="POST" enctype="multipart/form-data">
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
                            </select>
                        </div>
                        <div class="col-lg-6 col-sm-12 form-group">
                            fecha:<input type="text" name="fecha" id="fecha" class="form-control" value="<?php date_default_timezone_set('America/Monterrey');
                                                                                                            echo date("d-m-Y"); ?>" readonly>
                        </div>
                    </div>
                    <br><br>
                    <!-- boton regresar -->
                    <a class="btn btn-warning" href="index.php">regresar</a>
                    <!-- boton enviar -->
                    <input type="submit" class="btn btn-success" name="agregar" value="Reservar Espacio">
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
    #se hace la actualizacion en la base de datos
    mysqli_query($conexion, "UPDATE cajon SET situacion='reservado' WHERE id='" . $_SESSION['cajon'] . "'");
    mysqli_close($conexion);
    #se abre una nueva ventana para poder generar el pdf
    echo "<script> window.open('reservarpdf.php', '_blank'); </script>";
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