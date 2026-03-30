<?php
include_once("../conexion.php");
if (isset($_GET['id'])) {
    echo $id = (int) $_GET['id'];
    mysqli_query($conexion, "DELETE FROM vehiculo WHERE id = '$id'");
    mysqli_close($conexion);
    header("Location: adminCon.php");
}
?>