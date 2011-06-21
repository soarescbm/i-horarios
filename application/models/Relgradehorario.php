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
class Relgradehorario extends P2s_Db_Table_Abstract {
	
    /**
    * Nome da tabela no banco de dados
    * @var String
    */
    protected $_name = 'rel_horarios_grade';
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

           $select->from(array('a' => 'rel_horarios_grade'))
                   ->join(array('d'=>'turma'), 'a.turma_id =  d.id' ,array('turma'=>'d.nome'))
                   ->join(array('e'=>'semana_dia'), 'a.semana_dia_id =  e.id' ,array('dia_id'=>'e.id','dia'=>'e.nome'))
                   ->join(array('f'=>'horario'), 'a.horario_id =  f.id' ,array('horario'=>'f.descricao'))
                   ->join(array('c'=>'disciplina'), 'a.disciplina_id =  c.id' ,array('sigla'=>'c.sigla','disciplina'=>'c.nome'))
                   ->order('e.sequencia');
                  
          
           $result = $select->query();
           return $result->fetchAll();
           

       }
       
        public function  selectTurmaList($grade,$where = null,$order = null) {

           $db= Zend_Db_Table::getDefaultAdapter();
           $select = new Zend_Db_Select($db);

           if ($where !== null)	{
			        $select->where($where);
            }
           if ($order !== null){
			        $select->order($order);

            }

           $select->from(array('a' => 'rel_horarios_grade'), array('id'=>'distinct(a.turma_id)'))
                  ->join(array('b'=>'turma'), 'a.turma_id = b.id', array('turma'=>'b.nome'))
                  ->order('turma')
                  ->where('a.grade_horario_id = ?', $grade );

           $db->setFetchMode(Zend_Db::FETCH_ASSOC);   
           return $select;
       }
        
}
