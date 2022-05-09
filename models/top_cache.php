<?php class_exists('_') || die('FORBIDDEN');

// this represent structure of table example
class top_cache extends table
{
    protected $primaryKeys = 'id_cacheado'; // one or multiple keys
    
    public $id_cacheado;
    public $tipo;
    public $espectro;
    public $id_post;
    
    static public function delteAll()
    {
        $q = _::$db->prepare('DELETE FROM top_cache');
        $q->execute();
    }
    
}