<?php class_exists('_') || die('FORBIDDEN');

class cache_relateds extends table{
    
    protected $primaryKeys = 'id_related_group';
    
    public $id_related_group;
    public $id_post;
    public $id_related;
    public $fecha_cacheado;
    
    static public function getRelateds($idPost)
    {
        return parent::getAll('WHERE id_post = ? && fecha_cacheado > ? LIMIT '.RELATEDS_CANTIDAD, array($idPost, time()));
    }
    
    static public function deleteOldCache($idPost)
    {
        parent::deleteAll('WHERE id_post = ?', array($idPost));
    }
}
