<?php
/**
 * Controlador de Grade de Horários
 *
 * @author Paulo Soares da Silva
 * @copyright P2S System - Soluções Web
 * @package Sysweb
 * @subpackage Default.Controller
 * @version 1.0
 */
class GradehorarioController extends P2s_Controller_Abstract {

   
    /**
     *Inicializa a instancia do objeto
     *@return void
     */
    public function init(){
        parent::init();
        Zend_Loader::loadClass('Gradehorario');
        Zend_Loader::loadClass('GerarGradeHorario');
        Zend_Loader::loadClass('Curso');
        Zend_Loader::loadClass('Relgradehorario');
        Zend_Loader::loadClass('Horario');
        Zend_Loader::loadClass('Dias');
        
        $this->setModel(new Gradehorario);
        $this->setTituloPagina('Grades de Horários');
        $this->setTituloPaginaClass('_config');
        $this->setInfoForm('Dados da Grade de Horário');
        $this->setItemNome('Grade de Horário');
       

              
    }
  
   public function indexAction(){
       $this->_forward('listar');
   }
   protected function gerarGrade($id,$curso){
       
       $grade = new GerarGradeHorario((int)$id,(int)$curso);
       
   }
   public function inserirAction() {
        $form = $this->getForm();
       
        if($this->_request->isPost()){

            if ($form->isValid($_POST)){
                $id = $form->getValue('id');
                if(empty($id)){
                     $form->removeElement('id');
                     $grade_id = $this->getModel()->insert($form->getValues());
                     $this->getSession()->mensagem = $this->getItemNome().$this->getMensagens('adicionado');
                     
                     //Gera Grade de Horário
                     $this->gerarGrade($grade_id,$form->getValue('curso_id'));
                     
                     $this->_redirect($this->getController().'/listar');
                }
                else{
                     
                     $where = $this->getModel()->getAdapter()->quoteInto('id = ?',$id);
                     $this->getModel()->update($form->getValues(),$where);
                     $this->getSession()->mensagem = $this->getItemNome().$this->getMensagens('editado');
                     $this->_redirect($this->getController().'/listar');
                }

            } else {
                
                $form->populate($_POST);

            }
        }
        $this->view->form = $form;
        $this->viewAssign();
        $this->render('inserir', null, true);
     }
          
     
   
    public function  getForm() {
        $form = parent::getForm();
        
        //Atributo id
        $id = new Zend_Form_Element_Hidden('id');
        
        //Atributo sub menu
       $curso = new Zend_Form_Element_Select('curso_id');
       $curso->setLabel('Curso:')
             ->setRequired()
              ->addMultiOptions(array('0'=>''));
       $tb_curso = new Curso();
       $resultado = $tb_curso->fetchAll(null,'nome')->toArray();
       $options = array();
       foreach ($resultado as $linha ){
              $options[$linha['id']] = $linha['nome'];
       }

       $curso->addMultiOptions($options);
       
        //Atributo nome
        $semestre = new Zend_Form_Element_Text('semestre');
        $semestre->setLabel('Semestre:')
                ->setRequired(true)
                ->setAttrib('class', 'input')
                ->setAttrib('size', '20')
                ->addFilter('StringTrim')
                ->setDescription('Exemplo: 2011.1');
        
          
            
        $form->addElements(array($id, $curso, $semestre));
        $form->setElementDecorators($this->getElementDecorators());
        $id->setDecorators($this->getElementHiddenDecorators());

        return $form;
    }
     public function  addColuna(&$records, $indice, $id, $row) {

             
        $records[$indice]['Visualizar'] = '<a  href="'.$this->getUrl().'/visualizar/id/'.$id.'"  title="Visualizar Grade de Horário '.$id. '"><img src="'.$this->getUrlBase().'/public/templates/system/imagens/search.png" border="0" title="Ativa"></a>';
             
    }
    public function visualizarAction(){
           $grade_id = $this->_request->getParam('id');
           if(!isset ($grade_id)){
                $this->_forward('listar');
                
           }
                      
           $tb_grade_horario = new Gradehorario();
           $curso = $tb_grade_horario->fetchCurso($grade_id);
           
           $model_grad = new Relgradehorario();
           
           //Paginador
           $paginador  = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($model_grad->selectTurmaList(
             $grade_id,$this->getWhere())));
           
           
           //Página requisitada
           if($this->_request->getParam('pagina')){
             $paginador->setCurrentPageNumber($this->_request->getParam('pagina'));
	   }

