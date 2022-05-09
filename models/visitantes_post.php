<?php class_exists('_') || die('FORBIDDEN');

// this represent structure of table example
class visitantes_post extends table
{
    // the primary keys (use array to multiple keys)
    protected $primaryKeys = array('id_post', 'id_usuario'); // one or multiple keys
    
    public $id_usuario;
    public $id_post;
    public $fecha;
    
    static public function is_visited($me, $post)
    {
        $r = parent::getUnique('WHERE id_usuario = ? AND id_post = ?', array($me, $post));
        return isset($r['id_usuario']);
    }
    
}