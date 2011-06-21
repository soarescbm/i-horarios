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
class RestricaoController extends P2s_Controller_Abstract {

   
    public function init(){
        parent::init();
        Zend_Loader::loadClass('Professor');
        Zend_Loader::loadClass('Tiporestricao');
        Zend_Loader::loadClass('Restricao');
        Zend_Loader::loadClass('Dias');
        Zend_Loader::loadClass('Curso');
        

        $this->setModel(new Restricao());
        $this->setTituloPagina('Restrições dos Professores');
        $this->setTituloPaginaClass('_config');
        $this->setInfoForm('Dados das Restrições');
        $this->setItemNome('Restrição');
        $this->setHtmlColunasTabela(array(
            'th'=>array('Dia'=>array( 'align' =>'left', 'width'=>'120' ),
                        'Nivel'=>array( 'align' =>'left', 'width'=>'120' ))
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
       $professor = new P2s_Form_Select('professor_id');
       $professor->setLabel('Professor:')
            ->setRequired(true);           
       $professor->addMultiOptions(array(''=>''));
       //consuta dos options
       $tb = new Professor();
       $resultado = $tb->fetchAll('ativo=1','nome')->toArray();
       $options = array();
       foreach ($resultado as $linha ){
              $options[$linha['id']] = $linha['nome'];
       }
       $professor->addMultiOptions($options);



       //Atributo ações
       $dias = new P2s_Form_Select('semana_dia_id');
       $dias->setLabel('Dia:')
                  ->setRequired(true);
       //consuta dos options
       $tb = new Dias();
       $resultado = $tb->fetchAll('ativo=1','sequencia')->toArray();
       $options = array();
       foreach ($resultado as $linha ){
              $options[$linha['id']] = $linha['nome'];
       }

       $dias->addMultiOptions($options);


      
       $restricao = new P2s_Form_Select('tipo_restricao_id');
       $restricao->setLabel('Restricao:')
            ->setRequired(true);           
       $restricao->addMultiOptions(array(''=>''));
       //consuta dos options
       $tb = new Tiporestricao();
       $resultado = $tb->fetchAll(null,'nivel')->toArray();
       $options = array();
       foreach ($resultado as $linha ){
              $options[$linha['id']] = $linha['nome'];
       }
       $restricao->addMultiOptions($options);
       
       
        $form->addElements(array($id, $professor,$dias,$restricao));
        $form->setElementDecorators($this->getElementDecorators());
        $id->setDecorators($this->getElementHiddenDecorators());
        $dias->setDecorators(array(
        'ViewHelper',
        array(array('ajax' => 'HtmlTag'), array('tag' => 'div', 'class' => 'checkboxAjax')),
        array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
        array('Label',array('tag' => 'td')),
        array(array('row' => 'HtmlTag'), array('tag' => 'tr')))
                );
      

        return $form;
    }
   
   
   /**
     * Listagem do itens do controlador
     */
    public function listarAction(){

        //Paginador
        $paginador  = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($this->getModel()->selectProfessorList(
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

         foreach($correnteItens as $professor){

           $professor_id = $professor['id'];
           $texto = $professor['professor'];
           //$editar = '<a  href="'.$this->getUrl().'/editar/professor_id/'.$professor_id.'"  title="Editar Restrição"><img src="'.$this->getUrlBase().'/public/templates/system/imagens/edit2.png" border="0"></a>';
           $contexto = $tag->getTag('span', array(),$texto);
           //$contexto .=$tag->getTag('span', array(),$editar);

           $lista .= $tag->getTag('div', array('id'=>'titulo_list_grupo'),$contexto);

           $where = "a.professor_id = '".$professor_id."'";
           $dias = $this->getModel()->selectList($where,'dia');

           foreach ($dias as $row){
              
              $id = $row[$fieldKey];
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
       $c = new Zend_Form_Element_Select('professor_id');
       $c->setLabel('Professor:')
                 ->addMultiOptions(array(''=>''));
       //consuta dos options
       $tb = new Professor();
       $resultado = $tb->fetchAll(null, 'nome')->toArray();
       $options = array();
       foreach ($resultado as $linha ){
              $options[$linha['id']] = $linha['nome'];
       }
       $c->addMultiOptions($options);


       $form->addElements(array($c));
       $form->setElementDecorators($this->getElementDecorators());

       return $form;

    }
   public function paramWhere() {
            $form = $this->getFormSearch();
             //inicialização da cláusula where
            $where = 'a.id > 0';
            if($this->_request->isPost()){
                $form->populate($_POST);
                //Retorna os valores filtrados
           
                $professor = $form->getValue('professor_id');
                    
                if (!empty($professor)){
                     $where .= ' AND a.professor_id ='.$professor;
                }
              
            }elseif ($this->_request->isGet()) {
                $professor = $this->_request->getParam('professor_id');
                if (!empty($professor)){
                    $where .= ' AND a.professor_id ='.$professor;
                   
               }
            }

      
       return $where;
    }
   
    
}

