<?php class_exists('_') || die('FORBIDDEN');

// this function is execute after controllers.
// this DON'T EXECUTE IF YOU CALL REDIRECT IN CONTROLLER
_::attach_footer(function(){
        _::$view->show('footer');
        
        if(!isset(_::$globals['me'])) return true;
        $sesion = new sesiones(_::$globals['me']->id_usuario);
        if($sesion->void)
        {
                $sesion = new sesiones();
                $sesion->id_usuario = _::$globals['me']->id_usuario;
        }
        $sesion->last_activity = time();
        $sesion->php_sessid = session_id();
        $sesion->ubicacion = _::$globals['controller'];
        $sesion->ip = get_real_ip();
        $sesion->save();
    });