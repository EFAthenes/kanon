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
abstract class KField implements JsonSerializable
{
    private string $name="";
    private mixed $value;
    private mixed $default=null;
    private string $type="UNKNOWN";
    private bool $is_null=false;
    private ?string $comment=null;
    private bool $auto_increment=false;
    private bool $primary_key=false;
    private bool $secondary_key=false;
    private bool $foreign_key=false;
    private string $foreign_key_table="";
    private string $input_name="";
    private bool $unsigned=false;
    private string $kerror="";
    private ?string $pendingRename=null;
    protected string $labelError="";
    
    
    public static string $NAME="name";
    public static string $VALUE="value";
    public static string $TYPE="type";
    
    
    public static string $INTEGER="INTEGER";
    public static string $VARCHAR="VARCHAR";
    public static string $TEXT="TEXT";
    public static string $DATE="DATE";
    public static string $DATETIME="DATETIME";
    public static string $TIMESTAMP="TIMESTAMP";
    public static string $TIME="TIME";
    public static string $FLOAT="FLOAT";
    public static string $DOUBLE="DOUBLE";
    public static string $BOOL="BOOL";
    public static string $YEAR="YEAR";
    public static string $ENUM="ENUM";
    public static string $GEOMETRY="GEOMETRY";
    public static string $POINT="POINT";
    public static string $LINESTRING="LINESTRING";
    public static string $POLYGON="POLYGON";
    public static string $MULTIPOINT="MULTIPOINT";
    public static string $MULTILINESTRING="MULTILINESTRING";
    public static string $MULTIPOLYGON="MULTIPOLYGON";
    public static string $RASTER="RASTER";
    public static string $UNKNOWN="UNKNOWN";
    
    //private $delimiter="\n";
    
    //##########################################################################
    // ABSTRACT METHODS
    //##########################################################################
    abstract function __construct();
    protected abstract function affectValue(mixed $value,bool $defaultValue=false) : bool;
    //abstract function initField( array $array ) : bool;
    //##########################################################################

    
    // SETTER SYSTEM
    public function set(mixed $value) : bool
    {
        return $this->affectValue($value,false);
    }
    public function setDefault(mixed $value=null) : bool
    {
        return $this->affectValue($value,true);
    }  
    protected function setVarValue(mixed $value,bool $defaultValue=false) : void
    {
        if($defaultValue)
        {
            $this->setValueDefaultVar($value);
        }
        else
        {
            $this->setValueVar($value);
        }
    }  
    final protected function setValueDefaultVar(mixed $default) : void
    {
        $this->default=$default;
    }   
    final protected function setValueVar(mixed $value) : void
    {
        $this->value=$value;
    }
    final protected function getStringMethodError(bool $defaultValue=false) : string
    {
        if($defaultValue)
        {
            return "setDefault";
        }
        else
        {
            return "set";
        }
    }
    
    //##########################################################################
    // FACTORY KField
    //##########################################################################  
    
    public static function factoryKField(mixed $type) : KField
    {
        $kfield=null;
        switch($type)
        {
            case KField::$INTEGER:
                $kfield=new KFieldInteger();
                break;
            case KField::$VARCHAR:
                $kfield=new KFieldVarChar();
                break;
            case KField::$TEXT:
                $kfield=new KFieldText();
                break;
            case KField::$BOOL:
                $kfield=new KFieldBool();
                break;
            case KField::$FLOAT:
                $kfield=new KFieldFloat();
                break;
            case KField::$DOUBLE:
                $kfield=new KFieldDouble();
                break;
            case KField::$DATE:
                $kfield=new KFieldDate();
                break;
            case KField::$DATETIME:
                $kfield=new KFieldDateTime();
                break;  
            case KField::$YEAR:
                $kfield=new KFieldYear();
                break; 
            case KField::$TIME:
                $kfield=new KFieldTime();
                break;
            case KField::$TIMESTAMP:
                $kfield=new KFieldTimeStamp();
                break;    
            case KField::$GEOMETRY:
                $kfield=new KFieldGeometry();
                break;             
            default:
                $kfield= new KFieldUnKnown();
        }
        return $kfield;
    }
    
    //##########################################################################
    // Specialize KField
    //########################################################################## 

    public static function specializeVarChar(KField $field) : ?KFieldVarChar
    {
        if($field instanceof KFieldVarChar)
        {
            return $field;
        }
        return null;
    }
    
