<?php class_exists('_') || die('FORBIDDEN');

// this represent structure of table example
class tags_post extends table
{
    protected $primaryKeys = array('id_post', 'id_tag'); // one or multiple keys
    
    public $id_tag;
    public $id_post;
    
    public static function getTags($idpost){
        return parent::getAll('WHERE id_post = ?', array($idpost));
    }
    
}