<?php

_::define_autocall(function(){
    if(!SECCION_MPS) {
        _::redirect('closed', false);
        die();
    }
});

_::define_controller('mensajes', function(){
    if(!isset(_::$globals['me'])) { _::redirect('e403'); return false; } 
    _::$view->assign('subsection', null);
    _::$view->assign('section', null);
    _::$view->assign('triseccion', 1);
    
    $msg = _::factory(mensajes_recibidos::getMyMPS(_::$globals['me']->id_usuario),'id_mensaje', 'mensajes_recibidos');
    _::$view->assign('mpsList', $msg);
    
    _::$view->show('msg_menu');
    _::$view->show('msg_list_recv');
    
});

_::define_controller('mensajes_enviados', function(){
    if(!isset(_::$globals['me'])) { _::redirect('e403'); return false; } 
    _::$view->assign('subsection', null);
    _::$view->assign('section', null);
    _::$view->assign('triseccion', 2);
    
    $msg = _::factory(mensajes_enviados::getMyMPS(_::$globals['me']->id_usuario),'id_mensaje', 'mensajes_enviados');
    _::$view->assign('mpsList', $msg);
    
    _::$view->show('msg_menu');
    _::$view->show('msg_list');
    
});

_::define_controller('mensajes_nick', function(){
    // solo si estoy logueado
    if(!isset(_::$globals['me'])) die();
    // solo si esta activado el autocompletaod
    if(!AUTOCOMPLETE_PARTIALS) {
        _::$view->ajax(array());
        return false;
    }
    $partial = (string)_::$post['query'];
    $users = usuarios::get_by_partialNick($partial);
    $out = array();
    foreach($users as $user)
    {
        $out[] = $user['nick'];
    }
    _::$view->ajax($out);
});

_::define_controller('mensajes_vaciar', function(){

    if(!isset(_::$globals['me'])) return false;
    
    $tipo = _::$post['tipo']->int();
    
    if($tipo == 1)
    {
        mensajes_recibidos::vaciar(_::$globals['me']->id_usuario);
    } else if($tipo == 2)
    {
        mensajes_enviados::vaciar(_::$globals['me']->id_usuario);
    } else if($tipo == 3)
    {
        mensajes_enviados::vaciar(_::$globals['me']->id_usuario);
        mensajes_recibidos::vaciar(_::$globals['me']->id_usuario);
    }
    
    _::$view->ajax(array('error' => false));
});


