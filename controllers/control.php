<?php

_::define_autocall(function(){
	if(!isset(_::$globals['me']) || !_::$globals['me']->isMod()) {
        _::redirect('/e404', false);
        return false;
    }
	if(!SYSTEM_CONTROL)
	{
		_::redirect('/closed', false);
        return false;
	}
    _::$view->assign('section', null);
    _::$view->assign('subsection', null);
});

_::define_controller('control_banneds', function(){
    
	_::$view->assign('banneds', usuarios::getBanneds());
	
    _::$view->assign('section', 'control');
    _::$view->assign('subsection', 'control_banneds');

	_::$view->show('moderacion/user_banned');
});

_::define_controller('control', function(){

	$reporte = post_reportes::count('id_usuario', 'WHERE revisado = 0');
	_::$view->assign('reportPosts', $reporte>0);
    
	_::$view->assign('topMods', usuarios::getTopMods());
    _::$view->assign('section', 'control');
    _::$view->assign('subsection', 'control_inicio');
    

	_::$view->show('moderacion/inicio');
});

_::define_controller('control_tour', function(){
    
    _::$view->assign('section', 'control');
    _::$view->assign('subsection', 'control_tour');
	 _::$view->assign('noReports', false);
    
	$page = isset(_::$get['page']) ? _::$get['page']->int() : 0;
    $reporte = post_reportes::getAll('WHERE revisado = 0 LIMIT '.$page.',1');
	$reporte_users = users_reportes::getAll('WHERE revisado = 0 LIMIT '.$page.',1');
	$reporte_mps = mps_reportes::getAll('WHERE revisado = 0 LIMIT '.$page.',1');
	
	if($reporte == array() && $reporte_users == array() && $reporte_mps == array()) _::$view->assign('noReports', true);
	
	if(isset($reporte[0])) $reporte = $reporte[0]; else $reporte = null;
	if(isset($reporte_users[0])) $reporte_users = $reporte_users[0]; else $reporte_users = null;
	if(isset($reporte_mps[0])) $reporte_mps = $reporte_mps[0]; else $reporte_mps = null;
	_::$view->assign('pageNext', $page+1);
	
    $fecha = new _date($reporte['fecha']);
    _::$view->assign('fechaReporte', $fecha->fDay('/')->fMonth('/')->fYear()->format());
	
	$fecha_user = new _date($reporte_users['fecha']);
    _::$view->assign('fechaReporteUser', $fecha_user->fDay('/')->fMonth('/')->fYear()->format());
	
	$fecha_mp = new _date($reporte_mps['fecha']);
    _::$view->assign('fechaReporteMp', $fecha_mp->fDay('/')->fMonth('/')->fYear()->format());
    
    $reporte = new post_reportes(array($reporte['id_post'], $reporte['id_usuario']));
    _::$view->assign('reporte', $reporte);
	$post = new post($reporte->id_post);
    _::$view->assign('postRorte', $post);
    _::$view->assign('autor', new usuarios($post->id_usuario));
	_::$view->assign('reporter', new usuarios($reporte->id_usuario));
    _::$view->assign('razonReporte', new razones_reportes($reporte->id_razon_reporte));
	
	$reporteU = new users_reportes(array($reporte_users['id_profile'], $reporte_users['id_usuario']));
    _::$view->assign('reporteUsers', $reporteU);
	$user_reported = new usuarios($reporteU->id_profile);
    _::$view->assign('userReported', $user_reported);
	$user_reportador = new usuarios($reporteU->id_usuario);
    _::$view->assign('userReportador', $user_reportador);
    //_::$view->assign('autor', new usuarios($post->id_usuario));
    _::$view->assign('razonReporteUser', new razones_reportes_usuarios($reporteU->id_razon_reporte));
	
	$reporteMP = new mps_reportes(array($reporte_mps['id_mp'], $reporte_mps['id_usuario']));
    _::$view->assign('reporteMps', $reporteMP);
	_::$view->assign('theMP', new mensajes_recibidos($reporteMP->id_mp));
	_::$view->assign('razonReporteMP', new razones_reportes_mp($reporteMP->id_razon_reporte));
	_::$view->assign('mpReportador', new usuarios($reporteMP->id_usuario));
    
	_::$view->show('moderacion/tour');
});

_::define_controller('control_history', function(){
    
    _::$view->assign('section', 'control');
    _::$view->assign('subsection', 'control_history');
    
    _::$view->assign('lastReport', _::factory(mod_history::getAll('ORDER BY id_history DESC LIMIT 25'), 'id_history', 'mod_history'));
    
	_::$view->show('moderacion/history');
});

_::define_controller('control_reviseduser', function(){
	
	$idr = _::$post['id']->int();

	$reports = _::factory(users_reportes::getAll('WHERE id_profile = ?', array($idr)), array('id_profile', 'id_usuario'), 'users_reportes');
	foreach($reports as $report){
		$report->revisado = 1;
		$report->save();
	}
	if(isset(_::$post['assignpts']))
	{
		$pts = new puntos_moderadores();
		$pts->id_post = $idr;
		$pts->id_moderador = _::$globals['me']->id_usuario;
		$pts->fecha = time();
		$pts->puntos = TOUR_PUNTOS_IGNORAR;
		$pts->accion = 4; // ignorar
		$pts->save();
	}
	
	_::$view->ajax(array('status' => 'true', 'pts' => TOUR_PUNTOS_IGNORAR));
});

_::define_controller('control_revisedmp', function(){
	
	$idr = _::$post['id']->int();

	$reports = _::factory(mps_reportes::getAll('WHERE id_mp = ?', array($idr)), array('id_mp', 'id_usuario'), 'mps_reportes');
	foreach($reports as $report){
		$report->revisado = 1;
		$report->save();
	}
	if(isset(_::$post['assignpts']))
	{
		$pts = new puntos_moderadores();
		$pts->id_post = $idr;
		$pts->id_moderador = _::$globals['me']->id_usuario;
		$pts->fecha = time();
		$pts->puntos = TOUR_PUNTOS_IGNORAR;
		$pts->accion = 4; // ignorar
		$pts->save();
	}
	
	_::$view->ajax(array('status' => 'true', 'pts' => TOUR_PUNTOS_IGNORAR));
});



_::define_controller('control_revised', function(){

	$idr = _::$post['id']->int();
	
	$post = new post($idr);
    $post->revision = 0;
    $post->save();

	$reports = _::factory(post_reportes::getAll('WHERE id_post = ?', array($idr)), array('id_post', 'id_usuario'), 'post_reportes');
	foreach($reports as $report){
		$report->revisado = 1;
		$report->save();
	}
	if(isset(_::$post['assignpts']))
	{
		$pts = new puntos_moderadores();
		$pts->id_post = $idr;
		$pts->id_moderador = _::$globals['me']->id_usuario;
		$pts->fecha = time();
		$pts->puntos = TOUR_PUNTOS_IGNORAR;
		$pts->accion = 4; // ignorar
		$pts->save();
	}
	
	_::$view->ajax_plain('true');
});

_::define_controller('control_iptable', function(){
    
    _::$view->assign('section', 'control');
    _::$view->assign('subsection', 'control_iptable');
    
    _::$view->assign('iptables', iptables::getAllObjects('ip', 'ORDER BY fecha DESC'));
    
	_::$view->show('moderacion/iptable');
	
});