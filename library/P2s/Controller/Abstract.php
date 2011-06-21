<?php

/**
 * Controlador Abstract do sistema
 *
 * @filesource
 * @author Paulo Soares da Silva
 * @copyright P2S System - Soluções Web
 * @package SysWeb
 * @subpackage P2s.Controller
 * @version 1.0
 */
Abstract class P2s_Controller_Abstract extends Zend_Controller_Action {
   /**
    * Título da view. array('tituto'=>'Titulo da View, 'class'=>'selotor css')
    * @var array
    */
    private $_tituloPagina = array();
    /**
    * Nome do controlador
    * @var string
    */
    private $_controller;
    /**
    * Url base do sistema
    * @var string
    */
    private $_urlBase;
    /**
    * Url do controlador
    * @var string
    */
    private $_url;
    /**
    * Modelo da tabela de banco de dados
    * @var Zend_Db_Table
    */
    private $_model;
   /**
    * Número padrão de itens por listagem
    * @var int
    */
    private $_itensDefault;
    /**
    * Rótulo das colunas da listagem
    * @var array
    */
    private $_fields = array();
    /**
    * Informação do Formulário
    * @var string
    */
    private $_infoForm;
    /**
    * Decorador de elemento do formulário
    * @var array
    */
    private $_elementDecorators  = array (
        'ViewHelper',
        'DescriptionCustom',
        'Errors',
        array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
        array('Label', array('tag' => 'td', 'requiredSuffix'=>' *')),
        array(array('row' => 'HtmlTag'), array('tag' => 'tr')));
    /**
    * Decorador de butão do formulário
    * @var array
    */
    private $_buttonDecorators = array(
        'ViewHelper',
        array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
        array(array('label' => 'HtmlTag'), array('tag' => 'td', 'placement' => 'prepend')),
        array(array('row' => 'HtmlTag'), array('tag' => 'tr')));

    /**
    * Decorador de elemento hidden do formulário
    * @var array
    */

    private $_elementHiddenDecorators =  array(
        'ViewHelper',
        array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'hidden')),
        array(array('label' => 'HtmlTag'), array('tag' => 'td', 'placement' => 'prepend')),
        array(array('row' => 'HtmlTag'), array('tag' => 'tr')));
    
    /**
    * Decorador de options de radio checkbox
    * @var array
    */

    private $_optionDecorators =  array(
        'ViewHelper',
        'Errors',
        array(array('options' => 'HtmlTag'), array('tag' => 'div', 'class' => 'options')),
        array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
        array('Label',array('tag' => 'td')),
        array(array('row' => 'HtmlTag'), array('tag' => 'tr')));
    /**
    * Decorador de select com função de Adicionar
    * @var array
    */
    private  $_selectAddDecorators = array(
        'ViewHelper',
        'AddItens',
        'DescriptionCustom',
        'Errors',
        array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
        array('Label', array('tag' => 'td', 'requiredSuffix'=>' *')),
        array(array('row' => 'HtmlTag'), array('tag' => 'tr')));

    /**
    * Decorador do formulário
    * @var array
    */
    private $_formDecorators = array(
            'FormElements',
             array('HtmlTag', array('tag' => 'table')),
            'Form');
    private $_formNoTableDecorators = array(
            'FormElements',
            'Form');
    
    private $_displayGroupDecorators = array(
            'FormElements',
             array('HtmlTag', array('tag' => 'table')),
            'Fieldset'
            );
     /**
    * Nome do item principal da listagem
    * @var string
    */
    private $_itemNome;
    /**
    * Registro de session do sistema
    * @var Zend_Session
    */
    private $_session;
    /**
    * Atributos das colunas da tabela de listagem dos dados
    * @var array
    */
    private $_htmlTableAtribut = array();
    /**
    * Perfil do usuário ativo
    * @var string
    */
    private $_perfil;
    /**
     * Lista de controle de acesso
     * @var Zend_Acl
     */
    private $_acl;
    /**
     * Cláusula where default
     * @var string $where
     */
    private $_where = null;
    
    private $_mensagens = array(
        'editado'=>' editado com sucesso!',
        'adicionado'=>' adicionado com sucesso!',
        'excluido'=>' excluido com sucesso!',
        'confirma_exclusao'=>'Tem certeza que deseja excluir o ',
        'nao_encontrado'=>' não encontrado!'
        );

    /**
     * Inicaliza a instancia do controlador
     */
    public function init(){
        
        $this->setItensPagina(20);
        $this->setController($this->getRequest()->getControllerName());
        $this->setUrlBase(BASE_URL);
        $this->setItemNome('Item');
        $this->setUrl($this->getUrlBase().'/'.$this->getController());
        $this->setSession(new Zend_Session_Namespace(DEFAULT_SESSION));
        $this->setAcl(Zend_Registry::get('Acl'));
        $this->setPerfil(Zend_Registry::get('Perfil'));

    }
    /**
     * Acão defaut do contrador
     */
    public function indexAction(){
       
        $this->_forward('listar');
    }
     
     /**
     * Acão inserir do controlador
     */
    public function inserirAction(){

        $form = $this->getForm();
       
        if($this->_request->isPost()){

            if ($form->isValid($_POST)){
                $id = $form->getValue('id');
                if(empty($id)){

                   
                     $this->getModel()->insert($form->getValues());
                     $this->getSession()->mensagem = $this->getItemNome().$this->getMensagens('adicionado');
                     $this->_redirect($this->getController().'/listar');
                }
                else{
                     
                     $where = $this->getModel()->getAdapter()->quoteInto('id = ?',$id);
                     $this->getModel()->update($form->getValues(),$where);
                     $this->getSession()->mensagem = $this->getItemNome().$this->getMensagens('editado');
                     $this->_redirect($this->getController().'/listar');
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
     * Acão inserir em lithbox
     */
    public function inserirboxAction(){

       $form = $this->getForm();
       $form->setName('formbox');
       $url = $this->getUrl().'/inserirbox/element/'.$this->_request->getParam('element')
              .'/field/'.$this->_request->getParam('field');
       $form->setAction($url);
       $field = $this->_request->getParam('field');

        if($this->_request->isPost()){

            if ($form->isValid($_POST)){
                  //consulta a existência do mesmo nome
                     $where = $field.' ="'.$form->getValue($field).'"';
                     $resultado = $this->getModel()->fetchRow($where);
                     

                     //Verifica se já existe o mesmo nome
                      if ($resultado != null) {
                             $form->nome->addError('O nome de '.$this->getItemNome(). ' "'.$form->getValue($field).
                                  '" já existe.');
                             $form->populate($_POST);
                      }
                      else{

                        $this->getModel()->insert($form->getValues());
                        $resultado_array = $this->getModel()->fetchRow($where)->toArray();
                        $this->view->option = $resultado_array;
                        $this->view->field = $field;
                        $this->view->selectId = '#'.$this->_request->getParam('element');
                      }
                  
      
                  
            } else {

                $form->populate($_POST);

            }
        }
        $this->view->form = $form;
        $this->viewAssign();
        $this->_helper->layout->disableLayout();
        $this->render('inserirbox', null, true);
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

                 $this->getSession()->mensagem = $this->getItemNome().$this->getMensagens('nao_encontrado');
                 $this->_redirect($this->getController().'/listar');
            }
            $resultado = $resultado->toArray();
            $form = $this->getForm();
            $form->populate($resultado);

            $this->view->form = $form;
            $this->viewAssign();
            $this->render('inserir', null, true);

         }
         else{
            $this->_redirect($this->getController().'/listar');
         }
       
    }
    /**
     * Edição rápida de status
     */
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
     /**
     * Listagem do itens do controlador
     */
    public function listarAction(){
                
        //Paginador
        $paginador  = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($this->getModel()->selectList(
             $this->getWhere(),$this->getModel()->getOrderField())));

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



         //Itens da consulta atual
         $correnteItens = $paginador->getCurrentItems();
         $tabela = new P2s_Html_Table();

         $records = array();
         $indice = 0;
         $fieldKey = $this->getModel()->getFieldKey();
         $this->setFields($this->getModel()->getFieldLabel());
         
                 
         foreach ($correnteItens as $row){

             $id = $row[$fieldKey];
             $fields = $this->getFields();
              foreach ($fields as $field => $label){
                  $records[$indice][$label] = $row[$field];
              }

              $this->addColuna($records,$indice,$id,$row);
              if($this->getAcl()->has(strtolower($this->getController()))){

                    if($this->getAcl()->isAllowed($this->getPerfil(),strtolower($this->getController()),'editar')){
                         $records[$indice]['Editar'] = '<a  href="'.$this->getUrl().'/editar/'.$fieldKey.'/'.$id.'"  title="Editar item '.$id. '"><img src="'.$this->getUrlBase().'/public/templates/system/imagens/edit2.png" border="0"></a>';
                    }
                }
              if($this->getAcl()->has(strtolower($this->getController()))){

                    if($this->getAcl()->isAllowed($this->getPerfil(),strtolower($this->getController()),'excluir')){
                          $records[$indice]['Excluir'] = '<a class ="excluir" href="'.$this->getUrl().'/excluir/'.$fieldKey.'/'.$id.'"  title="'.$this->getItemNome().' '. $id. '"><img src="'.$this->getUrlBase().'/public/templates/system/imagens/del.png" border="0"></a>';
                    }
                }
             
                           
              $indice++;
         }

         $this->view->tabela = $tabela->create($records,$this->getHtmlColunasTabela());
         $this->view->paginador = $paginador;
         $this->view->mensagem = $this->getSession()->mensagem;
         $this->view->search = $this->getModel()->getFieldSearch();
         $this->view->search_custom = $this->getFormSearch();
         

         $this->getSession()->unsetAll();

         if($this->getAcl()->has(strtolower($this->getController()))){

                    if($this->getAcl()->isAllowed($this->getPerfil(),strtolower($this->getController()),'inserir')){
                         $this->view->flegAdicionar = true;
                    }else {
                         $this->view->flegAdicionar = false;
                    }
         }
         $this->viewAssign();
         $this->render('listar', null, true);
        
         
    }
     /**
     * Ação de exclusão do controlador
     */
    public function excluirAction(){

        if($this->_request->isGet()){
            $filter = new Zend_Filter_Digits();
            $id = $filter->filter($this->_request->getParam('id'));

            $where = $this->getModel()->getAdapter()->quoteInto('id = ?',$id);

           
                $this->getModel()->delete($where);
                $this->getSession()->mensagem = $this->getItemNome().$this->getMensagens('excluido');
          


         }
         $this->_redirect($this->getController().'/listar');

    }
     /**
     * Configuração incial do view
     */
    protected function viewAssign(){

        $this->view->titulo = $this->getTituloPagina('titulo');
        $this->view->tituloClass = $this->getTituloPagina('class');
        $this->view->url = $this->getUrl();
        $this->view->infoForm = $this->getInfoForm();
        $this->view->item = strtolower($this->getItemNome());
        $this->view->confirma_exclusao = $this->getMensagens('confirma_exclusao');
    }
    /**
     * Inicaliza o formulária do controlador
     * @return Zend_Form
     */
    protected function getForm(){

        $form = new Zend_Form();
        $form->setAction($this->getUrl().'/inserir');
        $form->setMethod('post');
        $form->setName('form01');
        $form->setAttrib('class', 'form01');
        $form->setDecorators($this->getFormDecorators());
        $form->addPrefixPath('P2s_Form_Decorator', 'P2s/Form/Decorator/', 'decorator');
        

       
        
        return $form;
    }
    /**
     * Inicaliza o formulária do filtro de listagem
     * @return Zend_Form
     */
    protected function getFormSearch(){
              
        return null;
    }
    /**
     * Retorna a condição parametrizada da consulta de dados
     * @return string
     */
    public function getWhere(){

        //Persistência da cláusula where através de sessão
        $where_persistente = new Zend_Session_Namespace($this->getController().'_where');

       
        if($this->_request->isPost() || $this->_request->isGet()){

            //Persistência da clausula where para possibilitar paginação com os parâmentros de pesquisa
            if(($this->_request->getParam('pagina') || $this->_request->getParam('itens')) && $where_persistente->__isset('where')){

                 $this->_where = $where_persistente->where;

            }else {
                $this->_where =  ($this->_where == null)? $this->paramWhere() : $this->_where.$this->paramWhere();
                $where_persistente->where = $this->_where;
                $where_persistente->setExpirationSeconds('300');

            }

        }else{
           //Caso não exista requisição por método post unset a session
           $where_persistente->unsetAll();
        }

       
       return $this->_where;
    }

     /**
     * Parametriza os dados da consulta
     * @retorn string Retorna os parâmetros da consulta
     */
    public function paramWhere(){

        $where = null;

        $search = $this->getModel()->getFieldSearch();

        if($this->_request->getParam($search['field'])){
            
            $where = str_replace('_', '.', $search['field']).'="'. $this->_request->getParam($search['field'])
                    .'" OR  '.str_replace('_', '.', $search['field']).' LIKE "%'.$this->_request->getParam($search['field'])
                    .'%"';

        }
        return $where;
    }
    /**
     * Adicina nova colunas na listagem de dados;
     * @param array $records Referência de records de dados
     * @param string $indice Índide da array de records de dados
     * @param int $id   Id do Item de Dados
     */
    public function addColuna(&$records,$indice,$id,$row){

       
    }
    /**
     * Retorna uma array com a configuração do decorador do elemento default
     * @return array
     */
    public function getElementDecorators(){

        return $this->_elementDecorators;

    }
     /**
     * Retorna uma array com a configuração do decorador do elemento hidden
     * @return array
     */
    public function getElementHiddenDecorators(){

        return $this->_elementHiddenDecorators;

    }
     /**
     * Retorna uma array com a configuração do decorador do elemento button
     * @return array
     */
    public function getElementButtonDecorators(){

        return $this->_buttonDecorators;

    }
     /**
     * Retorna uma array com a configuração do decorador do elemento select Add
     * o qual adicionar um link para um inserção de itens.
     * @return array
     */
    public function getElementSelectAddDecorators(){

        return $this->_selectAddDecorators;

    }
     /**
     * Retorna uma array com a configuração do decorador de elementos checkbox e radios.
     * @return array
     */
    public function getElementOptionsDecorators(){

        return $this->_optionDecorators;

    }
     /**
     * Retorna uma array com a configuração do decorador do formulário customizado
     * @return array
     */
    public function getFormDecorators(){

        return $this->_formDecorators;

    }
     /**
     * Retorna uma array com a configuração do decorador do formulário default
     * @return array
     */
    public function getFormNoTableDecorators(){

        return $this->_formNoTableDecorators;

    }
    /**
     * Retorna uma array com a configuração do decorador do display grupo(Fieldset) default
     * @return array
     */
    public function getDisplayGroupDecorators(){

        return $this->_displayGroupDecorators;

    }
    /**
     * Set a cláusula condicional da consulta padrão
     * @param string $where cláusula condicional
     */
    public function setWhere($where){
        $this->_where = $where;
    }
    /**
     * Set o titulo  da pagina
     * @param string $titulo
     */
    public function setTituloPagina($titulo){
        
        $this->_tituloPagina['titulo'] = $titulo;
    }
    /**
     * Set a class da pagina
     * @param  string $class
     */
    public function setTituloPaginaClass($class){

        $this->_tituloPagina['class'] = $class;
    }
    /**
     * Retorna o titulo ou a class da página
     * @param string ['titulo']['class'] $param
     */
    public function getTituloPagina($param = null){

        if(null !== $param){
            switch ($param) {
                case 'titulo': return $this->_tituloPagina['titulo'];
                break;
                case 'class' : return $this->_tituloPagina['class'];
                default: return null;
                break;
            }
        }
    }
    /**
     * @param Zend_Acl $acl 
     */
    public function setAcl ($acl){
        $this->_acl = $acl;
    }
    /**
     *
     * @return Zend_Acl
     */
    public function getAcl (){
        return $this->_acl;
    }

    /**
     * @param Sting $param
     */
    public function setController ($param){
        $this->_controller = $param;
    }
    /**
     *
     * @return string $param
     */
    public function getController (){
       return $this->_controller;
    }

    /**
     * @param array $param
     */
    public function setFields ($param){
        $this->_fields = $param;
    }
    /**
     *
     * @return array $param
     */
    public function getFields (){
       return $this->_fields;
    }

    /**
     * @param array $param
     */
    public function setHtmlColunasTabela($param){
        $this->_htmlTableAtribut = $param;
    }
    /**
     *
     * @return array $param
     */
    public function getHtmlColunasTabela (){
       return $this->_htmlTableAtribut;
    }
    /**
     * @param array $param
     */
    public function setInfoForm($param){
        $this->_infoForm = $param;
    }
    /**
     *
     * @return array $param
     */
    public function getInfoForm (){
       return $this->_infoForm;
    }

     /**
     * @param string $param
     */
    public function setItemNome($param){
        $this->_itemNome= $param;
    }
    /**
     *
     * @return string $param
     */
    public function getItemNome (){
       return $this->_itemNome;
    }

     /**
     * Set o número de itens exibidos na listagem por página
     * @param int $param
     */
    public function setItensPagina($param){
        $this->_itensDefault= $param;
    }
    /**
     *
     * @return int $param
     */
    public function getItensPagina (){
       return $this->_itensDefault;
    }

     /**
     * @param Zend_Db_Table  $model
     */
    public function setModel($model){
        $this->_model = $model;
    }
    /**
     *
     * @return Zend_Db_Table $model
     */
    public function getModel (){
       return $this->_model;
    }

     /**
     * @param string  $perfil
     */
    public function setPerfil($perfil){
        $this->_perfil = $perfil;
    }
    /**
     *
     * @return string $perfil
     */
    public function getPerfil (){
       return $this->_perfil;
    }

     /**
     * @param Zend_Session_Namespace  $session
     */
    public function setSession(Zend_Session_Namespace $session){
        $this->_session = $session;
    }
    /**
     *
     * @return Zend_Session_Namespace $session
     */
    public function getSession (){
       return $this->_session;
    }

     /**
     * @param string  $url
     */
    public function setUrl($url){
        $this->_url = $url;
    }
     /**
     *
     * @return string $url
     */
    public function getUrl (){
       return $this->_url;
    }

     /**
     * @param string  $url
     */
    public function setUrlBase($url){
        $this->_urlBase = $url;
    }
     /**
     *
     * @return string $url
     */
    public function getUrlBase (){
       return $this->_urlBase;
    }
    
    /**
     * Set as mensagens
     *  
     * @param string $mensagens  Valor dos índices [editado][adicionado][excluido][confirma_exclusao]
     */
    public function setMensagens($mensagens){
        
        $this->_mensagens = $mensagens;        
        
    }
     /**
     * Retorna a mensagem correspondente ao parametro passado
     * 
     * @return string $param  Valor do parâmetro [editado][adicionado][excluido][confirma_exclusao]
     */
    public function getMensagens($param){
        
        return $this->_mensagens[$param];
    }

}

