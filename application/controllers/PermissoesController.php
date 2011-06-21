<?php

/**
 * Controlador das permissões  do sistema
 *
 * @filesource
 * @author Paulo Soares da Silva
 * @copyright P2S System - Soluções Web
 * @package SysWeb
 * @subpackage Default.Controller
 * @version 1.0
 */
class PermissoesController extends P2s_Controller_Abstract {

   

    public function init(){
        parent::init();
        Zend_Loader::loadClass('Perfisaplicacoes');
        Zend_Loader::loadClass('Aplicacoes');
        Zend_Loader::loadClass('Perfis');
        Zend_Loader::loadClass('Acoes');
        Zend_Loader::loadClass('Acoesaplicacao');

        $this->setModel( new Perfisaplicacoes());
        $this->setTituloPagina('Controle de Permissões de Acesso as Aplicações');
        $this->setTituloPaginaClass('_config');
        $this->setInfoForm('Dados da Permissão');
        $this->setItemNome('Registro');
        $this->setHtmlColunasTabela(array(
            'th'=>array('Perfil'=>array( 'align' =>'left', 'width'=>'90' ),
                        'Aplicação'=>array( 'align' =>'left', 'width'=>'80' ),
                         'Ações'=>array( 'align' =>'left', 'width'=>'300' ))

             ));

              
    }
    public function  getForm() {
        $form = parent::getForm();
        
        //Atributo id
        $id = new Zend_Form_Element_Hidden('id');
        

        //Atributo perfil
       $perfil = new Zend_Form_Element_Select('perfis_id');
       $perfil->setLabel('Perfil:');
       $perfil->setRequired(true);
       $perfil->addMultiOptions(array(''=>''));
       //consuta dos options
       $tb_perfis = new Perfis();
       $resultado = $tb_perfis->fetchAll(null, 'nivel')->toArray();
       $options = array();
       foreach ($resultado as $linha ){
              $options[$linha['id']] = $linha['nome'];
       }

       $perfil->addMultiOptions($options);


       //Atributo aplicacao
       $aplicacao = new Zend_Form_Element_Select('aplicacoes_id');
       $aplicacao->setLabel('Aplicação:');
       $aplicacao->setRequired(true);
       $aplicacao->addMultiOptions(array(''=>''));
       //consuta dos options
       $tb_aplicacoes = new Aplicacoes();
       $resultado = $tb_aplicacoes->fetchAll(null, 'nome')->toArray();
       $options = array();
       foreach ($resultado as $linha ){
              $options[$linha['id']] = $linha['nome'];
       }

       $aplicacao->addMultiOptions($options);


       //Atributo ações
       $acao = new Zend_Form_Element_MultiCheckbox('acoes');
       $acao->setLabel('Acões:');
       //consuta dos options
       $tb_acoes = new Acoes();
       $resultado = $tb_acoes->fetchAll()->toArray();
       $options = array();
       foreach ($resultado as $linha ){
              $options[$linha['nome']] = $linha['nome'];
       }

       $acao->addMultiOptions($options);
      
       
      

       
        $form->addElements(array($id, $perfil,$aplicacao,$acao));
        $form->setElementDecorators($this->getElementDecorators());
        $id->setDecorators($this->getElementHiddenDecorators());
        $acao->setDecorators(array(
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
       $aplicacao = new Zend_Form_Element_Select('aplicacoes_id');
       $aplicacao->setLabel('Aplicação:')
                 ->addMultiOptions(array(''=>''));
       //consuta dos options
       $tb_aplicacoes = new Aplicacoes();
       $resultado = $tb_aplicacoes->fetchAll(null, 'nome')->toArray();
       $options = array();
       foreach ($resultado as $linha ){
              $options[$linha['id']] = $linha['nome'];
       }

       $aplicacao->addMultiOptions($options);



        //Atributo perfil
        $perfil = new Zend_Form_Element_Select('perfis_id');
        $perfil->setLabel('Perfil:')
                ->addMultiOptions(array(''=>''));
       //consulta dos options
       $tb_perfis = new Perfis();
       $resultado = $tb_perfis->fetchAll(null,'nivel')->toArray();
       $options = array();
       foreach ($resultado as $linha ){
              $options[$linha['id']] = $linha['nome'];
       }

       $perfil->addMultiOptions($options);



       $form->addElements(array($id, $perfil, $aplicacao));
       $form->setElementDecorators($this->getElementDecorators());

       return $form;

    }
    public function inserirAction(){

        $form = $this->getForm();

        if($this->_request->isPost()){

            if ($form->isValidPartial($_POST)){
                $id = $form->getValue('id');
                $data = array();
                $data['perfis_id'] = $form->getValue('perfis_id');
                $data['aplicacoes_id']= $form->getValue('aplicacoes_id');
                $data['acoes']= implode(', ',$form->getValue('acoes'));

               
                if(empty($id)){
                    

                     $this->getModel()->insert($data);
                     $this->getSession()->mensagem = $this->getItemNome()." adicionado com sucesso!";
                     $this->_redirect($this->getController().'/listar');
                }
                else{

                     $where = $this->getModel()->getAdapter()->quoteInto('id = ?',$id);
                     $this->getModel()->update($data,$where);
                     $this->getSession()->mensagem = $this->getItemNome()." editado com sucesso!";
                     $this->_redirect($this->getController().'/listar');
                }

            } else {

                $form->populate($_POST);

            }
        }
        $this->view->form = $form;
        //Não exibe a div que contém os checkbox;
        $this->view->divCheckBox = 'none';
        $this->viewAssign();
        
        }
    public function editarAction(){

        if($this->_request->isGet()){
            $filter = new Zend_Filter_Digits();
            $id = $filter->filter($this->_request->getParam('id'));

            $where = $this->getModel()->getAdapter()->quoteInto('id = ?',$id);
            $resultado = $this->getModel()->fetchRow($where);

            if(count($resultado) == 0){

                 $this->getSession()->mensagem = $this->getItemNome()." não encontrado!";
                 $this->_redirect($this->getController().'/listar');
            }

            //converte resultado em uma array
            $resultado = $resultado->toArray();
           

            //set options do aplicacao
            $tb_aplicacoesacoes = new Acoesaplicacao();
            $where = $this->getModel()->getAdapter()->quoteInto('aplicacoes_id = ?',$resultado['aplicacoes_id']);
            $resultado2 = $tb_aplicacoesacoes->selectAcoes($where);
            $resultado2 = explode(', ', $resultado2[0]['acoes']);
           
            $options= array();
            foreach ($resultado2 as $row){
                    $options[$row]=$row;

             }
            $form = $this->getForm();
            $form->acoes->setMultiOptions($options);

            //set options selected
            $resultado['acoes']=explode(', ', $resultado['acoes']);
            $form->populate($resultado);

           
            $this->view->form = $form;
            $this->viewAssign();
            $this->view->divCheckBox = 'block';
            $this->render('inserir', null, false);

         }
         else{
            $this->_redirect($this->getController().'/listar');
         }

    }
    public function ajaxAction(){
         if($this->_request->isPost()){

             $aplicacao_id = $this->_request->getParam('aplicacao');
             $tb_aplicacoesacoes = new Acoesaplicacao();
             $where = $this->getModel()->getAdapter()->quoteInto('aplicacoes_id = ?',$aplicacao_id);
             $resultado = $tb_aplicacoesacoes->selectAcoes($where);
             $resultado = explode(', ', $resultado[0]['acoes']);
             $options= array();
             foreach ($resultado as $row){
                    $options[$row]=$row;
                    
             }

             $this->view->options = $options;

         }else{
              $this->_redirect($this->getController().'/listar');
         }
         $this->_helper->layout->disableLayout();
     }
    public function paramWhere() {
            $form = $this->getFormSearch();

            $form->populate($_POST);
            //Retorna os valores filtrados

            $perfil = $form->getValue('perfis_id');
            $aplicacao = $form->getValue('aplicacoes_id');

            //inicialização da cláusula where
            $where = 'a.id > 0';

           if(!empty($perfil)){
                     $where .= ' AND perfis_id ='.$perfil;
            }

           if(!empty($aplicacao)){
                     $where .= ' AND aplicacoes_id ='.$aplicacao;
            }



       return $where;
    }
}

