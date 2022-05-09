<?php

if(!isset(_::$globals['me']) || _::$globals['me']->id_rango != 2){
        _::redirect('/e404', false);
        die();
}

_::$view->assign('section', null);
_::$view->assign('subsection', null);
_::$view->assign('searchText', null);
    
_::define_controller('config', function(){
    
    _::$view->assign('aSeccion', 0);
    
    $reportes = security::getAllObjects('id_reporte', 'ORDER BY id_reporte DESC LIMIT 25');
    _::$view->assign('lastReports', $reportes);
    
    _::$view->show('admin/menu');
    _::$view->show('admin/main');
    
});

_::define_controller('config_mantenimiento', function(){
        
    if(_::$isPost && !empty(GLOBAL_KERNELL_PASSWORD) && GLOBAL_KERNELL_PASSWORD == (string)_::$post['adminPass'])
    {
        $c = json_decode(file_get_contents(__DIR__.'/../configs/mantenimiento.json'));
        $c->mantenimiento = isset(_::$post['mantenimiento']);
        $c->msg = (string)_::$post['msg']->real();
        $c->registro = isset(_::$post['registro']);
        $c->login = isset(_::$post['login']);
        $c->nubs = isset(_::$post['nubs']);
        $c->top = isset(_::$post['top']);
        $c->buscador = isset(_::$post['buscador']);
        $c->mps = isset(_::$post['mps']);
        $c->perfil = isset(_::$post['perfil']);
        $c->newpost = isset(_::$post['newpost']);
        $c->notasVersion = isset(_::$post['notasVersion']);
        $c->notificaciones = isset(_::$post['notificaciones']);
        $c->categorias = isset(_::$post['categorias']);
        $c->puntos = isset(_::$post['puntos']);
        $c->seguidores = isset(_::$post['seguidores']);
        $c->comentarios = isset(_::$post['comentarios']);
        $c->comentariosNew = isset(_::$post['comentariosNew']);
        $c->borradores = isset(_::$post['borradores']);
        $c->favoritos = isset(_::$post['favoritos']);
        $c->cron = isset(_::$post['cron']);
        $c->historial = isset(_::$post['historial']);
        $c->serviceStatus = isset(_::$post['serviceStatus']);
        $c->history = isset(_::$post['history']);
        $c->reportes = isset(_::$post['reportes']);
        $c->control = isset(_::$post['control']);
        
        file_put_contents(__DIR__.'/../configs/mantenimiento.json', json_encode($c));
        
        _::$view->assign('saved', true);
    }
    
    _::$view->assign('aSeccion', 1);
    _::$view->show('admin/menu');
    _::$view->show('admin/mantenimiento');
    
});

_::define_controller('config_performance', function(){
    
    if(_::$isPost && !empty(GLOBAL_KERNELL_PASSWORD) && GLOBAL_KERNELL_PASSWORD == (string)_::$post['adminPass'])
    {
        $c = json_decode(file_get_contents(__DIR__.'/../configs/performance.json'));
        $c->autocomplete = isset(_::$post['autocomplete']);
        $c->searchLimit = _::$post['searchLimit']->int();
        $c->searchRelateds = isset(_::$post['searchRelateds']);
        $c->relatedsCantidad = _::$post['relatedsCantidad']->int();
        $c->recacheRelateds = _::$post['recacheRelateds']->int();
        $c->recachearPosts = isset(_::$post['mantenimiento']);
        file_put_contents(__DIR__.'/../configs/performance.json', json_encode($c));
        
        _::$view->assign('saved', true);
    }
    
    _::$view->assign('aSeccion', 2);
    _::$view->show('admin/menu');
    _::$view->show('admin/performance');
    
});

_::define_controller('config_recaptcha', function(){
    
    if(_::$isPost && !empty(GLOBAL_KERNELL_PASSWORD) && GLOBAL_KERNELL_PASSWORD == (string)_::$post['adminPass'])
    {
        $c = json_decode(file_get_contents(__DIR__.'/../configs/recaptcha.json'));
        
        $c->key = (string)_::$post['key']->real();
        $c->secret = (string)_::$post['secret']->real();
        $c->registro = isset(_::$post['registro']);
        $c->fbappid = (string)_::$post['fbappid']->real();
        $c->fbappsecret = (string)_::$post['fbappsecret']->real();
        $c->twitter = (string)_::$post['twitter']->real();
        
        file_put_contents(__DIR__.'/../configs/recaptcha.json', json_encode($c));
        
        _::$view->assign('saved', true);
    }
    
    _::$view->assign('aSeccion', 3);
    _::$view->show('admin/menu');
    _::$view->show('admin/recaptcha');
    
});

_::define_controller('config_home', function(){
    
    if(_::$isPost && !empty(GLOBAL_KERNELL_PASSWORD) && GLOBAL_KERNELL_PASSWORD == (string)_::$post['adminPass'])
    {
        $c = json_decode(file_get_contents(__DIR__.'/../configs/home.json'));
        
        $c->espectroTiempoOnline = _::$post['searchLimit']->int();
        $c->startUserCount = _::$post['startUserCount']->int();
        $c->maxListHome = _::$post['maxListHome']->int();
        $c->maxPostList = _::$post['maxPostList']->int();
        $c->box = isset(_::$post['box']);
        $c->boxTitulo = (string)_::$post['boxTitulo']->real();
        $c->boxContenido = (string)_::$post['boxContenido']->real();
        $c->statsHistorical = isset(_::$post['statsHistorical']);
        $c->statsTimespectro = _::$post['statsTimespectro']->int();
        $c->topHomeCantidad = _::$post['topHomeCantidad']->int();
        $c->tagsize1 = _::$post['tagsize1']->int();
        $c->tagsize2 = _::$post['tagsize2']->int();
        $c->tagsize3 = _::$post['tagsize3']->int();
        $c->tagsize4 = _::$post['tagsize4']->int();
        $c->tagsize5 = _::$post['tagsize5']->int();
        $c->tags = _::$post['tags']->int();
        $c->noticias = isset(_::$post['noticias']);
        
        file_put_contents(__DIR__.'/../configs/home.json', json_encode($c));
        
        _::$view->assign('saved', true);
    }
        
    _::$view->assign('aSeccion', 4);
    _::$view->show('admin/menu');
    _::$view->show('admin/home');
    
});

