<?php

define('GLOBAL_KERNELL_PASSWORD', '14159265301190'); // use null para inhabilitar el guardado de configuraciones
define('LASTYEAR', date('Y'));

$mantenimiento = json_decode(file_get_contents(__DIR__.'/mantenimiento.json'));
$home = json_decode(file_get_contents(__DIR__.'/home.json'));
$avatar = json_decode(file_get_contents(__DIR__.'/avatar.json'));
$captcha = json_decode(file_get_contents(__DIR__.'/recaptcha.json'));
$rangos = json_decode(file_get_contents(__DIR__.'/rangos.json'));
$top = json_decode(file_get_contents(__DIR__.'/top.json'));
$performance = json_decode(file_get_contents(__DIR__.'/performance.json'));
$moderation = json_decode(file_get_contents(__DIR__.'/moderation.json'));

date_default_timezone_set('America/Argentina/Buenos_Aires');

define('FB_APPID', $captcha->fbappid);
define('FB_APPSECRET', $captcha->fbappsecret);

define('MANTENIMIENTO_GENERAL', $mantenimiento->mantenimiento);
define('CLOSED_MESSAGE', $mantenimiento->msg);
// SECCIONES //
define('SECCION_REGISTRO', $mantenimiento->registro);
define('SECCION_LOGIN', $mantenimiento->login);
define('SECCION_NOBS', $mantenimiento->nubs);
define('SECCION_DESTACADOS', false); // pendiente
define('SECCION_TOP', $mantenimiento->top);
define('SECCION_BUSCADOR', $mantenimiento->buscador);
define('SECCION_MPS', $mantenimiento->mps); 
define('SECCION_PERFIL', $mantenimiento->perfil);
define('SECCION_AGREGAR_POST', $mantenimiento->newpost);
define('SECCION_NOTAS_VERSION', $mantenimiento->notasVersion);
define('SECCION_NOTIFICACIONES', $mantenimiento->notificaciones);
define('SECCION_CATEGORIAS', $mantenimiento->categorias);
define('SECCION_BORRADORES', $mantenimiento->borradores);
define('SECCION_FAVORITOS', $mantenimiento->favoritos);
define('SECCION_HISTORY', $mantenimiento->history); // pendiente
define('SECCION_STATUS', $mantenimiento->serviceStatus);

// sistemas
define('SYSTEM_PUNTOS', $mantenimiento->puntos); // esta activado dar puntos?
define('SYSTEM_SEGUIDORES', $mantenimiento->seguidores);
define('SYSTEM_CONTROL', $mantenimiento->control); // está habilitada el área de control? // pendiente
define('SYSTEM_REPORTES', $mantenimiento->reportes); // está habilitado el sistema de reportes? // pendiente
define('SYSTEM_COMENTARIOS', $mantenimiento->comentarios); // está habilitado el sistema de comentarios?
define('SYSTEM_COMENTARIOS_NEW', $mantenimiento->comentariosNew); // está habilitado crear nuevo comentario

define('CRON_CORE', $mantenimiento->cron); // funciona los cron?
define('CRON_KEY', 'ASDPOFMQPWMSLDA684579224');

// HOME //

define('SECCION_REGISTRO_EMAILS', true);
define('EMAIL_WEBMASTER', 'alexander171294@gmail.com');
define('REGISTRO_SMTP', true);
define('REGISTRO_SMTP_SERVER', 'smtp.gmail.com');
define('EMAIL_WEBMASTER_PASSWORD', 'Hellsing38674516'); // solo si está SMTP activo
define('REGISTRO_SMTP_SSL', true);

// onlines ultimos 25 minutos
define('RANGO_TIEMPO_ONLINE', time()-$home->espectroTiempoOnline);
define('START_USER_COUNT', $home->startUserCount); // valor inicial de cuenta de usuarios (es un agregado de usuarios ficticios a la cuenta de usuarios)
// cantidad de comentarios a mostrar en home
define('MAX_LIST_HOME_BOXES', $home->maxListHome);
// cantidad de post a mostrar en la home y listados
define('MAX_POSTS_LIST', $home->maxPostList);

define('BOX_SHOW', $home->box);
define('BOX1_TITULO', $home->boxTitulo);
define('BOX1_CONTENIDO', $home->boxContenido);

define('STATS_HOME_HISTORICAL', $home->statsHistorical); // usar estadisticas de todos los tiempos en home
// si stats_home_historical es false, usará el siguiente tiempo para tomar el top:
define('STATS_HOME_TIMESPECTRO', $home->statsTimespectro); // top post ultimas 48 horas
define('TOP_HOME_CANTIDAD', $home->topHomeCantidad); // cantidad de post en top de la home

