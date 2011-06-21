<?php

/**
 * Controlador das ações das aplicações do sistema
 *
 * @filesource
 * @author Paulo Soares da Silva
 * @copyright P2S System - Soluções Web
 * @package SysWeb
 * @subpackage Default.Controller
 * @version 1.0
 */
class AcoesaplicacaoController extends P2s_Controller_Abstract {

   
    public function init(){
        parent::init();
        Zend_Loader::loadClass('Acoesaplicacao');
        Zend_Loader::loadClass('Aplicacoes');
        Zend_Loader::loadClass('Acoes');
        $this->setModel(new Acoesaplicacao());
        $this->setTituloPagina('Ações das Aplicações');
        $this->setTituloPaginaClass('_config');
        $this->setInfoForm('Dados das Ações da Aplicação');
        $this->setItemNome('Ações');
        $this->setHtmlColunasTabela(array(
            'th'=>array('Aplicação'=>array( 'align' =>'left', 'width'=>'100' ),
                        'Ações'=>array( 'align' =>'left', 'width'=>'300' ))
             ));

       
    }
    public function  getForm() {
        $form = parent::getForm();
        
        //Atributo id
        $id = new Zend_Form_Element_Hidden('id');
        

        //Atributo aplicacao
       $aplicacao = new P2s_Form_Select('aplicacoes_id');
       $aplicacao->setLabel('Aplicação:');
       $aplicacao->setRequired(true);
       $aplicacao->setAddItens('/aplicacoes/inserirbox');
       $aplicacao->addMultiOptions(array(''=>''));
       //consuta dos options
       $tb_aplicacoes = new Aplicacoes();
       $resultado = $tb_aplicacoes->fetchAll(null,'nome')->toArray();
       $options = array();
       foreach ($resultado as $linha ){
              $options[$linha['id']] = $linha['nome'];
       }

       $aplicacao->addMultiOptions($options);



       //Atributo ações
       $acao = new Zend_Form_Element_MultiCheckbox('acoes');
       $acao->setLabel('Acões:');
       $acao->setRequired(true);
       //consuta dos options
       $tb_acoes = new Acoes();
       $resultado = $tb_acoes->fetchAll(null,'nome')->toArray();
       $options = array();
       foreach ($resultado as $linha ){
              $options[$linha['nome']] = $linha['nome'];
       }

       $acao->addMultiOptions($options);


       
       
       
        $form->addElements(array($id, $aplicacao,$acao));
        $form->setElementDecorators($this->getElementDecorators());
        $id->setDecorators($this->getElementHiddenDecorators());
        $acao->setDecorators(array(
        'ViewHelper',
        array(array('ajax' => 'HtmlTag'), array('tag' => 'div', 'class' => 'checkboxAjax')),
        array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
        array('Label',array('tag' => 'td')),
        array(array('row' => 'HtmlTag'), array('tag' => 'tr')))
                );
        $aplicacao->setDecorators($this->getElementSelectAddDecorators());

        return $form;
    }
    public function inserirAction(){

        $form = $this->getForm();

        if($this->_request->isPost()){

            if ($form->isValid($_POST)){
                $id = $form->getValue('id');
                $data = array();
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

                 $this->getSession()->mensagem = $this->getItemNome()." não encontrado!";
                 $this->_redirect($this->getController().'/listar');
            }
            $resultado = $resultado->toArray();
            $resultado['acoes']=explode(', ', $resultado['acoes']);
            $form = $this->getForm();
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

