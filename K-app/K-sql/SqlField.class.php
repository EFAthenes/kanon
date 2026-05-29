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
class SqlField
{
    private ?string $tablename;
    
    private string $name;
    private ?SqlFieldType $type= null;
    private mixed $default=null;
    private bool $is_null=false;
    private string $comment="";
    private bool $auto_increment=false;
    private bool $primary_key=false;
    private bool $secondary_key=false;
    private bool $foreign_key=false;
    private string $foreign_key_table="";
    private ?string $geometry_type=null;
    public function __construct()
    {
    }
    public function __destruct()
    {
    }
    /**
     * 
     * @param string|null $tablename
     * @param array<string,string>|null $row
     * @param string|null $engine_type
     * @return void
     */
    public function initByDB(?string $tablename,?array $row,?string $engine_type) : void
    {
        $this->tablename=$tablename;
        if(is_array($row)&&!is_null($engine_type))
        {
            if(($engine_type==Sql::$MYSQL||$engine_type==Sql::$MARIADB)&&isset($row['Field']))
            {
                $this->name=$row['Field'];
                $this->type=new SqlFieldType($this->tablename,$row,$engine_type);

                if(isset($row['Null'])&&$row['Null']=="NO")
                {
                    $this->is_null=false;                    
                }
                else
                {
                    $this->is_null=true;        
                }
                
                if(isset($row['Key'])&&$row['Key']=="PRI")
                {
                    $this->primary_key=true;
                }
                else if(isset($row['Key'])&&$row['Key']=="UNI")
                {
                    $this->secondary_key=true;
                }
                else if(isset($row['Key'])&&$row['Key']=="MUL")
                {
                    $this->foreign_key=true;
                }
                
                //if($row['Default']!="NULL")
                /** @phpstan-ignore-next-line */
                if(isset($row['Default'])&&!is_null($row['Default']))
                {
                    if($row['Default']=="CURRENT_TIMESTAMP")
                    {
                        $this->default="''";
                    }
                    elseif($row['Default']=="")
                    {
                        $this->default='';  
                    }
                    else
                    {
                        $this->default=$row['Default'];
                    }
                    //echo "==>".$row['Default']."<==\n";
                }
                else
                {
                    if($this->is_null)
                    {
                        $this->default="NULL";
                    }
                }
                $comment = isset($row['Comment']) ? $row['Comment']:"";
                $this->comment=$comment;
                //echo 'EXTRA=='.$row['Extra'].'\n';
                if(isset($row['Extra'])&&$row['Extra']==="auto_increment")
                {
                    $this->auto_increment=true;
                }
                else
                {
                    $this->auto_increment=false;
                }
                
                //echo print_r($this);
            }
            else if($engine_type==Sql::$POSTGRES&&isset($row['column_name']))
            {
                $this->name=$row['column_name'];
                $this->type=new SqlFieldType($this->tablename,$row,$engine_type);
                $this->is_null=$this->convertYesNoToBool($row['is_nullable']);
                $this->auto_increment=false;
                if(isset($row['column_default']))
                {
                    //KDebugger::getInstance()->dump($this->name,"1");
                    if(stringStartsWith($row['column_default'],"nextval("))
                    {
                        $this->auto_increment=true;
                        $this->default="''";
                    }
                    else if(string_contains("::",$row['column_default']))
                    {
                        $array=explode("::",$row['column_default']);
                        $this->default=$array[0];                    
                    }
                    else
                    {
                        $this->default=$row['column_default'];
                    }
                    //echo "==>".$row['column_default']."<==\n";
                    //KDebugger::getInstance()->dump($row['column_default'],"2");
                }
                else
                {
                    if($this->is_null=="YES")
                    {
                        $this->default="NULL";
                    }
                    else
                    {
                        // make default by type
                        $this->default="''";
                    }
                }
                $this->comment="";
                               
                //CHECK PRIMARY KEY 
                $query="SELECT K.TABLE_NAME,
K.COLUMN_NAME,
K.CONSTRAINT_NAME
FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS AS C
JOIN INFORMATION_SCHEMA.KEY_COLUMN_USAGE AS K
ON C.TABLE_NAME = K.TABLE_NAME
AND C.CONSTRAINT_CATALOG = K.CONSTRAINT_CATALOG
AND C.CONSTRAINT_SCHEMA = K.CONSTRAINT_SCHEMA
AND C.CONSTRAINT_NAME = K.CONSTRAINT_NAME
WHERE C.CONSTRAINT_TYPE = 'PRIMARY KEY'
AND K.COLUMN_NAME = '".$row['column_name']."'
AND K.TABLE_NAME ='".$this->tablename."'";
                
                $results = Sql::getInstance()->request_SQL($query);  
                //echo $query;
                if($results && ($row2 = Sql::getInstance()->fetch_array($results)))
                {
                    $this->primary_key=true;
                }
                // CHECK AUTOINCREMENT
                //$this->auto_increment=$row['Extra'];
            }
        }
    }
    function getName() : string
    {
        return $this->name;
    }

