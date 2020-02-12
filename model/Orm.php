<?php

namespace model;

use dawfony\Klasto;
use \model\Producto;
class Orm
{

    public function obtenerVideojuegos($pagina = 0)
    {
        global $VIAJES_POR_PAGINA;
        $viajes = Klasto::getInstance()->query(
            "SELECT `id_producto`, `nombre`, `descripcion`, `precio`, `foto` FROM `producto` limit ? offset ?",
            [$VIAJES_POR_PAGINA, ($pagina * $VIAJES_POR_PAGINA)],
            "model\Producto"
        );
        return $viajes;
    }

    public function cantidadVideojuegos() {
        $bd = Klasto::getInstance();
        $sql = "select count(*) as contador from producto";
        return $bd->queryOne($sql);
    }



    public function obtenerVideojuego($id) {
        $bd = Klasto::getInstance();
        $sql = "select id_producto, nombre, descripcion, precio, foto from producto where id_producto = ?";
        return $bd->queryOne($sql, [$id], "model\Producto");
        
    }

    public function annadirACarrito($idProducto, $cookie) {
        $bd = Klasto::getInstance();
        $sql = "select id_visitante, id_producto, cantidad from cesta where id_visitante = ? and id_producto = ?";
        $productoVisitante = $bd->queryOne($sql, [$cookie, $idProducto], "model\Cesta");
        if (!$productoVisitante) {
            $sql = "insert into cesta (id_visitante, id_producto, cantidad) values (?, ?, 1)";
            $ejecutar = $bd->execute($sql, [$cookie, $idProducto]);
            if ($ejecutar === 0) {
                echo "No se ha podido hacer la operacion";
                die();
            }
        }
    }

    public function obtenerCantidadPedidos($cookie) {
        $bd = Klasto::getInstance();
        $sql = "select count(*) as contador from cesta where id_visitante = ?";
        return $bd->queryOne($sql, [$cookie]);
    }

    public function obtenerPedidosUsuario($cookie) {
        $bd = Klasto::getInstance();
        $sql = "select cesta.id_producto, cesta.cantidad, producto.nombre, producto.foto, 
                producto.precio * cesta.cantidad as precio 
                from cesta, producto 
                where id_visitante = ? and cesta.id_producto = producto.id_producto";
        return $bd->query($sql, [$cookie]);
    }
    //Te dice el precio total de todos los productos
    public function precioTotalPedidos($cookie) {
        $bd = Klasto::getInstance();
        $sql = "select sum(cesta.cantidad * producto.precio) as sumaTotal 
                from cesta, producto 
                where id_visitante = ? and cesta.id_producto = producto.id_producto";
        return $bd->queryOne($sql, [$cookie]);
    }

    public function aumentarPedido($idProducto, $cookie) {
        $bd = Klasto::getInstance();
        $sql = "update cesta set cantidad = cantidad + 1 where id_visitante = ? and id_producto = ?";
        $ejecutar = $bd->execute($sql, [$cookie, $idProducto]);
        if ($ejecutar === 0) {
            echo json_encode(["No se ha podido hacer la operacion"]);
            die();
        }
    }

    public function disminuirPedido($idProducto, $cookie) {
        $bd = Klasto::getInstance();
        $sql = "update cesta set cantidad = cantidad - 1 where id_visitante = ? and id_producto = ?";
        $ejecutar = $bd->execute($sql, [$cookie, $idProducto]);
        if ($ejecutar === 0) {
            echo json_encode(["No se ha podido hacer la operacion"]);
            die();
        }
    }

    public function obtenerCantidadPrecioPedido($idProducto, $cookie) {
        $bd = Klasto::getInstance();
        $sql = "select producto.precio * cesta.cantidad as precio, cesta.cantidad from producto, cesta 
            where id_visitante = ? and producto.id_producto = cesta.id_producto and producto.id_producto = ?";
        return $bd->queryOne($sql, [$cookie, $idProducto]);
    }

    public function quitarPedido($idProducto, $cookie) {
        $bd = Klasto::getInstance();
        $sql = "delete from cesta where id_producto = ? and id_visitante = ?";
        $ejecutar = $bd->execute($sql, [$idProducto, $cookie]);
        if ($ejecutar === 0) {
            echo json_encode(["No se ha podido hacer la operacion"]);
            die();
        }
    }
}
