<?php

_::define_controller('puntuar_post', function(){
	if(!SYSTEM_PUNTOS || !isset(_::$globals['me'])){
		_::$view->ajax(array('error' => true));
		return false;
	}
	$pid = _::$post['id']->int();
	$puntos = _::$post['puntos']->int();
	_e::set('PUNTUAR_POST_INEXISTENTE', 'El post no existe');
	_e::set('PUNTUAR_EXAGERADO', 'No puedes puntuar esta cantidad a un post.');
	_e::set('PUNTUAR_INSUFICIENTES', 'No tienes puntos suficientes.');
	_e::set('PUNTUAR_NUEVAMENTE', 'No puedes puntuar dos veces lo mismo.');
	try{
		$post = new post($pid);
		if($post->void) throw new Exception(_e::get('PUNTUAR_POST_INEXISTENTE'));
		if($puntos>10 || $puntos<1) throw new Exception(_e::get('PUNTUAR_EXAGERADO'));
		if($puntos>_::$globals['me']->puntos_disponibles) throw new Exception(_e::get('PUNTUAR_INSUFICIENTES'));
		if(puntos_post::puntue(_::$globals['me']->id_usuario, $post->id_post)) throw new Exception(_e::get('PUNTUAR_NUEVAMENTE'));
		_::$globals['me']->puntos_disponibles -= $puntos;
		_::$globals['me']->save();
		$post->puntos += $puntos;
		$post->save();
		$index = new puntos_post();
		$index->id_post = $pid;
		$index->id_usuario = _::$globals['me']->id_usuario;
		$index->cantidad = $puntos;
		$index->fecha = time();
		$index->save();
		$autor = new usuarios($post->id_usuario);
		$autor->puntos_obtenidos += $puntos;
		$autor->save();
		_::$view->ajax(array('error' => false));
	} catch(Exception $e) {
		_::$view->ajax_plain($e);
	}
});

