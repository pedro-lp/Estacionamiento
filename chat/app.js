//se inicializa firebase
firebase.initializeApp({
  apiKey: "AIzaSyAmWq5oY3hGQdTiOhsXpH3HAnpax_nyuII",
  authDomain: "proyectoalumnos-5e73c.firebaseapp.com",
  databaseURL: "https://proyectoalumnos-5e73c.firebaseio.com",
  projectId: "proyectoalumnos-5e73c",
  storageBucket: "proyectoalumnos-5e73c.appspot.com",
  messagingSenderId: "409709994838",
  appId: "1:409709994838:web:1b78e7ff139377718cecf7",
  measurementId: "G-T4P1WDFD16",
});
//se crea la variable de la base de datos
var db = firebase.firestore();

//funcion que carga los datos a las tablas
function cargarDatos(doc) {
  var sexoSelect = `${doc.data().sexo}`;
  var tab;
  //segun la tabla se colocan los datos
  if (sexoSelect == "Femenino") {
    tab = tablaMujeres;
  } else if (sexoSelect == "Masculino") {
    tab = tablaHombres;
  } //se comienzan a crear los registros
  tab.innerHTML += `
          <tr>
          <th scope="row">${doc.data().matricula}</th>
          <td>${doc.data().nombre}</td>
          <td>${doc.data().apePat}</td>
          <td>${doc.data().apeMat}</td>
          <td>${doc.data().edad}</td>
          <td>${doc.data().sexo}</td>
          <td>${doc.data().promedio}</td>
          <td><button class="btn btn-outline-warning" onclick="editar(
            '${doc.id}',
            '${doc.data().matricula}',
            '${doc.data().nombre}',
            '${doc.data().apePat}',
            '${doc.data().apeMat}',
            '${doc.data().edad}',
            '${doc.data().sexo}',
            '${doc.data().promedio}')">Editar</button></td>
          <td><button class="btn btn-outline-danger" onclick="eliminar('${
            doc.id
          }')">Eliminar</button></td>        
          </tr> `;
}

//se cargan todos los registros de firebase
db.collection("chat").onSnapshot((querySnapshot) => {
  var nombre;
  var mensaje;
  chatUI.innerHTML = "";
  tablaHombres.innerHTML = "";
  querySnapshot.forEach((doc) => {
    console.log(`${doc.id} => ${doc.data().first}`);
    calificaciones += parseInt(doc.data().promedio);
    numPersonas++;
    //se manda a llamar al metodo de cargar datos
    cargarDatos(doc);
    //se imprime el total de personas y de promedio
    tablaResultados.innerHTML =
      `<tr> <td>` +
      numPersonas +
      `</td> <td>` +
      calificaciones / numPersonas +
      `</td> </tr> `;
  });
});

//Guarda el registro del alumno introducido en firebase
function guardar() {
  var nombre = document.getElementById("nombre").value;
  var mensaje = document.getElementById("mensaje").value;
  if (nombre == "" || mensaje == "") {
    //evalua que las casillas no esten vacias
    //si alguna casilla esta vacio manda un mensaje de error
    alert("Rellene todas las casillas");
  } else {
    //comienza a guardar los datos
    db.collection("chat")
      .add({
        nombre: nombre,
        mensaje: mensaje,
      })
      .then(function (docRef) {
        //se manda un mensaje
        console.log("Mensaje enviadp ", docRef.id);
        limpiarCasillas(); //se limpian las casillas
      })
      .catch(function (error) {
        //se manda un mensaje de error
        console.error("Error no se pudo enviar el mensaje: ", error);
      });
  }
}

function limpiarCasillas() {
  //borra los valores que tienen cada una de las casillas del formulario
  document.getElementById("nombre").value = "";
  document.getElementById("mensaje").value = "";
}
