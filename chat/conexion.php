<?php

$server = "localhost";

$user = "id15852223_pedro";

$pass = "%Tempa710487cb0%";

$db = "id15852223_estacionamiento";

$puerto = "3306";



$conexion = new mysqli($server, $user, $pass, $db);



if ($conexion->connect_errno) {

	die("La conexion ha fallado" . $conexion->connect_errorno);

} else {

	//echo "se conecto correctamente a la BD";

}