_::define_controller('make_draft',function(){
    $id = _::$post['id']->int();
    $post = new post($id);
    if(isset(_::$globals['me']) && (_::$globals['me']->isMod() || _::$globals['me']->id_usuario == $post->id_usuario))
    {
        $post->borrador = 1;
        $post->save();
        if(_::$globals['me']->isMod())
        {
            $post->revision = 0;
            $post->save();
            
            
            $history = new mod_history();
            $history->tipo_target = 1; // post
            $history->id_target = $id;
            $history->accion = 1; // movido a borrador
            $history->id_moderador = _::$globals['me']->id_usuario;
            $history->original_title = $post->titulo;
            $history->fecha = time();
            $history->save();
            
            $pts = new puntos_moderadores();
            $pts->id_post = $id;
            $pts->id_moderador = _::$globals['me']->id_usuario;
            $pts->fecha = time();
            $pts->puntos = TOUR_PUNTOS_BORRADOR;
            $pts->accion = 3; // borrador
            $pts->save();
        }
        _::$view->ajax(array('status' => true));
    } else {
        _::$view->ajax(array('status' => false));
    }
});
_::define_controller('make_undraft',function(){
    $id = _::$post['id']->int();
    $post = new post($id);
    if(isset(_::$globals['me']) && (_::$globals['me']->isMod() || _::$globals['me']->id_usuario == $post->id_usuario))
    {
        $post->borrador = 0;
        $post->save();
        _::$view->ajax(array('status' => true));
    } else {
        _::$view->ajax(array('status' => false));
    }
});
_::define_controller('make_unsuspend',function(){
    $id = _::$post['id']->int();
    $post = new post($id);
    if(isset(_::$globals['me']) && (_::$globals['me']->isMod()))
    {
        $post->revision = 0;
        $post->save();
        _::$view->ajax(array('status' => true));
    } else {
        _::$view->ajax(array('status' => false));
    }
});
_::define_controller('delete_post',function(){
    $id = _::$post['id']->int();
    $post = new post($id);
    if(isset(_::$globals['me']) && (_::$globals['me']->isAdmin() || _::$globals['me']->isMod() || _::$globals['me']->id_usuario == $post->id_usuario))
    {
        $post->delete();
        
        // BORRAR TODOS LOS COMENTARIOS DE ESTE POST
        $comentarios = _::factory(comentarios::getAll('WHERE id_post = ?', array($id)), 'id_comentario', 'comentarios');
        foreach($comentarios as $comentario)
        {
            // BORRAR LIKES DE COMENTARIOS
            $pts_comentarios = _::factory(puntos_comentarios::getAll('WHERE id_comentario = ?', array($comentario->id_comentario)), array('id_comentario', 'id_usuario'), 'puntos_comentarios');
            foreach($pts_comentarios as $pts)
            {
                $pts->delete();
            }
            $comentario->delete();
        }
        
        // BORRAR TODOS LOS PUNTOS DEL POST
        
        puntos_post::deleteAll('WHERE id_post = ?', array($id));
        
        // BORRAR FAVORITOS DEL POST
        favoritos_post::deleteAll('WHERE id_post = ?', array($id));

        // BORRAR SEGUIDORES DEL POST
        seguidores_post::deleteAll('WHERE id_post = ?', array($id));
        
        visitantes_post::deleteAll('WHERE id_post = ?', array($id));
        
        // BORRAR DENUNCIAS DEL POST
        post_reportes::deleteAll('WHERE id_post = ?', array($id));
        
        // BORRAR asociacion de tags
        tags_post::deleteAll('WHERE id_post = ?', array($id));
        
        if(_::$globals['me']->isMod())
        {
            $history = new mod_history();
            $history->tipo_target = 1; // post
            $history->id_target = $post->id_post;
            $history->accion = 3; // movido a borrador
            $history->id_moderador = _::$globals['me']->id_usuario;
            $history->original_title = $post->titulo;
            $history->fecha = time();
            $history->save();
            
            $pts = new puntos_moderadores();
            $pts->id_post = $id;
            $pts->id_moderador = _::$globals['me']->id_usuario;
            $pts->fecha = time();
            $pts->puntos = TOUR_PUNTOS_BORRAR;
            $pts->accion = 2; // borrar
            $pts->save();
        }
        
        $notify = new notificaciones();
        $notify->id_usuario = $post->id_usuario;
        $notify->id_actor = _::$globals['me']->id_usuario;
        $notify->id_target = $post->id_post;
        $notify->tipo_accion = 8;
        $notify->fecha = time();
        $notify->visto = 0;
        $notify->save();
        
        // borrar en catalogo
        catalogo_sugerencias::deleteAll('WHERE id_post = ?', array($post->id_post));
        catalogo_reportes::deleteAll('WHERE id_post = ?', array($post->id_post));
        if($post->o_categoria->is_multiple)
        {
            catalogo_index_multiple::deleteAll('WHERE id_post = ?', array($post->id_post));
        } else {
            $obj = catalogo_objeto::getObject($post->id_post, $post->o_categoria->id_categoria);
            $obj->delete();
        }
        // fin borrar en catálogo
        
        _::$view->ajax(array('status' => true));
    } else {
        _::$view->ajax(array('status' => false));
    }
});
_::define_controller('sticky_post',function(){
    $id = _::$post['id']->int();
    $post = new post($id);
    if(isset(_::$globals['me']) && _::$globals['me']->isAdmin())
    {
        $post->sticky = 1;
        $post->save();
        
        $notify = new notificaciones();
        $notify->id_usuario = $post->id_usuario;
        $notify->id_actor = _::$globals['me']->id_usuario;
        $notify->id_target = $post->id_post;
        $notify->tipo_accion = 7;
        $notify->fecha = time();
        $notify->visto = 0;
        $notify->save();
        
        if(_::$globals['me']->isMod())
        {
            $history = new mod_history();
            $history->tipo_target = 1; // post
            $history->id_target = $post->id_post;
            $history->accion = 4; // movido a borrador
            $history->id_moderador = _::$globals['me']->id_usuario;
            $history->original_title = $post->titulo;
            $history->fecha = time();
            $history->save();
        }
        
        _::$view->ajax(array('status' => true));
    } else {
        _::$view->ajax(array('status' => false));
    }
});
_::define_controller('unsticky_post',function(){
    $id = _::$post['id']->int();
    $post = new post($id);
    if(isset(_::$globals['me']) && _::$globals['me']->isAdmin())
    {
        $post->sticky = 0;
        $post->save();
        _::$view->ajax(array('status' => true));
    } else {
        _::$view->ajax(array('status' => false));
    }
});
_::define_controller('edit_post',function(){
    _::$view->assign('section', 'posts');
    _::$view->assign('subsection', null);
    $id = _::$get['id']->int();
    $post = new post($id);
    if(isset(_::$globals['me']) && (_::$globals['me']->isAdmin() || _::$globals['me']->isMod() || _::$globals['me']->id_usuario == $post->id_usuario) && !$post->void)
    {
        _::$view->assign('emoticonos', emoticonos::getAllObjects('id_emoticono'));
        _::$view->assign('error', null);
        _::$view->assign('error_location', 0);
        _::$view->assign('titulo', $post->titulo);
        _::$view->assign('contenido', $post->contenido);
        _::$view->assign('cerrados', $post->comentarios == 0);
        _::$view->assign('registrados', $post->publico == 0);
        $tags = _::factory(tags_post::getAll('WHERE id_post = ?', array($post->id_post)), 'id_tag', 'tags');
        foreach($tags as $tag)
            $tagsOUT[] = $tag->texto_tag;
        _::$view->assign('tags', trim(implode(', ',$tagsOUT),','));
        _::$view->assign('category', $post->categoria);
        if(_::$isPost)
        {
            try{
                
                if(_::$post['titulo']->words()<2)
                {
                    _::$view->assign('error_location', 1);
                    throw new Exception('El titulo debe tener al menos 2 palabras');
                }
                if(_::$post['contenido']->words()<5)
                {
                    _::$view->assign('error_location', 2);
                    throw new Exception('El contenido debe tener al menos 5 palabras');
                }
                $tags = (string)_::$post['tags'];
                $tags = explode(',', $tags);
                $tags = array_filter($tags);
                $tags = array_map('trim', $tags);
                if(count($tags)<3)
                {
                    _::$view->assign('error_location', 3);
                    throw new Exception('Debes ingresar al menos 3 tags');
                }
                if(!isset(_::$post['categoria']) || _::$post['categoria']->int() < 1)
                {
                    _::$view->assign('error_location', 4);
                    throw new Exception('Debes seleccionar la categoría');
                }
                $closed = isset(_::$post['closed']);
                $register = isset(_::$post['register']);
                $nub = false;
                if(_::$globals['me']->id_rango == 1) $nub = true;
                
                $post = new post($id);
                $post->titulo = (string)_::$post['titulo'];
                $post->contenido = strip_tags((string)_::$post['contenido']->noParseBR());
                $post->categoria = _::$post['categoria']->int();
                $post->comentarios = $closed ? 0 : 1;
                $post->publico = $register ? 0 : 1;
                $post->puntos = 0;
                $post->visitas = 0;
                $post->favoritos = 0;
                $post->seguidores = 0;
                $post->borrador = (int)isset(_::$post['borrador']);
                //$post->nub_section = (int)$nub;
                //$post->id_usuario = _::$globals['me']->id_usuario;
                $post->patrocinado = 0;
                //$post->sticky = 0;
                $post->fecha_publicacion = time();
                
                // forzamos a recachear
                $post->cached_bbc = null;
                
                $post->save();
                
                if(_::$globals['me']->isMod())
                {
                    $history = new mod_history();
                    $history->tipo_target = 1; // post
                    $history->id_target = $post->id_post;
                    $history->accion = 2; // movido a borrador
                    $history->id_moderador = _::$globals['me']->id_usuario;
                    $history->original_title = $post->titulo;
                    $history->fecha = time();
                    $history->save();
                    
                    
                }
                
                tags_post::deleteAll('WHERE id_post = ?', array($post->id_post));
                // los tags
                foreach($tags as $tag)
                {
                    $tagI = tags::getExists($tag);
                    if($tagI == false)
                    {
                        $tagO = new tags();
                        $tagO->texto_tag = $tag;
                        $tagO->repeticiones = 0;
                        $tagI = $tagO->save();
                    }
                    $index = new tags_post();
                    $index->id_post = $post->id_post;
                    $index->id_tag = $tagI;
                    $index->save();
                }
               
                /* ESTO NO ES UN POST NUEVO
                _::$globals['me']->post_creados++;
                _::$globals['me']->save();*/
                
                _::$view->assign('post', $post);
                if(isset(_::$post['borrador']))
                    _::$view->show('editar_borrador_ok');
                else
                    _::$view->show('editar_post_ok');
                return true;
            } catch(Exception $e){
                _::$view->assign('titulo', (string)_::$post['titulo']);
                _::$view->assign('contenido', (string)_::$post['contenido']);
                _::$view->assign('error', $e->getMessage());
            }
        }
        _::$view->assign('post', $post);
        _::$view->show('edit_post');
    } else {
        _::redirect('/');
    }
});