    function getType() : ?SqlFieldType
    {
        return $this->type;
    }
    
    function getTypeName() : string
    {
        if(!is_null($this->type))
        {
            return $this->type->getType();
        }
        return SqlFieldType::$UNKNOWN;
    }
    
    function getCastTypePHP() : string
    {
        $castString=$this->getTypeInPHP();
        if(!empty($castString))
        {
            if($this->getIs_null())
            {
                if($castString=="int"||$castString=="float")
                {
                   return  'isStringIntegerNull($this->'.$this->getName().") ? null : (".$castString.")";
                }
                else
                {
                    //$typeStringNull="is_null";
                    return 'is_null($this->'.$this->getName().") ? null : (".$castString.")";
                }
            }           
            return "(".$castString.")";
        }
        return "";        
        
        
    } 
    private function getTypeInPHP() : string
    {
        $typeString="";
        switch($this->getTypeName())
        {
            case SqlFieldType::$BOOL :
                $typeString="bool";
                break;
            case SqlFieldType::$INTEGER :
                $typeString="int";
                break; 
            case SqlFieldType::$FLOAT :
                $typeString="float";
                break;   
            case SqlFieldType::$DOUBLE :
                $typeString="float";
                break;              
            case SqlFieldType::$VARCHAR :
                $typeString="string";
                break;  
            case SqlFieldType::$TEXT :
                $typeString="string";
                break;  
            case SqlFieldType::$DATE :
                $typeString="string";
                break;              
            case SqlFieldType::$TIMESTAMP :
                $typeString="string";
                break;  
            case SqlFieldType::$TIME :
                $typeString="string";
                break;
            case SqlFieldType::$YEAR :
                $typeString="string";
                break; 
            case SqlFieldType::$DATE :
                $typeString="string";
                break;  
            case SqlFieldType::$DATETIME :
                $typeString="string";
                break;              
            case SqlFieldType::$GEOMETRY :
                $typeString="string";
                break;             
        }
        return $typeString;
    }
    
    function getTypeReturnPHP() : string
    {
        $typeString=$this->getTypeInPHP();
        
        if(!empty($typeString))
        {
            $typeStringNull="";
            if($this->getIs_null())
            {
                $typeStringNull="?";
            }
            
            return " : ".$typeStringNull.$typeString;
        }
        return "";
    }
    
    

    function getDefault() : mixed
    {
        return $this->default;
    }
    
    function getDefaultString() : ?string
    {
        if(isStringNull($this->default))
        {
            return null;
        }
        else
        {
            if($this->default=="")
            {
                return "''";
            }
            else
            {
                if(substr($this->default, 0, 1 ) === "'" && substr($this->default, -1)=== "'")
                {
                    return $this->default;
                }
                else
                {
                    return "'".$this->default."'";
                }
            }
        }
    }     

    function getIs_null() : bool
    {
        return $this->is_null;
    }
    
    function getIs_nullValue() : int
    {
        if($this->is_null)
        {
            return 1;
        }
        return 0;
    }    

    function getComment() : string
    {
        return $this->comment;
    }

    function setName(string $name): void
    {
        $this->name=$name;
    }

    function setType(SqlFieldType $type) : void
    {
        $this->type=$type;
    }

    function setDefault(mixed $default): void
    {
        $this->default=$default;
    }

