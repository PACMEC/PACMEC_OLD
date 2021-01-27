<?php 
/* *******************************
 *
 * Developer by FelipheGomez
 *
 * ******************************/
// DATABASE CONFIG
define('DB_port', '3306');
define('DB_driver', 'mysql');
define('DB_host', 'localhost');
define('DB_user', 'root');
define('DB_pass', '');
define('DB_database', 'club');
define('DB_charset', 'utf8mb4');
define('DB_prefix', 'pacmec_');

define('AUTH_KEY_COST', 10);

// define('PACMEC_PUBLIC_KEY', );

define("PLUGIN_DEFECTO", "CMS"); // CONTROLLER DEFAULT - ACCION DEFECTO
define("ACCION_DEFECTO", "Index"); // ACTION DEFAULT - ACCION DEFECTO
define("LAYOUT_DEFECTO", "main"); // ACTION DEFAULT - ACCION DEFECTO
define("TEMA_DEFECTO", "none"); // THEME DEFAULT - TEMA DEFECTO

if ( ! defined( 'PACMEC_PATH' ) ) { define( 'PACMEC_PATH', __DIR__ . '/' ); }

setlocale(LC_MONETARY, 'es_CO');
date_default_timezone_set("America/Bogota");