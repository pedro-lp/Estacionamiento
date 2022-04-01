<?php
session_start();
$usuario = $_SESSION['usuario'];
$rol = (int) $_SESSION['rol'];
if (!isset($usuario)) {
    //si no tiene sesion iniciada se manda a login
    header("location: login.php");
} else {
    //si no tiene permiso se le pide que acceda con otro usuario
    if ($rol != 1) {
        echo ("<div align='center'><a href='login.php'><h4>No tienes permiso para acceder a esta seccion</h4></a><br></div>");
    }
}

?>
<html lang="es">

<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Conductores</title>
    <nav class="navbar navbar-light bg-info justify-content-between">
        <a class="navbar-brand text-white" href="?">Administrar Conductores</a>
    </nav>
</head>

<body style="background: linear-gradient(180deg, #C6FCFC, #A7FC97);">

    <div class="container p-4">
        <?php
        include("conexion.php");
        $result = mysqli_query($conexion, "SELECT * from vehiculo");
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
        while ($row = mysqli_fetch_array($result)) {
            $id = (int) $row[0];
            echo "<tr>
			<td>" . $row[0] . "</td>
            <td>" . $row[1] . "</td>
            <td>" . $row[2] . "</td>
            <td>" . $row[3] . "</td>
            <td>" . $row[4] . "</td>
            <td>" . $row[5] . "</td>
            <td>" . $row[6] . "</td>
            <td><a href='editarCon.php?id=$id' class='btn btn-outline-warning'>Editar</a>
            <a href='elimCon.php?id=$id' class='btn btn-outline-danger'>Remover</a></td>
        </tr>
        <tbody>";
        }
        echo "</table>";
        ?>
        <a class="btn btn-danger" href="index.php">
            <h6>Regresar</h6>
        </a>
    </div>

</body>

</html>