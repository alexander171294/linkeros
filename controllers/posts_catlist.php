<?php class_exists('_') || die('FORBIDDEN');

/* CONTROLLER CATEGORIA_LISTA / categoria
 * Sección Posts
 */

_::define_controller('categoria_lista', function(){
    
    // El sistema de categorías está funcionando?
	if(!SECCION_CATEGORIAS) {
        _::redirect('closed');
        return false;
    }
    
    // elegimos la sección y subsección
	_::$view->assign('section', 'posts');
	_::$view->assign('subsection', 'home');
    // requerimos esto para poder hacer el top del home
    require('others/homeStats.php');
	
	// el id de la categoría
	$idCategory = _::$get['id']->int();
    
    // corrección del paginado
    _::$view->assign('PrePaginatorSeo', 'categoria/'.$idCategory.'/');
	
    // no hay sticky
	_::$view->assign('sticky', array());
	
    // hay paginado?
	if(isset(_::$get['page']))
		$start = _::$get['page']->int()*MAX_POSTS_LIST;
	else
		$start = 0;
		
    // obtenemos los posts de esta categoría
	$posts = post::getAllObjects('id_post', 'WHERE nub_section = 0 AND sticky = 0 AND categoria = ? AND borrador = 0 AND revision = 0 ORDER BY id_post DESC LIMIT '.$start.','.MAX_POSTS_LIST, array($idCategory));
	_::$view->assign('posts', $posts);
	
    // contar estadísticas
	_::$view->assign('countMiembros', usuarios::count('id_usuario'));
	_::$view->assign('countPosts', post::count('id_post'));
	_::$view->assign('countComentarios', comentarios::count('id_comentario'));
    _::$view->assign('uonline', START_USER_COUNT+sesiones::getOnlines());
	
	// si hay mas post de los que podemos mostrar y los que se mostraron en paginas anteriores
	if(post::count('id_post', 'WHERE nub_section = 0 AND sticky = 0 AND categoria = ?', array($idCategory)) > $start+MAX_POSTS_LIST)
	{
		_::$view->assign('NextPage', $start/MAX_POSTS_LIST+1);
		_::$view->assign('ShowNext', true);
	}
	else
		_::$view->assign('ShowNext', false);
	
	// ultimos comentarios
	_::$view->assign('LastComments', comentarios::getAllObjects('id_comentario', 'ORDER BY id_comentario DESC LIMIT '.MAX_LIST_HOME_BOXES));
	
	// lista de usuarios online
	_::$view->assign('uOnlineList', sesiones::getOnlinesObj());
    
    // últimos objetos en catalogo
	_::$view->assign('CatalogObjectsHome', catalogo_objeto::getAllObjects('id_objeto', 'ORDER BY id_objeto DESC LIMIT 2'));

    // TOP DEL HOME //
	$stats = getHomeStats(STATS_HOME_HISTORICAL, STATS_HOME_TIMESPECTRO);
	_::$view->assign('statsPost', $stats['posts']);
	_::$view->assign('statsUsers', $stats['users']);
    // FIN TOP HOME //
	
    // Lista de tags random
	_::$view->assign('listTags', tags::getRandomTags(HOME_CANTIDAD_TAGS));
	
    // cargamos la plantilla necesaria
	_::$view->show('home');
});