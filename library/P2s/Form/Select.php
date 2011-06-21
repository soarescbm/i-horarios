<?php
/**
 * Classe para criação de elementos customizados no formulário
 *
 * @filesource
 * @author Paulo Soares da Silva
 * @copyright P2S System - Soluções Web
 * @package SysWeb
 * @subpackage P2s.Form
 * @version 1.0
 */
class P2s_Form_Select extends Zend_Form_Element_Select
{
    protected $_addItens;
    public function setAddItens ($url, $field ='nome', $class = 'add'){

       $element = $this->getName();
       $content = '<span class="addItens">';
       $content .= '<a href="'.BASE_URL.$url.'" class="'.$class.'"  id="'.$element.'" alt="'.$field.'" >';
       $content .='<img src="'.BASE_URL.'/public/templates/system/imagens/adicionar.png" border="0"/>';
       $content .= '</a></span>';

       $this->_addItens = $content;

    }
    public function getAddItens(){
        return $this->_addItens;
    }
}

