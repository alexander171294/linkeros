<?php class_exists('_') || die('FORBIDDEN');

// this represent structure of table example
class perfiles extends table
{
    // the primary keys (use array to multiple keys)
    protected $primaryKeys = 'id_usuario'; // one or multiple keys
    
    public $id_usuario;
    public $facebook;
    public $twitter;
    public $steam;
    public $battlenet;
    public $xbox;
    public $intereses;
    public $hobbies;
    public $series;
    public $musica;
    public $deportes;
    public $libros;
    public $peliculas;
    public $comidas;
    public $heroes;
    
    static public function exists($id)
    {
        $r = parent::getUnique('WHERE id_usuario = ?', array($id));
        return isset($r['id_usuario']);
    }
    
}