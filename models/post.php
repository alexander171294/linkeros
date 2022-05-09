<?php class_exists('_') || die('FORBIDDEN');

// this represent structure of table example
class post extends table
{
    // the primary keys (use array to multiple keys)
    protected $primaryKeys = 'id_post'; // one or multiple keys
    
    public $id_post;
    public $id_usuario;
    public $titulo;
    public $contenido;
    public $categoria;
    public $comentarios;
    public $publico;
    public $puntos;
    public $visitas;
    public $favoritos;
    public $seguidores;
    public $nub_section;
    public $titulo_seo;
    public $patrocinado;
    public $sticky;
    public $o_categoria;
    public $fecha_publicacion;
    public $fecha;
    public $borrador;
    public $comentarios_obtenidos;
    public $revision;
    public $cached_bbc;
    
    public function getRecosCatalog()
    {
        return catalogo_sugerencias::count('id_post', 'WHERE id_post = ?', array($this->id_post));
    }
    
    public function prevRecoCatalog()
    {
        if(isset(_::$globals['me']))
        {
            $me = _::$globals['me'];
            return catalogo_sugerencias::getUnique('WHERE id_post = ? AND id_usuario = ?', array($this->id_post, $me->id_usuario)) ? true : false;
        }
        return false;
    }
    
    public function tituloCortado()
    {
        if(strlen($this->titulo)>=76)
        return substr($this->titulo, 0, 75);
        else return $this->titulo;
    }
    
    public function keywords()
    {
        $tags = _::factory(tags_post::getTags($this->id_post), 'id_tag', 'tags');
        $out = null;
        foreach($tags as $tag)
        {
            $out .= $tag->texto_tag.', ';
        }
        return trim($out, ', ');
    }
    
    public function contenidoCortado()
    {
        $contenido = $this->clearBBC($this->contenido);
        if(strlen($contenido)>=156)
        return substr($contenido, 0, 150).'...';
        else return $contenido;
    }
    
    public function __construct($ids = null)
    {
        parent::__construct($ids);
        if(!$this->void)
        {
            $o = new objectVar($this->titulo);
            $this->titulo_seo = (string)$o->seo();
            $this->o_categoria = new categorias($this->categoria);
            $this->fecha = $this->fecha_publicacion;
        }
    }
    
    public function parseContent(&$contenidoBloqueado, $ignoreCache = false)
    {
        if(!empty($this->cached_bbc) && !$ignoreCache) return $this->cached_bbc;
        _::declare_component('bbcode');
        $bbcodes = include('others/bbcode.php');
        $bbparser = new bbparser($bbcodes);
        $contenidoBloqueado = array();
        $out = $bbparser->parse($this->contenido, $contenidoBloqueado);
        @$contenidoBloqueado[0][0] = nl2br($contenidoBloqueado[0][0]);
        $out = $this->parseCitas($out);
        $out = $this->parseEmoti($out);
        $out = $this->parseVideo($out);
        $out = nl2br(str_replace('&amp;','&',$out));
        $this->cached_bbc = $out;
        parent::save();
        return $out;
    }
    
    protected function parseEmoti($out)
    {
        $emoticonos = emoticonos::getAll();
        foreach($emoticonos as $emoticono)
        {
            $out = str_replace('('.$emoticono['codigo'].')', '<img src="/'.coreData::$v.'images/emoticonos/'.$emoticono['imagen'].'" class="emoticonoGLB">', $out);
        }
        return $out;
    }
    
    protected function parseVideo($out)
    {
        $glbRegex = '/\[video\](.+)\[\/video\]/';
        $matches = array();
        $videotag = include('others/videotag.php');
        preg_match_all($glbRegex, $out, $matches);
        foreach($matches[1] as $key => $link)
        {
            $passed = false;
            foreach($videotag as $regex)
                if(preg_match($regex, urldecode($link)) > 0)
                {
                    
                    $passed = true;
                    break;
                }
            if($passed)
            {
                // revisamos https
                $regex = '/^http\:\/\//';
                $link = preg_replace($regex, 'https://', urldecode($link), 1);
                $out = str_replace($matches[0][$key], '<iframe src="'.$link.'"  class="youtubeEmbed"></iframe>', $out);
            }
            else
                echo '<!-- link drop: '.$link.' -->';
        }
        return $out;
    }
    
    private function parseCitas($in)
    {
        $regex = '/\[cita=([0-9]+)\]([^[]+)\[\/cita\]/';
        
        $fr = array();
        preg_match_all($regex, $in, $fr);
        $out = $in;
        for($i = 0; $i<count($fr[1]); $i++)
        {
            $regex_repl = '/\[cita='.$fr[1][$i].'\]([^[]+)\[\/cita\]/';
            $cita = new comentarios($fr[1][$i]);
            
            $out = preg_replace($regex_repl, '<div class="cita"><a class="user" href="/usuario/'.$cita->usuario_objeto->id_usuario.'/'.$cita->usuario_objeto->nick_seo.'">@'.$cita->usuario_objeto->nick.'</a> dijo: <div class="contentCita">'.$cita->parse_content().'</div> <div class="fecha">'.$cita->fecha_formateada.'</div></div>', $out);
        }
        return $out;
    }
    
