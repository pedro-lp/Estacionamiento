<?php
#se crean las variables de la conexion
$server = "localhost";
$user = "root";
$pass = "";
$db = "estacionamiento";
$puerto = "3306";
#se hace la conexion
$conexion = new mysqli($server, $user, $pass, $db, $puerto);

if ($conexion->connect_errno) {
	die("La conexion ha fallado" . $conexion->connect_errorno);
} else {
	echo "se conecto correctamente a la BD ";
}
