<?php 
/**
  * Frente de la aplicación de PACMEC. Este archivo no hace nada, pero carga
  * pacmec-load.php que hace y le dice a PACMEC que cargue el sistema.
  *
  * @package PACMEC
  */

// echo "-".time()."-\n";
if ( ! defined( 'PACMEC_PATH' ) ) { define( 'PACMEC_PATH', __DIR__ . '/' ); }
require PACMEC_PATH . 'pacmec-load.php';
// echo "-".time()."-\n";



do_action('init');

if(is_file("{$GLOBALS['PACMEC']['theme']['dir']}/index.php") && file_exists("{$GLOBALS['PACMEC']['theme']['dir']}/index.php")){
	require_once "{$GLOBALS['PACMEC']['theme']['dir']}/index.php";
} else {
	exit("Error critico del tema, index.php no encontrado.");
}

// echo json_encode(array_keys($GLOBALS), JSON_PRETTY_PRINT);
// echo "\n";
// echo json_encode($GLOBALS['PACMEC']['session'], JSON_PRETTY_PRINT);