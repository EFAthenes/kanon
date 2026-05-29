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
class KFieldFloat extends KFieldDouble
{
    function __construct()
    {
        $this->setType(static::$FLOAT);
        $this->setValueVar(0);
        $this->labelError="KFieldFloat::";
        $this->setDefault(0);
        self::$MAX_VALUE_DIGITS=8;
        $this->setDigits_precision(8);
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