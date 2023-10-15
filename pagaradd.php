<?php
#session_start(); ?>

<html lang="es">

<!-- cabecera de la pagina web -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- titulo de la pagina-->
    <title>Sacar Vehiculo</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>

    <nav class="navbar navbar-light bg-info justify-content-between">
        <a class="navbar-brand text-white" href="?">Sacar Vehiculo</a>
    </nav>
</head>

<!-- cuerpo de la pagina-->
<body style="background: linear-gradient(180deg, white, #DDECFF);">

    <div class="container p-4">
            <!-- div donde esta el formulario que al inicio no es visible -->
            <div id="formulario2">
            <!-- formulario que verifica que exista el registro primeramente -->
                <form action="pagaradd.php" method="POST">
                    <div class="row">
                        <div class="col-lg-6 col-sm-12 form-group">
                            placas:<input type="text" name="placas" id="placas" class="form-control" value="<?php echo $_SESSION['placas'];?>" readonly>
                        </div>
                        <div class="col-lg-6 col-sm-12 form-group">
                            id cajon:
                            <?php
                            if ($_SESSION['cajon'] != null) {
                                #se carga el cajon donde se encuentra
                                echo '<input type="text" name="id_cajon" id="id_cajon" class="form-control" value="' . $_SESSION['cajon'] . '" readonly>';
                            }
                            ?>
                        </div>
                        <div class="col-lg-6 col-sm-12 form-group">
                            fecha:<input type="text" name="fecha" class="form-control" value="<?php date_default_timezone_set('America/Monterrey');
                                                                                                echo date("d-m-Y"); ?>" readonly>
                        </div>
                        <div class="col-lg-6 col-sm-12 form-group">
                            hora llegada:<input type="text" name="hora_llegada" id="hora_llegada" class="form-control" value="<?php echo $_SESSION['horaEntrada']; ?>" readonly>
                        </div>
                        <div class="col-lg-6 col-sm-12 form-group">
                            hora salida:<input type="text" name="hora_salida" id="hora_salida" class="form-control" value="<?php echo date("H:i:s"); ?>" readonly>
                        </div>
                        <div class="col-lg-6 col-sm-12 form-group">
                            <?php if ($_SESSION['lavado'] == 0) {   ?>
                                <!-- se muestra si pago por el lavado del auto -->
                                ¿Requiere lavado de auto?:<input type="text" name="lavado" id="lavado" class="form-control" value="No" readonly>
                            <?php
                            }else{?>
                                ¿Requiere lavado de auto?:<input type="text" name="lavado" id="lavado" class="form-control" value="Si" readonly>
                            <?php
                            } ?>                            
                        </div>
                        <div class="col-lg-6 col-sm-12 form-group">
                            <?php if ($_SESSION['foto'] != null) {   ?>
                                <!-- se muestra la fotografia -->
                                <label class="form-control" >Foto del Vehiculo:</label>
                                 <img src="<?php echo $_SESSION['foto'] ?>" width="300" class="d-inline-block align-top" alt="" loading="lazy">
                            <?php
                            } ?>
                        </div>                        
                    </div>
                    <br><br>
                        <!-- boton enviar -->
                    <input type="submit" class="btn btn-success" name="agregar" value="Pagar Vehiculo">                    
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
    $_SESSION['hora_salida'] = $_POST['hora_salida'];
    #se hace la resta de horas que se ha estado en el estacionamiento
    $diff = StrToTime($_SESSION['hora_salida']) - StrToTime($_SESSION['horaEntrada']);
    $tiempo = ceil($diff / (60 * 60));
    #se pregunta si se lavo el auto o no
    if ($_SESSION['lavado'] == 0) {
        $pago = 0;
    } else {
        #se cobra el lavado del auto
        $pago = 30;
    } 
    #se verifica el tamaño del auto y hace una suma   
    if ($_SESSION['tamaño'] == 'Chico') {
        $pago = $pago + $tiempo * 12;
    } else {
        $pago = $pago + $tiempo * 18;
    }
    $_SESSION['pago'] = $pago;
    #se hace las actualizaciones correspondientes
    mysqli_query($conexion, "UPDATE cajon SET situacion='disponible' WHERE id='" . $_SESSION['cajon'] . "'");
    mysqli_query($conexion, "UPDATE resguardo SET hora_salida='" . $_SESSION['hora_salida'] . "', pago='$pago' WHERE id='" . $_SESSION['idresguardo'] . "'");
    mysqli_close($conexion);
    #se abre una nueva ventana para poer generar el pdf
    echo "<script> window.open('generarpdf.php', '_blank'); </script>";
    foreach ($_SESSION as $valor) {
        echo $valor . ',';
    }
    #se dirige al index
    echo "<script> window.location='index.php'; </script>";
}
?>