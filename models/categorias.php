<?php class_exists('_') || die('FORBIDDEN');

// this represent structure of table example
class categorias extends table
{
    // the primary keys (use array to multiple keys)
    protected $primaryKeys = 'id_categoria'; // one or multiple keys
    
    public $id_categoria;
    public $nombre_categoria;
    public $foto_css;
    public $nombre_seo;
    public $in_catalog;
    public $is_online;
    public $is_multiple;
    
    public function __construct($ids)
    {
        parent::__construct($ids);
        if(!$this->void)
        {
            $o = new objectVar($this->nombre_categoria);
            $this->nombre_seo = (string)$o->seo();
        }
    }
    
    static public function getBySearch($q)
    {
        $q = '%'.$q.'%';
        return parent::getAll('WHERE nombre_categoria LIKE ?', array($q));
    }
    
    static public function getFavoritosCates($user)
    {
        $favPost = favoritos_post::getAll('WHERE id_usuario = ?', array($user));
        $cates = array();
        foreach($favPost as $fav)
        {
            $post = post::getUnique('WHERE id_post = ?', array($fav['id_post']));
            $cates[$post['categoria']] = true;
        }
        $out = array();
        foreach($cates as $key => $value)
            $out[] = new categorias($key);
        return $out;
    }
    
}