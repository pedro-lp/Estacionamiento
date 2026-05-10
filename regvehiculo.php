<?php
#header('Cache-Control: no cache'); //no cache
#session_cache_limiter('private_no_expire'); // works
//session_cache_limiter('public'); // works too
#session_start();
#se incluiye la conexion
include("conexion.php");
require_permission("vehiculos.crear");

if (isset($_POST['agregar'])) {
    verify_csrf();
    $marca = clean_text($_POST['marca'] ?? '', 50);
    $modelo = clean_text($_POST['modelo'] ?? '', 50);
    $placas = clean_plate($_POST['placas'] ?? '');
    $color = clean_text($_POST['color'] ?? '', 30);
    $tamano = clean_tamano($_POST['tamano'] ?? 'Chico');
    $nombredue = clean_text($_POST['nombredue'] ?? '', 100);

    db_query(
        "INSERT INTO vehiculo (cliente_id, marca, modelo, placas, color, tamano, nombredue) VALUES (?, ?, ?, ?, ?, ?, ?)",
        "issssss",
        active_client_id(),
        $marca,
        $modelo,
        $placas,
        $color,
        $tamano,
        $nombredue
    );
    audit_log("vehiculos.crear", "vehiculo", null, "Vehiculo registrado: " . $placas);

    header("location:index.php");
    exit();
}
?>
<html lang="es">

<!-- cabecera de la pagina web -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- titulo de la pagina-->
    <title>Registrar Vehiculo</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/app.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>


    <nav class="navbar navbar-dark parking-navbar justify-content-between">
        <a class="navbar-brand text-white" href="?">Registrar Vehiculo</a>
    </nav>

</head>

<!-- cuerpo de la pagina-->
<body class="parking-app">

    <div class="container p-4">
        <br>
        <div align="center">
            <?php if (isset($_SESSION['message'])) { ?>
                <div class="alert alert-<?= $_SESSION['message_type']; ?> alert-dismissible fade show" role="alert">
                    <?= $_SESSION['message'] ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php session_unset();
            } ?>
            <!-- formulario de registro-->
            <form action="regvehiculo.php" method="POST" class="card parking-card p-4">
                <?php echo csrf_field(); ?>
                <div class="row">
                    <div class="col-lg-6 col-sm-12 form-group">
                        <!-- se obtiene el id a partir del ultimo registro que se tiene en la base de datos -->
                        ID:<input type="text" name="id" class="form-control" value="<?php
                                                                                    $mostrar = db_one("SELECT id FROM vehiculo WHERE cliente_id = ? ORDER BY id DESC LIMIT 1", "i", active_client_id());
                                                                                    echo (($mostrar['id'] ?? 0) + 1);
                                                                                    ?>" disabled>
                    </div>
                    <div class="col-lg-6 col-sm-12 form-group">
                        Nombre del Dueño:<input type="text" name="nombredue" class="form-control" required>
                    </div>
                    <div class="col-lg-6 col-sm-12 form-group">
                        Marca:<input type="text" name="marca" class="form-control" required>
                    </div>
                    <div class="col-lg-6 col-sm-12 form-group">
                        Modelo:<input type="text" name="modelo" class="form-control" required>
                    </div>
                    <div class="col-lg-6 col-sm-12 form-group">
                        Placas:<input type="text" name="placas" class="form-control" required>
                    </div>
                    <div class="col-lg-6 col-sm-12 form-group">
                        Color:<input type="text" name="color" class="form-control" required>
                    </div>

                    <div class="col-lg-6 col-sm-12 input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="tamano">Seleccionar tamano: </label>
                        </div>
                        <select name="tamano" id="tamano" class="custom-select">
                            <option value="Chico">Chico</option>
                            <option value="Grande">Grande</option>
                        </select>
                    </div>
                </div>
                <br><br>
                <div align="center">
                        <!-- boton enviar -->
                    <a class="btn btn-outline-secondary" href="index.php">Regresar</a>
                    <input type="submit" class="btn btn-parking" name="agregar" value="Agregar Vehiculo">
                </div>
            </form>
        </div>
</body>

</html>
