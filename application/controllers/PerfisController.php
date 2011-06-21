<?php

/**
 * Controlador dos perfis dos sistema
 *
 * @filesource
 * @author Paulo Soares da Silva
 * @copyright P2S System - Soluções Web
 * @package SysWeb
 * @subpackage Default.Controller
 * @version 1.0
 */
class PerfisController extends P2s_Controller_Abstract {

   

    public function init(){
        parent::init();
        Zend_Loader::loadClass('Perfis');
        $this->setModel(new Perfis());
        $this->setTituloPagina('Perfis do Sistema');
        $this->setTituloPaginaClass('_user');
        $this->setInfoForm('Dados do Perfil');
        $this->setItemNome('Perfil');
        $this->setHtmlColunasTabela(array(
            'th'=>array('Nome'=>array( 'align' =>'left', 'width'=>'100' ),
                        'Herda de'=>array( 'align' =>'left', 'width'=>'100' ))
             ));
            
       
    }
    public function inserirAction(){

        $form = $this->getForm();

        if($this->_request->isPost()){

            if ($form->isValid($_POST)){
                $id = $form->getValue('id');
               
                if(empty($id)) {
                     //consulta a existência do perfil
                     $where = "nome ='".strtolower($form->getValue('nome'))."'";
                     $existe_perfil = $this->getModel()->fetchRow($where);

                     //consulta a existência do mesmo nivel
                     $where = "nivel =".strtolower($form->getValue('nivel'));
                     $existe_nivel = $this->getModel()->fetchRow($where);

                     //Verifica se o perfil herda dele mesmo, o que não pode acontecer
                     $where = "id =".$form->getValue('herda_id');
                     $resultado = $this->getModel()->fetchRow($where);
                     $nivel= $resultado->nivel;

                     if($existe_perfil != null){
                          $form->nome->addError('Já existe um perfil com esse nome.');
                     }
                     elseif($existe_nivel != null){
                          $form->nivel->addError('Já existe um perfil com esse nível.');
                     }
                     elseif($nivel > $form->getValue('nivel')){
                          $form->herda_id->addError('Um perfil não pode herda outro perfil de nível superior.');
                     }
                     else {
                         $this->getModel()->insert($form->getValues());
                         $this->getSession()->mensagem = $this->getItemNome()." adicionado com sucesso!";
                         $this->_redirect($this->getController().'/listar');
                     }

                     
                }
                else{
                     //consulta a existência do perfil
                     $where = "nome ='".strtolower($form->getValue('nome'))."' AND id <>". $id;
                     $existe_perfil = $this->getModel()->fetchRow($where);

                     //consulta a existência do mesmo nivel
                     $where = "nivel =".strtolower($form->getValue('nivel'))." AND id <>". $id;
                     $existe_nivel = $this->getModel()->fetchRow($where);

                      //Verifica se o perfil herda dele mesmo, o que não pode acontecer
                     $where = "id =".$form->getValue('herda_id');
                     $resultado = $this->getModel()->fetchRow($where);
                     $herda= $resultado->nome;
                     $nivel= $resultado->nivel;

                      //Verifica se o nível auterado é maior do um perfil que o herda
                     $where = 'herda_id ='.$form->getValue('id').' AND nivel < '.$form->getValue('nivel');
                     $tem_nivel_menor = $this->getModel()->fetchRow($where);


                     if($existe_perfil != null){
                          $form->nome->addError('Já existe um perfil com esse nome.');
                     }
                     elseif($tem_nivel_menor != null){
                          $form->nivel->addError('Este perfil é herdado por outro perfil de nível inferior.');
                     }
                     elseif($existe_nivel != null){
                          $form->nivel->addError('Já existe um perfil com esse nível.');
                     }
                     elseif($herda == $form->getValue('nome')){

                          $form->herda_id->addError('O perfil "'.$form->getValue('nome').'" não pode herdar ele mesmo.');
                     }
                     elseif($nivel > $form->getValue('nivel')){
                          $form->herda_id->addError('Um perfil não pode herda outro perfil de nível superior.');
                     }
                     else{

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
    /**
     * Acão defaut do contrador
     */
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
            $form = $this->getForm();

            //Atributo personalizado
            $form->herda_id->clearMultiOptions();
            $form->herda_id->addMultiOptions(array('0'=>''));
            $where = 'nivel < '.$resultado['nivel'];
            $consulta = $this->getModel()->fetchAll($where,'nivel')->toArray();
            $options = array();
            foreach ($consulta as $linha ){
                 $options[$linha['id']] = $linha['nome'];
             }
            $form->herda_id->addMultiOptions($options);
            //fim
            
            $form->populate($resultado);
            $this->view->form = $form;
            $this->viewAssign();
            $this->render('inserir', null, true);

         }
         else{
            $this->_redirect($this->getController().'/listar');
         }

    }
    
    public function  getForm() {
        $form = parent::getForm();
        
        //Atributo id
        $id = new Zend_Form_Element_Hidden('id');
        

        //Atributo nome do perfil
        $nome = new Zend_Form_Element_Text('nome');
        $nome->setLabel('Nome:');
        $nome->setRequired(true);
        $nome->setAttrib('class', 'input');
        $nome->setAttrib('size', '50');
        $nome->addFilter('StringTrim');
        //$nome->addFilter('Alpha');
        $nome->addValidator('StringLength', true, array('4','30'));
        //$nome->addValidator('Alpha');


        //Atributo herda perfil
       $herda = new Zend_Form_Element_Select('herda_id');
       $herda->setLabel('Herda de:');
       $herda->addMultiOptions(array('0'=>''));
       $resultado = $this->getModel()->fetchAll(null,'nivel')->toArray();

       $options = array();
       foreach ($resultado as $linha ){
              $options[$linha['id']] = $linha['nome'];
       }
     
       $herda->addMultiOptions($options);

       //Atributo nivel
       $nivel = new Zend_Form_Element_Text('nivel');
       $nivel->setLabel('Nivel:');
       $nivel->addValidator('Digits');
       $nivel->setAttrib('maxlength', '2');
       $nivel->setAttrib('size', '2');
       $nivel->setRequired(true);
       $nivel->setValue('0');

       
       
        $form->addElements(array($id, $nome,$herda,$nivel));
        $form->setElementDecorators($this->getElementDecorators());
        $id->setDecorators($this->getElementHiddenDecorators());

        return $form;
    }
   
}

