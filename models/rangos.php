<?php class_exists('_') || die('FORBIDDEN');

// this represent structure of table example
class rangos extends table
{
    // the primary keys (use array to multiple keys)
    protected $primaryKeys = 'id_rango'; // one or multiple keys
    
    public $id_rango;
    public $nombre_rango;
    public $foto_css;
    public $puntos_disponibles;

    
}