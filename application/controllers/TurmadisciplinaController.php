<?php

/**
 * Controlador dos relacionamento turma x disciplina
 *
 * @filesource
 * @author Paulo Soares da Silva
 * @copyright P2S System - Soluções Web
 * @package SysWeb
 * @subpackage Default.Controller
 * @version 1.0
 */
class TurmadisciplinaController extends P2s_Controller_Abstract {

   
    public function init(){
        parent::init();
        Zend_Loader::loadClass('Turmadisciplina');
        Zend_Loader::loadClass('Turma');
        Zend_Loader::loadClass('Disciplina');
        Zend_Loader::loadClass('Curso');

        $this->setModel(new Turmadisciplina());
        $this->setTituloPagina('Disciplinas das Turmas');
        $this->setTituloPaginaClass('_config');
        $this->setInfoForm('Dados das Disciplinas das Turmas');
        $this->setItemNome('Disciplina');
        $this->setHtmlColunasTabela(array(
            'th'=>array('Disciplina'=>array( 'align' =>'left', 'width'=>'300' ),
                        'Professor'=>array( 'align' =>'left', 'width'=>'250' ))
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
    public function  getForm() {
       $form = parent::getForm();
        
       //Atributo id
       $id = new Zend_Form_Element_Hidden('id');
        
       //Atributo aplicacao
       $turma = new P2s_Form_Select('turma_id');
       $turma->setLabel('Turma:')
            ->setRequired(true)
            ->setAddItens('/turma/inserirbox');
       $turma->addMultiOptions(array(''=>''));
       //consuta dos options
       $tb = new Turma();
       $resultado = $tb->fetchAll('ativo=1','nome')->toArray();
       $options = array();
       foreach ($resultado as $linha ){
              $options[$linha['id']] = $linha['nome'];
       }
       $turma->addMultiOptions($options);



       //Atributo ações
       $disciplinas = new Zend_Form_Element_MultiCheckbox('disciplinas');
       $disciplinas->setLabel('Disciplinas:')
                  ->setRequired(true);
       //consuta dos options
       $tb = new Disciplina();
       $resultado = $tb->fetchAll(null,'nome')->toArray();
       $options = array();
       foreach ($resultado as $linha ){
              $options[$linha['id']] = $linha['nome'];
       }

       $disciplinas->addMultiOptions($options);


       
       
       
        $form->addElements(array($id, $turma,$disciplinas));
        $form->setElementDecorators($this->getElementDecorators());
        $id->setDecorators($this->getElementHiddenDecorators());
        $disciplinas->setDecorators(array(
        'ViewHelper',
        array(array('ajax' => 'HtmlTag'), array('tag' => 'div', 'class' => 'checkboxAjax')),
        array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
        array('Label',array('tag' => 'td')),
        array(array('row' => 'HtmlTag'), array('tag' => 'tr')))
                );
        $turma->setDecorators($this->getElementSelectAddDecorators());

        return $form;
    }
    public function inserirAction(){

        $form = $this->getForm();

        if($this->_request->isPost()){

            if ($form->isValid($_POST)){
                $id = $form->getValue('id');
                $data = array();
                $data['turma_id']= $form->getValue('turma_id');
                $disciplinas = $form->getValue('disciplinas');
              
                $delete = $this->getModel()->delete('turma_id ='.$data['turma_id'] );

                foreach ($disciplinas as $disciplina_id){
                    $data['disciplina_id'] = $disciplina_id;
                    $this->getModel()->insert($data);
                }
                               
                if($delete == 0){
                  $this->getSession()->mensagem = $this->getItemNome()." adicionadas com sucesso!";
                }else{
                  $this->getSession()->mensagem = $this->getItemNome()." atualizadas com sucesso!";
                }
                
                $this->_redirect($this->getController().'/listar');
               

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
            $id = $filter->filter($this->_request->getParam('turma_id'));

            $where = $this->getModel()->getAdapter()->quoteInto('turma_id = ?',$id);
            $resultado = $this->getModel()->selectList($where);

            if(count($resultado) == 0){

                 $this->getSession()->mensagem = $this->getItemNome()." não encontrado!";
                 $this->_redirect($this->getController().'/listar');
            }
            $dados = array();
            $dados['turma_id']=$id;
            foreach ($resultado as $r){
              $disciplinas[] = $r['disciplina_id'];
            }
            $dados['disciplinas'] = $disciplinas;
           
            $form = $this->getForm();
            $form->populate($dados);

            $this->view->form = $form;
            $this->viewAssign();
            $this->render('inserir', null, true);

         }
         else{
            $this->_redirect($this->getController().'/listar');
         }

    }

   /**
     * Listagem do itens do controlador
     */
    public function listarAction(){

        //Paginador
        $paginador  = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($this->getModel()->selectTurmaList(
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
         $fields = $this->getFields();
         $tag = new P2s_Html_Tag();
         $lista = "";

         foreach($correnteItens as $turma){

           $turma_id = $turma['id'];
           $texto = $turma['turma']. "  -  ".$turma['curso'];
           $editar = '<a  href="'.$this->getUrl().'/editar/turma_id/'.$turma_id.'"  title="Editar Turma"><img src="'.$this->getUrlBase().'/public/templates/system/imagens/edit2.png" border="0"></a>';
           $contexto = $tag->getTag('span', array(),$texto);
           $contexto .=$tag->getTag('span', array(),$editar);

           $lista .= $tag->getTag('div', array('id'=>'titulo_list_grupo'),$contexto);

           $where = "a.turma_id = '".$turma_id."'";
           $disciplinas = $this->getModel()->selectList($where,'disciplina');

           foreach ($disciplinas as $row){
              
              $id = $row[$fieldKey];
              foreach ($fields as $field => $label){
                  $records[$indice][$label] = $row[$field];
              }

              $this->addColuna($records,$indice,$id,$row);
              /*
              if($this->getAcl()->has(strtolower($this->getController()))){

                    if($this->getAcl()->isAllowed($this->getPerfil(),strtolower($this->getController()),'editar')){
                         $records[$indice]['Editar'] = '<a  href="'.$this->getUrl().'/editar/'.$fieldKey.'/'.$id.'"  title="Editar item '.$id. '"><img src="'.$this->getUrlBase().'/public/templates/system/imagens/edit2.png" border="0"></a>';
                    }
                }
              */
              if($this->getAcl()->has(strtolower($this->getController()))){

                    if($this->getAcl()->isAllowed($this->getPerfil(),strtolower($this->getController()),'excluir')){
                          $records[$indice]['Excluir'] = '<a class ="excluir" href="'.$this->getUrl().'/excluir/'.$fieldKey.'/'.$id.'"  title="'.$this->getItemNome().' '. $id. '"><img src="'.$this->getUrlBase().'/public/templates/system/imagens/del.png" border="0"></a>';
                    }
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
         $this->viewAssign();
         $this->render('listar', null, true);


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
       $c = new Zend_Form_Element_Select('curso_id');
       $c->setLabel('Curso:')
                 ->addMultiOptions(array(''=>''));
       //consuta dos options
       $tb = new Curso();
       $resultado = $tb->fetchAll(null, 'nome')->toArray();
       $options = array();
       foreach ($resultado as $linha ){
              $options[$linha['id']] = $linha['nome'];
       }

       $c->addMultiOptions($options);



        //Atributo aplicacao
       $t = new Zend_Form_Element_Select('turma_id');
       $t->setLabel('Turma:')
                 ->addMultiOptions(array(''=>''));
       //consuta dos options
       $tb = new Turma();
       $resultado = $tb->fetchAll(null, 'nome')->toArray();
       $options = array();
       foreach ($resultado as $linha ){
              $options[$linha['id']] = $linha['nome'];
       }

       $t->addMultiOptions($options);;


       $form->addElements(array($c,$t));
       $form->setElementDecorators($this->getElementDecorators());

       return $form;

    }
   public function paramWhere() {
            $form = $this->getFormSearch();

            $form->populate($_POST);
            //Retorna os valores filtrados

            $curso = $form->getValue('curso_id');
            $turma = $form->getValue('turma_id');

            //inicialização da cláusula where
            $where = 'a.id > 0';

           if(!empty($curso)){
                     $where .= " AND c.id ='".$curso."'";

            }
           if(!empty($turma)){
                     $where .= ' AND a.turma_id ='.$turma;
            }


       return $where;
    }
}