// probabilidades de que salga un tamaño especifico
define('HOME_TAG_SIZE1', $home->tagsize1); // 40% de que salga 1 
define('HOME_TAG_SIZE2', $home->tagsize2); // 26% de que salga 2 
define('HOME_TAG_SIZE3', $home->tagsize3); // 19% de que salga 3 
define('HOME_TAG_SIZE4', $home->tagsize4); // 10% de que salga 4 
define('HOME_TAG_SIZE5', $home->tagsize5); // 5% de que salga 5

// cantidad de tags a mostrar en la nube de tags del home
define('HOME_CANTIDAD_TAGS', $home->tags);

define('HOME_NOTIFICACIONES', $home->noticias); // activamos las notificaciones en home?

// FIN CONSTANTES HOME //

// AVATAR //
define('AVATAR_UPLOADER', $avatar->uploader);
define('AVATAR_EXTERNAL_LINK', $avatar->link);
define('AVATAR_CAM_TAKE', $avatar->cam);
// controles de avatar //
define('MAX_IMAGE_WH', $avatar->maxWH); // ¿Limitar la imagen a un tamaño especifico de alto y ancho?
define('MAX_IMAGE_WIDTH', $avatar->width); // si la constante anterior es true, esto especifica el maximo de ancho de la imagen en pixeles
define('MAX_IMAGE_HEIGHT', $avatar->height); // si la constante anterior es true, esto especifica el maximo de alto de la imagen en pixeles
define('MAX_IMAGE_SIZE', $avatar->maxSize); // limitar el tamaño de la imagen en kb (o false para no limitar)
define('AVATAR_HOSTING_EXTERNAL', $avatar->hostingExternal); // si esto está en true, los avatar desde links serán descargados y hosteados en el server propio
// si está en false se utilizará el link externo (aunque no recomendamos desactivarlo por varios motivos)

define('AVATAR_CDN', $avatar->cdn); // usar cdn para guardar link de imagen?
define('AVATAR_CDN_LINK', $avatar->cdnLink); // cual es el subdominio de avatars?

define('AVATAR_SCALE', $avatar->scale); // redimensionar imagen avatar al subirla
define('AVATAR_SCALE_WIDTH', $avatar->nWidth); // WIDTH EN PIXELES
define('AVATAR_SCALE_HEIGHT', $avatar->nHeight); // HEIGHT EN PIXELES


// RECAPTCHA //
define('RECAPTCHA_KEY', $captcha->key);
define('RECAPTCHA_SECRET', $captcha->secret);
define('REQUIRE_REGISTRO_CAPTCHA', $captcha->registro); // captcha requerido en el registro?
define('REQUIRE_LOGIN_CAPTCHA', false); // pendiente
define('LOGIN_SECURE_CAPTCHA', 3); // cantidad de intentos fallidos antes de mostrar captcha en el login // pendiente
define('REQUIRE_POST_CAPTCHA_ALL', false); // requerimos captcha para crear post
define('REQUIRE_POST_CAPTCHA_NUB', false); // los nobatos requieren captcha para crear post?

// RANGOS Y ASIGNACIONES //
define('PUNTOS_NFU', $rangos->nfu); // puntos que necesita un novato en un post para ser NFU
define('TIEMPO_FULL_USER', $rangos->fulluser); // tiempo que tienes que ser nfu para llegar a full user (60 días)
define('PUNTOS_GREAT_USER', $rangos->great); // puntos que necesita un full user en un post para ser Great User
// Modalidad aleatoria
// si está activado elige un nobato al azar que tenga un post que supere los puntos específicos
// y le asigna el rango especificado
define('M_A_ACTIVE', $rangos->modalidadAleatoria);
define('M_A_NEWB_ONLY', $rangos->MAnewbyeonly); // solo para usuarios nobatos?
define('PUNTOS_M_A', $rangos->puntosMA); // se necesita un post con x puntos para entrar en el sorteo
define('RANGO_M_A', $rangos->rangoMA); // se le asigna un rango al azar

// TOPS //
define('TOP_CACHEADO', $top->cacheado); // esto hace que los tops se calculen cuando se ejecute la tarea cron correspondiente
// si eso se desactiva puede afectar de forma negativa el rendimiento del script (y host)
define('TOP_CACHEADO_HOY', $top->cacheHoy); // cachear top de hoy?
define('TOP_CACHEADO_AYER', $top->cacheAyer); // cachear top de ayer?
define('TOP_CACHEADO_SEMANA', $top->cacheSemana); // cachear top de la semana?
define('TOP_CACHEADO_MES', $top->cacheMes); // cachear top del mes?
define('TOP_CACHEADO_MES_ANTERIOR', $top->cacheMesAnterior); // cachear top del mes anterior?
define('TOP_CACHEADO_ANIO', $top->cacheAnio); // cachear top del anio?
define('TOP_CACHEADO_HISTORICO', $top->cacheHistorico); // cachear top histórico
// CONFIG TOPS
define('TOP_CANTIDAD', $top->cantidad); // cantidad de pot que van al top

