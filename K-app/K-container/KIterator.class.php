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
/**
 * @implements \Iterator<mixed,mixed>
 */
abstract class KIterator implements \Iterator
{
    protected string $delimitor="<br />";
    /**
     * 
     * @var array<mixed,mixed>
     */
    protected array $array;
    protected bool $debug=false;
    
    public function __construct()
    {
        $this->array=array();
    }            
    public function current() : mixed 
    {
        $current = current($this->array);
        return $current;
    }

    public function key() : mixed 
    {
        $key = key($this->array);
        return  $key;        
    }

    public function next(): void
    {
        $next = next($this->array);        
    }

    public function rewind(): void
    {
        reset($this->array);
    }

    public function valid(): bool
    {
        $key = key($this->array);
        return (!is_null($key));         
    }
    
    public function getDebug() : bool
    {
        return $this->debug;
    }

    public function setDebug(bool $debug) : void
    {
        $this->debug=$debug;
    }    
    
    public function keyExists(mixed $key) : bool
    {
        return array_key_exists($key, $this->array);
    }
    
    // ALIAS
    public function isIndexValid(mixed $index) : bool
    {
        return $this->keyExists($index);
    }  
    /**
     * 
     * @return array<mixed,mixed>|null
     */
    public function toArray() : ?array
    {
        return $this->array;
    }
    
    public function free() : void
    {
        $this->clear();
        //Cannot unset property KIterator::$array because it might have hooks in a subclass.
        //unset($this->array);        
    }
    
    public function clear() : void
    {
        $this->array=array();
    }  
   

    public function getCount() : int
    {
        return count($this->array);
    }
    
    // ALIAS getCount
    public function getNB() : int
    {
        return $this->getCount();
    }
    
    public function getSize() : int
    {
        return $this->getCount();
    }
    
    public function isEmpty() : bool
    {
        if(count($this->array)>0)
        {
            return false;
        }
        return true;
    }
    
    abstract public function toString(string $delimitor="") : string;

}