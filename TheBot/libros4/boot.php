<?php

$outLocation = __dir__.'/../../ini_files/xml';
require(__dir__.'/../functions.php');
require(__dir__.'/../htmldom.php');

$page = (int)file_get_contents('page.ini');

$file = str_get_html(curlGet('http://www.libros4.com/page/'.$page.'/'));

$posts = $file->find('.postBoxTitle a');

$tags = array('libro', 'descargar', 'gratis', 'pdf');

$categoria = 'Libros/Ebooks';

foreach($posts as $key => $post)
{
    echo '[+] Parsing #'.($key+1).PHP_EOL;
    $titulo = str_standaryze($post->innertext);
    $link = $post->href;
    echo '[-] Getting content of: '.$link.PHP_EOL;
    unset($post);
    $thePost = str_get_html(curlGet($link));
    
    $foto = $thePost->find('.entry-wrap img')[0]->src;
    $contenido = '[align=center][img]'.$foto.'[/img][/align]'.PHP_EOL.PHP_EOL;
    $contenido .= str_standaryze(strip_tags($thePost->find('.entry-wrap p')[5]->innertext)).PHP_EOL;
    $contenido .= '[hr]'.PHP_EOL.'[align=center]'.PHP_EOL.str_standaryze(strip_tags($thePost->find('.entry-wrap fieldset')[0]->innertext.'[/align]')).PHP_EOL;
    $linkObjects = $thePost->find('.entry-wrap fieldset a');
    $links = '';
    foreach($linkObjects as $link)
    {
        $links .= $link->href.PHP_EOL;
    }
    $contenido .= '[locked]'.$links.'[/locked]';
    $tags[] = str_standaryze($thePost->find('#breadcrumbs a')[1]->innertext);
    $formatted = formatThePost($titulo, $categoria, $tags, $contenido);
    saveThePost($outLocation, $formatted);
    echo '[<] Saved #'.($key+1).PHP_EOL;
}

$page--;
file_put_contents('page.ini', $page);