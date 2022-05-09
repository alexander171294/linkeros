<?php

_::define_autocall(function(){
    if(!isset(_::$globals['me'])) { _::redirect('e403'); return false; } 
    if(!SECCION_BORRADORES) {
        _::redirect('closed', false);
        die();
    }
    _::$view->assign('subsection', null);
    _::$view->assign('section', null);
});

_::define_controller('borradores', function(){
    _::$view->assign('triseccion', 1);
    $borradores = post::getBorradores(_::$globals['me']->id_usuario);
    _::$view->assign('borradoresList', $borradores);
    _::$view->show('borradores_menu');
    _::$view->show('borradores_list');
});

_::define_controller('borradores_vaciar', function(){
    post::deleteAll('WHERE id_usuario = ? AND borrador = 1', array(_::$globals['me']->id_usuario));
    _::$view->ajax(array('status' => true));
});

_::define_controller('borradores_publicar', function(){
    $target = _::$post['idpost']->int();
    $post = new post($target);
    if($post->id_usuario == _::$globals['me']->id_usuario)
    {
        $post->borrador = 0;
        $post->save();
        _::$view->ajax(array('status' => true));
    } else {
        _::$view->ajax(array('status' => false));
    }
});

_::define_controller('borradores_borrar', function(){
    $target = _::$post['idpost']->int();
    $post = new post($target);
    if($post->id_usuario == _::$globals['me']->id_usuario)
    {
        $post->delete();
        _::$view->ajax(array('status' => true));
    } else {
        _::$view->ajax(array('status' => false));
    }
});

_::define_controller('borradores_revision', function(){
    _::$view->assign('triseccion', 2);
    $borradores = post::getRevision(_::$globals['me']->id_usuario);
    _::$view->assign('borradoresList', $borradores);
    _::$view->show('borradores_menu');
    _::$view->show('revision_list');
});