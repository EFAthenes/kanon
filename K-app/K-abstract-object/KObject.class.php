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
abstract class KObject
{
    //intern private fields
    private string $identifier_post_get="";
    private bool $is_input_post_get=false;
    /**
     * 
     * @var array<string,mixed>|null
     */
    private ?array $array_modified_field=null; 
    /**
     * 
     * @var array<string,mixed>|null
     */
    private ?array $array_modified_field_error=null; 
    private ?HashMap$mapKField=null;
    private ?HashMap$mapFieldName=null;
    private ?ArrayList $listFieldName=null;
    private bool $initKFields=false;
    /* @var $mapForeignKeyFields HashMap */
    private ?HashMap $mapForeignKeyFields=null;
    private ?HashMap $mapKlinkObjectItems=null;
    private ?HashMap $mapKlinkObjectTablesItems=null;
    private bool $update_date_modified=true;
    private bool $fkObjectsInitialized=false;
    
    // ObligatoryFields
    private ?string $id="";
    private ?string $date_created="";
    private ?string $date_modified="";
    private string $kerror="";
    private string $table_name="";
    private string $alias_sql="";
    private string $alias_prefix="";
    private bool $use_cache=false;
    
    private bool $isGeo=false; 
    private string $fieldGeo="";
    
    //private string $foreign_key_table_characters="fk_id_";
    
    public static string $TABLE_NAME="";
    public static string $ID="id";
    public static string $DATE_CREATED="date_created";
    public static string $DATE_MODIFIED="date_modified";
    
    abstract public function initKFields() : bool;
    abstract protected function initMapFieldName() : bool;  
    
    abstract public function getInputPostValue_Id() : bool;
    abstract public function getInputGetValue_Id() : bool;
    
//    abstract protected function initCustom();
       
    public function getClassName() : string
    {
        $className=get_class($this);       
        if(strlen($className)>2)
        {
            $end = substr($className, -2);
            if(strcmp("Db",$end)===0)
            {
                $className = substr_replace($className ,"",-2);
            }
        }
        return $className;        
    }
    protected function __construct()
    {
        $this->mapKField=new HashMap();
        $this->mapFieldName=new HashMap();
        $this->initMapFieldName();
        $this->listFieldName=$this->mapFieldName->toArrayListNotSort();
        $this->mapForeignKeyFields=new HashMap();
        $this->mapKlinkObjectTablesItems=new HashMap();
        $this->mapKlinkObjectItems=new HashMap();
        $this->alias_prefix="fk_alias_";
    }
    public function __destruct()
    {
    }
    
    public function __clone()
    {
        $this->array_modified_field=null; 
        $this->array_modified_field_error=null;         
        $this->mapKField=clone $this->mapKField;
        $this->mapFieldName=clone $this->mapFieldName;
        $this->listFieldName= $this->mapFieldName->toArrayListNotSort();      
        $this->mapForeignKeyFields=clone $this->mapForeignKeyFields;
        $this->mapKlinkObjectTablesItems=clone $this->mapKlinkObjectTablesItems;
        $this->mapKlinkObjectItems=clone $this->mapKlinkObjectItems;      
    }
        
    //##########################################################################
    //##########################################################################
    //##########################################################################
    //##########################################################################
    // KlinkOjects Methods
    //##########################################################################  
//    public function getMapOfKlinkOjects($className,$fk_field) : HashMap
//    {     
//        //KLink_Epoque_Fiche::class  => $className
//        //KLink_Epoque_Fiche::$FK_ID_FICHE_LIVRE  => $fk_field
//        $dBList=new DbList($className);
//        //$dBList->setDebug(true);
//        $map=$dBList->getByArrayInIdMap(array(new QueryField($fk_field,$this->getId(),QueryField::$EQUAL)));
//        return $map;
//    } 
    
    // For fk_id fields
    public function getMapKlinkObjectItems() : HashMap
    {
        return $this->mapKlinkObjectItems;
    }    
    public function addKlinkObjectItem(KLinkObjectItem $item) : bool
    {
        return $this->mapKlinkObjectItems->put($item->className,$item);
    } 
    public function makeKlinkObjectItem(mixed $the_className,mixed $the_fk_id_field) : bool
    {
        $className="".$the_className;
        $fk_id_field="".$the_fk_id_field;
        if(!empty($className)&&!empty($fk_id_field))
        {
            $linkObjectItem= new KLinkObjectItem($className,$fk_id_field);
            return $this->addKlinkObjectItem($linkObjectItem);
        }
        return false;
    }
    public function getKlinkObjectItem(mixed $className) : ?KLinkObjectItem
    {
        return $this->mapKlinkObjectItems->get($className);
    }
    
    // For external Tables
    public function getMapKlinkObjectTablesItems() : HashMap
    {
        return $this->mapKlinkObjectTablesItems;
    }    
    public function addKlinkObjectTableItem(KLinkObjectItem $item) : bool
    {
        return $this->mapKlinkObjectTablesItems->put($item->className,$item);
    } 
    public function makeKlinkObjectTableItem(string $className, string $fk_id_field) : bool
    {
        $linkObjectItem= new KLinkObjectItem($className,$fk_id_field);
        return $this->addKlinkObjectTableItem($linkObjectItem);
    }
    public function getKlinkObjectTableItem(string $className) : KLinkObjectItem
    {
        $item=$this->mapKlinkObjectTablesItems->get($className);
        if(is_null($item))
        {
            $item=new KLinkObjectItem(null,null);
        }
        return $item;
    }
    
    public function getMapOfKlinkTableOjects(string $className,?SqlOrder $order=null,?string $filterField=null,?string $filterValue=null) : HashMap
    {
        $map=new HashMap();
        if(!isInteger($this->getId()))
        {
            return $map;
        }       
        
        $item=$this->getKlinkObjectTableItem($className);
        if(!is_null($item->className))
        {
            $dBList=new DbList($item->className);
            $dBList->setCache($this->use_cache);
            $arrayOfQueryField=array(new QueryField($item->fk_id_field,$this->getId(),QueryField::$EQUAL));
            if(!is_null($filterField) && !is_null($filterValue))
            {
                $arrayOfQueryField[]=new QueryField($filterField,$filterValue,QueryField::$EQUAL);
            }
            $map=$dBList->getByArrayInIdMap($arrayOfQueryField,$order,null);//array(new QueryField($item->fk_id_field,$this->getId(),QueryField::$EQUAL))); 
        }
        else
        {
//           echo "ITEM  NULL =>".$className." // ".$item->toString()."<br />";
//           exit();
        }
      
        return $map; 
    }
    
    public function getMapOfForeignTableOjects(string $className,string $foreign_key_id,?SqlOrder $order=null) : HashMap
    {
        $dBList=new DbList($className);
        //$foreign_key_id=$this->foreign_key_table_characters.strtolower($className);
        $arrayOfQueryField=array(new QueryField($foreign_key_id,$this->getId(),QueryField::$EQUAL));
        $map=$dBList->getByArrayInIdMap($arrayOfQueryField,$order,null);
        return $map; 
    }    


