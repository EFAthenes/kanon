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
class KFieldBool extends KField
{
    function __construct()
    {
        $this->setType(static::$BOOL);
        $this->setValueVar(false);
        $this->labelError="KFieldBool::";
        $this->setDefault(false);
    }
    
    function affectValue(mixed $value,bool $defaultValue=false) : bool
    {       
        //KDebugger::getInstance()->dump($value,"KFieldBool::affectValue");
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
        $result=$this->testIfBool($value);
        if($result===null)
        {
            $this->setKerror($this->labelError.$this->getStringMethodError($defaultValue)."[".$this->getName()."] => Value is not bool ->".$value);
            return false;
        }
        $this->setVarValue($result,$defaultValue);
        //KDebugger::getInstance()->dump($this->getValueForSql(Sql::getInstance()),"KFieldBool::affectValue End");
        return true;        
    }

    private function testIfBool(mixed $in) : ?bool /*: ?bool */
    {
        return convertToBool($in);
    }

    public function convertTypeToSqlString(Sql $sql,mixed $value) : string
    {
        return $sql->convertToBool($value);
    }
    
    public function convertTypeToSqlStringForNull(Sql $sql,mixed $value) : string
    {
        return $sql->convertToBool($value);
    }    
    
    public function notEqualValue(mixed $value) : bool
    {
        return convertToBool($value)!==convertToBool($this->get());
    }
    
    function toString(string $delimiter="\n") : string
    {
        $string="#######################".$delimiter."+ KField".$delimiter.parent::toString().$delimiter;
        $array=get_object_vars($this);
        $properties=array_keys($array);
        $string.="+ ".get_class($this).$delimiter;
        foreach($properties as $key=> $value)
        {
            $string.="$key = $value =>".$this->$value.$delimiter;
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
        $array=get_object_vars($this);
        $properties=array_keys($array);
        foreach($properties as $key=> $value)
        {
            $arrayJSON[$value]=$this->$value;
        }
        return [
            $arrayJSON
        ];
    }
    
    public function getDefaultString() : string
    {
        if(is_null($this->getDefault()))
        {
            return "NULL";
        }
        else
        {
            if($this->getDefault()==="")
            {
                return "''";
            }
            else
            {
                return "".convertBoolToString($this->getDefault());
            }
        }
    }     

}