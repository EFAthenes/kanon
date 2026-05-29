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
class PostgreSql extends EngineSql
{
    private const COUNT_KW="count";
    private const COUNT_KW_UNIQUE="count(*)";
    
    public function __construct()
    {
        $this->setEngine_type(Sql::$POSTGRES);
        $this->setSchema("public");
        $this->setQuote("\"");
        $this->setDefault_insert_id("DEFAULT");
    }
    public function __destruct()
    {
    }

    public function connect_DB() : bool
    {
        $appName=frenchToURI(ParamManager::getInstance()->app_name.KRandom::makeRandom());
        // New Connection
        //--client_encoding=UTF8
        
        set_error_handler(function (
            int $severity,
            string $message,
            string $file,
            int $line
        ) {
            throw new ErrorException($message, 0, $severity, $file, $line);
        });
        
        try
        {
            $this->server=pg_connect('host='.$this->getHost().' user='.$this->getUser().' password='.$this->getPasswd().' dbname='.$this->getBdd().' connect_timeout=5 options=\'--application_name='.$appName.'\' '); 
            // Check for errors
            if(!$this->server)
            {
                throw new RuntimeException('PostgreSQL connection failed.');        
            }     
        } 
        finally
        {
            restore_error_handler();
            $this->setError("PostgreSql::connect_DB() ==> status!=true ");
            $this->sendError();
            return false;                
        }

 
        pg_set_client_encoding($this->server, "UNICODE");
        $this->setConnected(true);
        return true;
    }
    
    public function connect_DB_Server() : bool
    {
        $appName=frenchToURI(ParamManager::getInstance()->app_name.KRandom::makeRandom());
        // New Connection
        //--client_encoding=UTF8
        $this->server=pg_connect('host='.$this->getHost().' user='.$this->getUser().' password='.$this->getPasswd().' connect_timeout=5 options=\'--application_name='.$appName.'\' '); 

        // Check for errors
        if(!$this->server)
        {
            $this->setError("PostgreSql::connect_DB() ==> status!=true ");
            $this->sendError();
            return false;            
        }      
        pg_set_client_encoding($this->server, "UNICODE");
        //$this->setConnected(true);
        return true;        
    }

    public function disconnect_DB() : bool
    {
//        echo "disconnect_DB()\n";
//        echo print_r($this->server)."\n";
//        if(!$this->getConnected() || is_null($this->server) || !pg_close($this->server))
//        {
//            return false;          
//        }
        $this->setConnected(false);
        return true;
    }

    public function request_SQL(string $query) : mixed
    {
        if(!$this->server)
        {
            $this->setError("PostgreSql::request_SQL() ==> result!=true // Report : Server not connected");                    
        }
        else
        {        
            $timer = new KTimer($query);
            $timer->start();
            $this->query = $query;
            $error=null;
            
            
            set_error_handler(function ($err_severity, $err_msg, $err_file, $err_line)//, array $err_context)
            {
                throw new ErrorException( $err_msg, 0, $err_severity, $err_file, $err_line );
            }, E_WARNING);
            try 
            {
                $this->result = pg_query($this->server, $query);
            } 
            catch (Exception $e) 
            {
                $this->result=false;
                $error = $e->getMessage();
                //KDebugger::getInstance()->dump($e->getMessage(),"Exception");
                //KDebugger::getInstance()->dump($query,"Query Exception");
            }
            restore_error_handler();
            
            if(!$this->result)
            {
                if($error)
                {
                    $this->setError("PostgreSql::request_SQL() ==> pg_query -> result!=true // Report :" . $error." // Query :".$this->query);
                }
                else
                {
                    $this->setError("PostgreSql::request_SQL() ==> pg_query -> result!=true // Report :" . pg_last_error()." // Query :".$this->query);
                }
                $this->sendError();
                $this->result = null;
            }
            $timer->stop();
            return $this->result;
        }
        return null;
    }

    public function request_SQL_NoLogs(string $query) : mixed
    {
        if(!$this->server)
        {
            $this->setError("PostgreSql::request_SQL_NoLogs() ==> result!=true // Report : Server not connected");                    
        }
        else
        {        
            $this->query = $query;
            $this->result = pg_query($this->server, $query);
            if(!$this->result)
            {
                $this->setError("PostgreSql::request_SQL_NoLogs() -> result!=true // Report :" . pg_last_error()." ");
                $this->result = null;
            }
            return $this->result;
        }
        return null;
    }

    public function query_SQL_bool(string $query) : bool
    {
        if(!$this->server)
        {
            $this->setError("PostgreSql::query_SQL_bool() ==> result!=true // Report : Server not connected");                    
        }
        else
        {
            $this->query = $query;
            $this->result = pg_query($this->server, $query);
            if(!$this->result)
            {
                $this->setError("PostgreSql::query_SQL_bool() -> result!=true // Report :" . pg_last_error()." ");
                $this->sendError();
                return false;
            }
            return true;     
        }
        return false;
    }

    public function queryFetch_SQL(string $query) : mixed
    {
        if(!$this->server)
        {
            $this->setError("PostgreSql::queryFetch_SQL() ==> result!=true // Report : Server not connected");                    
        }
        else
        {         
            $this->query = $query;
            $this->result = pg_query($this->server, $query);
            if(!$this->result)
            {
                $this->setError("PostgreSql::queryFetch_SQL() -> result!=true // Report :" . pg_last_error()." ");
                $this->sendError();
                return null;
            }
            return pg_fetch_array($this->result,NULL, PGSQL_ASSOC);    
        }
        return null;
    }
    
