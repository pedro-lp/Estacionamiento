<?php 
include("conexion.php");
session_start();
$usuario =$_POST['usuario'];
$clave =$_POST['clave'];
$result = mysqli_query($conexion, "SELECT * from usuarios where username='$usuario' and password='$clave'");
$mostrar = mysqli_fetch_array($result);
if ($mostrar != null) {
    $_SESSION['usuario'] = $mostrar['username'];
    $_SESSION['rol'] = $mostrar['rol_id'];
    header("location: index.php");
}else{
    echo "<script>alert('LOS DATOS SON INCORRECTOS');history.back();</script>";    
}
?>