<?php
//header('Cache-Control: no cache'); //no cache
//session_cache_limiter('private_no_expire'); // works
#session_cache_limiter('public'); // works too
#session_start();
include("../conexion.php");
require_permission("chat.ver");
$usuario = $_SESSION['usuario'] ?? 'Invitado';
$rol = (int) ($_SESSION['rol'] ?? 0);
$clienteId = active_client_id();

?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Estacionamiento</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <link rel="stylesheet" href="../assets/app.css">
    <nav class="navbar navbar-dark parking-navbar justify-content-between">
        <a class="navbar-brand text-white" href="?">Chat Estacionamiento</a>
    </nav>
</head>

<body class="parking-app">
    <!-- The core Firebase JS SDK is always required and must be listed first -->
    <script src="https://www.gstatic.com/firebasejs/8.2.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.2.1/firebase-database.js"></script>
    <script src="firebase.config.js"></script>
    <script>
        var nombre = <?php echo json_encode($usuario, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
        var mensajesRef = null;
        if (window.firebaseConfig) {
            firebase.initializeApp(window.firebaseConfig);
            mensajesRef = firebase.database().ref("clientes/<?php echo (int) $clienteId; ?>/mensajes");
        }
    </script>


    <!-- formulario para enviar el mensaje-->
    <!-- create a form to send message -->
    <br>
    <div align="center">
        <a class="btn btn-info" href="../index.php">Regresar</a>
    </div><br>
    <!-- create a list -->
    <div align="center">
        <div class="col-lg-6 col-sm-12 form-group w-50">
            <div id="chat-status" class="text-muted mb-2"></div>
            <ul id="mensajes"></ul>
        </div>
    </div>
    <div align="center">
        <div class="col-lg-6 col-sm-12 form-group w-50">
            <form onsubmit="return enviarMensaje();">
                <input class="form-control" id="mensaje" placeholder="Escribe un mensaje" autocomplete="off" required><br>
                <input type="submit" class="btn btn-warning">
            </form>
        </div>
    </div>
    <script>
        if (!mensajesRef) {
            document.getElementById("chat-status").innerText = "Chat no configurado.";
        } else {
            mensajesRef.on("child_added", function(snapshot) {
                var html = "";

                if (snapshot.val().remitente == nombre) {
                    html += "<li style='background: #e4ffcc;' align='right' id='mensaje-" + snapshot.key + "'>";
                    html += "<button class='btn btn-outline-danger' data-id='" + snapshot.key + "' onclick='borrarMensaje(this);'> ";
                    html += "Borrar";
                    html += "</button> ";
                } else {
                    html += "<li style='background: #f1eae0;' align='left' id='mensaje-" + snapshot.key + "'>";
                }
                html += "<b>" + snapshot.val().remitente + "</b>: " + snapshot.val().mensaje;
                html += "</li><br>";

                document.getElementById("mensajes").innerHTML += html;
            });

            mensajesRef.on("child_removed", function(snapshot) {
                document.getElementById("mensaje-" + snapshot.key).innerHTML = "Este mensaje fue eliminado";
            });
        }

        function borrarMensaje(self) {
            if (!mensajesRef) {
                return;
            }
            // get message ID
            var mensajeId = self.getAttribute("data-id");
            // delete message
            mensajesRef.child(mensajeId).remove();
        }

        function enviarMensaje() {
            if (!mensajesRef) {
                return false;
            }
            // get message
            var mensaje = document.getElementById("mensaje").value;

            // save in database
            mensajesRef.push().set({
                "remitente": nombre,
                "mensaje": mensaje
            });
            document.getElementById("mensaje").value = "";
            // prevent form from submitting
            return false;
        }
    </script>
</body>

</html>
