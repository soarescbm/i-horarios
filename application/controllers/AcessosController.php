<?php
/**
 * Controlador de acessos ao sistemas
 *
 * @author Paulo Soares da Silva
 * @copyright P2S System - Soluções Web
 * @package Sysweb
 * @subpackage Default.Controller
 * @version 1.0
 */
class AcessosController extends P2s_Controller_Abstract {

   
    /**
     *Inicializa a instancia do objeto
     *@return void
     */
    public function init(){
        parent::init();
        Zend_Loader::loadClass('Logacesso');


        $this->setModel( new Logacesso());
        $this->setTituloPagina('Log de Acessos');
        $this->setTituloPaginaClass('_key');
        $this->setInfoForm('Dados do Acesso');
        $this->setItemNome('Log');
        $this->setHtmlColunasTabela( array(
            'th'=>array('Usuário'=>array( 'align' =>'left', 'width'=>'150' ))
            
                ));

              
    }
    /**
     * Lista os acessos ralizados no sistema
     * @return void
     */
    public function listarAction(){
        //Procurar
        $where = null;
        $search = $this->getModel()->getFieldSearch();

        if($this->_request->getParam($search['field'])){
            $where = str_replace('_', '.', $search['field']).'="'. $this->_request->getParam($search['field'])
                    .'" OR  '.str_replace('_', '.', $search['field']).' LIKE "%'.$this->_request->getParam($search['field'])
                    .'%"';

        }
        //Paginador
        $paginador  = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($this->getModel()->selectList(
                $where,$this->getModel()->getOrderField())));

        //Página requisitada
        if($this->_request->getParam('pagina')){

			$paginador->setCurrentPageNumber($this->_request->getParam('pagina'));
	    	}

        //Setando número de Itens
        if($this->_request->getParam('itens')){

             $paginador->setItemCountPerPage($this->_request->getParam('itens'));
         }
         else{
             $paginador->setItemCountPerPage($this->getItensPagina());
         }


         //cria a tabela de listagem
         $tabela = new P2s_Html_Table();

         $correnteItens = $paginador->getCurrentItems();

         $records = array();
         $indice = 0;
         $fieldKey = $this->getModel()->getFieldKey();
         $this->_fields = $this->getModel()->getFieldLabel();

         
         $data = new Zend_Date();
         foreach ($correnteItens as $row){

             $id = $row[$fieldKey];

              foreach ($this->_fields as $field => $label){
                  //formatação da data
                  if($field == 'data_hora') {
                        $data->set($row['data_hora'],'YYYY-MM-dd HH:mm:ss');
                        $records[$indice][$label] = $data->toString('dd-MM-YYYY HH:mm:ss');
                  }else{
                       $records[$indice][$label] = $row[$field];
                  }

              }
              //exibição do botão editar de acordo as restrições
              if($this->getAcl()->has(strtolower($this->getController()))){

                    if($this->getAcl()->isAllowed($this->_perfil,strtolower($this->getController()),'editar')){
                         $records[$indice]['Editar'] = '<a  href="'.$this->getUrl().'/editar/'.$fieldKey.'/'.$id.'"  title="Editar item '.$id. '"><img src="'.$this->getUrlBase().'/public/templates/system/imagens/edit2.png" border="0"></a>';
                    }
                }
              //exibição do botão excluir de acordo as restrições
              if($this->getAcl()->has(strtolower($this->getController()))){

                    if($this->getAcl()->isAllowed($this->_perfil,strtolower($this->getController()),'excluir')){
                          $records[$indice]['Excluir'] = '<a class ="excluir" href="'.$this->getUrl().'/excluir/'.$fieldKey.'/'.$id.'"  title="'.$this->getItemNome().' '. $id. '"><img src="'.$this->getUrlBase().'/public/templates/system/imagens/del.png" border="0"></a>';
                    }
                }



              $indice++;
         }
         //cria a tabela de listagem
         $this->view->tabela = $tabela->create($records,$this->getHtmlColunasTabela());
         $this->view->paginador = $paginador;
         $this->view->mensagem = $this->getSession()->mensagem;
         //destroya session
         $this->getSession()->unsetAll();
         //exibe o botão inserir conforme permissão
         if($this->getAcl()->has(strtolower($this->getController()))){

                    if($this->getAcl()->isAllowed($this->_perfil,strtolower($this->getController()),'inserir')){
                         $this->view->flegAdicionar = true;
                    }else {
                         $this->view->flegAdicionar = false;
                    }
         }
         $this->viewAssign();
         $this->render('listar', null, true);


    }
    /**
     * Especifica o formulário
     */
    public function  getForm() {
        $form = parent::getForm();
        
        
    }
   
}

