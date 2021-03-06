<?php class_exists('_') || die('FORBIDDEN');

// model autogenerated by model_maker.php
class catalogo_sugerencias extends table
{
    // Primary keys
    protected $primaryKeys = array('id_sugerencia', 'id_post', 'id_usuario', 'id_objeto');

    // fields:
    public $id_sugerencia = null;
    public $id_post = null;
    public $id_usuario = null;
    public $id_objeto = null;
    
    static public function getBests($limit)
    {
        $q = self::$pdo->prepare('SELECT id_post, count(id_sugerencia) as SCantidad FROM catalogo_sugerencias GROUP BY id_post ORDER BY SCantidad DESC LIMIT '.$limit);
        $q->execute();
        $r = $q->fetchAll(PDO::FETCH_ASSOC);
        return _::factory($r, 'id_post', 'post');
    }

}