_::define_controller('mensajes_nuevo', function(){
    
    _e::set('MP_USER', 'El usuario no existe');
    _e::set('MP_ME', 'No te puedes enviar un mensaje a ti mismo');
    _e::set('MP_ASUNTO', 'El asunto debe tener al menos 3 caracteres');
    _e::set('MP_MENSAJE', 'El mensaje debe tener al menos 7 caracteres');
    _e::set('MP_LOGIN', 'Debes estar logueado para enviar un mp');
    try{
        if(!isset(_::$globals['me'])) throw new Exception(_e::get('MP_LOGIN'));
        if(_::$post['asunto']->len() < 3) throw new Exception(_e::get('MP_ASUNTO'));
        if(_::$post['mensaje']->len() < 7) throw new Exception(_e::get('MP_MENSAJE'));
        $uid = usuarios::get_by_nick((string)_::$post['nick']);
        if(empty($uid)) throw new Exception(_e::get('MP_USER'));
        if($uid == _::$globals['me']->id_usuario) throw new Exception(_e::get('MP_ME'));
        // creamos el mp
        $mp = new mensajes_enviados();
        $mp->id_emisor = _::$globals['me']->id_usuario;
        $mp->id_receptor = $uid;
        $mp->asunto_mensaje = (string)_::$post['asunto'];
        $mp->contenido_mensaje = (string)_::$post['mensaje'];
        $mp->fecha_mensaje = time();
        $mp->save();
        
        $mp = new mensajes_recibidos();
        $mp->id_emisor = _::$globals['me']->id_usuario;
        $mp->id_receptor = $uid;
        $mp->asunto_mensaje = (string)_::$post['asunto'];
        $mp->contenido_mensaje = (string)_::$post['mensaje'];
        $mp->fecha_mensaje = time();
        $mp->visto = 0;
        $mp->save();
        
        $receptor = new usuarios($uid);
        if(_::$globals['me']->isMod() && $receptor->id_rango !== 2 && $receptor->id_rango !== 3 && TOUR_PUNTOS_MP > 0)
        {
            $pts = new puntos_moderadores();
            $pts->id_post = 0;
            $pts->id_moderador = _::$globals['me']->id_usuario;
            $pts->fecha = time();
            $pts->puntos = TOUR_PUNTOS_MP;
            $pts->accion = 1; // mp
            $pts->save();
        }
        
        // mp desde donde respondemos
        $origin = _::$post['origin']->int();
        if($origin>0)
        {
            $origen = new mensajes_recibidos($origin);
            if($origen->id_receptor == _::$globals['me']->id_usuario)
            {
                $origen->visto = 2;
                $origen->save();
            }
        }
        
        _::$view->ajax(array('error' => false));
    } catch(Exception $e) {
        _::$view->ajax_plain($e->getMessage());
    }
    
    
});
_::define_controller('mensajes_eliminar', function(){
    if(!isset(_::$globals['me'])) { return false; } 
    $id = _::$post['id']->int();
    
    if(_::$post['tipo']->int() == 1)
    {
        // recibido
        $mensaje = new mensajes_recibidos($id);
        if($mensaje->id_receptor !== _::$globals['me']->id_usuario) {
            _::$view->ajax(array('error' => true));
            die();
        }
        $mensaje->delete();
    } else {
        // enviado
        $mensaje = new mensajes_enviados($id);
        if($mensaje->id_emisor !== _::$globals['me']->id_usuario) {
            _::$view->ajax(array('error' => true));
            die();
        }
        $mensaje->delete();
    }
    _::$view->ajax(array('error' => false));
});
_::define_controller('mensajes_ver', function(){
    if(!isset(_::$globals['me'])) { _::redirect('e403'); return false; } 
    $id = _::$get['id']->int();
    $msg_r = new mensajes_recibidos($id);
    $msg_e = new mensajes_enviados($id);
    _::$view->assign('reportes', _::factory(razones_reportes_mp::getAll(), 'id_razon_reporte', 'razones_reportes_mp'));
    $sended = false;
    $me = $msg_r->id_receptor == _::$globals['me']->id_usuario;
    if(!$me)
    {
        $me = $msg_e->id_emisor == _::$globals['me']->id_usuario;
        $autor = null;
        $sended = true;
        $msg_e->contenido_mensaje = nl2br($msg_e->contenido_mensaje);
    } else {
        $autor = $msg_r->id_emisor;
        _::$view->assign('AOnline', sesiones::isOnline($msg_r->id_emisor));
        $msg_r->visto = 1;
        $msg_r->save();
        $msg_r->contenido_mensaje = nl2br($msg_r->contenido_mensaje);
        $uObject = new usuarios($autor);
    }
    if(!$me) { _::redirect('e404'); return false; }
    
    _::$view->assign('autor', !empty($autor) ? $uObject : null);
    _::$view->assign('rango_autor', !empty($autor) ? new rangos($uObject->id_rango) : null);
    
    _::$view->assign('subsection', null);
    _::$view->assign('section', null);
    _::$view->assign('sended', $sended);
    _::$view->assign('triseccion', $sended ? 2 : 1);
    _::$view->assign('message', $sended ? $msg_e : $msg_r);
    
    _::$view->show('msg_menu');
    _::$view->show('mensaje');
});

_::define_controller('report_mp', function(){
    if(!SYSTEM_REPORTES) die();
    _e::set('REPORT_NO_LOGIN', 'Debes estar logueado para poder reportar este mp');
    _e::set('REPORT_POST_INEX', 'El mp que quieres reportar no existe');
    _e::set('REPORT_PREREPORT', 'Ya habías reportado este mp anteriormente');
    _e::set('REPORT_RAZON', 'La razón no es válida');
    _e::set('REPORT_MSG', 'Debes aclarar el reporte');
    try {
        if(!isset(_::$globals['me'])) throw new Exception(_e::get('REPORT_NO_LOGIN'));
        $idmp = _::$post['id_mp']->int();
        $mp = new mensajes_recibidos($idmp);
        if($mp->void) throw new Exception(_e::get('REPORT_POST_INEX'));
        if(mps_reportes::prevReported(_::$globals['me']->id_usuario, $idmp)) throw new Exception(_e::get('REPORT_PREREPORT'));
        $razon = _::$post['razon']->int();
        $razon = new razones_reportes_mp($razon);
        if($razon->void) throw new Exception(_e::get('REPORT_RAZON'));
        if(_::$post['message']->len()<3) throw new Exception(_e::get('REPORT_MSG'));
        $report = new mps_reportes();
        $report->id_mp = $idmp;
        $report->id_usuario = _::$globals['me']->id_usuario;
        $report->id_razon_reporte = $razon->id_razon_reporte;
        $report->mensaje = (string) _::$post['message'];
        $report->fecha = time();
        $report->revisado = 0;
        $report->save();
        _::$view->ajax(array('error' => false));
    } catch(Exception $e)
    {
        _::$view->ajax_plain($e->getMessage());
    }
    
});