_::define_controller('config_top', function(){
    
    if(_::$isPost && !empty(GLOBAL_KERNELL_PASSWORD) && GLOBAL_KERNELL_PASSWORD == (string)_::$post['adminPass'])
    {
        $c = json_decode(file_get_contents(__DIR__.'/../configs/top.json'));
        
        $c->cacheado = isset(_::$post['cacheado']);
        $c->cacheHoy = isset(_::$post['cacheHoy']);
        $c->cacheAyer = isset(_::$post['cacheAyer']);
        $c->cacheSemana = isset(_::$post['cacheSemana']);
        $c->cacheMes = isset(_::$post['cacheMes']);
        $c->cacheMesAnterior = isset(_::$post['cacheMesAnterior']);
        $c->cacheAnio = isset(_::$post['cacheAnios']);
        $c->cacheHistorico = isset(_::$post['cacheHistorico']);
        $c->cantidad = _::$post['cantidad']->int();
        
        file_put_contents(__DIR__.'/../configs/top.json', json_encode($c));
        
        _::$view->assign('saved', true);
    }
        
    _::$view->assign('aSeccion', 5);
    _::$view->show('admin/menu');
    _::$view->show('admin/top');
    
});

_::define_controller('config_rangos', function(){
    
    if(_::$isPost && !empty(GLOBAL_KERNELL_PASSWORD) && GLOBAL_KERNELL_PASSWORD == (string)_::$post['adminPass'])
    {
        $c = json_decode(file_get_contents(__DIR__.'/../configs/rangos.json'));
        
        $c->nfu = _::$post['nfu']->int();
        $c->fulluser = (string)_::$post['fulluser']->real();
        $c->great = _::$post['great']->int();
        $c->modalidadAleatoria = isset(_::$post['modalidadAleatoria']);
        $c->MAnewbyeonly = isset(_::$post['MAnewbyeonly']);
        $c->puntosMA = _::$post['puntosMA']->int();
        $c->rangoMA = _::$post['rangoMA']->int();
        
        file_put_contents(__DIR__.'/../configs/rangos.json', json_encode($c));
        
        _::$view->assign('saved', true);
    }
    
    _::$view->assign('aSeccion', 6);
    _::$view->show('admin/menu');
    _::$view->show('admin/rangos');
    
});

_::define_controller('config_avatar', function(){
    
    if(_::$isPost && !empty(GLOBAL_KERNELL_PASSWORD) && GLOBAL_KERNELL_PASSWORD == (string)_::$post['adminPass'])
    {
        $c = json_decode(file_get_contents(__DIR__.'/../configs/avatar.json'));
        
        $c->uploader = isset(_::$post['uploader']);
        $c->link = isset(_::$post['link']);
        $c->cam = isset(_::$post['cam']);
        $c->maxWH = isset(_::$post['maxWH']);
        $c->width = _::$post['width']->int();
        $c->height = _::$post['height']->int();
        $c->maxSize = _::$post['maxSize']->int();
        $c->hostingExternal = isset(_::$post['hostingExternal']);
        $c->cdn = isset(_::$post['cdn']);
        $c->cdnLink = (string)_::$post['cdnLink']->real();
        $c->scale = isset(_::$post['scale']);
        $c->nWidth = _::$post['nWidth']->int();
        $c->nHeight = _::$post['nHeight']->int();
        
        file_put_contents(__DIR__.'/../configs/avatar.json', json_encode($c));
        
        _::$view->assign('saved', true);
    }
    
    _::$view->assign('aSeccion', 7);
    _::$view->show('admin/menu');
    _::$view->show('admin/avatar');
    
});

_::define_controller('config_moderation', function(){
        
    if(_::$isPost && !empty(GLOBAL_KERNELL_PASSWORD) && GLOBAL_KERNELL_PASSWORD == (string)_::$post['adminPass'])
    {
        $c = json_decode(file_get_contents(__DIR__.'/../configs/moderation.json'));
        
        $c->ptsMPs = _::$post['ptsMPs']->int();
        $c->ptsBorrar = _::$post['ptsBorrar']->int();
        $c->ptsBorrador = _::$post['ptsBorrador']->int();
        $c->ptsIgnorar = _::$post['ptsIgnorar']->int();
        $c->ptsBannear = _::$post['ptsBannear']->int();
        $c->history = isset(_::$post['history']);
        $c->iptables = isset(_::$post['iptables']);
        $c->topMejores = isset(_::$post['topMejores']);
        $c->DInTop = _::$post['DInTop']->int();
        $c->autoMod = isset(_::$post['autoMod']);
        $c->countDenuncias = _::$post['countDenuncias']->int();
        $c->negativosComments = _::$post['negativosComments']->int();
        $c->antifloodPost = _::$post['antifloodPost']->int();
        $c->antifloodComment = _::$post['antifloodComment']->int();
        
        file_put_contents(__DIR__.'/../configs/moderation.json', json_encode($c));
        
        _::$view->assign('saved', true);
    }
           
    
    _::$view->assign('aSeccion', 8);
    _::$view->show('admin/menu');
    _::$view->show('admin/moderation');
    
});