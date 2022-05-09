<?php class_exists('_') || die('FORBIDDEN');

// this represent structure of table example
class usuarios extends table
{
    // the primary keys (use array to multiple keys)
    protected $primaryKeys = 'id_usuario'; // one or multiple keys
    
    // the fields:
    public $id_usuario;
    public $nick;
    public $password;
    public $nombre;
    public $email;
    public $id_rango;
    public $sexo;
    public $dia_n;
    public $mes_n;
    public $anio_n;
    public $pais;
    public $region;
    public $status_user;
    public $token_activacion;
    public $puntos_obtenidos;
    public $puntos_disponibles;
    public $post_creados;
    public $comentarios_creados;
    public $seguidores;
    public $siguiendo;
    public $mensaje_perfil;
    public $avatar;
    public $fecha_registro;
    public $nfu_desde = 0;
    public $last_activity_post = 0;
    public $last_activity_comment = 0;
    public $last_activity_mp = 0;
    public $last_ip = '';
    public $ban_reason = '';
    public $login_intentos = 0;
    public $post_catalogo = 0;
    public $aportes_catalogo = 0;
    public $puntos_catalogo = 0;
    public $theme = 1;
    
    public $nick_seo;
    public $rango_obj;
    
    public function __construct($ids = null)
    {
        parent::__construct($ids);
        $o = new objectVar($this->nick);
        $this->nick_seo = $o->seo();
    }
    
    static public function exists($id)
    {
        $r = parent::getUnique('WHERE id_usuario = ?', array($id));
        return isset($r['id_usuario']);
    }
    
    public function getRango()
    {
        if(empty($this->rango_obj)) $this->rango_obj = new rangos($this->id_rango);
        return $this->rango_obj;
    }
    
    static public function exists_nick($nick)
    {
        $r = parent::getUnique('WHERE nick = ?', array($nick));
        return isset($r['id_usuario']);
    }
    
    static public function get_by_nick($nick)
    {
        $r = parent::getUnique('WHERE nick = ?', array($nick));
        return $r['id_usuario'];
    }
    
    static public function get_by_partialNick($nick)
    {
        $r = parent::getAll('WHERE nick LIKE ?', array($nick.'%'));
        return $r;
    }
    
    static public function exists_email($email)
    {
        $r = parent::getUnique('WHERE email = ?', array($email));
        return isset($r['id_usuario']);
    }
    
    static public function get_by_email($email)
    {
        $r = parent::getUnique('WHERE email = ?', array($email));
        return $r['id_usuario'];
    }
    
    public function isAdmin()
    {
        return ($this->id_rango == 2);
    }
    
    public function isMod()
    {
        return ($this->id_rango == 2) || ($this->id_rango == 3);
    }
    
    public function isBanned()
    {
        return $this->status_user == 2;
    }
    
    static public function updatePoints($idrango, $puntos)
    {
        $q = _::$db->prepare('UPDATE usuarios SET puntos_disponibles = ? WHERE id_rango = ?');
        $q->execute(array($puntos, $idrango));
        return true;
    }
    
    static public function getBySearch($q)
    {
        $q = '%'.$q.'%';
        return parent::getAll('WHERE lower(nick) LIKE ?', array(strtolower($q)));
    }
    
    static public function getTopMods()
    {
        $mods = self::getAllObjects('id_usuario', 'WHERE id_rango = 2 OR id_rango = 3');
        $fecha = time()-24*60*60*DIAS_TOP_MODERACION;
        $modsCounts = array();
        foreach($mods as $mod)
        {
            $puntos = puntos_moderadores::count('id_moderador', 'WHERE id_moderador = ? AND fecha > ?', array($mod->id_usuario, $fecha));
            $modsCounts[] = array('puntos' => $puntos, 'uobject' => $mod);
        }
        // burbujeo
        for($i = 0; $i<count($modsCounts)-1; $i++)
            for($z = $i; $z<count($modsCounts); $z++)
                if($modsCounts[$i]['puntos'] < $modsCounts[$z]['puntos'])
                {
                    $aux = $modsCounts[$i];
                    $modsCounts[$i] = $modsCounts[$z];
                    $modsCounts[$z] = $aux;
                }
        return $modsCounts;
    }
    