    //##########################################################################
    //##########################################################################
    

    
//    function getKlinkForeignKeyKObject(string $fieldName) /*PHP7-2 : ?KObject */
//    {
//        $object=$this->mapKlinkForeignKeyFields->get($fieldName);
//        return $object;
//    }
    


    
    
    //##########################################################################
    //##########################################################################
    //##########################################################################
    //##########################################################################
    // ForeignKeyKObject Methods
    //##########################################################################    
    function getForeignKeyKObject(string $fieldName) : ?KObject 
    {
        $object=$this->mapForeignKeyFields->get($fieldName);
        return $object;
    }
    
    function initForeignKeyKObject(string $fieldName) : bool
    {
        $status=false;
        /* @var $object KObject */
        $object=$this->mapForeignKeyFields->get($fieldName);
        $field_value=$this->getFieldValue($fieldName);
        if(!is_null($object))
        {
            //echo "!NULL  => ".$field_value."\n";
            if($object->initById($field_value))
            {
                $status=true;
            }
            else 
            {
                $this->setKerror("KObject::initForeignKeyKObject() => Field '".$fieldName."' not init by '".$field_value."' => ".$object->getKerror());
            }
        }
        else
        {
            $this->setKerror("KObject::initForeignKeyKObject() => Field '".$fieldName."' not found");
        }
        return $status;
    }
    private function addForeignKeyField(string $fieldName) : bool
    {
        $status=false;
        // fk_id_tablename_variable
        $array=explode("_",$fieldName,4);
        if(count($array)>= 3 && $array[0]==="fk"&&$array[1]==="id")
        {
            //echo $array[2]."\n";
            $classname=ucfirst($array[2]);
            if(class_exists($classname))
            {
                $object=new $classname();
                if($object instanceof KObject)
                {
                    $this->mapForeignKeyFields->put($fieldName,$object);
                    $status=true;
                }
                else
                {
                    $this->setKerror("KObject::addForeignKeyField() => Field '".$fieldName."' Class not type KObject => '".$classname."' ");
                }
            }
            else
            {
                $this->setKerror("KObject::addForeignKeyField() => Field '".$fieldName."' Class not found => '".$classname."' ");
            }
        }
        else
        {
            $this->setKerror("KObject::addForeignKeyField() => Field '".$fieldName."' not Foreign Key");
        }
        return $status;
    }
    
    public function initAllForeignKeyKObjects(?Sql $sql=null,bool $debug=false) : bool
    {
        //if($debug)KDebugger::getInstance()->dump($sql,"initAllForeignKeyKObjects 1");
        $sql_null=false;
        if(is_null($sql))
        {
            $sql=new Sql();
            $sql->connect_DB();
            $sql_null=true;
        }
        $status=true;
        /* @var $object KObject */
        /* @var $list ArrayList */
        $list=$this->mapForeignKeyFields->toKeyArrayList();
        //KDebugger::getInstance()->dump($list,"initAllForeignKeyKObject Lists");
        //if($debug)KDebugger::getInstance()->dump($list,"initAllForeignKeyKObjects 2");
        foreach($list as $key)
        {
            //KDebugger::getInstance()->dump($key,"initAllForeignKeyKObjects");
            if($object=$this->mapForeignKeyFields->getKObject($key))
            {               
                $object->setId($this->getFieldValue($key));
                //KDebugger::getInstance()->dump($object,"KObjects");
                if(!$object->initAllByBdSql($sql))
                {
                    $this->setKerror("KObject::initAllForeignKeyKObjects() => Field '".$object->getTable_name()."' not init => ".$object->getKerror());
                    $status=false;
                }
            }
        }
        if($sql_null)
        {
            $sql->disconnect_DB();
        }        
        return $status;
    }
    function getMapForeignKeyFields() : HashMap
    {
        return $this->mapForeignKeyFields;
    }
    
    //##########################################################################
    //##########################################################################
    //##########################################################################
    //##########################################################################    
    
    
    public function getMapKField() : HashMap
    {
        return $this->mapKField;
    }
    public function getMapFieldName() : HashMap
    {
        return $this->mapFieldName;
    }
    function getListFieldName() : ArrayList
    {
        return $this->listFieldName;
    }

    function setListFieldName(ArrayList $listFieldName) : void
    {
        $this->listFieldName=$listFieldName;
    }
    public function addFieldName(string $fieldName) : bool
    {
        if($this->mapFieldName->put($fieldName,$fieldName))
        {
            return true;
        }
        $this->setKerror("KObject::addFieldName() => Field '".$fieldName."' already present.");
        return false;
    }
    public function fieldNameExists(string $fieldName) : bool
    {
        return $this->mapFieldName->keyExists($fieldName);
    }

        
    public function getKField(mixed $field_name) : KField
    {
        $name="".$field_name;
        $field=$this->mapKField->get($name);
        if(!is_null($field))
        {
            return $field;
        }
        return new KFieldUnKnown();
    }
    public function addKField(KField $field) : bool
    {
        return $this->mapKField->put($field->getName(),$field);
        //return $this->mapField->put($field->getName(),$field);
    }
    public function addField(mixed $the_type,mixed $value,mixed $the_name) : bool
    {
        $type="".$the_name;
        $name="".$the_name;
        if(empty($type)||empty($name))
        {
            return false;
        }
        //KField::$TYPE => KField::$DATETIME ,KField::$VALUE => "201-05-05 05:05:05"
        $options=[
            KField::$TYPE => $type, 
            KField::$VALUE => $value, 
            KField::$NAME => $name];
        $field=KField::makeByArray($options);
        if($field instanceof KFieldUnKnown)
        {
            return false;
        }
        else
        {
            return $this->addKField($field);
        }
    }
    public function setKFieldValue(mixed $name,mixed $value) : bool
    {
        if(!$this->initKFields)
        {
            $this->initKFields();
        }
        $kField=$this->getKField($name);
        
        if(!($kField instanceof KFieldUnKnown))
        {            
            //KDebugger::getInstance()->dump($name."=>".$value,"Yo");
            if($kField->set($value))
            {
//                echo "YES ".$name." // ".$value." <br /><br />";
//                echo $kField->toString();
                return true;
            }
            else
            {
//                echo $kField->getKerror();
//                exit();
                $this->setKerror($kField->getKerror());
                return false;
            }        
        }
        $this->setKerror("KField name not found =>".$name);
        return false;
    }
    
