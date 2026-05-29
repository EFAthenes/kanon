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
class NoDB extends EngineSql
{   
    public function __construct()
    {
        $this->setEngine_type(Sql::$NO_DB);
    }
    public function __destruct()
    {
    }

    public function connect_DB() : bool
    {
        return true;
    }
    
    public function connect_DB_Server() : bool
    {
         return false;     
    }

    public function disconnect_DB() : bool
    {
        return false;
    }

    public function request_SQL(string $query) : mixed
    {
        return false;
    }

    public function request_SQL_NoLogs(string $query) : mixed
    {
        return null;
    }

    public function query_SQL_bool(string $query) : bool
    {
        return false;
    }

    public function queryFetch_SQL(string $query) : mixed
    {
        return null;
    }
    
    public function num_rows_from_results() : int
    {
        return 0;        
    }    

    public function num_rows(string $query) : int
    {
        return 0;        
    }
    
    public function num_rows_table(string $table) : int
    {
        return 0;        
    }
    
    public function num_fields_table(string $table) : int
    {
        return 0;
    }    

    public function fetch_array(mixed $results): array|null|false
    {
        return null;
    }
    
    public function real_escape_string(string $string) : string
    {
        return "";
    }
    
    public function getLastId() : int
    {
        return 0;
    }
    
    public function getLastModificationForTable(string $tablename) : string
    {
        return "";
    }
    
    
    public function showTables() : mixed
    {
        return null;          
    }
    
    public function getFieldShowTableName() : string
    {
        return "";
    }
    //TODO
    public function copyRowToAnotherTable(?string $initial_query,?string $toTable) : bool
    {   
        return false;              
    }   
    
    public function showColumnInformation(?string $tableName) : array
    {
        return [];   
    }
    
    public function createIndex(string $tablename, array $arrayFields) : bool
    {
        return true;
    }
    
    public function getFieldShowColumnName() : string
    {
        return "";
    }   
    
    public function addFieldDateCreated(string $tableName) : bool
    {
        return false;        
    }

    public function addFieldDateModified(string $tableName) : bool
    {
        return false;        
    }

    public function addFieldId(string $tableName) : bool
    {
        return false;  
    } 
    
    // CHECK IF I HAVE TO QUOTE all the package
    public function setFieldIdPK(string $tableName) : bool
    {
        return false;        
    }
    
    public function setFieldIdAutoIncrement(string $tableName) : bool
    {
        return false;        
    }
    
    public function getQuerySelectDistinct(string $table,string $fieldName,string $where_request="",string $full_outer_join="",bool $count=false) : string
    {
        return "";
    }    
    
    public function getQuerySelectAllById(string $tables,string $select_for_alias, string $where_request) : string
    {
        return "";
    }
    
    public function getQueryDeleteById(string $table,string $value_id) : string
    {
        return "";
    }
     
    public function getPrefixLikeSQL(string $field,bool $unaccent) : string
    {
        return "";
    }
    
    public function getLikeSQL(string $field,bool $case_sensitive,bool $unaccent) : string
    {
        return "";
    }
    
    public function getNotLikeSQL(string $field,bool $case_sensitive,bool $accent_sensitive) : string
    {
        return "";
    }    
        
    public function getWholeWordSQL(string $field,bool $case_sensitive,bool $unaccent) : string
    {
        return "";
    } 

    public function makeLimitString(?SqlLimit $limit=null) : string
    {
        return "";
    }
    public function makeOrderString(SqlOrder $order) : string
    {
        return "";
    } 
    
    //##########################################################################
    // TODO
    //##########################################################################
    public function isDatabaseExisting(?string $database) : bool
    {
        return false;
    }   
    
    public function isTableExisting(?string $table) : bool
    {
        return false;
    }     
    
    public function makeDataBase(?string $database) : bool
    {
        return false;
    } 
    
    public function updateAutoincrementFieldToMax(?string $tablename,?string $field) : bool
    {
        return false;
    }
    
    private function createSequenceName(string $tablename,string $fieldname) : string
    {
        return "";
    }
    
    public function createModelInDb(?string $tablename,ArrayList $fields) : bool
    {
        return false;
    }
    
    public function addFields(string $tableName,ArrayList $kfields) : bool
    {
        return false;
    }
    
    public function dropFields(string $tableName,ArrayList $kfields) : bool
    {
        return false;
    }   
    
    public function renameFields(string $tableName,ArrayList $kfields) : bool
    {
        return false;
    }
    
    public function createTable(string $tableName) : bool
    {
        return false; 
    }
    
    public function renameTable(string $tableName,string $new_TableName) : bool
    {
        return false;        
    }
    
    public function deleteTable(string $tableName) : bool
    {
        return false; 
    }
    
    public function emptyTable(string $tableName) : bool
    {
        return false; 
    }
    
    public function beginTransaction() : bool
    {
        return false; 
    }
    
    public function commitTransaction() : bool
    {
        return false; 
    }
    
    public function rollbackTransaction() : bool
    {
        return false; 
    }
    
    public function countKeyWord() : string
    {
        return "";
    }
    
    public function emptyDatabase(): bool
    {
        return false;
    }    
    
    public function geoFunctionToString(string $field) : string
    {
        return "";
    }
    
    public function stringFunctionToGeo(string $value) : string
    {
        return "";
    }    
    
}