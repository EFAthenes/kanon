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
class MySql extends EngineSql
{
    private const COUNT_KW="count(*)";
    public function __construct()
    {
        $this->setEngine_type(Sql::$MYSQL);
        $this->setQuote("`");
        $this->setDefault_insert_id("NULL");
        $this->setStringCharset("utf8mb4");
        $this->setStringCollation("utf8mb4_0900_ai_ci");
    }
    public function __destruct()
    {
    }

    public function connect_DB() : bool
    {
        // New Connection
        try
        {   
            $this->server = new mysqli($this->getHost(), $this->getUser(), $this->getPasswd(), $this->getBdd());
    //        echo "BDD ".$this->bdd."\n\n";
    //        exit;
            // Check for errors
//            if(!$this->server)
//            {
//                $this->setError("MySql::connect_DB() ==> status!=true // cannot join server ");
//                $this->sendError();
//                return false;              
//            }        
//            else 
            if($this->server->connect_error)
            {
                $this->setError("MySql::connect_DB() ==> status!=true // Number:" . $this->server->connect_errno." => ".$this->server->connect_error);
                $this->sendError();
                return false;            
            }       

            if($this->request_SQL_NoLogs("SET NAMES utf8")==null)
            {
                return false;
            }
            $this->setConnected(true);
        }
        catch (mysqli_sql_exception $e)
        {
            $this->setError("MySql::connect_DB() ==> status!=true // Message:" . $e->getMessage());
            $this->sendError();
            return false;                 
        }
        return true;
    }
      
    public function connect_DB_Server() : bool
    {
        try
        {  
            // New Connection
            $this->server = new mysqli($this->getHost(), $this->getUser(), $this->getPasswd());
    //        echo "BDD ".$this->bdd."\n\n";
    //        exit;
            // Check for errors
//            if(!$this->server)
//            {
//                $this->setError("MySql::connect_DB_Server() ==> status!=true // cannot join server ");
//                $this->sendError();
//                return false;              
//            }
            if($this->server->connect_error)
            {
                $this->setError("MySql::connect_DB() ==> status!=true // Number:" . $this->server->connect_errno." => ".$this->server->connect_error);
                $this->sendError();
                return false;            
            }       

            if($this->request_SQL_NoLogs("SET NAMES utf8")==null)
            {
                return false;
            }
        }
        catch (mysqli_sql_exception $e)
        {
            $this->setError("MySql::connect_DB_Server() ==> status!=true // Message:" . $e->getMessage());
            $this->sendError();
            return false;                 
        }        
        return true;
    }

    public function disconnect_DB() : bool
    {
//        if($this->server==null || $this->server->connect_error)
//        {
//            //server is not connected
//            return true;
//        }
//        
//        if(!$this->getConnected())
//        {
//            return true;
//        }
        
        //var_dump($this->server);
//        if(!$this->server->close())
//        {
//            $this->setError("MySql::disconnect_DB() ==> status!=true // Number:" . $this->server->connect_errno." => ".$this->server->connect_error);
//            return false;
//            //$this->error = "sql::disconnect_DB() ==> mysql_select_db -> status!=true // Report :" . mysql_error();
//            //$this->sendError();            
//        }
        $this->setConnected(false);
        return true;
    }
    
    private function patchFullOuter(string $query) : string
    {
        if(string_contains("FULL OUTER JOIN", $query))
        {
            $limit="";
            if(string_contains("LIMIT", $query))
            {
                $array=explode("LIMIT", $query);
                $query=$array[0];
                $limit=" LIMIT".$array[1]." ";
            }
            
            $order="";
            if(string_contains("ORDER BY", $query))
            {
                $array=explode("ORDER BY", $query);
                
                $array2=explode(".",$array[1]);
                
                $order=" ORDER BY ".$array2[1]." ";
                
            }            
            
            $query_one=str_replace("FULL OUTER JOIN", "LEFT JOIN", $query);
            $query_two=str_replace("FULL OUTER JOIN", "RIGHT JOIN", $query);
            $query="(".$query_one.") UNION ALL (".$query_two.")".$order.$limit;
        }
        return $query;
    }

    public function request_SQL(string $query) : mixed
    {
        $timer = new KTimer($query);
        $timer->start();
        
        $this->query = $this->patchFullOuter($query);
//       echo "\n".$query."\n";
//        KDebugger::getInstance()->dump($this->query);
        try
        {
            $result = $this->server->query($this->query);
            if(!$result)
            {
                $this->setError("MySql::request_SQL() ==> mysql_query -> result!=true // Report :" . $this->server->error." => ".$this->server->sqlstate);
                $this->sendError();
                $result = null;
            }
            $timer->stop();
        }
        catch(Exception $e)
        {
            $this->setError("MySql::request_SQL() ==> mysql_query -> result!=true // Report :" . $this->server->error." => ".$this->server->sqlstate);
            $this->sendError();
            $result = null;
        }
        return $result;
    }

