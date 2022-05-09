<?php

function formatThePost($titulo, $category = 'OffTopic', $tags = array(), $content = null)
{
    $out = '<xml>'.PHP_EOL;
    $out .= '<title>'.$titulo.'</title>'.PHP_EOL;
    $out .= '<category>'.$category.'</category>'.PHP_EOL;
    $out .= '<tagList>'.PHP_EOL;
    foreach($tags as $tag)
    {
        $out .= '<tag>'.$tag.'</tag>';
    }
    $out .= '</tagList>'.PHP_EOL;
    $out .= '<content>'.PHP_EOL;
    $out .= $content;
    $out .= '</content>'.PHP_EOL;
    $out .= '</xml>'.PHP_EOL;
    return $out;
}

function saveThePost($location, $format)
{
    $uid = uniqid();
    $time = time();
    file_put_contents($location.'/'.$time.$uid.'.xml', $format);
}

function curlGet($link, $timeOut = 3)
{
	$curl = curl_init();
	curl_setopt_array($curl, array(
			CURLOPT_URL => $link,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_CONNECTTIMEOUT => $timeOut,
			//CURLOPT_FILETIME => false,
			CURLOPT_USERAGENT => 'iPhone',
	));
	$result =  curl_exec($curl);
	curl_close($curl);
	return $result;
}

function str_standaryze($str)
{
	$find = array('á', 'é', 'í', 'ó', 'ú', 'ñ');
    $repl = array('a', 'e', 'i', 'o', 'u', 'n');
    $str = str_replace ($find, $repl, $str);
	$str = iconv(mb_detect_encoding($str, mb_detect_order(), true), "UTF-8", $str);
    return html_entity_decode(html_entity_decode(html_entity_decode(htmlentities(convert_to($str, 'UTF-8')))));
}

function convert_to ( $source, $target_encoding )
    {
    // detect the character encoding of the incoming file
    $encoding = mb_detect_encoding( $source, "auto" );
       
    // escape all of the question marks so we can remove artifacts from
    // the unicode conversion process
    $target = str_replace( "?", "[question_mark]", $source );
       
    // convert the string to the target encoding
    $target = mb_convert_encoding( $target, $target_encoding, $encoding);
       
    // remove any question marks that have been introduced because of illegal characters
    $target = str_replace( "?", "", $target );
       
    // replace the token string "[question_mark]" with the symbol "?"
    $target = str_replace( "[question_mark]", "?", $target );
   
    return $target;
    }

function w1250_to_utf8($text) {
    // map based on:
    // http://konfiguracja.c0.pl/iso02vscp1250en.html
    // http://konfiguracja.c0.pl/webpl/index_en.html#examp
    // http://www.htmlentities.com/html/entities/
    $map = array(
        chr(0x8A) => chr(0xA9),
        chr(0x8C) => chr(0xA6),
        chr(0x8D) => chr(0xAB),
        chr(0x8E) => chr(0xAE),
        chr(0x8F) => chr(0xAC),
        chr(0x9C) => chr(0xB6),
        chr(0x9D) => chr(0xBB),
        chr(0xA1) => chr(0xB7),
        chr(0xA5) => chr(0xA1),
        chr(0xBC) => chr(0xA5),
        chr(0x9F) => chr(0xBC),
        chr(0xB9) => chr(0xB1),
        chr(0x9A) => chr(0xB9),
        chr(0xBE) => chr(0xB5),
        chr(0x9E) => chr(0xBE),
        chr(0x80) => '&euro;',
        chr(0x82) => '&sbquo;',
        chr(0x84) => '&bdquo;',
        chr(0x85) => '&hellip;',
        chr(0x86) => '&dagger;',
        chr(0x87) => '&Dagger;',
        chr(0x89) => '&permil;',
        chr(0x8B) => '&lsaquo;',
        chr(0x91) => '&lsquo;',
        chr(0x92) => '&rsquo;',
        chr(0x93) => '&ldquo;',
        chr(0x94) => '&rdquo;',
        chr(0x95) => '&bull;',
        chr(0x96) => '&ndash;',
        chr(0x97) => '&mdash;',
        chr(0x99) => '&trade;',
        chr(0x9B) => '&rsquo;',
        chr(0xA6) => '&brvbar;',
        chr(0xA9) => '&copy;',
        chr(0xAB) => '&laquo;',
        chr(0xAE) => '&reg;',
        chr(0xB1) => '&plusmn;',
        chr(0xB5) => '&micro;',
        chr(0xB6) => '&para;',
        chr(0xB7) => '&middot;',
        chr(0xBB) => '&raquo;',
    );
    return html_entity_decode(mb_convert_encoding(strtr($text, $map), 'UTF-8', 'ISO-8859-2'), ENT_QUOTES, 'UTF-8');
}