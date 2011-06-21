
<?php
/**
 * Classe Decorater para adicionar novos itens
 * @filesource
 * @author Paulo Soares da Silva
 * @copyright P2S System - Soluções Web
 * @package SysWeb
 * @subpackage P2s.Form.Decorator
 * @version 1.0
 */
class P2s_Form_Decorator_AddItens extends Zend_Form_Decorator_Abstract
{

    public function buidAddItens(){

          $element = $this->getElement();
          $add = $element->getAddItens();
          if (empty($add)) {
            return '';
         }
            return $add;
    }
   /**
     * Render errors
     *
     * @param  string $content
     * @return string
     */
    public function render($content)
    {
        $element = $this->getElement();
        $view    = $element->getView();
        if (null === $view) {
            return $content;
        }

        $addItens = $this->buidAddItens();
        if (empty($addItens)) {
            return $content;
        }

        $separator = $this->getSeparator();
        $placement = $this->getPlacement();


        switch ($placement) {
            case self::APPEND:
                return $content . $separator . $addItens;
            case self::PREPEND:
                return $addItens . $separator . $content;
        }
    }

}


