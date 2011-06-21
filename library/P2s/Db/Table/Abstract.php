<?php
/**
 * Modelo Abstract das tabelas do sistema
 *
 * @filesource
 * @author Paulo Soares da Silva
 * @copyright P2S System - Soluções Web
 * @package SysWeb
 * @subpackage P2s.Db.Table
 * @version 1.0
 */
Abstract class P2s_Db_Table_Abstract extends Zend_Db_Table_Abstract {
    /**
     * Ordenação da consulta de listagem, coluna da tabela.
     * @var string
     */
    protected $_orderField;
    /**
     * Array dos rótulos das coluna da listagem dos dados. array('coluna-da-tabela'=>'rótulo')
     * @var array
     */
    protected $_fieldLabel = array();
    /**
     * Chave primária da tabela
     * @var string
     */
    protected $_fieldKey;
    /**
     * Campos de pesquisa
     * @var array
     */
    protected $_fieldSearch = array();
    /**
     * Construtor do modelo
     */
    public function  __construct() {
        parent::__construct();

    }
    /**
     * Get os rótulos da listagem
     * @return array
     */
    public function getFieldLabel(){

        return $this->_fieldLabel;
    }
    /**
     * Get a chave primária da tabela
     * @return string
     */
    public function getFieldKey(){

        return $this->_fieldKey;
    }
    /**
     * Get a coluna de ordenação
     * @return string
     */
    public function getOrderField(){

        return $this->_orderField;
    }
    /**
     * Get os campos de pesquisa
     * @return array
     */
    public function getFieldSearch(){

        return $this->_fieldSearch;
    }
    /**
     * Consulta da listagem do itens da tabela
     * @param string $where
     * @param string $order
     * @return Zend_Db_Select
     */
    public function selectList($where = null,$order = null){

       $select = $this->select();
		if ($where !== null)
		{
			$select->where($where);

		}
                if ($order !== null)
		{
			$select->order($order);
		}

       return $select;
    }
}


