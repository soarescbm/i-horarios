<?php 
/**
 *  Modelo da tabela Menus do sistema
 *
 * @filesource
 * @author Paulo Soares da Silva
 * @copyright P2S System - Soluções Web
 * @package SysWeb
 * @subpackage Default.Model
 * @version 1.0
 */
class Menu extends P2s_Db_Table_Abstract {
	
    /**
    * Nome da tabela no banco de dados
    * @var String
    */
    protected $_name = 'menu';
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
        $this->_orderField = 'nome';
        $this->_fieldSearch = array('field'=>'a_nome','label'=>'Nome:');

        $this->_fieldLabel = array(
            'id'=>'Id',
            'nome' => 'Nome',
            'aplicacao' => 'Link',
            'sub_menu' =>'Sub Menu de',
            'param' =>'Parâmetros',
            'indice'=>'Índice',
            'ativo' =>'Ativo',

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

            $select->from(array('a' => 'menu'),array('a.id','a.nome', 'a.aplicacao', 'a.param', 'a.indice', 'a.ativo'))
                   ->joinLeft(array('b'=>'menu'), 'a.sub_menu_id = b.id', array('sub_menu'=>'b.nome'));
                 
           return $select;

       }

   
}
