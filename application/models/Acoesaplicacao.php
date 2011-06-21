<?php 
/**
 *  Modelo da tabela relacionamento das ações ao aplicacoes
 *
 * @filesource
 * @author Paulo Soares da Silva
 * @copyright P2S System - Soluções Web
 * @package SysWeb
 * @subpackage Default.Model
 * @version 1.0
 */
class AcoesAplicacao extends P2s_Db_Table_Abstract {
	
    /**
     * Nome da tabela no banco de dados
     * @var String
     */
    protected $_name = 'aplicacoes_acoes';
    /**
     * Coluna da chave primária da tabela
     * @var String | Array
     */
    protected $_primary = 'id';
    

    /**
     * Construtor do modelo
     */
    public function  __construct() {
        parent::__construct();
        $this->_fieldKey = 'id';
        $this->_orderField = 'aplicacao';
        $this->_fieldSearch = array('field'=>'a_nome','label'=>'Aplicação:');

        $this->_fieldLabel = array(
            'id'=>'Id',
            'aplicacao' => 'Aplicação',
            'acoes' => 'Ações'
            );
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

           $select->from(array('a' => 'aplicacoes'), array('aplicacao'=>'a.nome'))
                   ->from(array('c'=>'aplicacoes_acoes'), array('id'=>'c.id','acoes'))
                   ->where('c.aplicacoes_id = a.id');
           return $select;

       }
    /**
     * Consulta de ações 
     * @param string $where
     * @return Zend_Db_Table
     */
    public function  selectAcoes($where = null){

           $db= Zend_Db_Table::getDefaultAdapter();
           $select = new Zend_Db_Select($db);
           

           if ($where !== null)	{
			$select->where($where);
            }

           $select->from(array('aplicacoes_acoes'));
                  
           $sql = $select->query();

           return $sql->fetchAll();

    }

   
}
