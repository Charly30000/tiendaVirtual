window.onload = function() {
    let aumentar = document.getElementsByClassName("aumentar");
    for (let flecha of aumentar) {
        flecha.onclick = annadirPedido;
    }
    let disminuir = document.getElementsByClassName("disminuir");
    for (let flecha of disminuir) {
        flecha.onclick = disminuirPedido;
    }
}

function annadirPedido(evt) {
    evt.preventDefault();
    let id_producto = this.id.split("_");
    let url = URL_PATH + "/aumentarPedido/" + id_producto[0];
    console.log(url)
    fetch(url)
    .then(function(respuesta) {
        return respuesta.json();
    })
    .then(function(respuesta) {
        document.getElementById(id_producto[0] + "_precio").textContent = respuesta.precio;
        document.getElementById(id_producto[0] + "_cantidad").textContent = respuesta.cantidad;
        document.getElementById("precioTotal").textContent = respuesta.sumaTotal;
    })
}

function disminuirPedido(evt) {
    evt.preventDefault();
    let id_producto = this.id.split("_");
    let url = URL_PATH + "/disminuirPedido/" + id_producto[0];
    fetch(url)
    .then(function(respuesta) {
        return respuesta.json();
    })
    .then(function(respuesta) {
        document.getElementById(id_producto[0] + "_precio").textContent = respuesta.precio;
        document.getElementById(id_producto[0] + "_cantidad").textContent = respuesta.cantidad;
        document.getElementById("precioTotal").textContent = respuesta.sumaTotal;
    })
}
