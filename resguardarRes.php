<?php
//header('Cache-Control: no cache'); //no cache
//session_cache_limiter('private_no_expire'); // works
//session_cache_limiter('public'); // works too
#session_start();
include("conexion.php");
?>

<html lang="es">

<!-- cabecera de la pagina web -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- titulo de la pagina-->
    <title>Reservar Vehiculo</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/app.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>

    <nav class="navbar navbar-dark parking-navbar justify-content-between">
        <a class="navbar-brand text-white" href="?">Reservar Vehiculo</a>
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
                        <input type="hidden" name="placas" class="form-control" value="<?php echo h($_SESSION['placas'] ?? ''); ?>" required>
                        <div class="col-lg-6 col-sm-12 form-group">
                            <!-- imprime los cajones que estan disponibles -->
                            id cajon:<select class="custom-select" name="id_cajon">
                                <option value="" disabled="disabled">---Disponibles---</option>
                                <?php
                                $cajones = db_all("SELECT * FROM cajon WHERE situacion = 'disponible'");
                                foreach ($cajones as $valores) {
                                    echo '<option value="' . h($valores['id']) . '">' . 'Cajón Num. ' . h($valores['id']) . '</option>';
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
    $_SESSION['cajon'] = clean_cajon_id($_POST['id_cajon'] ?? 0);
    #se hace la actualizacion en la base de datos
    db_query("UPDATE cajon SET situacion = 'reservado' WHERE id = ?", "i", $_SESSION['cajon']);
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
    $placas = clean_plate($_POST['placas'] ?? '');
    #se recibe el id que manda el usuario, y se buscan los demas atributos
    $mostrar = db_one("SELECT * FROM vehiculo WHERE placas = ?", "s", $placas);
    #si se encontro algo se pasa a lo siguiente
    if ($mostrar != null) {
        #se asignan atributos
        $_SESSION['id'] = $mostrar['id'];
        $_SESSION['marca'] = $mostrar['marca'];
        $_SESSION['modelo'] = $mostrar['modelo'];
        $_SESSION['placas'] = $mostrar['placas'];
        $_SESSION['color'] = $mostrar['color'];
        $_SESSION['tamano'] = $mostrar['tamano'];
        $_SESSION['nombredue'] = $mostrar['nombredue'];
        #se imprime un mensaje
        echo ('<br><h3>La placa: ' . h($_SESSION['placas']) . ' tiene el id: ' . h($_SESSION['id']) . ' y pertenece a: ' . h($_SESSION['nombredue']) . '<h3>');
        echo ("<script>document.getElementById('placas').value = '" . h($_SESSION['placas']) . "'; document.getElementById('formulario1').style.display = 'block';</script>");
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
