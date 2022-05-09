<?php

$outLocation = __dir__.'/../../ini_files/xml';
require(__dir__.'/../functions.php');
require(__dir__.'/../htmldom.php');

$series = file_get_contents('serie.ini');

foreach(explode(',',$series) as $serie)
{
        
    $file = str_get_html(curlGet(trim($serie)));
    
    $posts = $file->find('#lcholder a');
    
    $tags = array('serie', 'ver', 'gratis', 'online');
    
    $categoria = 'Series Online';
    
    $generos = $file->find('.clm a.lc');
    foreach($generos as $genero)
    {
        $tags[] = str_standaryze($genero->innertext);
    }
    
    $foto = $file->find('.clm img')[0]->src;
    $sinopsis = str_standaryze(strip_tags($file->find('.clm .sinop')[0]->innertext));
    
    foreach($posts as $key => $post)
    {
        echo '[+] Parsing #'.($key+1).PHP_EOL;
        $titulo = str_standaryze($post->innertext);
        
        $contenido = '[align=center][img]'.$foto.'[/img][/align]'.PHP_EOL.PHP_EOL;
        $contenido .= '[b]Sinopsis:[/b]'.PHP_EOL;
        $contenido .= $sinopsis.PHP_EOL.PHP_EOL;
        $contenido .= '[hr]'.PHP_EOL;
        
        $link = 'http://www.seriesw.net'.$post->href;
        echo '[-] Getting content of: '.$link.PHP_EOL;
        unset($post);
        $thePost = str_get_html(curlGet($link));
        if(!$thePost) break;
        $videos = $thePost->find('.tab_container .tab_content iframe');
        foreach($videos as $key => $video)
        {
            $dsr = 'data-src';
            if(!empty($video->src))
                $vlnk = $video->src;
            else
                $vlnk = $video->$dsr;
            if(strpos($vlnk, 'seriesw.net') > 0)
            {
                echo '[?] Getting  special LINK'.PHP_EOL;
                $real = str_get_html(curlGet($vlnk))->find('script')[1]->innertext;
                $regex = '/\{file\: \"https\:\/\/([a-zA-Z0-9\-\_\.\/=])+"/';
                $out = null;
                if(preg_match($regex, $real, $out))
                    $vlnk = str_replace(array('{file: "', '"'), null, $out[0]);
                else
                    continue;
            }
            $contenido .= '[b]Opcion '.($key+1).':[/b]'.PHP_EOL.'[video]'.urlencode($vlnk).'[/video]'.PHP_EOL;
        }
        
    
        $formatted = formatThePost($titulo, $categoria, $tags, $contenido);
        saveThePost($outLocation, $formatted);
        echo '[<] Saved #'.($key+1).PHP_EOL;
        echo '[!] WAITING 5 secs'.PHP_EOL;
        sleep(5);
    }
}

