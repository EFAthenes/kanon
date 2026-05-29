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
class DbList
{
    private ?string $className=null;  
    /* @var $class KObject */
    private ?KObject $class=null;
    /**
     * 
     * @var array<string>|null
     */
    private ?array $sql_add_request_temp=null; 
    private ?Sql $sql=null;
    /** @phpstan-ignore-next-line */
    private bool $select_all_fields=false;
    private bool $debug=false;
    private bool $cache=false;
    private string $cache_key="";
    private mixed $results=null;
    private bool $count=false;
    
    // For Last Query
    /**
     * 
     * @var array<QueryField>|null 
     */
    private ?array $last_query_array=null;
    private ?SqlOrder $last_query_order=null;
    /** @phpstan-ignore-next-line */
    private ?SqlLimit $last_query_limit=null;
    /**
     * 
     * @var array<int,mixed>|null
     */
    private ?array $last_query_join=null;
    private string $last_query="";
    private string $last_query_nb="";
    private bool $initForeignObject=false;
    private ?KTimer $timer=null;
    private bool $timer_on=true;
    private ?KCache $kCache=null;
    
    private string $cache_lastModificationforTable="";
    private int $cache_numRowsTable=0;
            
    public static string $CONDITION_AND=" AND ";
    public static string $CONDITION_OR=" OR ";
    
    public function __construct(?string $className)
    {
        $this->className=$className;
        $this->sql=Sql::getInstance();
        $this->kCache=KCache::getInstance();      
        if(is_null($this->sql))
        {
            echo "SQL CONNECTION FAILED";
            exit();
        }
    }
       
    public function reset() : void
    {
        $this->count=false;
        $this->initForeignObject=false;
    }
    
    public function __destruct()
    {
    }
    
    public function setSqlEngine(Sql $sql) : void
    {
        $this->sql=$sql;
    }
    
    function getCacheKey() : string
    {
        return $this->cache_key;
    }
    
    function getCache() : bool
    {
        return $this->cache;
    }

    function setCache(bool $cache) : void
    {
        $this->cache=$cache;
    }

    public function setDebug(bool $debug) : void
    {
        $this->debug=$debug;
    }
    
    private function checkIfClassExists() : bool
    {
        if(class_exists($this->className))
        {
            $this->class=null;
            $class=new $this->className();
            if($class instanceof KObject)
            {
                $this->class=$class;
                return true;
            }
        }
        if($this->debug)
        {
            echo 'CLASS DOESNT EXIST !!!\n';
        }
        return false;
    }
    
    private function startTimer() : void
    {
        if($this->timer_on)
        {
            $this->timer=new KTimer();
            $this->timer->start();
        }        
    }
    private function stopTimer() : void
    {
        if($this->timer_on)
        {
            $this->timer->stop();
        }        
    }    
    public function getTime() : string
    {
        if($this->timer_on && $this->timer instanceof KTimer)
        {
            return $this->timer->toString();
        }
        return "";
    }
    
    public function getAll(?SqlOrder $order=null) : ArrayList
    {
        return $this->getByArray(null,$order);
    }
    
    public function getAllById() : HashMap
    {
        return $this->getByArrayInIdMap();
    }   
    /**
     * 
     * @param array<int,QueryField>|null $array
     * @param SqlOrder|null $order
     * @param SqlLimit|null $limit
     * @param array<int,mixed>|null $arrayJoin
     * @return string
     */
    public function test(?array $array=null,?SqlOrder $order=null,?SqlLimit $limit=null ,?array $arrayJoin=null ) : string
    {
        if(!$this->checkIfClassExists())
        {
            return "Class doesn't exist!";
        }           
        return $this->makeQuery($array,$order,$limit,"",$arrayJoin); 
    }
    /**
     * 
     * @param array<int,QueryField>|null $array
     * @param SqlOrder|null $order
     * @param SqlLimit|null $limit
     * @param array<int,mixed>|null $arrayJoin
     * @param string $onlyOneField
     * @return string
     */
    public function getTheQuery(?array $array=null,?SqlOrder $order=null,?SqlLimit $limit=null ,?array $arrayJoin=null,string $onlyOneField="" ) : string
    {
        if(!$this->checkIfClassExists())
        {
            return "";
        }  
        if(!empty($onlyOneField))
        {
            if(!$this->class->fieldNameExists($onlyOneField))
            {
                return "";
            }        
            if($order==null)
            {
                $order=new SqlOrder($onlyOneField,SqlOrder::$ASC);   
            }
            $order->setTable($this->class->getTable_name());
        }
        
        return $this->makeQuery($array,$order,$limit,$onlyOneField,$arrayJoin); 
    }
    
    public function getWhere(string $field_name,mixed $field_value,string $field_type_search="") : ArrayList
    {  
        $where=[QueryField::m($field_name, $field_value,$field_type_search)];
        return $this->getByArray($where);
    }    
    /**
     * 
     * @param mixed $field_value
     * @param string $field_type_search
     * @param array<int,string>|null $excludeFields
     * @return ArrayList
     */
    public function getAnyWhere(mixed $field_value,string $field_type_search="",?array $excludeFields=null) : ArrayList
    {  
        $where=$this->makeAnyWhere($field_value, $field_type_search, $excludeFields);
        if(is_null($where))
        {
            return new ArrayList();
        }
        return $this->getByArray([$where]);
    }
    
