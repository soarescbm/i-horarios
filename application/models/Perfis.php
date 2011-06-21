<?php 
/**
 * Modelo da tabela perfis do sistema
 *
 * @filesource
 * @author Paulo Soares da Silva
 * @copyright P2S System - Soluções Web
 * @package SysWeb
 * @subpackage Default.Model
 * @version 1.0
 */
class Perfis extends P2s_Db_Table_Abstract {
	
     /**
    * Nome da tabela no banco de dados
    * @var String
    */
    protected $_name = 'perfis';
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
        $this->_orderField = 'nivel';
        $this->_fieldSearch = array('field'=>'a_nome','label'=>'Nome:');
        $this->_fieldLabel = array(
            'id' => 'Id',
            'nome' => 'Nome',
            'herda'=>'Herda de',
            'nivel'=>'Nível'
            );
    }
    /**
     * Consulta de listagem personalizada
     * @param String $where
     * @param String $order
     * @return Zend_Db_Select
     */
    public function  selectList($where = null, $order = null) {
           $select = parent::selectList($where, $order);

           $select->from(array('a' => 'perfis'), array('a.id','a.nome','a.nivel'))
                   ->joinLeft(array('b'=>'perfis'), 'a.herda_id = b.id', array('herda'=>'b.nome'));
           return $select;
    }
   
}
