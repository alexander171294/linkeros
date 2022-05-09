<?php class_exists('_') || die('FORBIDDEN');

// this represent structure of table example
class moderacion_tipos_acciones extends table
{
    // the primary keys (use array to multiple keys)
    protected $primaryKeys = 'id_tipo'; // one or multiple keys
    
    public $id_tipo;
    public $descripcion;
    
}
