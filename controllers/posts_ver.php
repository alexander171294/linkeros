<?php class_exists('_') || die('FORBIDDEN');

/* CONTROLLER VER_POST / post
 * Sección Posts
 */
_::define_controller('ver_post', function(){
	
	// sección posts
	_::$view->assign('section', 'posts');
	
	// obtenemos id del post
	$idpost = _::$get['id']->int();
	// creamos el post
	$post = new post($idpost);
	// está en la sección novatos?
	if($post->nub_section == 1)
		_::$view->assign('subsection', 'novatos');
	else
		_::$view->assign('subsection', 'home');
	
	// el post ya no existe
	if($post->void) { _::redirect('post_404'); return false; }
	// el post es privado
	if($post->publico == 0 && !isset(_::$globals['me'])) { _::redirect('post_403'); return false; }
	// el post se encuentra en revisión
	if($post->revision == 1 && (!isset(_::$globals['me']) || !_::$globals['me']->isMod())) { _::redirect('post_501'); return false; }
	// si se encuentra en borrador
	if($post->borrador == 1 && (!isset(_::$globals['me']) || (!_::$globals['me']->isMod() && _::$globals['me']->id_usuario !== $autor->id_usuario))) { _::redirect('post_404'); return false; }
	
	// obtener los emoticonos
	_::$view->assign('emoticonos', emoticonos::getAllObjects('id_emoticono'));
		
	// estoy logueado
	if(isset(_::$globals['me']))
		$linked = linkeo::is_linkeado(_::$globals['me']->id_usuario, $idpost); // verifico si lo linkie
	else
		$linked = false;
	_::$view->assign('linked_post', $linked);
	
	// asigno el post
	_::$view->assign('post', $post);
	
	// asigno los detalles del autor
	$autor = new usuarios($post->id_usuario);
	_::$view->assign('AOnline', sesiones::isOnline($autor->id_usuario));
	_::$view->assign('autor', $autor);
	_::$view->assign('rango_autor', new rangos($autor->id_rango));
	
	// obtengo los tags del post
	_::$view->assign('tags', _::factory(tags_post::getTags($idpost), 'id_tag', 'tags'));
	
	// obtengo la fecha de publicación del post y la formateo
	$fecha = new _date($post->fecha_publicacion);
	$fecha->fDay('/')->fMonth('/')->fYear();
	_::$view->assign('fechaPost', $fecha->format());
	
	// contamos las visitas si estoy logueado y no lo visité antes	
	if(isset(_::$globals['me']) && !visitantes_post::is_visited(_::$globals['me']->id_usuario, $idpost))
	{
		$post->visitas++;
		$post->save();
		$index = new visitantes_post();
		$index->id_post = $idpost;
		$index->id_usuario = _::$globals['me']->id_usuario;
		$index->fecha = time();
		$index->save();
	}
	
	// ver caché de relacionados
	$relacionados = cache_relateds::getRelateds($post->id_post);
	if(count($relacionados) > 0)
	{
		$relateds = _::factory($relacionados, 'id_related', 'post');
	} else { // recacheamos
		// LOS RELACIONADOS POR CATEGORÍA
		if(!SEARCH_RELATEDS)
		{
			$cat = $post->categoria;
			$results = post::getRand('id_post', 'WHERE categoria = '.$cat, RELATEDS_CANTIDAD);
			$relateds = _::factory($results, 'id_post', 'post');
		} else { // LOS RELACIONADOS POR SIMILITUD DE TITULO
			$query = $post->titulo;
			_::declare_component('searcher');
			$search = new Buscador($query);
			$q = $search->getQuerys();
			foreach($q as $uQ)
			{
				$addCat = null;
				$addAutor = null;
				$out = post::getAll('WHERE titulo LIKE ? LIMIT '.RELATEDS_CANTIDAD, array($uQ));
				$search->merge($out);
				$results = $search->filterQuerys('id_post', RELATEDS_CANTIDAD);
				if(count($results) > RELATEDS_CANTIDAD) break; // si superamos los 10 resultados no hay necesidad de seguir
			}
			$relateds = _::factory($results, 'id_post', 'post');
		}
		// recachear relateds
		cache_relateds::deleteOldCache($post->id_post);
		$lifetime = time()+RECACHEAR_RELATEDS;
		foreach($relateds as $related)
		{
			$cache = new cache_relateds();
			$cache->id_post = $post->id_post;
			$cache->id_related = $related->id_post;
			$cache->fecha_cacheado = $lifetime;
			$cache->save();
		}
	}
	// asignamos los relacionados
	_::$view->assign('relateds', $relateds);

	// tipos reportes
	_::$view->assign('reportes', razones_reportes::getAllObjects('id_razon_reporte'));

	// comentarios
	_::$view->assign('comentarios', _::factory(comentarios::getAllByPost($post->id_post), 'id_comentario', 'comentarios'));

	// catalogos
	// es catalogable?
	_::$view->assign('isCatalogable', $post->o_categoria->in_catalog == 1);
	// está en el catalogo?
	$isInCatalog = catalogo_objeto::isInCatalog($post->id_post, $post->o_categoria);
	// si está en el catálogo y estoy logueado
	if($isInCatalog && isset(_::$globals['me'])) 
	{
		// ¿ya está reportado?
		$reporte = new catalogo_reportes(array(_::$globals['me']->id_usuario, $post->id_post));
		_::$view->assign('noReportedCatalog', $reporte->void);
	}
	// asignamos las variables
	_::$view->assign('inCatalog', $isInCatalog);
	// cantidad de recomendaciones para el catálogo
	_::$view->assign('catalogReco', $post->getRecosCatalog());
	// ya recomende el catálogo?
	_::$view->assign('prevRecommend', $post->prevRecoCatalog());
	// obtenemos el objeto-catálogo
	_::$view->assign('catalogObject', catalogo_objeto::getObject($post->id_post, $post->o_categoria));

	// cargamos la vista asociada
	_::$view->show('post');
});


