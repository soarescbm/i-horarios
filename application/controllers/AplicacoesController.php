<?php

/**
 * Controlador dos aplicações dos sistema
 *
 * @filesource
 * @author Paulo Soares da Silva
 * @copyright P2S System - Soluções Web
 * @package SysWeb
 * @subpackage Default.Controller
 * @version 1.0
 */
class AplicacoesController extends P2s_Controller_Abstract {

   

    public function init(){
        parent::init();
        Zend_Loader::loadClass('Aplicacoes');
        $this->setModel(new Aplicacoes());
        $this->setTituloPagina('Aplicações do Sistema');
        $this->setTituloPaginaClass('_config');
        $this->setInfoForm('Dados da Aplicação');
        $this->setItemNome('Aplicação');
        $this->setHtmlColunasTabela(array(
            'th'=>array('Nome'=>array( 'align' =>'left', 'width'=>'100' ))));
        
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
        

        //Atributo nome
        $nome = new Zend_Form_Element_Text('nome');
        $nome->setLabel('Nome:');
        $nome->setRequired(true);
        $nome->setAttrib('class', 'input');
        $nome->setAttrib('size', '50');
        $nome->addFilter('StringTrim');
        $nome->addFilter('Alpha');
        $nome->addValidator('StringLength', true, array('4','30'));
        $nome->addValidator('Alpha');
        $existeNome = new Zend_Validate_Db_NoRecordExists(
                array(
                    'table' =>'aplicacoes',
                    'field' => 'nome',
                    'exclude' => array(
                        'field'=>'id',
                        'value'=> $this->_request->getParam('id')
                    )
                  )
        );
        $nome->addValidator($existeNome);

       
       
       
        $form->addElements(array($id, $nome));
        $form->setElementDecorators($this->getElementDecorators());
        $id->setDecorators($this->getElementHiddenDecorators());

        return $form;
    }
   
}

