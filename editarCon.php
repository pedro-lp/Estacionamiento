<?php
#inicia la sesion
#session_start();
#Comprobar si la variable está definida
if (isset($_POST['enviar'])) {
    #si no tiene sesion iniciada se manda a login
    include("conexion.php");
    #asigna el id a la variable convirtiendolo a Int
    $id = (int) $_POST['id'];
    #se hace un update
    mysqli_query($conexion, "UPDATE vehiculo SET marca='" . $_POST['marca'] . "', modelo='" . $_POST['modelo'] . "', placas='" . $_POST['placas'] . "', color='" . $_POST['color'] . "', tamaño='" . $_POST['tamaño'] . "', nombredue='" . $_POST['nombredue'] . "' WHERE id='$id'");
    #se cierra la conexion
    mysqli_close($conexion);
    #se manda a la pagina de administrar
    header("location: adminCon.php");
} else {
    include("conexion.php");
    #se recibe el id que manda el usuario, y se buscan los demas atributos
    $id = (int) $_REQUEST['id'];
    $result = mysqli_query($conexion, "SELECT * from vehiculo where id='$id'");
    $row = mysqli_fetch_array($result);
    #se asignan atributos
    $id = $row[0];;
    $marca = $row[1];
    $modelo = $row[2];
    $placas = $row[3];
    $color = $row[4];
    $tamaño = $row[5];
    $nombredue = $row[6];
}
?>
<html lang="es">

<!-- cabecera de la pagina web -->
<head>
    <!-- importacion de bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- titulo de la pagina-->
    <title>Editar Conductor</title>
    <nav class="navbar navbar-light bg-info justify-content-between">
        <a class="navbar-brand text-white" href="?">Editar Conductor</a>
    </nav>
</head>

<!-- cuerpo de la pagina-->
<body style="background: linear-gradient(180deg, #C6FCFC, #A7FC97);">
    <div class="container p-4">
        <center>
            <h2>Editar Conductor</h2>
            <!-- imagen -->
            <div>
                <img src="img/logo.png" width="100" class="d-inline-block align-top" alt="" loading="lazy">
            </div> <br>
            <div class="p-3 mb-2 bg-secondary text-white w-50">
                <!-- formulario que contiene los datos que previamente se sacaron de la base de datos -->
                <form action="editarCon.php" method="POST">
                    <div class="form-group">
                        <input type="hidden" name="id" id="id" value="<?php echo $id; ?>">
                        <label for="usuario">Marca</label><br>
                        <input class="form-control" type="text" name="marca" id="marca" value="<?php echo $marca; ?>" placeholder="Marca del Automovil" required>
                    </div>
                    <div class="form-group">
                        <label for="usuario">Modelo</label><br>
                        <input class="form-control" type="text" name="modelo" id="modelo" value="<?php echo $modelo; ?>" placeholder="Modelo del Automovil" required>
                    </div>
                    <div class="form-group">
                        <label for="usuario">Placas</label><br>
                        <input class="form-control" type="text" name="placas" id="placas" value="<?php echo $placas; ?>" placeholder="Placas del Automovil" required>
                    </div>
                    <div class="form-group">
                        <label for="usuario">Color</label><br>
                        <input class="form-control" type="text" name="color" id="color" value="<?php echo $color; ?>" placeholder="Color del Automovil" required>
                    </div>
                    <!-- segun el tamaño que trae la base de datos es el que se selecciona -->
                    <div class="form-group">
                        <label for="usuario">Tamaño</label><br>
                        <select class="form-control" name="tamaño" id="tamaño" required>
                            <option value="Chico" <?php if ($tamaño == "Chico") {
                                                        echo "selected";
                                                    } ?>>Chico</option>
                            <option value="Grande" <?php if ($tamaño == "Grande") {
                                                        echo "selected";
                                                    } ?>>Grande</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="usuario">Nombre del dueño</label><br>
                        <input class="form-control" type="text" name="nombredue" id="nombredue" value="<?php echo $nombredue; ?>" placeholder="Dueño del Automovil" required>
                    </div>
                    <!-- boton regresar -->
                    <div class="form-group">
                        <a class="navbar-brand text-white" href="adminUsu.php">
                            <h6>Regresar</h6>
                        </a>
                        <!-- boton enviar -->
                        <button class="btn btn-primary" name="enviar" id="enviar" type="submit">Editar Conductor</button>
                    </div>
                </form>
            </div>
        </center>
    </div>
</body>

</html>