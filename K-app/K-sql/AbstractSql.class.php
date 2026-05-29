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
abstract class AbstractSql
{
    public function __construct()
    {
    }
    public function __destruct()
    {
    } 
    
    abstract public function connect_DB() : bool;
    abstract public function connect_DB_Server() : bool;
    abstract public function disconnect_DB() : bool;
    abstract public function request_SQL(string $query): mixed;
    abstract public function request_SQL_NoLogs(string $query): mixed;
    abstract public function query_SQL_bool(string $query) : bool;
    abstract public function queryFetch_SQL(string $query) : mixed;
    abstract public function num_rows_from_results() : int;
    abstract public function num_rows(string $query): int;
    abstract public function num_rows_table(string $table): int;
    abstract public function num_fields_table(string $table): int;
    abstract public function fetch_array(mixed $results): array|null|false; /* @phpstan-ignore-line */
    abstract public function real_escape_string(string $string) : string;
    abstract public function getLastId() : int;
    abstract public function getLastModificationForTable(string $tablename) : string;
    abstract public function copyRowToAnotherTable(?string $initial_query,?string $toTable) : bool;
    abstract public function sendError() : void;
    abstract public function makeLimitString(SqlLimit $limit) : string;
    abstract public function makeOrderString(SqlOrder $order) : string;    
    abstract public function isDatabaseExisting(?string $database) : bool;
    abstract public function isTableExisting(?string $tablename) : bool;
    abstract public function makeDataBase(?string $database) : bool;
    abstract public function createModelInDb(string $tablename,ArrayList $fields) : bool;
    abstract public function updateAutoincrementFieldToMax(string $tablename,string $fieldname) : bool;
    abstract public function geoFunctionToString(string $field) : string;
    abstract public function stringFunctionToGeo(string $value) : string;
    abstract public function getStringCharset() : string;
    abstract public function getStringCollation() : string;
    abstract public function setStringCharset(string $charset) : bool;
    abstract public function setStringCollation(string $collation) : bool;    
    
    abstract public function getQuerySelectDistinct(string $table,string $fieldName,string $where_request="",string $full_outer_join="",bool $count=false) : string;
    abstract public function getQuerySelectDistinctAndCount(string $table,string $fieldName,string $where_request="",string $full_outer_join="") : string;    
    abstract public function getQuerySelectAllById(string $tables,string $select_for_alias, string $where_request) : string;
    abstract public function getQueryDeleteById(string $table,string $value_id) : string;
    abstract public function getPrefixLikeSQL(string $field,bool $accent_sensitive) : string;
    abstract public function getLikeSQL(string $field,bool $case_sensitive,bool $accent_sensitive) : string;
    abstract public function getNotLikeSQL(string $field,bool $case_sensitive,bool $accent_sensitive) : string;
    abstract public function getWholeWordSQL(string $field,bool $case_sensitive,bool $accent_sensitive) : string;
    
    abstract public function getGeomIntersectSQL(string $field,string $value) : string;
    abstract public function getGeomContainsSQL(string $field,string $value) : string;
    abstract public function getGeomIsContainedSQL(string $field,string $value) : string;
    
    abstract public function showTables() : mixed; // Check return type
    abstract public function getFieldShowTableName() : string;
    /**
     * @return array<int,string>
     */
    abstract public function getAllTablesNamesInArray() : array;
    
    abstract public function setQuote(string $quote) : bool;
    abstract public function getQuote() : string;   
    abstract public function quoteString(string $string) : string;
    abstract public function countKeyWord() : string;

    /**
     * @return array<int,SqlField>
     */
    abstract public function showColumnInformation(?string $tableName) : array;
    /**
     * @param string $tablename
     * @param array<int,string> $arrayFields
     */
    abstract public function createIndex(string $tablename, array $arrayFields) : bool;
    
    abstract public function addFieldId(string $tableName) : bool;
    abstract public function addFieldDateCreated(string $tableName) : bool;
    abstract public function addFieldDateModified(string $tableName) : bool;
    abstract public function setFieldIdPK(string $tableName) : bool;
    abstract public function setFieldIdAutoIncrement(string $tableName) : bool;
    
    abstract public function addFields(string $tableName,ArrayList $fields) : bool;
    abstract public function dropFields(string $tableName,ArrayList $fields) : bool;
    abstract public function renameFields(string $tableName,ArrayList $fields) : bool;
    
    abstract public function createTable(string $tableName) : bool;
    abstract public function renameTable(string $tableName,string $new_TableName) : bool;
    abstract public function deleteTable(string $tableName) : bool;
    abstract public function emptyTable(string $tableName) : bool;
       
    abstract public function convertToBool(mixed $var) : string;
    
    abstract public function beginTransaction() : bool;
    abstract public function commitTransaction() : bool;
    abstract public function rollbackTransaction() : bool;
    
    abstract public function emptyDatabase() : bool;
}