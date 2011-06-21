<?php


/**
 * @see Zend_Validate_Abstract
 */
require_once 'Zend/Validate/Abstract.php';


class P2s_Validate_Cpf extends Zend_Validate_Abstract
{
    const CPF_INVALID  = 'cpf_invalid';


   
    protected $_messageTemplates = array(
        self::CPF_INVALID   => "'%value%' não é um número de CPF válido",
              
    );

    
    public function isValid($value)
    {
        
       $this->_setValue((string) $value);


       	//Retirar todos os caracteres que nao sejam 0-9
	$s="";
	for ($x=1; $x<=strlen($value); $x=$x+1)
	{
		$ch=substr($value,$x-1,1);
		if (ord($ch)>=48 && ord($ch)<=57)
		{
			$s=$s.$ch;
		}
	}

	$value=$s;
	if ($value=="00000000000" || $value=="11111111111" || $value=="22222222222" || $value=="33333333333" || $value=="44444444444" ||
            $value=="55555555555" || $value=="66666666666" || $value=="77777777777" || $value=="88888888883" || $value=="99999999999")
	{
		$then;
                $this->_error(self::CPF_INVALID);
		return false;
	}else{
		$Numero[1]=intval(substr($value,1-1,1));
		$Numero[2]=intval(substr($value,2-1,1));
		$Numero[3]=intval(substr($value,3-1,1));
		$Numero[4]=intval(substr($value,4-1,1));
		$Numero[5]=intval(substr($value,5-1,1));
		$Numero[6]=intval(substr($value,6-1,1));
		$Numero[7]=intval(substr($value,7-1,1));
		$Numero[8]=intval(substr($value,8-1,1));
		$Numero[9]=intval(substr($value,9-1,1));
		$Numero[10]=intval(substr($value,10-1,1));
		$Numero[11]=intval(substr($value,11-1,1));

		$soma=10*$Numero[1]+9*$Numero[2]+8*$Numero[3]+7*$Numero[4]+6*$Numero[5]+5*
		$Numero[6]+4*$Numero[7]+3*$Numero[8]+2*$Numero[9];
		$soma=$soma-(11*(intval($soma/11)));

		if ($soma==0 || $soma==1)
		{
			$resultado1=0;
		}
		else
		{
			$resultado1=11-$soma;
		}

		if ($resultado1==$Numero[10])
		{
			$soma=$Numero[1]*11+$Numero[2]*10+$Numero[3]*9+$Numero[4]*8+$Numero[5]*7+$Numero[6]*6+$Numero[7]*5+
			$Numero[8]*4+$Numero[9]*3+$Numero[10]*2;
			$soma=$soma-(11*(intval($soma/11)));

			if ($soma==0 || $soma==1)
			{
				$resultado2=0;
			}else{
				$resultado2=11-$soma;
			}

			if ($resultado2==$Numero[11])
			{
				return true;
			}else{
                                $this->_error(self::CPF_INVALID);
				return false;
			}
		}else{
                        $this->_error(self::CPF_INVALID);
			return false;
		}
	}

     }
}