_::define_controller('block_content', function(){
    
    $idp = _::$request['post']->int();
    // chequear el captcha
    $post = new post($idp);
    $out = '';
    if(!$post->void)
    {
		if(!isset(_::$globals['me']))
		{
			$recaptcha = (string)_::$post['captcha'];
			_::declare_component('curl');
			$r = curlPost('https://www.google.com/recaptcha/api/siteverify', array('secret' => RECAPTCHA_SECRET, 'response' => $recaptcha));
			if(json_decode($r)->success !== true)
			{
				$out = '["El captcha no es valido"]';
			} else {
				$CB = array();
				$post->parseContent($CB, true);
				$out = '[';
				foreach($CB as $cbIteration)
				foreach($cbIteration as $content)
				{
					$out .= '"'.base64_encode($content).'",';
				}
				$out = rtrim($out, ',');
				$out .= ']';
			}
		} else { // bypasseamos el captcha
			$CB = array();
			$post->parseContent($CB, true);
			$out = '[';
			foreach($CB as $cbIteration)
			foreach($cbIteration as $content)
			{
				$out .= '"'.base64_encode($content).'",';
			}
			$out = rtrim($out, ',');
			$out .= ']';
		}
        
    } else { $out = 'Post inexistente'; }
    _::$view->ajax_plain($out);
});

