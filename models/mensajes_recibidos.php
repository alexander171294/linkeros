<?php class_exists('_') || die('FORBIDDEN');

class mensajes_recibidos extends table
{
    // the primary keys (use array to multiple keys)
    protected $primaryKeys = 'id_mensaje'; // one or multiple keys
    
    public $id_mensaje;
    public $id_emisor;
    public $id_receptor;
    public $asunto_mensaje;
    public $contenido_mensaje;
    public $fecha_mensaje;
    public $visto;
    
    public $o_sender = null;
    
    static public function getMyMPS($id)
    {
        return parent::getAll('WHERE id_receptor = ? ORDER BY id_mensaje DESC', array($id));
    }
    
    static public function getNewsMPS($me)
    {
        return parent::count('id_mensaje', 'WHERE id_receptor = ? AND visto = 0', array($me));
    }
    
    public function user()
    {
        if(!is_object($this->o_sender))
            $this->o_sender = new usuarios($this->id_emisor);
        return $this->o_sender;
    }
    
    public function getFecha()
    {
        $fecha = new _date($this->fecha_mensaje);
        return $fecha->fHour(':')->fMinute(' - ')->fDay('/')->fMonth('/')->fYear()->format();
    }
    
    static public function vaciar($me)
    {
        parent::deleteAll('WHERE id_receptor = ?', array($me));
    }
    
}