    public function insert() : bool
    {
        $sql=Sql::getInstance();
        if(is_null($sql))
        {
           return false; 
        }        
        $status=$this->insertSql($sql);
        return $status;
    }
    public function insertSql(Sql $sql) : bool
    {
        $status=false;
        $this->makeDateCreation();   
        $this->fillKFields();
        $request="INSERT INTO ".$sql->quoteString(static::$TABLE_NAME)." (";
        for($i=0;$i<$this->listFieldName->getSize();$i++)
        {
            $fieldName=$this->listFieldName->get($i);
            $request.=$sql->quoteString($fieldName);
            if(($i+1)<$this->listFieldName->getSize())
            {
                $request.=",";
            }
        } 
        
        $update_sequence=false;
        // GET ID VALUE
        $kField=$this->getKField(static::$ID);
        $id_value=$kField->get();
        $default_id_value=0;
        //echo "ID_VALUE ==>".$id_value."!! \n\n";
        if(is_null($id_value) || $id_value<=0)
        {
            //ECHO "DEFAULT VALUE !!";
            $id_value=$sql->getDefault_insert_id();
        }
        else
        {
            $default_id_value=$id_value;
            $id_value=$this->getValueForDb($this->listFieldName->get(0),$sql);
            //CHECK IF ID ALREADY EXISTS AND UPDATE SEQUENCE
            //------------------------------------------------------------------
            $update_sequence=true;
        }
        //default       
        $request.="
)VALUES(
".$id_value.",";
        for($i=1;$i<$this->listFieldName->getSize();$i++)
        {
            //$fieldMethodGet="get".ucfirst($this->listFieldName->get($i));
            $request.=$this->getValueForDb($this->listFieldName->get($i),$sql);
            if(($i+1)<$this->listFieldName->getSize())
            {
                $request.=",";
            }
        } 
        $request.=");";

        //echo "\n\n".$request."\n\n";

        if($sql->request_SQL($request)!=NULL)
        {
            $status=true;
            if($update_sequence)
            {
                // UPDATE AUTO INCREMENT
                $sql->updateAutoincrementFieldToMax(static::$TABLE_NAME,static::$ID);                
            }
            else
            {
                $this->setId($sql->getLastId());
            }
        }
        else
        {
            //$this->setKerror($sql->getError());//."//".$request);
            $this->setKerror($sql->getError());
        }
        return $status;
    }    
    private function getValueForDb(string $fieldName,Sql $sql) : string
    {       
        if($this->initKFields)
        {
            $kField=$this->getKField($fieldName);
//            if("is_private"==$fieldName)
//            {
//                KDebugger::getInstance()->dump($kField, "1getValueFor Kfield=> ".$fieldName);
//                KDebugger::getInstance()->dump($kField->getValueForSql($sql), "1getValueForDb => ".$fieldName);
//            }
            return "".$kField->getValueForSql($sql);
        }
        else
        {
            $fieldMethodGet="get".ucfirst($fieldName)."ForSql";
            //KDebugger::getInstance()->dump($this->$fieldMethodGet($sql), "2 getValueForDb => ".$fieldName);
            return $this->$fieldMethodGet($sql);
        }
    }
    public function updateInBd() : bool
    {
        $sql=Sql::getInstance();
        if(is_null($sql))
        {
           return false; 
        }
        return $this->updateInBdSql($sql);        
    }
    
    public function updateInBdSql(Sql $sql) : bool
    {
        //KDebugger::getInstance()->dump("updateInBdSql","updateInBdSql");
        $status=false;
        if($this->update_date_modified)
        {       
            $this->makeDateModification();  
        }
        $this->fillKFields();
        $request="UPDATE ".$sql->quoteString(static::$TABLE_NAME)." SET ";
        for($i=1;$i<$this->listFieldName->getSize();$i++)
        {
            $fieldName=$this->listFieldName->get($i);
            $request.=$sql->quoteString($fieldName)."=".$this->getValueForDb($fieldName,$sql)."";
            if(($i+1)<$this->listFieldName->getSize())
            {
                $request.=",";
            }
        } 
        $request.="
WHERE ".$sql->quoteString(static::$TABLE_NAME).".".$sql->quoteString(static::$ID)." ='".$sql->real_escape_string("".$this->getId())."';";
        
        //KDebugger::getInstance()->dump($request,"Query");

        if($sql->request_SQL($request)!=NULL)
        {
            $status=true;
        }
        else
        {
            $this->setKerror($sql->getError());
        }  
        return $status;
    }
    
     
    protected function getTablesForRequest(Sql $sql,bool $fKobject=true) : string
    {
        $tableString=$sql->quoteString($this->getTable_name())."AS".$sql->quoteString($this->getAlias_sql());
        if($fKobject)
        {
            $tableStringFKobject="";
            $map=$this->getMapForeignKeyFields();
            /* @var $kObject KObject */
            foreach($map as $fk_fieldName => $kObject)
            {
                $tableStringFKobject.=" FULL OUTER JOIN ".$sql->quoteString($kObject->getTable_name())." AS ".$sql->quoteString($kObject->getAlias_sql());
                $tableStringFKobject.=" ON ".$sql->quoteString($this->getAlias_sql()).".".$sql->quoteString($fk_fieldName)."=".$sql->quoteString($kObject->getAlias_sql()).".".$sql->quoteString(KObject::$ID);
            }
            $tableString.=$tableStringFKobject;            
        }
        return $tableString;
    }
    private function getWhereForRequestId(Sql $sql,mixed $id) : string
    {
        $whereString=$sql->quoteString($this->getAlias_sql()).".".$sql->quoteString(KObject::$ID)."=".$sql->real_escape_string("".$id);
        return $whereString;
    } 
 
    public function initById(mixed $id, bool $fKobject=true) : bool
    {       
        $sql=Sql::getInstance();
        if(is_null($sql))
        {
            return false;
        }
        return $this->initByIdSql($id,$sql,$fKobject);        
    }   
    
    public function initByIdSql(mixed $id,Sql $sql, bool $fKobject=true) : bool
    {      
        if(is_null($id))
        {
            $this->setKerror("Kobject::initByIdSql() => id is null ");
            return false;
        }
        else if(!isInteger($id))
        {
            if(is_object($id))
            {
                $this->setKerror("Kobject::initByIdSql() => id is an object not an integer ");
            }
            else
            {
                $this->setKerror("Kobject::initByIdSql() => id(".$id.") is not an integer ");
            }
            return false;
        }
        $query=$sql->getQuerySelectAllById( $this->getTablesForRequest($sql,$fKobject),
                                            $this->getQuerySelectForAlias($sql,$fKobject,true),
                                            $this->getWhereForRequestId($sql,$id));
        //echo $query;
        $status=$this->fillAttributes($query,$sql,$fKobject); 
        return $status;        
    } 
      
