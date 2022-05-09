<?php class_exists('_') || die('FORBIDDEN');

// this represent structure of table example
class mps_reportes extends table
{
    // the primary keys (use array to multiple keys)
    protected $primaryKeys = array('id_mp', 'id_usuario'); // one or multiple keys
    
    public $id_usuario;
    public $id_mp;
    public $id_razon_reporte;
    public $mensaje;
    public $fecha;
    public $revisado;
    
    static public function prevReported($me, $idpost)
    {
        $reporte = new mps_reportes(array($idpost, $me));
        return !$reporte->void;
    }
    
}