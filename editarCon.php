<?php
#inicia la sesion
#session_start();
#Comprobar si la variable está definida
if (isset($_POST['enviar'])) {
    #si no tiene sesion iniciada se manda a login
    include("conexion.php");
    #asigna el id a la variable convirtiendolo a Int
    $id = (int) $_POST['id'];
    $marca = clean_text($_POST['marca'] ?? '', 50);
    $modelo = clean_text($_POST['modelo'] ?? '', 50);
    $placas = clean_plate($_POST['placas'] ?? '');
    $color = clean_text($_POST['color'] ?? '', 30);
    $tamano = clean_tamano($_POST['tamano'] ?? 'Chico');
    $nombredue = clean_text($_POST['nombredue'] ?? '', 100);
    #se hace un update
    db_query(
        "UPDATE vehiculo SET marca = ?, modelo = ?, placas = ?, color = ?, tamano = ?, nombredue = ? WHERE id = ?",
        "ssssssi",
        $marca,
        $modelo,
        $placas,
        $color,
        $tamano,
        $nombredue,
        $id
    );
    #se cierra la conexion
    mysqli_close($conexion);
    #se manda a la pagina de administrar
    header("location: adminCon.php");
} else {
    include("conexion.php");
    #se recibe el id que manda el usuario, y se buscan los demas atributos
    $id = (int) $_REQUEST['id'];
    $row = db_one("SELECT * FROM vehiculo WHERE id = ?", "i", $id);
    if (!$row) {
        header("location: adminCon.php");
        exit();
    }
    #se asignan atributos
    $id = $row['id'];;
    $marca = $row['marca'];
    $modelo = $row['modelo'];
    $placas = $row['placas'];
    $color = $row['color'];
    $tamano = $row['tamano'];
    $nombredue = $row['nombredue'];
}
?>
<html lang="es">

<!-- cabecera de la pagina web -->
<head>
    <!-- importacion de bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/app.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- titulo de la pagina-->
    <title>Editar Conductor</title>
    <nav class="navbar navbar-dark parking-navbar justify-content-between">
        <a class="navbar-brand text-white" href="?">Editar Conductor</a>
    </nav>
</head>

<!-- cuerpo de la pagina-->
<body class="parking-app">
    <div class="container p-4">
        <center>
            <h2>Editar Conductor</h2>
            <!-- imagen -->
            <div>
                <img src="img/logo.png" width="100" class="d-inline-block align-top" alt="" loading="lazy">
            </div> <br>
            <div class="card parking-card col-lg-6 col-md-8 mx-auto">
                <div class="card-body p-4">
                <!-- formulario que contiene los datos que previamente se sacaron de la base de datos -->
                <form action="editarCon.php" method="POST">
                    <div class="form-group">
                        <input type="hidden" name="id" id="id" value="<?php echo $id; ?>">
                        <label for="usuario">Marca</label><br>
                        <input class="form-control" type="text" name="marca" id="marca" value="<?php echo h($marca); ?>" placeholder="Marca del Automovil" required>
                    </div>
                    <div class="form-group">
                        <label for="usuario">Modelo</label><br>
                        <input class="form-control" type="text" name="modelo" id="modelo" value="<?php echo h($modelo); ?>" placeholder="Modelo del Automovil" required>
                    </div>
                    <div class="form-group">
                        <label for="usuario">Placas</label><br>
                        <input class="form-control" type="text" name="placas" id="placas" value="<?php echo h($placas); ?>" placeholder="Placas del Automovil" required>
                    </div>
                    <div class="form-group">
                        <label for="usuario">Color</label><br>
                        <input class="form-control" type="text" name="color" id="color" value="<?php echo h($color); ?>" placeholder="Color del Automovil" required>
                    </div>
                    <!-- segun el tamaño que trae la base de datos es el que se selecciona -->
                    <div class="form-group">
                        <label for="usuario">Tamaño</label><br>
                        <select class="form-control" name="tamano" id="tamano" required>
                            <option value="Chico" <?php if ($tamano =="Chico") {
                                                        echo "selected";
                                                    } ?>>Chico</option>
                            <option value="Grande" <?php if ($tamano =="Grande") {
                                                        echo "selected";
                                                    } ?>>Grande</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="usuario">Nombre del dueño</label><br>
                        <input class="form-control" type="text" name="nombredue" id="nombredue" value="<?php echo h($nombredue); ?>" placeholder="Dueño del Automovil" required>
                    </div>
                    <!-- boton regresar -->
                    <div class="form-group">
                        <a class="btn btn-outline-secondary" href="adminCon.php">Regresar</a>
                        <!-- boton enviar -->
                        <button class="btn btn-parking" name="enviar" id="enviar" type="submit">Editar Conductor</button>
                    </div>
                </form>
                </div>
            </div>
        </center>
    </div>
</body>

</html>