    public function num_rows_from_results() : int
    {
        if(is_null($this->result))
        {
            $this->setError("PostgreSql::num_rows_from_result() ==> this->result -> NULL ");
            return 0;
        }
        $number =pg_num_rows($this->result);
        return $number;        
    }    

    public function num_rows(string $query) : int
    {
        $number=-1;
        if(!$this->server)
        {
            $this->setError("PostgreSql::num_rows() ==> result!=true // Report : Server not connected");                    
        }
        else
        {         
            $this->query = $query;
            $this->result = pg_query($this->server, $query);
            if(!$this->result)
            {
                $this->setError("PostgreSql::num_rows() ==> pg_query -> result!=true // Report :" . pg_last_error()." ");
                $this->sendError();
                return 0;
            }
            $number =pg_num_rows($this->result);
            //echo "ROW =>".$number."\n";
        }
        return $number;        
    }
    
    public function num_rows_table(string $table) : int
    {
        $number=-1;
        if(!$this->server)
        {
            $this->setError("PostgreSql::num_rows_table() ==> result!=true // Report : Server not connected");                    
        }
        else
        {        
            $query="SELECT ".self::COUNT_KW_UNIQUE." FROM ".$this->quoteString($table);
            $this->query = $query;
            $this->result = pg_query($this->server, $query);
            if(!$this->result)
            {
                $this->setError("PostgreSql::num_rows_table() ==> query -> result!=true // Report :" . pg_last_error()." ");
                $this->sendError();
                return 0;
            }
            $number=0;
            if($row=pg_fetch_row($this->result))
            {
                //echo print_r($row);
                $number=intval($row[0]);
            }
            //echo "ROW =>".$number."\n";
        }
        return $number;        
    }
    
    public function num_fields_table(string $table) : int
    {
        $number=-1;
        if(!$this->server)
        {
            $this->setError("PostgreSql::num_fields_table() ==> result!=true // Report : Server not connected");                    
        }
        else
        {
            $query="select ".self::COUNT_KW_UNIQUE." from INFORMATION_SCHEMA.COLUMNS where table_name ='".$table."'";
            $this->query = $query;
            //echo "<br />".$query."<br />";
            $this->result = pg_query($this->server, $query);
            if(!$this->result)
            {
                $this->setError("PostgreSql::num_fields_table() ==> query -> result!=true // Report :" . pg_last_error()." ");
                $this->sendError();
                return 0;
            }
            $number=0;
            if($row=pg_fetch_row($this->result))
            {
                //echo print_r($row);
                $number=intval($row[0]);
            }
            //echo "ROW =>".$number."\n";
        }
        return $number;        
    }    

    public function fetch_array(mixed $results): array|null|false
    {
        return pg_fetch_array($results,NULL, PGSQL_ASSOC);
    }
    
    public function real_escape_string(string $string) : string
    {
        return pg_escape_string($this->server,$string);
    }
    
    public function getLastId() : int
    {
        if(!$this->server)
        {
            $this->setError("PostgreSql::getLastId() ==> result!=true // Report : Server not connected <br />");                    
        }
        else
        {         
            $result = pg_query($this->server,"SELECT lastval();");
            if(!$result)
            {
//                $this->error = "PostgreSql::getLastId() -> result!=true // Report :" . pg_last_error()." <br />";
//                $this->sendError();
                return 1;            
            }
            $insert_row = pg_fetch_row($result);
            if(is_array($insert_row)&&count($insert_row))
            {
                return intval($insert_row[0]);
            }
        }
        return 0;
    }
    
    public function getLastModificationForTable(string $tablename) : string
    {
        $last_date_modified = "";   
        if(!$this->server)
        {
            $this->setError("PostgreSql::getLastModification() ==> result!=true // Report : Server not connected <br />");                    
        }
        else
        {        
            $result = pg_query($this->server,"SELECT date_modified FROM ".$tablename." ORDER BY date_modified DESC Limit 1 ");
            if(!$result)
            {
                $this->setError("PostgreSql::getLastModification() -> result!=true // Report :" . pg_last_error()." <br />");
                $this->sendError();
                return "";           
            }
            if(($row = $this->fetch_array($result)) != NULL) 
            {
                $last_date_modified=$row["date_modified"];
                if(is_null($last_date_modified))
                {
                    $last_date_modified="";
                }
            }
        }
        return $last_date_modified;
    }
    
    
    public function showTables() : mixed
    {
        $result = null;            
        if(!$this->server)
        {
            $this->setError("PostgreSql::showTables() ==> result!=true // Report : Server not connected <br />");
        }
        else
        {
            $this->query="SELECT * FROM pg_catalog.pg_tables WHERE schemaname != 'pg_catalog' AND schemaname != 'information_schema' AND tablename!= 'spatial_ref_sys' ORDER BY tablename ";
            $result = pg_query($this->server,$this->query);
            if(!$result)
            {
                $this->setError("PostgreSql::showTables() ==> result!=true // Report :" . pg_last_error() . " <br />");
                $result = null;
            }
        }
        return $result;          
    }
    
    public function getFieldShowTableName() : string
    {
        return "tablename";
    }
    //TODO
    public function copyRowToAnotherTable(?string $initial_query,?string $toTable) : bool
    {
        if(is_null($toTable) || is_null($initial_query))
        {
            return false;
        }        
        return false;              
    }   
    
