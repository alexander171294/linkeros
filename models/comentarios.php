<?php class_exists('_') || die('FORBIDDEN');

// this represent structure of table example
class comentarios extends table
{
    // the primary keys (use array to multiple keys)
    protected $primaryKeys = 'id_comentario'; // one or multiple keys
    
    public $id_comentario;
    public $id_usuario;
    public $id_moderador;
    public $comentario;
    public $razon_editado;
    public $positivos;
    public $negativos;
    public $fecha;
    public $id_post;
    public $cached_bbc;
    
    public $usuario_objeto;
    public $fecha_formateada;
    
    private $modObject;
    private $postObject;
    
    public function __construct($ids = null)
    {
        parent::__construct($ids);
        if(!$this->void)
        {
            $this->usuario_objeto = new usuarios($this->id_usuario);
            $o = new _date($this->fecha);
            $o->fHour(':')->fMinute(' ')->fDay('/')->fMonth('/')->fYear();
            $this->fecha_formateada = $o->format();
        }
    }
    
    static public function getAllByPost($post)
    {
        return parent::getAll('WHERE id_post = ?', array($post));
    }
    
    public function parse_content()
    {
        if(!empty($this->cached_bbc)) return $this->cached_bbc;
        _::declare_component('bbcode');
        $bbcodes = include('others/bbcode.php');
        $bbparser = new bbparser($bbcodes);
        $contenidoBloqueado = array();
        $out = $bbparser->parse($this->comentario, $contenidoBloqueado);
        $out = $this->parseCitas($out);
        $out = $this->parseEmoti($out);
        $out = nl2br(str_replace('&amp;','&',$out));
        $this->cached_bbc = $out;
        parent::save();
        return  $out;
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
    
    private function parseCitas($in)
    {
        $regex = '/\[cita=([0-9]+)\]([^[]+)\[\/cita\]/';
        
        $fr = array();
        preg_match_all($regex, $in, $fr);
        $out = $in;
        for($i = 0; $i<count($fr[1]); $i++)
        {
            if($fr[1][$i] == $this->id_comentario || $fr[1][$i] >= $this->id_comentario) continue;
            $regex_repl = '/\[cita='.$fr[1][$i].'\]([^[]+)\[\/cita\]/';
            $cita = new comentarios($fr[1][$i]);
            if($cita->void)
            {
                $out = preg_replace($regex_repl, '<div class="cita"><a class="user" href="#">Oops!</a> <div class="contentCita"><b>Este comentario fué eliminado</b></div> <div class="fecha"></div></div>', $out);
            } else
            $out = preg_replace($regex_repl, '<div class="cita"><a class="user" href="/usuario/'.$cita->usuario_objeto->id_usuario.'/'.$cita->usuario_objeto->nick_seo.'">@'.$cita->usuario_objeto->nick.'</a> dijo: <div class="contentCita">'.$cita->parse_content().'</div> <div class="fecha">'.$cita->fecha_formateada.'</div></div>', $out);
        }
        return $out;
    }
    
    public function isPositive()
    {
        return $this->positivos-$this->negativos>0;
    }
    
    public function countPoints(){
        return $this->positivos-$this->negativos;
    }
    
    public function getModerador()
    {
        if(empty($this->modObject)) $this->modObject = new usuarios($this->id_moderador);
        return $this->modObject;
    }
    
    public function getPost()
    {
        if(empty($this->postObject)) $this->postObject = new post($this->id_post);
        return $this->postObject;
    }
    
    public function parse_retorno()
    {
        $this->comentario = str_replace(array("\r\n", "\r", "\n"), null, $this->comentario);
        //$this->comentario = preg_replace('/(<br \/>)((<br \/>)+)/i', "<br />", $this->comentario);
        return $this->comentario;
    }
    
    public function getHora()
    {
        return date('H:i', $this->fecha);
    }
}