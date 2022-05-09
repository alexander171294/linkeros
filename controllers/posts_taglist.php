<?php class_exists('_') || die('FORBIDDEN');

/* CONTROLLER TAG_LISTA / tag
 * Sección Posts
 */

_::define_controller('tag_lista', function(){
    // Sección (para el menú)
	_::$view->assign('section', 'posts');
	_::$view->assign('subsection', 'home');
    // necesitamos esto para obtener la funcion generadora de tops
    require('others/homeStats.php');
    
    // obtenemos el id del tag
	$idTag = _::$get['id']->int();
	
	// corregimos link del paginado para reutilizar plantilla
    _::$view->assign('PrePaginatorSeo', 'categoria/'.$idTag.'/');
    
    // no mostramos stickys
	_::$view->assign('sticky', array());
	
    // en qué página estamos?
	if(isset(_::$get['page']))
		$start = _::$get['page']->int()*MAX_POSTS_LIST;
	else
		$start = 0;
		
    // obtenemos los post de este tag
	$posts = tags_post::getAll('id_post', 'WHERE id_tag = ? LIMIT '.$start.','.MAX_POSTS_LIST, array($idTag));
	_::$view->assign('posts', $posts);
	
    // conteo estadístico del home
	_::$view->assign('countMiembros', usuarios::count('id_usuario'));
	_::$view->assign('countPosts', post::count('id_post'));
	_::$view->assign('countComentarios', comentarios::count('id_comentario'));
    _::$view->assign('uonline', START_USER_COUNT+sesiones::getOnlines());
	
	// si hay mas post de los que podemos mostrar y los que se mostraron en paginas anteriores
	if(post::count('id_post', 'WHERE nub_section = 0 AND sticky = 0 AND categoria = ?', array($idTag)) > $start+MAX_POSTS_LIST)
	{
		_::$view->assign('NextPage', $start/MAX_POSTS_LIST+1);
		_::$view->assign('ShowNext', true);
	}
	else
		_::$view->assign('ShowNext', false);
	
	// ultimos comentarios
	_::$view->assign('LastComments',_::factory(comentarios::getAll('ORDER BY id_comentario DESC LIMIT '.MAX_LIST_HOME_BOXES), 'id_comentario', 'comentarios'));
	
    // obtenemos lista de usuarios online
	_::$view->assign('uOnlineList', sesiones::getOnlinesObj());
    
    // obtenemos lo último del catálogo
	_::$view->assign('CatalogObjectsHome', catalogo_objeto::getAllObjects('id_objeto', 'ORDER BY id_objeto DESC LIMIT 2'));

	
	// obtenemos el top del home //
	$stats = getHomeStats(STATS_HOME_HISTORICAL, STATS_HOME_TIMESPECTRO);
	_::$view->assign('statsPost', $stats['posts']);
	_::$view->assign('statsUsers', $stats['users']);
    // fin top del home//
	
    // obtenemos la lista de tags
	_::$view->assign('listTags', tags::getRandomTags(HOME_CANTIDAD_TAGS));
	
	_::$view->show('home');
});