<?php

/**
 * 
 * Controlador de autenticão
 * 
 * @author Paulo Soares da Silva
 * @copyright P2S System - Soluções Web
 * @package Sysweb
 * @subpackage Default.Controller
 * @version 1.0
 */
class LoginController extends Zend_Controller_Action
{ 
   
   public function init()
    {
       Zend_Loader::loadClass('Logacesso');
       $this->_helper->layout->setLayout('layout_login');
                     
    }

   public function indexAction() 
    {    
      $auth = Zend_Auth::getInstance();
      if($auth->hasIdentity()){
          $this->_redirect('/');
      }

      if($this->_request->isPost()){

            $post = Zend_Registry::get('post');
    	    $user= $post->user_nome;
    	    $pass= $post->user_senha;
    		
    	     if(empty($user)){

    	     	$this->view->erro_login = 'Informe o nome de usuário';
    	     	
    	     }
    	     
    	     else{
    	     	
    	    	$db = Zend_Db_Table::getDefaultAdapter();
    	     	$autent= new Zend_Auth_Adapter_DbTable($db,'usuarios','nome_usuario','senha','md5(?) AND ativo != "0"');
    	     	  
    	     	$autent->setIdentity($user)
    	               ->setCredential($pass);
    	     	$resultado = $autent->authenticate();
    	     	
    	     	if($resultado->isValid()){
    	     		
    	     		$auth = Zend_Auth::getInstance();
    	     		$dados = $autent->getResultRowObject(NULL,'senha');
                        $auth->getStorage()->write($dados);

                        //grava o nome do perfil na session Zend_Auth
                        $this->setPerfil($auth->getIdentity()->perfis_id);
                           	     	                   
                        //grava dados do acesso
                        $this->_gravaLogin();
                        //grava session acl
                        $this->_sessionAcl();

                                               
                        $this->_redirect('/');

    	     	       	     	    
    	     	}
    	     	else {
    	     		$this->view->erro_login = 'Usuário ou senha inválidos.';
    	     	}
    	     	
    	     	
    	     }
    	
       		
    	}
    
    	
    }
    
    public function logoffAction(){
    	
    	$auth = Zend_Auth::getInstance();
    	$auth->clearIdentity();
    	$this->_redirect('/');
    }
   
   
    

    private function _gravaLogin(){

        $tb_log_acesso = new Logacesso();

        $auth =  Zend_Auth::getInstance();
        $identidade = $auth->getIdentity();

        $dados['usuarios_id'] = $identidade->id;
        $dados['ip'] = $ip = getenv('REMOTE_ADDR');

        $tb_log_acesso->insert($dados);

    }

    private function _sessionAcl(){

       $session = new Zend_Session_Namespace('Zend_Acl');
       $acl = new P2s_Acl_Acl();

       //Grava o objeto $acl em um session
       $session->acl = $acl;

    }

    private function setPerfil($perfil_id){

        $perfil = strtolower(Zend_Db_Table::getDefaultAdapter()->fetchOne(
                        'SELECT nome FROM perfis WHERE id = '.$perfil_id));

        $sessionAuth = new Zend_Session_Namespace('Zend_Auth');
        $sessionAuth->perfil = $perfil;
        $sessionAuth->sistema = SISTEMA;
    }
}
