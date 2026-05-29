<?php
/*
 * @license AGPL-3.0
 * 
 * @copyright Copyright (c) 2026 EFA, Ecole française d'athènes, EFAthenes.
 *
 * @author Louis Mulot <louis.mulot@efa.gr>
 * 
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program. If not, see <http://www.gnu.org/licenses/>
 * 
 */
declare(strict_types=1);
class KFieldDouble extends KField
{
    public static string $MIN_VALUE="min_value";
    public static string $MAX_VALUE="max_value";
    public static string $DEFAULT_VALUE="default_value";
    public static string $DIGITS_PRECISION="digits_precision";
    public static int $MAX_VALUE_DIGITS=15;
    
    private ?float $min_value=null;
    private ?float $max_value=null;
    private ?float $default_value=null;
    private ?int $digits_precision=null;   
    private int $round_mode=PHP_ROUND_HALF_UP;
    
    /*
PHP_ROUND_HALF_UP 	Round val up to precision decimal places away from zero, when it is half way there. Making 1.5 into 2 and -1.5 into -2.
PHP_ROUND_HALF_DOWN 	Round val down to precision decimal places towards zero, when it is half way there. Making 1.5 into 1 and -1.5 into -1.
PHP_ROUND_HALF_EVEN 	Round val to precision decimal places towards the next even value.
PHP_ROUND_HALF_ODD 	Round val to precision decimal places towards the next odd value.
     */
    
    function __construct()
    {
        $this->setType(static::$DOUBLE);
        $this->setValueVar(0);
        $this->labelError="KFieldDouble::";
        $this->setDefault(0);
    }
    
    function affectValue(mixed $value,bool $defaultValue=false) : bool
    {
        if(isStringNull($value))
        {
            if($this->getIs_null())
            {
                $this->setVarValue(NULL,$defaultValue);
                return true;
            }
            else
            {
                $this->setKerror($this->labelError.$this->getStringMethodError($defaultValue)."[".$this->getName()."] => Value cannot be NULL ");
                return false;                
            }
        }   
        
        $valueConvert=try2ConvertToFloat($value);

//        $string="";
//        $string.="VALUE=".$value."// CONVERT=".$valueConvert."<br />";
//        $string.="Double=".is_double($valueConvert)."<br />";
//        $string.="INT=".is_int($valueConvert)."<br />";
//        $string.="IS NULL=".$this->getIs_null()."<br />";
//        $string.="NULL=".isStringNull($value)."<br />";
//        $string.="EVALUATE=".((!is_double($valueConvert)) && ($this->getIs_null() && !isStringNull($value)))."<br /><br />";
//        
//        $this->setKerror($string);        
        if(!is_null($valueConvert)&&$valueConvert==0)
        {
            $this->setVarValue(0,$defaultValue);
            return true;
        }
        
        if(filter_var($valueConvert, FILTER_VALIDATE_FLOAT)===false) // (!is_double($valueConvert))) 
        {
            $this->setKerror($this->labelError.$this->getStringMethodError($defaultValue)."[".$this->getName()."] => Value is not Double ->".$value." // ".$valueConvert);
            return false;
        }  
        if(!is_null($this->min_value))
        {
            if($this->min_value>$valueConvert)
            {
                $this->setKerror($this->labelError.$this->getStringMethodError($defaultValue)."[".$this->getName()."] => Value min is superior ".$this->min_value." > ".$valueConvert);
                return false;
            }
        }
        if(!is_null($this->max_value))
        {
            if($this->max_value<$valueConvert)
            {
                $this->setKerror($this->labelError.$this->getStringMethodError($defaultValue)."[".$this->getName()."] => Value max is inferior ".$this->max_value." > ".$valueConvert);
                return false;
            }            
        }          
        if(!is_null($this->digits_precision))
        {
            //echo "ROUND IT ".$value." \n";
            /** @phpstan-ignore-next-line */
            $value=round($valueConvert,$this->digits_precision,$this->round_mode);
            //echo "ROUNDED ".$value." \n";
        }
        
        $this->setVarValue($valueConvert,$defaultValue);
        return true;
    }      
    
    
    function getMin_value():?float
    {
        return $this->min_value;
    }

    function getMax_value():?float
    {
        return $this->max_value;
    }

    function getDefault_value():?float
    {
        return $this->default_value;
    }

//    function getAuto_increment() : bool
//    {
//        return $this->auto_increment;
//    }

