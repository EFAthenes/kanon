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
class SqlOrder
{
    private string $table="";
    private string $field="";
    private string $type="";
    /**
     * 
     * @var array<int,SqlOrder>|null
     */
    private ?array $arrayOrder=null;
    private bool $option_count=false;
    private bool $isGeometric=false;
    
    public static string $ASC="ASC";
    public static string $DSC="DESC";
    public static string  $COUNT="count_sql_order_k";
    
    function __construct(string $field,string $type="",bool $isGeometric=false)
    {
        if($field==self::$COUNT)
        {
            $this->option_count=true;
        }

        $this->field=$field;
        $this->setType($type);
        $this->isGeometric=$isGeometric;
    }
    function __destruct()
    {
        
    } 
    public function setGeometricField() : SqlOrder
    {
        $this->isGeometric=true;
        return $this;
    }
    
    public function isGeometricField() : bool
    {
        return  $this->isGeometric;
    }
    
    public function getTable() : string
    {
        return $this->table;
    }
    public function setTable(string $table) : void
    {
        $this->table=$table;
    }
    public function getField() : string
    {
        return $this->field;
    }
    public function getType() : string
    {
        return $this->type;
    }
    public function setField(string $field) : void
    {
        $this->field=$field;
    }
    public function setType(?string $type) : void
    {       
        if(empty($type))
        {
            return;
        }
        if($type==static::$ASC || strtolower($type)==static::$ASC)
        {
            $this->type=$type;
        }
        if($type==static::$DSC || strtolower($type)==static::$DSC)
        {
            $this->type=$type;
        }        
        else
        {
            $this->type=static::$ASC;
        } 
    }
    
    public function addSqlOrder(SqlOrder $order) : void
    {
        if(is_null($this->arrayOrder))
        {
            $this->arrayOrder=[];
        }
        $this->arrayOrder[]=$order;
    }
    
    public function addSqlOrderByInit(string $field,string $type="",bool $isGeometric=false) : void
    {
        if(is_null($this->arrayOrder))
        {
            $this->arrayOrder=[];
        }
        $this->arrayOrder[]=new SqlOrder($field,$type,$isGeometric);
    }
    /**
     * 
     * @return array<int,SqlOrder>|null
     */
    public function getArraySqlOrder() : ? array
    {
        return $this->arrayOrder;
    }
    public function getAllFieldsOrder() :string
    {
        $stringFields=$this->field;
        /* @var $order SqlOrder */
        if(!is_null($this->arrayOrder))
        {
            foreach ($this->arrayOrder as $order)
            {
                $stringFields.=",".$order->getAllFieldsOrder();
            }
        }
        return $stringFields; 
    }
    public function isOrderByCount() : bool
    {
        return $this->option_count;
    }
    
    public function toString(string $delimitor="<br />") :string
    {
        $string="";
        $string.="Field=>".$this->field.$delimitor;
        $string.="Type=>".$this->type.$delimitor;
        $string.="Table=>".$this->table.$delimitor;
        if($this->isOrderByCount())
        {
            $string.="Count=>yes".$delimitor;
        }
        else
        {
            $string.="Count=>yes".$delimitor;
        }
        return $string;
    }
}