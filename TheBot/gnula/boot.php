<?php

$outLocation = __dir__.'/../../ini_files/xml';
require(__dir__.'/../functions.php');
require(__dir__.'/../htmldom.php');

$page = file_get_contents('page.ini');
$start = (int)file_get_contents('start.ini');

$file = str_get_html(curlGet($page));

$posts = $file->find('a.Ntooltip');

$categoria = 'Peliculas Online';

for($key = $start; $key < count($posts); $key++)
{
    $tags = array('pelicula', 'ver', 'gratis', 'online');
    $post = $posts[$key];
    echo '[+] Parsing #'.($key+1).PHP_EOL;
    file_put_contents('start.ini', $key+1);
    $titulo = strip_tags(str_standaryze($post->innertext)).' [Online][+Servers]';
    $link = $post->href;
    echo '[-] Getting content of: '.$link.PHP_EOL;
    unset($post);
    $thePost = str_get_html(curlGet($link));
    
    $foto = $thePost->find('.entry img')[0]->src;
    $contenido = '[align=center][img]'.$foto.'[/img][/align]'.PHP_EOL.PHP_EOL;
    echo 'Getting Content: '.PHP_EOL;
    $first = true;
    foreach($thePost->find('.entry p') as $pTag)
    {
        echo 'Content Length: '.strlen($pTag->innertext).PHP_EOL;
        if(!$first && strlen($pTag->innertext) > 15)
        {
            $content = $pTag->innertext;
            break;
        } else if(strlen($pTag->innertext) > 15) $first = false;
    }
    $contenido .= '[b]Sinopsis[/b]'.PHP_EOL.PHP_EOL.strip_tags(str_standaryze($content)).PHP_EOL;
    $linkObjects = $thePost->find('.contenedor_tab iframe');
    $links = array();
    foreach($linkObjects as $key2 => $link)
    {
        if(empty($link->src))
        {
            $drc = 'data-src';
            $contenido .= '[b]Opcion '.($key2+1).':[/b]'.PHP_EOL.'[video]'.urlencode($link->$drc).'[/video]'.PHP_EOL.PHP_EOL;
        } else {
            $contenido .= '[b]Opcion '.($key2+1).':[/b]'.PHP_EOL.'[video]'.urlencode($link->src).'[/video]'.PHP_EOL.PHP_EOL;
        }
    }
    /*
    $Ntags = explode(',',str_standaryze($thePost->find('#informacion P')[3]->innertext));
    foreach($Ntags as $tag)
        $tags[] = $tag;*/
    foreach(explode(' ', $titulo) as $tag)
    {
        if(strlen($tag)>3)
        {
            $tags[] = $tag;
        }
    }
    $formatted = formatThePost($titulo, $categoria, $tags, $contenido);
    saveThePost($outLocation, $formatted);
    echo '[<] Saved #'.($key+1).PHP_EOL;
    echo '[!] WAITING 15 secs'.PHP_EOL;
    sleep(15);
}