        //Setando número de Itens
         if($this->_request->getParam('itens')){
             $paginador->setItemCountPerPage($this->_request->getParam('itens'));
         }
         else{
             $paginador->setItemCountPerPage($this->getItensPagina());
         }

                
         //Itens da consulta atual
         $correnteItens = $paginador->getCurrentItems();
         $tabela = new P2s_Html_Table();

         
         $indice = 0;
         $tag = new P2s_Html_Tag();
         $lista = "";
         $tb_horarios = new Horario();
         $tb_dias = new Dias();
         $horarios = $tb_horarios->fetchHorarioDisponivel($curso->id);
         $dias = $tb_dias->fetchAll('ativo = 1','sequencia');
         foreach($correnteItens as $turma){
           $records = array();
           $turma_id = $turma['id'];
           $texto = $turma['turma'];
           
           $contexto = $tag->getTag('span', array(),$texto);
           $lista .= $tag->getTag('div', array('id'=>'titulo_list_grupo'),$contexto);

          
           foreach ($horarios as $horario){
              
              $where = " a.grade_horario_id = ".$grade_id." AND a.turma_id = ".$turma_id . " AND
                   a.horario_id = ".$horario->id;
              $aulas = $model_grad->selectList($where);             
              $records[$indice]['Horários'] = $horario->horario;
              
              foreach ($dias as $dia){
                   
                   $records[$indice][$dia->nome] = $this->getAula($dia->id,$aulas);
              }  

              $indice++;
            
         }
         
          $lista .= $tabela->create($records,$this->getHtmlColunasTabela());
          unset($records);
         }
         $this->view->tabela = $lista;
         $this->view->paginador = $paginador;
         $this->view->mensagem = $this->getSession()->mensagem;
         $this->view->search = $this->getModel()->getFieldSearch();
         $this->view->search_custom = $this->getFormSearch();


         $this->getSession()->unsetAll();

         if($this->getAcl()->has(strtolower($this->getController()))){

                    if($this->getAcl()->isAllowed($this->getPerfil(),strtolower($this->getController()),'inserir')){
                         $this->view->flegAdicionar = true;
                    }else {
                         $this->view->flegAdicionar = false;
                    }
         }
               
    
           
           $this->setTituloPagina('Grades de Horários  -  Curso: '.$curso->curso.'  -  Semestre: '.$curso->semestre);
           $this->viewAssign();
    }
    public function getAula ($dia, $aulas){
        $disciplina = "";
        foreach ($aulas as $aula){
            if($aula->dia_id == $dia ){
                $disciplina .= $aula->sigla;
            }
        }
        return $disciplina;
    }
    public function listarAction(){
                
        //Paginador
        $paginador  = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($this->getModel()->selectList(
             $this->getWhere(),$this->getModel()->getOrderField())));

        //Página requisitada
        if($this->_request->getParam('pagina')){
             $paginador->setCurrentPageNumber($this->_request->getParam('pagina'));
	}

        //Setando número de Itens
         if($this->_request->getParam('itens')){
             $paginador->setItemCountPerPage($this->_request->getParam('itens'));
         }
         else{
             $paginador->setItemCountPerPage($this->getItensPagina());
         }



         //Itens da consulta atual
         $correnteItens = $paginador->getCurrentItems();
         $tabela = new P2s_Html_Table();

         $records = array();
         $indice = 0;
         $fieldKey = $this->getModel()->getFieldKey();
         $this->setFields($this->getModel()->getFieldLabel());
         
                 
         foreach ($correnteItens as $row){

             $id = $row[$fieldKey];
             $fields = $this->getFields();
              foreach ($fields as $field => $label){
                  $records[$indice][$label] = $row[$field];
              }

              $this->addColuna($records,$indice,$id,$row);
              if($this->getAcl()->has(strtolower($this->getController()))){

                    if($this->getAcl()->isAllowed($this->getPerfil(),strtolower($this->getController()),'editar')){
                         $records[$indice]['Editar'] = '<a  href="'.$this->getUrl().'/editar/'.$fieldKey.'/'.$id.'"  title="Editar item '.$id. '"><img src="'.$this->getUrlBase().'/public/templates/system/imagens/edit2.png" border="0"></a>';
                    }
                }
              if($this->getAcl()->has(strtolower($this->getController()))){

                    if($this->getAcl()->isAllowed($this->getPerfil(),strtolower($this->getController()),'excluir')){
                          $records[$indice]['Excluir'] = '<a class ="excluir" href="'.$this->getUrl().'/excluir/'.$fieldKey.'/'.$id.'"  title="'.$this->getItemNome().' '. $id. '"><img src="'.$this->getUrlBase().'/public/templates/system/imagens/del.png" border="0"></a>';
                    }
                }
             
                           
              $indice++;
         }

         $this->view->tabela = $tabela->create($records,$this->getHtmlColunasTabela());
         $this->view->paginador = $paginador;
         $this->view->mensagem = $this->getSession()->mensagem;
         $this->view->search = $this->getModel()->getFieldSearch();
         $this->view->search_custom = $this->getFormSearch();
         

         $this->getSession()->unsetAll();

         if($this->getAcl()->has(strtolower($this->getController()))){

                    if($this->getAcl()->isAllowed($this->getPerfil(),strtolower($this->getController()),'inserir')){
                         $this->view->flegAdicionar = true;
                    }else {
                         $this->view->flegAdicionar = false;
                    }
         }
         $this->viewAssign();
         
        
         
    }
}

