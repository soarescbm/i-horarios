<?php

/**
 * Controlador dos usuários do sistema
 *
 * @filesource
 * @author Paulo Soares da Silva
 * @copyright P2S System - Soluções Web
 * @package SysWeb
 * @subpackage Default.Controller
 * @version 1.0
 */
class UsuariosController extends P2s_Controller_Abstract {

   

    public function init(){
        parent::init();
        Zend_Loader::loadClass('Usuarios');
        Zend_Loader::loadClass('Perfis');
       
        $this->setModel( new Usuarios());
        $this->setTituloPagina('Usuários');
        $this->setTituloPaginaClass('_user');
        $this->setInfoForm('Dados do Usuário');
        $this->setItemNome('Usuário');
        $this->setHtmlColunasTabela(array(
            'th'=>array('Nome Completo'=>array( 'align' =>'left', 'width'=>'200' ),
                        'Nome de Usuário'=>array( 'align' =>'left', 'width'=>'100' ),
                        'Tipo de Perfil'=>array( 'align' =>'left', 'width'=>'100' )
                )));


            
       
    }
    public function inserirAction(){
        //Recebe parametros do formulario a instanciar;
        if($this->_request->getParam('form')){
           $f = $this->_request->getParam('form');
        }else{
           $f = 1;
        }

        switch ($f){
            //Formulario de inserção
            case '1' : $form = $this->getForm();
                break;
            //Formulário de Edição
            case '2' : $form = $this->getFormEdit();
                break;
            //Formulário de Edição de Senha
            case '3' : $form = $this->getFormSenha();
                break;
            default : $form = $this->getForm();
          
        }


        if($this->_request->isPost()){

            if ($form->isValid($_POST)){
                 $id = $form->getValue('id');
                 if(empty($id)){
                         //consulta a existência do mesmo login
                         $where = $this->getModel()->getAdapter()->quoteInto('nome_usuario = ?',
                         $form->getValue('nome_usuario'));
                         $exite_login = $this->getModel()->fetchRow($where);
                         //Faz a comparação entre a senha e a repetição da senha
                          if(strcasecmp($form->getValue('senha'),$form->getValue('senha2'))!= 0){
                             $form->senha2->addError('As senhas  digitadas são diferentes.');
                             $form->populate($_POST);
                          }
                          //Verifica se já existe o mesmo login
                          elseif ($exite_login != null) {
                             $form->nome_usuario->addError('O nome de usuário "'.$form->getValue('nome_usuario').
                                  '" já existe.');
                             $form->populate($_POST);
                          }
                          else{
                             //Remove o elemento senha2, usado para verificação de senha
                             $form->removeElement('senha2');
                             //Encriptação da senha com MD5
                             $form->senha->setValue(md5($form->getValue('senha')));

                             //Inserção
                             $this->getModel()->insert($form->getValues());
                             $this->getSession()->mensagem = $this->getItemNome()." adicionado com sucesso!";
                             $this->_redirect($this->getController().'/listar');

                        }

                     }
                     //Edição de Senha
                     elseif($f == 3){
                          //Faz a comparação entre a senha e a repetição da senha
                          if(strcasecmp($form->getValue('senha'),$form->getValue('senha2'))!= 0){
                             $form->senha2->addError('As senhas  digitadas são diferentes.');
                             $form->populate($_POST);
                          }else{
                             //Remove o elemento senha2, usado para verificação de senha
                             $form->removeElement('senha2');
                             //Encriptação da senha com MD5
                             $form->senha->setValue(md5($form->getValue('senha')));
                             //Edição
                                $where = $this->getModel()->getAdapter()->quoteInto('id = ?',$id);
                                $this->getModel()->update($form->getValues(),$where);
                                $this->getSession()->mensagem = $this->getItemNome()." editado com sucesso!";
                                $this->_redirect($this->getController().'/listar');
                          }
                     }
                     else{
                             //consulta a existência do mesmo login
                             $where = "nome_usuario ='".$form->getValue('nome_usuario')."' AND id <>".$id;
                            
                             $exite_login = $this->getModel()->fetchRow($where);

                             if ($exite_login != null) {
                                 $form->nome_usuario->addError('O nome de usuário "'.$form->getValue('nome_usuario').
                                  '" já existe.');
                                 $form->populate($_POST);
                             }
                             else{
                                //Edição
                                $where = $this->getModel()->getAdapter()->quoteInto('id = ?',$id);
                                $this->getModel()->update($form->getValues(),$where);
                                $this->getSession()->mensagem = $this->getItemNome()." editado com sucesso!";
                                $this->_redirect($this->getController().'/listar');
                             }


                    }
              

            } else {

                $form->populate($_POST);

            }
        }
        $this->view->form = $form;
        $this->viewAssign();
        $this->render('inserir', null, true);
        }
    

