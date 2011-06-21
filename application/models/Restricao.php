<?php 
/**
 *  Modelo da tabela relacionamento das turmas as disciplinas
 *
 * @filesource
 * @author Paulo Soares da Silva
 * @copyright P2S System - Soluções Web
 * @package SysWeb
 * @subpackage Default.Model
 * @version 1.0
 */
class Restricao extends P2s_Db_Table_Abstract {
	
    /**
     * Nome da tabela no banco de dados
     * @var String
     */
    protected $_name = 'restricao';
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
        $this->_orderField = 'professor';
        $this->_fieldLabel = array(
            'id'=>'Id',
             'dia' => 'Dia',
             'restricao' => 'Restrição',
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

           $select->from(array('a' => 'restricao'), array('id'=>'a.id','professor_id'))
                  ->joinLeft(array('b'=>'professor'), 'a.professor_id = b.id', array('professor'=>'b.nome'))
                  ->joinLeft(array('c'=>'semana_dia'), 'a.semana_dia_id = c.id', array('dia'=>'c.nome'))
                  ->joinLeft(array('d'=>'tipo_restricao'), 'a.tipo_restricao_id = d.id', array('restricao'=>'d.nome'));
          
           $result = $select->query();
           return $result->fetchAll();

       }
     
    public function  selectProfessorList($where = null,$order = null) {

           $db= Zend_Db_Table::getDefaultAdapter();
           $select = new Zend_Db_Select($db);

           if ($where !== null)	{
			        $select->where($where);
            }
           if ($order !== null){
			        $select->order($order);

            }

           $select->from(array('a' => 'restricao'), array('id'=>'distinct(a.professor_id)'))
                  ->join(array('b'=>'professor'), 'a.professor_id = b.id', array('professor'=>'b.nome'))
                  ->order('professor');

                 
           return $select;
       }
       public function  selectRestricao($professor) {
            $db= Zend_Db_Table::getDefaultAdapter();
            $select = new Zend_Db_Select($db);
            $select->from(array('a'=>'restricao'))
                       ->where('a.tipo_restricao_id = 3 ')
                       ->where('a.professor_id = ?',$professor);
           
            $db->setFetchMode(Zend_Db::FETCH_OBJ);
            return $result = $db->fetchAll($select); 
          
               
                
     }
}
