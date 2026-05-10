<?php 
#se inicia la sesion
include("conexion.php");
audit_log("logout", "sesion", current_user_id(), "Cierre de sesion");
#se vacian las variables
session_unset();
#se desrtuye la sesion
session_destroy();
#se regresa al login
header("location: login.php");
exit();
