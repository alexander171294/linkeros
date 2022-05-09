<?php

// PHPQuery Hiper Cache. New technology implemented at: 25/06/2016
// si no estoy logueado //
/* VERSION ALPHA NO RECOMENDAMOS ACTIVAR ESTE CODE
require('phpquery/hiperCache.php');
$PHPQuery_hiperCache = new hiperCache(require('others/cacheDetails.php'));
$PHPQuery_hiperCache->load_HQ_cache();
*/
// END OF PHPQueryHiperCache

// Developer mode, active errors, and others.
define('DEVMODE', true);
define('VERSION', '2.0.1');
// show cost of execution
define('PQ_VIEW_COST', false);

define('PHPQUERY54', true);
// PHPQuery examples :)
// you can write this code in the way you want, it is very flexible

// first define DEVELOPER MODE, if the constant get value true, the framework show errors and remake the cache of tpl always.
// if the constant get value false, the framework DON'T SHOW ERRORS, and don't remake cache of tpls (only if there are not or you delete old cache)

// now, require the loader of PHPQuery, this is only line necessary
require('others/funciones.php');
require('configs/constantes.php');


/*
if(REDIRECT_SSL && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'http') // without https
{
	header('location: https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
	die('301 to HTTPS');
}*/

if(REDIRECT_WWW && strpos($_SERVER['HTTP_HOST'], 'www.') === false)
{
	if(REDIRECT_SSL)
		header('location: https://www.'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
	else
		header('location: http://www.'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
	die('301 to WWW');
}


require('phpquery/loader.php');
coreData::$default_404_controller = 'e404';
coreData::$default_500_controller = 'e500';
coreData::$v = 'themes/';

// this set new values for the default configuration of DB
// see default config in phpquery/default_config/dbData.php
if(file_exists('settings.php'))
	include('settings.php');

// extension of view
// if you don't like .tpl extension in views, you set do you want
// you see default config in phpquery/default_config/tplData.php
tplData::$extension = '.html';	

// the important, you need declare the controllers that exist
// first declare file (example.php), later you declare the controllers in the file

// inicio controladores posts
_::declare_controller('posts_home', 'home');
_::declare_controller('posts_ver', 'ver_post');
_::declare_controller('posts_novatos', 'novatos');
_::declare_controller('posts_taglist', 'tag_lista');
_::declare_controller('posts_catlist', 'categoria_lista');
_::declare_controller('posts_agregar', 'agregar');
_::declare_controller('error_control', 'e404', 'e403', 'e500', 'post_404', 'post_403', 'post_501');
// Fin controladores posts
// inicio de controladores buscador
_::declare_controller('buscador', 'buscador');
_::declare_controller('catalogo_buscador', 'catalogo_buscador');
// Fin de controladores buscador
// inicio de controladores top
_::declare_controller('tops', 'tops', 'top_hoy', 'top_ayer', 'top_semana', 'top_mes', 'top_mesanterior', 'top_anio');
// Fin de controladores top

_::declare_controller('estaticas', 'version', 'terms', 'protocolo', 'agradecimientos', 'closed', 'closed_all', 'constjs', 'security', 'banned', 'tutorial', 'proxy', 'catalogo_calidades');
_::declare_controller('users', 'check_nick', 'check_email', 'realizar_registro', 'ajax_login', 'logout', 'ver_perfil', 'check_account', 'new_data', 'report_users');
_::declare_controller('post_moderate', 'puntuar_post', 'make_draft', 'make_undraft', 'delete_post', 'sticky_post', 'unsticky_post', 'make_unsuspend', 'edit_post', 'block_content', 'report');
_::declare_controller('seguidores', 'no_seguir_usuario', 'seguir_usuario', 'no_seguir_post', 'seguir_post', 'favorito_post', 'linkear_post');
_::declare_controller('comentarios', 'agregar_comentario', 'eliminar_comentario', 'votar_comentario', 'actualizar_comentario', 'reiniciar_comentario');
_::declare_controller('edit_account', 'account', 'account_password', 'account_avatar', 'account_profile', 'account_design');
_::declare_controller('cron', 'cron_rangos', 'cron_puntos', 'cron_tops', 'cron_sitemap', 'cron_medalla');
_::declare_controller('bot', 'bot_newuser', 'bot_comments', 'xml_importer');
_::declare_controller('notificaciones', 'notify');
_::declare_controller('mensajes', 'mensajes', 'mensajes_enviados', 'mensajes_vaciar', 'mensajes_nuevo', 'mensajes_nick', 'mensajes_ver', 'mensajes_eliminar', 'report_mp');
_::declare_controller('borradores', 'borradores', 'borradores_vaciar', 'borradores_publicar', 'borradores_borrar', 'borradores_revision');
_::declare_controller('config', 'config', 'config_mantenimiento', 'config_performance', 'config_recaptcha', 'config_home', 'config_top', 'config_rangos', 'config_avatar', 'config_moderation');
_::declare_controller('user_moderate', 'cambiar_rango_usuario', 'bannear_usuario', 'desbannear_usuario', 'banip', 'ubanip');
_::declare_controller('control', 'control', 'control_tour', 'control_history', 'control_revised', 'control_revisedmp', 'control_reviseduser', 'control_banneds', 'control_iptable');
_::declare_controller('favoritos', 'favoritos', 'favoritos_delete', 'favoritos_vaciar');
_::declare_controller('uac', 'uac_profile', 'uac_drop', 'uac_password');
_::declare_controller('catalogos', 'catalogos', 'catalogos_descargables', 'catalogos_online', 'catalogo_add', 'catalogo_aprove', 'catalogo_added', 'catalogo_objeto', 'catalogo_report', 'catalogo_delete', 'catalogo_void_suggest', 'catalogo_void_reports');
_::declare_controller('theme', 'theme');

// initialize the framework using debug mode (you can change the constant if like)
_::init(DEVMODE);
_::declare_component('userErrorHandler');

if(!file_exists('install.log'))
{
	_::declare_controller('installer', 'install', 'install_ajax');
} else {
	require('others/headers.php');
	require('others/footer.php');
}



// then set the variable for select controller (in this case "action")
$action = isset($_GET['action']) ? $_GET['action'] : 'home';
// ya lo instalamos?
if(!file_exists('install.log'))
{
	if($action !== 'install_ajax')
		$action = 'install';
} elseif($action == 'install' || $action == 'install_ajax') $action = 'home';

// execute the action (that is, call controller set in $action if exist)

if(MANTENIMIENTO_GENERAL)
	_::execute('closed_all');
else
{
	_::execute($action);
}
// to end, show all views seted using _::$view->show();
_::$view->execute();

// PHPQuery Hiper Cache. New technology implemented at: 25/06/2016
//$PHPQuery_hiperCache->save_HQ_cache();
// END OF PHPQueryHiperCache

// SHOW TIME COST
if(defined('PQ_VIEW_COST') && PQ_VIEW_COST == TRUE)
	var_dump(_::get_cost());