    public function getQuerySelectForAlias(Sql $sql,bool $fKobject=true,bool $distinct=false) : string
    {
        $querySelect="";
        for($i=0;$i<$this->listFieldName->getSize();$i++)
        {
            if($i>0)
            {
                $querySelect.=',';
            }
            else if($distinct)
            {
                $querySelect="Distinct ";
            }
            //echo $this->isGeo."//".$this->fieldGeo."||".$this->listFieldName->get($i)."<br />";
            if($this->isGeo && $this->fieldGeo==$this->listFieldName->get($i))
            {
                $querySelect.=$sql->geoFunctionToString($sql->quoteString($this->alias_sql).'.'.$sql->quoteString($this->listFieldName->get($i)));
            }
            else
            {
                $querySelect.=$sql->quoteString($this->alias_sql).'.'.$sql->quoteString($this->listFieldName->get($i));
            }
            $querySelect.=' AS '.$sql->quoteString($this->alias_sql.'_'.$this->listFieldName->get($i));                
        }
        if($fKobject)
        {
            $map=$this->getMapForeignKeyFields();
            /* @var $kObject KObject */
            foreach($map as $fk_fieldName => $kObject)
            {
                if($querySelect!="")
                {
                    $querySelect.=',';
                }
                $querySelect.=$kObject->getQuerySelectForAlias($sql,false);
            }  
        }   
        //KDebugger::getInstance()->dump($querySelect,"getQuerySelectForAlias");
        return $querySelect;
    }
        
    public function getQuerySelectAllById(Sql $sql,string $whereString="",bool $fKobject=true,string $joinString="") : string
    {
        $querySelect=$this->getQuerySelectForAlias($sql,$fKobject,true);
        $queryFrom=' FROM '.$this->getTablesForRequest($sql,$fKobject);
        $queryWhere="";
        
        $queryWhere=' WHERE '.$sql->quoteString($this->getTable_name()).".".$sql->quoteString(KObject::$ID).' IS NOT NULL ';
        
        if($whereString!='')
        {
            $queryWhere.=' AND '.$whereString;
        }
        $query='SELECT '.$querySelect.' '.$queryFrom.' '.$joinString.' '.$queryWhere;
        return $query;
    }
    
    /**
     * 
     * @param array<string,mixed> $result
     * @param Sql $sql
     * @param bool $fKobject
     * @return bool
     */
    public function initByResultSql(array $result,Sql $sql, bool $fKobject=true) : bool
    {       
        if($result!=null)
        {
            return $this->fillAttributesResult($result,$sql,$fKobject);        
        }
        return false;
    } 
    protected function fillAttributes(string $query,Sql $sql, bool $fKobject=true) : bool
    {
        $this->fkObjectsInitialized=$fKobject;
        //echo $query;
        $status=false;
        $req = $sql->request_SQL($query);
        //var_dump($req);
        if(($result=$sql->fetch_array($req))!=NULL)
        {
            //echo "INSIDE <br />";
            $status=$this->fillAttributesResult($result,$sql,$fKobject);
        }
        else
        {
            if($sql->getError()=="")
            {
                $this->setKerror("NO RESULT FOUND!");
            }
            else
            {
                $this->setKerror($sql->getError());
            }
        }
        return $status;
    }    
    
    public function delete() : bool
    {
        $status=false;
        $sql=Sql::getInstance();
        if(is_null($sql))
        {
           return false; 
        }
        
        //$sql->connect_DB();
        $query=$sql->getQueryDeleteById($this->getTable_name(),"".$this->getId());
        if($sql->request_SQL($query)!=NULL)
        {
            $status=true;
        }
        else
        {
            $this->setKerror($sql->getError());
        }
        //$sql->disconnect_DB();
        return $status;
    }   
    
    public function fusionTheObject(mixed $id) : bool
    {
        return false;
    }
    
    public function initByPostId() : bool
    {
        if($this->getInputPostValue_Id())
        {
            return $this->initById($this->getId());
        }
        return false;
    }
    
    public function initByGetId() : bool
    {
        if($this->getInputGetValue_Id())
        {
            return $this->initById($this->getId());
        }
        return false;
    } 
    public function initAllByBd(mixed $id="") : bool
    {
        $status=false;
        if($id===""&& $this->getId()<=0)
        {
            $status=false;
        }    
        else if(!empty($id))
        {
            $status=$this->initById($id,true);
        }
        else if($this->getId()>0)
        {
            $status=$this->initById($this->getId(),true);
        }
        if($status)
        {
            $sql=Sql::getInstance();
            $status=$this->initAllForeignKeyKObjects($sql,true);
            //KDebugger::getInstance()->dump($status,"initAllByBd");
        }
        return $status;
    }
    public function initAllByBdSql(Sql $sql,mixed $id="") : bool
    {
        $status=false;
        if($id===""&&$this->getId()<=0)
        {
            $status=false;
        }    
        else if(!empty($id))
        {
            $status=$this->initByIdSql($id,$sql);
        }
        else if($this->getId()>0)
        {
            $status=$this->initByIdSql($this->getId(),$sql);
        }
        if($status)
        {
            $status=$this->initAllForeignKeyKObjects($sql);
        }
        return $status;
    }      
    public function deleteAll() : bool
    {
        //echo "deleteAll "."<br />";
        $errorString="";
        $this->setKerror("");
        //DELETE KLINKS
        /* @var $item KLinkObjectItem*/
        foreach ($this->mapKlinkObjectTablesItems as $item)
        {       
            if(!is_null($item)&&!is_null($item->className)&&!is_null($item->fk_id_field))
            {
                $dBList=new DbList($item->className);
                $arrayOfQueryField=array(new QueryField($item->fk_id_field,$this->getId(),QueryField::$EQUAL));
                $map=$dBList->getByArray($arrayOfQueryField);
                
                /* @var $kobject KObject */
                foreach ($map as $kobject)
                {
                    //echo "DELETE KLINK ".$kobject->getId()."<br />";
                    if(!$kobject->deleteAll())
                    {
                        $errorString.=$kobject->getKError();
                    }
                }
            }     
        }

        if($errorString!="")
        {
            $this->setKerror($errorString);
            return false;
        }       
        return $this->delete();
    }
    public function updateInBdAll() : bool
    {
        return $this->updateInBd();
    }

    public function getTable_name() : string
    {
        return $this->table_name;
    }

    function setTable_name(string $table_name) : void
    {
        static::$TABLE_NAME=$table_name;
        $this->table_name=$table_name;
    }
    
    function getAlias_sql() : string
    {
        return $this->alias_sql;
    }

    function setAlias_sql(mixed $alias_sql) : void
    {
        $this->alias_sql="".$alias_sql;
    }

    
    /**
     * 
     * @return array<string,mixed>
     */
    public function getArrayModifications():array
    {
        if($this->array_modified_field===null)
        {
            $this->array_modified_field=array();
        }
        return $this->array_modified_field;
    }
    /**
     * 
     * @return array<string,mixed>
     */
    public function getArrayModificationsError()
    {
        if($this->array_modified_field_error===null)
        {
            $this->array_modified_field_error=array();
        }
        return $this->array_modified_field_error;
    }    
    public function getArrayModificationsErrorToString(string $delimiter="<br />") : string
    {
        $string="";
        if(!is_null($this->array_modified_field_error)&& count($this->array_modified_field_error)>0)
        {
            foreach($this->array_modified_field_error as $errorString)
            {
                $string.=$errorString.$delimiter;
            }
        }
        return $string;
    }
    
