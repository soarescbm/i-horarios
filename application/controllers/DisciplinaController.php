<?php
/**
 * Controlador de Disciplina
 *
 * @author Paulo Soares da Silva
 * @copyright P2S System - Soluções Web
 * @package Sysweb
 * @subpackage Default.Controller
 * @version 1.0
 */
class DisciplinaController extends P2s_Controller_Abstract {

   
    /**
     *Inicializa a instancia do objeto
     *@return void
     */
    public function init(){
        parent::init();
        Zend_Loader::loadClass('Disciplina');
        Zend_Loader::loadClass('Professor');
        Zend_Loader::loadClass('Curso');
        Zend_Loader::loadClass('Cursodisciplina');


        $this->setModel( new Disciplina());
        $this->setTituloPagina('Disciplinas');
        $this->setTituloPaginaClass('_config');
        $this->setInfoForm('Dados da Disciplina');
        $this->setItemNome('Disciplina');
        $this->setHtmlColunasTabela( array(
            'th'=>array('Nome'=>array( 'align' =>'left', 'width'=>'250' ),
                'Professor'=>array( 'align' =>'left', 'width'=>'250' ),
                'Sigla'=>array( 'align' =>'left', 'width'=>'60' ))
            
                ));

        $this->setMensagens(
                array(
                    'editado'=>' editada com sucesso!',
                    'adicionado'=>' adicionada com sucesso!',
                    'excluido'=>' excluida com sucesso!',
                    'confirma_exclusao'=>'Tem certeza que deseja excluir a ',
                    'nao_encontrado'=>' não encontrada!'
                )
         );
              
    }
     /**
     * Descreve o formulário de cadastro
     * @return Zend_Form
     */
    public function  getForm() {
        $form = parent::getForm();

        //Atributo id
        $id = new Zend_Form_Element_Hidden('id');

        //Atributo nome
        $sigla = new Zend_Form_Element_Text('sigla');
        $sigla->setLabel('Sigla:');
        $sigla->setRequired(true);
        $sigla->setAttrib('class', 'input');
        $sigla->setAttrib('size', '10');
        $sigla->addFilter('StringTrim');
        $sigla->addFilter('StringToUpper');
        $existeSigla = new Zend_Validate_Db_NoRecordExists(
                array(
                    'table' =>'disciplina',
                    'field' => 'sigla',
                    'exclude' => array(
                        'field'=>'id',
                        'value'=> $this->_request->getParam('id')
                    )
                  )
        );
        $sigla->addValidator($existeSigla);

        //Atributo nome
        $nome = new Zend_Form_Element_Text('nome');
        $nome->setLabel('Nome:');
        $nome->setRequired(true);
        $nome->setAttrib('class', 'input');
        $nome->setAttrib('size', '60');
        $nome->addFilter('StringTrim');
        $existeNome = new Zend_Validate_Db_NoRecordExists(
                array(
                    'table' =>'disciplina',
                    'field' => 'nome',
                    'exclude' => array(
                        'field'=>'id',
                        'value'=> $this->_request->getParam('id')
                    )
                  )
        );
        $nome->addValidator($existeNome);


        $ch_semanal = new Zend_Form_Element_Text('carga_horaria_semanal');
        $ch_semanal->setLabel('Carga Horária Semanal:')
              ->setRequired()
              ->setAttrib('class', 'input')
              ->setAttrib('size', '2')
              ->addFilter('Digits')
              ->addValidator('Digits');

        $ch_total = new Zend_Form_Element_Text('carga_horaria_total');
        $ch_total->setLabel('Carga Horária Total:')
              ->setRequired()
              ->setAttrib('class', 'input')
              ->setAttrib('size', '2')
              ->addFilter('Digits')
              ->addValidator('Digits');

          //Atributo sub menu
       $professor = new P2s_Form_Select('professor_id');
       $professor->setAddItens('/professor/inserirbox');
       $professor->setLabel('Professor:')
               ->setRequired()
              ->addMultiOptions(array('0'=>''));
       $tb = new Professor();
       $resultado = $tb->fetchAll(null,'nome')->toArray();
       $options = array();
       foreach ($resultado as $linha ){
              $options[$linha['id']] = $linha['nome'];
       }

       $professor->addMultiOptions($options);
       
      //Atributo disciplinas
       $curso = new Zend_Form_Element_MultiCheckbox('cursos');
       $curso->setLabel('Cursos:')
                  ->setRequired(true);
       //consuta dos options
       $tb = new Curso();
       $resultado = $tb->fetchAll(null,'nome')->toArray();
       $options = array();
       foreach ($resultado as $linha ){
              $options[$linha['id']] = $linha['nome'];
       }

       $curso->addMultiOptions($options);

        $form->addElements(array($id, $sigla, $nome, $professor, $ch_semanal, $ch_total,$curso));
        $form->setElementDecorators($this->getElementDecorators());
        $id->setDecorators($this->getElementHiddenDecorators());
        $professor->setDecorators($this->getElementSelectAddDecorators());
        $curso->setDecorators(array(
        'ViewHelper',
        array(array('ajax' => 'HtmlTag'), array('tag' => 'div', 'class' => 'checkboxAjax')),
        array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
        array('Label',array('tag' => 'td')),
        array(array('row' => 'HtmlTag'), array('tag' => 'tr')))
                );

        return $form;
    }

