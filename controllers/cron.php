<?php if(!CRON_CORE || ((string)_::$get['key'] !== CRON_KEY)) die();

_::define_controller('cron_medalla', function(){
    if(isset(_::$globals['me']) && _::$globals['me']->id_usuario == 1)
    {
        $id = _::$get['id_medalla']->int();
        $where = isset(_::$get['where']) ? (string)_::$get['where'] : null;
        $targets = usuarios::getAllObjects('id_usuario', $where);
        foreach($targets as $target)
        {
            $medal = new user_medals();
            $medal->id_medalla = $id;
            $medal->id_usuario = $target->id_usuario;
            $medal->fecha = time();
            $medal->save();
        }
        _::$view->ajax_plain('Ok, targeted: '.count($targets).' user(s)');
    }
});

_::define_controller('cron_rangos', function(){
    // NEW FULL USER
    $posts = _::factory(post::getAll('WHERE nub_section = 1 && puntos >= '.PUNTOS_NFU), 'id_post', 'post');
    foreach($posts as $post) // post que tiene 50 puntos y es nub
    {
        $u = new usuarios($post->id_usuario);
        if($u->id_rango == 1)
        {
            $u->id_rango = 4; // new full user
            $u->nfu_desde = time();
            $u->save();
            $notify = new notificaciones();
            $notify->id_usuario = $u->id_usuario;
            $notify->id_actor = 0;
            $notify->id_target = 4; // nfu
            $notify->tipo_accion = 13;
            $notify->fecha = time();
            $notify->visto = 0;
            $notify->save();
            post::moveToHome($post->id_usuario);
        }
    }
    // FULL USER
    $hace_tiempo = time()-TIEMPO_FULL_USER;
    $users = _::factory(usuarios::getAll('WHERE id_rango = 4 AND nfu_desde > 0 AND nfu_desde <= '.$hace_tiempo), 'id_usuario', 'usuarios');
    foreach($users as $user)
    {
        $user->id_rango = 5;
        $user->save();
        $notify = new notificaciones();
        $notify->id_usuario = $u->id_usuario;
        $notify->id_actor = 0;
        $notify->id_target = 5; // FullUser
        $notify->tipo_accion = 13;
        $notify->fecha = time();
        $notify->visto = 0;
        $notify->save();
        unset($user);
    }
    // GREAT USER
    $posts = _::factory(post::getAll('WHERE puntos >= '.PUNTOS_GREAT_USER), 'id_post', 'post');
    foreach($posts as $post)
    {
        $u = new usuarios($post->id_usuario);
        if($u->id_rango == 5)
        {
            $u->id_rango = 6; // Great User
            $u->save();
            $notify = new notificaciones();
            $notify->id_usuario = $u->id_usuario;
            $notify->id_actor = 0;
            $notify->id_target = 6; // Great User
            $notify->tipo_accion = 13;
            $notify->fecha = time();
            $notify->visto = 0;
            $notify->save();
            unset($u);
        }
    }
    
    // Modalidad Asignación Aleatoria
    if(M_A_ACTIVE)
    {
        if(M_A_NEWB_ONLY) $adding = 'nub_section = 1 && ';
        $posts = _::factory(post::getAll('WHERE '.$adding.'puntos >= '.PUNTOS_M_A), 'id_post', 'post');
        $post = array_rand($posts);
        $u = new usuarios($post->id_usuario);
        if($u->id_rango == 1 || (!M_A_NEWB_ONLY && $u->id_rango !== 2 && $u->id_rango !== 3))
        {
            $u->id_rango = RANGO_M_A;
            $u->nfu_desde = time();
            $u->save();
            $notify = new notificaciones();
            $notify->id_usuario = $u->id_usuario;
            $notify->id_actor = 0;
            $notify->id_target = 1; // nfu
            $notify->tipo_accion = 13;
            $notify->fecha = time();
            $notify->visto = 0;
            $notify->save();
            post::moveToHome($post->id_usuario);
        }
    }
    
    _::$view->ajax(array('error', true));
});