    static public function getBanneds()
    {
        $users = self::getAllObjects('id_usuario', 'WHERE status_user = 2');
        $out = array();
        foreach($users as $user)
        {
            $history = mod_history::getUnique('WHERE id_target = ? AND tipo_target = 2 AND accion = 7', array($user->id_usuario));
            $history['fecha'] = date('d/m/Y', $history['fecha']);
            $history['moderador'] = new usuarios($history['id_moderador']);
            $out[] = array('user' => $user, 'history' => $history);
        }
        return $out;
    }
    
    public function getActivityHoy()
    {
        $espectro = strtotime(date('d M Y'));
        
        $posts = post::getAllObjects('id_post', 'WHERE id_usuario = ? && fecha_publicacion > ? LIMIT '.MAX_PROFILE_ACTIVITY, array($this->id_usuario, $espectro));
        
        $comentarios = comentarios::getAllObjects('id_comentario', 'WHERE id_usuario = ? && fecha > ? LIMIT '.MAX_PROFILE_ACTIVITY, array($this->id_usuario, $espectro));
        // fecha
        $puntos = puntos_post::getAllObjects(array('id_post', 'id_usuario'), 'WHERE id_usuario = ? && fecha > ? LIMIT '.MAX_PROFILE_ACTIVITY, array($this->id_usuario, $espectro));
        // fecha
        $favoritos = favoritos_post::getAllObjects(array('id_post', 'id_usuario'), 'WHERE id_usuario = ? && fecha > ? LIMIT '.MAX_PROFILE_ACTIVITY, array($this->id_usuario, $espectro));
        // fecha
        $seguidores_u = seguidores_usuarios::getAllObjects(array('id_usuario', 'id_seguidor'), 'WHERE id_seguidor = ? && fecha > ? LIMIT '.MAX_PROFILE_ACTIVITY, array($this->id_usuario, $espectro));
        $seguidores_p = seguidores_post::getAllObjects(array('id_post', 'id_usuario'), 'WHERE id_usuario = ? && fecha > ? LIMIT '.MAX_PROFILE_ACTIVITY, array($this->id_usuario, $espectro));
        // fecha
        $activity = array_merge($comentarios, $puntos, $favoritos, $seguidores_u, $seguidores_p, $posts);
        for($i = 0; $i<count($activity) AND $i<MAX_PROFILE_ACTIVITY; $i++)
            for($z = $i; $z<count($activity); $z++)
            {
                if($activity[$i]->fecha < $activity[$z]->fecha)
                {
                    $aux = $activity[$i];
                    $activity[$i] = $activity[$z];
                    $activity[$z] = $aux;
                }
            }
        return array_slice($activity, 0, MAX_PROFILE_ACTIVITY);
    }
    
