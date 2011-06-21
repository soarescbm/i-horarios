<?php

/**
 * Controlador de conta de usuários do sistema
 *
 * @filesource
 * @author Paulo Soares da Silva
 * @copyright P2S System - Soluções Web
 * @package SysWeb
 * @subpackage Default.Controller
 * @version 1.0
 */
class ContaController extends P2s_Controller_Abstract {

   

    public function init(){
        parent::init();
        Zend_Loader::loadClass('Usuarios');

        $this->setModel( new Usuarios());
        $this->setTituloPagina('Conta de Usuário');
        $this->setTituloPaginaClass('_user');
        $this->setInfoForm('Alteração de Senha');
        $this->setItemNome('Senha');


       
       
    }
    public function indexAction(){
        $id = Zend_Registry::get('Id');
        $where = $this->getModel()->getAdapter()->quoteInto('id = ?',$id);
        $resultado = $this->getModel()->fetchRow($where);

        $this->view->conta = $resultado;
        $this->viewAssign();

    }
    public function editarAction(){

            $id = Zend_Registry::get('Id');
            $where = $this->getModel()->getAdapter()->quoteInto('id = ?',$id);
            $resultado = $this->getModel()->fetchRow($where);

            if(count($resultado) == 0){

                 $this->getSession()->mensagem = $this->getItemNome()." não encontrado!";
                 $this->_redirect($this->getController().'/listar');
            }
            $resultado = $resultado->toArray();
            $form = $this->getForm();
            $form->populate($resultado);

            $this->view->form = $form;
            $this->viewAssign();
            $this->render('inserir', null, true);


    }
    public function inserirAction(){
        if($this->_request->isPost()){
            $form = $this->getForm();
            if ($form->isValid($_POST)){
                          $id = $form->getValue('id');

                          $where = $this->getModel()->getAdapter()->quoteInto('id = ?',$id);
                          $resultado = $this->getModel()->fetchRow($where);
                        
                          //Faz a comparação entre a senha e a repetição da senha
                          if(strcasecmp($form->getValue('nova1'),$form->getValue('nova2'))!= 0){
                             $form->nova2->addError('As senhas  digitadas são diferentes.');
                             $form->populate($_POST);
                          }elseif(strcasecmp(md5($form->getValue('senha')),$resultado['senha']) != 0){
                             $form->senha->addError('A senha digitada não confere com senha atual.');
                             $form->populate($_POST);
                          }else{
                             //Encriptação da senha com MD5
                             $form->senha->setValue(md5($form->getValue('nova1')));
                             //Remove os elementos senhas
                             $form->removeElement('nova1');
                             $form->removeElement('nova2');
                             //Edição
                                $where = $this->getModel()->getAdapter()->quoteInto('id = ?',$id);
                                $this->getModel()->update($form->getValues(),$where);
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
    public function  getForm() {
        $form = parent::getForm();
        $form->setAction($this->getUrl().'/inserir/');
        //Atributo id
        $id = new Zend_Form_Element_Hidden('id');
        
      
        //Atributo senha
        $senha1 = new Zend_Form_Element_Password('senha');
        $senha1->setRequired(true);
        $senha1->setLabel('Senha Atual:');
        $senha1->setAttrib('class', 'input');
        $senha1->setAttrib('size', '30');
        $senha1->setAttrib('autocomplete', 'off');
        $senha1->addFilter('StringTrim');
        //$nome->addFilter('Alpha');
        $senha1->addValidator('StringLength', true, array('6','30'));
        //$nome->addValidator('Alpha');

        //Atributo senha
        $senha2 = new Zend_Form_Element_Password('nova1');
        $senha2->setRequired(true);
        $senha2->setLabel('Nova Senha:');
        $senha2->setAttrib('class', 'input');
        $senha2->setAttrib('size', '30');
        $senha2->setAttrib('autocomplete', 'off');
        $senha2->addFilter('StringTrim');
        //$nome->addFilter('Alpha');
        $senha2->addValidator('StringLength', true, array('6','30'));
        //$nome->addValidator('Alpha');

         //Atributo senha
        $senha3 = new Zend_Form_Element_Password('nova2');
        $senha3->setRequired(true);
        $senha3->setLabel('Repita a Nova Senha:');
        $senha3->setAttrib('class', 'input');
        $senha3->setAttrib('size', '30');
        $senha3->setAttrib('autocomplete', 'off');
        $senha3->addFilter('StringTrim');
        //$nome->addFilter('Alpha');
        $senha3->addValidator('StringLength', true, array('6','30'));
        //$nome->addValidator('Alpha');

        
       
        $form->addElements(array($id,$senha1,$senha2,$senha3));
        $form->setElementDecorators($this->getElementDecorators());
        $id->setDecorators($this->getElementHiddenDecorators());
       
        return $form;
    }
   
}

