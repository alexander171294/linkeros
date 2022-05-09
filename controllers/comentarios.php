<?php

if(!SYSTEM_COMENTARIOS) {
        _::redirect('closed');
        return false;
}

_::define_controller('agregar_comentario', function(){
    _e::set('COMENTARIO_LOGIN', 'Debes estar logueado para comentar');
    _e::set('COMENTARIOS_CLOSED', 'Los comentarios de este post fueron cerrados');
    _e::set('COMENTARIO_NOTRANG', 'No puedes comentar un post de la home');
    _e::set('COMENTARIO_POST_INVALIDO', 'Este post no es valido');
    _e::set('COMENTARIO_CONTENIDO_INVALIDO', 'El comentario debe tener 4 o más caracteres');
    _e::set('COMENTARIO_ANTIFLOOD', 'Debes esperar '.ANTIFLOOD_MAX_COMMENT.' segundos entre un comentario y otro.');
    try{
        if(!isset(_::$globals['me']->id_usuario)) throw new Exception(_e::get('COMENTARIO_LOGIN'));
        $idpost = _::$post['id']->int();
        $post = new post($idpost);
        if($post->comentarios == 0) throw new Exception(_e::get('COMENTARIOS_CLOSED'));
        if($post->nub_section == 0 && _::$globals['me']->id_rango == 1) throw new Exception(_e::get('COMENTARIO_NOTRANG'));
        if($idpost<1) throw new Exception(_e::get('COMENTARIO_POST_INVALIDO'));
        $contenido = trim(strip_tags((string)_::$post['content']));
        if(strlen($contenido) < 4) throw new Exception(_e::get('COMENTARIO_CONTENIDO_INVALIDO'));
        if((int)_::$globals['me']->last_activity_comment > time()-ANTIFLOOD_MAX_COMMENT) throw new Exception(_e::get('COMENTARIO_ANTIFLOOD'));
        $comment = new comentarios();
        $comment->id_usuario = _::$globals['me']->id_usuario;
        $comment->id_moderador = 0;
        $comment->comentario = $contenido;
        $comment->razon_editado = ' ';
        $comment->positivos = 0;
        $comment->negativos = 0;
        $comment->fecha = time();
        $comment->id_post = $idpost;
        $id = $comment->save();
        $comentario = new comentarios($id);
        $post = new post($idpost);
        $post->comentarios_obtenidos++;
        $post->save();
        $objeto = get_object_vars($comentario);
        
        // notificacion para el autor del post:
        // si no soy yo mismo el que comenta y el autor
        if($post->id_usuario != _::$globals['me']->id_usuario)
        {
            $notify = new notificaciones();
            $notify->id_usuario = $post->id_usuario;
            $notify->id_actor = _::$globals['me']->id_usuario;
            $notify->id_target = $post->id_post;
            $notify->tipo_accion = 4;
            $notify->fecha = time();
            $notify->visto = 0;
            $notify->save(); 
        }
        
        // notificación para los seguidores de post
        $seguidores = _::factory(seguidores_post::getAll('WHERE id_post = ?', array($post->id_post)), 'id_usuario', 'usuarios');
        foreach($seguidores as $seguidor)
        {
            // revisar
            // no se me notifica si soy seguidor y el que comento
            if($seguidor->id_usuario == _::$globals['me']->id_usuario) continue;
            $notify = new notificaciones();
            $notify->id_usuario = $seguidor->id_usuario;
            $notify->id_actor = _::$globals['me']->id_usuario;
            $notify->id_target = $comentario->id_post;
            $notify->tipo_accion = 5;
            $notify->fecha = time();
            $notify->visto = 0;
            $notify->save();
        }
        
        
        _::$globals['me']->comentarios_creados++;
        _::$globals['me']->last_activity_comment = time();
        _::$globals['me']->save();
        $objeto['usuario_objeto'] = array('id_usuario' => $objeto['usuario_objeto']->id_usuario, 'avatar'=>$objeto['usuario_objeto']->avatar, 'nick'=>$objeto['usuario_objeto']->nick);
        _::$view->ajax(array('error' => false, 'objeto' => $objeto, 'contenido' => $comentario->parse_content()));
    } catch(Exception $e){
        _::$view->ajax_plain($e->getMessage());
    }
});

