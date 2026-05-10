<?php 
#se incluye la conexion
include("conexion.php");
#se captura el post
$usuario = clean_text($_POST['usuario'] ?? '', 100);
$clave = (string) ($_POST['clave'] ?? '');
#se hace un select para verificar que existe
$mostrar = authenticate_user($usuario, $clave);
if ($mostrar != null) {
    #si el usuario existe se asigna a la sesion y se manda al index
    $_SESSION['usuario'] = $mostrar['Usuario'];
    $_SESSION['rol'] = $mostrar['rol_id'];
    header("location: index.php");
}else{
    #si los datos son incorrectos se manda una alerta
    echo "<script>alert('LOS DATOS SON INCORRECTOS');history.back();</script>";    
}
