<?php 
/**
 * Modelo da tabela curso
 *
 * @filesource
 * @author Paulo Soares da Silva
 * @copyright P2S System - Soluções Web
 * @package SysWeb
 * @subpackage Default.Model
 * @version 1.0
 */
class Curso extends P2s_Db_Table_Abstract {
	
    /**
    * Nome da tabela no banco de dados
    * @var String
    */
    protected $_name = 'curso';
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
        $this->_fieldSearch = array('field'=>'nome','label'=>'Curso:');
        $this->_fieldLabel = array(
            'id' => 'Id',
            'nome' => 'Nome',
            'turno'=> 'Turno'
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

           $select->from(array('a' => 'curso'))
                   ->joinLeft(array('b'=>'turno'), 'a.turno_id = b.id', array('turno'=>'b.nome'));
           return $select;
    }
}
