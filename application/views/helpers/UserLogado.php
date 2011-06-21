<?php
/**
 *
 * Helper de infomação de autenticação do usuário
 *
 * @filesource
 * @author Paulo Soares da Silva
 * @copyright P2S  Pystem Soluções Web - 2010
 * @package SysWeb
 * @subpackage view.helpers
 * @version 1.0
 */
class Zend_View_Helper_UserLogado  {
    /**
     * Nome do usuário
     * @var string
     */
    public $user;
    
    
    public function userLogado(){

          Zend_Loader::loadClass('Logacesso');
    	  $dados_user = Zend_Auth::getInstance()->getIdentity();
          $this->user = $dados_user->nome;
          //pesquisa o último acesso
          $tb_log_acesso = new Logacesso();
          
          $where = "usuarios_id = '".$dados_user->id."'";
          //$tab_log_acesso->select()->where($where)->order('data_hora DESC')->limit(1);
          
          $resultado = $tb_log_acesso->fetchAll($where, 'data_hora DESC',2,0)->toArray();
          
          $texto = 'Olá, <strong>'. strtoupper( $this->user).'</strong>.<br/>';
          
          
          if (isset($resultado[1]['data_hora'])){
                $dh = explode(' ',$resultado[1]['data_hora']);
          
                $data = explode('-',$dh['0']);
                $hora = explode(':',$dh['1']);
          
                $data_br = $data['2'].'/'.$data['1'].'/'.$data['0'];
                $hora_br = $hora['0'].':'.$hora['1'];
          
          
                $texto .= 'Seu último acesso foi em '.$data_br.', às '.$hora_br.'.';
            
            
            
          }
         
          return ($texto);
    }
}

