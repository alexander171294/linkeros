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


_::define_controller('favoritos', function(){
    _::$view->assign('triseccion', 1);
    if(!isset(_::$get['cate']))
        $favoritos = post::getFavoritos(_::$globals['me']->id_usuario);
    else
    {
        _::$view->assign('triseccion', _::$get['cate']->int());
        $favoritos = post::getFavoritos(_::$globals['me']->id_usuario, _::$get['cate']->int());
    }
    $catFav = categorias::getFavoritosCates(_::$globals['me']->id_usuario);
    _::$view->assign('favsList', $favoritos);
    _::$view->assign('cateList', $catFav);
    _::$view->show('favoritos_menu');
    _::$view->show('favoritos_list');
});

_::define_controller('favoritos_vaciar', function(){
    favoritos_post::deleteAll('WHERE id_usuario = ?', array(_::$globals['me']->id_usuario));
    _::$view->ajax(array('status' => true));
});

_::define_controller('favoritos_delete', function(){
    $target = _::$post['idpost']->int();
    $favorito = new favoritos_post(array($target, _::$globals['me']->id_usuario));
    $favorito->delete();
    _::$view->ajax(array('status' => true));
});