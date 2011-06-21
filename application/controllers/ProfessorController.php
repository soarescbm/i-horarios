<?php
/**
 * Controlador de Professor
 *
 * @author Paulo Soares da Silva
 * @copyright P2S System - Soluções Web
 * @package Sysweb
 * @subpackage Default.Controller
 * @version 1.0
 */
class ProfessorController extends P2s_Controller_Abstract {

   
    /**
     *Inicializa a instancia do objeto
     *@return void
     */
    public function init(){
        parent::init();
        Zend_Loader::loadClass('Professor');
       

        $this->setModel( new Professor());
        $this->setTituloPagina('Professores');
        $this->setTituloPaginaClass('_config');
        $this->setInfoForm('Dados do Professor');
        $this->setItemNome('Professor');
        $this->setHtmlColunasTabela( array(
            'th'=>array('Nome'=>array( 'align' =>'left', 'width'=>'250' )),
                            
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
        $nome = new Zend_Form_Element_Text('nome');
        $nome->setLabel('Nome:');
        $nome->setRequired(true);
        $nome->setAttrib('class', 'input');
        $nome->setAttrib('size', '50');
        $nome->addFilter('StringTrim');
      


        $ch_sala = new Zend_Form_Element_Text('carga_horaria_sala');
        $ch_sala->setLabel('Carga Horária em Sala:')
              ->setRequired()
              ->setDescription('Carga Horária Ideal')
              ->setAttrib('class', 'input')
              ->setAttrib('size', '2')
              ->addFilter('Digits')
              ->addValidator('Digits');


        $ch_trabalho = new Zend_Form_Element_Text('carga_horaria_trabalho');
        $ch_trabalho->setLabel('Carga Horária de Trabalho')
              ->setRequired()
              ->setAttrib('class', 'input')
              ->setAttrib('size', '2')
              ->addFilter('Digits')
              ->addValidator('Digits');


       //Atributo situação
       $ativo = new Zend_Form_Element_Radio('ativo');
       $ativo->setValue('1');
       $ativo->setLabel('Ativo:');
       $ativo->addMultiOptions(array('1'=>'Sim', '0'=>'Não'));

       $form->addElements(array($id, $nome, $ch_sala, $ch_trabalho,$ativo));
       $form->setElementDecorators($this->getElementDecorators());
       $id->setDecorators($this->getElementHiddenDecorators());
       $ativo->setDecorators($this->getElementOptionsDecorators());

        return $form;
    }
    public function  addColuna(&$records, $indice, $id, $row) {
              $records[$indice]['Restrições'] = '<a  href="'.$this->getUrlBase().'/restricao/listar/professor_id/'.$id.'"  title="Editar item '.$id. '"><img src="'.$this->getUrlBase().'/public/templates/system/imagens/edit2.png" border="0"></a>';
              if($row['ativo'] == 1){
                  $records[$indice]['Ativo'] = '<a  href="'.$this->getUrl().'/editarstatus/id/'.$id.'/ativo/0"  title="Editar item '.$id. '"><img src="'.$this->getUrlBase().'/public/templates/system/imagens/tick.png" border="0" title="Ativa"></a>';
              }else {
                  $records[$indice]['Ativo'] = '<a  href="'.$this->getUrl().'/editarstatus/id/'.$id.'/ativo/1"  title="Editar item '.$id. '"><img src="'.$this->getUrlBase().'/public/templates/system/imagens/cancelar.png"  heith= "5px" border="0" title="Não Ativa"></a>';
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

