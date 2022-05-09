<?php class_exists('_') || die('FORBIDDEN');

// this represent structure of table example
class razones_reportes_mp extends table
{
    // the primary keys (use array to multiple keys)
    protected $primaryKeys = 'id_razon_reporte'; // one or multiple keys
    
    public $id_razon_reporte;
    public $nombre;
}