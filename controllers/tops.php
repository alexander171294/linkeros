<?php class_exists('_') || die('FORBIDDEN');

// controlador top
if(!SECCION_TOP){
        _::redirect('closed', false);
        die();
    }
    _::$view->assign('section', 'tops');
    _::$view->assign('subsection', 'top_post');
    
// controlador tops
_::define_controller('tops', function(){
    
    // espectro de días
    _::$view->assign('topEspectro', 7);
    // top histórico
    // si está habilitado el cacheado y está activado el cacheado de top histórico
    if(TOP_CACHEADO && TOP_CACHEADO_HISTORICO)
    {
        // obtenemos todo el caché
        $xPuntos = _::factory(top_cache::getAll('WHERE espectro = 7 AND tipo = 1'), 'id_post', 'post');
        $xFavoritos = _::factory(top_cache::getAll('WHERE espectro = 7 AND tipo = 2'), 'id_post', 'post');
        $xComentarios = _::factory(top_cache::getAll('WHERE espectro = 7 AND tipo = 3'), 'id_post', 'post');
        $xSeguidores = _::factory(top_cache::getAll('WHERE espectro = 7 AND tipo = 4'), 'id_post', 'post');
    } else { // si no hay caché
        // generamos el top
        $xPuntos = _::factory(post::getAll('ORDER BY puntos DESC LIMIT '.TOP_CANTIDAD),'id_post', 'post');
        $xFavoritos = _::factory(post::getAll('ORDER BY favoritos DESC LIMIT '.TOP_CANTIDAD),'id_post', 'post');
        $xComentarios = _::factory(post::getAll('ORDER BY comentarios DESC LIMIT '.TOP_CANTIDAD),'id_post', 'post');
        $xSeguidores = _::factory(post::getAll('ORDER BY seguidores DESC LIMIT '.TOP_CANTIDAD),'id_post', 'post');
    }
    // asignamos los resultados
    _::$view->assign('xPuntos', $xPuntos);
    _::$view->assign('xFavoritos', $xFavoritos);
    _::$view->assign('xComentarios', $xComentarios);
    _::$view->assign('xSeguidores', $xSeguidores);
    // mostramos el top
    _::$view->show('top');
});


_::define_controller('top_hoy', function(){
    _::$view->assign('topEspectro', 1);
    // top histórico
    if(TOP_CACHEADO && TOP_CACHEADO_HOY)
    {
        $xPuntos = _::factory(top_cache::getAll('WHERE espectro = 1 AND tipo = 1'), 'id_post', 'post');
        $xFavoritos = _::factory(top_cache::getAll('WHERE espectro = 1 AND tipo = 2'), 'id_post', 'post');
        $xComentarios = _::factory(top_cache::getAll('WHERE espectro = 1 AND tipo = 3'), 'id_post', 'post');
        $xSeguidores = _::factory(top_cache::getAll('WHERE espectro = 1 AND tipo = 4'), 'id_post', 'post');
    } else {
        $hoy = time()-60*60*24;
        $xPuntos = _::factory(post::bestPuntos(TOP_CANTIDAD, $hoy), 'id_post', 'post');
        $xFavoritos = _::factory(post::bestFavoritos(TOP_CANTIDAD, $hoy), 'id_post', 'post');
        $xComentarios = _::factory(post::bestComentarios(TOP_CANTIDAD, $hoy), 'id_post', 'post');
        $xSeguidores = _::factory(post::bestSeguidores(TOP_CANTIDAD, $hoy), 'id_post', 'post');
    }
    _::$view->assign('xPuntos', $xPuntos);
    _::$view->assign('xFavoritos', $xFavoritos);
    _::$view->assign('xComentarios', $xComentarios);
    _::$view->assign('xSeguidores', $xSeguidores);
    _::$view->show('top');
});

