<?php

_::define_controller('cambiar_rango_usuario', function(){
    if(!isset(_::$globals['me']) || _::$globals['me']->id_rango != 2) return false;
    $rango = _::$post['rango']->int();
    $idu = _::$post['idu']->int();
    $user = new usuarios($idu);
    if($user->id_rango == 2) return false;
    $user->id_rango = $rango;
    $user->save();
    $history = new mod_history();
    $history->tipo_target = 2; // usuario
    $history->id_target = $user->id_usuario;
    $history->accion = 9; // Rango actualizado
    $history->id_moderador = _::$globals['me']->id_usuario;
    $history->original_title = $user->nick;
    $history->fecha = time();
    $history->save();
	$notify = new notificaciones();
    $notify->id_usuario = $user->id_usuario;
    $notify->id_actor = 0;
    $notify->id_target = $user->id_rango;
    $notify->tipo_accion = 13;
    $notify->fecha = time();
    $notify->visto = 0;
    $notify->save();
    _::$view->ajax(array('error' => false));
});

_::define_controller('bannear_usuario', function(){
    if(!isset(_::$globals['me']) || (_::$globals['me']->id_rango != 2 && _::$globals['me']->id_rango != 3)) return false;
    $idu = _::$post['idu']->int();
    $user = new usuarios($idu);
    if($user->id_rango == 2) return false;
    $user->status_user = 2;
    $user->ban_reason = (string)_::$post['motivo'];
    $user->save();
    $history = new mod_history();
    $history->tipo_target = 2; // usuario
    $history->id_target = $user->id_usuario;
    $history->accion = 7; // banneado
    $history->id_moderador = _::$globals['me']->id_usuario;
    $history->original_title = $user->nick;
    $history->fecha = time();
    $history->save();
    
    if(isset(_::$post['assignpts']))
	{
		$pts = new puntos_moderadores();
		$pts->id_post = $idu;
		$pts->id_moderador = _::$globals['me']->id_usuario;
		$pts->fecha = time();
		$pts->puntos = TOUR_PUNTOS_BANNEAR;
		$pts->accion = 7; // ignorar
		$pts->save();
	}
    
    _::$view->ajax(array('error' => false, 'pts' => TOUR_PUNTOS_BANNEAR));
});

_::define_controller('desbannear_usuario', function(){
    if(!isset(_::$globals['me']) || (_::$globals['me']->id_rango != 2 && _::$globals['me']->id_rango != 3)) return false;
    $idu = _::$post['idu']->int();
    $user = new usuarios($idu);
    if($user->id_rango == 2) return false;
    $user->status_user = 1;
    $user->save();
    $history = new mod_history();
    $history->tipo_target = 2; // usuario
    $history->id_target = $user->id_usuario;
    $history->accion = 8; // desbanneado
    $history->id_moderador = _::$globals['me']->id_usuario;
    $history->original_title = $user->nick;
    $history->fecha = time();
    $history->save();
    _::$view->ajax(array('error' => false));
});

_::define_controller('banip', function(){
    if(!isset(_::$globals['me']) || _::$globals['me']->id_rango != 2) return false;
    $idu = _::$post['id']->int();
    $user = new usuarios($idu);
    if($user->id_rango == 2)
	{
		_::$view->ajax(array('status' => false));
	} else {
		$theIP = ip2long($user->last_ip);
		$ban = new iptables();
		$ban->ip = $theIP;
		$ban->fecha = time();
		$ban->moderator = _::$globals['me']->id_usuario;
		$ban->save();
		$ban->execIpTables();
		_::$view->ajax(array('status' => true));
	}
});

_::define_controller('ubanip', function(){
    if(!isset(_::$globals['me']) || _::$globals['me']->id_rango != 2) return false;
    $ip = _::$get['ip']->int();
    $ban = new iptables($ip);
	$ban->delete();
	_::$view->ajax(array('status' => true));
});