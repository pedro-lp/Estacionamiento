<?php
#se verifica que traig a algo el get
if (isset($_GET['id'])) {
    #se incluye la conexion
    include("conexion.php");
    $id = (int) $_GET['id'];
    #se ejecuta la sentencia sql
    db_query("DELETE FROM usuarios WHERE id = ?", "i", $id);
    #se cierra la conexion
    mysqli_close($conexion);
    #se regresa a la pagina de administracion
    header("Location: adminUsu.php");
}