    /**
     * 
     * @param mixed $field_value
     * @param string $field_type_search
     * @param array<int,string>|null $excludeFields
     * @return ArrayList
     */
    public function getIdsAnyWhere(mixed $field_value,string $field_type_search="",?array $excludeFields=null) : ArrayList
    {  
        $where=$this->makeAnyWhere($field_value, $field_type_search, $excludeFields);
        if(is_null($where))
        {
            return new ArrayList();
        }
        return $this->getIdsByArray([$where]);
    } 
    
    /**
     * 
     * @param string $fieldName
     * @param mixed $field_value
     * @param string $field_type_search
     * @param array<int,string>|null $excludeFields
     * @return ArrayList
     */
    public function getDistinctFieldAnyWhere(string $fieldName,mixed $field_value,string $field_type_search="",?array $excludeFields=null) : ArrayList
    {  
        $where=$this->makeAnyWhere($field_value, $field_type_search, $excludeFields);
        if(is_null($where))
        {
            return new ArrayList();
        }
        return $this->getDistinctField($fieldName,[$where]);
    }    
    
    /**
     * 
     * @param mixed $field_value
     * @param string $field_type_search
     * @param array<int,string>|null $excludeFields
     * @return array<int,QueryField>|null
     */
    private function makeAnyWhere(mixed $field_value,string $field_type_search="",?array $excludeFields=null) :?array
    {
        $where=[];
        if(is_null($excludeFields))
        {
            $excludeFields=[KObject::$ID,KObject::$DATE_CREATED,KObject::$DATE_MODIFIED];
        }
        if(!$this->checkIfClassExists())
        { 
            return null;
        }      
        /* @var $classMother KObject */
        $classMother=new $this->className();
        $classMother->initKFields();
        $fieldsName=$classMother->getListFieldName();
        foreach($fieldsName as $field_name)
        {
            if(!in_array($field_name, $excludeFields))
            {
                $kfield=$classMother->getKField($field_name);
                if($kfield->isSqlStandardQueriable())
                {
                    $where[]=QueryField::m($field_name, $field_value,$field_type_search);
                }
            }
        }
        
        if(count($where)==0)
        {
            return null;
        }
        return $where;
    }
       
    /**
     * 
     * @param array<int,QueryField>|array<int,array<int,QueryField>>|array<int,array<int, array<int, QueryField>>>|null $queryFields                        
     * @param SqlOrder|null $order
     * @param SqlLimit|null $limit
     * @param array<int,mixed>|null $arrayJoin
     * @param CacheSqlParameter|null $cacheParam
     * @return ArrayList
     */
    public function getByArray(?array $queryFields=null,?SqlOrder $order=null,?SqlLimit $limit=null ,?array $arrayJoin=null,?CacheSqlParameter $cacheParam=null) : ArrayList
    {      
        $this->startTimer();
        if(!$this->checkIfClassExists())
        {
            return new ArrayList();
        }      
        $this->select_all_fields=true;
        $this->last_query=$this->makeQuery($queryFields,$order,$limit,"",$arrayJoin); 
        if($this->cache && $this->isQueryCached("getByArray","",$cacheParam))
        {      
            return $this->makeArrayListByCache();
        }
        //echo "NOT CACHE ".$this->cache."<br />";
        $list_class=new ArrayList(); 
        
        $this->results = $this->sql->request_SQL($this->last_query);      
        //$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
        // PATCH MYSQL
        //$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
        $initForeignKObjects=false;
        if($this->initForeignObject&&!$this->isSQLtypeMySQL())
        {
            $initForeignKObjects=true;
        }
        //$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
        $arrayTemp=[];
        $classMother=new $this->className();
        while(($result=$this->sql->fetch_array($this->results))!=NULL)
        {
            /* @var $class KObject */
            $class=clone $classMother;
            if(!$class->initByResultSql($result,$this->sql,$initForeignKObjects))
            {
                echo $class->getKerror();
                return $list_class;
            }           
            //$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
            // PATCH MYSQL
            //$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
            else if($this->initForeignObject&&$this->isSQLtypeMySQL())
            {
                $class->initAllForeignKeyKObjects($this->sql);
            }
            //$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
            
            $arrayTemp[]=$class;
        }
        $list_class->replaceArray($arrayTemp);
        
        if($this->cache)
        {
            $this->makeFileCacheFromArray($arrayTemp);
        }        
        
        $this->stopTimer();
        return $list_class; 
    }    
    /**
     * 
     * @param array<int,QueryField>|array<int,array<int,QueryField>>|array<int,array<int, array<int, QueryField>>>|null $array 
     * @param SqlOrder|null $order
     * @param SqlLimit|null $limit
     * @param array<int,mixed>|null $arrayJoin
     * @param CacheSqlParameter|null $cacheParam
     * @return ArrayList
     */
    public function getIdsByArray(?array $array=null,?SqlOrder $order=null,?SqlLimit $limit=null,?array $arrayJoin=null,?CacheSqlParameter $cacheParam=null) : ArrayList
    {     
        return $this->getDistinctField(KObject::$ID,$array,$order,$limit,$arrayJoin,$cacheParam);        
    }
    
