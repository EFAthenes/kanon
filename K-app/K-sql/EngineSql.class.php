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
class EngineSql extends Sql
{
    protected mixed $server;
    protected mixed $result=null;
    protected string $error = "";  
    protected string $query = "";
    private string $host = "localhost";
    private string $user = "root";
    private string $bdd = "bdd";
    private string $passwd = "";
    private string $error_email = "error@mail.com";
    private bool $debug = true;
    private string $engine_type="";
    private string $schema="";
    private string $quote="";
    private string $default_insert_id="";
    protected string $collation="";
    protected string $charset="";
    
    public function __construct()
    {

    }
    
    public function __destruct()
    {

    }
    
    function getServer():string
    {
        return $this->server;
    }

    function getHost():string
    {
        return $this->host;
    }

    function getUser():string
    {
        return $this->user;
    }

    function getBdd():string
    {
        return $this->bdd;
    }

    function getPasswd():string
    {
        return $this->passwd;
    }

    function getError():string
    {
        return $this->error;
    }

    function getError_email():string
    {
        return $this->error_email;
    }

    function getDebug():bool
    {
        return $this->debug;
    }

    function getQuery():string
    {
        return $this->query;
    }

    function getEngine_type():string
    {
        return $this->engine_type;
    }

    function setServer(string $server):void
    {
        $this->server=$server;
    }

    function setHost(string $host):void
    {
        $this->host=$host;
    }

    function setUser(string $user):void
    {
        $this->user=$user;
    }

    function setBdd(string $bdd):void
    {
        $this->bdd=$bdd;
    }

    function setPasswd(string $passwd):void
    {
        $this->passwd=$passwd;
    }

    function setError(string $error):void
    {
        $this->error=$error;
    }

    function setError_email(string $error_email):void
    {
        $this->error_email=$error_email;
    }

    function setDebug(bool $debug):void
    {
        $this->debug=$debug;
    }

    function setQuery(string $query):void
    {
        $this->query=$query;
    }
    
    function setEngine_type(string $engine_type):void
    {
        $this->engine_type=$engine_type;
    }
    
    function getSchema():string
    {
        return $this->schema;
    }

    function setSchema(string $schema):void
    {
        $this->schema=$schema;
    }
    
    public function getQuote() : string
    {
        return $this->quote;
    }
    
    function setQuote(?string $quote) : bool
    {
        $status=false;
        if(!is_null($quote) &&strlen($quote)>0)
        {
            $this->quote=$quote;
            $status=true;
        }
        return $status;
    } 
    
    function quoteString(string $string) : string
    {
        return $this->quote.$string.$this->quote;
    }
    
    function doublequoteString(string $string) : string
    {
        return '"'.addcslashes($string,'"').'"';
    }    
    
    
    function getDefault_insert_id():string
    {
        return $this->default_insert_id;
    }

    function setDefault_insert_id(string $default_insert_id):void
    {
        $this->default_insert_id=$default_insert_id;
    } 
    
    public function geoFunctionToString(string $field) : string
    {
        return $field;
    }
    
    public function stringFunctionToGeo(string $value) : string
    {
        return $value;
    }  
    
    public function getStringCharset() : string
    {
        return $this->charset;
    }
    public function getStringCollation() : string
    {
        return $this->collation;
    }
    public function setStringCharset(string $charset) : bool
    {
        $this->charset=$charset;
        return true;
    }
    public function setStringCollation(string $collation) : bool
    {
        $this->collation=$collation;
        return true;
    }    
    
}