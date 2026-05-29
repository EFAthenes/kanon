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
class SqlFieldType
{
    private ?string $tablename=null;
    
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
    
    private ?string $type=null;
    private ?string $length=null;
    /**
     * 
     * @var array<int,string>|null
     */
    private ?array $enums=null;
    private bool $unsigned=false;
    
    private ?string $srid=null;
    private ?string $coord_dimension=null;
      
    /**
     * 
     * @param string $tablename
     * @param array<string,string>|null $field
     * @param string|null $engine_type
     */
    public function __construct(string $tablename,?array $field,?string $engine_type)
    {
        $this->type=self::$UNKNOWN;
        $this->tablename=$tablename;
        $this->initByDB($field,$engine_type);
    }
    
    public function __destruct()
    {
    }
    /**
     * 
     * @param array<string,string>|null $row
     * @param string|null $engine_type
     * @return void
     */
    public function initByDB(?array $row,?string $engine_type) : void
    {
        if(is_array($row)&&!is_null($engine_type))
        {
            if(($engine_type==Sql::$MYSQL||$engine_type==Sql::$MARIADB)&&isset($row['Type']))
            {
                $field=$row['Type'];
                //$field
                if(stringStartsWith($field,"int(")) 
                {
                    $this->length=getStringBetween($field,"(",")");
                    if($this->length=="1")
                    {
                        $this->type=self::$BOOL;
                    }
                    else
                    {
                        $this->type=self::$INTEGER;
                    }
                    $this->enums=null;
                }
                else if($field=="int" || $field=="int unsigned") 
                {
                    $this->type=self::$INTEGER;                   
                    $this->enums=null;
                }                
                else if(stringStartsWith($field,"smallint(")) 
                {
                    $this->length=getStringBetween($field,"(",")");
                    if($this->length=="1")
                    {
                        $this->type=self::$BOOL;
                    }
                    else
                    {
                        $this->type=self::$INTEGER;
                    }
                    $this->enums=null;
                }
                else if($field=="smallint") 
                {
                    $this->type=self::$INTEGER;                   
                    $this->enums=null;
                }
                else if(stringStartsWith($field,"mediumint(")) 
                {
                    $this->length=getStringBetween($field,"(",")");
                    if($this->length=="1")
                    {
                        $this->type=self::$BOOL;
                    }
                    else
                    {
                        $this->type=self::$INTEGER;
                    }
                    $this->enums=null;
                }
                else if($field=="mediumint") 
                {
                    $this->type=self::$INTEGER;                   
                    $this->enums=null;
                }
                else if(stringStartsWith($field,"tinyint(")) 
                {
                    $this->type=self::$BOOL;
                    $this->enums=null;
                }    
                else if($field=="tinyint") 
                {
                    $this->type=self::$BOOL;                   
                    $this->enums=null;
                }
                else if(stringStartsWith($field,"bigint(")) 
                {
                    $this->length=getStringBetween($field,"(",")");
                    if($this->length=="1")
                    {
                        $this->type=self::$BOOL;
                    }
                    else
                    {
                        $this->type=self::$INTEGER;
                    }
                    $this->enums=null;
                }
                else if($field=="bigint") 
                {
                    $this->type=self::$INTEGER;                   
                    $this->enums=null;
                }
                else if(stringStartsWith($field,"double")) 
                {
                    $this->type=self::$DOUBLE;
                    $this->length=null;
                    $this->enums=null;
                }
                else if(stringStartsWith($field,"float")) 
                {
                    $this->type=self::$FLOAT;
                    $this->length=null;
                    $this->enums=null;
                }            
                else if(stringStartsWith($field,"varchar(")) 
                {
                    $this->type=self::$VARCHAR;
                    $this->length=getStringBetween($field,"(",")");
                    $this->enums=null;
                }
                else if(stringStartsWith($field,"text") || stringStartsWith($field,"longtext") || stringStartsWith($field,"mediumtext"))
                {
                    $this->type=self::$TEXT;
                    $this->length=null;
                    $this->enums=null;
                }
                else if(stringStartsWith($field,"datetime"))
                {
                    $this->type=self::$DATETIME;
                    $this->length=null;
                    $this->enums=null;
                }
                else if(stringStartsWith($field,"date"))
                {
                    $this->type=self::$DATE;
                    $this->length=null;
                    $this->enums=null;
                }   
                else if(stringStartsWith($field,"timestamp"))
                {
                    $this->type=self::$TIMESTAMP;
                    $this->length=null;
                    $this->enums=null;
                }
                else if(stringStartsWith($field,"time"))
                {
                    $this->type=self::$TIME;
                    $this->length=null;
                    $this->enums=null;
                }
                else if(stringStartsWith($field,"year"))
                {
                    $this->type=self::$YEAR;
                    $this->length=null;
                    $this->enums=null;
                }
                else if(stringStartsWith($field,"enum"))
                {
                    $this->type=self::$ENUM;
                    $this->length=null;
                    $enums=getStringBetween($field,"(",")");
                    $array_enums=explode (",",$enums);
                    $this->enums=array();
                    for($i=0; $i<count($array_enums); $i++)
                    {
                        $this->enums[]=getStringBetween($array_enums[$i],"'","'");
                    }   
                }
                else if(stringStartsWith($field,"geometry"))
                {
                    $this->type=self::$GEOMETRY;
                    $this->length=null;
                    $this->enums=null;       
                }
                
                if(string_contains("unsigned",$field))
                {
                    $this->unsigned=true;
                }
            }
            else if($engine_type==Sql::$POSTGRES&&isset($row['data_type']))
            {
                $field=$row['data_type'];
                if(stringStartsWith($field,"integer")
                        ||stringStartsWith($field,"smallint")
                        ||stringStartsWith($field,"int")
                        ||stringStartsWith($field,"bigint")
                        ||stringStartsWith($field,"numeric")) 
                {
                    $this->type=self::$INTEGER;
                    //$row['udt_name']
                    //$this->length=getStringBetween($row['udt_name'],"(",")");
                    //$this->length=str_first_replace("int","",$row['udt_name']);
                    $this->enums=null;
                }
                else if(stringStartsWith($field,"float8") 
                        || stringStartsWith($field,"double precision")
                        || stringStartsWith($field,"real")
                        ) 
                {
                    $this->type=self::$DOUBLE;
                    $this->length=null;
                    $this->enums=null;
                }
                else if(stringStartsWith($field,"float4")) 
                {
                    $this->type=self::$FLOAT;
                    $this->length=null;
                    $this->enums=null;
                } 
                else if(stringStartsWith($field,"bool")) 
                {
                    $this->type=self::$BOOL;
                    $this->length=null;
                    $this->enums=null;
                }                   
                else if(stringStartsWith($field,"character varying")) 
                {
                    $this->type=self::$VARCHAR;
                    $this->length=$row['character_maximum_length'];
                    $this->enums=null;
                }
                else if(stringStartsWith($field,"text"))
                {
                    $this->type=self::$TEXT;
                    $this->length=null;
                    $this->enums=null;
                }
                else if(stringStartsWith($field,"datetime"))
                {
                    $this->type=self::$DATETIME;
                    $this->length=null;
                    $this->enums=null;
                }
                else if(stringStartsWith($field,"date"))
                {
                    $this->type=self::$DATE;
                    $this->length=null;
                    $this->enums=null;
                }   
                else if(stringStartsWith($field,"timestamp"))
                {
                    $this->type=self::$TIMESTAMP;
                    $this->length=null;
                    $this->enums=null;
                }
                else if(stringStartsWith($field,"time"))
                {
                    $this->type=self::$TIME;
                    $this->length=null;
                    $this->enums=null;
                }
                else
                {
                    if(stringStartsWith($field,"USER-DEFINED"))
                    {
                        ///echo "TEST==".$field;
                        //udt_name    udt_schema
                        //SELECT * FROM geometry_columns 
                        if(stringStartsWith($row['udt_name'],"geometry"))
                        {
                            $sql=Sql::getInstance();
                            //COLONNE GEO
                            $query="SELECT * FROM geometry_columns WHERE f_table_name='".$this->tablename."' AND f_geometry_column='".$row['column_name']."'";
                            $row2 = $sql->queryFetch_SQL($query);
                            if(!is_null($row2))
                            {
                                //echo "TYPE == ".$row2["type"]."\n\n";
                                
                                if(stringStartsWith($row2["type"],"POINT"))
                                {
                                    $this->type=self::$POINT;
                                }
                                elseif(stringStartsWith($row2["type"],"LINESTRING"))
                                {
                                    $this->type=self::$LINESTRING;
                                }
                                elseif(stringStartsWith($row2["type"],"POLYGON"))
                                {
                                    $this->type=self::$POLYGON;
                                }
                                elseif(stringStartsWith($row2["type"],"GEOMETRY"))
                                {
                                    $this->type=self::$GEOMETRY;
                                }
                                elseif(stringStartsWith($row2["type"],"MULTIPOINT"))
                                {
                                    $this->type=self::$MULTIPOINT;
                                }
                                elseif(stringStartsWith($row2["type"],"MULTILINESTRING"))
                                {
                                    $this->type=self::$MULTILINESTRING;
                                }
                                elseif(stringStartsWith($row2["type"],"MULTIPOLYGON"))
                                {
                                    $this->type=self::$MULTIPOLYGON;
                                }
                                else
                                {
                                    echo "ERROR GEOMETRY TYPE => Table = ".$this->tablename." // Field =".$row['column_name'];
                                    exit();
                                }
                                
                                $this->srid=$row2['srid'];
                                $this->coord_dimension=$row2['coord_dimension'];
                                
//                                echo $query."\n";
//                                echo print_r($row2);
//                                exit();
                            }
                        }
                        else if(stringStartsWith($row['udt_name'],"raster"))
                        {
                            $query="SELECT * FROM raster_columns WHERE f_table_name='".$this->tablename."' AND r_raster_column='".$row['column_name']."'";
                            $this->type=self::$RASTER;
                        }
                        else
                        {
                            //echo "ELSE \n";
                            $query="
                            select n.nspname as enum_schema,  
                                t.typname as enum_name,
                                string_agg(e.enumlabel, ', ') as enum_value
                            from pg_type t 
                                join pg_enum e on t.oid = e.enumtypid  
                                join pg_catalog.pg_namespace n ON n.oid = t.typnamespace
                            Where t.typname='".$row['udt_name']."'
                            group by enum_schema, enum_name";
                            //echo "\n".$query."\n";
                            $results = pg_query($query);
                            if($results && ($row2 = pg_fetch_array($results,NULL, PGSQL_ASSOC)))
                            {
                                $this->type=self::$ENUM;
                                $this->length=null;
                                $this->enums=explode (",",$row2["enum_value"]);
                                //$this->enums=array();
//                                for($i=0; $i<count($array_enums); $i++)
//                                {
//                                    $this->enums[]=getStringBetween($array_enums[$i],"'","'");
//                                }   
                            }
                        }
                    }
                    /*
                    if($row['pg_catalog'])
                    {
                        
                    }
                     * 
                     */
                }
                /*
                else if(stringStartsWith($field,"year"))
                {
                    $this->type=self::$YEAR;
                    $this->length=null;
                    $this->enums=null;
                }
                 * 
                 */
                /*
                else if(stringStartsWith($field,"enum"))
                {
                    $this->type=self::$ENUM;
                    $this->length=null;
                    $enums=getStringBetween($field,"(",")");
                    $array_enums=explode (",",$enums);
                    $this->enums=array();
                    for($i=0; $i<count($array_enums); $i++)
                    {
                        $this->enums[]=getStringBetween($array_enums[$i],"'","'");
                    }   
                }
                 * 
                 */
            }
        }
    }  
    function getType() : string
    {
        return $this->type;
    }

    function getLength() : ?string
    {
        return $this->length;
    }

    function setType(string $type) : void
    {
        $this->type=$type;
    }

    function setLength(?string $length) : void
    {
        $this->length=$length;
    }
    /**
     * 
     * @return array<int,string>|null
     */
    function getEnums() :?array
    {
        return $this->enums;
    }

    /**
     * 
     * @param array<int,string>|null $enums
     * @return void
     */
    function setEnums(?array $enums) : void
    {
        $this->enums=$enums;
    }

    function getUnsigned() : bool
    {
        return $this->unsigned;
    }

    function setUnsigned(bool $unsigned) : void
    {
        $this->unsigned=$unsigned;
    }
    function getTablename() : string
    {
        return $this->tablename;
    }

    function getSrid() :? string
    {
        return $this->srid;
    }

    function getCoord_dimension():?string
    {
        return $this->coord_dimension;
    }

    function setTablename(string $tablename) : void
    {
        $this->tablename=$tablename;
    }

    function setSrid(?string $srid) : void
    {
        $this->srid=$srid;
    }

    function setCoord_dimension(?string $coord_dimension) : void
    {
        $this->coord_dimension=$coord_dimension;
    }
}