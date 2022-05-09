<?php class_exists('_') || die('FORBIDDEN');

/* CONTROLLER NOVATOS
 * Sección Posts
 */

_::define_controller('novatos', function(){
    
    // Si está cerrado el sistema de novatos
	if(!SECCION_NOBS) { _::redirect('/closed', false); die(); }
    
    // seleccionamos la sección y subsección
	_::$view->assign('section', 'posts');
	_::$view->assign('subsection', 'novatos');
    // ajuste para reutilizar el paginado.
    _::$view->assign('PrePaginatorSeo', 'novatos/');
    // requerimos función para obtener los tops en home
    require('others/homeStats.php');
	
    // no asignamos stickys
	_::$view->assign('sticky', array());
	
    // en qué página estamos?
	if(isset(_::$get['page']))
		$start = _::$get['page']->int()*MAX_POSTS_LIST;
	else
		$start = 0;
		
    // cargamos los post de la sección nub
	$posts = post::getAllObjects('id_post', 'WHERE nub_section = 1 AND sticky = 0  AND borrador = 0 AND revision = 0 ORDER BY id_post DESC LIMIT '.$start.','.MAX_POSTS_LIST);
	_::$view->assign('posts', $posts);
	
	// si hay mas post de los que podemos mostrar y los que se mostraron en paginas anteriores
	if(post::count('id_post', 'WHERE nub_section = 1 AND sticky = 0') > $start+MAX_POSTS_LIST)
	{
		_::$view->assign('NextPage', $start/MAX_POSTS_LIST+1);
		_::$view->assign('ShowNext', true);
	}
	else
		_::$view->assign('ShowNext', false);
	
    // establecemos los contadores estadísticos
	_::$view->assign('countMiembros', usuarios::count('id_usuario'));
	_::$view->assign('countPosts', post::count('id_post'));
	_::$view->assign('countComentarios', comentarios::count('id_comentario'));
	_::$view->assign('uonline', START_USER_COUNT+sesiones::getOnlines());
    
	// ultimos comentarios
	_::$view->assign('LastComments', comentarios::getAllObjects('id_comentario', 'ORDER BY id_comentario DESC LIMIT '.MAX_LIST_HOME_BOXES));
	
    // lista de usuarios en linea
	_::$view->assign('uOnlineList', sesiones::getOnlinesObj());
    
    // obtenemos lo último del catálogo
	_::$view->assign('CatalogObjectsHome', catalogo_objeto::getAllObjects('id_objeto', 'ORDER BY id_objeto DESC LIMIT 2'));

    // generamos los tops del home
	$stats = getHomeStats(STATS_HOME_HISTORICAL, STATS_HOME_TIMESPECTRO);
	_::$view->assign('statsPost', $stats['posts']);
	_::$view->assign('statsUsers', $stats['users']);
    // fin de los tops del home
	
    // obtenemos la lista de tags
	_::$view->assign('listTags', tags::getRandomTags(HOME_CANTIDAD_TAGS));
	
    // cargamos la vista asociada
	_::$view->show('home');
});