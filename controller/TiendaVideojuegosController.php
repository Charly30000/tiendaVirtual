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

    function quitarPedido($idProducto) {
        $cookie = sanitizar(session_id());
        (new Orm)->quitarPedido($idProducto, $cookie);
        echo json_encode(["realizado"]);
    }

    function annadirDireccion() {
        $cookie = sanitizar(session_id());
        $cantidadPedidos = (new Orm)->obtenerCantidadPedidos($cookie);
        echo Ti::render("view/annadirDireccion.phtml",compact("cantidadPedidos"));
    }

    function annadirDireccionCompletado() {
        global $URL_PATH;
        $cookie = sanitizar(session_id());
        $nombre = sanitizar($_POST["nombre"] ?? "ninguno");
        $email = sanitizar($_POST["email"] ?? "ninguno");
        $direccion = sanitizar($_POST["direccion"] ?? "ninguno");
        $usuario = new Usuario;
        $usuario->nombre_usuario = $nombre;
        $usuario->direccion_correo = $email;
        $usuario->direccion_fisica = $direccion;
        //Empezamos a hacer las insercciones pertinentes para crear al usuario y su pedido
        (new Orm)->annadirUsuario($usuario);
        (new Orm)->annadirPedido($nombre);
        //Obtenemos los pedidos del usuario para añadirlos a la base de datos de pedidos en espera
        $pedidosUsuario = (new Orm)->obtenerPedidosUsuario($cookie);
        $obtenerIdPedido = (new Orm)->obtenerIdPedido($nombre);
        //Inserto todos los productos en pedidos
        foreach ($pedidosUsuario as $value) {
            (new Orm)->insertarProductosPide($value["id_producto"], $obtenerIdPedido["id_pedido"], $value["cantidad"]);
        }
        $cod_comercio = 2222;
        $cod_pedido = $obtenerIdPedido["id_pedido"];
        $importe = (new Orm)->precioTotalPedidos($cookie)["sumaTotal"];
        $concepto = "Pago del usuario $nombre en concepto de la compra de videojuegos";
        $cantidadPedidos = (new Orm)->obtenerCantidadPedidos($cookie);
        echo Ti::render("view/enviarDatosABanco.phtml",compact("cod_comercio", "cod_pedido", "importe", "concepto", "cantidadPedidos"));
    }

    function informa() {
        $cod_pedido = sanitizar($_REQUEST["cod_pedido"] ?? 0);
        $importe = sanitizar($_REQUEST["importe"] ?? 0);
        $estado = sanitizar($_REQUEST["estado"] ?? "");
        $cod_operacion = sanitizar($_REQUEST["cod_operacion"] ?? 0);
        $estadoPago = "";
        if ($estado === "ok") {
            $estadoPago = "realizado";
        } elseif ($estado === "nook") {
            $estadoPago = "pago sin exito";
        } elseif ($estado === "cancelado") {
            $estadoPago = "cancelado";
        } else {
            $estadoPago = "intento hack";
        }
        (new Orm)->actualizarEstadoPedido($estadoPago, $cod_pedido);
        echo '{"msg": "Servidor de la tienda informado"}';
    }

    function retorno() {
            $cookie = sanitizar(session_id());
            $cantidadPedidos = (new Orm)->obtenerCantidadPedidos($cookie);
            $cod_pedido = sanitizar($_REQUEST["cod_pedido"] ?? "");
            $mensaje = "";
            if ($cod_pedido) {
                $estadoPedido = (new Orm)->obtenerEstadoPedido($cod_pedido);
                switch ($estadoPedido["estado_pedido"]) {
                    case 'realizado':
                        $mensaje = "El pago se ha podido realizar con exito!";
                        break;
                    case 'cancelado':
                        $mensaje = "El pago ha sido cancelado";
                        break;
                    case 'pago sin exito':
                        $mensaje = "El pago no se ha podido realizar";
                        break;
                    case 'realizado':
                        $mensaje = "Se ha detectado un intento de hack";
                        break;
                    default:
                        $mensaje = "Ha ocurrido un error inesperado, por favor, vuelva a intentarlo más tarde";
                        break;
                }
                echo Ti::render("view/verEstadoPedido.phtml",compact("mensaje", "cod_pedido", "cantidadPedidos"));
            }
    }
}
