<?php
require 'vendor/autoload.php'; // El autoload de las clases gestionadas por composer
require 'cargarconfig.php'; // Carga de la configuración, autoload para nuestras clases y tareas de inicialización

use NoahBuscher\Macaw\Macaw;

/* DEFINICIÓN DE RUTAS */
session_start();
// página principal
Macaw::get($URL_PATH . '/', "controller\TiendaVideojuegosController@listarVideojuegos");
Macaw::get($URL_PATH . '/listado', "controller\TiendaVideojuegosController@listarVideojuegos");
Macaw::get($URL_PATH . '/listado/(:num)', "controller\TiendaVideojuegosController@listarVideojuegos");
Macaw::get($URL_PATH . '/iniciarSesion', "controller\TiendaVideojuegosController@iniciarSesion");
Macaw::post($URL_PATH . '/iniciarSesion', "controller\TiendaVideojuegosController@iniciarSesionAceptado");
Macaw::get($URL_PATH . '/cerrarSesion', "controller\TiendaVideojuegosController@cerrarSesion");
Macaw::get($URL_PATH . '/viaje/(:num)', "controller\TiendaVideojuegosController@obtenerViaje");
Macaw::get($URL_PATH . '/annadirACarrito/(:num)', "controller\TiendaVideojuegosController@annadirACarrito");
Macaw::get($URL_PATH . '/actualizarListaPedidos', "controller\TiendaVideojuegosController@actualizarListaPedidos");
Macaw::get($URL_PATH . '/verPedidos', "controller\TiendaVideojuegosController@verPedidos");
Macaw::get($URL_PATH . '/comprobarCompra', "controller\TiendaVideojuegosController@comprobarCompra");
Macaw::get($URL_PATH . '/aumentarPedido/(:num)', "controller\TiendaVideojuegosController@aumentarPedido");
Macaw::get($URL_PATH . '/disminuirPedido/(:num)', "controller\TiendaVideojuegosController@disminuirPedido");
// Despachar rutas

  Macaw::dispatch();

