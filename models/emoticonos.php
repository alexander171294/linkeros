<?php class_exists('_') || die('FORBIDDEN');

// this represent structure of table example
class emoticonos extends table
{
    // the primary keys (use array to multiple keys)
    protected $primaryKeys = 'id_emoticono'; // one or multiple keys
    
    public $id_emoticono;
    public $codigo;
    public $imagen;

}