_::define_controller('top_ayer', function(){
    _::$view->assign('topEspectro', 2);
    // top histórico
    if(TOP_CACHEADO && TOP_CACHEADO_AYER)
    {
        $xPuntos = _::factory(top_cache::getAll('WHERE espectro = 2 AND tipo = 1'), 'id_post', 'post');
        $xFavoritos = _::factory(top_cache::getAll('WHERE espectro = 2 AND tipo = 2'), 'id_post', 'post');
        $xComentarios = _::factory(top_cache::getAll('WHERE espectro = 2 AND tipo = 3'), 'id_post', 'post');
        $xSeguidores = _::factory(top_cache::getAll('WHERE espectro = 2 AND tipo = 4'), 'id_post', 'post');
    } else {
        $hoy = time()-60*60*24;
        $ayer = time()-60*60*48;
        $xPuntos = _::factory(post::bestPuntos(TOP_CANTIDAD, $ayer, $hoy), 'id_post', 'post');
        $xFavoritos = _::factory(post::bestFavoritos(TOP_CANTIDAD, $ayer, $hoy), 'id_post', 'post');
        $xComentarios = _::factory(post::bestComentarios(TOP_CANTIDAD, $ayer, $hoy), 'id_post', 'post');
        $xSeguidores = _::factory(post::bestSeguidores(TOP_CANTIDAD, $ayer, $hoy), 'id_post', 'post');
    }
    _::$view->assign('xPuntos', $xPuntos);
    _::$view->assign('xFavoritos', $xFavoritos);
    _::$view->assign('xComentarios', $xComentarios);
    _::$view->assign('xSeguidores', $xSeguidores);
    _::$view->show('top');
});

_::define_controller('top_semana', function(){
    _::$view->assign('topEspectro', 3);
    // top histórico
    if(TOP_CACHEADO && TOP_CACHEADO_SEMANA)
    {
        $xPuntos = _::factory(top_cache::getAll('WHERE espectro = 3 AND tipo = 1'), 'id_post', 'post');
        $xFavoritos = _::factory(top_cache::getAll('WHERE espectro = 3 AND tipo = 2'), 'id_post', 'post');
        $xComentarios = _::factory(top_cache::getAll('WHERE espectro = 3 AND tipo = 3'), 'id_post', 'post');
        $xSeguidores = _::factory(top_cache::getAll('WHERE espectro = 3 AND tipo = 4'), 'id_post', 'post');
    } else {
        $semana = time()-60*60*24*7;
        $xPuntos = _::factory(post::bestPuntos(TOP_CANTIDAD, $semana), 'id_post', 'post');
        $xFavoritos = _::factory(post::bestFavoritos(TOP_CANTIDAD, $semana), 'id_post', 'post');
        $xComentarios = _::factory(post::bestComentarios(TOP_CANTIDAD, $semana), 'id_post', 'post');
        $xSeguidores = _::factory(post::bestSeguidores(TOP_CANTIDAD, $semana), 'id_post', 'post');
    }
    _::$view->assign('xPuntos', $xPuntos);
    _::$view->assign('xFavoritos', $xFavoritos);
    _::$view->assign('xComentarios', $xComentarios);
    _::$view->assign('xSeguidores', $xSeguidores);
    _::$view->show('top');
});

_::define_controller('top_mes', function(){
    _::$view->assign('topEspectro', 4);
    // top histórico
    if(TOP_CACHEADO && TOP_CACHEADO_MES)
    {
        $xPuntos = _::factory(top_cache::getAll('WHERE espectro = 4 AND tipo = 1'), 'id_post', 'post');
        $xFavoritos = _::factory(top_cache::getAll('WHERE espectro = 4 AND tipo = 2'), 'id_post', 'post');
        $xComentarios = _::factory(top_cache::getAll('WHERE espectro = 4 AND tipo = 3'), 'id_post', 'post');
        $xSeguidores = _::factory(top_cache::getAll('WHERE espectro = 4 AND tipo = 4'), 'id_post', 'post');
    } else {
        $mes = time()-60*60*7*4;
        $xPuntos = _::factory(post::bestPuntos(TOP_CANTIDAD, $mes), 'id_post', 'post');
        $xFavoritos = _::factory(post::bestFavoritos(TOP_CANTIDAD, $mes), 'id_post', 'post');
        $xComentarios = _::factory(post::bestComentarios(TOP_CANTIDAD, $mes), 'id_post', 'post');
        $xSeguidores = _::factory(post::bestSeguidores(TOP_CANTIDAD, $mes), 'id_post', 'post');
    }
    _::$view->assign('xPuntos', $xPuntos);
    _::$view->assign('xFavoritos', $xFavoritos);
    _::$view->assign('xComentarios', $xComentarios);
    _::$view->assign('xSeguidores', $xSeguidores);
    _::$view->show('top');
});

