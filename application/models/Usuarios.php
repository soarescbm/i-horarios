<?php 
/**
 *  Modelo da tabela usuários do sistema
 *
 * @filesource
 * @author Paulo Soares da Silva
 * @copyright P2S System - Soluções Web
 * @package SysWeb
 * @subpackage Default.Model
 * @version 1.0
 */
class Usuarios extends P2s_Db_Table_Abstract {
	
    /**
    * Nome da tabela no banco de dados
    * @var String
    */
    protected $_name = 'usuarios';
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
            'id' => 'Id',
            'nome' => 'Nome Completo',
            'nome_usuario' => 'Nome de Usuário',
            'perfil' =>'Tipo de Perfil',
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
           $id = Zend_Registry::get('Id');
           $perfil = Zend_Registry::get('Perfil');
           
           $db= Zend_Db_Table::getDefaultAdapter();

           $consulta = $db->fetchAll("SELECT nivel FROM perfis WHERE nome ='".$perfil."'");

           $select = new Zend_Db_Select($db);

           if ($where !== null)	{
			$select->where($where);
            }
           if ($order !== null){
			$select->order($order);
            }

            $select->from(array('a' => 'usuarios'))
                   ->joinLeft(array('b'=>'perfis'), 'a.perfis_id = b.id', array('perfil'=>'b.nome'))
                   ->where('b.nivel <='.$consulta[0]['nivel']);
           return $select;

       }
       
    public function find($id){
        
        $db= Zend_Db_Table::getDefaultAdapter();
       
        $where = "a.id =".(int)$id;
        $sql = $this->selectList($where);
        
        return $db->fetchRow($sql);
        
    }

   
}