    /**
     * 
     * @param array<int,QueryField>|array<int,array<int,QueryField>>|array<int,array<int, array<int, QueryField>>>|null $array 
     * @param SqlOrder|null $order
     * @param SqlLimit|null $limit
     * @param CacheSqlParameter|null $cacheParam
     * @return HashMap
     */
    public function getByArrayInIdMap(?array $array=null,?SqlOrder $order=null,?SqlLimit $limit=null,?CacheSqlParameter $cacheParam=null) : HashMap
    {  
        $this->startTimer();
        if(!$this->checkIfClassExists())
        {
            if($this->debug)
            {
                echo "DbList::getByArrayInIdMap // checkIfClassExists FALSE ==> NULL";
            }
            return new HashMap();
        }
         
        $this->select_all_fields=true;
        $this->last_query=$this->makeQuery($array,$order,$limit,"",null);
        if($this->cache && $this->isQueryCached("getByArrayInIdMap","",$cacheParam))
        {           
            return $this->makeHashMapByCache();
        }
        $map_class= new HashMap();      
        $req = $this->sql->request_SQL($this->last_query);
        //echo $this->last_query;
        //$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
        // PATCH MYSQL
        //$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$    
        $initForeignKObjects=false;
        if($this->initForeignObject&&!$this->isSQLtypeMySQL())
        {
            $initForeignKObjects=true;
        }
        //$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
        $arrayTemp=[];
        $classMother=new $this->className();
        while(($result=$this->sql->fetch_array($req))!=NULL)
        {
            $class=clone $classMother;  
            $class->initByResultSql($result,$this->sql,$initForeignKObjects); //,$this->sql);
            //$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
            // PATCH MYSQL
            //$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
            if($this->initForeignObject&&$this->isSQLtypeMySQL())
            {
                $class->initAllForeignKeyKObjects($this->sql);
            }
            //$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
            $arrayTemp[$result[$class->makeAliasSqlField($class::$ID)]]=$class;
        }
        $map_class->replaceArray($arrayTemp);
        
        if($this->cache)
        {
            $this->makeFileCacheFromArray($arrayTemp);
        }         

        $this->stopTimer();
        return $map_class;      
    }
    
    public function getNbLastQuery() : int
    {  
        return $this->getNb($this->last_query_array,$this->last_query_order,$this->last_query_join);
    }
    
    public function getNbWhere(string $field_name,mixed $field_value) : int
    {    
        $where=[QueryField::m($field_name, $field_value)];
        return $this->getNb($where);
    }
    /**
     * 
     * @param array<int,QueryField>|array<int,array<int,QueryField>>|array<int,array<int, array<int, QueryField>>>|null $array 
     * @param SqlOrder|null $order
     * @param array<int,mixed>|null $arrayJoin
     * @return int
     */
    public function getNb(?array $array=null,?SqlOrder $order=null,?array $arrayJoin=null) : int
    {
        $this->startTimer();
        if(!$this->checkIfClassExists())
        {
            return 0;
        }  
        
        $this->last_query_nb=$this->makeQuery($array,$order,null,"",$arrayJoin);   
        if($this->cache && $this->isQueryCached("getNb",$this->last_query_nb))
        {           
            return $this->makeNbByCache();
        }
        
        $cache=false;
        if($this->cache)
        {
            $cache=true;
        }
        $this->cache=false;
        
        $list=$this->getDistinctField($this->class::$ID,$array,$order,null,$arrayJoin);
        $nb=$list->getSize();
        
        if($cache)
        {
            $this->cache=true;
        }       
        if($this->cache)
        {
            $this->makeFileCacheFromInt($nb);
        }

        $this->stopTimer();
        return $nb;
    }
    /**
     * 
     * @param string $fieldName
     * @param array<int,QueryField>|array<int,array<int,QueryField>>|array<int,array<int, array<int, QueryField>>>|null $array
     * @param SqlOrder|null $sqlOrder
     * @param SqlLimit|null $limit
     * @param CacheSqlParameter|null $cacheParam
     * @return ArrayList|null
     */
    public function getIdAndFieldValue(string $fieldName,?array $array=null,?SqlOrder $sqlOrder=null,?SqlLimit $limit=null,?CacheSqlParameter $cacheParam=null) : ?ArrayList
    {
        $this->startTimer();
        if(!$this->checkIfClassExists())
        {
            return null;
        } 
        if(!$this->class->fieldNameExists($fieldName))
        {
            return null;
        }
        if($sqlOrder==null)
        {
            $sqlOrder=new SqlOrder($fieldName,SqlOrder::$ASC);   
        }
        $sqlOrder->setTable($this->class->getTable_name()); 
        
        $this->last_query=$this->makeQuery($array,$sqlOrder,$limit,"",null);
        if($this->cache && $this->isQueryCached("getIdAndFieldValue",'',$cacheParam))
        {           
            return $this->makeArrayListByCache();
        }
        $arrayTemp=[];
        $list=new ArrayList(); 
        $req = $this->sql->request_SQL($this->last_query);        
        while(($result=$this->sql->fetch_array($req))!=NULL)
        {
            $arrayTemp[]=[$result[$this->class->makeAliasSqlField(KObject::$ID)],$result[$this->class->makeAliasSqlField($fieldName)]];
        } 
        $list->replaceArray($arrayTemp);
        if($this->cache)
        {
            $this->makeFileCacheFromArray($arrayTemp);
        }   
        
        $this->stopTimer();
        return $list;        
        
    }
    
