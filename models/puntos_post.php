<?php class_exists('_') || die('FORBIDDEN');

// this represent structure of table example
class puntos_post extends table
{
    // the primary keys (use array to multiple keys)
    protected $primaryKeys = array('id_post', 'id_usuario'); // one or multiple keys
    
    public $id_post;
    public $id_usuario;
    public $cantidad;
    public $fecha;
    
    public $post_Object = null;
    
    static public function puntue($me, $post)
    {
        $r = parent::getUnique('WHERE id_usuario = ? AND id_post = ?', array($me, $post));
        return isset($r['id_usuario']);
    }
    
    public function getFecha()
    {
        return date('H:i d/m/Y', $this->fecha);
    }
    
    public function getPost()
    {
        if(empty($this->post_Object)) $this->post_Object = new post($this->id_post);
        return $this->post_Object;
    }
    
    public function getHora()
    {
        return date('H:i', $this->fecha);
    }
    
}