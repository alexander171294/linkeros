<?php class_exists('_') || die('FORBIDDEN');

class mensajes_enviados extends table
{
    // the primary keys (use array to multiple keys)
    protected $primaryKeys = 'id_mensaje'; // one or multiple keys
    
    public $id_mensaje;
    public $id_emisor;
    public $id_receptor;
    public $asunto_mensaje;
    public $contenido_mensaje;
    public $fecha_mensaje;
    
    
    public $o_sender = null;
    
    static public function getMyMPS($id)
    {
        return parent::getAll('WHERE id_emisor = ? ORDER BY id_mensaje DESC', array($id));
    }
    
    public function user()
    {
        if(!is_object($this->o_sender))
            $this->o_sender = new usuarios($this->id_receptor);
        return $this->o_sender;
    }
    
    public function getFecha()
    {
        $fecha = new _date($this->fecha_mensaje);
        return $fecha->fHour(':')->fMinute(' - ')->fDay('/')->fMonth('/')->fYear()->format();
    }
    
    static public function vaciar($me)
    {
        parent::deleteAll('WHERE id_emisor = ?', array($me));
    }
    
}