    /**
     * 
     * @param array<string,mixed> $options
     * @return KField
     */
    public static function makeByArray(array $options) : KField
    {
        if(!isset($options[static::$TYPE]))
        {
            return new KFieldUnKnown();
        }
        $type=$options[static::$TYPE];
        unset($options[static::$TYPE]);
        
        $kfield=static::factoryKField($type);
        
        $value=null;
        if(array_key_exists(static::$VALUE, $options))
        {
            $value=$options[static::$VALUE];
            unset($options[static::$VALUE]);
        } 

        if($value!=NULL)
        {
            $kfield->set($value);
        }
        if($options!=NULL)
        {
            $kfield->initField($options);
        }            
        
        return $kfield;
    }
    
    public static function makeByJSON(mixed $jsonString ) : KField
    {
        $array = json_decode("".$jsonString,true );
//        echo "\nmakeByJSON\n";
//        echo print_r($array);
//        echo "\n###################\n";
        if(isset($array)&&is_array($array)&&isset($array[0]))
        {
            return self::makeByArray($array[0]);
        }
        return new KFieldUnKnown();
//        return self::make($array[0]);
    }
    /**
     * 
     * @param mixed $type
     * @param mixed $value
     * @param array<string,mixed>|null $options
     * @return KField
     */
    public static function make(mixed $type=NULL,mixed $value=NULL,?array $options=NULL) : KField
    {
        $kfield=static::factoryKField($type);        
        if($value!=NULL)
        {
            $kfield->set($value);
        }
        if($options!=NULL)
        {
            $kfield->initField($options);
        }            
        return $kfield;
    }
    //##########################################################################

    /**
     * 
     * @param mixed $value
     * @param string $methodName
     * @return mixed
     */
    private function convertValue(mixed $value,string $methodName) : mixed
    {
        $class = new ReflectionClass(static::class);
        $method = $class->getMethod($methodName);
        $params = $method->getParameters(); 
        
        /* @var $param ReflectionParameter */
        $param = $params[0];
        //$params[0]->getType();
        
        //echo "".$methodName."\n";
        //echo "".$params[0]->getType()."\n";
        //var_dump($params[0]->getType());
        
        /* @var $type ReflectionType */
        $type=$param->getType();
        
        if(is_null($type))
        {
            return '';
        }
        
        /* @phpstan-ignore-next-line */
        $typeName=$type->getName();
        $value2=match($typeName)
        {
            'string' => strval($value),
            'mixed' => $value,
            'bool' => boolval($value),
            'int' => intval($value),
            'float' => floatval($value),
            'double' => doubleval($value),
            default => ''
        };
        return $value2;
    }
    
    /**
     * 
     * @param array<string,mixed>|null $array
     * @return bool
     */
    public function initField(?array $array) : bool
    {
        $status= false;
        $status2= true;
        if($array!=null)
        {
            foreach($array as $key=> $value)
            {
                $methodName="set".ucfirst($key);
                if(method_exists($this, $methodName))
                {  
                    $status=true;
                    $value2=$this->convertValue($value,$methodName);
                    $status2=$this->$methodName($value2);
                }
                else
                {
                    $this->setKerror("initField() => Field doesn't exist ".$key);
                    $status2=false;
                }
            }
            if(!$status2)
            {
                $status= false;
            }
        }
        return $status;
    }
    
    public function initFieldByJsonFile() : bool
    {
        return false;
    }
    
    
    
    function get():mixed
    {
        return $this->getValue();
    }
    
    private function debug(mixed $s1,mixed $s2,bool $debug) : void
    {
        if($debug)
            KDebugger::getInstance()->dump($s1,$s2);
    }
    
    public function getValueForSql(Sql $sql) : string
    {
//        $debug=0;
//        if($this->getName()=="doc_public")
//        {
//            $debug=1;
//        }
        //$this->debug($this->getName(),"the field",$debug);
        $valueReturn="";
        if(is_null($this->value))
        {
            //$this->debug($this->value,"getValueForSql(is_null)",$debug);
            if ($this->getIs_null())
            {
//                $this->debug($this->getIs_null(),"getValueForSql(is_null_1)",$debug);
                $valueReturn = "NULL";
            }
            else
            {
//                $this->debug($this->getDefaultString(),"getValueForSql(is_null_2)",$debug);
                $valueReturn =$this->addFunctionSQLToValue($sql,"'" .$sql->real_escape_string($this->convertTypeToSqlString($sql,$this->default)). "'");
            }            
        } 
        else
        {
//            $this->debug($this->value,"getValueForSql(!is_null)",$debug);
            $valueReturn=$this->addFunctionSQLToValue($sql,"'".$sql->real_escape_string($this->convertTypeToSqlString($sql,$this->value))."'");
        }        
//        $this->debug($this->value,"getValueForSql(1)",$debug);
//        $this->debug($valueReturn,"getValueForSql(2)",$debug);
        
        return $valueReturn;
    }
    
//    public function convertTypeToSqlStringForNull(Sql $sql,string $value) : string
//    {
//        if(is_null($this->default))
//        {
//            return "NULL";
//        }        
//        return "".$value;
//    }
    public function convertTypeToSqlString(Sql $sql, mixed $value) : string
    {
        return "".$value;
    }    
    
