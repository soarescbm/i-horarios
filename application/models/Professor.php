<?php 
/**
 * Modelo da tabela professor
 *
 * @filesource
 * @author Paulo Soares da Silva
 * @copyright P2S System - Soluções Web
 * @package SysWeb
 * @subpackage Default.Model
 * @version 1.0
 */
class Professor  extends P2s_Db_Table_Abstract {
	
    /**
    * Nome da tabela no banco de dados
    * @var String
    */
    protected $_name = 'professor';
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
        $this->_fieldSearch = array('field'=>'nome','label'=>'Professor:');
        $this->_fieldLabel = array(
            'id' => 'Id',
            'nome' => 'Nome',
            'carga_horaria_sala' => 'CH. em Sala',
            'carga_horaria_trabalho' => 'CH. de Trabalho',
            'ativo' => 'Ativo'
            );
    }
  
}
