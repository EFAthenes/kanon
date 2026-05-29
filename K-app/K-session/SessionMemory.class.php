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
 * Description of SessionMemory
 *
 * @author Mulot Louis
 *
 * Singleton for Session container
 */
class SessionMemory
{
    private ?HashMap $map=null;
    private static ?SessionMemory $instance=null;
    public static string $USER="user";
    public static string $INFO="info";
    public static string $LAST_PAGE="last_page";
    public static string $PARAMETERS="parameters";
    public static string $SEARCH="search";
    public static string $CONFIG_MANAGER="config_manager";
    public static string $XLS_MANAGER="xls_manager";
    public static string $RANDOM="random";
    public static string $LANGUAGE="language";
    public static string $CONSULTANT_DROIT="consultant_droit";
    public static string $XLS_DATA="xls_data";
    public static string $DELETE_XLS_DATA="delete_xls_data";
    public static string $HISTORY_PAGE="history_page";
    public static string $MAP_SINGLETON="map_singleton";

    private function __construct()
    {
        $this->map=new HashMap(); //SessionMemoryMap();        
    }

    public function __destruct()
    {
        
    }

    private static function isCLI() : bool
    {
        return (php_sapi_name()==='cli');
    }

    public static function getInstance(): SessionMemory
    {
        if(self::$instance==null)
        {
            if(self::isCLI())
            {
                self::$instance=new self();
            }
            else if(isset($_SESSION['memory']))
            {
                self::$instance=$_SESSION['memory'];
            }
            else
            {
                self::$instance=new self();
                $_SESSION['memory']=self::$instance;
            }
        }
        return self::$instance;
    }

    public static function reGetInstance() : bool
    {
        if(isset($_SESSION['memory']))
        {
            self::$instance=$_SESSION['memory'];
            return true;
        }
        return false;
    }

    //---------------------
    public function putLastPage(mixed $object) : bool
    {
        return $this->putOrReplace(self::$LAST_PAGE,$object);
    }

    public function getLastPage() : mixed
    {
        $lastPage=$this->map->get(self::$LAST_PAGE);
        return $lastPage;
    }

    public function removeLastPage() : void
    {
        $this->remove(self::$LAST_PAGE);
    }

    //----------------------
    //----------------------

    public function getUser(): Kapp_Users
    {
        //$user=new Users();
        //$user=$this->map->get(self::$USER);
        $user=$this->map->get(self::$USER);
        if(is_null($user))
        {
            $user=new Kapp_Users();
        }
        return $user;
    }
    
//    public function &getMapSingleton() : array
//    {
//        $map_singleton=$this->map->get(self::$MAP_SINGLETON);
//        if(is_null($map_singleton))
//        {
//            $map_singleton=[];
//            $this->map->put(self::$MAP_SINGLETON,$map_singleton);
//        }
//        return $map_singleton;
//    }
//    public function replaceMapSingleton(array $map_singleton) : void
//    {
//        $this->map->putOrReplace(self::$MAP_SINGLETON,$map_singleton);
//    }    

    public function addKSingleton(string $key,HashMapSingleton $singleton) : void
    {
        $map_singleton=$this->map->get(self::$MAP_SINGLETON);
        if(is_null($map_singleton))
        {
            $map_singleton=new HashMap();
            $this->map->put(self::$MAP_SINGLETON,$map_singleton);
        }
        
        $singletonB=$map_singleton->get($key);
        if(is_null($singletonB))
        {
            $map_singleton->put($key,$singleton);
        }
        else
        {
            $map_singleton->putOrReplace($key,$singleton);
        }
    }
    public function getKSingleton(string $key) : ?HashMapSingleton
    {
        /* @var $singletonMap HashMapSingleton */
        $map_singleton=$this->map->get(self::$MAP_SINGLETON);
        if(!is_null($map_singleton))
        {
            return $map_singleton->get($key);
        }
        return null;
    }
    
    public function putUser(Kapp_Users $user) : bool
    {
        return $this->putOrReplace(self::$USER,$user);
    }

    public function removeUser() : void
    {
        $this->remove(self::$USER);
    }

    public function putHistoryPage(mixed $object) : bool
    {
        return $this->put(self::$HISTORY_PAGE,$object);
    }

    public function getHistoryPage() : mixed
    {
        $history=$this->map->get(self::$HISTORY_PAGE);
        return $history;
    }

    public function removeHistoryPage() : bool
    {
        return $this->map->remove(self::$HISTORY_PAGE);
    }

    public function putParam(mixed $object) : bool
    {
        return $this->putOrReplace(self::$PARAMETERS,$object);
    }

    public function getParam() : mixed
    {
        $bag=$this->map->get(self::$PARAMETERS);
        return $bag;
    }

    public function removeParam() : void
    {
        $this->map->remove(self::$PARAMETERS);
    }

    public function putRandom(mixed $object) : bool
    {
        return $this->putOrReplace(self::$RANDOM,$object);
    }

    public function getRandom() : mixed
    {
        $bag=$this->map->get(self::$RANDOM);
        return $bag;
    }

    public function removeRandom() : void
    {
        $this->map->remove(self::$RANDOM);
    }

    public function put(mixed $value,mixed $object) : bool
    {
        return $this->map->put($value,$object);
    }

    public function replace(mixed $value,mixed $object) : bool
    {
        return $this->map->replace($value,$object);
    }

    public function clear() : void
    {
        $this->map->clear();
        unset($this->map);
        $this->map=new HashMap();
    }

    public function get(mixed $value) : mixed
    {
        return $this->map->get($value);
    }

    public function exists(mixed $value): bool
    {
        return $this->map->get($value) != null;
    }

    public function remove(mixed $value) : bool
    {
        return $this->map->remove($value);
    }

    public function putOrReplace(mixed $value,mixed $object) : bool
    {
        if($this->map->get($value)!=null)
        {
            return $this->map->replace($value,$object);
        }
        else
        {
            return $this->map->put($value,$object);
        }
    }

    public function getMap() : ?HashMap
    {
        return $this->map;
    }

    public function reset() : void
    {
        unset($_SESSION['memory']);
        self::getInstance();
    }
    
    public function toString(string $delimitor="<br />") : string
    {
        if($this->map!=null)
        {
            return "<br />SESSION MEMORY ==>".$this->map->toString($delimitor="<br />");
        }
        else
        {
            return " this->map == NULL ";
        }
    }

}