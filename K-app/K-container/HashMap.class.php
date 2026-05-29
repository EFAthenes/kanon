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
class HashMap extends KIterator
{
    public function __clone() : void
    {
        $array = [];
        foreach ($this->array as $key => $value)
        {
            if (is_object($value))
            {
                $array[$key] = clone $value;
            }
            else
            {
                $array[$key] = $value;
            }
        }
        $this->array = $array;
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
    
    /**
     * 
     * @return array<mixed,mixed>
     */
    public function getArray() : array
    {
        return $this->array;
    }

    public function put(mixed $key, mixed $value): bool
    {
        if (array_key_exists($key, $this->array))
        {
            return false;
        }
        $this->array[$key] = $value;
        return true;
    }

    public function putOrReplace(mixed $key, mixed $value): bool
    {
        $this->array[$key] = $value;
        return true;
    }

    public function get(mixed $key) : mixed
    {
        if (array_key_exists($key, $this->array))
        {
            return $this->array[$key];
        }
        return null;
    }

    public function getKObject(mixed $key): ?KObject
    {
        if (!is_null($this->array[$key]) && $this->array[$key] instanceof KObject)
        {
            return $this->array[$key];
        }
        return null;
    }

    public function replace(mixed $key,mixed $value) : bool
    {
        if (!array_key_exists($key, $this->array))
        {
            return false;
        }
        $this->array[$key] = $value;
        return true;
    }

    public function toArrayList(): ArrayList
    {
        $list = new ArrayList();
        $array = $this->customSort($this->array);
        foreach ($array as $key => $value)
        {
            if ($this->debug)
            {
                echo "KEY==" . $key . "//" . $value . "<br />";
            }
            $list->add($value);
        }
        return $list;
    }

    public function toArrayListNotSort(): ArrayList
    {
        $list = new ArrayList();
        //KDebugger::getInstance()->dump($this->array);
        $list->replaceArray(array_values($this->array));
        //KDebugger::getInstance()->dump($list);
//        foreach($this->array as $key => $value)
//        {
//            $list->add($value);
//        }
        return $list;
    }

    public function toKeyArrayList(): ArrayList
    {
        $list = new ArrayList();
        $array = $this->array;
        foreach ($array as $key => $value)
        {
            $list->add($key);
        }
        return $list;
    }

    /**
     * 
     * @param array<mixed,mixed> $array
     * @return array<mixed,mixed>
     */
    private function customSort(array $array): array
    {
        ksort($array);
        return $array;
    }

    public function toString(string $delimitor = "") : string
    {
        if ($delimitor == "")
        {
            $delimitor = $this->delimitor;
        }
        $print = "HashMap::toString()" . $delimitor;
        foreach ($this->array as $key => $value)
        {
            /** @phpstan-ignore-next-line */
            if ($value instanceof KObject || $value instanceof KField || $value instanceof KLinkObject || $value instanceof KLinkObjectItem)
            {
                $print .= $delimitor . "Key = " . $key . " || Value = " . $value->toString($delimitor);
            }
            else if ($value instanceof KComponent)
            {
                $print .= $delimitor . "Key = " . $key . " || Value = " . $value->toString($delimitor);
            }
            else if (is_array($value))
            {
                $print .= $delimitor . "Key = " . $key . " || Value = Array";
                $i = 0;
                foreach ($value as $val)
                {
                    $print .= $delimitor . "Val[" . $i . "] = " . $val . "";
                    $i++;
                }
            }
            else if (!is_object($value) && settype($value, 'string') !== false)
            {
                $print .= $delimitor . "Key = " . $key . " || Value = " . $value;
            }
            else if (!is_null($value) && is_object($value))
            {
                $print .= $delimitor . "Key = " . $key . " || Value is Object type = " . get_class($value);
                $toStringMethod = "toString";
                if (method_exists($value, $toStringMethod))
                {
                    $print .= $delimitor . $toStringMethod . "==>" . $delimitor . $value->$toStringMethod($delimitor);
                }
            }
            else
            {
                $print .= $delimitor . "Key = " . $key . " || Object no convertable to String ";
            }
        }
        return $print;
    }

    public function remove(mixed $key) : bool
    {
        //unset($array[$key]);
        if (array_key_exists($key, $this->array))
        {
            unset($this->array[$key]);
            return true;
        }
        return false;
    }

    public function exists(mixed $key) : bool
    {
        if(array_key_exists($key, $this->array))
        {
            return true;
        }
        return false;
    }
    
    public function setToTerminal() : void
    {
        $this->delimitor = "\n";
    }
}