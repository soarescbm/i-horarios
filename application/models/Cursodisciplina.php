<?php 
/**
 *  Modelo da tabela relacionamento das cursos as disciplinas
 *
 * @filesource
 * @author Paulo Soares da Silva
 * @copyright P2S System - Soluções Web
 * @package SysWeb
 * @subpackage Default.Model
 * @version 1.0
 */
class Cursodisciplina extends P2s_Db_Table_Abstract {
	
    /**
     * Nome da tabela no banco de dados
     * @var String
     */
    protected $_name = 'rel_curso_disciplina';
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
        $this->_orderField = 'curso';
        $this->_fieldLabel = array(
             'id'=>'Id',
             'curso' => 'Curso',
             'disciplina' => 'Disciplina',
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

           $select->from(array('a' => 'rel_curso_disciplina'), array('id'=>'a.id','curso_id'))
                 ->joinLeft(array('c'=>'disciplina'), 'a.disciplina_id = c.id', array('disciplina_id'=>'c.id','disciplina'=>'c.nome',
                      'ch'=>'c.carga_horaria_total'))
                  ->joinLeft(array('d'=>'professor'), 'c.professor_id = d.id', array('professor'=>'d.nome'));
          
           $result = $select->query();
           return $result->fetchAll();

       }
     
   
}