    public function  getForm() {
        $form = parent::getForm();
        $form->setAction($this->getUrl().'/inserir/form/1');
        //Atributo id
        $id = new Zend_Form_Element_Hidden('id');

             
               
        //Atributo nome do usuário
        $nome = new Zend_Form_Element_Text('nome');
        $nome->setLabel('Nome Completo:');
        $nome->setRequired(true);
        $nome->setAttrib('class', 'input');
        $nome->setAttrib('size', '60');
        $nome->addFilter('StringTrim');
        $nome->addFilter('Alpha',array ('allowwhitespace' => true));
        $nome->addValidator('StringLength', true, array('4','100'));
        //$nome->addValidator('Alpha');

        //Atributo nome de usuário
        $nome_usuario = new Zend_Form_Element_Text('nome_usuario');
        $nome_usuario->setLabel('Nome de Usuário:');
        $nome_usuario->setRequired(true);
        $nome_usuario->setAttrib('class', 'input');
        $nome_usuario->setAttrib('size', '40');
        $nome_usuario->addFilter('StringTrim');
        //$nome->addFilter('Alpha');
        $nome_usuario->addValidator('StringLength', true, array('4','100'));
        //$nome->addValidator('Alpha');

        //Atributo perfil
       $perfil = new Zend_Form_Element_Select('perfis_id');
       $perfil->setLabel('Perfil:');
       $perfil->setRequired(true);
       $perfil->addMultiOptions(array(''=>''));
       //consulta dos options
       $tb_perfis = new Perfis();
       $user_perfil = Zend_Registry::get('Perfil');
       //consulta perfil 
       $c_perfil = $tb_perfis->fetchRow("nome='".$user_perfil."'");
       //restrige os perfis de acordo com o nível do usuário
       $resultado = $tb_perfis->fetchAll("nivel <=".$c_perfil->nivel,'nivel')->toArray();
       $options = array();
       foreach ($resultado as $linha ){
              $options[$linha['id']] = $linha['nome'];
       }

       $perfil->addMultiOptions($options);

        //Atributo senha
        $senha1 = new Zend_Form_Element_Password('senha');
        $senha1->setRequired(true);
        $senha1->setLabel('Senha:');
        $senha1->setAttrib('class', 'input');
        $senha1->setAttrib('size', '30');
        $senha1->setAttrib('autocomplete', 'off');
        $senha1->addFilter('StringTrim');
        //$nome->addFilter('Alpha');
        $senha1->addValidator('StringLength', true, array('6','30'));
        //$nome->addValidator('Alpha');

        //Atributo senha
        $senha2 = new Zend_Form_Element_Password('senha2');
        $senha2->setRequired(true);
        $senha2->setLabel('Repita a Senha:');
        $senha2->setAttrib('class', 'input');
        $senha2->setAttrib('size', '30');
        $senha2->setAttrib('autocomplete', 'off');
        $senha2->addFilter('StringTrim');
        //$nome->addFilter('Alpha');
        $senha2->addValidator('StringLength', true, array('6','30'));
        //$nome->addValidator('Alpha');

     

       //Atributo situação
       $ativo = new Zend_Form_Element_Radio('ativo');
       $ativo->setValue('1');
       $ativo->setLabel('Situação:');
       $ativo->addMultiOptions(array('1'=>'Ativo', '0'=>'Não Ativo'));
       

       
       
       
        $form->addElements(array($id, $nome,$nome_usuario,$perfil,$senha1,$senha2,$ativo));
        $form->setElementDecorators($this->getElementDecorators());
        $id->setDecorators($this->getElementHiddenDecorators());
        $ativo->setDecorators($this->getElementOptionsDecorators());

        return $form;
    }
    public function  getFormEdit() {
        $form = parent::getForm();
        $form->setAction($this->getUrl().'/inserir/form/2');

        //Atributo id
        $id = new Zend_Form_Element_Hidden('id');

       
        
        //Atributo nome do usuário
        $nome = new Zend_Form_Element_Text('nome');
        $nome->setLabel('Nome:');
        $nome->setRequired(true);
        $nome->setAttrib('class', 'input');
        $nome->setAttrib('size', '60');
        $nome->addFilter('StringTrim');
        $nome->addFilter('Alpha',array ('allowwhitespace' => true));
        $nome->addValidator('StringLength', true, array('4','100'));
        //$nome->addValidator('Alpha');

        //Atributo nome de usuário
        $nome_usuario = new Zend_Form_Element_Text('nome_usuario');
        $nome_usuario->setLabel('Nome de Usuário:');
        $nome_usuario->setRequired(true);
        $nome_usuario->setAttrib('class', 'input');
        $nome_usuario->setAttrib('size', '40');
        $nome_usuario->addFilter('StringTrim');
        //$nome->addFilter('Alpha');
        $nome_usuario->addValidator('StringLength', true, array('4','100'));
        //$nome->addValidator('Alpha');

        //Atributo perfil
       $perfil = new Zend_Form_Element_Select('perfis_id');
       $perfil->setLabel('Perfil:');
       $perfil->setRequired(true);
       $perfil->addMultiOptions(array(''=>''));
       //consuta dos options
       $tb_perfis = new Perfis();
       $user_perfil = Zend_Registry::get('Perfil');
       //consulta perfil 
       $c_perfil = $tb_perfis->fetchRow("nome='".$user_perfil."'");
       //restrige os perfis de acordo com o nível do usuário
       $resultado = $tb_perfis->fetchAll("nivel <=".$c_perfil->nivel,'nivel')->toArray();
       $options = array();
       foreach ($resultado as $linha ){
              $options[$linha['id']] = $linha['nome'];
       }

       $perfil->addMultiOptions($options);



       //Atributo situação
       $ativo = new Zend_Form_Element_Radio('ativo');
       $ativo->setValue('1');
       $ativo->setLabel('Situação:');
       $ativo->addMultiOptions(array('1'=>'Ativo', '0'=>'Não Ativo'));





        $form->addElements(array($id, $nome,$nome_usuario,$perfil,$ativo));
        $form->setElementDecorators($this->getElementDecorators());
        $id->setDecorators($this->getElementHiddenDecorators());
        $ativo->setDecorators($this->getElementOptionsDecorators());

        return $form;
    }
    public function  getFormSenha() {
        $form = parent::getForm();
        $form->setAction($this->getUrl().'/inserir/form/3');
        //Atributo id
        $id = new Zend_Form_Element_Hidden('id');

       
        //Atributo nome do usuário
        $nome = new Zend_Form_Element_Text('nome');
        $nome->setLabel('Usuário:');
        $nome->setAttrib('class', 'input');
        $nome->setAttrib('size', '40');
        $nome->setAttrib('readonly', 'readonly');
        $nome->addFilter('StringTrim');
        //$nome->addFilter('Alpha');
        $nome->addValidator('StringLength', true, array('4','100'));
        //$nome->addValidator('Alpha');

        //Atributo senha
        $senha1 = new Zend_Form_Element_Password('senha');
        $senha1->setRequired(true);
        $senha1->setLabel('Senha:');
        $senha1->setAttrib('class', 'input');
        $senha1->setAttrib('size', '20');
        $senha1->setAttrib('autocomplete', 'off');
        $senha1->addFilter('StringTrim');
        //$nome->addFilter('Alpha');
        $senha1->addValidator('StringLength', true, array('6','30'));
        //$nome->addValidator('Alpha');

        //Atributo senha
        $senha2 = new Zend_Form_Element_Password('senha2');
        $senha2->setRequired(true);
        $senha2->setLabel('Repita a Senha:');
        $senha2->setAttrib('class', 'input');
        $senha2->setAttrib('size', '20');
        $senha2->setAttrib('autocomplete', 'off');
        $senha2->addFilter('StringTrim');
        //$nome->addFilter('Alpha');
        $senha2->addValidator('StringLength', true, array('6','30'));
        //$nome->addValidator('Alpha');



        $form->addElements(array($id,$nome,$senha1,$senha2));
        $form->setElementDecorators($this->getElementDecorators());
        $id->setDecorators($this->getElementHiddenDecorators());
       

        return $form;
    }
    public function  editarAction(){

        if($this->_request->isGet()){
                     
                 $filter = new Zend_Filter_Digits();
                 $id = $filter->filter($this->_request->getParam('id'));
                 //Não permite alterar o usuario ativo;
                 $id_ativo = Zend_Registry::get('Id');
                 $resultado = $this->getModel()->find($id);

                
                                  
                 if($resultado == null){

                      $this->getSession()->mensagem = $this->getItemNome()." não encontrado!";
                      $this->_redirect($this->getController().'/listar');
                 }

               
                 
                //Se parametro 's' igual a 1: formulário de mudança de senha
                if($this->_request->getParam('s')==1){
                     $form = $this->getFormSenha();
                }else{
                     $form = $this->getFormEdit();
                }
                
                $form->populate($resultado);

                $this->view->form = $form;
                $this->viewAssign();
                $this->render('inserir', null, true);
         }
           
        
         else{
            $this->_redirect($this->getController().'/listar');
         }

    }
    public function  excluirAction(){

        if($this->_request->isGet()){
            $filter = new Zend_Filter_Digits();
            $id = $filter->filter($this->_request->getParam('id'));

            //Não permite excluir o usuario ativo;
            $id_ativo = Zend_Registry::get('Id');
            $resultado = $this->getModel()->find($id);
            
            if($resultado == null){

                 $this->getSession()->mensagem = $this->getItemNome()." não encontrado!";
                 $this->_redirect($this->getController().'/listar');
            }
            elseif($id == $id_ativo){
                 $this->getSession()->mensagem = "Não é permitido excluir você mesmo!";
                 $this->_redirect($this->getController().'/listar');
            }
             
            $where = $this->getModel()->getAdapter()->quoteInto('id = ?',$id);
            $this->getModel()->delete($where);
            $this->getSession()->mensagem = $this->getItemNome()." excluido com sucesso!";

         }
         $this->_redirect($this->getController().'/listar');

    }
    public function  getFormSearch() {
        
        $form = new Zend_Form();
        
            
        $form->setAction($this->getUrl().'/listar')
             ->setMethod('post')
             ->setName('search')
             ->setAttrib('class', 'form01')
             ->setDecorators($this->getFormDecorators())
             ->addPrefixPath('P2s_Form_Decorator', 'P2s/Form/Decorator/', 'decorator');
        
        //Atributo nome do usuário
        $id = new Zend_Form_Element_Text('id');
        $id->setLabel('Código Id:')
                ->setAttrib('class', 'input')
                ->setAttrib('size', '6')
                ->addFilter('StringTrim')
                ->addFilter('Digits');
                
        
        //Atributo nome do usuário
        $nome = new Zend_Form_Element_Text('nome');
        $nome->setLabel('Nome:')
                ->setAttrib('class', 'input')
                ->setAttrib('size', '40')
                ->addFilter('StringTrim')
                ->addFilter('Alpha',array ('allowwhitespace' => true));
      
        
        
        
        //Atributo perfil
        $perfil = new Zend_Form_Element_Select('perfis_id');
        $perfil->setLabel('Perfil:')
                ->addMultiOptions(array(''=>''));
       //consulta dos options
       $tb_perfis = new Perfis();
       $user_perfil = Zend_Registry::get('Perfil');
       //consulta perfil 
       $c_perfil = $tb_perfis->fetchRow("nome='".$user_perfil."'");
       //restrige os perfis de acordo com o nível do usuário
       $resultado = $tb_perfis->fetchAll("nivel <=".$c_perfil->nivel,'nivel')->toArray();
       $options = array();
       foreach ($resultado as $linha ){
              $options[$linha['id']] = $linha['nome'];
       }

       $perfil->addMultiOptions($options);


      
       
       $form->addElements(array($id, $nome, $perfil));
       $form->setElementDecorators($this->getElementDecorators());
       
       return $form;
         
        
    }
    public function  paramWhere(){

            $form = $this->getFormSearch();

            $form->populate($_POST);
            //Retorna os valores filtrados

            $perfil = $form->getValue('perfis_id');
            $nome = $form->getValue('nome');
            $id = $form->getValue('id');
            


            //inicialização da cláusula where
            $where = 'a.id > 0';

            if(!empty($perfil)){
                     $where .= ' AND perfis_id ='.$perfil;
            }

            if(!empty($nome)){
                     $where .= ' AND a.nome LIKE "%'.$nome.'%"';
            }

            if(!empty($id)){
                     $where .= ' AND a.id ='.$id;
            }
            

       return $where;
    }
    public function  addColuna(&$records, $indice, $id, $row) {

              if($this->getAcl()->has(strtolower($this->getController()))){

                if($this->getAcl()->isAllowed($this->getPerfil(),strtolower($this->getController()),'editar')){
                          $records[$indice]['Alterar Senha'] = '<a href="'.$this->getUrl().'/editar/s/1/'.$this->getModel()->getFieldKey().'/'.$id.'"  title="'.$this->getItemNome().' '. $id. '"><img src="'.$this->getUrlBase().'/public/templates/system/imagens/cad2.png" border="0"></a>';
                    }
              }
              if($row['ativo'] == 1){
                  $records[$indice]['Ativo'] = '<a  href="'.$this->getUrl().'/editarstatus/id/'.$id.'/ativo/0"  title="Editar item '.$id. '"><img src="'.$this->getUrlBase().'/public/templates/system/imagens/tick.png" border="0" title="Ativa"></a>';
              }else {
                  $records[$indice]['Ativo'] = '<a  href="'.$this->getUrl().'/editarstatus/id/'.$id.'/ativo/1"  title="Editar item '.$id. '"><img src="'.$this->getUrlBase().'/public/templates/system/imagens/cancelar.png"  heith= "5px" border="0" title="Não Ativa"></a>';
              }
    }
}
