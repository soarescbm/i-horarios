<?php

/**
 * @see Zend_Acl
 */
require_once 'Zend/Acl.php';
/**
 * @see Zend_Acl_Resource
 */
require_once 'Zend/Acl/Resource.php';
/**
 * @see Zend_Acl_Role
 */
require_once 'Zend/Acl/Role.php';
/**
 * @see Zend_Db_Table
 */
require_once 'Zend/Db/Table.php';

/**
   * Lista de Controle de Acesso
   *
   * Adicionar as permissões de acesso ao aplicacoes/funcionalidades e acões do
   * sistema.
   *
   * @author Paulo Soares da Silva
   * @copyright P2S System - Soluções Web
   * @package Sysweb
   * @subpackage P2s.Acl
   * @version 1.0
   */
class P2s_Acl_Acl extends Zend_Acl {
	/**
         * Construtor do objeto
         */
	public function __construct(){
            //Recebe a conexão do bando de dados
            $db = Zend_Db_Table::getDefaultAdapter();
            
            $perfis = $this->getPerfis($db);
            $aplicacoes = $this->getAplicacoes($db);
            $permissoes = $this->getPermissoes($db);

            $this->setPerfis($perfis);
            $this->setAplicacoes($aplicacoes);
            $this->setPermissoes($permissoes);
	
	}

        /**
         * Adiciona os perfis do sistema a lista de controle de acesso
         * @param Zend_Db $perfis
         */
        protected  function setPerfis($perfis){
            //Adicionando perfis

            foreach($perfis as $p)
                {
                                           
                      $perfil = new Zend_Acl_Role(strtolower($p['nome']));
                                           
                      if($p['herda_nome'] !== null){
                              $this->addRole($perfil, strtolower($p['herda_nome']));
                                                     }
                       else {
                             $this->addRole($perfil);
                       }

                }

               
        }
        /**
         * Adiciona os aplicacoes/controladores do sistema a lista de controle de
         * acesso.
         * @param Zend_Db $aplicacoes
         */
        protected function setAplicacoes($aplicacoes){
             //Adicionando aplicacoes
            foreach( $aplicacoes as $r)
                {
                        $aplicacao = new Zend_Acl_Resource(strtolower($r['nome']));
                        $this->add($aplicacao);

                }

        }
        /**
         * Adiciona as permissões a lista de controle de acesso
         * @param Zend_Db $permissoes
         */
        protected function setPermissoes ($permissoes){

            //Adicionando permissões
            $acoes = array();
            foreach( $permissoes as $p)
                {
                        if(!empty($p['acoes'])){
                             $acoes = explode(', ', $p['acoes']);
                        }else {
                             $acoes = null;
                        }

                        $this->allow(strtolower($p['perfil']), strtolower($p['aplicacao']), $acoes );


                       
                }
        }
        /**
         * Consulta dos perfis
         * @param Zend_Db $db
         * @return Zend_Db
         */
        protected function getPerfis( $db){

             $sql = 'SELECT a.nome,
                            b.nome as herda_nome
                        FROM perfis a
                        LEFT JOIN perfis b
                        ON a.herda_id = b.id
                        ORDER BY a.nivel ASC';


     
             return $perfis =  $db->fetchAll($sql);
            
        }
        /**
         * Consulta dos aplicacoes
         * @param Zend_Db $db
         * @return Zend_Db
         */
        protected function getAplicacoes(  $db){

             $sql = 'SELECT DISTINCT nome
                    FROM aplicacoes';

             return $aplicacoes = $db->fetchAll($sql);

        }
        /**
         * Consulta das permissoes
         * @param Zend_Db $db
         * @return Zend_Db
         */
        protected function getPermissoes( $db){

             $sql = 'SELECT * FROM permissoes';
                         
             return $permissoes = $db->fetchAll($sql) ;

        }
	
	
}
