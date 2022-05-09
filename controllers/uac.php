<?php

_::define_autocall(function(){
    if(!isset(_::$globals['me']) || (_::$globals['me']->id_rango != 2 && _::$globals['me']->id_rango != 3)) {
        _::redirect('/',false);
        die();
    }
});

_::define_controller('uac_drop', function(){
    // vaciar usuario
    $user = new usuarios(_::$post['idu']->int());
    if(!_::$globals['me']->isAdmin() || $user->isMod()) {
        _::redirect('/',false);
        die();
    }
    $comments = comentarios::getAll('WHERE id_usuario = ?', array($user->id_usuario));
    foreach($comments as $comment)
    {
        puntos_comentarios::deleteAll('WHERE id_comentario = ?', array($comment['id_comentario']));
    }
    comentarios::deleteAll('WHERE id_usuario = ?', array($user->id_usuario));
    favoritos_post::deleteAll('WHERE id_usuario = ?', array($user->id_usuario));
    mensajes_enviados::deleteAll('WHERE id_emisor = ?', array($user->id_usuario));
    mensajes_recibidos::deleteAll('WHERE id_receptor = ?', array($user->id_usuario));
    notificaciones::deleteAll('WHERE id_usuario = ?', array($user->id_usuario));
    perfiles::deleteAll('WHERE id_usuario = ?', array($user->id_usuario));
    users_reportes::deleteAll('WHERE id_profile = ?', array($user->id_usuario));
    users_reportes::deleteAll('WHERE id_usuario = ?', array($user->id_usuario));
    $posts = post::getAllObjects('id_post', 'WHERE id_usuario = ?', array($user->id_usuario));
    visitantes_post::deleteAll('WHERE id_usuario = ?', array($user->id_usuario));
    foreach($posts as $post)
    {
        post_reportes::deleteAll('WHERE id_post = ?', array($post->id_post));
        puntos_post::deleteAll('WHERE id_post = ?', array($post->id_post));
        seguidores_post::deleteAll('WHERE id_post = ?', array($post->id_post));
        tags_post::deleteAll('WHERE id_post = ?', array($post->id_post));
        visitantes_post::deleteAll('WHERE id_post = ?', array($post->id_post));
        $post->delete();
    }
    seguidores_usuarios::deleteAll('WHERE id_usuario = ?', array($user->id_usuario));
    seguidores_usuarios::deleteAll('WHERE id_seguidor = ?', array($user->id_usuario));
    $user->puntos_obtenidos = 0;
    $user->puntos_disponibles = 0;
    $user->post_creados = 0;
    $user->comentarios_creados = 0;
    $user->seguidores = 0;
    $user->siguiendo = 0;
    $user->mensaje_perfil = ' ';
    $user->avatar = DEFAULT_AVATAR_LINK;
    $user->nfu_desde = 0;
    $user->last_activity_post = time();
    $user->last_activity_comment = time();
    $user->last_activity_mp = time();
    $user->nombre = ' ';
    $user->save();
    
    _::$view->ajax(array('success' => true));
    
});

_::define_controller('uac_password', function(){
    $user = new usuarios(_::$get['page']->int());
    if($user->isAdmin()) {
        _::redirect('/',false);
        die();
    }
    _::$view->assign('section', null);
    _::$view->assign('subsection', 'account_password');
    _::$view->assign('theuser', $user->id_usuario);
    _::$view->assign('error', false);
    _::$view->assign('errorObject', null);
    _::$view->assign('saved', false);
    $me = $user;
    if(_::$isPost)
    {
        $nueva[0] = (string)_::$post['new_pass'];
        $nueva[1] = (string)_::$post['new_pass2'];
        _e::set('NEWPASS_VIEJA_INVALIDA', 'La contraseña actual no es correcta');
        _e::set('NEWPASS_NUEVA_INVALIDA', 'La contraseña nueva debe tener al menos 8 caracteres');
        _e::set('NEWPASS_NUEVA_COINCIDENCIA', 'Las contraseñas no coinciden');
        try{
            if(_::$post['new_pass']->len() < 8) throw new Exception(_e::get('NEWPASS_NUEVA_INVALIDA'));
            if($nueva[0] !== $nueva[1]) throw new Exception(_e::get('NEWPASS_NUEVA_COINCIDENCIA'));
            // realizar el cambio
            $me->password = (string)_::$post['new_pass']->hash();
            $me->save();
            _::$view->assign('saved', true);
        } catch(Exception $e) {
            _::$view->assign('error', true);
            // tirar error y ponerlo en el cartel correspondiente
            _::$view->assign('errorObject', json_decode($e->getMessage()));
        }
    }
    _::$view->show('otros/uac_account_menu');
    _::$view->show('account_password');
});

