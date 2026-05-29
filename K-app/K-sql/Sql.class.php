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
class Sql extends AbstractSql
{
    public static string $MYSQL="MYSQL";
    public static string $MARIADB="MARIADB";
    public static string $POSTGRES="POSTGRES";
    public static string $NO_DB="NO_DB";
    public static string $FK_ID_PREFIX="fk_id_";

    private mixed $engine=null;
    private bool $connected=false;
    private string $error_email="";

    private static ?Sql $instance =null;
    private string $engine_type_string="";
    
    public static function getInstance() : ?Sql
    {
        if(is_null(Sql::$instance))
        {
            Sql::$instance=new Sql();
        }
        if(self::initInstance(Sql::$instance))
        {
            return Sql::$instance;
        }
        return null;
    }
    
    public static function getInstanceToCreateDB(string $database) : ?string
    {
        $sql=new Sql();
        if($sql->connect_DB_Server())
        {
            if($sql->makeDataBase($database))
            {
                return null;
            }
        }
        return $sql->getError();
    }
    
    private static function initInstance(Sql $the_instance) : bool
    {
        if(!$the_instance->connected)
        {
            $the_instance->connect_DB();
        }       
        return $the_instance->connected;
    }

    public function __construct()
    {
        $this->error_email = ParamManager::getInstance()->error_email;
        
        $user = ParamManager::getInstance()->sql_user;
        $passwd = ParamManager::getInstance()->sql_pass;
        $bdd = ParamManager::getInstance()->sql_database;
        $host = ParamManager::getInstance()->sql_host;
        $this->engine_type_string = ParamManager::getInstance()->sql_engine;
        
        $this->init_DB($this->engine_type_string,$host,$user,$passwd,$bdd);
        //$this->init_DB(Sql::$POSTGRES,"localhost","postgres","Zar4SQL359#pol","manu_test");
    }

    public function __destruct()
    {
        $this->disconnect_DB();
    }
    
    public function isEnginePostgres() : bool
    {
        if($this->engine_type_string==Sql::$POSTGRES)
        {
            return true;
        }
        return false;
    }
    public function isEngineMySQL() : bool
    {
        if($this->engine_type_string==Sql::$MYSQL)
        {
            return true;
        }
        return false;
    } 
    public function isEngineMariaDb() : bool
    {
        if($this->engine_type_string==Sql::$MARIADB)
        {
            return true;
        }
        return false;
    }
    public function isEngineMySQLLike() : bool
    {
        if($this->isEngineMySQL() || $this->isEngineMariaDb())
        {
            return true;
        }
        return false;
    }        
        
    public function init_DB(string $db_engine,string$db_host,string $db_user,string $db_pass,string $db_name) : void
    {
        /* @var $this->engine AbstractSql */
        if($db_engine==Sql::$MYSQL)
        {
            $this->engine=new MySql();
        }
        else if($db_engine==Sql::$MARIADB)
        {
            $this->engine=new MariaDb();
        }        
        else if($db_engine==Sql::$POSTGRES)
        {
            $this->engine=new PostgreSql();
        }
        else if($db_engine==Sql::$NO_DB)
        {
            $this->engine=new NoDB();
        }        
        else
        {
            echo "engine ERROR";
            exit();
        }
        //$this->engine->setEngine_type($db_engine);
        $this->engine->setHost($db_host);
        $this->engine->setUser($db_user);
        $this->engine->setPasswd($db_pass);
        $this->engine->setBdd($db_name);
    }
    
    public function getConnected() : bool
    {
        return $this->connected;
    }
    protected function setConnected(bool $connected):void
    {
        $this->connected=$connected;
    }
    public function connect_DB() : bool
    {
        $this->connected=$this->engine->connect_DB();
        return $this->getConnected();
    }
    
    public function connect_DB_Server() : bool
    {
        return $this->connected=$this->engine->connect_DB_Server();
    } 
    
    public function disconnect_DB() : bool
    {
        if(!$this->connected)
        {
            return true;
        }
        if(!$this->engine)
        {
            return true;
        }
        $this->connected=!$this->engine->disconnect_DB();
        return !$this->connected;
    }
    