     /**
     * 
     * @param string $fieldName
     * @param array<int,QueryField>|array<int,array<int,QueryField>>|array<int,array<int, array<int, QueryField>>>|null $array
     * @param SqlOrder|null $sqlOrder
     * @param SqlLimit|null $limit
     * @param CacheSqlParameter|null $cacheParam
     * @return array<int,mixed>
     */
    public function getIdAndFieldValueToArrayDouble(string $fieldName,?array $array=null,?SqlOrder $sqlOrder=null,?SqlLimit $limit=null,?CacheSqlParameter $cacheParam=null) : array
    {
        $arrayDouble=[];
        $map=$this->getIdAndFieldValueInMap($fieldName,$array,$sqlOrder,$limit,$cacheParam);
        if(!is_null($map))
        {
            foreach($map as $key=>$value)
            {
                $arrayDouble[]=[$key,$value];
            }
        }
        return $arrayDouble;
    }
    
    /**
     * 
     * @param string $fieldName
     * @param array<int,QueryField>|array<int,array<int,QueryField>>|array<int,array<int, array<int, QueryField>>>|null $array
     * @param SqlOrder|null $sqlOrder
     * @param SqlLimit|null $limit
     * @param CacheSqlParameter|null $cacheParam
     * @return HashMap|null
     */
    public function getIdAndFieldValueInMap(string $fieldName,?array $array=null,?SqlOrder $sqlOrder=null,?SqlLimit $limit=null,?CacheSqlParameter $cacheParam=null) : ?HashMap
    {
        $this->startTimer();
        if(!$this->checkIfClassExists())
        {
            return null;
        } 
        if(!$this->class->fieldNameExists($fieldName))
        {
            return null;
        }
        if($sqlOrder==null)
        {
            $sqlOrder=new SqlOrder($fieldName,SqlOrder::$ASC);   
        }
        $sqlOrder->setTable($this->class->getTable_name()); 
        
        $this->last_query=$this->makeQuery($array,$sqlOrder,$limit,"",null);
        if($this->cache && $this->isQueryCached("getIdAndFieldValueInMap",'',$cacheParam))
        {           
            return $this->makeHashMapByCache();
        }
        $arrayTemp=[];
        $map_class=new HashMap(); 
        $req = $this->sql->request_SQL($this->last_query);        
        while(($result=$this->sql->fetch_array($req))!=NULL)
        {
            $arrayTemp[$result[$this->class->makeAliasSqlField(KObject::$ID)]]=$result[$this->class->makeAliasSqlField($fieldName)];
        } 
        $map_class->replaceArray($arrayTemp);        
        
        if($this->cache)
        {
            $this->makeFileCacheFromArray($arrayTemp);
        }          
        $this->stopTimer();
        return $map_class;           
    }    
    /**
     * 
     * @param string $fieldName
     * @param SqlOrder|null $order
     * @param SqlLimit|null $limit
     * @param CacheSqlParameter|null $cacheParam
     * @return ArrayList
     */
    public function getDistinctFieldLastQuery(string $fieldName,?SqlOrder $order=null,?SqlLimit $limit=null,?CacheSqlParameter $cacheParam=null) : ArrayList
    {
        return $this->getDistinctField($fieldName,$this->last_query_array,$order,$limit,$this->last_query_join,$cacheParam);
    }    
    /**
     * 
     * @param string $fieldName
     * @param array<int,QueryField>|array<int,array<int,QueryField>>|array<int,array<int, array<int, QueryField>>>|null $array
     * @param SqlOrder|null $sqlOrder
     * @param SqlLimit|null $limit
     * @param array<int,mixed>|null $arrayJoin
     * @param CacheSqlParameter|null $cacheParam
     * @return ArrayList
     */
    public function getDistinctField(string $fieldName,?array $array=null,?SqlOrder $sqlOrder=null,?SqlLimit $limit=null,?array $arrayJoin=null,?CacheSqlParameter $cacheParam=null) : ArrayList
    {
        $this->startTimer();
        if(!$this->checkIfClassExists())
        {
            return new ArrayList();
        } 
        if(!$this->class->fieldNameExists($fieldName))
        {
            return new ArrayList();
        }        
        if($sqlOrder==null)
        {
            $sqlOrder=new SqlOrder($fieldName,SqlOrder::$ASC);   
        }
        $sqlOrder->setTable($this->class->getTable_name());
        
        $this->last_query=$this->makeQuery($array,$sqlOrder,$limit,$fieldName,$arrayJoin);
        //KDebugger::getInstance()->dump($this->last_query);
        
        if($this->cache && $this->isQueryCached("getDistinctField","",$cacheParam))
        {         
            return $this->makeArrayListByCache();
        }
        $arrayTemp=[];
        $list=new ArrayList(); 
        $req = $this->sql->request_SQL($this->last_query);        
        while(($result=$this->sql->fetch_array($req))!=NULL)
        {
            $arrayTemp[]=$result[$fieldName];
        }
        $list->replaceArray($arrayTemp);
        if($this->cache)
        {
            $this->makeFileCacheFromArray($arrayTemp);
        }        
        $this->stopTimer();
        return $list;
    }
    /**
     * 
     * @param string $fieldName
     * @param array<int,QueryField>|array<int,array<int,QueryField>>|array<int,array<int, array<int, QueryField>>>|null $array
     * @param SqlOrder|null $sqlOrder
     * @param SqlLimit|null $limit
     * @param array<int,mixed>|null $arrayJoin
     * @return ArrayList
     */
    public function getDistinctFieldAndCount(string $fieldName,?array $array=null,?SqlOrder $sqlOrder=null,?SqlLimit $limit=null,?array $arrayJoin=null) : ArrayList
    {
        $this->startTimer();
        if(!$this->checkIfClassExists())
        {
            return new ArrayList();
        } 
        if(!$this->class->fieldNameExists($fieldName))
        {
            return new ArrayList();
        }        
        if($sqlOrder==null)
        {
            $sqlOrder=new SqlOrder($fieldName,SqlOrder::$ASC);   
        }
        $sqlOrder->setTable($this->class->getTable_name());
        
        $this->count=true;
        
        $this->last_query=$this->makeQuery($array,$sqlOrder,$limit,$fieldName,$arrayJoin);
        if($this->cache && $this->isQueryCached("getDistinctField"))
        {           
            return $this->makeArrayListByCache();
        }
        $arrayTemp=[];
        $list=new ArrayList(); 
        $req = $this->sql->request_SQL($this->last_query);        
        while(($result=$this->sql->fetch_array($req))!=NULL)
        {
            $arrayTemp[]=[$result[$fieldName],$result[$this->sql->countKeyWord()]];
        }
        $list->replaceArray($arrayTemp);
        if($this->cache)
        {
            $this->makeFileCacheFromArray($arrayTemp);
        }        
        $this->stopTimer();
        return $list;
    }
    /**
     * 
     * @param array<int,QueryField>|array<int,array<int,QueryField>>|array<int,array<int, array<int, QueryField>>>|null $array
     * @return int
     */
    public function deleteByArray(?array $array=NULL) : int
    {
        $this->startTimer();
        if(!$this->checkIfClassExists())
        {
            return -1;
        }        
        $count_deleted=0;
        $this->last_query=$this->makeQuery($array,null,null,"id");

        $list=new ArrayList(); 
        $req = $this->sql->request_SQL($this->last_query);
        while(($result=$this->sql->fetch_array($req))!=NULL)
        {
            $list->add($result[$this->className::$ID]);
        }
        //$this->sql->disconnect_DB();
        $classMother=new $this->className();                  
        for ($i = 0; $i < $list->getSize(); $i++) 
        {
            $class=clone $classMother;           
            $class->initById($list->get($i));
            if($class->delete())
            {
                $count_deleted++;
            }
        }    
        $this->stopTimer();        
        return $count_deleted;         
    } 
    
    
//    public function getByJoinArray(array $joinArray,$array=null,SqlOrder $order=null,SqlLimit $limit=null) /*PHP7-2: ?ArrayList */
//    {      
//        $this->startTimer();
//        if(!$this->checkIfClassExists())
//        {
//            return null;
//        }      
//        $this->select_all_fields=true;
//        $this->last_query=$this->makeQuery($array,$order,$limit); 
//        $list_class=new ArrayList(); 
//        $this->results = $this->sql->request_SQL($this->last_query);
//        
//        //$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
//        // PATCH MYSQL
//        //$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
//        $initForeignKObjects=false;
//        if($this->initForeignObject&&$this->sql->getEngine_type()!=Sql::$MYSQL)
//        {
//            $initForeignKObjects=true;
//        }
//        //$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
//        
//        while(($result=$this->sql->fetch_array($this->results))!=NULL)
//        {
//            /* @var $class KObject */
//            $class=new $this->className();
//            if(!$class->initByResultSql($result,$this->sql,$initForeignKObjects))
//            {
//                echo $class->getKerror();
//                return null;
//            }
//            
//            //$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
//            // PATCH MYSQL
//            //$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
//            else if($this->initForeignObject&&$this->sql->getEngine_type()==Sql::$MYSQL)
//            {
//                $class->initAllForeignKeyKObjects($this->sql);
//            }
//            //$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
//            
//            $list_class->add($class);
//        }
//        
//        $this->stopTimer();
//        return $list_class; 
//    }    
    
    
    //##########################################################################
    // PRIVATE
    //##########################################################################
    /**
     * 
     * @param array<int,QueryField>|array<int,array<int,QueryField>>|array<int,array<int, array<int, QueryField>>>|null $array
     * @param SqlOrder|null $order
     * @param SqlLimit|null $limit
     * @param string $oneField
     * @param array<int,mixed>|null $arrayJoin
     * @return string
     */
    private function makeQuery(?array $array=NULL,?SqlOrder $order=null,?SqlLimit $limit=null,string $oneField="",?array $arrayJoin=null) : string
    {
        $this->last_query_array=$array;
        $this->last_query_order=$order;
        $this->last_query_limit=$limit;
        $this->last_query_join=$arrayJoin;
        
        $sql_limit="";
        if($limit!=null)
        {
            $sql_limit=$this->sql->makeLimitString($limit);
        }
        $sql_order="";
        $sql_group_by="";
        if($order!=null)
        {
            $order->setTable($this->class->getTable_name());
            $sql_order=$this->sql->makeOrderString($order);
            if($oneField!=""&&!$order->isOrderByCount()&&$oneField!=$order->getField())
            {
                $oneField.=",".$order->getAllFieldsOrder();          
            }
        }
        
        $first_add_request=true;
        $sql_add_request="";  
        $this->sql_add_request_temp=array();
        $string=$this->checkQueryField($array,DbList::$CONDITION_AND);
        if($string!="")
        {
            $first_add_request=false;
            $this->sql_add_request_temp[]=$string;
        }        
        
        $full_outer_join="";
        if(is_array($arrayJoin) && count($arrayJoin))
        {
            //echo "1<br />";
            $count=0;
            foreach($arrayJoin as $join)
            {       
                //echo "2<br />";
                if(!is_null($join) && $join instanceof SqlJoinKlink)
                {
                    $count++;
                    //echo "3<br />".$this->className."//".$join->getJoinString($this->className,$this->sql)."<br />";
                    
                    $full_outer_join.=" ".$join->getJoinString($this->className,$this->sql)." ";
                    $string=$this->checkQueryField($join->getArrayQueryField(),DbList::$CONDITION_AND);
                    if($string!="")
                    {
                        //echo "4<br />".$string;
                        if($first_add_request)
                        {
                            $first_add_request=false;
                        }
                        else
                        {
                            $string=" AND ".$string;
                        }
                        $this->sql_add_request_temp[]=" ".$string;
                    }         
                }
            }
                }
               
        //echo "COUNT ".count($this->sql_add_request_temp)." // ".$this->sql_add_request_temp[0]."<br />";
        if(count($this->sql_add_request_temp)>0)
        {
            for ($i = 0; $i < count($this->sql_add_request_temp); $i++)
            {
                $sql_add_request.=$this->sql_add_request_temp[$i];
            }       
        } 
        
        //$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
        // PATCH MYSQL
        //$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
        $initForeignObject=false;
        if($this->initForeignObject&&!$this->isSQLtypeMySQL())
        {
            $initForeignObject=$this->initForeignObject;
        }
        //$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
        $query="";
        if($this->count)
        {
            $sql_group_by=" GROUP BY ".$this->sql->quoteString($oneField);
            $query=$this->sql->getQuerySelectDistinctAndCount($this->class->getTable_name(),$oneField,$sql_add_request,$full_outer_join)." ".$sql_group_by." ".$sql_order.' '.$sql_limit;
        }
        else if($oneField)
        {
            //KDebugger::getInstance()->dump("oneField");
            $query=$this->sql->getQuerySelectDistinct($this->class->getTable_name(),$oneField,$sql_add_request,$full_outer_join)." ".$sql_order.' '.$sql_limit;
        }
        else
        {
            $query=$this->class->getQuerySelectAllById($this->sql,$sql_add_request,$initForeignObject,$full_outer_join).' '.$sql_order.' '.$sql_limit;
        }
        
        if($this->debug)
        {
            KDebugger::getInstance()->dump($query,$this->className);
            //echo $query.'<br />';
        }
        return $query;               
    }
 
