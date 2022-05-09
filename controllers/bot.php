<?php if(!CRON_CORE || ((string)_::$get['key'] !== CRON_KEY)) die();

_::define_controller('bot_newuser', function(){
    $users = file_get_contents('ini_files/bot_usuarios.ini');
    $users = explode("\r\n", $users);
    $intento = 0;
    if(count($users)>0)
    {
        $new_user_ok = false;
        while($new_user_ok == false)
        {
            $nuser = strtolower($users[$intento]);
            unset($users[$intento]);
            // existe?
            if(!usuarios::exists_nick($nuser))
            { // lo registramos
                $usuario = new usuarios(null);
                $usuario->nick = $nuser;
                $usuario->nombre = ' ';
                $usuario->email = $nuser.'@linkeros.com';
                $usuario->id_rango = 4; // nfu
                $usuario->sexo = mt_rand(1,2);
                $usuario->dia_n = mt_rand(1,29);
                $usuario->mes_n = mt_rand(1,12);
                $usuario->anio_n = mt_rand(1980,2000);
                $usuario->pais = 1;
                $usuario->region = ' ';
                $usuario->status_user = 1; // 0 pendiente de email.
                $usuario->token_activacion = md5(base64_encode(mt_rand(10000, 999999).'TOKEN'));
                $usuario->puntos_obtenidos = 0;
                $usuario->puntos_disponibles = 0;
                $usuario->post_creados = 0;
                $usuario->comentarios_creados = 0;
                $usuario->seguidores = 0;
                $usuario->siguiendo = 0;
                $usuario->mensaje_perfil = ' ';
                $usuario->password = password_hash('BUM123456K1', PASSWORD_DEFAULT);
                $usuario->avatar = '/themes/default/images/default-avatar.jpg';
                $usuario->nfu_desde = time();
                $usuario->fecha_registro = time();
                // damos de alta el usuario
                $id = $usuario->save();
        
                $bot_usuarios = new bot_usuarios();
                $bot_usuarios->id_usuario = $id;
                $bot_usuarios->usos = 0;
                $bot_usuarios->save();
                
                $new_user_ok = true;
                _::$view->ajax(array('status' => 'ok'));
            } else { // buscamos otro
                $intento++;
                // si no hay siguiente terminar
                if(!isset($users[$intento])) $new_user_ok = true;
                // si hay siguiente reintentar
                _::$view->ajax(array('status' => 'no new user'));
            }
        }
        
        // guardamos los cambios
        $users = implode("\r\n", $users);
        file_put_contents('ini_files/bot_usuarios.ini', $users);
    } else _::$view->ajax(array('status' => 'void file'));
});

_::define_controller('bot_comments', function(){
    $comentarios = file_get_contents('ini_files/bot_comments.ini');
    $nros = explode(',', $comentarios);
    $min = $nros[0];
    $max = $nros[1];
    // minimo y maximo de comentarios que debe tener un post y con bots
    // es decir, si tiene menos del minimo los bots van a comentar
    // hasta llegar a un numero al azar entre el minimo y el maximo
    
    // obtener post que necesitan comentarios
    $posts = _::factory(post::getAll('WHERE comentarios_obtenidos < '.$min.' AND nub_section = 0'), 'id_post', 'post');
    foreach($posts as $post)
    {
        // cuantos comentarios queremos tener?
        $cantidad = mt_rand($min, $max);
        // cuantos faltan?
        $total = $cantidad - $post->comentarios;
        // creamos los comentarios necesarios
        for($i = 0; $i < $total; $i++)
        {
            $st = array('gracias', 'genial', 'un espectaculo', 'graciela por el aporte', 'se agradece', 'muchas gracias');
            $nd = array('maquinola!', 'capo!', 'groso!', 'eminencia!', 'genialidad!', 'master');
            $rd = array('segui asi', 'mas aportes asi hacen falta', 'te doy puntos (si me quedan xD)', 'a favoritos :O', 'otro gran aporte');
            $th = array('+1 (es lo que me queda)', '+2 no tengo mas :(', '+3 ya di mis otros 7', '+4 no me quedan mas', '+5 ;)', '+6 :)', '+7 es lo que hay :P', '+8 asi me quedan para otro post', '+9 casi casi 10 xD', '+10');
            
            $st = $st[mt_rand(0, count($st)-1)];
            $nd = $nd[mt_rand(0, count($nd)-1)];
            $rd = $rd[mt_rand(0, count($rd)-1)];
            $action = mt_rand(0, count($th)-1);
            $th = $th[$action];
            
            $final = $st.' '.$nd.', '.$rd.' '.$th;
            $final_if_is_author = ':)';
            
            // obtenemos un bot al azar
            $user = new usuarios(bot_usuarios::getRand('id_usuario')['id_usuario']);
            
            if($post->id_usuario == $user->id_usuario)
                $contenido = $final_if_is_author;
            else
                $contenido = $final;
            
            // creamos el comentario
            $comment = new comentarios();
            $comment->id_usuario = $user->id_usuario;
            $comment->id_moderador = 0;
            $comment->comentario = (string)$contenido;
            $comment->razon_editado = ' ';
            $comment->positivos = 0;
            $comment->negativos = 0;
            $comment->fecha = time();
            $comment->id_post = $post->id_post;
            $comment->save();
            
            $user->comentarios_creados++;
            $user->save();
            
            $post->comentarios_obtenidos++;
            if($post->id_usuario != $user->id_usuario)
                $post->puntos += $action+1;
            $post->save();
            
            if($post->id_usuario != $user->id_usuario){
                $autor = new usuarios($post->id_usuario);
                $autor->puntos_obtenidos += $action+1;
                $autor->save();
            }
        }
    }
    _::$view->ajax(array('status' => 'ready'));
});



