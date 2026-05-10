<?php
#se verifica que traig a algo el get
if (isset($_GET['id'])) {
    #se incluye la conexion
    include("conexion.php");
    require_permission("vehiculos.eliminar");
    verify_csrf($_GET["csrf_token"] ?? "");
    $id = (int) $_GET['id'];
    #se ejecuta la sentencia sql
    if (is_general_admin()) {
        db_query("DELETE FROM vehiculo WHERE id = ?", "i", $id);
    } else {
        db_query("DELETE FROM vehiculo WHERE id = ? AND cliente_id = ?", "ii", $id, current_client_id());
    }
    audit_log("vehiculos.eliminar", "vehiculo", $id, "Vehiculo eliminado");
    #se cierra la conexion
    mysqli_close($conexion);
    #se regresa a la pagina de administracion
    header("Location: adminCon.php");
}
