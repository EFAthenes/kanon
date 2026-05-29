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
class SqlJoinKlink
{
    protected ?KObject $klink = null;
    /**
     * 
     * @var array<int,QueryField|array<int,QueryField>>
     */
    protected array $arrayQueryField = [];
    protected bool $status_initialization = false;
    protected int $type=0;
    protected ?string $className=null;
    protected string $kLinkObjectName="";

    /**
     * 
     * @param string $kLinkObjectName
     * @param array<int,QueryField|array<int,QueryField>> $arrayQueryField
     * @param string|null $className
     */
    public function __construct(string $kLinkObjectName, array $arrayQueryField,?string $className=null)
    {
        if (class_exists($kLinkObjectName))
        {
            $this->kLinkObjectName=$kLinkObjectName;
            $object = new $kLinkObjectName();
            if ($object instanceof KLinkObject)
            {
                /* @var $object KLinkObject */
                $this->klink=$object;
                $this->klink->setAlias_sql($this->klink->getAlias_sql()."_".KRandom::makeRandomString("4"));
                $this->checkQueryField($this->arrayQueryField,$arrayQueryField);
                if (count($this->arrayQueryField) > 0)
                {
                    $this->status_initialization = true;
                    $this->type=1;
                }
            }
            elseif(!is_null($className) && $object instanceof KObject )
            {
                /* @var $object KObject */
                if($object->getMapKlinkObjectTablesItems()->get($className))
                {
                    $this->klink=$object;
                    $this->checkQueryField($this->arrayQueryField,$arrayQueryField);
                    if (count($this->arrayQueryField) > 0)
                    {
                        $this->status_initialization = true;
                        $this->type=2;
                        $this->className=$className;
                    }
                }
            }
        }
    }
    
    public function getKLinkObjectName() : string
    {
        return $this->kLinkObjectName; 
    }
    
    public function addSqlJoinKlink(SqlJoinKlink $sqlJoinKlink) : bool
    {
        if(strcmp($sqlJoinKlink->getKLinkObjectName(),$this->getKLinkObjectName())==0)
        {
            $newQueryArray=[];
            
            foreach($this->arrayQueryField as $queryField)
            {
                $newQueryArray[]=$queryField;
            }
            
            foreach ($sqlJoinKlink->getArrayQueryField() as $queryField)
            {
                $newQueryArray[]=$queryField;             
            }
            $this->arrayQueryField=$newQueryArray;
            return true;
        }
        return false;
    }
    
    public function getStatusInitialization() : bool
    {
        return $this->status_initialization;
    }
    
    //array <int,QueryField|array<int,QueryField|null>|null>
    // array <int,QueryField|array<int,QueryField|null>|null> $arrayQueryField
    /**
     * 
     * @param array <int,mixed> $arrayQueryFieldStorage
     * @param array <int,mixed> $arrayQueryField
     * @return void
     */
    private function checkQueryField(array &$arrayQueryFieldStorage,array $arrayQueryField) : void
    {
        foreach ($arrayQueryField as $queryField)
        {
            if (!is_null($queryField) && $queryField instanceof QueryField)
            {
                $queryField->setTable_name($this->klink->getAlias_sql());
                $arrayQueryFieldStorage[] = $queryField;
            }
            elseif (!is_null ($queryField) && is_array($queryField))
            {
                $new_array=[];
                $this->checkQueryField($new_array,$queryField);
                $arrayQueryFieldStorage[] = $new_array;
            }
        }
    }

    public function __destruct()
    {
        
    }

    public function getJoinString(string $kObjectClassName, Sql $sql,string $tag=""): string
    {
        $joinString = "";
        if ($this->status_initialization)
        {
            if($this->type==1)
            {
                $item = $this->klink->getKlinkObjectItem($kObjectClassName);
                if (!is_null($item))
                {                    
                    $joinString = "INNER JOIN " . $sql->quoteString($this->klink->getTable_name()) . " as ".$sql->quoteString($this->klink->getAlias_sql())." ON " . $sql->quoteString($this->klink->getAlias_sql()).".".$sql->quoteString($item->fk_id_field) . " = " . $sql->quoteString($item->className::$TABLE_NAME) . "." . $sql->quoteString(KObject::$ID);
                }
            }
            else
            {
                $item=$this->klink->getKlinkObjectTableItem($this->className);
                if (!is_null($item->className)&&!is_null($item->fk_id_field))
                {
                    $joinString = "INNER JOIN " . $sql->quoteString($this->klink->getTable_name()) . " as ".$sql->quoteString($this->klink->getAlias_sql())." ON " . $sql->quoteString($this->klink->getAlias_sql()).".". $sql->quoteString(KObject::$ID). " = " . $sql->quoteString($this->className::$TABLE_NAME) . "." .$sql->quoteString($item->fk_id_field);
                }
            }
        }
        return $joinString;
    }   
    /**
     * 
     * @return array <int,QueryField|array<int,QueryField>>
     */
    public function getArrayQueryField(): array
    {
        return $this->arrayQueryField;
    }
    
    public function toString(string $delimitor="<br />") : string
    {
        return "Not implemented";
    }

}