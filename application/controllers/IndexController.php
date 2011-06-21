<?php

/**
 * 
 * Controlador de autenticão
 * 
 * @filesource 
 * @author Paulo Soares da Silva 
 * @copyright P2S - Soluções Web
 * @package SysWeb 
 * @subpackage Default.Controller
 * @version 1.0
 */
class IndexController extends P2s_Controller_Abstract
{ 
   
   public function init()
    {
        $this->setTituloPagina('Painel Principal');
        $this->setTituloPaginaClass('_cpanel');
                       
    }

   public function indexAction() 
    {    
        $this->viewAssign();
    }

    public function  viewAssign() {
        $this->view->titulo = $this->getTituloPagina('titulo');
        $this->view->tituloClass = $this->getTituloPagina('class');
        $this->view->url = $this->getUrl();
        $this->view->urlBase  = $this->getUrlBase();
    }
    
  
}









