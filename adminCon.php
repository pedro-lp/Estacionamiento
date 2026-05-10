<?php
#inicia la sesion
#session_start();
include("conexion.php");
#asigna los datos de la sesion
$usuario = $_SESSION['usuario'] ?? null;
$rol = (int) ($_SESSION['rol'] ?? 0);
if (!isset($usuario)) {
    #si no tiene sesion iniciada se manda a login
    header("location: login.php");
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
    <title>Administrar Conductores</title>
    <nav class="navbar navbar-dark parking-navbar justify-content-between">
        <a class="navbar-brand text-white" href="?">Administrar Conductores</a>
    </nav>
</head>
<!-- cuerpo de la pagina-->
<body class="parking-app">

    <div class="container p-4">
        <!-- boton registrar -->
        <div class="col">
            <a class="btn btn-accent" href="regvehiculo.php">Registrar Vehiculo</a>
        </div><br><br>
        <?php
        #se incluye la conexion
        #se hace un select
        $result = db_result("SELECT * FROM vehiculo");
        #se imprime la tabla
        echo "        
        <table class='table'>
        <thead class='thead-dark'>
            <th scope='col' colspan='8'>Tabla General</th>
        </thead>
        <tbody>
            <th scope='col'>ID</th>
            <th scope='col'>Marca</th>
            <th scope='col'>Modelo</th>
            <th scope='col'>Placas</th>
            <th scope='col'>Color</th>
            <th scope='col'>Tamaño</th>
            <th scope='col'>Dueño</th>
            <th scope='col'>Opciones</th>";
            #se hace un while para obtener todos los datos
        while ($row = mysqli_fetch_array($result)) {
            $id = (int) $row[0];
            echo "<tr>
			<td>" . h($row[0]) . "</td>
            <td>" . h($row[1]) . "</td>
            <td>" . h($row[2]) . "</td>
            <td>" . h($row[3]) . "</td>
            <td>" . h($row[4]) . "</td>
            <td>" . h($row[5]) . "</td>
            <td>" . h($row[6]) . "</td>
            <td><a href='editarCon.php?id=$id' class='btn btn-outline-warning'>Editar</a>
            <a href='elimCon.php?id=$id' class='btn btn-outline-danger'>Remover</a></td>
        </tr>
        <tbody>";
        }
        #se cierra la tabla
        echo "</table>";
        ?>
        <!-- boton regresar -->
        <a class="btn btn-outline-secondary" href="index.php">Regresar</a>
    </div>

</body>

</html>
