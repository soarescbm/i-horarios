<?php

/**
 * Controlador do Turno
 *
 * @author Paulo Soares da Silva
 * @copyright P2S System - Soluções Web
 * @package Sysweb
 * @subpackage Default.Controller
 * @version 1.0
 */
class TurnoController extends P2s_Controller_Abstract {

   
    /**
     * Inicializa a instancia do controlador
     */
    public function init(){
        parent::init();
        Zend_Loader::loadClass('Turno');
        $this->setModel(new Turno());
        $this->setTituloPagina('Turno');
        $this->setTituloPaginaClass('_config');
        $this->setInfoForm('Dados do Turno');
        $this->setItemNome('Turno');
        $this->setHtmlColunasTabela(array(
            'th'=>array('Nome'=>array( 'align' =>'left', 'width'=>'80' ))));
        
        

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
                    'table' =>'turno',
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

