<?php class_exists('_') || die('FORBIDDEN');

// this represent structure of table example
class seguidores_usuarios extends table
{
    // the primary keys (use array to multiple keys)
    protected $primaryKeys = array('id_usuario', 'id_seguidor'); // one or multiple keys
    
    public $id_usuario;
    public $id_seguidor;
    public $fecha;
    
    public $objUsuario = null;
    
    static public function is_follow($user, $me)
    {
        $r = parent::getUnique('WHERE id_usuario = ? AND id_seguidor = ?', array($me, $user));
        return isset($r['id_usuario']);
    }
    
    public function getFecha()
    {
        return date('H:i d/m/Y', $this->fecha);
    }
    
    public function getSeguidor(){
        if(empty($this->objUsuario)) $this->objUsuario = new usuarios($this->id_usuario);
        return $this->objUsuario;
    }
    
    public function getHora()
    {
        return date('H:i', $this->fecha);
    }
}