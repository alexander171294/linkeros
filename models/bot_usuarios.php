<?php class_exists('_') || die('FORBIDDEN');

class bot_usuarios extends table
{
    // the primary keys (use array to multiple keys)
    protected $primaryKeys = 'id_usuario'; // one or multiple keys
    
    // the fields:
    public $id_usuario;
    public $usos;
    
}