    /*
MYSQL is case insensitive
LIKE 
     * LIKE BINARY
     
      
POSTGRES is case sensitive
LIKE == LIKE BINARY
     * ILIKE == LIKE
     * unaccent()

     */

    /**
     * 
     * @param array<int,QueryField>|array<int,array<int,QueryField>>|array<int,array<int, array<int, QueryField>>>|null  $array
     * @param string|null $condition
     * @return string
     */
    private function checkQueryField(?array $array,?string $condition) : string
    {
        if(is_array($array) && count($array))
        {
            $sql_add_request_temp=[];
            $sql_add_request_temp[]='(';
            $boolean_one_query_is_ok=false;
            /* var $queryField QueryField|array */               
            foreach ($array as $queryField)
            {            
                /** @phpstan-ignore-next-line */
                if(!is_null($queryField) && ( (is_array($queryField)&&count($queryField)) || $queryField instanceof QueryField ) )
                {
                    $boolean_one_query_is_ok=true;
                    
                    if(count($sql_add_request_temp)>1)
                    {                   
                        //echo "==> OK <br />";
                        $sql_add_request_temp[]=' '.$condition.' ';
                    }                            

                    if(is_array($queryField))
                    {
                        //echo "IS ARRAY<br />";
                        $new_condition=DbList::$CONDITION_OR;
                        if($condition==DbList::$CONDITION_OR)
                        {
                            $new_condition=DbList::$CONDITION_AND;
                        }
                        $string=$this->checkQueryField($queryField,$new_condition);
                        if($string!='')
                        { 
                            $sql_add_request_temp[]=$string;
                        }
                        else
                        {
                            //condition not meet so we remove the AND // OR
                            array_pop($sql_add_request_temp);
                        }
                    }
                    else //if($queryField instanceof QueryField)
                    {
//                        KDebugger::getInstance()->dump($this->className,"TableName");
//                        KDebugger::getInstance()->dump($this->className::$TABLE_NAME,$this->className);
                        $tableName=$this->className::$TABLE_NAME;
                        if($queryField->getTable_name()!="")
                        {
                            $tableName=$queryField->getTable_name();
                        }
                        
                        if(is_null($queryField->getField_value()))
                        {
                            if($queryField->getField_type_search()==QueryField::$NOT_LIKE || $queryField->getField_type_search()==QueryField::$NOT_EQUAL)
                            {
                                $sql_add_request_temp[]=' '.$this->sql->getPrefixLikeSQL($this->sql->quoteString($tableName).'.'.$this->sql->quoteString($queryField->getField_name()),true).' IS NOT NULL';                                
                                //$queryField->getField_accent_sensitive()
                            }
                            else
                            {
                                $sql_add_request_temp[]=' '.$this->sql->getPrefixLikeSQL($this->sql->quoteString($tableName).'.'.$this->sql->quoteString($queryField->getField_name()),true).' IS NULL';                                
                                //$queryField->getField_accent_sensitive()
                            }                           
                        }
                        else if($queryField->getField_type_search()==QueryField::$LIKE_1)
                        {
                            $like=$this->sql->getLikeSQL("'%".$this->sql->real_escape_string($queryField->getField_value())."%'",$queryField->getField_case_sensitive(),$queryField->getField_accent_sensitive());
                            $sql_add_request_temp[]=' '.$this->sql->getPrefixLikeSQL($this->sql->quoteString($tableName).'.'.$this->sql->quoteString($queryField->getField_name()),$queryField->getField_accent_sensitive())." ".$like;      
                        }
                        else if($queryField->getField_type_search()==QueryField::$LIKE_2)
                        {
                            $like=$this->sql->getLikeSQL("'%".$this->sql->real_escape_string($queryField->getField_value())."'",$queryField->getField_case_sensitive(),$queryField->getField_accent_sensitive());
                            $sql_add_request_temp[]=' '.$this->sql->getPrefixLikeSQL($this->sql->quoteString($tableName).'.'.$this->sql->quoteString($queryField->getField_name()),$queryField->getField_accent_sensitive()).' '.$like;      
                        }
                        else if($queryField->getField_type_search()==QueryField::$LIKE_3)
                        {
                            $like=$this->sql->getLikeSQL("'".$this->sql->real_escape_string($queryField->getField_value())."%'",$queryField->getField_case_sensitive(),$queryField->getField_accent_sensitive());
                            $sql_add_request_temp[]=' '.$this->sql->getPrefixLikeSQL($this->sql->quoteString($tableName).'.'.$this->sql->quoteString($queryField->getField_name()),$queryField->getField_accent_sensitive()).' '.$like;      
                        }
                        else if($queryField->getField_type_search()==QueryField::$NOT_LIKE_3)
                        {
                            $like=$this->sql->getNotLikeSQL("'".$this->sql->real_escape_string($queryField->getField_value())."%'",$queryField->getField_case_sensitive(),$queryField->getField_accent_sensitive());
                            $sql_add_request_temp[]=' '.$this->sql->getPrefixLikeSQL($this->sql->quoteString($tableName).'.'.$this->sql->quoteString($queryField->getField_name()),$queryField->getField_accent_sensitive()).' '.$like;      
                        } 
                        else if($queryField->getField_type_search()==QueryField::$WHOLE_WORD)
                        {
                            $like=$this->sql->getWholeWordSQL($this->sql->real_escape_string($queryField->getField_value()),$queryField->getField_case_sensitive(),$queryField->getField_accent_sensitive());
                            $sql_add_request_temp[]=' '.$this->sql->getPrefixLikeSQL($this->sql->quoteString($tableName).'.'.$this->sql->quoteString($queryField->getField_name()),$queryField->getField_accent_sensitive()).' '.$like;                                  
                        }
                        else if($queryField->getField_type_search()==QueryField::$IN_QUERY)
                        {
                            $like=QueryField::$IN_QUERY." (".$queryField->getField_value().")";
                            $sql_add_request_temp[]=' '.$this->sql->getPrefixLikeSQL($this->sql->quoteString($tableName).'.'.$this->sql->quoteString($queryField->getField_name()),$queryField->getField_accent_sensitive()).' '.$like;      
                        }
                        else if ($queryField->getField_type_search() == QueryField::$GEOM_INTERSECT_WKT)
                        {
                            $field = $this->sql->getPrefixLikeSQL($this->sql->quoteString($tableName).'.'.$this->sql->quoteString($queryField->getField_name()), true);
                            $value = $queryField->getField_value();
                            $sql_add_request_temp[] = $this->sql->getGeomIntersectSQL($field, $value);
                        }
                        else if ($queryField->getField_type_search() == QueryField::$GEOM_CONTAINS_WKT)
                        {
                            $field = $this->sql->getPrefixLikeSQL($this->sql->quoteString($tableName).'.'.$this->sql->quoteString($queryField->getField_name()), true);
                            $value = $queryField->getField_value();
                            $sql_add_request_temp[] = $this->sql->getGeomContainsSQL($field, $value);
                        }
                        else if ($queryField->getField_type_search() == QueryField::$GEOM_IS_CONTAINED_WKT)
                        {
                            $field = $this->sql->getPrefixLikeSQL($this->sql->quoteString($tableName).'.'.$this->sql->quoteString($queryField->getField_name()), true);
                            $value = $queryField->getField_value();
                            $sql_add_request_temp[] = $this->sql->getGeomIsContainedSQL($field, $value);
                        }                        
                        else
                        {
                            $sql_add_request_temp[] = ' '.$this->sql->getPrefixLikeSQL($this->sql->quoteString($tableName).'.'.$this->sql->quoteString($queryField->getField_name()), true).' '.$queryField->getField_type_search()."'".$this->sql->real_escape_string($queryField->getField_value_asString())."'";
                        }
                    } 
//                    else
//                    {
//                        //condition not meet so we remove the AND // OR
//                        array_pop($sql_add_request_temp);
//                    }
                }
            } 
            $sql_add_request_temp[]=')';

            
            $string='';
            if($boolean_one_query_is_ok)
            {
                for ($i = 0; $i < count($sql_add_request_temp); $i++)
                {
                    if($sql_add_request_temp!='')
                    {
                        $string.=$sql_add_request_temp[$i];
                    }
                }
            }
            //echo $string."<br />";
            return $string;
        }
        return "";
    }
    
