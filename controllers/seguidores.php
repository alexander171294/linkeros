<?php // faltan actualizar contadores

if(!SYSTEM_SEGUIDORES) {
        _::redirect('closed');
        return false;
}

_::define_controller('no_seguir_usuario', function(){
    try{
        if(!isset(_::$globals['me'])) throw new Exception('-1');
        if(!seguidores_usuarios::is_follow(_::$globals['me']->id_usuario, _::$post['id']->int())) throw new Exception('0');
        $index = new seguidores_usuarios(array(_::$post['id']->int(), _::$globals['me']->id_usuario));
        $index->delete();
        $idu = _::$post['id']->int();
        $user = new usuarios($idu);
        $user->seguidores--;
        $user->save();
        _::$globals['me']->siguiendo--;
        _::$globals['me']->save();
        _::$view->ajax(array('error' => false));
    } catch(Exception $e) {
        _::$view->ajax(array('error' => true));
    }
});

_::define_controller('seguir_usuario', function(){
    try{
        if(!isset(_::$globals['me'])) throw new Exception('-1');
        if((int)_::$globals['me']->id_usuario == _::$post['id']->int()) throw new Exception('Autofollow isn\'t allowed.');
        if(seguidores_usuarios::is_follow(_::$globals['me']->id_usuario, _::$post['id']->int())) throw new Exception('0');
        $index = new seguidores_usuarios();
        $index->id_usuario = _::$post['id']->int();
        $index->id_seguidor = _::$globals['me']->id_usuario;
        $index->fecha = time();
        $index->save();
        $idu = _::$post['id']->int();
        $user = new usuarios($idu);
        $user->seguidores++;
        $user->save();
        
        // crear notificacion
        $notify = new notificaciones();
        $notify->id_usuario = _::$post['id']->int();
        $notify->id_actor = _::$globals['me']->id_usuario;
        $notify->id_target = _::$post['id']->int();
        $notify->tipo_accion = 1;
        $notify->fecha = time();
        $notify->visto = 0;
        $notify->save();
        
        _::$globals['me']->siguiendo++;
        _::$globals['me']->save();
        _::$view->ajax(array('error' => false));
    } catch(Exception $e) {
        _::$view->ajax(array('error' => true));
    }
});

_::define_controller('no_seguir_post', function(){
    try{
        if(!isset(_::$globals['me'])) throw new Exception('-1');
        if(!seguidores_post::is_follow(_::$globals['me']->id_usuario, _::$post['id']->int())) throw new Exception('0');
        $index = new seguidores_post(array(_::$post['id']->int(), _::$globals['me']->id_usuario));
        $index->delete();
        $post = new post(_::$post['id']->int());
        $post->seguidores--;
        $post->save();
        _::$view->ajax(array('error' => false));
    } catch(Exception $e) {
        _::$view->ajax(array('error' => true));
    }
});

_::define_controller('seguir_post', function(){
    try{
        if(!isset(_::$globals['me'])) throw new Exception('-1');
        if(seguidores_usuarios::is_follow(_::$post['id']->int(), _::$globals['me']->id_usuario)) throw new Exception('0');
        $index = new seguidores_post();
        $index->id_usuario = _::$globals['me']->id_usuario;
        $index->id_post = _::$post['id']->int();
        $index->fecha = time();
        $index->save();
        $post = new post(_::$post['id']->int());
        $post->seguidores++;
        $post->save();
        _::$view->ajax(array('error' => false));
    } catch(Exception $e) {
        _::$view->ajax(array('error' => true));
    }
});

_::define_controller('favorito_post', function(){
    try{
        if(!isset(_::$globals['me'])) throw new Exception('-1');
        if(favoritos_post::is_fav(_::$post['id']->int(), _::$globals['me']->id_usuario)) throw new Exception('0');
        $index = new favoritos_post();
        $index->id_usuario = _::$globals['me']->id_usuario;
        $index->id_post = _::$post['id']->int();
        $index->fecha = time();
        $index->save();
        $post = new post(_::$post['id']->int());
        $post->favoritos++;
        $post->save();
        
        // crear notificacion
        $notify = new notificaciones();
        $notify->id_usuario = $post->id_usuario;
        $notify->id_actor = _::$globals['me']->id_usuario;
        $notify->id_target = _::$post['id']->int();
        $notify->tipo_accion = 2;
        $notify->fecha = time();
        $notify->visto = 0;
        $notify->save();
        
        _::$view->ajax(array('error' => false));
    } catch(Exception $e) {
        _::$view->ajax(array('error' => true));
    }
});

_::define_controller('linkear_post', function(){
        try{
            $idp = _::$post['id']->int();
            if(!isset(_::$globals['me'])) throw new Exception('-1');
            if(linkeo::is_linkeado(_::$globals['me']->id_usuario, $idp)) throw new Exception('0');
            $seguidores = _::factory(seguidores_usuarios::getAll('WHERE id_usuario = ?', array(_::$globals['me']->id_usuario)), 'id_seguidor', 'usuarios');
            foreach($seguidores as $seguidor)
            {
                $notify = new notificaciones();
                $notify->id_usuario = $seguidor->id_usuario;
                $notify->id_actor = _::$globals['me']->id_usuario;
                $notify->id_target = _::$post['id']->int();
                $notify->tipo_accion = 12; // recomendación
                $notify->fecha = time();
                $notify->visto = 0;
                $notify->save();
            }
            $linkeo = new linkeo();
            $linkeo->id_usuario = _::$globals['me']->id_usuario;
            $linkeo->id_post = _::$post['id']->int();
            $linkeo->fecha = time();
            $linkeo->save();
            _::$view->ajax(array('error' => false)); 
        } catch(Exception $e) {
            _::$view->ajax(array('error' => true));
        }
});