    public function addToArrayModification(string $field_name,mixed $field_value) : void
    {
        $this->array_modified_field[$field_name]=$field_value;
    }
    public function addToArrayModificationError(string $field_name,mixed $field_value) : void
    {
        $this->array_modified_field_error[$field_name]=$field_value;
    }    
    public function initArrayModification() : void
    {
        $this->array_modified_field=array();
    }
    public function initArrayModificationError() : void
    {
        $this->array_modified_field_error=array();
    }    
    public function getIdentifierPostGet() : string
    {
        return $this->identifier_post_get;
    }
    public function setIdentifierPostGet(?string $identifier) : void
    {
        if(is_null($identifier))
        {
            $this->identifier_post_get="";
        }
        else
        {
            $this->identifier_post_get=$identifier;
        }
    }
    public function isInputPostGet() : bool
    {
        if($this->is_input_post_get)
        {
            return true;
        }
        return false;
    }
    public function setInputPostGet(mixed $is_input_post_get) : void
    {
        if($is_input_post_get)
        {
            $this->is_input_post_get=true;
        }
        else
        {
            $this->is_input_post_get=false;
        }
    }     
 
    public function toStringNonEmpty() : string
    {
        return $this->convertToString("\n",true);
    }
    
    public function toWebStringNonEmpty() : string
    {
        return $this->convertToString("<br />",true);
    }    
    
    public function toString() : string
    {
        return $this->convertToString("\n");
    }
    public function toWebString() : string
    {
        return $this->convertToString("<br />");
    } 
       
    //################################
    // GET Modifications in String
    //################################
    public function hasBeenModified() : bool
    {
        if(count($this->getArrayModifications()))
        {
            return true;
        }
        return false;
    }
    public function getModificationsInString() : string
    {
        return $this->getModifications("\n");
    }
    public function getModificationsInWebString() : string
    {
        return $this->getModifications("<br />");
    }    
    
    public function getModificationsError(string $delimiter="") : string
    {
        $string="";
        if(count($this->getArrayModificationsError())<1)
        {
            return "";
        }
        
        foreach ($this->getArrayModificationsError() as $key => $value) 
        {
            $string .= "$key = $value ".$delimiter;
        }
        return $string;    
    }
    
    public function getModificationsErrorInString() : string
    {
        return $this->getModificationsError("\n");
    }
    public function getModificationsErrorInWebString() : string
    {
        return $this->getModificationsError("<br />");
    }    
    
    public function getModifications(string $delimiter="") : string
    {
        $string="";
        if(count($this->getArrayModifications())<1)
        {
            return "";
        }
        
        foreach ($this->getArrayModifications() as $key => $value) 
        {
            $string .= "$key = $value ".$delimiter;
        }
        return $string;    
    }    
    
    public function newItemMessageWebString() : string
    {
        return $this->newItemMessage("<br />");
    }
    public function newItemMessageString() : string
    {
        return $this->newItemMessage("\n");
    }          
    protected function newItemMessage(string $delimiter="") : string
    {
        $string="";
        $string.="####################################".$delimiter;
        $string.="Nouvel objet => ".$this->getClassName().$delimiter.$delimiter;
        $string.=$this->convertToString($delimiter);
        $string.="####################################".$delimiter;
        return $string;
    }
    public function deletedItemMessageWebString() : string
    {
        return $this->deletedItemMessage("<br />");
    }
    public function deletedItemMessageString() : string
    {
        return $this->deletedItemMessage("\n");
    } 
     protected function deletedItemMessage(string $delimiter="") : string
    {
        $string="";
        $string.="####################################".$delimiter;
        $string.="Objet Supprimé => ".$this->getClassName().$delimiter.$delimiter;
        $string.=$this->convertToString($delimiter);
        $string.="####################################".$delimiter;
        return $string;
    }   
    
    public function modifyItemMessageWebString() : string
    {
        return $this->modifyItemMessage("<br />");
    }
    public function modifyItemMessageString() : string
    {
        return $this->modifyItemMessage("\n");
    }
    
    protected function modifyItemMessage(string $delimiter) : string
    {
        if($this->getModifications($delimiter)==="")
        {
            return "";
        }
        $string="";
        $string.="####################################".$delimiter;
        $string.="Objet modifié => ".$this->getClassName().$delimiter.$delimiter;
        $string.=$this->convertToString($delimiter);
        $string.=$delimiter.$delimiter."Champs modifiés => ".$delimiter;
        $string.=$this->getModifications($delimiter);
        $string.="####################################".$delimiter;  
        return $string;
    }  
    
    
    public function getIdForSql(Sql $sql) : string
    {
        return "".$this->id;
    }    
    public function getId() : int
    {
        return intval($this->id);
    }
    public function setId(mixed $id,bool $verification=true) : bool
    {
        if(!$verification)
        {
            $this->id="".$id;
            $this->setIdentifierPostGet($this->id);
            return true;            
        }
        if($this->setKFieldValue(static::$ID,$id))
        {
            $this->id = is_null($id) ? null : "".$id; 
            $this->setIdentifierPostGet($this->id);
            return true;
        }
        return false;         
    }
    public function resetId() : void
    {
        $this->id=null;
        $kField=$this->getKField(static::$ID);
        $kField->setValueToNull(false);
    }
    public function getDate_createdForSql(Sql $sql) : ?string
    {
        return $this->date_created;
    }
    public function getDate_created() : ?string
    {
        return $this->date_created;
    }
    public function setDate_created(mixed $date_created,bool $verification=true) : bool
    {      
        if(!$verification)
        {
            $this->date_created = $date_created;
            return true;            
        }
        if($this->setKFieldValue(static::$DATE_CREATED,$date_created))
        {
            $this->date_created = $date_created;
            return true;
        }
        return false;        
    }
    public function getDate_modifiedForSql(Sql $sql) : ?string
    {
        return $this->date_modified;
    }    
    public function getDate_modified() : ?string
    {
        return $this->date_modified;
    }
    public function setDate_modified(mixed $date_modified,bool $verification=true) : bool
    {
        if(!$verification)
        {
            $this->date_modified = $date_modified;
            return true;            
        }
        if($this->setKFieldValue(static::$DATE_MODIFIED,$date_modified))
        {
            $this->date_modified = $date_modified;
            return true;
        }
        return false;  
    } 
    
    public function makeDateCreation() : void
    {
        // 2010-02-11 12:23:57
        $date_created=date("Y-m-d H:i:s");
        $this->setDate_created($date_created);
        $this->setDate_modified($date_created);
    }  
    
    public function makeDateModification() : void
    {
        $date_created=date("Y-m-d H:i:s");
        $this->setDate_modified($date_created);
    } 
    
    public function resetDates() : void
    {
        $this->date_created ="";
        $this->date_modified ="";
    }

    public function getAllInputPostList() : bool
    {
        return false;
    }
    
    public function getTheStringValue() : string
    {
        return $this->getClassName();
    }
 
    function getKerror() : string
    {
        return $this->kerror;
    }

    function setKerror(mixed $kerror) : void
    {
        $this->kerror="".$kerror;
    }
    
    function getInitKFields() : bool
    {
        return $this->initKFields;
    }

