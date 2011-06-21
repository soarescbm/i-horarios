<?php
/**
 *
 * Helper de informação de data
 *
 * @filesource
 * @author Paulo Soares da Silva
 * @copyright P2S  Pystem Soluções Web - 2010
 * @package SysWeb
 * @subpackage view.helpers
 * @version 1.0
 */
class Zend_View_Helper_InfoData  {
	
    
    
    
    public function infoData(){
    	  
    	  $dia = date("l"); 

        //Depois verifica-se em que dia estamos, e coloca-se o dia correspondente em portugu&ecirc;s, pois o retorno da fun&ccedil;&atilde;o date("l") vem em Ingl&ecirc;s e n&atilde;o em portugu&ecirc;s 

        switch($dia)
            {
            case "Monday":
            $dia_port = "Segunda-Feira";
            break; 
            case "Tuesday":
            $dia_port = "Ter&ccedil;a-Feira";
            break; 
            case "Wednesday":
            $dia_port = "Quarta-Feira";
            break; 
            case "Thursday":
            $dia_port = "Quinta-Feira";
            break; 
            case "Friday":
            $dia_port = "Sexta-Feira";
            break; 
            case "Saturday":
            $dia_port = "S&aacute;bado";
            break; 
            case "Sunday":
            $dia_port = "Domingo";
            break; 
            }


            //Vai-se buscar o mes em que estamos 
            $mes = date("n"); 

            // E &eacute; necess&aacute;rio verificar tamb&eacute;m o m&ecirc;s em portugu&ecirc;s e fazer a respectiva atribui&ccedil;&atilde;o 
            switch($mes) 
            {
            case "1":
            $mes_port = "Janeiro";
            break; 
            case "2":
            $mes_port = "Fevereiro";
            break; 
            case "3":
            $mes_port = "Mar&ccedil;o";
            break; 
            case "4":
            $mes_port = "Abril";
            break; 
            case "5":
            $mes_port = "Maio";
            break; 
            case "6":
            $mes_port = "Junho";
            break; 
            case "7":
            $mes_port = "Julho";
            break; 
            case "8":
            $mes_port = "Agosto";
            break; 
            case "9":
            $mes_port = "Setembro";
            break; 
            case "10":
            $mes_port = "Outubro";
            break; 
            case "11":
            $mes_port = "Novembro";
            break; 
            case "12":
            $mes_port = "Dezembro";
            break; 
            } 

            //Depois de se ter o dia e o m&ecirc;s em portugu&ecirc;s &eacute; s&oacute; mostrar a data da forma que desejada. 
            $texto = $dia_port;
            $texto .=", ";
            $texto .= date("d");
            $texto .= " de ";
            $texto .= $mes_port;
            $texto .= " de ";
            $texto .= date("Y").".";
            
            
         
          return ($texto);
    }
}