    public function getLast_query() : string
    {
        return $this->last_query;
    }
    
    public function getLast_query_NB() : string
    {
        return $this->last_query_nb;
    }    


    function getInitForeignObject() : bool
    {
        return $this->initForeignObject;
    }

    function setInitForeignObject(bool $initForeignObject) : void
    {
        $this->initForeignObject=$initForeignObject;
    } 
    
    private function isQueryCached(string $type_query,string $last_query="",?CacheSqlParameter $cacheParam=null) : bool
    {
        if($last_query=="")
        {
            $last_query=$this->last_query;
        }
        $this->cache_key=$this->class->getTable_name()."-".$type_query."-".$this->getLastModifiedDateForTablename()."-".convertBoolToStringNumber($this->initForeignObject)."-".md5($last_query);      
        //echo $this->cache_filename."<br />";
        if(!is_null($cacheParam))
        {
            $this->cache_key=$cacheParam->makeCacheKey($this->cache_key);
        }
        return $this->kCache->isValueCached($this->cache_key);
    }
    
    public function getLastModifiedDateForTablename() : string
    {
        if(!$this->checkIfClassExists())
        {
            return "";
        }
        
        if($this->cache && !empty($this->cache_lastModificationforTable)&& !empty($this->cache_numRowsTable))
        {
            // DO NOT COMPUTE
        }
        else
        {
            //$time=new KTimer();
            //$time->start();
            $this->cache_lastModificationforTable=$this->sql->getLastModificationForTable($this->class->getTable_name());
            //$time->stop();
            //KDebugger::getInstance()->dump($time->totalTimeToString(),"getLastModifiedDateForTablename => ".$this->class->getTable_name());
           
            //$time->start();
            $this->cache_numRowsTable=$this->sql->num_rows_table($this->class->getTable_name());    
            //$time->stop();
            //KDebugger::getInstance()->dump($time->totalTimeToString(),"num_rows_table => ".$this->class->getTable_name());
        }
        return str_replace(":","-",str_replace(" ","_",$this->cache_lastModificationforTable."_".$this->cache_numRowsTable));
    }
    /**
     * 
     * @return array<int,mixed> 
     */
    private function makeArrayByCache() : array
    {
        $array=$this->kCache->makeArrayFromCache();
        if(is_array($array))
        {
            return $array;
        }
        return [];
    }
    
