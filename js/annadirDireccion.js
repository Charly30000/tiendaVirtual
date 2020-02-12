let formulario = document.getElementById("formulario");

formulario.addEventListener("submit", function(evt) {
    let nombre = document.getElementById("nombre").value;
    let email = document.getElementById("email").value;
    let direccion = document.getElementById("direccion").value;
    let mensajeError = "";
    let error = false;
    if (nombre === "") {
        mensajeError += "Añade un nombre por favor\n";
        error = true;
    }
    if (email === "") {
        mensajeError += "Añade un correo por favor\n";
        error = true;
    }
    if (direccion === "") {
        mensajeError += "Añade una direccion por favor";
        error = true;
    }

    if (error) {
        evt.preventDefault();
        alert(mensajeError);
    }
})