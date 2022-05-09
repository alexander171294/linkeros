<?php class_exists('_') || die('FORBIDDEN');

$lista = array();

// [hr] -> <hr />
$lista[] = array('id' => 'hr', 'html_base' => '<hr />', 'type' => BBPARSER_SIMPLE);
$lista[] = array('id' => 'br', 'html_base' => '<br />', 'type' => BBPARSER_SIMPLE);

// [url=?]title[/url] -> <a href="?">title</a>
$lista[] = array('id' => 'url', 'html_base' => '<a href="?">', 'html_final'=>'</a>', 'type' => BBPARSER_PARAMS);
$lista[] = array('id' => 'color', 'html_base' => '<span style="color:?">', 'html_final'=>'</span>', 'type' => BBPARSER_PARAMS);

// [b]alex[/b] -> <b>alex</b>
$lista[] = array('id' => 'b', 'html_base' => '<b>', 'html_final'=>'</b>', 'type' => BBPARSER_DOUBLE);
$lista[] = array('id' => 'i', 'html_base' => '<i>', 'html_final'=>'</i>', 'type' => BBPARSER_DOUBLE);
$lista[] = array('id' => 'u', 'html_base' => '<u>', 'html_final'=>'</u>', 'type' => BBPARSER_DOUBLE);
$lista[] = array('id' => 'del', 'html_base' => '<del>', 'html_final'=>'</del>', 'type' => BBPARSER_DOUBLE);
$lista[] = array('id' => 'ul', 'html_base' => '<ul class="listPostUL">', 'html_final'=>'</ul>', 'type' => BBPARSER_DOUBLE);
$lista[] = array('id' => 'li', 'html_base' => '<li>', 'html_final'=>'</li>', 'type' => BBPARSER_DOUBLE);

// [img]?[/img] -> <img src="?" />
$lista[] = array('id' => 'img', 'html_base' => '<img src="', 'html_final'=>'" />', 'type' => BBPARSER_DOUBLE);
$lista[] = array('id' => 'youtube', 'html_base' => '<iframe src="https://www.youtube.com/embed/', 'html_final' => '" class="youtubeEmbed"></iframe>', 'type' => BBPARSER_DOUBLE);

$lista[] = array('id' => 'font', 'html_base' => '<span style="font-family:?">', 'html_final'=>'</span>', 'type' => BBPARSER_PARAMS);
$lista[] = array('id' => 'size', 'html_base' => '<span style="font-size:?">', 'html_final'=>'</span>', 'type' => BBPARSER_PARAMS);
$lista[] = array('id' => 'align', 'html_base' => '<p style="text-align:?">', 'html_final'=>'</p>', 'type' => BBPARSER_PARAMS);

$lista[] = array('id' => 'locked', 'html_base' => '<div class="contentBlocked"><p>Este contenido se encuentra bloqueado<br />Ingresa el captcha para desbloquearlo:</p><div id="g-recaptchaInPost" class="g-recaptcha" data-sitekey="'.RECAPTCHA_KEY.'"></div><button class="btn btn-azul" id="unlockContent">Desbloquear</button></div>', 'type' => BBPARSER_SUPRESS);

return $lista;