    public function addFunctionSQLToValue(Sql $sql,string $value) : string
    {
        return "".$value;
    }    
    
    public function notEqualValue(mixed $value) : bool
    {
        return $value!==$this->get();
    }

    protected function getValue() : mixed
    {
        return $this->value;
//        if(isset($this->value))
//        {
//            return $this->value;
//        }
//        return $this->getDefault();
    }
    
   
    public function setValueToNull(bool $verification=true) : bool
    {
        if($verification && $this->getIs_null())
        {
            $this->setValueVar(NULL);
            return true;
        }
        else if(!$verification)
        {
            $this->setValueVar(NULL);
            return true;            
        }
        return false;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getType() : string
    {
        return $this->type;
    }

    public function getDefault() : mixed
    {
        return $this->default;
    }
    
    public function getDefaultString() : string
    {
        if(is_null($this->default))
        {
            return "NULL";
        }
//        elseif(empty($this->default))
//        {
//            return "''";
//        }
        return $this->default."";        
    }
    
    public function getValueInString() : string
    {
        if(is_null($this->value))
        {
            return "NULL";
        }
        return $this->value."";           
    }
    
    public function exportValueInString() : string
    {
        return var_export($this->value, true);
    }
    

    public function getIs_null() : bool
    {
        return $this->is_null;
    }

    public function getComment() : ?string
    {
        return $this->comment;
    }

    public function getAuto_increment() : bool
    {
        return $this->auto_increment;
    }

    public function getPrimary_key(): bool
    {
        return $this->primary_key;
    }

    public function getSecondary_key(): bool
    {
        return $this->secondary_key;
    }

    public function getForeign_key(): bool
    {
        return $this->foreign_key;
    }

    public function setName(string $name) : void
    {
        $this->name=$name;
    }

    public function setType(string $type) : void
    {
        $this->type=$type;
    }
    

//    public function setDefault(mixed $default=null) : bool
//    {
//        if(is_null($default))
//        {
//            if($this->is_null)
//            {
//                $this->default=$default;
//                return true;
//            } 
//            $this->setKerror($this->labelError."setDefault[".$this->getName()."] => Value cannot be NULL ");
//            return false;
//        }
//        return $this->setDefaultValue($default);
//    }
    
    public function setIs_null(bool $is_null=false) : void
    {
        if($is_null)
        {
            $this->is_null=true;
        }
        else
        {
            $this->is_null=false;
        }
    }
    
    public function setIs_null_ByInt(int $is_null=0) : void
    {
        if($is_null)
        {
            $this->is_null=true;
        }
        else
        {
            $this->is_null=false;
        }        
    }    

    public function setComment(?string $comment) : void
    {
        $this->comment=$comment;
    }
    
    public function getInput_name() : string
    {
        return $this->input_name;
    }

    public function setInput_name(string $input_name) : void
    {
        $this->input_name=$input_name;
    }
    
    public function setAuto_increment(bool $auto_increment=false) : void
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
    
    public function setAuto_increment_ByInt(int $auto_increment=0) : void
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
    

    public function setPrimary_key(bool $primary_key=false) : void
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
    
    public function setPrimary_key_ByInt(int $primary_key=0) : void
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

    public function setSecondary_key(bool $secondary_key=false) : void
    {
        $this->secondary_key=$secondary_key;
    }
    
    public function setSecondary_key_ByInt(int $secondary_key=0) : void
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

    public function setForeign_key(bool $foreign_key=false) : void
    {
        $this->foreign_key=$foreign_key;
    }
    
    public function setForeign_key_ByInt(int $foreign_key=0) : void
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
    
    public function getForeign_key_table() : string
    {
        return $this->foreign_key_table;
    }

    public function setForeign_key_table(string $foreign_key_table) : void
    {
        $this->foreign_key_table = $foreign_key_table;
    }
 
    public function getUnsigned() : bool
    {
        return $this->unsigned;
    }

    public function setUnsigned(mixed $unsigned=false) : void
    {
        if($unsigned)
        {
            $this->unsigned=true;        
        }
        else
        {
            $this->unsigned=false;     
        }
    }
    
    public function setUnsigned_ByInt(int $unsigned=0) : void
    {
        if($unsigned)
        {
            $this->unsigned=true;        
        }
        else
        {
            $this->unsigned=false;     
        }
    }    
    
    public function getPendingRename() : ?string
    {
        return $this->pendingRename;
    }

    public function setPendingRename(?string $pendingRename) : void
    {
        $this->pendingRename = $pendingRename;
    }
    
    public function getKerror() : string
    {
        return $this->kerror;
    }

    public function setKerror(string $kerror,string $delimiter="\n") : void
    {
        $this->kerror.=$kerror.$delimiter;
    }
    
    public function setNewKerror(string $kerror,string $delimiter="\n") : void
    {
        $this->kerror=$kerror.$delimiter;
    }    
    
    public function isError() : bool
    {
        if($this->kerror!="")
        {
            return true;
        }
        return false;
    }
    
    
    public function toString(string $delimiter="\n") : string
    {
        $array = get_object_vars($this);
        $properties = array_keys($array);
        $string="";
        foreach ($properties as $key => $value) 
        {
            $string .= "$key = $value =>".$this->$value.$delimiter;
        }
        return $string;
    }
    
    //  json_encode($object);
    /**
     * 
     * @return array<int,array<mixed,mixed>>
     */
    public function jsonSerialize() : array
    {
        $array = get_object_vars($this);
        $properties = array_keys($array);
        $arrayJSON=array();
        foreach ($properties as $key => $value) 
        {
            $arrayJSON[$value]=$this->$value;
        }
        return [
            $arrayJSON
        ];   
    }
    
    public function isGeometric() : bool
    {
        $isGeo=false;
        switch($this->type)
        {
            case self::$GEOMETRY :
                $isGeo=true;
                break;
            case self::$LINESTRING :
                $isGeo=true;
                break;
            case self::$POLYGON :
                $isGeo=true;
                break;
            case self::$MULTIPOINT :
                $isGeo=true;
                break;
            case self::$MULTILINESTRING :
                $isGeo=true;
                break;        
        }
        return $isGeo;
    }
    
    public function isSqlStandardQueriable() : bool
    {
        $isQueriable=false;
        switch($this->type)
        {
            case self::$VARCHAR :
                $isQueriable=true;
                break;
            case self::$TEXT :
                $isQueriable=true;
                break;            
//            case self::$INTEGER :
//                $isQueriable=true;
//                break;
//            case self::$FLOAT :
//                $isQueriable=true;
//                break;       
        }
        return $isQueriable;        
    }
    
    public function __destruct()
    {
        
    }
    
    protected function verifFormatDate(string $date, string $format) : bool
    {
        $dt=DateTime::createFromFormat($format, $date);
        if( $dt !== false && $this->testDateTimeLastError($dt->getLastErrors())) 
        {
            return true;
        }
        else if($dt === false)
        {
            $this->setKerror("KField::verifFormatDate ERROR on ".$date);
        }
        else if(is_array($dt->getLastErrors()))
        {
            $stringError=json_encode($dt->getLastErrors());
            $this->setKerror("KField::verifFormatDate =>".$stringError);
        }
        else
        {
            $this->setKerror("KField::verifFormatDate =>date:".$date."|| format:".$format);
        }        
        return false;
    }
    
    protected function testDateTimeLastError(mixed $getLastErrors) : bool
    {
        $status=true;
        if(is_bool($getLastErrors))
        {
            return !$getLastErrors;
        }
        else if(is_array($getLastErrors))
        {
            if(isset($getLastErrors['warning_count'])&&$getLastErrors['warning_count']>0)
            {
                $status=false;
            }
            if(isset($getLastErrors['warnings'])&& is_array($getLastErrors['warnings'])&&count($getLastErrors['warnings'])>0)
            {
                $status=false;
            }  
            if(isset($getLastErrors['error_count'])&&$getLastErrors['error_count']>0)
            {
                $status=false;
            }            
            if(isset($getLastErrors['errors'])&& is_array($getLastErrors['errors'])&&count($getLastErrors['errors'])>0)
            {
                $status=false;
            }                        
        }
        return $status;  
    }
    
    /**
     * 
     * @return array<int,string>
     */
    public static function allTypesToArray() : array
    {      
        $array=[];
        $array[]=self::$INTEGER;
        $array[]=self::$VARCHAR;
        $array[]=self::$TEXT;
        $array[]=self::$FLOAT;
        $array[]=self::$DOUBLE;
        $array[]=self::$BOOL;
        $array[]=self::$DATE;
        $array[]=self::$DATETIME;
        $array[]=self::$TIMESTAMP;
        $array[]=self::$TIME;       
        $array[]=self::$YEAR;
        $array[]=self::$ENUM;
        $array[]=self::$GEOMETRY;
        $array[]=self::$LINESTRING;
        $array[]=self::$POLYGON;
        $array[]=self::$MULTIPOINT;
        $array[]=self::$MULTILINESTRING;
        $array[]=self::$RASTER;
        return $array;
    }
}