    public function request_SQL_NoLogs(string $query) : mixed
    {
        $this->query = $this->patchFullOuter($query);
        $result = $this->server->query($this->query);
        if(!$result)
        {
            $this->setError("MySql::request_SQL_NoLogs() ==> mysql_query -> result!=true // Report :" . $this->server->error);
            $result = null;
        }
        return $result;
    }

    public function query_SQL_bool(string $query) : bool
    {
        $this->query = $this->patchFullOuter($query);
        $result = $this->server->query($this->query);
        if(!$result)
        {
            $this->setError("MySql::query_SQL_bool() ==> mysql_query -> result!=true // Report :". $this->server->error);
            $this->sendError();
            return false;
        }
        return true;        
    }

    public function queryFetch_SQL(string $query) : mixed
    {
        $this->query = $this->patchFullOuter($query);
        $result = $this->server->query($this->query);
        if(!$result)
        {
            $this->setError("MySql::queryFetch_SQL() ==> mysql_query -> result!=true // Report :" . $this->server->error);
            $this->sendError();
            return null;
        }
        return $result->fetch_assoc();        
    }

    public function num_rows_from_results() : int
    {
        $result = $this->server->query($this->query);
        if(!$result)
        {
            $this->setError("MySql::num_rows_from_results() ==> mysql_query -> result!=true // Report :" . $this->server->error);
            $this->sendError();
            return 0;
        }
        return $result->num_rows;        
    }    
    public function num_rows(string $query) : int
    {
        $this->query = $this->patchFullOuter($query);
        $result = $this->server->query($this->query);
        if(!$result)
        {
            $this->setError("MySql::num_rows() ==> mysql_query -> result!=true // Report :" . $this->server->error);
            $this->sendError();
            return 0;
        }
        return $result->num_rows;        
    }
    
    public function num_rows_table(string $table) : int
    {
        $query="SELECT ".self::COUNT_KW." FROM ".$this->quoteString($table);
        $this->query = $query;
        //KDebugger::getInstance()->dump($query);
        $this->result = $this->server->query($query);
        if(!$this->result)
        {
            $this->setError("MySql::num_rows_table() ==> mysql_query -> result!=true // Report :" . $this->server->error);
            $this->sendError();
            return 0;
        }
        $number=0;
        if($row=$this->fetch_array($this->result))
        {
            //echo print_r($row);
            $number= $row[self::COUNT_KW];
        }
        //echo "ROW =>".$number."\n";
        return intval($number);
    }
    
    public function num_fields_table(string $table) : int
    {
        $query="SELECT ".self::COUNT_KW." FROM information_schema.columns WHERE table_name='".$this->real_escape_string($table)."' AND table_schema='".$this->real_escape_string($this->getBdd())."' ";//." LIMIT 1,1";
        $this->query = $query;
//        KDebugger::getInstance()->dump($query);
        //echo  $this->query;
        $this->result = $this->server->query($query);
        if(!$this->result)
        {
            $this->setError("MySql::num_fields_table() ==> mysql_query -> result!=true // Report :" . $this->server->error);
            $this->sendError();
            return 0;
        }
        $number=0;
        if($row=$this->fetch_array($this->result))
        {
            //KDebugger::getInstance()->dump($row);
            //echo print_r($row);
            $number=$row[self::COUNT_KW];
        }
        //echo "ROW =>".$number."\n";
        return intval($number);
    }     
    /**
     * 
     * @param mixed $results
     * @return array<mixed,mixed>|null|false 
     */
    public function fetch_array(mixed $results): array|null|false
    {
        $result = $results->fetch_assoc();
        return $result;
    }
    
    public function real_escape_string(string $string) : string
    {
        return $this->server->real_escape_string($string);
    }
    
    public function getLastId() : int
    {
        return $this->server->insert_id;
    }
    
