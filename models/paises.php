<?php class_exists('_') || die('FORBIDDEN');

// this represent structure of table example
class paises extends table
{
    // the primary keys (use array to multiple keys)
    protected $primaryKeys = 'id_pais'; // one or multiple keys
    
    public $id_pais;
    public $nombre_pais;
    public $foto_css;

}