_::define_controller('top_mesanterior', function(){
    _::$view->assign('topEspectro', 5);
    // top histórico
    if(TOP_CACHEADO && TOP_CACHEADO_MES_ANTERIOR)
    {
        $xPuntos = _::factory(top_cache::getAll('WHERE espectro = 5 AND tipo = 1'), 'id_post', 'post');
        $xFavoritos = _::factory(top_cache::getAll('WHERE espectro = 5 AND tipo = 2'), 'id_post', 'post');
        $xComentarios = _::factory(top_cache::getAll('WHERE espectro = 5 AND tipo = 3'), 'id_post', 'post');
        $xSeguidores = _::factory(top_cache::getAll('WHERE espectro = 5 AND tipo = 4'), 'id_post', 'post');
    } else {
        $mes = time()-60*60*7*4;
        $mes_anterior = time()-60*60*7*8;
        $xPuntos = _::factory(post::bestPuntos(TOP_CANTIDAD, $mes_anterior, $mes), 'id_post', 'post');
        $xFavoritos = _::factory(post::bestFavoritos(TOP_CANTIDAD, $mes_anterior, $mes), 'id_post', 'post');
        $xComentarios = _::factory(post::bestComentarios(TOP_CANTIDAD, $mes_anterior, $mes), 'id_post', 'post');
        $xSeguidores = _::factory(post::bestSeguidores(TOP_CANTIDAD, $mes_anterior, $mes), 'id_post', 'post');
    }
    _::$view->assign('xPuntos', $xPuntos);
    _::$view->assign('xFavoritos', $xFavoritos);
    _::$view->assign('xComentarios', $xComentarios);
    _::$view->assign('xSeguidores', $xSeguidores);
    _::$view->show('top');
});

_::define_controller('top_anio', function(){
    _::$view->assign('topEspectro', 6);
    // top histórico
    if(TOP_CACHEADO && TOP_CACHEADO_ANIO)
    {
        $xPuntos = _::factory(top_cache::getAll('WHERE espectro = 6 AND tipo = 1'), 'id_post', 'post');
        $xFavoritos = _::factory(top_cache::getAll('WHERE espectro = 6 AND tipo = 2'), 'id_post', 'post');
        $xComentarios = _::factory(top_cache::getAll('WHERE espectro = 6 AND tipo = 3'), 'id_post', 'post');
        $xSeguidores = _::factory(top_cache::getAll('WHERE espectro = 6 AND tipo = 4'), 'id_post', 'post');
    } else {
        $anio = time()-60*60*365;
        $xPuntos = _::factory(post::bestPuntos(TOP_CANTIDAD, $anio), 'id_post', 'post');
        $xFavoritos = _::factory(post::bestFavoritos(TOP_CANTIDAD, $anio), 'id_post', 'post');
        $xComentarios = _::factory(post::bestComentarios(TOP_CANTIDAD, $anio), 'id_post', 'post');
        $xSeguidores = _::factory(post::bestSeguidores(TOP_CANTIDAD, $anio), 'id_post', 'post');
    }
    _::$view->assign('xPuntos', $xPuntos);
    _::$view->assign('xFavoritos', $xFavoritos);
    _::$view->assign('xComentarios', $xComentarios);
    _::$view->assign('xSeguidores', $xSeguidores);
    _::$view->show('top');
});