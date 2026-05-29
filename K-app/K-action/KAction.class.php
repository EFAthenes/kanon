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
abstract class KAction
{
    
    private bool $test=false;
    /**
     * 
     * @var array<int,string>
     */
    private array $errorMessagesArray=[];
    
    /**
     * 
     * @var array<string,string>
     */
    private array $arrayJson=[];    
    
    public function __construct()
    {
        
    }
    abstract public function execute() : bool;
    
    public function getErrorMessagesToString(string $separator="<br />") : string
    {
        $errorString="";
        foreach ($this->errorMessagesArray as $key => $string)
        { 
            $errorString.="".$key."->".$string.$separator;
        }
        return $errorString;
    }
    
    protected function addErrorString(string $string) : void
    {
        $this->errorMessagesArray[]=$string;
    }   
    
    protected function emptyErrorMEssages() : void
    {
        $this->errorMessagesArray=[];
    }
    /**
     * 
     * @return array<int,string>
     */
    public function getErrorMessagesArray() : array
    {
        return $this->errorMessagesArray;
    }   
    
    public function hasErrors() : bool
    {
        return (bool)(count($this->errorMessagesArray));
    }
    
    public function completeWithoutErrors() : bool
    {
        return (bool)(count($this->errorMessagesArray)==0);
    }    
    
    public function addItemToJson(string $key,mixed $value) : void
    {
        $this->arrayJson[$key]=$value;
    }
    
    /**
     * 
     * @return array<string,string>
     */
    public function getJsonArray() : array
    {    
        return $this->arrayJson;
    }

    public function isTest(): bool
    {
        return $this->test;
    }
    
    public function getTest(): bool
    {
        return $this->test;
    }

    public function setTest(bool $test): void
    {
        $this->test = $test;
    }
 
}