    public function showColumnInformation(?string $tableName) : array
    {
        if(is_null($tableName)||empty($tableName))
        {
            return [];
        }         
        $arrayFields=array();
        if(!$this->server)
        {
            $this->setError("PostgreSql::showColumnInformation() ==> result!=true // Report : Server not connected <br />");                    
        }
        else
        { 
            $this->query = "select * from INFORMATION_SCHEMA.COLUMNS where table_name = '".$tableName."' ORDER BY ordinal_position";
            $results = pg_query($this->server,$this->query);
            if(!$results)
            {
                $this->setError("PostgreSql::showColumnInformation() ==> result!=true // Report :" .pg_last_error() . " <br />");
                $results = null;
            }            
            if($results != null)
            {
                while (($row = $this->fetch_array($results)) != NULL) 
                {
    //                if($row['column_name']=="id")
    //                {
    //                    echo print_r($row);
    //                }
                    $field=new SqlField();
                    $field->initByDB($tableName,$row,$this->getEngine_type());
                    $arrayFields[]=$field;
                }
            }
        }
        return $arrayFields;       
    }
    
    public function createIndex(string $tablename, array $arrayFields) : bool
    {
        $stringFields="";
        $stringIndexName=$tablename;
        $separator="_";
        foreach ($arrayFields as $field)
        {
            if($stringFields!="")
            {
                $stringFields.=",";
            }
            $stringFields.=$field;
            $stringIndexName.=$separator.$field;
        }
        
        $alreadyExists=false;
        $this->query = "SELECT tablename,indexname,indexdef FROM pg_indexes WHERE schemaname = '".$this->getSchema()."' AND tablename ='".$tablename."' AND indexname='".$stringIndexName."'";
        $results = pg_query($this->server,$this->query);
        if($results != null)
        {
            if (($row = $this->fetch_array($results)) != NULL) 
            {
                $alreadyExists=true;
            }   
        }
        
        if(!$alreadyExists)
        {
            $this->query = "CREATE INDEX ".$stringIndexName." ON ".$tablename."(".$stringFields.");";
            $result = pg_query($this->server,$this->query);
            if(!$result)
            {
                $this->setError("PostgreSql::createIndex() ==> result!=true // Report :" . pg_last_error());
                return false;
            } 
        }
        return true;
    }
    
    public function getFieldShowColumnName() : string
    {
        return "Field";
    }   
    
    public function addFieldDateCreated(string $tableName) : bool
    {
        if(!$this->server)
        {
            $this->setError("PostgreSql::addFieldDateCreated() ==> result!=true // Report : Server not connected <br />");                    
        }
        else
        {        
            $this->query="ALTER TABLE ".$this->quoteString($tableName)." ADD date_created timestamp(6) DEFAULT NULL";
            if(pg_query($this->server,$this->query))
            {
                return true;
            }
            $this->setError("PostgreSql::addFieldDateCreated() ==> result!=true // Report :" .pg_last_error());
        }
        return false;        
    }

    public function addFieldDateModified(string $tableName) : bool
    {
        if(!$this->server)
        {
            $this->setError("PostgreSql::addFieldDateModified() ==> result!=true // Report : Server not connected");                    
        }
        else
        { 
            $this->query="ALTER TABLE ".$this->quoteString($tableName)." ADD date_modified timestamp(6) DEFAULT NULL";
            if(pg_query($this->server,$this->query))
            {
                return true;
            }
            $this->setError("PostgreSql::addFieldDateModified() ==> result!=true // Report :" .pg_last_error());
        }
        return false;        
    }

    public function addFieldId(string $tableName) : bool
    {
        if(!$this->server)
        {
            $this->setError("PostgreSql::addFieldId() ==> result!=true // Report : Server not connected");                    
        }
        else
        {        
            $this->query="ALTER TABLE ".$this->quoteString($tableName)." ADD id INTEGER DEFAULT NULL";
            if(pg_query($this->server,$this->query))
            {
                return $this->setFieldIdPK($tableName);
            }
            $this->setError("PostgreSql::addFieldId() ==> result!=true // Report :" .pg_last_error());
        }
        return false;  
    } 
    
    // CHECK IF I HAVE TO QUOTE all the package
    public function setFieldIdPK(string $tableName) : bool
    {
        if(!$this->server)
        {
            $this->setError("PostgreSql::setFieldIdPK() ==> result!=true // Report : Server not connected");                    
        }
        else
        {  
            $sequence_name=$this->createSequenceName($tableName,"id");
            
            $query_test_seq_exists="SELECT COUNT(*) FROM information_schema.sequences WHERE sequence_name='".$sequence_name."'";
            
            
            $result = pg_query($this->server, $query_test_seq_exists);
            if(!$result)
            {
                $this->setError("PostgreSql::setFieldIdPK() ==> query -> result!=true // Report :" . pg_last_error()." ");
                $this->sendError();
                return false;
            }
            $number=0;
            if($row=pg_fetch_row($result))
            {
                $number=$row[0];
            }  
            
            if($number>0)
            {
                $this->setError("PostgreSql::setFieldIdPK() ==> query -> Sequence name already exists : ".$sequence_name);
                $this->sendError();
                return false;                
            }
            
            
            
            $this->query= "CREATE SEQUENCE ".$sequence_name." OWNED BY ".$tableName.".id;
ALTER TABLE ".$tableName." ADD PRIMARY KEY (id);
ALTER TABLE ".$tableName." ALTER COLUMN id SET DEFAULT nextval('".$this->createSequenceName($tableName,"id")."');
UPDATE ".$tableName." SET id = DEFAULT; 
";
        //$this->query="ALTER TABLE ".$tableName." ADD PRIMARY KEY(id);";
            if(pg_query($this->server,$this->query))
            {
                return true;
            }
            $this->setError("PostgreSql::setFieldIdPK() ==> result!=true // Report :" .$this->server->error);
        }
        return false;        
    }
    
