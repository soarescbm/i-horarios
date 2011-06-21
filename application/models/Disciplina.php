<?php 
/**
 * Modelo da tabela horario
 *
 * @filesource
 * @author Paulo Soares da Silva
 * @copyright P2S System - Soluções Web
 * @package SysWeb
 * @subpackage Default.Model
 * @version 1.0
 */
class Disciplina  extends P2s_Db_Table_Abstract {
	
    /**
    * Nome da tabela no banco de dados
    * @var String
    */
    protected $_name = 'disciplina';
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
        $this->_fieldSearch = array('field'=>'nome','label'=>'Disciplina:');
        $this->_fieldLabel = array(
            'id' => 'Id',
            'sigla' => 'Sigla',
            'nome' => 'Nome',
            'professor' => 'Professor',
            'carga_horaria_semanal' => 'CH Semanal',
            'carga_horaria_total' => 'CH Total'
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

           $select->from(array('a' => 'disciplina'),array('id'=>'a.id', 'nome'=>'a.nome','sigla'=>'a.sigla',
               'carga_horaria_semanal'=>'a.carga_horaria_semanal','carga_horaria_total'=>'a.carga_horaria_total'))
                   ->joinLeft(array('b'=>'professor'), 'a.professor_id = b.id', array('professor_id'=>'b.id','professor'=>'b.nome'))
                   ->joinLeft(array('e'=>'rel_curso_disciplina'), 'e.disciplina_id = a.id', array('curso'=>'e.curso_id'));
                 
           return $select;
    }
    public function  fetchDisciplinas($turma) {
       $db= Zend_Db_Table::getDefaultAdapter();
       $select = new Zend_Db_Select($db);
       $select->from(array('a'=>'disciplina'), array('id'=>'a.id','nome'=>'a.nome', 'ch'=>'a.carga_horaria_semanal',
           'professor'=>'a.professor_id'))
                ->join(array('b'=>'rel_turma_disciplina'), 'b.disciplina_id = a.id',(array('turma'=>'b.turma_id')))
                 ->joinLeft(array('f'=>'restricao'), 'f.professor_id = a.professor_id', array('restricao'=>'f.tipo_restricao_id'))
                 ->order('restricao DESC')          
                ->where('b.turma_id = ?', $turma);
     
      $db->setFetchMode(Zend_Db::FETCH_OBJ);
      return $result = $db->fetchAll($select);
    }
    public function  fetchDisciplinasLecionadas($professor) {
       $db= Zend_Db_Table::getDefaultAdapter();
       $select = new Zend_Db_Select($db);
       $select->from(array('a'=>'disciplina'), array('id'=>'a.id','disciplina'=>'a.nome', 'ch'=>'a.carga_horaria_semanal',
           'professor'=>'a.professor_id'))
                   ->where('a.professor_id = ?', $professor);
      $db->setFetchMode(Zend_Db::FETCH_OBJ);
      return $result = $db->fetchAll($select);
    }
}