    private function makeHashMapByCache() : HashMap
    {
        $map=new HashMap();
        $map->replaceArray($this->makeArrayByCache());
        return $map;
    }
    
    private function makeArrayListByCache() : ArrayList
    {
        $list=new ArrayList();
        $list->replaceArray($this->makeArrayByCache());
        return $list;
    }
    /**
     * 
     * @param array<int,mixed> $arrayTemp
     */
    private function makeFileCacheFromArray(array $arrayTemp) : void
    {
        $this->kCache->makeCacheFromArray($arrayTemp);
    }
    
    private function makeFileCacheFromInt(int $nb) : bool
    {
        return $this->kCache->makeCacheFromInt($nb);
    } 
    
    private function makeIntByCache() : int
    {
        $value=$this->kCache->makeIntFromCache();
        if(!is_null($value))
        {
            return $value;
        }
        return 0;
    }
    
    private function isSQLtypeMySQL() : bool
    {
        if($this->sql->getEngine_type()==Sql::$MYSQL)
        {
            return true;
        }
        elseif($this->sql->getEngine_type()==Sql::$MARIADB)
        {
            return true;
        }  
        return false;
    }
    
    private function makeNbByCache() : int
    {
        $nb=(int)$this->makeIntByCache();
        return $nb;
    }
    
    public function countKeyWord() : string
    {
        return $this->sql->countKeyWord();
    }
}