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
class P2s_Form_Xhtml extends Zend_Form_Element_Xhtml
{
    public $helper = 'formNote';

    public function isValid($value, $context = null) {
        return true;
    }
}

