<?php 
#se inicia la sesion
session_start();
#se vacian las variables
session_unset();
#se desrtuye la sesion
session_destroy();
#se regresa al login
header("location: login.php");
exit();