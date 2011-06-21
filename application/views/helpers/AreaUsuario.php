<?php
/**
 *
 * Helper de informação do perfil do usuário
 *
 * @filesource
 * @author Paulo Soares da Silva
 * @copyright P2S  Pystem Soluções Web - 2010
 * @package SysWeb
 * @subpackage views.helpers
 * @version 1.0
 */
class Zend_View_Helper_AreaUsuario {
	
    public function areaUsuario(){
          
          $dados_user = Zend_Auth::getInstance()->getIdentity();
          $tipo_user =  Zend_Db_Table::getDefaultAdapter()->fetchOne(
                        'SELECT nome FROM perfis WHERE id = '.$dados_user->perfis_id);
    	  $texto = "Área do ". ucwords(str_replace('_', ' ', $tipo_user)) ;
          return ($texto);
    }
}

