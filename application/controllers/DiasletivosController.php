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
class DiasletivosController extends P2s_Controller_Abstract {

   
    /**
     * Inicializa a instancia do controlador
     */
    public function init(){
        parent::init();
        Zend_Loader::loadClass('Dias');
        $this->setModel(new Dias());
        $this->setTituloPagina('Dias Letivos da Semana');
        $this->setTituloPaginaClass('_config');
        $this->setInfoForm('Dados do dia');
        $this->setItemNome('Dia');
        $this->setHtmlColunasTabela(array(
            'th'=>array('Dia'=>array( 'align' =>'left', 'width'=>'80' ))));
        
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
                    'table' =>'semana_dia',
                    'field' => 'nome',
                    'exclude' => array(
                        'field'=>'id',
                        'value'=> $this->_request->getParam('id')
                    )
                  )
        );
        $nome->addValidator($existeNome);

        $ordem = new Zend_Form_Element_Text('sequencia');
        $ordem->setLabel('Sequência:')
              ->setRequired()
              ->setAttrib('class', 'input')
              ->setAttrib('size', '2')
              ->addFilter('Digits')
              ->addValidator('Digits');

          //Atributo situação
       $ativo = new Zend_Form_Element_Radio('ativo');
       $ativo->setValue('1');
       $ativo->setLabel('Letivo:');
       $ativo->addMultiOptions(array('1'=>'Sim', '0'=>'Não'));
       
       
        $form->addElements(array($id, $nome,$ordem,$ativo));
        $form->setElementDecorators($this->getElementDecorators());
        $id->setDecorators($this->getElementHiddenDecorators());
        $ativo->setDecorators($this->getElementOptionsDecorators());
        return $form;
    }
    public function  addColuna(&$records, $indice, $id, $row) {

              if($row['ativo'] == 1){
                  $records[$indice]['Letivo'] = '<a  href="'.$this->getUrl().'/editarstatus/id/'.$id.'/ativo/0"  title="Editar item '.$id. '"><img src="'.$this->getUrlBase().'/public/templates/system/imagens/tick.png" border="0" title="Ativa"></a>';
              }else {
                  $records[$indice]['Letivo'] = '<a  href="'.$this->getUrl().'/editarstatus/id/'.$id.'/ativo/1"  title="Editar item '.$id. '"><img src="'.$this->getUrlBase().'/public/templates/system/imagens/cancelar.png"  heith= "5px" border="0" title="Não Ativa"></a>';
              }
    }

    public function editarstatusAction(){


        if($this->_request->isGet()){

                $dados = array();
                $id = $this->_request->getParam('id');
                $ativo = $this->_request->getParam('ativo');

                if(!empty($id)){

                     $dados['ativo'] = $ativo;
                     $where = $this->getModel()->getAdapter()->quoteInto('id = ?',$id);
                     $this->getModel()->update($dados,$where);
                     //$this->getSession()->mensagem = $this->getItemNome()." editada com sucesso!";
                     $this->_redirect($this->getController().'/listar');
                }
                else{

                    $this->_redirect($this->getController().'/listar');
                }

           }

    }

   
}