    public function setFieldIdAutoIncrement(string $tableName) : bool
    {
        if(!$this->server)
        {
            $this->setError("PostgreSql::setFieldIdPK() ==> result!=true // Report : Server not connected");                    
        }
        else
        {        
            $this->query= "CREATE SEQUENCE ".$this->createSequenceName($tableName,"id")." OWNED BY ".$tableName.".id;
ALTER TABLE ".$tableName." ALTER COLUMN id SET DEFAULT nextval('".$this->createSequenceName($tableName,"id")."');
";
            //$this->query="ALTER TABLE ".$tableName." ADD PRIMARY KEY(id);";
            if(pg_query($this->server,$this->query))
            {
                return true;
            }
            $this->setError("PostgreSql::setFieldIdAutoIncrement() ==> result!=true // Report :" .$this->server->error);
        }
        return false;        
    }
    
    public function getQuerySelectDistinct(string $table,string $fieldName,string $where_request="",string $full_outer_join="",bool $count=false) : string
    {
        if($where_request!="")
        {
            $where_request=" WHERE ".$where_request;
        }    
        $array=explode(",",trim($fieldName));
        if(count($array)>1)
        {
            $fieldName="";
            foreach($array as $field)
            {
                if($fieldName!="")
                {
                    $fieldName.=",".$this->quoteString($table).".".$this->quoteString($field);
                }
                else
                {
                    $fieldName.=" DISTINCT(".$this->quoteString($table).".".$this->quoteString($field).")";
                    if($count)
                    {
                         $fieldName.=",".self::COUNT_KW."(".$this->quoteString($table).".".$this->quoteString($field).")";
                    }
                }
            }
        }
        else
        {
            $fieldName=" DISTINCT(".$this->quoteString($table).".".$this->quoteString($fieldName).")";
        }
        return "SELECT ".$fieldName." FROM ".$this->quoteString($table)." ".$full_outer_join." ".$where_request;
    }    
    
    public function getQuerySelectAllById(string $tables,string $select_for_alias, string $where_request) : string
    {
        return "SELECT ".$select_for_alias." FROM ".$tables." WHERE ".$where_request;
    }
    public function getQueryDeleteById(string $table,string $value_id) : string
    {
        return "DELETE FROM ".$this->quoteString($table)." WHERE ".$this->quoteString(KObject::$ID)."='".$this->real_escape_string($value_id)."'";
    }
    
    
    public function getPrefixLikeSQL(string $field,bool $unaccent) : string
    {
        if(!$unaccent)
        {
            return "unaccent(".$field.")";
        }
        return $field;
    }
    
    public function getLikeSQL(string $field,bool $case_sensitive,bool $unaccent) : string
    {
        $string="";
        if($case_sensitive)
        {
            $string.=" LIKE ";
        }
        else
        {
            $string.=" ILIKE ";
        }
        
        if(!$unaccent)
        {
            //$string.="".replace_accent($field)."";
            $string.="unaccent(".$field.")";
        }
        else
        {
            $string.=$field;
        }
        return $string;
    }
    
    public function getNotLikeSQL(string $field,bool $case_sensitive,bool $accent_sensitive) : string
    {
        return ' NOT '.$this->getLikeSQL($field,$case_sensitive,$accent_sensitive);
    }    
        
    
    public function getWholeWordSQL(string $field,bool $case_sensitive,bool $unaccent) : string
    {
        $string="";
        if($case_sensitive)
        {
            $string.=" LIKE ";
        }
        else
        {
            $string.=" ILIKE ";
        }
        
        if($unaccent)
        {
            //$string.="".replace_accent($field)."";
            $string.="unaccent(".$field.")";
        }
        else
        {
            $string.=$field;
        }
        return $string;
    } 
       
    #[\Override]
    public function getGeomIntersectSQL(string $field,string $value) : string
    {
        $string =" ST_Intersects(ST_GeomFromText('".$value."'), ".$field." )=true ";
        return $string;
    } 
    
    #[\Override]
    public function getGeomContainsSQL(string $field,string $value) : string
    {
        $string =" ST_Contains(ST_GeomFromText('".$value."'), ".$field." )=true ";
        return $string;
    }
    #[\Override]
    public function getGeomIsContainedSQL(string $field,string $value) : string
    {
        $string =" ST_Contains(".$field.",ST_GeomFromText('".$value."') )=true ";
        return $string;
    }    
    
    public function makeLimitString(?SqlLimit $limit=null) : string
    {
        $sql_limit="";
        if(is_null($limit))
        {
            return $sql_limit;
        }
        $nb_limit= intval($limit->getLimit());
        $offset=intval($limit->getOffset());
        
        if($nb_limit>0)
        {
            $position_start=0;
            if($offset>0)
            {
                $position_start=$offset;
            }
            $sql_limit=" LIMIT ".$nb_limit." OFFSET ".$position_start;
        }
        return $sql_limit;
    }
    public function makeOrderString(SqlOrder $order) : string
    {
        $sql_order="";
        $field_order=$order->getField();
        $table_order= $order->getTable();
        $type=$order->getType();
        if(!empty($field_order))
        {
            if($order->isOrderByCount())
            {
                $sql_order="ORDER BY ".$this->countKeyWord()." ".$type;
            }
            else
            {
                $sql_order="ORDER BY ".$this->quoteString($table_order).".".$this->quoteString($field_order)." ".$type;
            }
            $arrayOrder=$order->getArraySqlOrder();
            if(!is_null($arrayOrder))
            {
                foreach ($arrayOrder as $order_sql)
                {
                    $field_order=$order_sql->getField();                   
                    $type=$order_sql->getType();
                    if(!empty($field_order))
                    {
                        if($order_sql->isOrderByCount())
                        {
                            $sql_order.=",".$this->countKeyWord()." ".$type;
                        }
                        else
                        {                        
                            $sql_order.=",".$this->quoteString($table_order).".".$this->quoteString($field_order)." ".$type;
                        }
                    }                    
                }
            }
        }
        return $sql_order;
    } 
    
