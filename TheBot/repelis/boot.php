<?php

$outLocation = __dir__.'/../../ini_files/xml';
require(__dir__.'/../functions.php');
require(__dir__.'/../htmldom.php');

$page = (int)file_get_contents('page.ini');

$file = str_get_html(curlGet('http://www.repelis.tv/archivos/estrenos/pag/'.$page));

$posts = $file->find('a.info-title');

$tags = array('pelicula', 'ver', 'gratis', 'online');

$categoria = 'Peliculas Online';

foreach($posts as $key => $post)
{
    echo '[+] Parsing #'.($key+1).PHP_EOL;
    $titulo = strip_tags(str_standaryze($post->innertext));
    $link = $post->href;
    echo '[-] Getting content of: '.$link.PHP_EOL;
    unset($post);
    $thePost = str_get_html(curlGet($link));
    
    $foto = $thePost->find('.visible-desktop img.img-responsive')[0]->src;
    $contenido = '[align=center][img]'.$foto.'[/img][/align]'.PHP_EOL.PHP_EOL;
    $contenido .= str_standaryze(strip_tags($thePost->find('#pelicula p')[0]->innertext)).PHP_EOL;
    $linkObjects = $thePost->find('.tab-content iframe');
    $links = array();
    foreach($linkObjects as $key => $link)
    {
        if(empty($link->src))
        {
            $drc = 'data-src';
            $contenido .= '[b]Opcion '.($key+1).':[/b]'.PHP_EOL.'[video]'.urlencode($link->$drc).'[/video]'.PHP_EOL.PHP_EOL;
        } else {
            $contenido .= '[b]Opcion '.($key+1).':[/b]'.PHP_EOL.'[video]'.urlencode($link->src).'[/video]'.PHP_EOL.PHP_EOL;
        }
    }
    $Ntags = explode(',',str_standaryze($thePost->find('#informacion P')[3]->innertext));
    foreach($Ntags as $tag)
        $tags[] = $tag;
    $formatted = formatThePost($titulo, $categoria, $tags, $contenido);
    saveThePost($outLocation, $formatted);
    echo '[<] Saved #'.($key+1).PHP_EOL;
}

$page--;
file_put_contents('page.ini', $page);