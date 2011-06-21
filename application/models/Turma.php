<?php 
/**
 * Modelo da tabela turmas
 *
 * @filesource
 * @author Paulo Soares da Silva
 * @copyright P2S System - Soluções Web
 * @package SysWeb
 * @subpackage Default.Model
 * @version 1.0
 */
class Turma  extends P2s_Db_Table_Abstract {
	
    /**
    * Nome da tabela no banco de dados
    * @var String
    */
    protected $_name = 'turma';
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
        $this->_fieldSearch = array('field'=>'nome','label'=>'Turma:');
        $this->_fieldLabel = array(
            'id' => 'Id',
            'nome' => 'Nome',
            'curso' => 'Curso',
            'ativo' => 'Ativo'
           
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

           $select->from(array('a' => 'turma'))
                   ->joinLeft(array('b'=>'curso'), 'a.curso_id = b.id', array('curso'=>'b.nome'));
           return $select;
    }
    public function  fetchTurmas($curso) {
       $db= Zend_Db_Table::getDefaultAdapter();
       $select = new Zend_Db_Select($db);
       $select->from('turma',array('id','nome'))
                       ->where('ativo = 1')
                       ->where('curso_id = ?',$curso);
      $db->setFetchMode(Zend_Db::FETCH_OBJ);
      return $result = $db->fetchAll($select);
    }
    
}