    public function getQuerySelectDistinct(string $table,string $fieldName,string $where_request="",string $full_outer_join="",bool $count=false) : string
    {
        return $this->engine->getQuerySelectDistinct($table,$fieldName,$where_request,$full_outer_join,$count);
    }
    
    public function getQuerySelectDistinctAndCount(string $table,string $fieldName,string $where_request="",string $full_outer_join="") : string
    {
        $count=true;
        return $this->engine->getQuerySelectDistinct($table,$fieldName,$where_request,$full_outer_join,$count);
    }

    public function getQuerySelectAllById(string $tables,string $select_for_alias, string $where_request) : string
    {
        return $this->engine->getQuerySelectAllById($tables,$select_for_alias,$where_request);
    }
    public function getQueryDeleteById(string $table,string $value_id) : string
    {
        return $this->engine->getQueryDeleteById($table,$value_id);
    } 
    
    public function getPrefixLikeSQL(string $field,bool $accent_sensitive) : string
    {
        return $this->engine->getPrefixLikeSQL($field,$accent_sensitive);
    }     
    
    public function getLikeSQL(string $field,bool $case_sensitive,bool $accent_sensitive) : string
    {
        return $this->engine->getLikeSQL($field,$case_sensitive,$accent_sensitive);
    }   
    
    public function getNotLikeSQL(string $field,bool $case_sensitive,bool $accent_sensitive) : string
    {
        return $this->engine->getNotLikeSQL($field,$case_sensitive,$accent_sensitive);
    }     
    
    public function getWholeWordSQL(string $field,bool $case_sensitive,bool $accent_sensitive) : string
    {
        return $this->engine->getWholeWordSQL($field,$case_sensitive,$accent_sensitive);
    }  
    
    public function getGeomIntersectSQL(string $field,string $value) : string
    {
        return $this->engine->getGeomIntersectSQL($field,$value);
    }
    
    public function getGeomContainsSQL(string $field,string $value) : string
    {
        return $this->engine->getGeomContainsSQL($field,$value);
    }

    public function getGeomIsContainedSQL(string $field,string $value) : string
    {
        return $this->engine->getGeomIsContainedSQL($field,$value);
    }      
    
    public function request_SQL(string $query) : mixed
    {
        return $this->engine->request_SQL($query);
    }

    public function request_SQL_NoLogs(string $query) : mixed
    {
        return $this->engine->request_SQL_NoLogs($query);
    }

    public function query_SQL_bool(string $query) : bool
    {
        return $this->engine->query_SQL_bool($query);       
    }

    public function queryFetch_SQL(string $query) : mixed
    {
        return $this->engine->queryFetch_SQL($query);          
    }
    
    public function num_rows_from_results() : int
    {
        return $this->engine->num_rows_from_results();        
    }    

    public function num_rows(string $query) : int
    {
        return $this->engine->num_rows($query);        
    }
    
    public function num_rows_table(string $table) : int
    {
        return $this->engine->num_rows_table($table);        
    }    

    public function num_fields_table(string $table) : int
    {
        return $this->engine->num_fields_table($table);        
    }
    
    /**
     * 
     * @param mixed $results
     * @return array<string,mixed>|null|false
     */
    public function fetch_array(mixed $results): array|null|false
    {
        if($results!=null)
        {    
            return $this->engine->fetch_array($results);
        }
        return null;
    }
       
    public function real_escape_string(string $string) : string
    {
        return $this->engine->real_escape_string($string);
    }
    
    public function getLastId() : int
    {
        return $this->engine->getLastId();
    }
    
    public function getLastModificationForTable(string $tablename) : string
    {
        return $this->engine->getLastModificationForTable($tablename);
    }
    
    public function copyRowToAnotherTable(?string $initial_query,?string $toTable) : bool
    {
        return $this->engine->copyRowToAnotherTable($initial_query, $toTable);       
    }
    
    public function showTables() : mixed
    {
        return $this->engine->showTables();
    }
    public function getFieldShowTableName() : string
    {
        return $this->engine->getFieldShowTableName();
    }
    
