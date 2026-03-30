<?php
$server = "localhost";
$user = "root";
$pass = "";
$db = "ejemploselect";
$puerto = "33069";

$conexion = new mysqli($server, $user, $pass, $db, $puerto);

if ($conexion->connect_errno) {
	die("La conexion ha fallado" . $conexion->connect_errorno);
} else {
	//echo "se conecto correctamente a la BD";
}
