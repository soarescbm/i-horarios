<?php 
/**
 * Modelo da tabela Grade de Horários
 *
 * @filesource
 * @author Paulo Soares da Silva
 * @copyright P2S System - Soluções Web
 * @package SysWeb
 * @subpackage Default.Model
 * @version 1.0
 */
class Gradehorario extends P2s_Db_Table_Abstract {
	
    /**
    * Nome da tabela no banco de dados
    * @var String
    */
    protected $_name = 'grade_horario';
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
        $this->_fieldSearch = array('field'=>'nome','label'=>'Curso:');
        $this->_fieldLabel = array(
            'id' => 'Id',
            'curso' => 'Curso',
            'semestre' => 'Semestre',
            'criado' => 'Criado Em'
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

           $select->from(array('a' => 'grade_horario'), array('id'=>'a.id', 'semestre'=>'a.semestre',
               'criado'=>'DATE_FORMAT(a.create_at, "%d-%m-%Y %H:%i:%s")'))
                   ->join(array('b'=>'curso'), 'a.curso_id =  b.id' ,array('curso'=>'b.nome'));
                 
           return $select;

       }
      public function fetchCurso($grade){
       
       $db= Zend_Db_Table::getDefaultAdapter();
       $select = new Zend_Db_Select($db);
       
       $select->from(array('a' => 'grade_horario'),array('semestre'))
                  ->join(array('c'=>'curso'), 'a.curso_id = c.id', array('id' => 'c.id','curso' => 'c.nome'))
                  ->where('a.id = ?',$grade);
       
       $db->setFetchMode(Zend_Db::FETCH_OBJ);
       return $result = $db->fetchRow($select);
    }
}
