<?php

/**
 * Controlador de Ações
 *
 * @author Paulo Soares da Silva
 * @copyright P2S System - Soluções Web
 * @package Sysweb
 * @subpackage Default.Controller
 * @version 1.0
 */
class TiporestricaoController extends P2s_Controller_Abstract {

   
    /**
     * Inicializa a instancia do controlador
     */
    public function init(){
        parent::init();
        Zend_Loader::loadClass('Tiporestricao');
        $this->setModel(new Tiporestricao());
        $this->setTituloPagina('Tipo de Restrição');
        $this->setTituloPaginaClass('_config');
        $this->setInfoForm('Dados da Restrição');
        $this->setItemNome('Restrição');
        $this->setHtmlColunasTabela(array(
            'th'=>array('Nome'=>array( 'align' =>'left', 'width'=>'160' ))));
        
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
        $nome = new Zend_Form_Element_Text('nome');
        $nome->setLabel('Nome:');
        $nome->setRequired(true);
        $nome->setAttrib('class', 'input');
        $nome->setAttrib('size', '50');
        $nome->addFilter('StringTrim');
        $nome->addFilter('Alpha',array ('allowwhitespace' => true));
        $nome->addValidator('StringLength', true, array('4','30'));
        $existeNome = new Zend_Validate_Db_NoRecordExists(
                array(
                    'table' =>'tipo_restricao',
                    'field' => 'nome',
                    'exclude' => array(
                        'field'=>'id',
                        'value'=> $this->_request->getParam('id')
                    )
                  )
        );
        $nome->addValidator($existeNome);

       //Atributo nome
        $nivel = new Zend_Form_Element_Text('nivel');
        $nivel->setLabel('Nível:');
        $nivel->setRequired(true);
        $nivel->setAttrib('class', 'input');
        $nivel->setAttrib('size', '2');
        $nivel->addFilter('StringTrim');
        $nivel->addFilter('Digits');
        $nivel->addValidator('Digits');
       

       
       
        $form->addElements(array($id, $nome,$nivel));
        $form->setElementDecorators($this->getElementDecorators());
        $id->setDecorators($this->getElementHiddenDecorators());

        return $form;
    }
   
}

