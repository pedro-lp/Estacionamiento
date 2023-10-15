<?php
#se crean las variables de la conexion
$server = "localhost";
$user = "id19246745_root";
$pass = "D7!kiB!Suv";
$db = "id19246745_estacionamiento";
$puerto = "3306";
#se hace la conexion
$conexion = new mysqli($server, $user, $pass, $db, $puerto);

if ($conexion->connect_errno) {
	die("La conexion ha fallado" . $conexion->connect_errno);
}