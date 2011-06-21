<?php
/**
 * Controlador de dias letivos da semana
 *
 * @author Paulo Soares da Silva
 * @copyright P2S System - Soluções Web
 * @package Sysweb
 * @subpackage Default.Controller
 * @version 1.0
 */
class HorariosController extends P2s_Controller_Abstract {

   
    /**
     *Inicializa a instancia do objeto
     *@return void
     */
    public function init(){
        parent::init();
        Zend_Loader::loadClass('Horario');
        Zend_Loader::loadClass('Turno');
        $this->setModel( new Horario());
        $this->setTituloPagina('Horários de Aula');
        $this->setTituloPaginaClass('_config');
        $this->setInfoForm('Dados do horário');
        $this->setItemNome('Horário');
        $this->setHtmlColunasTabela( array(
            'th'=>array('Horário'=>array( 'align' =>'left', 'width'=>'100' ))
            
                ));
              
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
        $nome = new Zend_Form_Element_Text('descricao');
        $nome->setLabel('Horário:');
        $nome->setRequired(true);
        $nome->setAttrib('class', 'input');
        $nome->setAttrib('size', '20');
        $nome->addFilter('StringTrim');
        $nome->addValidator('StringLength', true, array('4','30'));
        $existeNome = new Zend_Validate_Db_NoRecordExists(
                array(
                    'table' =>'horario',
                    'field' => 'descricao',
                    'exclude' => array(
                        'field'=>'id',
                        'value'=> $this->_request->getParam('id')
                    )
                  )
        );
        $nome->addValidator($existeNome);


        $ordem = new Zend_Form_Element_Text('ordem');
        $ordem->setLabel('Sequência:')
              ->setRequired()
              ->setAttrib('class', 'input')
              ->setAttrib('size', '2')
              ->addFilter('Digits')
              ->addValidator('Digits');

          //Atributo sub menu
       $turno = new Zend_Form_Element_Select('turno_id');
       $turno->setLabel('Turno:')
              ->addMultiOptions(array('0'=>''));
       $tb_turno = new Turno();
       $resultado = $tb_turno->fetchAll()->toArray();
       $options = array();
       foreach ($resultado as $linha ){
              $options[$linha['id']] = $linha['nome'];
       }

       $turno->addMultiOptions($options);

        $form->addElements(array($id, $nome, $turno, $ordem));
        $form->setElementDecorators($this->getElementDecorators());
        $id->setDecorators($this->getElementHiddenDecorators());

        return $form;
    }
   
}

