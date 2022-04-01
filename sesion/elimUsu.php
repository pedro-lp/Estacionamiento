<?php
if (isset($_GET['id'])) {
    include("conexion.php");
    echo $id = (int) $_GET['id'];
    mysqli_query($conexion, "DELETE FROM usuarios WHERE id = '$id'");
    mysqli_close($conexion);
    header("Location: adminUsu.php");
}
?>