    protected function setInitKFields(bool $initKFields) : void
    {
        $this->initKFields=$initKFields;
    }

    /**
     * 
     * @param array<string,mixed> $arrayField
     * @param bool $verification
     * @return bool
     */
    public function initByArrayFieldName(array $arrayField,bool $verification=true) : bool
    {
        if(!isArrayAssoc($arrayField))
        {
            return false;
        }
        $status=true;
        foreach ($arrayField as $fieldName => $fieldValue) 
        {
            if(!$this->setFieldValue($fieldName,$fieldValue,$verification=true))
            {
                $status=false;
            }
        }
        return $status;
    }
    
    public function setFieldValue(string $fieldName,mixed $fieldValue,bool $verification=true) : bool
    {
        if($this->fieldNameExists($fieldName))
        {
            $stringMethod="set".ucfirst($fieldName)."";
            //KDebugger::getInstance()->dump($stringMethod,"KOBJECT");
            return $this->$stringMethod($fieldValue,$verification);
        }
        else
        {
            $this->setKerror("KObject::setFieldValue() Field name doesn't exist :".$fieldName);
            return false;
        }
    }
    
    public function getFieldValue(string $fieldName) : mixed
    {
        if($this->fieldNameExists($fieldName))
        {
            $stringMethod="get".ucfirst($fieldName)."";
            //echo "stringMethod:::".$stringMethod."\n";
            //KDebugger::getInstance()->dump($this->$stringMethod(),"getFieldValue 0");
            //KDebugger::getInstance()->dump("getFieldValue ".$stringMethod."//".strval($this->$stringMethod())."".$this->$stringMethod(),"getFieldValue");
            return $this->$stringMethod();
        }
        else
        {
            $this->setKerror("KObject::getFieldValue() Field name doesn't exist :".$fieldName);
            return false;
        }        
    }
    
    
    public function makeAliasSqlField(string $fieldname) : string
    {
        return $this->alias_sql."_".$fieldname;
    }
    
    /**
     * 
     * @param array<string,mixed> $result
     * @param Sql $sql
     * @param bool $fKobject
     * @return bool
     */
    protected function fillAttributesResult(array $result,Sql $sql, bool $fKobject=true) : bool
    {
        //echo print_r($result);
//        exit();
        if($result!=NULL)
        {
            for($i=0;$i<$this->listFieldName->getSize();$i++)
            {
                $fieldName=$this->listFieldName->get($i);
                $fieldAliasName=$this->makeAliasSqlField($fieldName);
                // PATCH FOR POSTGRES MAX lenght is 64
                if($sql->getEngine_type()==Sql::$POSTGRES && strlen($fieldAliasName)>63)
                {
                    $fieldAliasName=substr($fieldAliasName,0,63);
                    //KDebugger::getInstance()->dump($fieldAliasName,$fieldName);
                }
                if(!array_key_exists($fieldAliasName,$result))
                {
                    //echo print_r($result);
                    $this->setKerror("KObject::fillAttributesResult() Fieldname/Alias => ".$fieldName."/".$fieldAliasName." not found ! <br /><br /><pre>". print_r($result,true)."</pre>");
                    return false;
                }
                else if(!$this->setFieldValue($fieldName,$result[$fieldAliasName],false))
                {
                    $this->setKerror("KObject::fillAttributesResult() setFieldValue => FALSE // ".$fieldName." => ".$result[$fieldAliasName]." !");
                    return false;
                }
            }
            
            // Get foreign Object
            if($fKobject)
            {
                /* @var $kObject KObject */                
                foreach($this->mapForeignKeyFields as $fk_fieldName => $kObject)
                {
                    if(!$kObject->initByResultSql($result,$sql,false))
                    {
                        $this->setKerror("KObject::fillAttributesResult() [".$kObject->getClassName()."] Foreign KObject => ".$kObject->getAlias_sql()."->".$kObject->getKerror()."[".$this->getClassName()."]"."{".$this->toString()."}");
                        //echo "<br />||fk_fieldName".$fk_fieldName." ".$kObject->toWebString()."<br />";
                        return false;
                    }
                    //echo "<br />||fk_fieldName".$fk_fieldName." ".$kObject->toWebString()."<br />";
                }
            }
            return true;
        }
        else
        {
            $this->setKerror("KObject::fillAttributesResult() Result is null !");
            return false;
        }
    }
    
    /**
     * 
     * @return array<string,mixed>
     */
    public function arrayFieldsValue() : array
    {
        $array=array();
        for($i=0;$i<$this->listFieldName->getSize();$i++)
        {
            $fieldName=$this->listFieldName->get($i);
            $stringMethod="get".ucfirst($fieldName)."";
            $array[$fieldName]=$this->$stringMethod();
        }
        return $array;
    }
    /**
     * 
     * @return array<int,mixed>
     */
    public function fieldsToArray() : array
    {
        $array=array();
        for($i=0;$i<$this->listFieldName->getSize();$i++)
        {
            $fieldName=$this->listFieldName->get($i);
            $stringMethod="get".ucfirst($fieldName)."";
            $array[]=$this->$stringMethod();
        }
        return $array;        
    }
    /**
     * 
     * @param bool $upperCaseFirst
     * @param array<int,string> $excludeFields
     * @return array<int,mixed>
     */
    public function fieldsLabelToArray(bool $upperCaseFirst=true,array $excludeFields=[]) : array
    {
        $array=array();  
        for($i=0;$i<$this->listFieldName->getSize();$i++)
        {
            $fieldName=$this->listFieldName->get($i);
            if(!in_array($fieldName, $excludeFields))
            {
                if($upperCaseFirst)
                {
                    $array[]=ucfirst($fieldName);
                }
                else
                {
                    $array[]=$fieldName;
                }
            }
        }
        return $array;
    }
    
    /**
     * 
     * @param bool $uppercaseFirst
     * @return array<int,string>
     */
    public function fieldsObligatoryLabelToArray(bool $uppercaseFirst=true) : array
    {
        $array=[];
        if($uppercaseFirst)
        {
            $array[]=ucfirst(self::$ID);
            $array[]=ucfirst(self::$DATE_CREATED);
            $array[]=ucfirst(self::$DATE_MODIFIED);
        }
        else
        {
            $array[]=(self::$ID);
            $array[]=(self::$DATE_CREATED);
            $array[]=(self::$DATE_MODIFIED);            
        }
        return $array;
    }
    /**
     * 
     * @param bool $uppercaseFirst
     * @return array<int,string>
     */
    public function fieldsNonObligatoryLabelToArray(bool $uppercaseFirst=true) : array
    {
        $array=[];
        $array2=$this->fieldsLabelToArray($uppercaseFirst);
        $array1=$this->fieldsObligatoryLabelToArray($uppercaseFirst);
        foreach ($array2 as $label)
        {
            if(!in_array($label, $array1))
            {
                $array[]=$label;
            }
        }
        return $array;
    }
    public function convertToString(string $delimiter="\n",bool $nonEmpty=false) : string
    {
        $string=""; 
        for($i=0;$i<$this->listFieldName->getSize();$i++)
        {
            $fieldName=$this->listFieldName->get($i);
            $stringMethod="get".ucfirst($fieldName)."";
            //$array[$fieldName]=$this->$stringMethod();
            $value=$this->$stringMethod();
            if(($nonEmpty&&!empty($value)) || !$nonEmpty)
            {
                $string.=$fieldName."=".$value.$delimiter; 
            }
        } 
        return $string;
    }
    public function fieldsValueToJson() : string
    {
        return json_encode($this->arrayFieldsValue());
    }
    
