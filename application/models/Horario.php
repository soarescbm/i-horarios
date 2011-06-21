<?php 
/**
 * Modelo da tabela horario
 *
 * @filesource
 * @author Paulo Soares da Silva
 * @copyright P2S System - Soluções Web
 * @package SysWeb
 * @subpackage Default.Model
 * @version 1.0
 */
class Horario  extends P2s_Db_Table_Abstract {
	
    /**
    * Nome da tabela no banco de dados
    * @var String
    */
    protected $_name = 'horario';
    /**
     * Coluna da chave primária da tabela
     * @var String | Array
     */
    protected $_primary = 'id';
    

    /**
    * Contrutor do modelo
    */
    public function  __construct() {
        parent::__construct();
        $this->_fieldKey = 'id';
        $this->_orderField = 'id';
        $this->_fieldSearch = array('field'=>'descricao','label'=>'Horário:');
        $this->_fieldLabel = array(
            'id' => 'Id',
            'descricao' => 'Horário',
            'turno' => 'Turmo',
            'ordem' => 'Sequência'
            );
    }
   public function  selectList($where = null, $order = null) {
       $db= Zend_Db_Table::getDefaultAdapter();
       $select = new Zend_Db_Select($db);

       if ($where !== null)	{
		    	$select->where($where);
            }
       if ($order !== null){
			     $select->order($order);
            }

           $select->from(array('a' => 'horario'), array('a.id','a.descricao','a.ordem'))
                   ->joinLeft(array('b'=>'turno'), 'a.turno_id = b.id', array('turno'=>'b.nome'));
           return $select;
    }
    
    public function fetchHorarioDisponivel($curso){
       
       $db= Zend_Db_Table::getDefaultAdapter();
       $select = new Zend_Db_Select($db);
       
       $select->from(array('a'=>'horario'),array('id'=>'a.id','horario'=>'a.descricao','a.ordem'))
               ->join(array('b'=>'turno'),'b.id = a.turno_id',array())
               ->join(array('c'=>'curso'), 'b.id = c.turno_id',array())
               ->where('c.id = ?', $curso)
               ->order('a.ordem');
       
       $db->setFetchMode(Zend_Db::FETCH_OBJ);
       return $result = $db->fetchAll($select);
    }
}