    public function showColumnInformation(?string $tableName) : array
    {
        return $this->engine->showColumnInformation($tableName);
    }
    
    public function createIndex(string $tablename, array $arrayFields) : bool
    {
        return $this->engine->createIndex($tablename,$arrayFields);
    }
    
    public function getFieldShowColumnName() : string
    {
        return $this->engine->getFieldShowColumnName();
    }
       
    public function getAllTablesNamesInArray(): array
    {
        $arrayTablesNames=[];
        $results=$this->engine->showTables();
        if(!is_null($results))
        {
            while(($row=$this->engine->fetch_array($results))!=NULL)
            {            
                $arrayTablesNames[]=($row[$this->engine->getFieldShowTableName()]);
            }
        }
        return  $arrayTablesNames; 
    }  
    
    public function makeLimitString(?SqlLimit $limit=null) : string
    {
        return $this->engine->makeLimitString($limit);
    }
    
    public function makeOrderString(SqlOrder $order) : string
    {
        return $this->engine->makeOrderString($order);
    }    
    
    public function sendError() : void
    {
        /*
        if($this->debug)
        {
            echo "<br /><br />" . $this->error . "<br /><br /> " . $this->query . " <br /><br />";
            $print = new printToFile("query = \n" . $this->query . "\n\n\n Error = \n" . $this->error);
        }
        else
        {
            $url = new KURL();
            $mail = new ArchimageMail();
            $mail->sendEmailErrorPHP("SQL Error :: " . date("Y-m-d") . " // " . date("H-m-s-u"), str_ireplace("\n", "<br />", $this->error . "<br />" . $this->query . "<br /><br />" . $url->printURL()));
            SessionMemory::getInstance()->reset();
            exit();
        }
         * 
         */
    }
    

    //-----------------------------  GETTERS
    

    public function toString() : string
    {
        return "TODO :: toString()";
        //return "" . $this->engine->user . "&&" . $this->engine->passwd;
    }

    function getHost():string
    {
        return $this->engine->getHost();
    }

    function getBdd():string
    {
        return $this->engine->getBdd();
    }

//    function getDebug()
//    {
//        return $this->engine->debug;
//    }

    function setHost(string $host):void
    {
        $this->engine->setHost($host);
    }

    function setBdd(string $bdd):void
    {
        $this->engine->setBdd($bdd);
    }
//
//    function setDebug($debug)
//    {
//        $this->debug=$debug;
//    }
    function getServer():string
    {
        return $this->engine->getServer();
    }

    function getUser():string
    {
        return $this->engine->getUser();
    }

    function getPasswd():string
    {
        return $this->engine->getPasswd();
    }

    function getError_email():string
    {
        return $this->error_email;
    }

    function getQuery():string
    {
        return $this->engine->getQuery();
    }

    function getEngine_type():string
    {
        return $this->engine->getEngine_type();
    }

    function setServer(string $server):void
    {
        $this->engine->setServer($server);
    }

    function setUser(string $user):void
    {
        $this->engine->setUser($user);
    }

    function setPasswd(string $passwd):void
    {
        $this->engine->setPasswd($passwd);
    }

    function setError_email(string $error_email):void
    {
        $this->error_email=$error_email;
    }

    function setQuery(string $query):void
    {
        $this->engine->setQuery($query);
    }
    
    function setSchema(string $schema):void
    {
        $this->engine->setSchema($schema);
    }
    function getSchema():string
    {
        return $this->engine->getSchema();
    }
    
    function getError():string
    {
        return $this->engine->getError();
    }
    function setError(string $error):void
    {
        $this->engine->setError($error);
    }  
    function getQuote() : string
    {
        return $this->engine->getQuote();
    }
    
    function countKeyWord() : string
    {
        return $this->engine->countKeyWord();
    }

    function setQuote(string $quote) : bool
    {
        return $this->engine->setQuote($quote);
    }     
    
    function quoteString(string $quote) : string
    {
        return $this->engine->quoteString($quote);
    } 
    