    public function getActivityAyer()
    {
        $espectro['start'] = strtotime(date('d M Y'));
        $espectro['end'] = $espectro['start']-24*60*60;
        
        $posts = post::getAllObjects('id_post', 'WHERE id_usuario = ? && fecha_publicacion > ? && fecha_publicacion < ? LIMIT '.MAX_PROFILE_ACTIVITY, array($this->id_usuario, $espectro['end'], $espectro['start']));
        
        $comentarios = comentarios::getAllObjects('id_comentario', 'WHERE id_usuario = ? && fecha > ? && fecha < ? LIMIT '.MAX_PROFILE_ACTIVITY, array($this->id_usuario, $espectro['end'], $espectro['start']));
        // fecha
        $puntos = puntos_post::getAllObjects(array('id_post', 'id_usuario'), 'WHERE id_usuario = ? && fecha > ? && fecha < ? LIMIT '.MAX_PROFILE_ACTIVITY, array($this->id_usuario, $espectro['end'], $espectro['start']));
        // fecha
        $favoritos = favoritos_post::getAllObjects(array('id_post', 'id_usuario'), 'WHERE id_usuario = ? && fecha > ? && fecha < ? LIMIT '.MAX_PROFILE_ACTIVITY, array($this->id_usuario, $espectro['end'], $espectro['start']));
        // fecha
        $seguidores_u = seguidores_usuarios::getAllObjects(array('id_usuario', 'id_seguidor'), 'WHERE id_seguidor = ? && fecha > ? && fecha < ? LIMIT '.MAX_PROFILE_ACTIVITY, array($this->id_usuario, $espectro['end'], $espectro['start']));
        $seguidores_p = seguidores_post::getAllObjects(array('id_post', 'id_usuario'), 'WHERE id_usuario = ? && fecha > ? && fecha < ? LIMIT '.MAX_PROFILE_ACTIVITY, array($this->id_usuario, $espectro['end'], $espectro['start']));
        // fecha
        $activity = array_merge($comentarios, $puntos, $favoritos, $seguidores_u, $seguidores_p, $posts);
        for($i = 0; $i<count($activity) AND $i<MAX_PROFILE_ACTIVITY; $i++)
            for($z = $i; $z<count($activity); $z++)
            {
                if($activity[$i]->fecha < $activity[$z]->fecha)
                {
                    $aux = $activity[$i];
                    $activity[$i] = $activity[$z];
                    $activity[$z] = $aux;
                }
            }
        return array_slice($activity, 0, MAX_PROFILE_ACTIVITY);
    }
    
    public function getActivityAntes()
    {
        $espectro['start'] = strtotime(date('d M Y'))-48*60*60;
        
        $posts = post::getAllObjects('id_post', 'WHERE id_usuario = ? && fecha_publicacion < ? LIMIT '.MAX_PROFILE_ACTIVITY, array($this->id_usuario, $espectro['start']));
        
        $comentarios = comentarios::getAllObjects('id_comentario', 'WHERE id_usuario = ? && fecha < ? LIMIT '.MAX_PROFILE_ACTIVITY, array($this->id_usuario, $espectro['start']));
        // fecha
        $puntos = puntos_post::getAllObjects(array('id_post', 'id_usuario'), 'WHERE id_usuario = ? && fecha < ? LIMIT '.MAX_PROFILE_ACTIVITY, array($this->id_usuario, $espectro['start']));
        // fecha
        $favoritos = favoritos_post::getAllObjects(array('id_post', 'id_usuario'), 'WHERE id_usuario = ? && fecha < ? LIMIT '.MAX_PROFILE_ACTIVITY, array($this->id_usuario, $espectro['start']));
        // fecha
        $seguidores_u = seguidores_usuarios::getAllObjects(array('id_usuario', 'id_seguidor'), 'WHERE id_seguidor = ? && fecha < ? LIMIT '.MAX_PROFILE_ACTIVITY, array($this->id_usuario, $espectro['start']));
        $seguidores_p = seguidores_post::getAllObjects(array('id_post', 'id_usuario'), 'WHERE id_usuario = ? && fecha < ? LIMIT '.MAX_PROFILE_ACTIVITY, array($this->id_usuario, $espectro['start']));
        // fecha
        $activity = array_merge($comentarios, $puntos, $favoritos, $seguidores_u, $seguidores_p, $posts);
        for($i = 0; $i<count($activity) AND $i<MAX_PROFILE_ACTIVITY; $i++)
            for($z = $i; $z<count($activity); $z++)
            {
                if($activity[$i]->fecha < $activity[$z]->fecha)
                {
                    $aux = $activity[$i];
                    $activity[$i] = $activity[$z];
                    $activity[$z] = $aux;
                }
            }
        return array_slice($activity, 0, MAX_PROFILE_ACTIVITY);
    }

}