_::define_controller('uac_profile', function(){
    
    $user = new usuarios(_::$get['page']->int());
    if($user->isAdmin()) {
        _::redirect('/',false);
        die();
    }
    
    _::$view->assign('section', null);
    _::$view->assign('theuser', $user->id_usuario);
    _::$view->assign('subsection', 'account');
    _::$view->assign('error', false);
    _::$view->assign('errorObject', null);
    _::$view->assign('saved', false);
    
    _::$view->assign('paises', _::factory(paises::getAll(), 'id_pais', 'paises'));
    
    $me = $user;
    _::$view->assign('nombre', $me->nombre);
    _::$view->assign('Apais', $me->pais);
    _::$view->assign('region', $me->region);
    _::$view->assign('sexo', $me->sexo);
    
    _::$view->assign('dia_nac', $me->dia_n);
    _::$view->assign('mes_nac', $me->mes_n);
    _::$view->assign('anio_nac', $me->anio_n);
    
    $meses = array('void', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
    unset($meses[0]);
    _::$view->assign('meses', $meses);
    
    if(_::$isPost)
    {
        _e::set('EDIT_ACCOUNT_NOMBRE', 'El nombre no es valido');
        _e::set('EDIT_ACCOUNT_EMAIL', 'El email no es valido');
        _e::set('EDIT_ACCOUNT_PAIS', 'El pais no es valido');
        _e::set('EDIT_ACCOUNT_REGION', 'La region no es valida');
        _e::set('EDIT_ACCOUNT_SEXO', 'El genero elegido no es valido');
        _e::set('EDIT_ACCOUNT_FECHA_NAC', 'La fecha de nacimiento no es valida');
        _::$view->assign('nombre', (string)_::$post['nombre']);
        _::$view->assign('Apais', _::$post['pais']->int());
        _::$view->assign('region', (string)_::$post['region']);
        _::$view->assign('sexo', _::$post['sexo']->int());
        
        _::$view->assign('dia_nac', _::$post['dia']->int());
        _::$view->assign('mes_nac', _::$post['mes']->int());
        _::$view->assign('anio_nac', _::$post['anio']->int());
        try{
            if(_::$post['nombre']->len()>0)
            {
                if(_::$post['nombre']->len()<4) throw new Exception(_e::get('EDIT_ACCOUNT_NOMBRE'));
                $me->nombre = (string)_::$post['nombre'];
            }
            if(_::$post['pais']->int() < 1) throw new Exception(_e::get('EDIT_ACCOUNT_PAIS'));
            $me->pais = _::$post['pais']->int();
            if(_::$post['region']->len()>0)
            {
                if(_::$post['region']->len()<3) throw new Exception(_e::get('EDIT_ACCOUNT_REGION'));
                $me->region = (string)_::$post['region'];
            }
            if(_::$post['sexo']->int() < 1 || _::$post['sexo']->int() > 2) throw new Exception(_e::get('EDIT_ACCOUNT_SEXO'));
            $me->sexo = _::$post['sexo']->int();
            
            $fecha_dia = _::$post['dia']->int();
            $fecha_mes = _::$post['mes']->int();
            $fecha_anio = _::$post['anio']->int();
            if($fecha_dia<1 || $fecha_dia>31) throw new Exception(_e::get('EDIT_ACCOUNT_FECHA_NAC'));
            $me->dia_n = $fecha_dia;
            if($fecha_mes<1 || $fecha_mes>12) throw new Exception(_e::get('EDIT_ACCOUNT_FECHA_NAC'));
            $me->mes_n = $fecha_mes;
            if($fecha_anio<1900 || $fecha_mes>2014) throw new Exception(_e::get('EDIT_ACCOUNT_FECHA_NAC'));
            $me->anio_n = $fecha_anio;
            
            $me->save();
            _::$view->assign('saved', true);
        } catch(Exception $e) {
            _::$view->assign('error', true);
            _::$view->assign('errorObject', json_decode($e->getMessage()));
        }
    }
    
    _::$view->show('otros/uac_account_menu');
    _::$view->show('account');
    
});