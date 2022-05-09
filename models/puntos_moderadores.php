<?php class_exists('_') || die('FORBIDDEN');

// this represent structure of table example
class puntos_moderadores extends table
{
    // the primary keys (use array to multiple keys)
    protected $primaryKeys = array('id_puntos'); // one or multiple keys
    
    public $id_puntos;
    public $id_post;
    public $id_moderador;
    public $fecha;
    public $puntos;
    public $accion;
}