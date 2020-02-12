let botonesBorrar = document.getElementsByClassName("borrarPedido");
for (let item of botonesBorrar) {
    item.addEventListener("click", borrarPedido);
}

function borrarPedido() {
    let idPedido = this.id.split("_");
    let caja = this;
    let url = URL_PATH + "/quitarPedido/" + idPedido[0];
    fetch(url)
    .then(function(respuesta) {
        return respuesta.json();
    })
    .then (function (respuesta) {
        if (respuesta[0] === "realizado") {
            caja.parentNode.parentNode.remove();
        } else {
            alert(respuesta[0]);
        }
    })
}