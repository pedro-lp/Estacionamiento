<?php
#se verifica que traig a algo el get
if (isset($_GET['id'])) {
    #se incluye la conexion
    include("conexion.php");
    require_permission("usuarios.eliminar");
    verify_csrf($_GET["csrf_token"] ?? "");
    $id = (int) $_GET['id'];
    #se ejecuta la sentencia sql
    if (is_general_admin()) {
        db_query("DELETE FROM usuarios WHERE id = ?", "i", $id);
    } else {
        db_query("DELETE FROM usuarios WHERE id = ? AND cliente_id = ?", "ii", $id, current_client_id());
    }
    audit_log("usuarios.eliminar", "usuarios", $id, "Usuario eliminado");
    #se cierra la conexion
    mysqli_close($conexion);
    #se regresa a la pagina de administracion
    header("Location: adminUsu.php");
}
