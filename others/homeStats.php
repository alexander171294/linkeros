<?php class_exists('_') || die('FORBIDDEN');

function getHomeStats($historico = true, $timeSpectro = 1)
{
    if($historico)
    {
        $post_xPuntos = _::factory(post::getAll('ORDER BY puntos DESC LIMIT '.TOP_HOME_CANTIDAD),'id_post', 'post');
    } else {
        $hoy = time()-60*60*24*$timeSpectro;
        $post_xPuntos = _::factory(post::bestPuntos(TOP_CANTIDAD, $hoy), 'id_post', 'post');
    }
    
    $users_xPuntos = _::factory(usuarios::getAll('ORDER BY puntos_obtenidos DESC LIMIT '.TOP_HOME_CANTIDAD),'id_usuario', 'usuarios');
    
    return array('posts' => $post_xPuntos, 'users' => $users_xPuntos);
}