    //##########################################################################
    // TODO
    //##########################################################################
    public function isDatabaseExisting(?string $database) : bool
    {
//        if(is_null($database))
//        {
//            $this->setError("PostgreSql::isDatabaseExisting() ==> result!=true // database is NULL ");
//            return false;
//        }
//        else
        if(!is_string($database))
        {
            $this->setError("PostgreSql::isDatabaseExisting() ==> result!=true // database is not a string ");
            return false;            
        }
        
        $status=false;
        $already_connected=true;
        if(!$this->getConnected())
        {
            if(!$this->connect_DB_Server())
            {
                $this->setError("PostgreSql::isDatabaseExisting() ==> cannot connect to the Database!");
                return false;
            }
            $already_connected=false;
        } 
        $query="SELECT datname FROM pg_catalog.pg_database WHERE lower(datname) = lower('".$this->real_escape_string($database)."')";
        //echo $query;
        if($this->num_rows($query))
        {
            $status=true;
        }
        
        if(!$already_connected)
        {
            $this->disconnect_DB();
        }
        
        return $status;        
    }   
    
    public function isTableExisting(?string $table) : bool
    {
//        if(is_null($table))
//        {
//            $this->setError("PostgreSql::isTableExisting() ==> result!=true // table is NULL ");
//            return false;
//        }
//        else
        if(!is_string($table))
        {
            $this->setError("PostgreSql::isTableExisting() ==> result!=true // table is not a string ");
            return false;            
        }          
         
        $status=false;
        $already_connected=true;
        if(!$this->getConnected())
        {
            if(!$this->connect_DB())
            {
                $this->setError("PostgreSql::isTableExisting() ==> cannot connect to the server !");
                return false;
            }
            $already_connected=false;
        } 
        
        $query="SELECT 1
   FROM   information_schema.tables 
   WHERE  table_schema = '".$this->getSchema()."'
   AND    table_name = '".$table."'";
        //echo $query;
        if($this->num_rows($query))
        {
            $status=true;
        }
        if(!$already_connected)
        {
            $this->disconnect_DB();
        }
        
        return $status;
    }     
    
    public function makeDataBase(?string $database) : bool
    {
//        if(is_null($database))
//        {
//            $this->setError("PostgreSql::makeDataBase() ==> result!=true // database is NULL ");
//            return false;
//        }
//        else
        if(!is_string($database))
        {
            $this->setError("PostgreSql::makeDataBase() ==> result!=true // database is not a string ");
            return false;            
        }
        elseif(!isAlphaNumeric($database))
        {
            $this->setError("PostgreSql::makeDataBase() ==> result!=true // database contains forbidden chars ");
            return false;            
        }        

        $status=false;
        $already_connected=true;
        if(!$this->getConnected())
        {
            if(!$this->connect_DB_Server())
            {
                $this->setError("PostgreSql::makeDataBase() ==> cannot connect to the database!");
                return false;
            }
            $already_connected=false;
        } 
        
        $query="CREATE DATABASE ".$this->real_escape_string($database)."";
        if(pg_query($this->server,$query))
        {
            $query="CREATE EXTENSION postgis; CREATE EXTENSION unaccent;";
            $postgres=new PostgreSql();
            $postgres->setBdd($database);
            $postgres->setHost($this->getHost());
            $postgres->setUser($this->getUser());
            $postgres->setPasswd($this->getPasswd());
            $postgres->connect_DB();
            if($postgres->request_SQL($query))
            {
                $status=true;
            }
            else
            {
                $this->setError("PostgreSql::makeDataBase() ==> cannot enable POSTGIS EXTENSION FOR ``".$database."``!" .$postgres->getError());
            }
            $postgres->disconnect_DB();
        }
        else
        {
            $this->setError("PostgreSql::makeDataBase() ==> cannot create DB ``".$database."``!" .pg_last_error($this->server));
        }
        
        if(!$already_connected)
        {
            $this->disconnect_DB();
        }
        
        return $status; 
    } 
    
    public function updateAutoincrementFieldToMax(?string $tablename,?string $field) : bool
    {
        if(is_null($tablename))
        {
            $this->setError("PostgreSql::updateAutoincrementFieldToMax() ==> result!=true // Tablename is NULL ");
            return false;            
        } 
        if(is_null($field))
        {
            $this->setError("PostgreSql::updateAutoincrementFieldToMax() ==> result!=true // Field is NULL ");
            return false;            
        }          
        $status=false;
        $already_connected=true;
        if(!$this->getConnected())
        {
            if(!$this->connect_DB())
            {
                $this->setError("PostgreSql::updateAutoincrementFieldToMax() ==> cannot connect!" .pg_last_error());
                return false;
            }
            $already_connected=false;
        }
        
        $query="SELECT setval('".$this->createSequenceName($tablename,$field)."', COALESCE((SELECT MAX(".$field.")+1 FROM ".$this->getQuote().$tablename.$this->getQuote()."), 1), false);";
        if($this->request_SQL($query))
        {
            $status=true;
        }
        else
        {
            $this->setError("PostgreSql::updateAutoincrementFieldToMax() ==> SQL ERROR!" .pg_last_error());
        }
        
        if(!$already_connected)
        {
            $this->disconnect_DB();
        }        
        return true;
    }
    
