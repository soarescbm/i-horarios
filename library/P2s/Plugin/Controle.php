<?php
require_once 'P2s/Acl/Acl.php';
require_once 'Zend/Controller/Plugin/Abstract.php';
require_once 'Zend/Controller/Action/Helper/Redirector.php';

   /**
   * Controle de Acesso ao sistema
   *
   * Verifica se o usuário está autenticado e tem permissão para acessar os recursos
   * do sistema.
   *
   * @author Paulo Soares da Silva
   * @copyright P2S System - Soluções Web
   * @package P2S
   * @subpackage P2s.Plugin
   * @version 1.0
   */
class P2s_Plugin_Controle extends Zend_Controller_Plugin_Abstract {

    private $_auth;
    private $_acl;
    private $_urlLogin = array();
    private $_urlNoAcesso = array();
    private $_perfil;
    private $_tempoSessao;
    private $_sistema;


    public function __construct(){
	    	
        $this->_auth= Zend_Auth::getInstance();
        //Tempo de Duração da sessão;
        $this->setTempoSessao(TEMPO_SESSAO);

        //Direciona para o login
        $this->_urlLogin['controller'] = 'login';
        $this->_urlLogin['action'] = '';

        //Direciona para página de permissão negada
        $this->_urlNoAcesso['controller'] = 'error';
        $this->_urlNoAcesso['action'] = 'errorpermissao';

        

        }

    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
         $controller = strtolower($request->controller);
         $action = strtolower($request->action);

         $flag = false;
    	 //verifica se o usuário da autenticado
    	 if($this->_auth->hasIdentity()){
    	 	//Caso tenha, pega dados do usuario
    	 	$identidade = $this->_auth->getIdentity();
    	 	
                $this->setPerfil();
                $this->setAcl();
                $this->setRegistry($identidade->id);

                //veficação do sistema
                if(strcmp($this->getSistema(), SISTEMA) !=  0){
                    $controller = $this->_urlLogin['controller'];
                    $action  = $this->_urlLogin['action'];
                    $flag = true;
                    $this->_auth->clearIdentity();
                }
               else {
                   if($this->getAcl()->has($controller)){

                        if (!$this->getAcl()->isAllowed($this->getPerfil(), $controller, $action )) {

                             $controller = $this->_urlNoAcesso['controller'];
                             $action  = $this->_urlNoAcesso['action'];
                             $flag = true;
                       
                     }
                    }
               }
    	 }
    	 else{
                
                if ($controller != $this->_urlLogin['controller'])   {

                    $controller = $this->_urlLogin['controller'];
                    $action  = $this->_urlLogin['action'];
                    $flag = true;
                   
                   
                 }
    	 }

         if($flag){

                $this->setUrl($request,$controller, $action);
         }
       
    }

        private function setPerfil()
        {
            $sessionAuth = new Zend_Session_Namespace('Zend_Auth');
            $sessionAuth->setExpirationSeconds($this->getTempoSessao());
            $this->_perfil = $sessionAuth->perfil;
            $this->_sistema = $sessionAuth->sistema;

        }
        private function getPerfil()
        {

            return $this->_perfil;

        }
        private function setAcl()
        {
            $sessionAcl = new Zend_Session_Namespace('Zend_Acl');
            $sessionAcl->setExpirationSeconds($this->getTempoSessao());
            $this->_acl = $sessionAcl->acl;

        }
        private function getAcl()
        {

            return $this->_acl;

        }
        private function getSistema()
        {
            return $this->_sistema;
        }

        private function setRegistry($id){

                Zend_Registry::set('Acl', $this->getAcl());
                Zend_Registry::set('Perfil', $this->getPerfil());
                Zend_Registry::set('Id', $id);
        }
        private function setUrl(&$request,$controller,$action){

              $request->setControllerName($controller);
              $request->setActionName($action );
        }

       
        private function setTempoSessao($tempo){

            $this->_tempoSessao = $tempo;
        }
        private function getTempoSessao(){

            return $this->_tempoSessao;
        }
}