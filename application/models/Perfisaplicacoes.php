<?php 
/**
 * Modelo da tabela relacionamento do perfis/aplicacoes
 *
 * @filesource
 * @author Paulo Soares da Silva
 * @copyright P2S System - Soluções Web
 * @package SysWeb
 * @subpackage Default.Model
 * @version 1.0
 */
class Perfisaplicacoes extends P2s_Db_Table_Abstract {
	
    /**
    * Nome da tabela no banco de dados
    * @var String
    */
    protected $_name = 'perfis_aplicacoes';
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
        $this->_orderField = 'perfil';
        $this->_fieldSearch = array('field'=>'a_nome','label'=>'Perfil:');

        $this->_fieldLabel = array(
            'id'=>'Id',
            'perfil' => 'Perfil',
            'aplicacao' => 'Aplicação',
            'acoes' =>'Ações'
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

           $select->from(array('a' => 'perfis'), array('perfil'=>'a.nome'))
                   ->from(array('b'=>'aplicacoes'), array('aplicacao'=>'b.nome'))
                   ->from(array('c'=>'perfis_aplicacoes'), array('id'=>'c.id','acoes'=>'c.acoes'))
                   ->where('c.perfis_id = a.id AND c.aplicacoes_id = b.id');
           return $select;

       }

   
}
