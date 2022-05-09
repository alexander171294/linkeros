<?php class_exists('_') || die('FORBIDDEN');

/* CONTROLLER AGREGAR
 * Sección Posts
 */

_::define_controller('agregar', function(){
    // se pueden agregar posts?
	if(!SECCION_AGREGAR_POST) {
        _::redirect('closed');
        return false;
    }
    // estoy logueado?
	if(!isset(_::$globals['me'])){
		_::redirect('/e403', false);
		return false;
	}
    // seleccionamos la sección
    _::$view->assign('section', 'posts');
	_::$view->assign('subsection', 'add_post');
    
    // inicializamos los mensajes de errores
	_::$view->assign('error', null);
	_::$view->assign('error_location', 0);
	_::$view->assign('titulo', null);
	_::$view->assign('contenido', null);
	_::$view->assign('tags', null);
	_::$view->assign('categoria', null);
    // obtenemos los emoticonos
	_::$view->assign('emoticonos', emoticonos::getAllObjects('id_emoticono'));
	
	if(_::$isPost)// envié el formulario?
	{
		try{
            // si no pasó el filtro de flood
			if((int)_::$globals['me']->last_activity_post > time()-ANTIFLOOD_MAX_POST)
			{
                // mostramos el error
				_::$view->assign('error_location', 1);
				$pending = (((int)_::$globals['me']->last_activity_post)-(time()-ANTIFLOOD_MAX_POST));
				throw new Exception('Debe esperar '.$pending.' segundos antes de publicar otro post');
                security::advertir('Agregar post', 'Flood: pending '.$pending.'sec.', _::$globals['me']->id_usuario);
			}
            // el titulo tiene menos de dos palabras?
			if(_::$post['titulo']->words()<2)
			{
				_::$view->assign('error_location', 1);
				throw new Exception('El titulo debe tener al menos 2 palabras');
			}
            // el contenido tiene menos de 5 palabras?
			if(_::$post['contenido']->words()<5)
			{
				_::$view->assign('error_location', 2);
				throw new Exception('El contenido debe tener al menos 5 palabras');
			}
            // obtenemos los tags
			$tags = (string)_::$post['tags'];
            // los separamos por coma
			$tags = explode(',', $tags);
            // eliminamos los nulos
			$tags = array_filter($tags);
            // quitamos espacios vacíos
			$tags = array_map('trim', $tags);
			// quedaron menos de 3 tags?
			if(count($tags)<3)
			{
				_::$view->assign('error_location', 3);
				throw new Exception('Debes ingresar al menos 3 tags');
			}
            // no ingresó la categoría o es inferior a 1?
			if(!isset(_::$post['categoria']) || _::$post['categoria']->int() < 1)
			{
				_::$view->assign('error_location', 4);
				throw new Exception('Debes seleccionar la categoría');
			}
            
            //////// SECURITY PATCH #001 /////////
            // Date: 17:20 23/02/2017
            // Security Issue: categoría inexistente
            $categoria = new categorias(_::$post['categoria']->int());
            if($categoria->void)
            {
                _::$view->assign('error_location', 4);
				security::advertir('Agregar post', 'Categoria inexistente NRO:'._::$post['categoria']->int(), _::$globals['me']->id_usuario);
				throw new Exception('Error, categoría inexistente');
            }
            /////////////////////////////////
            
            // requiere registro y comentarios cerrados
			$closed = isset(_::$post['closed']);
			$register = isset(_::$post['register']);
            
            // va en la sección nub?
			$nub = false;
			if(_::$globals['me']->id_rango == 1) $nub = true;
			
            // creamos el nuevo post
			$post = new post();
			$post->titulo = (string)_::$post['titulo'];
			$post->contenido = strip_tags((string)_::$post['contenido']->noParseBR());
			$post->categoria = $categoria->id_categoria;
			$post->comentarios = $closed ? 0 : 1;
			$post->publico = $register ? 0 : 1;
			$post->puntos = 0;
			$post->visitas = 0;
			$post->favoritos = 0;
			$post->seguidores = 0;
			$post->nub_section = (int)$nub;
			$post->id_usuario = _::$globals['me']->id_usuario;
			$post->patrocinado = 0;
			$post->sticky = 0;
			$post->fecha_publicacion = time();
			$post->borrador = (int)isset(_::$post['borrador']);
			$post->comentarios_obtenidos = 0;
			$post->revision = 0;
            // guardamos el post
			$idp = $post->save();

			// creamos los tags
			foreach($tags as $tag)
			{
                // verificar existencia del tag
				$tagI = tags::getExists($tag);
                // si no existe lo creamos
				if($tagI === false)
				{
					$tagO = new tags();
					$tagO->texto_tag = $tag;
					$tagO->repeticiones = 1;
					$tagI = $tagO->save();
				} else { // si existe lo utilizamos
					$tagO = new tags($tagI);
					$tagO->repeticiones++;
					$tagO->save();
				}
                // asociamos el tag al post
				$index = new tags_post();
				$index->id_post = $idp;
				$index->id_tag = $tagI;
                // guardamos la asociación
				$index->save();
			}
			
            // cambiamos nuestra cantidad de post creados
			_::$globals['me']->post_creados++;
            // cambiamos la actividad del último post (por filtro antiflood)
			_::$globals['me']->last_activity_post = time();
            // guardamos
			_::$globals['me']->save();
			
            // recreamos el post previo
			$post = new post($idp);
			
			// enviar notificacion a mis seguidores de que creé un post
			$seguidores = seguidores_usuarios::getAllObjects('id_seguidor', 'WHERE id_usuario = ?', array( _::$globals['me']->id_usuario));
			foreach($seguidores as $seguidor)
			{
				$notify = new notificaciones();
				$notify->id_usuario = $seguidor->id_usuario;
				$notify->id_actor = _::$globals['me']->id_usuario;
				$notify->id_target = $post->id_post;
				$notify->tipo_accion = 6;
				$notify->fecha = time();
				$notify->visto = 0;
				$notify->save();
			}
			
            // asignamos el post (para el botón)
			_::$view->assign('post', $post);
            // si era en borrador
			if(isset(_::$post['borrador']))
				_::$view->show('agregar_borrador_ok');
			else
				_::$view->show('agregar_ok');
            // terminamos
			return true;
		} catch(Exception $e){
            // oh no!, ocurrió un error y esto lo atrapa para mostrarlo.
			_::$view->assign('titulo', (string)_::$post['titulo']);
			_::$view->assign('contenido', (string)_::$post['contenido']->noParseBR());
			_::$view->assign('GLBcategoria', isset(_::$post['categoria']) ? _::$post['categoria']->int() : 0);
			_::$view->assign('tags', (string)_::$post['tags']);
			_::$view->assign('error', $e->getMessage());
		}
	}
    // levantamos la vista asociada
	_::$view->show('agregar');
});