    private function createSequenceName(string $tablename,string $fieldname) : string
    {
        return $this->getSchema()."_".$tablename."_".$fieldname."_seq";
    }
    
    public function createModelInDb(?string $tablename,ArrayList $fields) : bool
    {
        $status=false;
//        if(is_null($tablename))
//        {
//            $this->setError("PostgreSql::createModelInDb() ==> result!=true // Tablename is NULL ");
//            return false;            
//        }
//        else
        if(!is_string($tablename))
        {
            $this->setError("PostgreSql::createModelInDb() ==> result!=true // Tablename is not a string ");
            return false;            
        }
        
        if($this->isTableExisting($tablename))
        {
            $this->setError("PostgreSql::createModelInDb() ==> result!=true // Table already '".$tablename."' exists");
            return false;
        }
        
        if($fields->getSize()==0)
        {
            $this->setError("PostgreSql::createModelInDb() ==> result!=true // Fields not present");
            return false;            
        }
        
        
        $query2=$this->makeQueryFromListKFields($tablename,$fields);
        
        //echo $query2;
        if(!empty($query2))
        {
$query="CREATE TABLE \"".$this->real_escape_string($this->getSchema())."\".\"".$this->real_escape_string($tablename)."\" (".$query2.")";

            if(!$this->request_SQL($query))
            {
                $this->setError("PostgreSql::createModelInDb() ==> ERROR WHEN WE CREATE TABLE ".$tablename);               
            }
            else
            {
                $status=true;
            }
        }
        return $status;
    }
    
    private function makeQueryFromListKFields(string $tablename,ArrayList $fields,bool $alter=false) : string
    {
        $query2="";
        $query_key="";
        $status=true;
        $kField= new KFieldUnKnown();
        for($i=0; $i<$fields->getSize(); $i++)
        {            
            if($i!=0)
            {
                $query2.=",
";                
            }
            
            if($alter)
            {
                $query2.=" ADD COLUMN ";
            }
            
            $kField=$fields->get($i);
            if($kField instanceof KFieldInteger)
            {
                $queryInteger="";
                $queryInteger.="\"".$kField->getName()."\" int4 ";

                if($kField->getAuto_increment())
                {
                    // CREATE SEQUENCE
                    $querySequence="CREATE SEQUENCE ".$this->createSequenceName($tablename,$kField->getName()).";"; // OWNED BY ".$tablename.".".$kField->getName().";";
                    if(!$this->request_SQL($querySequence))
                    {
                        $this->setError("PostgreSql::createModelInDb() ==> ERROR WHEN SEQUENCE => ".$querySequence." ==> ".$this->getError());
                        $status=false; 
                    }
                    $queryInteger.=" DEFAULT nextval('".$this->createSequenceName($tablename,$kField->getName())."'::regclass) ";
                }                
                else if(!is_null($kField->getDefault())&& $kField->getDefault()!="" )
                {
                    $queryInteger.=" DEFAULT '".$kField->getDefault()."' ";
                } 
                else if($kField->getIs_null())
                {
                    $queryInteger.=" DEFAULT NULL ";
                }              
                else// if(!$kField->getIs_null())
                {
                    $queryInteger.=" NOT NULL DEFAULT 0 ";
                }                
                
                $query2.=$queryInteger;  
            }
            else if($kField instanceof KFieldBool)
            {
                $query2.="\"".$kField->getName()."\" bool ";
                if(!$kField->getIs_null())
                {
                    $query2.=" NOT NULL ";
                }
                if(!is_null($kField->getDefault())&& $kField->getDefault()!="")
                {
                    $query2.=" DEFAULT '".$kField->getDefault()."' ";
                } 
                else if($kField->getIs_null())
                {
                    $query2.=" DEFAULT NULL ";
                }
                else
                {
                    $query2.=" DEFAULT FALSE ";
                }
            }            
            else if($kField instanceof KFieldFloat)
            {
                $query2.="\"".$kField->getName()."\" float ";
                if(!$kField->getIs_null())
                {
                    $query2.=" NOT NULL ";
                }
                if(!is_null($kField->getDefault())&& $kField->getDefault()!="")
                {
                    $query2.=" DEFAULT '".$kField->getDefault()."' ";
                } 
                else if($kField->getIs_null())
                {
                    $query2.=" DEFAULT NULL ";
                }
            }
            else if($kField instanceof KFieldDouble)
            {
                $query2.="\"".$kField->getName()."\" real ";
                if(!$kField->getIs_null())
                {
                    $query2.=" NOT NULL ";
                }
                if(!is_null($kField->getDefault())&& $kField->getDefault()!="")
                {
                    $query2.=" DEFAULT '".$kField->getDefault()."' ";
                } 
                else if($kField->getIs_null())
                {
                    $query2.=" DEFAULT NULL ";
                }
            }            
            else if($kField instanceof KFieldVarChar)
            {
                $query2.="\"".$kField->getName()."\" varchar(".$kField->getLength().") ";
                if(!$kField->getIs_null())
                {
                    $query2.=" NOT NULL ";
                }
                if(!is_null($kField->getDefault()))
                {
                    $query2.=" DEFAULT '".$kField->getDefault()."' ";
                }
                else if($kField->getIs_null())
                {
                    $query2.=" DEFAULT NULL ";
                }
            }
            else if($kField instanceof KFieldText)
            {
                $query2.="\"".$kField->getName()."\" text ";
                if(!$kField->getIs_null())
                {
                    $query2.=" NOT NULL ";
                    $query2.=" DEFAULT '".$kField->getDefault()."' ";
                } 
                else// if($kField->getIs_null())
                {
                    $query2.=" DEFAULT NULL ";
                }
            }             
            else if($kField instanceof KFieldDateTime)
            {
                $query2.="\"".$kField->getName()."\" timestamp ";
                if(!$kField->getIs_null())
                {
                    $query2.=" NOT NULL ";
                }
                else if($kField->getDefault()&& $kField->getDefault()!="")
                {
                    $query2.=" DEFAULT '".$kField->getDefault()."' ";
                }
                else// if($kField->getIs_null())
                {
                    $query2.=" DEFAULT NULL ";
                }
            } 
            else if($kField instanceof KFieldDate)
            {
                $query2.="\"".$kField->getName()."\" date ";
                if(!$kField->getIs_null())
                {
                    $query2.=" NOT NULL ";
                }
                else if($kField->getDefault()&& $kField->getDefault()!="")
                {
                    $query2.=" DEFAULT '".$kField->getDefault()."' ";
                }
                else// if($kField->getIs_null())
                {
                    $query2.=" DEFAULT NULL ";
                }
            }
            else if($kField instanceof KFieldTime)
            {
                $query2.="\"".$kField->getName()."\" time ";
                if(!$kField->getIs_null())
                {
                    $query2.=" NOT NULL ";
                }
                else if($kField->getDefault()&& $kField->getDefault()!="")
                {
                    $query2.=" DEFAULT '".$kField->getDefault()."' ";
                }
                else// if($kField->getIs_null())
                {
                    $query2.=" DEFAULT NULL ";
                }
            }
            else if($kField instanceof KFieldYear)
            {
                $query2.="\"".$kField->getName()."\" varchar(4) ";
                if(!$kField->getIs_null())
                {
                    $query2.=" NOT NULL ";
                }
                else if($kField->getDefault()&& $kField->getDefault()!="")
                {
                    $query2.=" DEFAULT '".$kField->getDefault()."' ";
                }
                else// if($kField->getIs_null())
                {
                    $query2.=" DEFAULT NULL ";
                }
            }
            else if($kField instanceof KFieldTimeStamp)
            {
                $query2.="\"".$kField->getName()."\" timestamp ";
                if(!$kField->getIs_null())
                {
                    $query2.=" NOT NULL ";
                }
                else if($kField->getDefault()&& $kField->getDefault()!="")
                {
                    $query2.=" DEFAULT '".$kField->getDefault()."' ";
                }
                else// if($kField->getIs_null())
                {
                    $query2.=" DEFAULT NULL ";
                }
            } 
            else if($kField instanceof KFieldGeometry)
            {
                $query2.="\"".$kField->getName()."\" public.geometry ";
                if(!empty($kField->getGeometry_type())
                     && !empty($kField->getSrid()))
                {
                    $query2.="(".$kField->getGeometry_type().",".$kField->getSrid().")";
                }
                if(!$kField->getIs_null())
                {
                    $query2.=" NOT NULL ";
                }                
                //exemple => geom public.geometry(MultiPolygon,6312),
            }             
            else
            {
                $this->setError("PostgreSql::createModelInDb() ==> Unknown Field type present ".$kField->getName());
                $status=false;
            }
            
            
            // check if Field is key
            if($kField->getPrimary_key())
            {
                $query_key=",PRIMARY KEY  (\"".$kField->getName()."\") ";
            }
        }
        
        $query2.=$query_key;
        
        return $query2;
    }
    
