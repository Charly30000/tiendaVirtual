<?php

spl_autoload_register(function($class) {
    require_once str_replace('\\','/',$class) . ".php";
});


// obtener configuración
if (!$config=parse_ini_file("config.ini")) {
    die ("No hay fichero configuración");
}

// El path hasta la app en la URL
$URL_PATH = $config['url_path'];
$VIAJES_POR_PAGINA = $config["viajes_por_pagina"];
// Inicializar BD
dawfony\Klasto::init($config["db_host"], $config["db_user"], $config["db_pass"], $config["db_name"]);

