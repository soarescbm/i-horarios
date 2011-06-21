<?php 

class GerarGradeHorario
{   
    private $_horariosDisponiveis = array();
    private $_dia = 0;
    private $_aula;
    private $_grade_id;
    private $_disciplinas = array();
    private $_horariosDispPro = array();
    private $_curso;
    private $_turmas = array();
    private $_modelDisciplina;
    private $_modelTurma;
    private $_modelRelGradeHorario;
    private $_horariosAlocadosPro = array();
    
   
    public function init(){
        Zend_Loader::loadClass('Turma');
        Zend_Loader::loadClass('Dias');
        Zend_Loader::loadClass('Disciplina');
        Zend_Loader::loadClass('Restricao');
        Zend_Loader::loadClass('Horario');
        Zend_Loader::loadClass('Relgradehorario');
    }
    
    public function __construct($id,$curso){
        $this->init();
        $this->_grade_id = (int) $id;
        $this->_curso = (int) $curso;
        $this->_modelTurma = new Turma();
        $this->_modelDisciplina = new Disciplina();
        $this->_modelHorario = new Horario();
        $this->_modelRelGradeHorario = new Relgradehorario();
        $this->setTurmas();
        $this->gerar();
       
    }
   
    public function gerar(){
        
        $dataHorarios = array();
        $dataHorarios['grade_horario_id'] = $this->_grade_id;
        
        foreach ($this->_turmas as $turma) {
            //setando disciplinas do período
            $this->setDisciplinas($turma->id);
            $this->setHorariosDisponiveis();
            
            foreach ($this->_disciplinas as $disciplina){
                  $professor = $disciplina->professor;
                  
                  $this->horariosDisponivelPro($professor);
                  $dataHorarios['turma_id'] = $turma->id;
                  $dataHorarios['disciplina_id'] = $disciplina->id;
                  $aulas_semanais = $disciplina->ch;
                  $cont = 0;
                  $horarios_disp = array();
                  while ($aulas_semanais != 0 || $cont == 1500 ){
                      $dia = $this->selecionaDia();
                      $dataHorarios['semana_dia_id'] = $dia;
                      
                      if($aulas_semanais == 3){
                         $horarios = $this->selecionaHorariosDisp($dia);
                         if(count($horarios) >= 3 ){
                             foreach($horarios as $horario){
                                 if(!$this->isHorarioAlocadoPro($professor, $dia, $horario)){
                                      
                                     $horarios_disp[] = $horario;
                                 }
                             }
                             $numero = count($horarios_disp);
                             if($numero >= 3){
                                 
                                 for ($i = 0; $i < 3; $i++){
                                     $dataHorarios['horario_id'] = $horarios_disp[$i];
                                     
                                     $this->_modelRelGradeHorario->insert($dataHorarios);
                                     $this->setHorariosAlocados($dia, $horarios_disp[$i]);
                                     $this->setHorariosAlocadosPro($professor, $dia, $horarios_disp[$i]);
                                 }
                                 $aulas_semanais = $aulas_semanais - 3;
                             }
                         }
                      }else{
                          $horarios = $this->selecionaHorariosDisp($dia);
                          foreach($horarios as $horario){
                                 if(!$this->isHorarioAlocadoPro($professor, $dia, $horario)){
                                      
                                     $horarios_disp[] = $horario;
                                 }
                             }
                             $numero = count($horarios_disp);
                             if($numero >= 2){
                                 
                                 for ($i = 0; $i < 2; $i++){
                                     $dataHorarios['horario_id'] = $horarios_disp[$i];
                                     $this->_modelRelGradeHorario->insert($dataHorarios);
                                     $this->setHorariosAlocados($dia, $horarios_disp[$i]);
                                     $this->setHorariosAlocadosPro($professor, $dia, $horarios_disp[$i]);
                                 }
                                 $aulas_semanais = $aulas_semanais - 2;
                                 $this->_dia = $dia;
                             }
                      }
                      $cont++;
                      $numero = 0;
                      unset($horarios_disp);
                      if($cont == 1500) {
                           throw new Zend_Exception('Não foi possível gerar a grade de horário completa, agrade foi gerada parcialmente. O número de tentativas Excedeu o Limite de 1500.');
                      }
                  }
                  $this->_dia = 0;          
                  
                  
            }
        }
    }
    protected function setTurmas(){
       $this->_turmas = $this->_modelTurma->fetchTurmas($this->_curso);
    }
    protected function setDisciplinas($turma){
     
       $this->_disciplinas = $this->_modelDisciplina->fetchDisciplinas($turma);
    }
    protected function horariosDisponivelPro($professor){
       $this->_horariosDispPro = array();
       $dias = $this->diasPreferencias($professor);
       $horarios = $this->_modelHorario->fetchHorarioDisponivel($this->_curso);
       
       foreach ($dias as $dia){
            foreach ($horarios as $horario){
                $this->_horariosDispPro[$dia][$horario->id] = true;
            }
            
        }
      
       
    }  
    protected function disciplinasLecionadas($professor){
       return $disciplinas = $this->_modelDisciplina->fetchDisciplinasLecionadas($professor);
    }
    protected function diasPreferencias($professor){
        $tb_restricao = new Restricao();
        $tb_dias = new Dias();
        $restricao = $tb_restricao->selectRestricao($professor);
        $dias = $tb_dias->fetchAll('ativo = 1');
        $dias_preferencia = array();
        $a1 = $a2 = array();
        
        foreach ($dias as $dia){
            $a1[] =  $dia->id;
        }
        foreach ($restricao as $r){
            $a2[] =  $r->semana_dia_id;
        }
        
       $dias_preferencia = array_diff($a1, $a2);
       
        return $dias_preferencia;
        
        
        
    } 
    public function selecionaDia(){
        $dias = $temp = array();
        $temp = array_keys($this->_horariosDispPro);
       
        foreach ($temp as $key => $dia){
            if($dia != $this->_dia){
                $dias[] = $dia;
            }
        }
        $n_dias = count($dias);
       
        $key = rand(0, $n_dias - 1);
        
        return $dias[$key];     
    }
    public function selecionaHorariosDisp($dia){
        $horarios = array();
        $key_dia = $dia;
        foreach($this->_horariosDisponiveis[$key_dia] as $key => $value){
            if ($value == true){
                $horarios[] = $key;
            }
        }
        return $horarios;
    }
    
