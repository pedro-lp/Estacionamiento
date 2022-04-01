<?php
#header('Cache-Control: no cache'); //no cache
#session_cache_limiter('private_no_expire'); // workssession_start();
session_start();
#se incluye la conexion
include("conexion.php");
#se asignan variables
$id = (int) $_REQUEST['id'];
$_SESSION['id'] = $id;
#se hace un select
$result = mysqli_query($conexion, "SELECT * from cajon WHERE id = '$id'");
$mostrar = mysqli_fetch_array($result);
#verifica que no este vacio
if ($mostrar != null) {
    #se asignan variables
    $area = $mostrar['area'];
    $situacion = $mostrar['situacion'];
    $_SESSION['area'] = $area;
    $_SESSION['situacion'] = $situacion;
} else {
    #imprime mensaje de error
    echo ("no se encontro el id");
}
#se cierra la conexion
mysqli_close($conexion);
?>

<html lang="es">
<!-- cabecera de la paguina web -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Vehiculo</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <nav class="navbar navbar-light bg-info justify-content-between">
        <a class="navbar-brand text-white" href="?">Registrar Vehiculo</a>
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
            <!-- Formulario de la pagina que debe ser rellenado con los nuevos datos-->
            <form action="index.php" method="POST">
                <div class="form-group">
                    ID:<input type="text" name="id" value="<?php echo $_SESSION['id']; ?>" class="form-control" disabled>
                </div>
                <!-- select de area -->
                <div class="form-group">
                    <label class="input-group-text" for="area">Area: </label>
                    <select name="area" id="area" class="custom-select">
                        <option value="<?php echo chop($area); ?>"><?php echo chop($area); ?></option>
                        <?php if (chop($area) == "10m2") { ?>
                            <option value="15m2">15m2</option>
                        <?php  } else if (chop($area) == "15m2") { ?>
                            <option value="10m2">10m2</option>
                        <?php  } ?>
                    </select>
                </div>
                <!-- select de situacion -->
                <div class="form-group">
                    <label class="input-group-text" for="situacion">Situacion: </label>
                    <select name="situacion" id="situacion" class="custom-select">
                        <option value="<?php echo chop($situacion); ?>"><?php echo chop($situacion); ?></option>
                        <?php if (chop($situacion) == "ocupado") { ?>
                            <option value="disponible">disponible</option>
                        <?php  } else if (chop($situacion) == "disponible") { ?>
                            <option value="ocupado">ocupado</option>
                        <?php  } ?>
                    </select>
                </div>
                <!-- boton enviar -->
                <input type="submit" class="btn btn-success" name="add" value="Agregar Vehiculo">
                <!-- boton regresar -->
                <a class="btn btn-warning" href="index.php">regresar</a>
            </form>
        </div>
    </div>
</body>

</html>
<?php

?>