_::define_controller('cron_puntos',function(){
    $rangos = rangos::getAll();
    foreach($rangos as $rango)
    {
        $r = $rango['id_rango'];
        $puntos = $rango['puntos_disponibles'];
        usuarios::updatePoints($r, $puntos);
    }
    _::$view->ajax(array('error', true));
});

_::define_controller('cron_tops', function(){
    if(!TOP_CACHEADO) return false;
    top_cache::delteAll();
    if(TOP_CACHEADO_HOY)
    {
        $hoy = time()-60*60*24;
        $xPuntos = post::bestPuntos(TOP_CANTIDAD, $hoy);
        $xFavoritos = post::bestFavoritos(TOP_CANTIDAD, $hoy);
        $xComentarios = post::bestComentarios(TOP_CANTIDAD, $hoy);
        $xSeguidores = post::bestSeguidores(TOP_CANTIDAD, $hoy);
        $xPuntos = _::factory($xPuntos, 'id_post', 'post');
        $xFavoritos = _::factory($xFavoritos, 'id_post', 'post');
        $xComentarios = _::factory($xComentarios, 'id_post', 'post');
        $xSeguidores = _::factory($xSeguidores, 'id_post', 'post');
        foreach($xPuntos as $post)
        {
            $top = new top_cache();
            $top->tipo = 1;
            $top->espectro = 1;
            $top->id_post = $post->id_post;
            $top->save();
        }
        foreach($xFavoritos as $post)
        {
            $top = new top_cache();
            $top->tipo = 2;
            $top->espectro = 1;
            $top->id_post = $post->id_post;
            $top->save();
        }
        foreach($xComentarios as $post)
        {
            $top = new top_cache();
            $top->tipo = 3;
            $top->espectro = 1;
            $top->id_post = $post->id_post;
            $top->save();
        }
        foreach($xSeguidores as $post)
        {
            $top = new top_cache();
            $top->tipo = 4;
            $top->espectro = 1;
            $top->id_post = $post->id_post;
            $top->save();
        }
    }
    if(TOP_CACHEADO_AYER)
    {
        $hoy = time()-60*60*24;
        $ayer = time()-60*60*48;
        $xPuntos = post::bestPuntos(TOP_CANTIDAD, $ayer, $hoy);
        $xFavoritos = post::bestFavoritos(TOP_CANTIDAD, $ayer, $hoy);
        $xComentarios = post::bestComentarios(TOP_CANTIDAD, $ayer, $hoy);
        $xSeguidores = post::bestSeguidores(TOP_CANTIDAD, $ayer, $hoy);
        $xPuntos = _::factory($xPuntos, 'id_post', 'post');
        $xFavoritos = _::factory($xFavoritos, 'id_post', 'post');
        $xComentarios = _::factory($xComentarios, 'id_post', 'post');
        $xSeguidores = _::factory($xSeguidores, 'id_post', 'post');
        foreach($xPuntos as $post)
        {
            $top = new top_cache();
            $top->tipo = 1;
            $top->espectro = 2;
            $top->id_post = $post->id_post;
            $top->save();
        }
        foreach($xFavoritos as $post)
        {
            $top = new top_cache();
            $top->tipo = 2;
            $top->espectro = 2;
            $top->id_post = $post->id_post;
            $top->save();
        }
        foreach($xComentarios as $post)
        {
            $top = new top_cache();
            $top->tipo = 3;
            $top->espectro = 2;
            $top->id_post = $post->id_post;
            $top->save();
        }
        foreach($xSeguidores as $post)
        {
            $top = new top_cache();
            $top->tipo = 4;
            $top->espectro = 2;
            $top->id_post = $post->id_post;
            $top->save();
        }
    }
    if(TOP_CACHEADO_SEMANA)
    {
        $semana = time()-60*60*24*7;
        $xPuntos = post::bestPuntos(TOP_CANTIDAD, $semana);
        $xFavoritos = post::bestFavoritos(TOP_CANTIDAD, $semana);
        $xComentarios = post::bestComentarios(TOP_CANTIDAD, $semana);
        $xSeguidores = post::bestSeguidores(TOP_CANTIDAD, $semana);
        $xPuntos = _::factory($xPuntos, 'id_post', 'post');
        $xFavoritos = _::factory($xFavoritos, 'id_post', 'post');
        $xComentarios = _::factory($xComentarios, 'id_post', 'post');
        $xSeguidores = _::factory($xSeguidores, 'id_post', 'post');
        foreach($xPuntos as $post)
        {
            $top = new top_cache();
            $top->tipo = 1;
            $top->espectro = 3;
            $top->id_post = $post->id_post;
            $top->save();
        }
        foreach($xFavoritos as $post)
        {
            $top = new top_cache();
            $top->tipo = 2;
            $top->espectro = 3;
            $top->id_post = $post->id_post;
            $top->save();
        }
        foreach($xComentarios as $post)
        {
            $top = new top_cache();
            $top->tipo = 3;
            $top->espectro = 3;
            $top->id_post = $post->id_post;
            $top->save();
        }
        foreach($xSeguidores as $post)
        {
            $top = new top_cache();
            $top->tipo = 4;
            $top->espectro = 3;
            $top->id_post = $post->id_post;
            $top->save();
        }
    }
    if(TOP_CACHEADO_MES)
    {
        $mes = time()-60*60*7*4;
        $xPuntos = post::bestPuntos(TOP_CANTIDAD, $mes);
        $xFavoritos = post::bestFavoritos(TOP_CANTIDAD, $mes);
        $xComentarios = post::bestComentarios(TOP_CANTIDAD, $mes);
        $xSeguidores = post::bestSeguidores(TOP_CANTIDAD, $mes);
        $xPuntos = _::factory($xPuntos, 'id_post', 'post');
        $xFavoritos = _::factory($xFavoritos, 'id_post', 'post');
        $xComentarios = _::factory($xComentarios, 'id_post', 'post');
        $xSeguidores = _::factory($xSeguidores, 'id_post', 'post');
        foreach($xPuntos as $post)
        {
            $top = new top_cache();
            $top->tipo = 1;
            $top->espectro = 4;
            $top->id_post = $post->id_post;
            $top->save();
        }
        foreach($xFavoritos as $post)
        {
            $top = new top_cache();
            $top->tipo = 2;
            $top->espectro = 4;
            $top->id_post = $post->id_post;
            $top->save();
        }
        foreach($xComentarios as $post)
        {
            $top = new top_cache();
            $top->tipo = 3;
            $top->espectro = 4;
            $top->id_post = $post->id_post;
            $top->save();
        }
        foreach($xSeguidores as $post)
        {
            $top = new top_cache();
            $top->tipo = 4;
            $top->espectro = 4;
            $top->id_post = $post->id_post;
            $top->save();
        }
    }
    if(TOP_CACHEADO_MES_ANTERIOR)
    {
        $mes = time()-60*60*7*4;
        $mes_anterior = time()-60*60*7*8;
        $xPuntos = post::bestPuntos(TOP_CANTIDAD, $mes_anterior, $mes);
        $xFavoritos = post::bestFavoritos(TOP_CANTIDAD, $mes_anterior, $mes);
        $xComentarios = post::bestComentarios(TOP_CANTIDAD, $mes_anterior, $mes);
        $xSeguidores = post::bestSeguidores(TOP_CANTIDAD, $mes_anterior, $mes);
        $xPuntos = _::factory($xPuntos, 'id_post', 'post');
        $xFavoritos = _::factory($xFavoritos, 'id_post', 'post');
        $xComentarios = _::factory($xComentarios, 'id_post', 'post');
        $xSeguidores = _::factory($xSeguidores, 'id_post', 'post');
        foreach($xPuntos as $post)
        {
            $top = new top_cache();
            $top->tipo = 1;
            $top->espectro = 5;
            $top->id_post = $post->id_post;
            $top->save();
        }
        foreach($xFavoritos as $post)
        {
            $top = new top_cache();
            $top->tipo = 2;
            $top->espectro = 5;
            $top->id_post = $post->id_post;
            $top->save();
        }
        foreach($xComentarios as $post)
        {
            $top = new top_cache();
            $top->tipo = 3;
            $top->espectro = 5;
            $top->id_post = $post->id_post;
            $top->save();
        }
        foreach($xSeguidores as $post)
        {
            $top = new top_cache();
            $top->tipo = 4;
            $top->espectro = 5;
            $top->id_post = $post->id_post;
            $top->save();
        }
    }
    if(TOP_CACHEADO_ANIO)
    {
        $anio = time()-60*60*365;
        $xPuntos = post::bestPuntos(TOP_CANTIDAD, $anio);
        $xFavoritos = post::bestFavoritos(TOP_CANTIDAD, $anio);
        $xComentarios = post::bestComentarios(TOP_CANTIDAD, $anio);
        $xSeguidores = post::bestSeguidores(TOP_CANTIDAD, $anio);
        $xPuntos = _::factory($xPuntos, 'id_post', 'post');
        $xFavoritos = _::factory($xFavoritos, 'id_post', 'post');
        $xComentarios = _::factory($xComentarios, 'id_post', 'post');
        $xSeguidores = _::factory($xSeguidores, 'id_post', 'post');
        foreach($xPuntos as $post)
        {
            $top = new top_cache();
            $top->tipo = 1;
            $top->espectro = 6;
            $top->id_post = $post->id_post;
            $top->save();
        }
        foreach($xFavoritos as $post)
        {
            $top = new top_cache();
            $top->tipo = 2;
            $top->espectro = 6;
            $top->id_post = $post->id_post;
            $top->save();
        }
        foreach($xComentarios as $post)
        {
            $top = new top_cache();
            $top->tipo = 3;
            $top->espectro = 6;
            $top->id_post = $post->id_post;
            $top->save();
        }
        foreach($xSeguidores as $post)
        {
            $top = new top_cache();
            $top->tipo = 4;
            $top->espectro = 6;
            $top->id_post = $post->id_post;
            $top->save();
        }
    }
    if(TOP_CACHEADO_HISTORICO)
    {
        $posts = _::factory(post::getAll('ORDER BY puntos DESC LIMIT '.TOP_CANTIDAD),'id_post', 'post');
        foreach($posts as $post)
        {
            $top = new top_cache();
            $top->tipo = 1;
            $top->espectro = 7;
            $top->id_post = $post->id_post;
            $top->save();
        }
        
        $posts = _::factory(post::getAll('ORDER BY favoritos DESC LIMIT '.TOP_CANTIDAD),'id_post', 'post');
        foreach($posts as $post)
        {
            $top = new top_cache();
            $top->tipo = 2;
            $top->espectro = 7;
            $top->id_post = $post->id_post;
            $top->save();
        }
        $posts = _::factory(post::getAll('ORDER BY comentarios DESC LIMIT '.TOP_CANTIDAD),'id_post', 'post');
        foreach($posts as $post)
        {
            $top = new top_cache();
            $top->tipo = 3;
            $top->espectro = 7;
            $top->id_post = $post->id_post;
            $top->save();
        }
        $posts = _::factory(post::getAll('ORDER BY seguidores DESC LIMIT '.TOP_CANTIDAD),'id_post', 'post');
        foreach($posts as $post)
        {
            $top = new top_cache();
            $top->tipo = 4;
            $top->espectro = 7;
            $top->id_post = $post->id_post;
            $top->save();
        }
    }
    _::$view->ajax(array('error', true));
});

_::define_controller('cron_sitemap', function(){
    $filename = __dir__.'/../sitemap.xml';
    $output = '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL.'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'.PHP_EOL;
    $posts = post::getAll('WHERE borrador = 0 AND revision = 0 ORDER BY id_post DESC LIMIT 20000');
    foreach($posts as $post)
     {
       $postObject = new post($post['id_post']);
       $output.= '<url><loc>'._::$globals['WEB_LINK'].'/posts/'.$postObject->id_post.'/'.$postObject->o_categoria->nombre_seo.'/'.$postObject->titulo_seo.'</loc><lastmod>'.date('Y-m-d', $postObject->fecha_publicacion).'</lastmod><changefreq>daily</changefreq><priority>1</priority></url> '.PHP_EOL;
       unset($postObject);
     }
    $output.= '</urlset>'.PHP_EOL;
    file_put_contents($filename, $output);
    echo $output;
    file_get_contents('http://google.com/ping?sitemap='.urlencode(_::$globals['WEB_LINK'].'/sitemap.xml'));
    die();
});