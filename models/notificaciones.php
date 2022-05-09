<?php class_exists('_') || die('FORBIDDEN');

// this represent structure of table example
class notificaciones extends table
{
    // the primary keys (use array to multiple keys)
    protected $primaryKeys = 'id_notificacion'; // one or multiple keys
    
    public $id_notificacion;
    public $id_usuario;
    public $id_actor;
    public $id_target;
    public $tipo_accion;
    public $fecha;
    public $visto;
    
    static public function countNews($me)
    {
        return parent::count('id_notificacion', 'WHERE id_usuario = ? AND visto = 0', array($me));
    }
    
    static public function getNews($me)
    {
        $q = _::$db->prepare('UPDATE notificaciones SET visto = 1 WHERE id_usuario = ? AND visto = 0');
        $q->execute(array($me));
        return parent::getAll('WHERE id_usuario = ? ORDER BY id_notificacion DESC LIMIT 8', array($me));
    }
}