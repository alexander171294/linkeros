<?php

_::define_autocall(function(){
    _::$view->assign('section', null);
    _::$view->assign('subsection', null);
});

_::define_controller('version', function(){
	if(!SECCION_NOTAS_VERSION) {
        _::redirect('e404');
        return false;
    }
	_::$view->show('version');
});
_::define_controller('banned', function(){
	_::$view->show('banned');
});
_::define_controller('terms', function(){
	_::$view->show('terms');
});
_::define_controller('tutorial', function(){
	_::$view->assign('section', 'posts');
    _::$view->assign('subsection', 'guia');
	_::$view->show('user_guide');
});
_::define_controller('security', function(){
	_::$view->assign('timenow', date('H:i d/m/Y'));
	_::$view->show('security');
});
_::define_controller('protocolo', function(){
	_::$view->assign('staff', usuarios::getAllObjects('id_usuario', 'WHERE id_rango = 2 OR id_rango = 3'));
	_::$view->show('protocolo');
});
_::define_controller('agradecimientos', function(){
	
	_::$view->show('agradecimientos');
});
_::define_controller('closed', function(){
	
	_::$view->show('mantenimiento_parcial');
});
_::define_controller('closed_all', function(){
	
	_::$view->show('mantenimiento_completo');
});

_::define_controller('constjs', function(){
	$jsVars = null;
	$jsVars .= 'var CONST_AUTOCOMPLETE_PARTIALS = '.(int)AUTOCOMPLETE_PARTIALS.';';
	_::$view->ajax_plain($jsVars);
});

_::define_controller('banned', function(){
	_::$view->show('proxy');
});


_::define_controller('catalogo_calidades', function(){
	_::$view->assign('section', 'catalogos');
    _::$view->assign('subsection', 'calidades');
	_::$view->show('catalogo_tuto');
});