    public function setHorariosDisponiveis(){
        $this->_horariosDisponiveis = array();
        $tb_dias = new Dias();
        $tb_horarios = new Horario();
        $dias = $horarios = array();
        $dias = $tb_dias->fetchAll('ativo = 1','sequencia');
        $horarios = $tb_horarios->fetchHorarioDisponivel($this->_curso);
        
        foreach ($dias as $dia){
            foreach ($horarios as $horario){
                $this->_horariosDisponiveis[$dia->id][$horario->id] = true;
            }
        }
       
     }
     public function setHorariosAlocadosPro($professor,$dia,$horario){
         
              $this->_horariosAlocadosPro[$professor][$dia][$horario] = false;
                   
     }
     public function setHorariosAlocados($dia,$horario){
          
              $this->_horariosDisponiveis[$dia][$horario] = false;
                
     }
     public function isHorarioAlocadoPro($professor,$dia,$horario){
         $flag = true;
         
         if(!array_key_exists($professor, $this->_horariosAlocadosPro)){
            $flag = false;
         }elseif(!array_key_exists($dia, $this->_horariosAlocadosPro[$professor])) {
            $flag = false;
         }elseif(!array_key_exists($horario, $this->_horariosAlocadosPro[$professor][$dia])) {
            $flag = false;
         }
         
         return $flag;
     }
        
}
