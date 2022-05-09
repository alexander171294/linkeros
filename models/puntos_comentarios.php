<?php class_exists('_') || die('FORBIDDEN');

// this represent structure of table example
class puntos_comentarios extends table
{
    // the primary keys (use array to multiple keys)
    protected $primaryKeys = array('id_comentario', 'id_usuario'); // one or multiple keys
    
    public $id_usuario;
    public $id_comentario;
    public $tipo;
    public $fecha;
    
    static public function noLike($me, $comentario)
    {
        $r = parent::getUnique('WHERE id_usuario = ? AND id_comentario = ?', array($me, $comentario));
        return !isset($r['id_usuario']);
    }
    
}