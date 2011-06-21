<?php

/**
 * Controlador dos menus do sistema
 *
 * @filesource
 * @author Paulo Soares da Silva
 * @copyright P2S System - Soluções Web
 * @package SysWeb
 * @subpackage Default.Controller
 * @version 1.0
 */
class MenusController extends P2s_Controller_Abstract {

   

    public function init(){
        parent::init();
        Zend_Loader::loadClass('Menu');
        $this->setModel( new Menu());
        $this->setTituloPagina('Menus');
        $this->setTituloPaginaClass('_config');
        $this->setInfoForm('Dados do Menu');
        $this->setItemNome('Menu');
        $this->setHtmlColunasTabela(array(
            'th'=>array('Nome'=>array( 'align' =>'left', 'width'=>'200' ),
                        'Link'=>array( 'align' =>'left', 'width'=>'100' ),
                        'Sub Menu de'=>array( 'align' =>'left', 'width'=>'100' ))
             ));
       
       
    }
    public function inserirAction(){

        $form = $this->getForm();

        if($this->_request->isPost()){

            if ($form->isValid($_POST)){
                
                 //Verifica se o menu é submenu dele mesmo, o que não pode ocorrer
                 $where = $this->getModel()->getAdapter()->quoteInto('id = ?',$form->getValue('sub_menu_id'));
                 $resultado = $this->getModel()->fetchRow($where);
                 $sub_menu= $resultado->nome;

                 if( strcasecmp($sub_menu,$form->getValue('nome')) == 0){
                          $form->sub_menu_id->addError('O menu "'.$form->getValue('nome').'" não pode ser submenu dele mesmo.');
                          $form->populate($_POST);
                 }
                 else{
                     $id = $form->getValue('id');
                     if(empty($id)){

                            //Inserção
                            $this->getModel()->insert($form->getValues());
                            $this->getSession()->mensagem = $this->getItemNome()." adicionado com sucesso!";
                            $this->_redirect($this->getController().'/listar');
                     }
                     else{
                     
                            //Edição
                            $where = $this->getModel()->getAdapter()->quoteInto('id = ?',$id);
                            $this->getModel()->update($form->getValues(),$where);
                            $this->getSession()->mensagem = $this->getItemNome()." editado com sucesso!";
                            $this->_redirect($this->getController().'/listar');
                     
                    }
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
        
        //Atributo id
        $id = new Zend_Form_Element_Hidden('id');
        

        //Atributo nome do menu
        $nome = new Zend_Form_Element_Text('nome');
        $nome->setLabel('Nome:');
        $nome->setRequired(true);
        $nome->setAttrib('class', 'input');
        $nome->setAttrib('size', '50');
        $nome->addFilter('StringTrim');
        $nome->addFilter('Alpha', array ('allowwhitespace' => true));
        $nome->addValidator('StringLength', true, array('4','30'));
        //$nome->addValidator('Alpha');

        //Atributo nome da aplicaco ou link
        $aplicacao = new Zend_Form_Element_Text('aplicacao');
        $aplicacao->setLabel('Aplicação / Link:');
        $aplicacao->setValue("#");
        $aplicacao->setRequired(true);
        $aplicacao->setAttrib('class', 'input');
        $aplicacao->setAttrib('size', '50');
        $aplicacao->addFilter('StringTrim');
        $aplicacao->setDescription('Nome da aplicacão ou a Url.');

        //$nome->addFilter('Alpha');
        $aplicacao->addValidator('StringLength', true, array('1','30'));
        //$nome->addValidator('Alpha');


        //Atributo parâmetros
        $param = new Zend_Form_Element_Text('param');
        $param->setLabel('Parâmetros:');
        $param->setAttrib('class', 'input');
        $param->setAttrib('size', '50');
        $param->addFilter('StringTrim');
        //$nome->addFilter('Alpha');
        $param->addValidator('StringLength', true, array('4','200'));
        //$nome->addValidator('Alpha');

        //Atributo sub menu
       $sub_menu = new Zend_Form_Element_Select('sub_menu_id');
       $sub_menu->setLabel('Sub Menu de:');
       $sub_menu->addMultiOptions(array('0'=>''));
       $resultado = $this->getModel()->fetchAll('ativo=1','nome')->toArray();
       $options = array();
       foreach ($resultado as $linha ){
              $options[$linha['id']] = $linha['nome'];
       }
     
       $sub_menu->addMultiOptions($options);

       //Atributo indice
       $indice = new Zend_Form_Element_Text('indice');
       $indice->setLabel('Índice:');
       $indice->addValidator('Digits');
       $indice->setAttrib('maxlength', '2');
       $indice->setAttrib('size', '2');


       //Atributo situação
       $ativo = new Zend_Form_Element_Radio('ativo');
       $ativo->setValue('1');
       $ativo->setLabel('Situação:');
       $ativo->addMultiOptions(array('1'=>'Ativado', '0'=>'Desativado'));
       

              
       
        $form->addElements(array($id, $nome,$aplicacao,$param,$sub_menu,$indice,$ativo));
        $form->setElementDecorators($this->getElementDecorators());
        $id->setDecorators($this->getElementHiddenDecorators());
        $ativo->setDecorators($this->getElementOptionsDecorators());

        return $form;
    }

   public function  addColuna(&$records, $indice, $id, $row) {

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