_::define_controller('eliminar_comentario', function(){
    try{
        if(!isset(_::$globals['me']->id_usuario)) throw new Exception(0);
        $id = _::$post['id']->int();
        if($id<1) throw new Exception(1);
        $comentario = new comentarios($id);
        if($comentario->void) throw new Exception(2);
        if($comentario->id_usuario != _::$globals['me']->id_usuario && _::$globals['me']->id_rango != 2 && _::$globals['me']->id_rango != 3) throw new Exception(3);
        $post = new post($comentario->id_post);
        $post->comentarios_obtenidos--;
        $post->save();
        $comentario->delete();
        
        // crear notificacion
        $notify = new notificaciones();
        $notify->id_usuario = $comentario->id_usuario;
        $notify->id_actor = _::$globals['me']->id_usuario;
        $notify->id_target = $comentario->id_post;
        $notify->tipo_accion = 3;
        $notify->fecha = time();
        $notify->visto = 0;
        $notify->save();
        
        // borrar asociacion de likes, likes
        $pts_comentarios = _::factory(puntos_comentarios::getAll('WHERE id_comentario = ?', array($comentario->id_comentario)), array('id_comentario', 'id_usuario'), 'puntos_comentarios');
        foreach($pts_comentarios as $pts)
        {
            $pts->delete();
        }
        _::$view->ajax(array('error' => false));
    } catch(Exception $e){
        _::$view->ajax_plain($e->getMessage());
    }
});

_::define_controller('votar_comentario', function(){
    try{
        if(!isset(_::$globals['me']->id_usuario)) throw new Exception(0);
        $id = _::$post['id']->int();
        $tipo = _::$post['tipo']->int();
        if($id<1) throw new Exception(1);
        $comentario = new comentarios($id);
        if($comentario->void) throw new Exception(2);
        if($comentario->id_usuario == _::$globals['me']->id_usuario) throw new Exception(3);
        if(!puntos_comentarios::noLike(_::$globals['me']->id_usuario, $id)) throw new Exception(4);
        if($tipo == 1)
            $comentario->positivos++;
        else
            $comentario->negativos++;
        $index = new puntos_comentarios();
        $index->id_usuario = _::$globals['me']->id_usuario;
        $index->id_comentario = $id;
        $index->tipo = $tipo;
        $index->fecha = time();
        $index->save();
        $comentario->save();
        
        _::$view->ajax(array('error' => false));
    } catch(Exception $e){
        _::$view->ajax_plain($e->getMessage());
    }
});

_::define_controller('actualizar_comentario', function(){
    _e::set('COMENTARIO_LOGIN', 'Debes estar logueado para comentar');
    _e::set('COMENTARIO_POST_INVALIDO', 'Este post no es valido');
    _e::set('COMENTARIO_CONTENIDO_INVALIDO', 'El comentario debe tener 4 o más caracteres');
    _e::set('COMENTARIO_NEED_ADMIN', 'Solo puedes editar tus comentarios');
    _e::set('COMENTARIO_INEXISTENTE', 'El comentario que quieres editar no existe');
    try{
        if(!isset(_::$globals['me']->id_usuario)) throw new Exception(_e::get('COMENTARIO_LOGIN'));
        $idcomment = _::$post['id']->int();
        if($idcomment<1) throw new Exception(_e::get('COMENTARIO_POST_INVALIDO'));
        $contenido = _::$post['content'];
        if($contenido->len() < 4) throw new Exception(_e::get('COMENTARIO_CONTENIDO_INVALIDO'));
        $comment = new comentarios($idcomment);
        if($comment->void) throw new Exception(_e::get('COMENTARIO_INEXISTENTE'));
        if($comment->id_usuario !== _::$globals['me']->id_usuario && !_::$globals['me']->isAdmin() && !_::$globals['me']->isMod())
        throw new Exception(_e::get('COMENTARIO_NEED_ADMIN'));

        $comment->comentario = trim(strip_tags((string)$contenido));
        $comment->id_moderador = _::$globals['me']->id_usuario;
        $comment->razon_editado = (string)_::$post['razon'];
        $comment->cached_bbc = null;
        $comment->save();
        
        _::$view->ajax(array('error' => false));
    } catch(Exception $e){
        _::$view->ajax_plain($e->getMessage());
    }
});

_::define_controller('reiniciar_comentario', function(){
        if(isset(_::$globals['me']) && _::$globals['me']->isMod())
        {
                $idcomment = _::$post['id']->int();
                $comment = new comentarios($idcomment);
                $comment->negativos = 0;
                $comment->positivos = 0;
                $comment->save();
                _::$view->ajax(array('error' => false));
        } return false;
});