<?php 
#se incluye la conexion
include("conexion.php");
#se inicia la sesion
#session_start();
#se captura el post
$usuario =$_POST['usuario'];
$clave =$_POST['clave'];
#se hace un select para verificar que existe
$result = mysqli_query($conexion, "SELECT * from usuarios where Usuario='$usuario' and password='$clave'");
$mostrar = mysqli_fetch_array($result);
if ($mostrar != null) {
    #si el usuario existe se asigna a la sesion y se manda al index
    $_SESSION['usuario'] = $mostrar['Usuario'];
    $_SESSION['rol'] = $mostrar['rol_id'];
    header("location: index.php");
}else{
    #si los datos son incorrectos se manda una alerta
    echo "<script>alert('LOS DATOS SON INCORRECTOS');history.back();</script>";    
}
