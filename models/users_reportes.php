<?php class_exists('_') || die('FORBIDDEN');

// this represent structure of table example
class users_reportes extends table
{
    // the primary keys (use array to multiple keys)
    protected $primaryKeys = array('id_profile', 'id_usuario'); // one or multiple keys
    
    public $id_usuario;
    public $id_profile;
    public $id_razon_reporte;
    public $mensaje;
    public $fecha;
    public $revisado = 0;
    
    static public function prevReported($me, $idpost)
    {
        $reporte = new users_reportes(array($idpost, $me));
        return !$reporte->void;
    }
    
}