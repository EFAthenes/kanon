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
class ArrayList extends KIterator
{

    public function __clone() : void
    {
//        foreach($this as $key => $val) 
//        {
//            if (is_object($val) || (is_array($val))) 
//            {
//                $this->{$key} = unserialize(serialize($val));
//            }
//        }            

        $array = [];
        foreach ($this->array as $value)
        {
            if (is_object($value))
            {
                $array[] = clone $value;
            }
            else
            {
                $array[] = $value;
            }
        }
        $this->array = $array;
    }
    
    /**
     * 
     * @return array<mixed,mixed>
     */
    public function getArray() : array
    {
        return $this->array;
    }

    public function get(mixed $i) : mixed
    {
        return $this->array[$i];
    }
    
    public function getOrNull(mixed $i) : mixed
    {
        if (!array_key_exists($i, $this->array))
        {
            return null;
        }
        return $this->get($i);
    }

    /**
     * 
     * @param array<mixed,mixed> $array
     * @return void
     */
    public function replaceArray(array $array) : void
    {
        $this->array = $array;
    }

    public function getKObject(mixed $i): ?KObject
    {
        if ($this->array[$i] instanceof KObject)
        {
            return $this->array[$i];
        }
        return null;
    }

    public function replace(mixed $i, mixed $object) : bool
    {
        if (!array_key_exists($i, $this->array))
        {
            return false;
        }
        $this->array[$i] = $object;
        return true;
    }

    public function add(mixed $object) : void
    {
        $this->array[] = $object;
    }

    public function addList(ArrayList $list) : bool
    {
        if ($list == null)
        {
            return false;
        }
        for ($i = 0; $i < $list->getSize(); $i++)
        {
            $this->add($list->get($i));
        }
        return true;
    }

    public function remove(mixed $i) : bool
    {
        if (!array_key_exists($i, $this->array))
        {
            return false;
        }
        unset($this->array[$i]);
        $this->array = array_values($this->array);
        return true;
    }

    public function removeObject(mixed $object) : bool
    {
        $status=false;
        $i=0;
        foreach ($this->array as $item)
        {
            if($item===$object)
            {
                unset($this->array[$i]);
                $status=true;
                $this->array = array_values($this->array); 
                break;
            }
            $i++;
        }
        return $status;
    }

    public function isEmpty() : bool
    {
        if (count($this->array) > 0)
        {
            return false;
        }
        return true;
    }

    public function getLast() : mixed
    {
        if (count($this->array) > 0)
        {
            return end($this->array);
        }
        return null;
    }

    /**
     * 
     * @return array<int,string>
     */
    public function toArrayOfString() : array
    {
        $array = array();
        for ($i = 0; $i < count($this->array); $i++)
        {
            $array[] = "" . $this->array[$i];
        }
        return $array;
    }

    public function toString(string $delimitor="") : string
    {
        if ($delimitor == "")
        {
            $delimitor = $this->delimitor;
        }
        $html = "";

        //for($i=0; $i< count($this->array); $i++)
        $i = 0;
        foreach ($this->array as $value)
        {
            if ($value instanceof KObject || $value instanceof KField)
            {
                $html .= $i . "-" . $value->toString($delimitor) . $delimitor;
            }
            else if (is_object($value) && method_exists($value, 'toString'))
            {
                $html .= $i . "-" . $value->toString($delimitor) . $delimitor;
            }
            else if (is_object($value))
            {
                $html .= $i . "-" . get_class($value) . $delimitor;
            }
            else if(is_array($value))
            {
                $html .= $i . "-" . print_r($value,true) . $delimitor;
            }
            else if (!is_object($value))
            {
                $html .= $i . "-" . gettype($value) . "=>" . ($value) . $delimitor;
            }
            else
            {
                $html .= $i . "-" . gettype($value) . $delimitor;
            }
            $i++;
        }
        return $html;
    }
    
    public function removeFirstElements(int $offset) : void
    {
        $this->array=array_slice($this->array, $offset);
    }
    
    public function getCurrentKey() : mixed /*: int|string|null */
    {
        return key($this->array);
    }
    
}