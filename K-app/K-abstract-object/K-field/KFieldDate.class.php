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
class KFieldDate extends KField
{
    private string $dateTime_format="Y-m-d";
    function __construct()
    {
        $this->setType(static::$DATE);
        $this->setIs_null(true);
        $this->setValueVar(NULL);
        $this->labelError="KFieldDate::";
    }
    
    function affectValue(mixed $value,bool $defaultValue=false) : bool
    { 
        if(isStringNull($value)||$value=="")
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
        
        $value="".$value;       
        if(!$this->verifFormatDate($value,$this->dateTime_format))
        {
            $this->setKerror($this->labelError.$this->getStringMethodError($defaultValue)."[".$this->getName()."] => Value is not a date (".$this->dateTime_format.") -> {".$value."}");
            return false;
        }
        $this->setVarValue($value,$defaultValue);
        return true;
    }
    
    function setNow() : void
    {
        $date=date($this->dateTime_format);
        $this->setValueVar($date);
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