_::define_controller('xml_importer', function(){
    $base = __DIR__.'/../ini_files/xml';
    $files = scandir($base);
    if(!isset($files[2])) die();
    $file = $files[mt_rand(2, count($files)-1)];
    $xml = simplexml_load_file($base.'/'.$file);
    agregarPost((string)$xml->title[0], (string)$xml->category[0], $xml->tagList[0]->tag, (string)$xml->content[0]);
    unlink($base.'/'.$file);
    _::$view->ajax(array('status' => 'ready'));
});

function agregarPost($titulo, $categoria, $tags, $contenido)
{
    if(empty($titulo) || empty($contenido)) return false;
    // buscar id categoria ///////////////////////
    $categoria = strtolower($categoria);
    if(strpos($categoria, 'juegos') !== false) $categoria = 'juegos';
    if(strpos($categoria, 'juegos') !== false) $categoria = 'juegos';
    $cats = _::factory(categorias::getBySearch($categoria), 'id_categoria', 'categorias');
    if(count($cats)>0)
    {
        $catid = $cats[0]->id_categoria;
    }
    
    if(!isset($catid))
        $catid = 27; // offtopic por defecto
    
    $post = new post();
	$post->titulo = $titulo;
	$post->contenido = $contenido;
	$post->categoria = $catid;
	$post->comentarios = 1;
	$post->publico = 1;
	$post->puntos = 0;
	$post->visitas = 0;
	$post->favoritos = 0;
	$post->seguidores = 0;
	$post->nub_section = 0;
    // obtenemos un bot al azar
    $idu = bot_usuarios::getRand('id_usuario')['id_usuario'];
    $bu = new bot_usuarios($idu);
    $bu->usos++;
    $bu->save();
    $user = new usuarios($idu);
	$post->id_usuario = $user->id_usuario;
	$post->patrocinado = 0;
	$post->sticky = 0;
	$post->fecha_publicacion = time();
	$post->borrador = 0;
	$post->comentarios_obtenidos = 0;
    $post->revision = 0;
	$idp = $post->save();
    
    // los tags
    //$tags = explode(';', $tags);
	foreach($tags as $tag)
	{
		$tagI = tags::getExists((string)$tag);
		if($tagI === false)
		{
			$tagO = new tags();
			$tagO->texto_tag = (string)$tag;
			$tagO->repeticiones = 1;
			$tagI = $tagO->save();
		} else {
			$tagO = new tags($tagI);
			$tagO->repeticiones++;
			$tagO->save();
		}
		$index = new tags_post();
		$index->id_post = $idp;
		$index->id_tag = $tagI;
		$index->save();
	}
    
    $user->post_creados++;
    $user->save();
}