<?php

$outLocation = __dir__.'/../../ini_files/xml';
require(__dir__.'/../functions.php');
require(__dir__.'/../htmldom.php');

$page = (int)file_get_contents('page.ini');

$file = str_get_html(curlGet('https://www.epublibre.org/catalogo/index/'.($page*18).'/nuevo/todos/sin/todos'));

$posts = $file->find('.portada-catalogo a');

$tags = array('libro', 'descargar', 'gratis', 'pdf', 'epub', 'torrent');

$categoria = 'Libros/Ebooks';

foreach($posts as $key => $post)
{
    echo '[+] Parsing #'.($key+1).PHP_EOL;
    $link = $post->href;
    echo '[-] Getting content of: '.$link.PHP_EOL;
    unset($post);
    $thePost = str_get_html(curlGet($link));
    $titulo =  trim($thePost->find('.cab_detalle .det_titulo')[0]->innertext);

    $autor = $thePost->find('table td a')[0]->innertext;
    $foto = $thePost->find('.cab_detalle #portada')[0]->src;
    $contenido = '[align=center][img]'.$foto.'[/img][/align]'.PHP_EOL.PHP_EOL;
    $contenido .= str_standaryze(strip_tags($thePost->find('.contenido .detalle .ali_justi span')[0]->innertext)).PHP_EOL;
    $contenido .= '[hr]'.PHP_EOL.'[align=center]'.PHP_EOL.'Autor: '.str_standaryze($autor).PHP_EOL.'[/align]'.PHP_EOL;
    $olink =  $thePost->find('#enlace_zbigz');
    //var_dump($olink[0]->value);
    $link = 'http://m.zbigz.com/myfiles?url='.urlencode($olink[0]->value);
    
    $contenido .= '[locked]'.$link.'[/locked]';
    
    $tags[] = str_standaryze($thePost->find('table.negrita td a')[1]->innertext);
    $tags[] = str_standaryze($thePost->find('table.negrita td a')[0]->innertext);
    $formatted = formatThePost($titulo, $categoria, $tags, $contenido);
    saveThePost($outLocation, $formatted);
    echo '[<] Saved #'.($key+1).PHP_EOL;
    echo '[!] WAITING 15 secs'.PHP_EOL;
    sleep(15);
    
}

$page--;
file_put_contents('page.ini', $page);