    public function getInputValueFieldName(mixed $fieldName) : string
    { 
        return static::$TABLE_NAME."_".$fieldName."_".$this->getIdentifierPostGet();
    }
    /**
     * 
     * @param array<string,mixed>|null $type
     * @param string $fieldName
     * @param bool $trim
     * @return bool
     */
    public function getInputValue(?array $type,string $fieldName,bool $trim=false) : bool
    {
        if(!is_array($type))
        {
            return false;
        }
        if(isset($type[$this->getInputValueFieldName($fieldName)]))
        {
            $value=$type[$this->getInputValueFieldName($fieldName)];
            if($trim)
            {
                $value=trim($value);
            }
            $kfield=$this->getKField($fieldName);
//            KDebugger::getInstance()->dump($value, $fieldName."1");
//            KDebugger::getInstance()->dump($kfield->get(), $fieldName."2");
            if($kfield->notEqualValue($value))
            {
//                if($kfield->getType()==KField::$BOOL)
//                {
//                    KDebugger::getInstance()->dump($value."//".$kfield->get(), $fieldName."=> MODIFIED");
//                    KDebugger::getInstance()->dump($this->getFieldValue($fieldName), $fieldName."=> MODIFIED 1");
//                }
                if($this->setFieldValue($fieldName,$value))
                {
//                    if($kfield->getType()==KField::$BOOL)
//                    {
//                    KDebugger::getInstance()->dump($value."//".$kfield->get(),$fieldName."=> MODIFIED 2");
//                    //KDebugger::getInstance()->dump($kfield,$fieldName."=> MODIFIED Kfield");
//                    //KDebugger::getInstance()->dump($kfield->getValueForSql(Sql::getInstance()),"MODIFIED SQL");
//                    KDebugger::getInstance()->dump($this->getFieldValue($fieldName),"MODIFIED Value");
//                    }
                    //KDebugger::getInstance()->dump($kfield->get(),$fieldName);
//                    KDebugger::getInstance()->dump($kfield);
                    //KDebugger::getInstance()->dump($_POST,"Modifications");
                    //echo "MODIFICATION <br />";
                    $this->setInputPostGet(1);
                    $this->addToArrayModification($fieldName,$value);
                    
                    //var_dump($this->getArrayModifications());
                    return true;
                }
                else
                {
                    $this->addToArrayModificationError($fieldName,$this->getKerror());
                }
            }
        }
        return false;
    }
    public function getInputValueByGet(string $fieldName,bool $trim=false) : bool
    {
        return $this->getInputValue($_GET,$fieldName,$trim);
    }
    public function getInputValueByPost(string $fieldName,bool $trim=false) : bool
    {
        return $this->getInputValue($_POST,$fieldName,$trim);
    }
    
    public function getAllInputPost(bool $trim=false) : bool
    {  
        $this->fillKFields();
        $this->initArrayModification();
        $this->initArrayModificationError();
        for($i=1;$i<$this->listFieldName->getSize();$i++)
        {
            $fieldName=$this->listFieldName->get($i);
            if(!empty($fieldName))
            {
                $this->getInputValueByPost($fieldName,$trim);         
            }
            else
            {
                return false;
            }
        }         
        return $this->isInputPostGet();
    }
    public function getAllInputGet(bool $trim=false) : bool
    {  
        $this->fillKFields();
        $this->initArrayModification();
        $this->initArrayModificationError();
        for($i=1;$i<$this->listFieldName->getSize();$i++)
        {
            $fieldName=$this->listFieldName->get($i);  
            if(!empty($fieldName))
            {
                $this->getInputValueByGet($fieldName,$trim);         
            } 
            else
            {
                return false;
            }            
        }         
        return $this->isInputPostGet();
    } 
    
    public function getAllInputPostWithID() : bool
    {
        $this->initArrayModification();
        $this->initArrayModificationError();
        for($i=0;$i<$this->listFieldName->getSize();$i++)
        {
            $fieldName=$this->listFieldName->get($i);
            $this->getInputValueByPost($fieldName);         
        }         
        return $this->isInputPostGet();      
    }
    public function getAllInputGetWithID() : bool
    {
        $this->initArrayModification();
        $this->initArrayModificationError();
        for($i=1;$i<$this->listFieldName->getSize();$i++)
        {
            $fieldName=$this->listFieldName->get($i);
            $this->getInputValueByGet($fieldName);         
        }         
        return $this->isInputPostGet();      
    }
    
    public function exportModelToJSONFile(string $filename) : bool
    {
        $status=false;
        if(file_exists($filename))
        {
            unlink($filename);
        }

        $fp = fopen($filename, "w");
        if(fwrite($fp, $this->exportModelToJSON()))
        {
            $status=true;
        }
        fclose($fp);
        return $status;
        //chmod($filename,0777);        
    }
    
    public function exportModelToJSON() : string
    {
        if(!$this->initKFields)
        {
            $this->initKFields();
        }        
        $stringJSON="";
        $list=$this->mapKField->toArrayListNotSort();
        $kField= new KFieldUnKnown();
        for($i=0; $i<$list->getSize(); $i++)
        {
            if($i!=0)
            {
                $stringJSON.=",";
            }
            $kField=$list->get($i);
            $stringJSON.=substr(json_encode($kField),1,-1);
        }
        if($stringJSON!="")
        {
            $stringJSON="[".$stringJSON."]";
        }
        return $stringJSON;
    }
    
    //TODO
    /*
    public function exportDataToJSON()
    {
        if(!$this->initKFields)
        {
            $this->initKFields();
        }        
        $stringJSON="";
        $list=$this->mapKField->toArrayListNotSort();
        $kField= new KFieldUnKnown();
        for($i=0; $i<$list->getSize(); $i++)
        {
            if($i!=0)
            {
                $stringJSON.=",";
            }
            $kField=$list->get($i);
            $stringJSON.=substr(json_encode($kField),1,-1);
        }
        if($stringJSON!="")
        {
            $stringJSON="[".$stringJSON."]";
        }
        return $stringJSON;        
    }    
    */
    public function exportDataToGeoJSON_String() : string
    {
        return "";
    }
    
    
    public function createModelInDb(Sql $sql) : bool
    {
        $status= false;
        $list=$this->mapKField->toArrayListNotSort();
        if($sql->createModelInDb($this->getTable_name(),$list))
        {
            $status=true;
        }
        else
        {
            $this->setKerror($sql->getError());
        }
        return $status;
    }
    