    public function convertToBool(mixed $var) : string
    {
        $result=convertToBool($var);
        if($result===true)
        {
            return "t";
        }
        else if($result===false)
        {
            return "f";
        }
        return "f";
    }
    
    public function addFields(string $tableName,ArrayList $kfields) : bool
    {
        if(!$this->server)
        {
            $this->setError("PostgreSql::addFields() ==> result!=true // Report : Server not connected <br />");                    
        }
        else
        {       
            $query2=$this->makeQueryFromListKFields($tableName,$kfields,true);
            if(!empty($query2))
            {
                
                $this->query="ALTER TABLE \"".$this->real_escape_string($this->getSchema())."\".\"".$this->real_escape_string($tableName)."\" ".$query2;
                //KDebugger::getInstance()->dump($this->query ,"Query Col");
                if(pg_query($this->server,$this->query))
                {
                    return true;
                }
                $this->setError("PostgreSql::addFields() ==> result!=true // Report :" .pg_last_error());
            }
            else
            {
                $this->setError("PostgreSql::addFields() ==> result!=true // No Fields to ADD ");
            }
        }
        return false;
    }
    
    public function dropFields(string $tableName,ArrayList $kfields) : bool
    {
        if(!$this->server)
        {
            $this->setError("PostgreSql::dropFields() ==> result!=true // Report : Server not connected <br />");                    
        }
        else
        {       
            $query2=null;
            /* @var $kfield KField */
            foreach ($kfields as $kfield)
            {
                if(!is_null($query2))
                {
                    $query2.=",";
                }
                $query2.=" drop column ".$kfield->getName()." ";
            }
            if(!is_null($query2))
            {  
                $this->query="ALTER TABLE \"".$this->real_escape_string($this->getSchema())."\".\"".$this->real_escape_string($tableName)."\" ".$query2;
                //KDebugger::getInstance()->dump($this->query ,"Query Col");
                if(pg_query($this->server,$this->query))
                {
                    return true;
                }
                $this->setError("PostgreSql::dropFields() ==> result!=true // Report :" .pg_last_error());
            }
            else
            {
                $this->setError("PostgreSql::dropFields() ==> result!=true // No Fields to Drop ");
            }
        }
        return false;
    }   
    