_::define_controller('report', function(){
    if(!SYSTEM_REPORTES) die();
    _e::set('REPORT_NO_LOGIN', 'Debes estar logueado para poder reportar este post');
    _e::set('REPORT_POST_INEX', 'El post que quieres reportar no existe');
    _e::set('REPORT_PREREPORT', 'Ya habías reportado este post anteriormente');
    _e::set('REPORT_RAZON', 'La razón no es válida');
    _e::set('REPORT_MSG', 'Debes aclarar el reporte');
    try {
        if(!isset(_::$globals['me'])) throw new Exception(_e::get('REPORT_NO_LOGIN'));
        $idpost = _::$post['id_post']->int();
        $post = new post($idpost);
        if($post->void) throw new Exception(_e::get('REPORT_POST_INEX'));
        if(post_reportes::prevReported(_::$globals['me']->id_usuario, $idpost)) throw new Exception(_e::get('REPORT_PREREPORT'));
        $razon = _::$post['razon']->int();
        $razon = new razones_reportes($razon);
        if($razon->void) throw new Exception(_e::get('REPORT_RAZON'));
        if(_::$post['message']->len()<3) throw new Exception(_e::get('REPORT_MSG'));
        $report = new post_reportes();
        $report->id_post = $idpost;
        $report->id_usuario = _::$globals['me']->id_usuario;
        $report->id_razon_reporte = $razon->id_razon_reporte;
        $report->mensaje = (string) _::$post['message'];
        $report->fecha = time();
        $report->revisado = 0;
        $report->save();
        $cReportes = post_reportes::count('id_post', 'WHERE id_post = ?', array($idpost));
        if($cReportes >= POST_DENUNCIAS_REVISION)
        {
            $notify = new notificaciones();
            $notify->id_usuario = $post->id_usuario;
            $notify->id_actor = 0;
            $notify->id_target = $idpost;
            $notify->tipo_accion = 9;
            $notify->fecha = time();
            $notify->visto = 0;
            $notify->save();
            $post->revision = 1; // post movido a revisión
            $post->save();
        }
        _::$view->ajax(array('error' => false));
    } catch(Exception $e)
    {
        _::$view->ajax_plain($e->getMessage());
    }
    
});