<?php
/**
 * Controlador de erros do sistema
 * @author Paulo Soares da Silva
 * @copyright P2S System - Soluções Web
 * @package Sysweb
 * @subpackage Default.Controller
 * @version 1.0
 */
class ErrorController extends Zend_Controller_Action
{

    public function errorAction()
    {
        
       
               
        $errors = $this->_getParam('error_handler');
        
        switch ($errors->type) { 
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
           
        
                // 404 error -- controller or action not found
                //$this->getResponse()->setHttpResponseCode(404);
                $this->view->message = 'Página não encontrada!';
                break;
            default:
                // application error 
                //$this->getResponse()->setHttpResponseCode(500);
                $this->view->message = 'Operação não realizada!' ;
                break;
        }
        
        $this->view->exception = $errors->exception;
        $this->view->request   = $errors->request;
    }

    public function errorpermissaoAction()
    {
        
        $this->view->message   = 'Caro usuário, você não tem permissão para acessar essa área do sistema';
    }

}

