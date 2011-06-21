<?php 
/**
 * Modelo da tabela acessos ao sistema
 *
 * @filesource
 * @author Paulo Soares da Silva
 * @copyright P2S System - Soluções Web
 * @package SysWeb
 * @subpackage Default.Model
 * @version 1.0
 */
class Logacesso extends P2s_Db_Table_Abstract {
	
    /**
    * Nome da tabela no banco de dados
    * @var String
    */
    protected $_name = 'logacesso';
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
        $this->_orderField = 'data_hora DESC';
        $this->_fieldSearch = array('field'=>'b_nome','label'=>'Usuário:');
        $this->_fieldLabel = array(
            'id' => 'Id',
            'nome' => 'Usuário',
            'data_hora' => 'Data',
            'ip' => 'Ip' );
       
        }
    /**
     * Consulta de listagem personalizada
     * @param String $where
     * @param String $order
     * @return Zend_Db_Select
     */
    public function  selectList($where = null, $order = null) {
           $db= Zend_Db_Table::getDefaultAdapter();
           $select = new Zend_Db_Select($db);

           if ($where !== null)	{
			$select->where($where);
            }
           if ($order !== null){
			$select->order($order);
            }

           $select->from(array('a' => 'logacesso'), array('a.id','a.data_hora','a.ip'))
                   ->joinLeft(array('b'=>'usuarios'), 'b.id = a.usuarios_id', array('b.nome'));
           return $select;

       }
    
}
