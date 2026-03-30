<?php
//header('Cache-Control: no cache'); //no cache
//session_cache_limiter('private_no_expire');
#session_start();
include("conexion.php");
/*while ($mostrar = mysqli_fetch_array($result)) {
    $mostrar['pago'];
}*/
//mysqli_close($conexion);
?>
<html lang="es">

<head>
    <!-- titulo de la pagina-->
    <title>Ocupacion por cajón</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
</head>

<!-- cuerpo de la pagina-->
<body style="background: linear-gradient(180deg, white, #FFE8CA);">
    <div align="center">
        <h1>Ocupación por cajón</h1><br>
        <h4>Fecha Inicial: <?php echo $_SESSION['fecInicio'] ?><br>Fecha Final: <?php echo $_SESSION['fecFin'] ?></h4><br>
    </div>
    <!-- se crea el canvas donde ira la grafica-->
    <canvas id="myChart"></canvas>
    <br>
    <div align="center">
    <!-- se crea la etiqueta donde va el mensaje-->
        <h3 name="mensaje" id="mensaje"></h3>
    </div>
</body>
<!-- se comienza a realizar el script-->
<script>
    var datos = [];
    <?php
    #$result = mysqli_query($conexion, "SELECT count(*) from resguardo WHERE id_cajon = '$i' AND fecha BETWEEN '2020-11-20 00:00:00' AND '2020-11-30 00:00:00'");
    #se hace un bucle de repeticion para los 24 cajones
    for ($i = 1; $i <= 24; $i++) {
        $result = mysqli_query($conexion, "SELECT count(*) from resguardo WHERE id_cajon = '$i' AND fecha BETWEEN '" . $_SESSION['fecInicio'] . "' AND '" . $_SESSION['fecFin'] . "'");
        $mostrar = mysqli_fetch_row($result) ?>
        datos.push(<?= $mostrar[0] ?>);
    <?php
    }
    ?>
    var cajones = [];
    for (i = 1; i <= 24; i++) {
        cajones.push('Cajon N°' + i);
    }
    //se hace la suma total
    let total = 0;
    datos.forEach(function(a) {
        total += a;
    });
    //se manda un mensaje a la etiqueta para que se muestre
    document.getElementById("mensaje").innerHTML = "Total de resguardos: " + total;

    //se comienza a realizar la grafica de barras
    var ctx = document.getElementById("myChart").getContext("2d");
    var myChart = new Chart(ctx, {
        type: "bar",
        data: {
            labels: cajones,
            datasets: [{
                data: datos,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.1)',
                    'rgba(54, 162, 235, 0.1)',
                    'rgba(255, 206, 86, 0.1)',
                    'rgba(75, 192, 192, 0.1)',
                    'rgba(153, 102, 255, 0.1)',
                    'rgba(255, 159, 64, 0.1)',
                    'rgba(255, 99, 132, 0.1)',
                    'rgba(54, 162, 235, 0.1)',
                    'rgba(255, 206, 86, 0.1)',
                    'rgba(75, 192, 192, 0.1)',
                    'rgba(153, 102, 255, 0.1)',
                    'rgba(255, 159, 64, 0.1)',
                    'rgba(255, 99, 132, 0.1)',
                    'rgba(54, 162, 235, 0.1)',
                    'rgba(255, 206, 86, 0.1)',
                    'rgba(75, 192, 192, 0.1)',
                    'rgba(153, 102, 255, 0.1)',
                    'rgba(255, 159, 64, 0.1)',
                    'rgba(255, 99, 132, 0.1)',
                    'rgba(54, 162, 235, 0.1)',
                    'rgba(255, 206, 86, 0.1)',
                    'rgba(75, 192, 192, 0.1)',
                    'rgba(153, 102, 255, 0.1)',
                    'rgba(255, 159, 64, 0.1)',

                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)',
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)',
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)',
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });
</script>
<br><br>
<!-- boton para regresar al inicio-->
<div align="center">
    <a class="btn btn-info" href="index.php">Ir al inicio</a>
</div>
<br>

</html>