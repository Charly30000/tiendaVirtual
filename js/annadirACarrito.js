window.onload = function() {
    this.document.getElementById("verCarrito").onclick = actualizarListaPedidos;
    this.document.getElementById("verCarrito").click();
}

function pedir(idProducto) {
    let url = URL_PATH + "/annadirACarrito/" + idProducto;
    fetch(url)
    .then(function(respuesta){
      return respuesta.json();
    })
    .then(function(respuesta){
      console.log(respuesta)
      document.getElementById("carrito").textContent = respuesta.contador;
    })
    this.document.getElementById("verCarrito").click();
  }

  function actualizarListaPedidos(evt) {
      evt.preventDefault();
      let url = URL_PATH + "/actualizarListaPedidos";
      fetch(url)
      .then(function(respuesta){
          return respuesta.json();
      })
      .then(function (respuesta) {
        console.log(respuesta)
          document.getElementById("listaPedidos").innerHTML = "";
          for (let producto of respuesta) {
              ponerProductoEnLista(producto);
          }
      })
  }

  function ponerProductoEnLista(producto) {
    let contenedor = document.getElementById("listaPedidos");
    //Creo div que tiene uno de los productos y lo añado en la lista de pedidos
    let row = document.createElement("div");
    contenedor.appendChild(row);
    row.setAttribute("class", "row");
    //Creo un div md2 (grid imagen)
    let colmd2 = document.createElement("div");
    row.appendChild(colmd2);
    colmd2.setAttribute("class", "col-md-2");
    colmd2.setAttribute("class", "bloque");
    //Creo un div md10 (grid nombre del producto)
    let colmd10 = document.createElement("div");
    row.appendChild(colmd10);
    colmd10.setAttribute("class", "col-md-10");
    colmd10.setAttribute("class", "bloque");
    //Inserto la imagen del producto en el colmd2
    colmd2.innerHTML = '<img src="'+ URL_PATH + '/assets/videojuegos/'+ producto.foto + '" alt="' + producto.foto +'">';
    //Inserto el nombre del producto en el colmd10
    colmd10.innerHTML = producto.nombre;
  }