    public function initByDefault() : void
    {
        if(!$this->getInitKFields())
        {
            $this->initKFields();
        }
        $kField=new KFieldUnKnown();
        $list=$this->mapKField->toArrayList();
        for($i=0; $i<$list->getSize(); $i++)
        {
            $kField=$list->get($i);
            $methodName="set".ucfirst($kField->getName());
            $this->$methodName($kField->getDefault(),false);
        }
    }
    public function fillKFields() : void
    {
        if(!$this->getInitKFields())
        {
            $this->initKFields();
        }
        $kField=new KFieldUnKnown();
        $list=$this->mapKField->toArrayList();
        for($i=0; $i<$list->getSize(); $i++)
        {
            $kField=$list->get($i);
            $methodGetName="get".ucfirst($kField->getName());
            $this->setKFieldValue($kField->getName(),$this->$methodGetName());
        }
    } 
    
    protected function getAliasPrefix() : string
    {
        return $this->alias_prefix;
    }
    protected function setAliasPrefix(string $alias_prefix) : void
    {
        $this->alias_prefix=$alias_prefix;
    }
    
    public static function makeClassNameFromTableName(mixed $tablename) : string
    {
        if(empty($tablename))
        {
            return "";
        }
        if(str_contains($tablename,"_"))
        {
            $tab=explode("_",$tablename);
        
            $string="";
            for ($i = 0; $i < count($tab); $i++)
            {
                $string.=ucfirst($tab[$i]);
                if($i!=(count($tab)-1))
                {
                    $string.="_";
                }
            }
            return $string;
        }  
        else
        {
            return ucfirst($tablename);
        }
    }
    
    function getUpdate_date_modified() : bool
    {
        return $this->update_date_modified;
    }

    function setUpdate_date_modified(bool $update_date_modified) : void
    {
        $this->update_date_modified=$update_date_modified;
    }
    
    /**
    * @return array <int,array<int,string>>
    */
    public function getForeignNotKlinkTables() : array
    {
        return [["",""]];
    }
    
    public function getUse_cache() : bool
    {
        return $this->use_cache;
    }

    public function setUse_cache(bool $use_cache) : void
    {
        $this->use_cache = $use_cache;
    }
    
    public function fkObjectsInitialized() : bool
    {
        return $this->fkObjectsInitialized;
    }
    
    public function initByFk_id(mixed $the_className,mixed $the_fieldname,mixed $id) : bool
    {
        $className="".$the_className;
        $fieldname="".$the_fieldname;      
        if(empty($className)||empty($fieldname))
        {
            return false;
        }
        
        $dblist = new DbList($className);
        $list = $dblist->getIdsByArray([new QueryField($fieldname, $id)]);
        //KDebugger::getInstance()->dump($list->getSize(),"Kobject initByFk_id");
        if ($list->getSize() == 1)
        {
            return $this->initById($list->get(0));
        }
        return false;
    }
    
    public function initByOneField(mixed $fieldName,mixed $fieldValue) : bool
    {
        if(empty($fieldName))
        {
            return false;
        }
        $dblist = new DbList($this->getClassName());
        $list = $dblist->getIdsByArray([new QueryField("".$fieldName, $fieldValue)]);
        if ($list->getSize() == 1)
        {
            return $this->initById($list->get(0));
        }
        return false;
    }

    
    public function getIsGeo(): bool
    {
        return $this->isGeo;
    }

    public function getFieldGeo(): string
    {
        return $this->fieldGeo;
    }

    public function setIsGeo(bool $isGeo) : KObject
    {
        $this->isGeo = $isGeo;
        return $this;
    }

    public function setFieldGeo(string $fieldGeo) : KObject
    {
        $this->fieldGeo = $fieldGeo;
        return $this;
    }
    
    public function getFieldForeignKeyLabel() : KField
    {              
        $this->initKFields();
        $theField=$this->getKField(KObject::$ID);
        $map=$this->getMapKField();
        /* @var $kfield KField */
        foreach ($map as $kfield)
        {
            //KDebugger::getInstance()->dump($fieldName);
            if($kfield->getName()!=KObject::$ID 
                    && $kfield->getName()!=KObject::$DATE_CREATED 
                    && $kfield->getName()!=KObject::$DATE_MODIFIED
                    && !$kfield->getForeign_key()
                    && !$kfield->getPrimary_key()
                    )
            {
                if($kfield->getType()==KField::$VARCHAR)
                {
                    $theField=$kfield;
                    break;
                }
            }
        }   
        return $theField;
    }
    
    /**
     * 
     * @return array<string,KField>
     */
    public function getAllForeignsKeyFields() : array
    {
        $this->initKFields();
        $arrayFkFieldsName=[];
        $map=$this->getMapKField();
        foreach($map as $field)
        {        
            /* @var $field KField */
            if(!empty($field->getForeign_key_table()))
            {
                $className=self::makeClassNameFromTableName($field->getForeign_key_table()); 
                if(class_exists($className))
                { 
                    $arrayFkFieldsName[$field->getName()]=$field;      
                }  
            }   
        }
        return $arrayFkFieldsName;
    }    
    
    /**
     * 
     * @return array<string,array<int,mixed>>
     */
    public function getAllForeignsKeyValue() : array
    {
        $arrayFkFieldsValue=[];
        $map=$this->getAllForeignsKeyFields();
        foreach($map as $field)
        {        
            $className=self::makeClassNameFromTableName($field->getForeign_key_table()); 
            $object = new $className();                
            $theField=$object->getFieldForeignKeyLabel();

            $dbListFk=DbListManager::getDb($className);
            if($dbListFk->getNb()<50000)
            {     
                $list=$dbListFk->getIdAndFieldValue($theField->getName());
                $newList=[];
                foreach ($list as $value)
                {
                    $newList[$value[0]]=$value[1];
                }
                $arrayFkFieldsValue[$field->getName()]=$newList;
            }               
        }  
        return $arrayFkFieldsValue;
    }
    
    /**
     * 
     * @return array<int,string>
     */
    public function getFieldNamesQueriable() : array
    {   
        $fieldNames=[];
        if(!$this->initKFields)
        {
            $this->initKFields();
        }
        $fieldsName = $this->getListFieldName();
        foreach ($fieldsName as $field_name)
        {
            $kfield = $this->getKField($field_name);
            if ($kfield->isSqlStandardQueriable())
            {
                $fieldNames[]=$field_name;
            }
        }
        return $fieldNames;
    }
    
    // NOT TESTED
//    public function copyKObject(KObject &$object) : void
//    {
//        if(!$this->initKFields)
//        {
//            $this->initKFields();
//        }        
//        $list=$this->mapKField->toArrayListNotSort();
//        /* @var $kField KField */
//        for($i=0; $i<$list->getSize(); $i++)
//        {
//            $kField=$list->get($i);
//            if(!is_null($kField))
//            {
//                $object->setFieldValue($kField->getName(), $kField->get());
//            }
//        }
//    }
}