<?php

if(!SECCION_NOTIFICACIONES){
        _::redirect('closed', false);
        die();
}

_::define_controller('notify', function(){
    
    if(!isset(_::$session['me'])) return false;
    $newNoty = notificaciones::getNews(_::$session['me']->int());
    $out = array();
    foreach($newNoty as $noty)
    {
        
        if($noty['tipo_accion'] != 1 && $noty['tipo_accion'] != 8 && $noty['tipo_accion'] != 13) // si son de los que tienen post
        {
            $actor = new usuarios($noty['id_actor']);
            $target = new post($noty['id_target']);
            if($target->void) continue;
            $out[] = array('tipo_accion' => $noty['tipo_accion'],
                    'actor' => $actor,
                    'actor_url' => '/usuario/'.$actor->id_usuario.'/'.$actor->nick_seo,
                    'target' => $target,
                    'target_url' => '/posts/'.$target->id_post.'/'.$target->o_categoria->nombre_seo.'/'.$target->titulo_seo
                );
        }
        else if($noty['tipo_accion'] != 13) // solo usan actores
        {
            $actor = new usuarios($noty['id_actor']);
            $out[] = array('tipo_accion' => $noty['tipo_accion'],
                    'actor' => $actor,
                    'actor_url' => '/usuario/'.$actor->id_usuario.'/'.$actor->nick_seo
                );
        }
        else
        {
            $rango = new rangos($noty['id_target']);
            $out[] = array('tipo_accion' => $noty['tipo_accion'],
                'rango' => $rango->nombre_rango
            );
        }
        
    }
    _::$view->ajax($out);
    
});