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
abstract class HashMapSingleton extends HashMap
{
    
    private const string PREFIX = "KSingleton-";

    private static ?HashMap $instances=null;
    
    private static function makeNewToSession(string $className,string $keyName) : static
    {
        $singleton=new $className();
        SessionMemory::getInstance()->addKSingleton($keyName, $singleton);
        /* @phpstan-ignore-next-line */
        return $singleton;
    }
    
    private static function makeNew(string $className,string $keyName) : static
    {
        $singleton=new $className();
        self::$instances->put($keyName,$singleton);
        /* @phpstan-ignore-next-line */
        return $singleton;
    }    
    
    public static function getInstance(bool $reload=false,bool $sessionType=true) : static
    {
        $singleton=null;
        $className=get_called_class();
        $keyName=self::PREFIX.$className;
        
        if($sessionType)
        {
            if($reload)
            {
                $singleton=self::makeNewToSession($className, $keyName);
            }
            else
            {
               $singleton=SessionMemory::getInstance()->getKSingleton($keyName);
               if(is_null($singleton))
               {
                    $singleton=self::makeNewToSession($className, $keyName);
               }
            }
            /* @phpstan-ignore-next-line */
            return $singleton;
        }
        else
        {
            if($reload||is_null(self::$instances))
            {
                self::$instances=new HashMap();
            }
            $singleton=self::$instances->get($keyName);
            if(is_null($singleton))
            {
                $singleton=self::makeNew($className, $keyName);
            }
            return $singleton;
        }
    }
    
    private function __construct()
    {        
        parent::__construct();
//        KDebugger::getInstance()->dump(get_called_class(),"HashmapSingleton");
//        KDebugger::getInstance()->dump(self::$instances,"instance");
//        KDebugger::getInstance()->dump(SessionMemory::getInstance(),"Memory");
        $this->init();
    }   
    
    abstract public function init() : void;
}  