    function setIs_null(bool $is_null=false) : void
    {
        $this->is_null=$is_null;
    }

    function setComment(string $comment) : void
    {
        $this->comment=$comment;
    }
    
    function getTablename() : string
    {
        return $this->tablename;
    }

    function getAuto_increment() : bool
    {
        return $this->auto_increment;
    }

    function getPrimary_key() : bool
    {
        return $this->primary_key;
    }

    function getSecondary_key() : bool
    {
        return $this->secondary_key;
    }

    function getForeign_key() : bool
    {
        return $this->foreign_key;
    }
    
    function getAuto_increment_byInt() : int
    {
        if($this->auto_increment)
        {
            return 1;
        }
        return 0;
    }

    function getPrimary_key_byInt() : int
    {
        if($this->primary_key)
        {
            return 1;
        }
        return 0;
    }

    function getSecondary_key_byInt() : int
    {
        if($this->secondary_key)
        {
            return 1;
        }
        return 0;
    }

    function getForeign_key_byInt() : int
    {
        if($this->foreign_key)
        {
            return 1;
        }
        return 0;
    }    
    
    public function getLength() : ?string
    {
        if(!is_null($this->type))
        {
            return $this->type->getLength();
        }
        return null;
    }
    
    public function getUnsigned() : bool
    {
        if(!is_null($this->type))
        {
            return $this->type->getUnsigned();
        }
        return false;
    }
    
    function setTablename(?string $tablename) : void
    {
        $this->tablename=$tablename;
    }

    function setAuto_increment(bool $auto_increment) : void
    {
        $this->auto_increment=$auto_increment;
    }

    function setPrimary_key(bool $primary_key) : void
    {
        $this->primary_key=$primary_key;
    }

    function setSecondary_key(bool $secondary_key) : void
    {
        $this->secondary_key=$secondary_key;
    }

    function setForeign_key(bool $foreign_key) : void
    {
        $this->foreign_key=$foreign_key;
    }
    
    function setAuto_increment_ByInt(int $auto_increment=0) : void
    {
        if($auto_increment)
        {
            $this->auto_increment=true;
        }
        else
        {
            $this->auto_increment=false;
        }
    }    
    
    function setPrimary_key_ByInt(int $primary_key=0) : void
    {
        if($primary_key)
        {
            $this->primary_key=true;
        }
        else
        {
            $this->primary_key=false;
        }
    }

    function setSecondary_key_ByInt(int $secondary_key=0) : void
    {
        if($secondary_key)
        {
            $this->secondary_key=true;
        }
        else
        {
            $this->secondary_key=false;
        }
    }

    function setForeign_key_ByInt(int $foreign_key=0) : void
    {
        if($foreign_key)
        {
            $this->foreign_key=true;
        }
        else
        {
            $this->foreign_key=false;
        }        
    }    
    
    
    function getForeign_key_table() : string
    {
        return $this->foreign_key_table;
    }

    function setForeign_key_table(string $foreign_key_table) : void
    {
        $this->foreign_key_table = $foreign_key_table;
    }
   
    function getGeometry_type() :?string
    {
        return $this->geometry_type;
    }

    function setGeometry_type(?string $geometry_type) : void
    {
        $this->geometry_type=$geometry_type;
    }

    public function getSrid() : ?string
    {
        if(!is_null($this->type))
        {
            return $this->type->getSrid();
        }
        return null;
    }
    
    public function getCoord_dimension() : ?string
    {
        if(!is_null($this->type))
        {
            return $this->type->getCoord_dimension();
        }
        return null;        
    }    
    
    function getEnumsInString() : string
    {
        $string="";
        if(!is_null($this->type))
        {
            for($i=0; $i<count($this->type->getEnums()); $i++)
            {
                if($string!="")
                {
                    $string.=",";
                }
                $string.="'".$this->type->getEnums()[$i]."'";
            }
            
        }
        return $string;        
    }
    
    public function toString() : string
    {
        return print_r($this,true);
    }
    
    private function convertYesNoToBool(string $yesNo) : bool
    {
        if($yesNo==="YES")
        {
            return true;
        }
        return false;
    }
}