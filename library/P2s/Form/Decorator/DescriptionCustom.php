
<?php
/**
 * Classe Decorater para exibir descrições dos inputs
 * @filesource
 * @author Paulo Soares da Silva
 * @copyright P2S System - Soluções Web
 * @package SysWeb
 * @subpackage P2s.Form.Decorator
 * @version 1.0
 */
class P2s_Form_Decorator_DescriptionCustom extends Zend_Form_Decorator_Abstract
{

    public function getdescriptionCustom(){

          $element = $this->getElement();
          $description = $element->getDescription();
          
          if (empty($description)) {
            return '';
          }
          $content = '<span class="description">';
          $content .='<img src="'.BASE_URL.'/public/templates/system/imagens/informacao.png" border="0" ';
          $content .=' title="'.$description.'"/>';
          $content .= '</span>';
          
          
          return $content;
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

        $description = $this->getdescriptionCustom();
        if (empty($description)) {
            return $content;
        }

        $separator = $this->getSeparator();
        $placement = $this->getPlacement();


        switch ($placement) {
            case self::APPEND:
                return $content . $separator . $description;
            case self::PREPEND:
                return $description . $separator . $content;
        }
    }

}