    function getDefault_insert_id():string
    {
        return $this->engine->getDefault_insert_id();
    }

    function setDefault_insert_id(string $default_insert_id):void
    {
        $this->engine->setDefault_insert_id($default_insert_id);
    }        
    
    function getConnectionParametersString(string $delimiter="\n"):string
    {
        $string="";
        $string.="ENGINE ==> ".$this->getEngine_type().$delimiter;
        $string.="IP     ==> ".$this->getHost().$delimiter;
        $string.="USER   ==> ".$this->getUser().$delimiter;
        $string.="DB     ==> ".$this->getBdd().$delimiter;
        return $string;
    }
    
    public function geoFunctionToString(string $field) : string
    {
        return $this->engine->geoFunctionToString($field);
    }
    
    public function stringFunctionToGeo(string $value) : string
    {
        return $this->engine->stringFunctionToGeo($value);
    }
     
     
    public function addFieldDateCreated(string $tableName) : bool
    {
        return $this->engine->addFieldDateCreated($tableName);
    }

    public function addFieldDateModified(string $tableName) : bool
    {
        return $this->engine->addFieldDateModified($tableName);
    }

    public function addFieldId(string $tableName) : bool
    {
        return $this->engine->addFieldId($tableName);
    }
    
    public function setFieldIdPK(string $tableName) : bool
    {
        return $this->engine->setFieldIdPK($tableName);
    }
    public function setFieldIdAutoIncrement(string $tableName) : bool
    {
        return $this->engine->setFieldIdAutoIncrement($tableName);
    }    
    public function isDatabaseExisting(?string $database) : bool
    {
        return $this->engine->isDatabaseExisting($database);
    } 
    
    public function isTableExisting(?string $tablename) : bool
    {
        return $this->engine->isTableExisting($tablename);
    }      
    
    public function makeDataBase(?string $database) : bool
    {
        return $this->engine->makeDataBase($database);
    }    
    
    public function createModelInDb(?string $tablename,ArrayList $fields) : bool
    {
        return $this->engine->createModelInDb($tablename,$fields);
    }
    
    public function updateAutoincrementFieldToMax(?string $tablename,?string $field) : bool
    {
        return $this->engine->updateAutoincrementFieldToMax($tablename,$field);
    }
    
    public function convertToBool(mixed $var) : string
    {
        return $this->engine->convertToBool($var);
    }
    
    public function addFields(string $tableName, ArrayList $fields) : bool
    {
        return $this->engine->addFields($tableName,$fields);
    }
    
    public function dropFields(string $tableName,ArrayList $fields) : bool
    {
        return $this->engine->dropFields($tableName,$fields);
    }
    
    public function renameFields(string $tableName,ArrayList $fields) : bool
    {
        return $this->engine->renameFields($tableName,$fields);
    }
     
    public function createTable(string $tableName) : bool
    {
        return $this->engine->createTable($tableName);
    }
    
    public function renameTable(string $tableName,string $new_TableName) : bool
    {
        return $this->engine->renameTable($tableName,$new_TableName);
    }
    
    public function deleteTable(string $tableName) : bool
    {
        return $this->engine->deleteTable($tableName);
    }
    
    public function emptyTable(string $tableName) : bool
    {
        return $this->engine->emptyTable($tableName);
    }
    
    public function beginTransaction() : bool
    {
        return $this->engine->beginTransaction();
    }
    
    public function commitTransaction() : bool
    {
        return $this->engine->commitTransaction();
    }
    
    public function rollbackTransaction() : bool
    {
        return $this->engine->rollbackTransaction();
    }
    
    public function emptyDatabase() : bool
    {
        return $this->engine->emptyDatabase();
    } 
    
    public function getStringCharset() : string
    {
        return $this->engine->getStringCharset();
    }
    public function getStringCollation() : string
    {
        return $this->engine->getStringCollation();
    }
    public function setStringCharset(string $charset) : bool
    {
        return $this->engine->setStringCharset($charset);
    }
    public function setStringCollation(string $collation) : bool
    {
        return $this->engine->setStringCollation($collation);
    }
}
