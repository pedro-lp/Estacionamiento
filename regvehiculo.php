<?php
#header('Cache-Control: no cache'); //no cache
#session_cache_limiter('private_no_expire'); // works
//session_cache_limiter('public'); // works too
session_start();
#se incluiye la conexion
include("conexion.php");
?>
<html lang="es">

<!-- cabecera de la pagina web -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- titulo de la pagina-->
    <title>Registrar Vehiculo</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>


    <nav class="navbar navbar-light bg-info justify-content-between">
        <a class="navbar-brand text-white" href="?">Registrar Vehiculo</a>
    </nav>

</head>

<!-- cuerpo de la pagina-->
<body style="background: linear-gradient(180deg, white, #F2DDFF);">

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
            <!-- formulario de registro-->
            <form action="regvehiculo.php" method="POST">
                <div class="row">
                    <div class="col-lg-6 col-sm-12 form-group">
                        <!-- se obtiene el id a partir del ultimo registro que se tiene en la base de datos -->
                        ID:<input type="text" name="id" class="form-control" value="<?php
                                                                                    $result = mysqli_query($conexion, "SELECT * from vehiculo order by id desc limit 1");
                                                                                    $mostrar = mysqli_fetch_array($result);
                                                                                    echo ($mostrar['id'] + 1);
                                                                                    mysqli_close($conexion);
                                                                                    ?>" disabled>
                    </div>
                    <div class="col-lg-6 col-sm-12 form-group">
                        Nombre del Dueño:<input type="text" name="nombredue" class="form-control" required>
                    </div>
                    <div class="col-lg-6 col-sm-12 form-group">
                        Marca:<input type="text" name="marca" class="form-control" required>
                    </div>
                    <div class="col-lg-6 col-sm-12 form-group">
                        Modelo:<input type="text" name="modelo" class="form-control" required>
                    </div>
                    <div class="col-lg-6 col-sm-12 form-group">
                        Placas:<input type="text" name="placas" class="form-control" required>
                    </div>
                    <div class="col-lg-6 col-sm-12 form-group">
                        Color:<input type="text" name="color" class="form-control" required>
                    </div>

                    <div class="col-lg-6 col-sm-12 input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="tamano">Seleccionar tamano: </label>
                        </div>
                        <select name="tamano" id="tamano" class="custom-select">
                            <option value="Chico">Chico</option>
                            <option value="Grande">Grande</option>
                        </select>
                    </div>
                </div>
                <br><br>
                <div align="center">
                        <!-- boton enviar -->
                    <input type="submit" class="btn btn-success" name="agregar" value="Agregar Vehiculo">
                </div>
            </form>
        </div>
</body>

</html>
<?php
#se verifica que el metodo trae algo
if (isset($_POST['agregar'])) {
    
    #se asignan atributos
    $id = $_POST['id'];
    $marca = $_POST['marca'];
    $modelo = $_POST['modelo'];
    $placas = $_POST['placas'];
    $color = $_POST['color'];
    $tamano = $_POST['tamano'];
    $nombredue = $_POST['nombredue'];
    #se introducen los datos
    mysqli_query($conexion, "INSERT INTO vehiculo (marca, modelo, placas, color, tamano,nombredue) 
    VALUES ( '$marca','$modelo', '$placas', '$color', '$tamano', '$nombredue')");
    mysqli_close($conexion);

    #se dirige al index
    header("location:index.php");
    return;
}
?>