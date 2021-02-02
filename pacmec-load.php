<?php 
/**
  * Frente a la aplicación de Manager. Este archivo no hace nada, pero carga
  * pacmec-load.php que hace y le dice a PACMEC que cargue el sistema.
  *
  * @package PACMEC
  */

global $PACMEC;

require_once PACMEC_PATH.'includes/config/settings.php';
include PACMEC_PATH . "includes/functions.php";
#include PACMEC_PATH . "includes/Hooks.php";

pacmec_init_header();
pacmec_init_vars();
pacmec_init_session();
pacmec_init_options();

if(siteinfo('enable_ssl') == 1 && $_SERVER["HTTPS"] != "on"){
    header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
    exit();
}

pacmec_init_route();
pacmec_init_plugins_actives();

if(!empty($GLOBALS['PACMEC']['route']->layout)){
	$GLOBALS['PACMEC']['theme'] = pacmec_load_theme(PACMEC_PATH."content/themes/{$GLOBALS['PACMEC']['options']['theme']}/layouts/{$GLOBALS['PACMEC']['route']->layout}");
	// exit("Plantilla detectada: " . PACMEC_PATH."content/themes/{$GLOBALS['PACMEC']['options']['theme']}/layouts/{$GLOBALS['PACMEC']['route']->layout}");
} else {
	$GLOBALS['PACMEC']['theme'] = pacmec_load_theme(PACMEC_PATH."content/themes/{$GLOBALS['PACMEC']['options']['theme']}");
}


if(!isset($GLOBALS['PACMEC']['theme']['path'])){
	echo "Hubo un error cargando el tema principal, consulte la documentacion o contacte con soporte.";
	exit();
}


add_style_head($GLOBALS['PACMEC']['options']['siteurl']."/includes/system/assets/css/pacmec.css", true);
add_style_head($GLOBALS['PACMEC']['options']['siteurl']."/includes/system/assets/css/font-awesome.min.css", true);
add_scripts_lib_head($GLOBALS['PACMEC']['options']['siteurl']."/includes/system/assets/lib/axios/dist/axios.js");
add_scripts_lib_head($GLOBALS['PACMEC']['options']['siteurl']."/includes/system/assets/lib/vue-2.6.11/dist/vue.js");
add_scripts_lib_head($GLOBALS['PACMEC']['options']['siteurl']."/includes/system/assets/lib/vue-router-3.4.9/dist/vue-router.js");
add_scripts_lib_head($GLOBALS['PACMEC']['options']['siteurl']."/includes/system/sdk/alerts.js");

add_action('head', function (){
	$r = "<script>";
	$r .= "window.pacmec = {options:{ siteurl: '".siteinfo('siteurl')."' }};";			
	$r .= "</script>\n";
	echo $r;
});

add_scripts_lib_head($GLOBALS['PACMEC']['options']['siteurl']."/includes/system/sdk/all.js");

/*
if(isset($_REQUEST['logout'])){
	session_unset();
	session_destroy();
	header("Refresh: 0; URL=".siteinfo('siteurl')."/login");
	exit();
}*/


#$GLOBALS['PACMEC']['route'] = $GLOBALS['PACMEC']['routes'][$GLOBALS['PACMEC']['req_url']];
set_route($GLOBALS['PACMEC']['req_url']);

if(isset(get_route()->is_actived) && get_route()->is_actived !== 1 ) { exit("route no exite o no esta activo."); }
if(!validate_permission(get_route()->permission_access)){
	// if(isUser() == true){
		$GLOBALS['PACMEC']['routes']['/errors']->title = "Sin acceso";
		$GLOBALS['PACMEC']['routes']['/errors']->content = "No tienes permisos suficientes para acceder al contenido.";
		$GLOBALS['PACMEC']['route'] = $GLOBALS['PACMEC']['routes']['/errors'];
	// } else { $GLOBALS['PACMEC']['route'] = $GLOBALS['PACMEC']['routes']['/login']; }
	
	#exit('NO tienes permisos suficientes para acceder al contenido');
}


add_action('page_title', function(){ if(isset($GLOBALS['PACMEC']['route']->id)){ return (pageinfo('page_title')); } });
add_action('page_body', function(){
	if(isset($GLOBALS['PACMEC']['route']->request_uri) && $GLOBALS['PACMEC']['route']->request_uri !== ""){
		# $GLOBALS['PACMEC']['route']->content
		echo do_shortcode($GLOBALS['PACMEC']['route']->content);
	}
	else {
		echo do_shortcode(
			errorHtml("Lo sentimos, no se encontro el archivo o página.", "Ruta no encontrada")
		);
	}
});


if(is_file($GLOBALS['PACMEC']['theme']['path']) && file_exists($GLOBALS['PACMEC']['theme']['path'])){ require_once $GLOBALS['PACMEC']['theme']['path']; } 
//echo "\t--- Tema  validado ---\n";
