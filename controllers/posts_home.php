<?php class_exists('_') || die('FORBIDDEN');

/* CONTROLLER HOME
 * Sección Posts
 */

_::define_controller('home', function(){
    // Sección (para el menú)
	_::$view->assign('section', 'posts');
	_::$view->assign('subsection', 'home');
    // esto es para reutilizar la misma plantilla agregand previos al paginado
    _::$view->assign('PrePaginatorSeo', null);
    // funciones repetidas en varios controladores:
    require('others/homeStats.php');

    // estoy dentro del paginado?
	if(isset(_::$get['page']))
		$start = _::$get['page']->int()*MAX_POSTS_LIST;
	else
		$start = 0;
        
    // obtener los post pegados
	$posts = post::getAllObjects('id_post', 'WHERE sticky = 1 ORDER BY id_post DESC');
	_::$view->assign('sticky', $posts);
    
    // obtener los posts del home
	$posts = post::getAllObjects('id_post', 'WHERE nub_section = 0 AND sticky = 0 AND borrador = 0 AND revision = 0 ORDER BY id_post DESC LIMIT '.$start.','.MAX_POSTS_LIST);
	_::$view->assign('posts', $posts);

	// si hay mas post de los que podemos mostrar y los que se mostraron en paginas anteriores
	if(post::count('id_post', 'WHERE nub_section = 0 AND sticky = 0') > $start+MAX_POSTS_LIST)
	{
        // próxima página
		_::$view->assign('NextPage', $start/MAX_POSTS_LIST+1);
        // mostrar el "siguiente" en listado.
		_::$view->assign('ShowNext', true);
	}
	else // no hay páginas 
		_::$view->assign('ShowNext', false);
        
    // Estadíticas del home
    // miembros
	_::$view->assign('countMiembros', usuarios::count('id_usuario'));
    // posts
	_::$view->assign('countPosts', post::count('id_post'));
    // comentarios 
	_::$view->assign('countComentarios', comentarios::count('id_comentario'));
    // Cantidad de usuarios en linea
	_::$view->assign('uonline', START_USER_COUNT+sesiones::getOnlines());

	// Últimos comentarios
	_::$view->assign('LastComments', comentarios::getAllObjects('id_comentario', 'ORDER BY id_comentario DESC LIMIT '.MAX_LIST_HOME_BOXES));
	
    // Lista de usuarios online
	_::$view->assign('uOnlineList', sesiones::getOnlinesObj());
    
    // Últimos en catálogo
	_::$view->assign('CatalogObjectsHome', catalogo_objeto::getAllObjects('id_objeto', 'ORDER BY id_objeto DESC LIMIT 2'));
    
    // obtenemos el listado de tags
    _::$view->assign('listTags', tags::getRandomTags(HOME_CANTIDAD_TAGS));

    // cargamos los tops del home
	$stats = getHomeStats(STATS_HOME_HISTORICAL, STATS_HOME_TIMESPECTRO);
	_::$view->assign('statsPost', $stats['posts']);
	_::$view->assign('statsUsers', $stats['users']);
	
    // Noticias del home
	$notificaciones = noticias::getAll();
	$gralnoty = array();
	foreach($notificaciones as $noti)
	{
		$gralnoty[] = $noti['contenido'];
	}
    // pasamos las noticias para javascript
	_::$view->assign('notificaciones_home',json_encode($gralnoty));

	// mostramos la vista home
	_::$view->show('home');
});