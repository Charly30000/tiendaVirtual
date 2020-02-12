<?php
namespace controller;
require_once ("funciones.php");
use \model\Orm;
use \dawfony\Ti;
use \model\Usuario;
class TiendaVideojuegosController extends Controller
{

    function listarVideojuegos($pagina = 0) {
        $orm = new Orm;
        $pagina = sanitizar($pagina);
        $cookie = sanitizar(session_id());
        $videojuegos = $orm->obtenerVideojuegos($pagina);
        $cantidadVideojuegos = $orm->cantidadVideojuegos();
        $cantidadPedidos = $orm->obtenerCantidadPedidos($cookie);
        echo Ti::render("view/listado.phtml",compact("videojuegos", "pagina", "cantidadVideojuegos", "cantidadPedidos"));
    }

    function iniciarSesionAceptado() {
        $loginUsuario = sanitizar($_POST["login"] ?? "");
        $contrasennaUsuario = sanitizar($_POST["contrasenna"] ?? "");
        $usuario = new Usuario;
        $usuario->login = $loginUsuario;
        $usuario->password = $contrasennaUsuario;
        $existeUsuario = (new Orm)->comprobarExistenciaUsuario($usuario);
        if (!$existeUsuario) {
            $error = "El usuario no existe";
            $data = ["error" => $error];
            echo Ti::render("view/iniciarSesion.phtml", $data);
            die();
        }
        $_SESSION["login"] = $loginUsuario;
        header("Location: listado");
    }

    function cerrarSesion() {
        session_start();
        session_unset();
        session_destroy();
        header("Location: listado");
    }

    function obtenerViaje($id) {
            $id = sanitizar($id);
            $videojuego = (new Orm)->obtenerVideojuego($id);
            header("Content-type: application/json");
            echo json_encode($videojuego);
    }
    
    function annadirACarrito($idProducto) {
        $id = sanitizar($idProducto);
        $cookie = sanitizar(session_id());
        (new Orm)->annadirACarrito($id, $cookie);
        echo json_encode((new Orm)->obtenerCantidadPedidos($cookie));
    }

    function actualizarListaPedidos() {
        $cookie = sanitizar(session_id());
        echo json_encode((new Orm)->obtenerPedidosUsuario($cookie));
    }

    function verPedidos() {
        $cookie = sanitizar(session_id());
        $pedidosUsuario = (new Orm)->obtenerPedidosUsuario($cookie);
        $cantidadPedidos = (new Orm)->obtenerCantidadPedidos($cookie);
        $precioTotal = (new Orm)->precioTotalPedidos($cookie);
        echo Ti::render("view/pedidos.phtml",compact("pedidosUsuario", "cantidadPedidos", "precioTotal"));
    }

    function comprobarCompra() {
        $cookie = sanitizar(session_id());
        $pedidosUsuario = (new Orm)->obtenerPedidosUsuario($cookie);
        $cantidadPedidos = (new Orm)->obtenerCantidadPedidos($cookie);
        $precioTotal = (new Orm)->precioTotalPedidos($cookie);
        echo Ti::render("view/comprobarPedidosCorrectos.phtml",compact("pedidosUsuario", "cantidadPedidos", "precioTotal"));
    }

    function aumentarPedido($idProducto) {
        $cookie = sanitizar(session_id());
        (new Orm)->aumentarPedido($idProducto, $cookie);
        $data = (new Orm)->obtenerCantidadPrecioPedido($idProducto, $cookie);
        $data += (new Orm)->precioTotalPedidos($cookie);
        echo json_encode($data);
    }

    function disminuirPedido($idProducto) {
        $cookie = sanitizar(session_id());
        (new Orm)->disminuirPedido($idProducto, $cookie);
        $data = (new Orm)->obtenerCantidadPrecioPedido($idProducto, $cookie);
        $data += (new Orm)->precioTotalPedidos($cookie);
        echo json_encode($data);
    }
}