// aceptamos que se completen los formularios con autocompletado?
define('AUTOCOMPLETE_PARTIALS', $performance->autocomplete); // si esto está activado generará una carga extra de consumo de recursos sobre el script
// un ejemplo de uso de esto, es cuando envias un mp, que el campo nick te sugiera usuarios existentes
define('SEARCHER_LIMIT', $performance->searchLimit); // cantidad de resultados de busqueda
define('SEARCH_RELATEDS', $performance->searchRelateds); // buscar relacionados del post en el buscador.
// hay dos formas de obtener los relacionados, o buscarlos por titulo en la db para buscar post parecidos
// u obtener post de la misma categoría y elegir x cantidad de forma aleatoria.
// SI SE ACTIVA EL MODO BUSCADOR, ESTO PUEDE AUMENTAR CONCIDERABLEMENTE EL CONSUMO DE RECURSOS
define('RELATEDS_CANTIDAD', $performance->relatedsCantidad); // post que serán mostrados como relacionados
define('RECACHEAR_RELATEDS', $performance->recacheRelateds); // CADA CUANTO TIEMPO RECACHEAR LOS RELACIONADOS?
// esto reduce conciderablemente el consumo de recursos cuanto más tiempo se establezca para recachear
define('RECACHEAR_POSTS', $performance->recachearPosts); // forzar recachear los post continuamente, aumenta drasticamente el consumo


// MODERACIÓN
define('TOUR_PUNTOS_MP', $moderation->ptsMPs); // puntos al enviar mp
define('TOUR_PUNTOS_BORRAR', $moderation->ptsBorrar); // puntos al borrar
define('TOUR_PUNTOS_BORRADOR', $moderation->ptsBorrador); // puntos al mandar a borrador
define('TOUR_PUNTOS_IGNORAR', $moderation->ptsIgnorar); // puntos al ignorar
define('TOUR_PUNTOS_BANNEAR', $moderation->ptsBannear); // puntos al bannear

define('MOD_HISTORY_ADMIN', $moderation->history); // guardar historial para administradores
define('IPTABLES_ADMIN', $moderation->iptables);

define('MOD_TOP_MEJORES', $moderation->topMejores);
define('DIAS_TOP_MODERACION', $moderation->DInTop);

define('MAX_LOGIN_INTENT', 1); // intentos maximos de login por usuario antes de pedir captcha

define('POST_AUTO_MODERAR', $moderation->autoMod); // mover post a revisión
define('POST_DENUNCIAS_REVISION', $moderation->countDenuncias); // cuantas denuncias se necesitan para que un post vaya a revisión?
define('COMMENTS_PUNTOS_NEGATIVOS_OCULTAR', $moderation->negativosComments); // cuantos puntos negativos en un comentario se necesitan para ocultarle
// es decir, si al restar positivos y negativos da un numero negativo menor o igual que el de esta constante
// el comentario será ocultado

// ANTIFLOOD //
define('ANTIFLOOD_MAX_POST', $moderation->antifloodPost); // 1 minuto
define('ANTIFLOOD_MAX_COMMENT', $moderation->antifloodComment); // 1 minuto

// twitter share //
define('TWITTER_VIA', $captcha->twitter);

define('REDIRECT_SSL', true);
define('REDIRECT_WWW', false);

define('MAX_PROFILE_ACTIVITY', 10); // Maximo de acciones en la lista de actividades en perfil (por sección)
define('MAX_POST_IN_PROFILE', 10);
define('MAX_BLOCK_FOLLOWERS_IN_PROFILE', 8);

define('DEFAULT_AVATAR_LINK', '/themes/default/images/default-avatar.jpg');
define('BLOCK_ALL_PROXY', false);

/* CONFIGURACIONES DEL CATÁLOGO */
define('SECCION_CATALOGO', true);
define('CATALOGO_OBJECTS_PERPAGE', 18);
define('CATALOGO_CANTIDAD_REPORTES', 5); // cantidad de post en lista de most reported y most suggested

define('CATALOGO_ISMODERABLE_BY_USER', true); // es moderable por greatuser?
define('CATALOGO_USER_RANGE', 6); // rango del usuario adicional que puede editar

// sistema de puntos del catálogo
define('CATALOGO_APROVE_PUNTOS_AUTOR',10);
define('CATALOGO_APROVE_APORTES_AUTOR',1);
define('CATALOGO_APROVE_APORTES_MOD',1);
define('CATALOGO_APROVE_PUNTOS_MOD',1);

define('CATALOGO_REPORT_PUNTOS_MOD', 3);