    function setMin_value(mixed $min_value) : bool
    {
        $status=false;
        $min_value= try2ConvertToFloat($min_value); 
        if(is_float($min_value))
        {
            if(is_float($this->max_value) && $this->max_value<=$min_value)
            {
                $this->setKerror($this->labelError."setMin_value[".$this->getName()."] => Value max is inferior ".$this->max_value." <= ".$min_value." ");
            }
            else if(is_float($this->default_value) && $this->default_value<$min_value)
            {
                $this->setKerror($this->labelError."setMax_value[".$this->getName()."] => Value min ".$min_value." > to default Value".$this->default_value." ");
            }            
            else
            {
                $this->min_value=floatval($min_value);
                $status=true;
            }
        }
        else
        {
            $this->setKerror($this->labelError."setMin_value[".$this->getName()."] => Value ".$min_value." not integer");
        }
        return $status;        
    }

    function setMax_value(mixed $max_value) : bool
    {
        $status=false;
        $max_value=try2ConvertToFloat($max_value); 
        if(is_float($max_value))
        {
            if(is_float($this->min_value) && $this->min_value>=$max_value)
            {
                $this->setKerror($this->labelError."setMax_value[".$this->getName()."] => Value min is superior ".$this->min_value." >= ".$max_value." ");
            }
            else if(is_float($this->default_value) && $this->default_value>$max_value)
            {
                $this->setKerror($this->labelError."setMax_value[".$this->getName()."] => Value max ".$max_value." < to default Value".$this->default_value." ");
            }
            else
            {
                $this->max_value=floatval($max_value);
                $status=true;
            }
        }
        else
        {
            $this->setKerror($this->labelError."setMax_value[".$this->getName()."] => Value ".$max_value." not integer");
        }
        return $status;
    }

    function setDefault_value(mixed $default_value) : bool
    {
        $status=false;
        $default_value=try2ConvertToFloat($default_value); 
        if(is_double($default_value))
        {
            if(!is_null($this->max_value) && $default_value>$this->max_value)
            {
                $this->setKerror($this->labelError."setDefault_value[".$this->getName()."] => Value ".$default_value." is > at Max Value ".$this->max_value);
            }
            else if(!is_null($this->min_value) && $default_value<$this->min_value)
            {
                $this->setKerror($this->labelError."setDefault_value[".$this->getName()."] => Value ".$default_value." is < at Min Value ".$this->min_value);
            }
            else
            {
                $this->default_value=floatval($default_value);
                $status=true;
            }
        }
        else
        {
            $this->setKerror($this->labelError."setDefault_value[".$this->getName()."] => Value ".$default_value." is not double");
        }
        return $status;
    } 
    
    function setDigits_precision(mixed $digits_precision) : bool
    {
        $status=false;
        $digits_precision=try2ConvertToInt($digits_precision); 
        if(is_int($digits_precision) && $digits_precision < static::$MAX_VALUE_DIGITS)
        {
            $this->digits_precision=$digits_precision;
            $status=true;
        }
        else
        {
            $this->setKerror($this->labelError."setDigits_precision[".$this->getName()."] => Value ".$digits_precision." is not integer");
        }
        return $status;
    }
    
    function getDigits_precision() : int
    {
        return $this->digits_precision;
    }

        
    
    public function getRound_mode() : int
    {
        return $this->round_mode;
    }

    public function setRound_mode(mixed $round_mode) : bool
    {
        if($round_mode==PHP_ROUND_HALF_UP
                ||$round_mode==PHP_ROUND_HALF_DOWN
                ||$round_mode==PHP_ROUND_HALF_EVEN
                ||$round_mode==PHP_ROUND_HALF_ODD
                )
        {
            $this->round_mode=$round_mode;
            return true;
        }
        return false;
    }
 
    public function toString(string $delimiter="\n") : string
    { 
        $string="#######################".$delimiter."+ KField".$delimiter.parent::toString().$delimiter;
        $array = get_object_vars($this);
        $properties = array_keys($array);
        $string.="+ ".get_class($this).$delimiter;
        foreach ($properties as $key => $value) 
        {
            $string .= "$key = $value =>".$this->$value.$delimiter;
        }
        $string.="#######################".$delimiter;
        return $string;
    } 
    
    /**
     * 
     * @return array<int,array<mixed,mixed>>
     */
    public function jsonSerialize() : array
    {
        $arrayJSON=parent::jsonSerialize();
        $array = get_object_vars($this);
        $properties = array_keys($array);
        foreach ($properties as $key => $value) 
        {
            $arrayJSON[$value]=$this->$value;
        }
        return [
            $arrayJSON
        ];
    }
}