<?php

/**
 * Controlador de ultimos acessos por usuário do sistema
 *
 * @filesource
 * @author Paulo Soares da Silva
 * @copyright P2S System - Soluções Web
 * @package SysWeb
 * @subpackage Default.Controller
 * @version 1.0
 */
class UltimosAcessosController extends P2s_Controller_Abstract {

   

    public function init(){
        parent::init();
        Zend_Loader::loadClass('Logacesso');
        $this->setModel(  new Logacesso());
        $this->setTituloPagina('Últimos Acessos');
        $this->setTituloPaginaClass('_key');
        $this->setInfoForm('Dados do Acesso');
        $this->setItemNome('Acesso');
        $this->setHtmlColunasTabela( array(
            'th'=>array('Usuário'=>array( 'align' =>'left', 'width'=>'150' ))


                ));



      
       
    }
    public function listarAction(){
        //Restrige a consulta ao usuário ativo
        $where = 'a.usuarios_id ='.Zend_Registry::get('Id');
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
                  if($field == 'data_hora') {
                        $data->set($row['data_hora'],'YYYY-MM-dd HH:mm:ss');
                        $records[$indice][$label] = $data->toString('dd-MM-YYYY HH:mm:ss');
                  }else{
                       $records[$indice][$label] = $row[$field];
                  }

              }

             
              $indice++;
         }

         $this->view->tabela = $tabela->create($records,$this->getHtmlColunasTabela());
         $this->view->paginador = $paginador;
         

         $this->view->flegAdicionar = false;
             
         
         $this->viewAssign();
         $this->view->search ='';
         $this->render('listar', null, true);


    }
    
   
}