    public function getLastModificationForTable(string $tablename) : string
    {
      
        $last_date_modified = "";   
        if(!$this->server)
        {
            $this->error = "MySql::getLastModification() ==> result!=true // Report : Server not connected <br />";                    
        }
        else
        {        
            $query="SELECT date_modified FROM ".$tablename." ORDER BY date_modified DESC Limit 1 ";
            $result = $this->server->query($query); 
            if(!$result)
            {
                $this->error = "MySql::getLastModification() -> result!=true // Report :" . $this->server->error." <br />";
                $this->sendError();
                return $last_date_modified;
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
    
    public function copyRowToAnotherTable(?string $initial_query,?string $toTable) : bool
    {
        if(is_null($initial_query)|| is_null($toTable))
        {
            return false;
        }
        $result = $this->server->query($initial_query);
        if($result == null)
        {
            return false;
        }
        $fields = "";
        for ($i = 0; $i < $this->server->field_count; $i++)
        {
            if($i > 0)
            {
                $fields .= ",";
            }
            $fields .=$result->fetch_field_direct($i)->name;
        }
        $query = "INSERT INTO $toTable ($fields) VALUES";
        $c = 0;
        $result->data_seek(0); //critical reset in case $z has been parsed beforehand. !
        while ($a = $result->fetch_assoc())
        {
            foreach ($a as $key => $as)
            {
                $a[$key] = addslashes($as);
                next($a);
            }
            if($c > 0)
            {
                $query.= ",";
            }
            $query.= "('" . implode("','",array_values($a)) . "')";
            $c++;
        }
        $query.= ";";

        if($this->request_SQL($query))
        {
            return true;
        }
        return false;              
    }  
    
    public function showTables() : mixed
    {
        $this->query = "SHOW TABLES FROM ".$this->getQuote().$this->getBdd().$this->getQuote();
        $result = $this->server->query($this->query);
        if(!$result)
        {
            $this->setError("MySql::showTables() ==> result!=true // Report :" . $this->server->error);
            $result = null;
        }
        return $result;        
    }
    
    public function getFieldShowTableName() : string
    {
        return "Tables_in_".$this->getBdd();
    }
    
    public function showColumnInformation(?string $tableName) : array
    {
        if(is_null($tableName)||empty($tableName))
        {
            return [];
        }        
        $this->query = "SHOW FULL COLUMNS FROM ".$this->getQuote().$tableName.$this->getQuote();
        $results = $this->server->query($this->query);
        if(!$results)
        {
            $this->setError("MySql::showColumnInformation() ==> result!=true // Report :" . $this->server->error);
            $results = null;
        }
        $arrayFields=array();
        if($results != null)
        {
            while (($row = $this->fetch_array($results)) != NULL) 
            {
                $field=new SqlField();
                $field->initByDB($tableName,$row,$this->getEngine_type());
                $arrayFields[]=$field;
//                echo $field->toString();
//                exit();
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
            $stringIndexName.=$separator.str_replace(self::$FK_ID_PREFIX, "",$field);
        }
        
        $alreadyExists=false;
        $this->query = "SHOW INDEX FROM ".$tablename." Where Key_name = ".$this->doublequoteString($stringIndexName);
        $results = $this->server->query($this->query);
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
            $result = $this->server->query($this->query);
            if(!$result)
            {
                $this->setError("MySql::createIndex() ==> result!=true // Report :" . $this->server->error);
                return false;
            } 
        }
        return true;
    }
    
    public function addFieldDateCreated(string $tableName) : bool
    {
        $this->query="ALTER TABLE ".$this->getQuote().$tableName.$this->getQuote()." ADD ".$this->getQuote()."date_created".$this->getQuote()." DATETIME NULL";
        if( $this->server->query($this->query))
        {
            return true;
        }
        $this->setError("MySql::addFieldDateCreated() ==> result!=true // Report :" .$this->server->error);
        return false;
    }

    public function addFieldDateModified(string $tableName) : bool
    {
        $this->query="ALTER TABLE ".$this->getQuote().$tableName.$this->getQuote()." ADD ".$this->getQuote()."date_modified".$this->getQuote()." DATETIME NULL";
        if( $this->server->query($this->query))
        {
            return true;
        }
        $this->setError("MySql::addFieldDateModified() ==> result!=true // Report :" .$this->server->error);
        return false;
    }

    public function addFieldId(string $tableName) : bool
    {
        $this->query="ALTER TABLE ".$this->getQuote().$tableName.$this->getQuote()." ADD ".$this->getQuote()."id".$this->getQuote()." INT NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (".$this->getQuote()."id".$this->getQuote().")";
        if( $this->server->query($this->query))
        {
            //Fix to create TABLE
            $queryTemp="ALTER TABLE ".$this->getQuote().$tableName.$this->getQuote()." DROP COLUMN ".$this->getQuote()."k_mysql_one".$this->getQuote()." ";
            $this->server->query($queryTemp);
            return true;
        }
        $this->setError("MySql::addFieldId() ==> result!=true // Report :" .$this->server->error);
        return false;
    }
    
    public function setFieldIdPK(string $tableName) : bool
    {
        $this->query="ALTER TABLE ".$this->getQuote().$tableName.$this->getQuote()." ADD PRIMARY KEY(`id`);";
        if( $this->server->query($this->query))
        {
            return true;
        }
        $this->setError("MySql::setFieldIdPK() ==> result!=true // Report :" .$this->server->error);
        return false;        
    }
    public function setFieldIdAutoIncrement(string $tableName) : bool
    {
        $this->query="ALTER TABLE ".$this->getQuote().$tableName.$this->getQuote()." MODIFY COLUMN `id` INT auto_increment ;";
        if( $this->server->query($this->query))
        {
            return true;
        }
        $this->setError("MySql::setFieldIdAutoIncrement() ==> result!=true // Report :" .$this->server->error);
        return false;        
    }    
    
    public function getQuerySelectDistinct(string $table,string $fieldName,string $where_request="",string $joinString="",bool $count=false) : string
    {
        if($where_request!="")
        {
            $where_request=" WHERE ".$where_request;
        }        
        $arrayFields=explode(",", $fieldName);
        $fieldsString="";
        foreach ($arrayFields as $field)
        {
            if($fieldsString!="")
            {
                $fieldsString.=",".$this->quoteString($table).".".$this->quoteString($field);
            }
            else
            {
                $fieldsString.="DISTINCT(".$this->quoteString($table).".".$this->quoteString($field).")";
                if($count)
                {
                     $fieldsString.=",".self::COUNT_KW;
                }
            }          
        }
        
        return "SELECT ".$fieldsString." FROM ".$this->quoteString($table)." ".$joinString." ".$where_request;
    }
            
    public function getQuerySelectAllById(string $tables,string $select_for_alias, string $where_request) : string
    {
        /* ,string $joinString="" */
        $query= "SELECT ".$select_for_alias." FROM ".$tables."  WHERE ".$where_request;
//        if(string_contains("FULL OUTER JOIN", $tables))
//        {
//            $query_one=str_replace("FULL OUTER JOIN", "LEFT JOIN", $query);
//            $query_two=str_replace("FULL OUTER JOIN", "RIGHT JOIN", $query);
//            $query=$query_one." UNION ".$query_two;
//        }
        return $query;
        //return "SELECT ".$select_for_alias." FROM ".$this->getQuote().$tableAlias.$this->getQuote()." WHERE ".$this->getQuote().$table.$this->getQuote().".".$this->getQuote().KObject::$ID.$this->getQuote()."='".$this->real_escape_string($value_id)."'";
    }
    public function getQueryDeleteById(string $table,string $value_id) : string
    {
        return "DELETE from ".$this->getQuote().$table.$this->getQuote()." WHERE ".$this->getQuote().KObject::$ID.$this->getQuote()."='".$this->real_escape_string($value_id)."'";
    }
    
    public function getPrefixLikeSQL(string $field,bool $unaccent) : string
    {
        return $field;
    }
    
    public function getLikeSQL(string $field,bool $case_sensitive,bool $accent_sensitive) : string
    {
        $string="";
        //collate utf8_bin
//        if($unaccent && !$case_sensitive)
//        {
//            $string.=" LIKE collate utf8_bin ".$field;
//        }
//        else 
//        else if($case_sensitive)
//        {
//            //utf8_general_cs
//            $string.=" LIKE ".$field." collate utf8_general_cs ";
//        }
        
        if($case_sensitive||$accent_sensitive)
        {
            $string.=" LIKE BINARY ".$field;
        }
        else
        {
            $string.=" LIKE ".$field." ";// COLLATE utf8_general_ci ";
        }
        return $string;
    }
    
    public function getNotLikeSQL(string $field,bool $case_sensitive,bool $accent_sensitive) : string
    {
        return ' NOT '.$this->getLikeSQL($field,$case_sensitive,$accent_sensitive);
    }    
    
    public function getWholeWordSQL(string $field,bool $case_sensitive,bool $accent_sensitive) : string
    {
        $string="";
        //$string=" REGEXP '"."\\\\".$field."\\\\"."b'";
       
        if($case_sensitive||$accent_sensitive)
        {
            $string=" REGEXP '"."\\\\".$field."\\\\"."b'";
        }
        else
        { //REGEXP '\\brid\\b'
            $string=" REGEXP '".$field."'";
        }
        return $string;
    }
    
    #[\Override]
    public function getGeomIntersectSQL(string $field,string $value) : string
    {
        $string =" ST_Intersects(GEOMFROMTEXT('".$value."'), ".$field." , 0 )";
        return $string;
    }
    
    #[\Override]
    public function getGeomContainsSQL(string $field,string $value) : string
    {
        $string =" ST_Intersects(GEOMFROMTEXT('".$value."'), ".$field." , 0 )";
        return $string;
    }
    
    #[\Override]
    public function getGeomIsContainedSQL(string $field,string $value) : string
    {
        $string =" ST_Intersects(".$field.",GEOMFROMTEXT('".$value."'), 0 )";
        return $string;
    }    
    
    public function makeLimitString(?SqlLimit $limit=null) : string
    {
        $sql_limit="";
        if(is_null($limit))
        {
            return $sql_limit;
        }
        $nb_limit=intval($limit->getLimitInt());
        $offset=intval($limit->getOffsetInt());
        
        if($nb_limit>0)
        {
            $position_start=0;
            if($offset>0)
            {
                $position_start=$offset;
            }
            $sql_limit=" LIMIT ".$position_start.",".$nb_limit;
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
            elseif($order->isGeometricField())
            {
                $sql_order="ORDER BY ".$this->geoFunctionToString($this->quoteString($table_order).".".$this->quoteString($field_order))." ".$type;
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
                    $field_order2=$order_sql->getField();                   
                    $type=$order_sql->getType();
                    if(!empty($field_order2))
                    { 
                        if($order_sql->isOrderByCount())
                        {
                            $sql_order.=",".$this->countKeyWord()." ".$type;
                        }
                        elseif($order_sql->isGeometricField())
                        {
                            $sql_order.=",".$this->geoFunctionToString($this->quoteString($table_order).".".$this->quoteString($field_order2))." ".$type;
                        }                        
                        else
                        {
                            $sql_order.=",".$this->quoteString($table_order).".".$this->quoteString($field_order2)." ".$type;
                        }
                    }                    
                }
            }            
        }
        return $sql_order;
    }
    
    public function isDatabaseExisting(?string $database) : bool
    {
//        if(is_null($database))
//        {
//            $this->setError("MySql::isDatabaseExisting() ==> result!=true // database is NULL ");
//            return false;
//        }
//        else
        if(!is_string($database))
        {
            $this->setError("MySql::isDatabaseExisting() ==> result!=true // database is not a string ");
            return false;            
        }        
        
        $status=false;
        $already_connected=true;
        if(!$this->getConnected())
        {
            if(!$this->connect_DB())
            {
                $this->setError("MySql::isDatabaseExisting() ==> cannot connect!" .$this->server->error);
                return false;
            }
            $already_connected=false;
        } 
        
        $query="SHOW DATABASES LIKE '".$this->real_escape_string($database)."'";
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
//            $this->setError("MySql::isTableExisting() ==> result!=true // table is NULL ");
//            return false;
//        }
//        else
        if(!is_string($table))
        {
            $this->setError("MySql::isTableExisting() ==> result!=true // table is not a string ");
            return false;            
        }          
         
        $status=false;
        $already_connected=true;
        if(!$this->getConnected())
        {
            if(!$this->connect_DB())
            {
                $this->setError("MySql::isTableExisting() ==> cannot connect!" .$this->server->error);
                return false;
            }
            $already_connected=false;
        } 
        
        $query="show tables like \"".$this->real_escape_string($table)."\";";
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
//            $this->setError("MySql::makeDataBase() ==> result!=true // database is NULL ");
//            return false;
//        }
//        else
        if(!is_string($database))
        {
            $this->setError("MySql::makeDataBase() ==> result!=true // database is not a string ");
            return false;            
        }
        
        $status=false;
        $already_connected=true;
        if(!$this->getConnected())
        {
            if(!$this->connect_DB())
            {
                $this->setError("MySql::makeDataBase() ==> cannot connect!" .$this->server->error);
                return false;
            }
            $already_connected=false;
        } 
        
        $query="CREATE DATABASE ".$this->real_escape_string($database)." CHARACTER SET ".$this->getStringCharset()." COLLATE ".$this->getStringCollation();
        if($this->request_SQL($query))
        {
            $status=true;
        }
        else
        {
            $this->setError("MySql::makeDataBase() ==> cannot create DB ``".$database."``!" .$this->server->error);
        }
        
        if(!$already_connected)
        {
            $this->disconnect_DB();
        }
        
        return $status;        
    }
    
    public function updateAutoincrementFieldToMax(?string $tablename,?string $field) : bool
    {
        return false;
    }    
    
    public function createModelInDb(?string $tablename,ArrayList $fields) : bool
    {
        $status=true;
//        if(is_null($tablename))
//        {
//            $this->setError("MySql::createModelInDb() ==> result!=true // Tablename is NULL ");
//            return false;            
//        }
//        else
        if(!is_string($tablename))
        {
            $this->setError("MySql::createModelInDb() ==> result!=true // Tablename is not a string ");
            return false;            
        }

        if($this->isTableExisting($tablename))
        {
            $this->setError("MySql::createModelInDb() ==> result!=true // Table already '".$tablename."' exists");
            return false;
        }
        
        if($fields->getSize()==0)
        {
            $this->setError("MySql::createModelInDb() ==> result!=true // Fields not present");
            return false;            
        }

        $query2=$this->makeQueryFromListKFields($tablename,$fields);       
             
        if(!empty($query2))
        {       
            $query="CREATE TABLE `".$tablename."` (".$query2.")";            
            
            //echo "Doing QUERY => ".$tablename."\n";
            //echo $query2."\n"."\n"."\n";
            if($this->request_SQL($query))
            {
                if($this->make_query_key!="")
                {
                    //sleep(1);
                    $the_query="ALTER TABLE `".$tablename."` ".$this->make_query_key;
                    if($this->request_SQL($the_query))
                    {
                        if($this->make_query_autoincrement!="")
                        {
                            //sleep(1);
                            if($this->request_SQL($this->make_query_autoincrement))
                            {
                                
                            }
                            else
                            {
                                $this->setError("MySql::createModelInDb() ==> ERROR WHEN CREATE AUTOINCREMENT FOR TABLE ".$tablename." // ".$this->make_query_autoincrement);
                                $status=false; 
                            }
                        }
                    }
                    else
                    {
                        $this->setError("MySql::createModelInDb() ==> ERROR WHEN CREATE KEY FOR TABLE ".$tablename." ==> ".$this->getError()." // ".$the_query);
                        $status=false; 
                    }
                }
            }
            else
            {
                $this->setError("MySql::createModelInDb() ==> ERROR WHEN CREATE TABLE ".$tablename." => ".$this->getError(). " // ".$query);
                $status=false; 
            }
        }
        return $status;
    }
    
    private ?string $make_query_key=null;
    private ?string $make_query_autoincrement=null;
    
    private function makeQueryFromListKFields(string $tablename,ArrayList $fields,bool $alter=false) : string
    {
        $query2="";
        $this->make_query_key="";
        $this->make_query_autoincrement="";      
        $status=true;
        $kField= new KFieldUnKnown();
        for($i=0; $i<$fields->getSize(); $i++)
        {
            if($i!=0)
            {
                $query2.=",
";
            }
            
            //$query2.=" ADD ";
            
            $kField=$fields->get($i);
            if($kField instanceof KFieldInteger)
            {
                $queryInteger="";
                $queryInteger.="`".$kField->getName()."` int(11) ";
                if($kField->getAuto_increment())
                { 
                    $this->make_query_autoincrement.="ALTER TABLE `".$tablename."` MODIFY ".$queryInteger." AUTO_INCREMENT ";
                    //$this->connect_DB();
                    $queryAutoIncrement="SELECT `AUTO_INCREMENT` FROM  INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '".$this->getBdd()."' AND TABLE_NAME = '".$tablename."';";
                    if($result=$this->queryFetch_SQL($queryAutoIncrement))
                    {
                        if(isInteger($result['AUTO_INCREMENT']))
                        {
                            $this->make_query_autoincrement.=", AUTO_INCREMENT=".$result['AUTO_INCREMENT'];
                        }
                    }
                    //$this->disconnect_DB();
                    
                    $this->make_query_autoincrement.=";";
                }
                else
                {
                    if($kField->getUnsigned())
                    {
                        $queryInteger.=" UNSIGNED ";
                    }
                    if(!$kField->getIs_null())
                    {
                        $queryInteger.=" NOT NULL ";
                    }
                    if(!is_null($kField->getDefault())&&isInteger($kField->getDefault()))
                    {
                        $queryInteger.=" DEFAULT '".$kField->getDefaultString()."' ";
                    } 
                    else if($kField->getIs_null())
                    {
                        $queryInteger.=" DEFAULT NULL ";
                    }
                }
                
                $query2.=$queryInteger;  
            }
            else if($kField instanceof KFieldBool)
            {
                $query2.="`".$kField->getName()."` tinyint(1) ";
                if(!$kField->getIs_null())
                {
                    $query2.=" NOT NULL ";
                }
                if(!is_null($kField->getDefault()))
                {
                    $query2.=" DEFAULT '".convertBoolToStringNumber($kField->getDefault())."' ";
                } 
                else if($kField->getIs_null())
                {
                    $query2.=" DEFAULT NULL ";
                }
            }            
            else if($kField instanceof KFieldFloat)
            {
                $query2.="`".$kField->getName()."` float ";
                if(!$kField->getIs_null())
                {
                    $query2.=" NOT NULL ";
                }
                if(!is_null($kField->getDefault()))
                {
                    $query2.=" DEFAULT '".$kField->getDefaultString()."' ";
                } 
                else if($kField->getIs_null())
                {
                    $query2.=" DEFAULT NULL ";
                }
            }
            else if($kField instanceof KFieldDouble)
            {
                $query2.="`".$kField->getName()."` double ";
                if(!$kField->getIs_null())
                {
                    $query2.=" NOT NULL ";
                }
                if(!is_null($kField->getDefault()))
                {
                    $query2.=" DEFAULT '".$kField->getDefaultString()."' ";
                } 
                else if($kField->getIs_null())
                {
                    $query2.=" DEFAULT NULL ";
                }
            }            
            else if($kField instanceof KFieldVarChar)
            {
                $query2.="`".$kField->getName()."` varchar(".$kField->getLength().") ";
                if(!$kField->getIs_null())
                {
                    $query2.=" NOT NULL ";
                }
                if(!is_null($kField->getDefault()))
                {
                    $query2.=" DEFAULT '".$kField->getDefaultString()."' ";
                }
                else if($kField->getIs_null())
                {
                    $query2.=" DEFAULT NULL ";
                }
            }
            else if($kField instanceof KFieldText)
            {
                $query2.="`".$kField->getName()."` text ";
                if(!$kField->getIs_null())
                {
                    $query2.=" NOT NULL ";
                }
                if($kField->getDefault())
                {
                    $query2.=" DEFAULT '".$kField->getDefaultString()."' ";
                } 
            }             
            else if($kField instanceof KFieldDateTime)
            {
                $query2.="`".$kField->getName()."` datetime ";
                if(!$kField->getIs_null())
                {
                    $query2.=" NOT NULL ";
                }
                else if($kField->getDefault())
                {
                    $query2.=" DEFAULT '".$kField->getDefaultString()."' ";
                }
                else/* if($kField->getIs_null())*/
                {
                    $query2.=" DEFAULT NULL ";
                }
            } 
            else if($kField instanceof KFieldDate)
            {
                $query2.="`".$kField->getName()."` date ";
                if(!$kField->getIs_null())
                {
                    $query2.=" NOT NULL ";
                }
                else if($kField->getDefault())
                {
                    $query2.=" DEFAULT '".$kField->getDefaultString()."' ";
                }
                else/* if($kField->getIs_null()) */
                {
                    $query2.=" DEFAULT NULL ";
                }
            }
            else if($kField instanceof KFieldTime)
            {
                $query2.="`".$kField->getName()."` time ";
                if(!$kField->getIs_null())
                {
                    $query2.=" NOT NULL ";
                }
                else if($kField->getDefault())
                {
                    $query2.=" DEFAULT '".$kField->getDefaultString()."' ";
                }
                else/* if($kField->getIs_null())*/
                {
                    $query2.=" DEFAULT NULL ";
                }
            }
            else if($kField instanceof KFieldYear)
            {
                $query2.="`".$kField->getName()."` year ";
                if(!$kField->getIs_null())
                {
                    $query2.=" NOT NULL ";
                }
                else if($kField->getDefault())
                {
                    $query2.=" DEFAULT '".$kField->getDefaultString()."' ";
                }
                else/* if($kField->getIs_null())*/
                {
                    $query2.=" DEFAULT NULL ";
                }
            }
            else if($kField instanceof KFieldGeometry)
            {
                $query2.="`".$kField->getName()."` GEOMETRY  ";
                if(!$kField->getIs_null())
                {
                    $query2.=" NOT NULL ";
                }
                else if($kField->getDefault())
                {
                    $query2.=" DEFAULT '".$kField->getDefaultString()."' ";
                }
                else/* if($kField->getIs_null())*/
                {
                    $query2.=" DEFAULT NULL ";
                }
            }            
            else if($kField instanceof KFieldTimeStamp)
            {
                $query2.="`".$kField->getName()."` datetime ";
                if(!$kField->getIs_null())
                {
                    $query2.=" NOT NULL ";
                }
                else if($kField->getDefault())
                {
                    $query2.=" DEFAULT '".$kField->getDefaultString()."' ";
                }
                else/* if($kField->getIs_null())*/
                {
                    $query2.=" DEFAULT NULL ";
                }
            } 
            else
            {
                $this->setError("MySql::createModelInDb() ==> Unknown Field type present ".$kField->getName());
                $status=false;
            }
            
            // check if Field is key
            if($kField->getPrimary_key())
            {
                if($this->make_query_key!="")
                {
                    $this->make_query_key.=",";
                }
//                else
//                {
//                    $query_key.=";";
//                }
                $this->make_query_key.=" ADD PRIMARY KEY (`".$kField->getName()."`) ";
            }
        }
        
        //$query2.=$query_key;
        
        return $query2;
    }
    
    
    public function convertToBool(mixed $var) : string
    {
        $result=convertToBool($var);
        if($result===true)
        {
            return "1";
        }
        else if($result===false)
        {
            return "0";
        }
        return "NULL";        
    }
    

    public function addFields(string $tableName,ArrayList $kfields) : bool
    {       
   
        $query2=$this->makeQueryFromListKFields($tableName,$kfields,true);
        
//        var_dump($kfields);
//        
//        echo $query2;
//        
//        exit;
        
        $this->query="ALTER TABLE ".$this->getQuote().$tableName.$this->getQuote()." ADD ".$query2;
        if( $this->server->query($this->query))
        {
            return true;
        }
        $this->setError("MySql::addFields() ==> result!=true // Report :" .$this->server->error." // Query = ".$this->query);
        return false;
    }
    
    public function dropFields(string $tableName,ArrayList $kfields) : bool
    {    
        $query2=null;
        /* @var $kfield KField */
        foreach ($kfields as $kfield)
        {
            if(!is_null($query2))
            {
                $query2.=",";
            }
            $query2.=" DROP COLUMN ".$this->getQuote().$kfield->getName().$this->getQuote()." ";
        }  
        if(!is_null($query2))
        {    
            $this->query="ALTER TABLE ".$this->getQuote().$tableName.$this->getQuote()." ".$query2;
            if( $this->server->query($this->query))
            {
                return true;
            }            
        }          
        $this->setError("MySql::dropFields() ==> result!=true // Report :" .$this->server->error);
        return false;
    }   
    
    public function renameFields(string $tableName,ArrayList $kfields) : bool
    {
        $query2=null;
        /* @var $kfield KField */
        foreach ($kfields as $kfield)
        {
            if(!is_null($query2))
            {
                $query2.=",";
            }
            $query2.=" RENAME COLUMN ".$this->getQuote().$kfield->getName().$this->getQuote()." TO ".$this->getQuote().$kfield->getPendingRename().$this->getQuote()." ";
        }  
        if(!is_null($query2))
        {    
            $this->query="ALTER TABLE ".$this->getQuote().$tableName.$this->getQuote()." ".$query2;
            if( $this->server->query($this->query))
            {
                return true;
            }            
        }          
        $this->setError("MySql::renameFields() ==> result!=true // Report :" .$this->server->error." // Query :".$this->query);
        return false;
    }  
    
    public function createTable(string $tableName) : bool
    {
        $this->query = "CREATE TABLE " . $this->getQuote() . $tableName . $this->getQuote() . " ( `k_mysql_one` INT NOT NULL ) ";
        if ($this->server->query($this->query))
        {
            return true;
        }
        $this->setError("MySql::createTable() ==> result!=true // Report :" . $this->server->error);
        return false;        
    }
    
    public function renameTable(string $tableName,string $new_TableName) : bool
    {
        $this->query = "ALTER TABLE " . $this->getQuote() . $tableName . $this->getQuote() . " RENAME TO  " . $this->getQuote() . $new_TableName . $this->getQuote() . " ";
        if ($this->server->query($this->query))
        {
            return true;
        }
        $this->setError("MySql::renameTable() ==> result!=true // Report :" . $this->server->error);
        return false;        
    }
    
    public function deleteTable(string $tableName) : bool
    {
        $this->query = "DROP TABLE " . $this->getQuote() . $tableName . $this->getQuote();
        if ($this->server->query($this->query))
        {
            return true;
        }
        $this->setError("MySql::deleteTable() ==> result!=true // Report :" . $this->server->error);
        return false;        
    }
    
    public function emptyTable(string $tableName) : bool
    {
        $this->query = "TRUNCATE TABLE " . $this->getQuote() . $tableName . $this->getQuote();
        if ($this->server->query($this->query))
        {
            return true;
        }
        $this->setError("MySql::emptyTable() ==> result!=true // Report :" . $this->server->error);
        return false;        
    }
    
    public function beginTransaction() : bool
    {
        $this->server->autocommit(false);
        if ($this->server->begin_transaction())
        {
            return true;
        }
        $this->setError("MySql::beginTransaction() ==> result!=true // Report :" . $this->server->error);
        return false;   
    }
    
    public function commitTransaction() : bool
    {       
        if ($this->server->commit())
        {
            $this->server->autocommit(true);
            return true;
        }
        $this->server->autocommit(true);
        $this->setError("MySql::commitTransaction() ==> result!=true // Report :" . $this->server->error);
        return false;
    }
    
    public function rollbackTransaction() : bool
    {
        if ($this->server->rollback())
        {
            $this->server->autocommit(true);
            return true;
        }
        $this->server->autocommit(true);
        $this->setError("MySql::rollbackTransaction() ==> result!=true // Report :" . $this->server->error);
        return false;
    }
    
    public function countKeyWord() : string
    {
        return self::COUNT_KW;
    }
    
    public function geoFunctionToString(string $field) : string
    {
        return " ST_AsText(".$field.") ";
    }
    
    public function stringFunctionToGeo(string $value) : string
    {
        return " ST_GeomFromText(".$value.") ";
    }
    
    public function emptyDatabase(): bool
    {
        $status=true;
        $results=$this->showTables();
        while(($row=$this->fetch_array($results))!=NULL)
        {
            $tableName=($row[$this->getFieldShowTableName()]);   
            if(!$this->deleteTable($tableName))
            {
                $status=false;
            }
        }
        return $status;
    }  
}