<?php
//header('Cache-Control: no cache'); //no cache
//session_cache_limiter('private_no_expire'); // works
#session_cache_limiter('public'); // works too
session_start();
$usuario = $_SESSION['usuario'];
$rol = (int) $_SESSION['rol'];

?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Estacionamiento</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <nav class="navbar navbar-light bg-info justify-content-between">
        <a class="navbar-brand text-white" href="?">Registrar Vehiculo</a>
    </nav>
</head>

<body style="background: linear-gradient(180deg, #C6FCFC, #A7FC97);">
    <!-- The core Firebase JS SDK is always required and must be listed first -->
    <script src="https://www.gstatic.com/firebasejs/8.2.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.2.1/firebase-database.js"></script>
    <script>
        // Your web app's Firebase configuration
        // For Firebase JS SDK v7.20.0 and later, measurementId is optional
        var firebaseConfig = {
            apiKey: "AIzaSyCBJ_igTtp7ZVfR-ghgQ0jSiGzNT8-Gjrc",
            authDomain: "chat-estacionamiento.firebaseapp.com",
            projectId: "chat-estacionamiento",
            storageBucket: "chat-estacionamiento.appspot.com",
            messagingSenderId: "775433452281",
            appId: "1:775433452281:web:ce2f6dc71126f1c919c028",
            measurementId: "G-3SKHT66TZT"
        };
        // Initialize Firebase
        firebase.initializeApp(firebaseConfig);
        //var nombre = prompt("nombre de Usuario");
        var nombre = "<?php echo $usuario ?>";
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
        // listen for incoming messages
        firebase.database().ref("mensajes").on("child_added", function(snapshot) {
            var html = "";

            // show delete button if message is sent by me
            if (snapshot.val().remitente == nombre) {
                // give each message a unique ID
                html += "<li style='background: #e4ffcc;' align='right' id='mensaje-" + snapshot.key + "'>";
                html += "<button class='btn btn-outline-danger' data-id='" + snapshot.key + "' onclick='borrarMensaje(this);'> ";
                html += "Borrar";
                html += "</button> ";
            } else {
                // give each message a unique ID
                html += "<li style='background: #f1eae0;' align='left' id='mensaje-" + snapshot.key + "'>";
            }
            html += "<b>" + snapshot.val().remitente + "</b>: " + snapshot.val().mensaje;
            html += "</li><br>";

            document.getElementById("mensajes").innerHTML += html;
        });

        function borrarMensaje(self) {
            // get message ID
            var mensajeId = self.getAttribute("data-id");
            // delete message
            firebase.database().ref("mensajes").child(mensajeId).remove();
        }

        // attach listener for delete message
        firebase.database().ref("mensajes").on("child_removed", function(snapshot) {
            // remove message node
            document.getElementById("mensaje-" + snapshot.key).innerHTML = "Este mensaje fue eliminado";
        });

        function enviarMensaje() {
            // get message
            var mensaje = document.getElementById("mensaje").value;

            // save in database
            firebase.database().ref("mensajes").push().set({
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