   public function getFormSearch() {
       $form = new Zend_Form();

        $form->setAction($this->getUrl().'/listar')
             ->setMethod('post')
             ->setName('search')
             ->setAttrib('class', 'form01')
             ->setDecorators($this->getFormDecorators())
             ->addPrefixPath('P2s_Form_Decorator', 'P2s/Form/Decorator/', 'decorator');


         //Atributo aplicacao
       $p = new Zend_Form_Element_Select('professor_id');
       $p->setLabel('Professor:')
                 ->addMultiOptions(array(''=>''));
       //consuta dos options
       $tb = new Professor();
       $resultado = $tb->fetchAll(null, 'nome')->toArray();
       $options = array();
       foreach ($resultado as $linha ){
              $options[$linha['id']] = $linha['nome'];
       }
        
       $p->addMultiOptions($options);



        //Atributo perfil
        $d = new Zend_Form_Element_Text('nome');
        $d->setLabel('Disciplina:')
              ->setAttrib('size', '30');

          //Atributo aplicacao
       $curso = new Zend_Form_Element_Select('curso_id');
       $curso->setLabel('Curso:')
                 ->addMultiOptions(array(''=>''));
       //consuta dos options
       $tb = new Curso();
       $resultado = $tb->fetchAll(null, 'nome')->toArray();
       $options = array();
       foreach ($resultado as $linha ){
              $options[$linha['id']] = $linha['nome'];
       }

       $curso->addMultiOptions($options);

       $form->addElements(array($d,$p,$curso));
       $form->setElementDecorators($this->getElementDecorators());

       return $form;

    }
   public function paramWhere() {
            $form = $this->getFormSearch();

            $form->populate($_POST);
            //Retorna os valores filtrados

            $professor_id = $form->getValue('professor_id');
            $disciplina = $form->getValue('nome');
            $curso_id = $form->getValue('curso_id');
            //inicialização da cláusula where
            $where = 'a.id > 0';

           if(!empty($disciplina)){
                     $where .= " AND a.nome LIKE '%".$disciplina."%'";

            }
           if(!empty($professor_id)){
                     $where .= ' AND a.professor_id ='.$professor_id;
            }
            if(!empty($curso_id)){
                     $where .= ' AND e.curso_id ='.$curso_id;
            }

            
       return $where;
    }
     public function inserirAction(){

        $form = $this->getForm();
       
        if($this->_request->isPost()){

            if ($form->isValid($_POST)){
                $id = $form->getValue('id');
                $cursos = $form->getValue('cursos');
               
                $dados = $form->getValues();
                unset ($dados['cursos']);
                if(empty($id)){
                     unset ($dados['id']);
                     $disciplina_id = $this->getModel()->insert($dados);
                     $tb_curso_disciplina = new Cursodisciplina();
                     
                     foreach ($cursos as $curso){
                         $tb_curso_disciplina->insert(array(
                             'disciplina_id' => $disciplina_id,
                             'curso_id' => $curso
                             ));
                     }
                     $this->getSession()->mensagem = $this->getItemNome().$this->getMensagens('adicionado');
                     $this->_redirect($this->getController().'/listar');
                }
                else{
                     
                     $where = $this->getModel()->getAdapter()->quoteInto('id = ?',$id);
                     $this->getModel()->update($dados,$where);
                     $tb_curso_disciplina = new Cursodisciplina();
                     $tb_curso_disciplina->delete('disciplina_id = ' . $id);
                     foreach ($cursos as $curso){
                         $tb_curso_disciplina->insert(array(
                             'disciplina_id' => $id,
                             'curso_id' => $curso
                             ));
                     }
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
        
        public function editarAction(){
        
        if($this->_request->isGet()){
            $filter = new Zend_Filter_Digits();
            $id = $filter->filter($this->_request->getParam('id'));

            $where = $this->getModel()->getAdapter()->quoteInto('id = ?',$id);
            $resultado = $this->getModel()->fetchRow($where);

            if(count($resultado) == 0){

                 $this->getSession()->mensagem = $this->getItemNome().$this->getMensagens('nao_encontrado');
                 $this->_redirect($this->getController().'/listar');
            }
            $resultado = $resultado->toArray();
            $form = $this->getForm();
            
            $tb_curso_disciplina = new Cursodisciplina();
            $rows = $tb_curso_disciplina->fetchAll('disciplina_id ='.$id)->toArray();
            foreach ($rows as $row){
                $resultado['cursos'] = $row['curso_id'];
            }
            $form->populate($resultado);
            

            $this->view->form = $form;
            $this->viewAssign();
            $this->render('inserir', null, true);

         }
         else{
            $this->_redirect($this->getController().'/listar');
         }
       
    }
}