    public function renameFields(string $tableName,ArrayList $kfields) : bool
    {
        if(!$this->server)
        {
            $this->setError("PostgreSql::renameFields() ==> result!=true // Report : Server not connected <br />");                    
        }
        else
        {       
            $query2=null;
            /* @var $kfield KField */
            foreach ($kfields as $kfield)
            {
                if(!is_null($query2))
                {
                    $query2.=",";
                }
                if( (!is_null($kfield)) && ($kfield instanceof KField) 
                        && (!empty($kfield->getName())) 
                                && (!is_null($kfield->getPendingRename())) 
                        )
                {
                    $query2.=" rename column ".$kfield->getName()." to ".$kfield->getPendingRename()." ";
                }
            }
            if(!is_null($query2))
            {  
                $this->query="ALTER TABLE \"".$this->real_escape_string($this->getSchema())."\".\"".$this->real_escape_string($tableName)."\" ".$query2;
                //KDebugger::getInstance()->dump($this->query ,"Query Col");
                if(pg_query($this->server,$this->query))
                {
                    return true;
                }
                $this->setError("PostgreSql::renameFields() ==> result!=true // Report :" .pg_last_error());
            }
            else
            {
                $this->setError("PostgreSql::renameFields() ==> result!=true // No Fields to Drop ");
            }
        }
        return false;
    }
    
    public function createTable(string $tableName) : bool
    {
        if(!$this->server)
        {
            $this->setError("PostgreSql::createTable() ==> result!=true // Report : Server not connected <br />");                    
        }
        else
        {       
            $this->query="CREATE TABLE \"".$this->real_escape_string($this->getSchema())."\".\"".$this->real_escape_string($tableName)."\" () ";
            if(pg_query($this->server,$this->query))
            {
                return true;
            }
            $this->setError("PostgreSql::createTable() ==> result!=true // Report :" .pg_last_error());

        }
        return false; 
    }
    
    public function renameTable(string $tableName,string $new_TableName) : bool
    {
        if(!$this->server)
        {
            $this->setError("PostgreSql::renameTable() ==> result!=true // Report : Server not connected <br />");                    
        }
        else
        {       
            $this->query="ALTER TABLE \"".$this->real_escape_string($this->getSchema())."\".\"".$this->real_escape_string($tableName)."\" RENAME TO  \"".$this->real_escape_string($new_TableName)."\" ";
            if(pg_query($this->server,$this->query))
            {
                return true;
            }
            $this->setError("PostgreSql::renameFields() ==> result!=true // Report :" .pg_last_error());

        }
        return false;        
    }
    
    public function deleteTable(string $tableName) : bool
    {
        if(!$this->server)
        {
            $this->setError("PostgreSql::deleteTable() ==> result!=true // Report : Server not connected <br />");                    
        }
        else
        {       
            $this->query="DROP TABLE \"".$this->real_escape_string($this->getSchema())."\".\"".$this->real_escape_string($tableName)."\" ";
            if(pg_query($this->server,$this->query))
            {
                return true;
            }
            $this->setError("PostgreSql::deleteTable() ==> result!=true // Report :" .pg_last_error());

        }
        return false; 
    }
    
    public function emptyTable(string $tableName) : bool
    {
        if(!$this->server)
        {
            $this->setError("PostgreSql::emptyTable() ==> result!=true // Report : Server not connected <br />");                    
        }
        else
        {       
            $this->query="TRUNCATE TABLE \"".$this->real_escape_string($this->getSchema())."\".\"".$this->real_escape_string($tableName)."\" ";
            if(pg_query($this->server,$this->query))
            {
                return true;
            }
            $this->setError("PostgreSql::emptyTable() ==> result!=true // Report :" .pg_last_error());

        }
        return false; 
    }
    
    public function beginTransaction() : bool
    {
        if(!$this->server)
        {
            $this->setError("PostgreSql::beginTransaction() ==> result!=true // Report : Server not connected <br />");                    
        }
        else
        {       
            $query="BEGIN";
            if(pg_query($this->server,$query))
            {
                return true;
            }
            $this->setError("PostgreSql::beginTransaction() ==> result!=true // Report :" .pg_last_error());

        }
        return false; 
    }
    
    public function commitTransaction() : bool
    {
        if(!$this->server)
        {
            $this->setError("PostgreSql::commitTransaction() ==> result!=true // Report : Server not connected <br />");                    
        }
        else
        {       
            $query="COMMIT";
            if(pg_query($this->server,$query))
            {
                return true;
            }
            $this->setError("PostgreSql::commitTransaction() ==> result!=true // Report :" .pg_last_error());

        }
        return false; 
    }
    
    public function rollbackTransaction() : bool
    {
        if(!$this->server)
        {
            $this->setError("PostgreSql::rollbackTransaction() ==> result!=true // Report : Server not connected <br />");                    
        }
        else
        {       
            $query="ROLLBACK";
            if(pg_query($this->server,$query))
            {
                return true;
            }
            $this->setError("PostgreSql::rollbackTransaction() ==> result!=true // Report :" .pg_last_error());

        }
        return false; 
    }
    
    public function countKeyWord() : string
    {
        return self::COUNT_KW;
    }
    
    public function emptyDatabase(): bool
    {
        return false;
    }    
    
    public function geoFunctionToString(string $field) : string
    {
        return " ST_AsGeoJSON(".$field.") ";
        //return " ST_AsGeoJSON(ST_Transform(".$field.",4326)) ";
    }
    
    public function stringFunctionToGeo(string $value) : string
    {
        return " ST_GeomFromText(".$value.") ";
    }    
    
}