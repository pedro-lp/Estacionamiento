<?php

$server = "localhost";

$user = "";

$pass = "";

$db = "estacionamiento";

$puerto = "3306";



$conexion = new mysqli($server, $user, $pass, $db);



if ($conexion->connect_errno) {

	die("entro al if de la conexion" . $conexion->connect_errorno);

} else {

	echo "else de conexion";

}