    public function parse_retorno()
    {
        $this->contenido = str_replace(array("\r\n", "\r", "\n"), null, $this->contenido);
        $this->contenido = preg_replace('/(<br \/>)((<br \/>)+)/i', "<br />", $this->contenido);
        return $this->contenido;
    }
    
    public function moveToHome($idu)
    {
        $q = _::$db->prepare('UPDATE post SET nub_section = 0 WHERE id_usuario = ?');
        $q->execute(array($idu));
        return true;
    }
    
    static public function bestPuntos($limit, $time_start, $time_end = null)
    {
        $add = '';
        if(!empty($time_end)) $add = ' AND fecha < '.$time_end;
        $query = 'SELECT id_post, count(id_post) as cuenta FROM puntos_post WHERE fecha > '.$time_start.$add.' GROUP BY id_post ORDER BY cuenta DESC LIMIT '.$limit;
        $q = _::$db->prepare($query);
        $q->execute();
        $out = array();
        while($r = $q->fetch(PDO::FETCH_ASSOC))
        {
            $out[] = $r;
        }
        return $out;
    }
    
    static public function bestFavoritos($limit, $time_start, $time_end = null)
    {
        $add = '';
        if(!empty($time_end)) $add = ' AND fecha < '.$time_end;
        $query = 'SELECT id_post, count(id_post) as cuenta FROM favoritos_post WHERE fecha > '.$time_start.$add.' GROUP BY id_post ORDER BY cuenta DESC LIMIT '.$limit;
        $q = _::$db->prepare($query);
        $q->execute();
        $out = array();
        while($r = $q->fetch(PDO::FETCH_ASSOC))
        {
            $out[] = $r;
        }
        return $out;
    }
    
    static public function bestComentarios($limit, $time_start, $time_end = null)
    {
        $add = '';
        if(!empty($time_end)) $add = ' AND fecha < '.$time_end;
        $query = 'SELECT id_post, count(id_post) as cuenta FROM comentarios WHERE fecha > '.$time_start.$add.' GROUP BY id_post ORDER BY cuenta DESC LIMIT '.$limit;
        $q = _::$db->prepare($query);
        $q->execute();
        $out = array();
        while($r = $q->fetch(PDO::FETCH_ASSOC))
        {
            $out[] = $r;
        }
        return $out;
    }
    
    static public function bestSeguidores($limit, $time_start, $time_end = null)
    {
        $add = '';
        if(!empty($time_end)) $add = ' AND fecha < '.$time_end;
        $query = 'SELECT id_post, count(id_post) as cuenta FROM seguidores_post WHERE fecha > '.$time_start.$add.' GROUP BY id_post ORDER BY cuenta DESC LIMIT '.$limit;
        $q = _::$db->prepare($query);
        $q->execute();
        $out = array();
        while($r = $q->fetch(PDO::FETCH_ASSOC))
        {
            $out[] = $r;
        }
        return $out;
    }
    
    public function clearBBC($content)
    {
        $regex = '/\[([\/]?)([^\[]+)\]/';
        $content = trim(preg_replace($regex, null, $content));
        $content = html_entity_decode(html_entity_decode($content));
        $content = strip_tags($content);
        return $content;
    }
    
    static public function getBorradores($user)
    {
        return self::getAllObjects('id_post', 'WHERE id_usuario = ? AND borrador = 1', array($user));
    }
    
    static public function getRevision($user)
    {
        return self::getAllObjects('id_post', 'WHERE id_usuario = ? AND revision = 1', array($user));
    }
    
    static public function getFavoritos($user, $category = null)
    {
        if(empty($category))
            return _::factory(favoritos_post::getAll('WHERE id_usuario = ?', array($user)), 'id_post', 'post');
        
        $favs = _::factory(favoritos_post::getAll('WHERE id_usuario = ?', array($user)), 'id_post', 'post');
        $out = array();
        foreach($favs as $fav)
        {
            if($fav->categoria == $category)
                $out[] = $fav;
        }
        unset($favs);
        return $out;
    }
    
    public function reportes()
    {
        return post_reportes::count('id_post', 'WHERE id_post = ?', array($this->id_post));
    }

    public function getFecha()
    {
        return date('H:i d/m/Y', $this->fecha_publicacion);
    }
    
    public function getHora()
    {
        return date('H:i', $this->fecha_publicacion);
    }
    
}