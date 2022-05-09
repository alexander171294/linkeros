<?php class_exists('_') || die('FORBIDDEN');

// this represent structure of table example
class tags extends table
{
    protected $primaryKeys = 'id_tag'; // one or multiple keys
    
    public $id_tag;
    public $texto_tag;
    public $repeticiones;
    
    public $size;
    
    public function __construct($params = null)
    {
        $this->tirarDados();
        parent::__construct($params);
    }
    
    static public function getBySearch($q)
    {
        $q = '%'.$q.'%';
        return parent::getAll('WHERE lower(texto_tag) LIKE ?', array(strtolower($q)));
    }
    
    private function tirarDados()
    {
        // constantes definidas en constantes.php
        $st = HOME_TAG_SIZE1;
        $nd = HOME_TAG_SIZE2+$st;
        $rd = HOME_TAG_SIZE3+$nd;
        $th = HOME_TAG_SIZE4+$rd;
        $total = HOME_TAG_SIZE5+$th;
        $dado = mt_rand(1, $total);
        if($dado <= $st)
        {
            $this->size = 1;
        } elseif($dado <= $nd) {
            $this->size = 2;
        } elseif($dado <= $rd) {
            $this->size = 3;
        } elseif($dado <= $th) {
            $this->size = 4;
        } else {
            $this->size = 5;
        }
    }
    
    public function getSEO()
    {
        $tag = $this->texto_tag;
        $tagO = new objectVar($tag);
        return (string)$tagO->seo();
    }
    
    static public function getExists($texto)
    {
        $r = parent::getUnique('WHERE texto_tag = ?', array($texto));
        return isset($r['id_tag']) ? $r['id_tag'] : false;
    }
    
    static public function getRandomTags($cantidad)
    {
        $out = array();
        foreach(parent::getRand('id_tag', null, $cantidad) as $randTag)
        {
            @$out[] = new tags($randTag['id_tag']);
        }
        return $out;
    }
    
    
}