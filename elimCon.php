<?php
#se verifica que traig a algo el get
if (isset($_GET['id'])) {
    #se incluye la conexion
    include("conexion.php");
    echo $id = (int) $_GET['id'];
    #se ejecuta la sentencia sql
    mysqli_query($conexion, "DELETE FROM vehiculo WHERE id = '$id'");
    #se cierra la conexion
    mysqli_close($conexion);
    #se regresa a la pagina de administracion
    header("Location: adminCon.php");
}
