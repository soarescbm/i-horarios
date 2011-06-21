<?php
/**
 * Controlador de Turmas ao sistemas
 *
 * @author Paulo Soares da Silva
 * @copyright P2S System - Soluções Web
 * @package Sysweb
 * @subpackage Default.Controller
 * @version 1.0
 */
class TurmaController extends P2s_Controller_Abstract {

   
    /**
     *Inicializa a instancia do objeto
     *@return void
     */
    public function init(){
        parent::init();
        Zend_Loader::loadClass('Turma');
        Zend_Loader::loadClass('Curso');


        $this->setModel( new Turma());
        $this->setTituloPagina('Turmas');
        $this->setTituloPaginaClass('_config');
        $this->setInfoForm('Dados da Turma');
        $this->setItemNome('Turma');
        $this->setHtmlColunasTabela( array(
            'th'=>array('Nome'=>array( 'align' =>'left', 'width'=>'150' ),
                     'Curso'=>array( 'align' =>'left', 'width'=>'200' ))
                ));

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
        $nome->setAttrib('size', '30');
        $nome->addFilter('StringTrim');
        

       
          //Atributo sub menu
       $curso = new Zend_Form_Element_Select('curso_id');
       $curso->setLabel('Curso:')
             ->setRequired()
              ->addMultiOptions(array('0'=>''));
       $tb_curso = new Curso();
       $resultado = $tb_curso->fetchAll(null,'nome')->toArray();
       $options = array();
       foreach ($resultado as $linha ){
              $options[$linha['id']] = $linha['nome'];
       }

       $curso->addMultiOptions($options);

       //Atributo situação
       $ativo = new Zend_Form_Element_Radio('ativo');
       $ativo->setValue('1');
       $ativo->setLabel('Ativo:');
       $ativo->addMultiOptions(array('1'=>'Sim', '0'=>'Não'));

       $form->addElements(array($id, $nome, $curso, $ativo));
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

    public function getFormSearch() {
       $form = new Zend_Form();

        $form->setAction($this->getUrl().'/listar')
             ->setMethod('post')
             ->setName('search')
             ->setAttrib('class', 'form01')
             ->setDecorators($this->getFormDecorators())
             ->addPrefixPath('P2s_Form_Decorator', 'P2s/Form/Decorator/', 'decorator');


         //Atributo aplicacao
       $curso = new Zend_Form_Element_Select('curso_id');
       $curso->setLabel('Curso:')
                 ->addMultiOptions(array(''=>''));
       //consuta dos options
       $tb = new Curso();
       $resultado = $tb->fetchAll(null, 'nome')->toArray();
       $options = array();
       foreach ($resultado as $linha ){
              $options[$linha['id']] = $linha['nome'];
       }

       $curso->addMultiOptions($options);



        //Atributo perfil
        $turma = new Zend_Form_Element_Text('nome');
        $turma->setLabel('Turma:')
              ->setAttrib('size', '30');



       $form->addElements(array($turma,$curso));
       $form->setElementDecorators($this->getElementDecorators());

       return $form;

    }

     public function paramWhere() {
            $form = $this->getFormSearch();

            $form->populate($_POST);
            //Retorna os valores filtrados

            $c = $form->getValue('curso_id');
            $t = $form->getValue('nome');

            //inicialização da cláusula where
            $where = 'a.id > 0';

           if(!empty($t)){
                     $where .= " AND a.nome ='".$t."'";
                   
            }

           if(!empty($c)){
                     $where .= ' AND a.curso_id ='.$c;
            }



       return $where;
    }
}

