<?php
require_once("../conexion.php");

$usuario = clean_text($_POST['usuario'] ?? '', 100);
$clave = (string) ($_POST['clave'] ?? '');
$mostrar = authenticate_user($usuario, $clave);

if ($mostrar !== null) {
    $_SESSION['usuario'] = $mostrar['Usuario'];
    $_SESSION['rol'] = $mostrar['rol_id'];
    header("Location: ../index.php");
    exit();
}

echo "<script>alert('LOS DATOS SON INCORRECTOS');history.back();</script>";
?>
