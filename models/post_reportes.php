<?php class_exists('_') || die('FORBIDDEN');

// this represent structure of table example
class post_reportes extends table
{
    // the primary keys (use array to multiple keys)
    protected $primaryKeys = array('id_post', 'id_usuario'); // one or multiple keys
    
    public $id_usuario;
    public $id_post;
    public $id_razon_reporte;
    public $mensaje;
    public $fecha;
    public $revisado;
    
    static public function prevReported($me, $idpost)
    {
        $reporte = new post_reportes(array($idpost, $me));
        return !$reporte->void;
    }
    
}