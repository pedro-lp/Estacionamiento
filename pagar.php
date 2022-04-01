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
            <div id="formulario1">
                <!-- formulario que verifica que exista el registro primeramente -->
                <form action="pagar.php" method="POST">
                    <div class="form-group w-50">
                        placas:<input type="text" name="placas" id="placas" class="form-control" required>
                    </div>
                    <a class="btn btn-warning" href="index.php">regresar</a>
                    <input type="submit" class="btn btn-success" name="verificar" value="Verificar registro">
                </form>
            </div>
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
        #se hace una consulta de los autos que aun no han pagado
        $result2 = mysqli_query($conexion, "SELECT * from resguardo where placas='$placas' and pago='0'");
        $mostrar2 = mysqli_fetch_array($result2);
        if ($mostrar2 != null) {
            #se asignan atributos
            $_SESSION['idresguardo'] = $mostrar2[0];
            $_SESSION['cajon'] = $mostrar2[2];
            $_SESSION['horaEntrada'] = $mostrar2[3];
            $_SESSION['foto'] = $mostrar2[7];
            $_SESSION['lavado'] = $mostrar2[8];
            #se imprime un mensaje
            echo ('<br><h3>La placa: ' . $_SESSION['placas'] . ' y pertenece a: ' . $_SESSION['nombredue'] . '<h3>');
            echo "<script> window.location='pagaradd.php'; </script>";
        } else {
            #se imprime un mensaje
            echo ('<br><h3>No se encontro el resguardo<h3>');
        }
    } else {
        #se imprime un mensaje
        echo ('<br><h3>No se encontro la placa<h3>');
    }
    mysqli_close($conexion);
}
?>