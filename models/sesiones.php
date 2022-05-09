<?php class_exists('_') || die('FORBIDDEN');

// this represent structure of table example
class sesiones extends table
{
    // the primary keys (use array to multiple keys)
    protected $primaryKeys = 'id_usuario'; // one or multiple keys
    
    public $id_usuario;
    public $last_activity;
    public $php_sessid;
    public $ubicacion;
    public $ip;
    
    static public function isOnline($uid)
    {
        $session = sesiones::getUnique('WHERE last_activity > ? AND id_usuario = ?', array(RANGO_TIEMPO_ONLINE, $uid));
        return isset($session['id_usuario']);
    }
    
    static public function getOnlines()
    {
        return sesiones::count('id_usuario', 'WHERE last_activity > ?', array(RANGO_TIEMPO_ONLINE));
    }
    
    static public function getOnlinesObj()
    {
        return _::factory(sesiones::getAll('WHERE last_activity > ?', array(RANGO_TIEMPO_ONLINE)), 'id_usuario', 'usuarios');
    }
}