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
class KFieldInteger extends KField
{
    public static string $MIN_VALUE="min_value";
    public static string $MAX_VALUE="max_value";
    public static string $DEFAULT_VALUE="default_value";
    //public static $AUTO_INCREMENT="auto_increment";
    
    private ?int $min_value=NULL;
    private ?int $max_value=NULL;
    private ?int $default_value=NULL;

    function __construct()
    {
        $this->setType(static::$INTEGER);
        $this->setValueVar(0);
        $this->labelError="KFieldInteger::";
        $this->setDefault(0);
    }
    
    function affectValue(mixed $value,bool $defaultValue=false) : bool
    {
        if(isStringIntegerNull($value))
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
        
        $valueConvert=try2ConvertToInt($value);      
        //echo "<br />=>".$valueConvert."//try2ConvertToInt=>".$value."<br />";
        if(!is_int($valueConvert))
        {
            $this->setKerror($this->labelError.$this->getStringMethodError($defaultValue)."[".$this->getName()."] => Value is not Integer ->".print_r($value,true));
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
        $this->setVarValue($valueConvert,$defaultValue);
        return true;
    }
    
    public function notEqualValue(mixed $value) : bool
    {
        return convertStringtoIntegerOrNull($value)!==convertStringtoIntegerOrNull($this->get());
    }    
    
    
    function getMin_value() : ?int
    {
        return $this->min_value;
    }

    function getMax_value() : ?int
    {
        return $this->max_value;
    }

    function getDefault_value() : ?int
    {
        return $this->default_value;
    }

//    function getAuto_increment()
//    {
//        return $this->auto_increment;
//    }

    function setMin_value(mixed $min_value) : bool
    {
        $status=false;
        $min_value=try2ConvertToInt($min_value);  
        if(is_int($min_value))
        {
            if(is_int($this->max_value) && $this->max_value<=$min_value)
            {
                $this->setKerror($this->labelError."setMin_value[".$this->getName()."] => Value max is inferior ".$this->max_value." <= ".$min_value." ");
            }
            else if(is_int($this->default_value) && $this->default_value<$min_value)
            {
                $this->setKerror($this->labelError."setMax_value[".$this->getName()."] => Value min ".$min_value." > to default Value".$this->default_value." ");
            }            
            else
            {
                $this->min_value=$min_value;
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
        $max_value=try2ConvertToInt($max_value);
        if(is_int($max_value))
        {
            if(is_int($this->min_value) && $this->min_value>=$max_value)
            {
                $this->setKerror($this->labelError."setMax_value[".$this->getName()."] => Value min is superior ".$this->min_value." >= ".$max_value." ");
            }
            else if(is_int($this->default_value) && $this->default_value>$max_value)
            {
                $this->setKerror($this->labelError."setMax_value[".$this->getName()."] => Value max ".$max_value." < to default Value".$this->default_value." ");
            }
            else
            {
                $this->max_value=$max_value;
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
        $default_value=try2ConvertToInt($default_value);
        if(is_int($default_value))
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
                $this->default_value=$default_value;
                $status=true;
            }
        }
        else
        {
            $this->setKerror($this->labelError."setDefault_value[".$this->getName()."] => Value ".$default_value." is not integer");
        }
        return $status;
    }

    function toString(string $delimiter="\n") : string
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