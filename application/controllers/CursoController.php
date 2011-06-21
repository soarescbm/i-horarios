<?php

/**
 * Controlador do Curso
 *
 * @author Paulo Soares da Silva
 * @copyright P2S System - Soluções Web
 * @package Sysweb
 * @subpackage Default.Controller
 * @version 1.0
 */
class CursoController extends P2s_Controller_Abstract {

   
    /**
     * Inicializa a instancia do controlador
     */
    public function init(){
        parent::init();
        Zend_Loader::loadClass('Curso');
        Zend_Loader::loadClass('Turno');
        $this->setModel(new Curso());
        $this->setTituloPagina('Cursos');
        $this->setTituloPaginaClass('_config');
        $this->setInfoForm('Dados do Curso');
        $this->setItemNome('Curso');
        $this->setHtmlColunasTabela(array(
            'th'=>array('Nome'=>array( 'align' =>'left', 'width'=>'200' ))));
        
       
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
        $existeNome = new Zend_Validate_Db_NoRecordExists(
                array(
                    'table' =>'curso',
                    'field' => 'nome',
                    'exclude' => array(
                        'field'=>'id',
                        'value'=> $this->_request->getParam('id')
                    )
                  )
        );
        $nome->addValidator($existeNome);

       
           //Atributo sub menu
       $turno = new Zend_Form_Element_Select('turno_id');
       $turno->setLabel('Turno:')
              ->setRequired()
              ->addMultiOptions(array('0'=>''));
       $tb_turno = new Turno();
       $resultado = $tb_turno->fetchAll()->toArray();
       $options = array();
       foreach ($resultado as $linha ){
              $options[$linha['id']] = $linha['nome'];
       }

       $turno->addMultiOptions($options);
       
        $form->addElements(array($id, $nome,$turno));
        $form->setElementDecorators($this->getElementDecorators());
        $id->setDecorators($this->getElementHiddenDecorators());

        return $form;
    }
   
}

