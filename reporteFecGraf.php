<?php
//header('Cache-Control: no cache'); //no cache
//session_cache_limiter('private_no_expire'); // works
//session_cache_limiter('public'); // works too
session_start();
?>

<html lang="es">

<!-- cabecera de la pagina web -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- titulo de la pagina-->
    <title>Administrar Automoviles</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>

    <nav class="navbar navbar-light bg-info justify-content-between">
        <a class="navbar-brand text-white" href="?">Gestionar Estacionamiento</a>
    </nav>
</head>

<!-- cuerpo de la pagina-->
<body style="background: linear-gradient(180deg, white, #CEFCC6);">
    <!-- formulario -->
    <form action="reporteFecGraf.php" method="POST">
        <div align="center">
            <h3>historial de ocupación por cajón</h3>
            <div class="w-50">
                <div class="col-lg-6 col-sm-12 form-group">
                    Primer dia:<input type="datetime-local" name="fecInicio" id="fecInicio" class="form-control">
                </div>
                <div class="col-lg-6 col-sm-12 form-group">
                    Ultimo dia:<input type="datetime-local" name="fecFin" id="fecFin" class="form-control">
                </div>
                <!-- boton regresar -->
                <a class="btn btn-warning" href="index.php">regresar</a>
                <!-- boton enviar -->
                <input type="submit" class="btn btn-success" name="agregar" value="Consultar">
            </div>
        </div>
    </form>
</body>

</html>
<?php
#verifica si el metodo post trae algo
if (isset($_POST['agregar'])) {
    #se asignan las variables
    $_SESSION['fecInicio'] = $_POST['fecInicio'];
    $_SESSION['fecFin'] = $_POST['fecFin'];
    #se manda a la pagina de graficas
    header("location:graficas.php");
    //echo "<script> window.location='index.php'; </script>";
}
