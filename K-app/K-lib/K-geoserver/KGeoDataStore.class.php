<?php declare(strict_types=1);

class KGeoDataStore
{
    protected $schema;
    protected $database;
    protected $host;
    protected $port;
    protected $passwd;
    protected $dbtype;
    protected $namespace;
    protected $user;

    function __construct()
    {
        
    }

    function getSchema()
    {
        return $this->schema;
    }

    function getDatabase()
    {
        return $this->database;
    }

    function getHost()
    {
        return $this->host;
    }

    function getPort()
    {
        return $this->port;
    }

    function getPasswd()
    {
        return $this->passwd;
    }

    function getDbtype()
    {
        return $this->dbtype;
    }

    function getNamespace()
    {
        return $this->namespace;
    }

    function getUser()
    {
        return $this->user;
    }

    function setSchema($schema)
    {
        $this->schema = $schema;
    }

    function setDatabase($database)
    {
        $this->database = $database;
    }

    function setHost($host)
    {
        $this->host = $host;
    }

    function setPort($port)
    {
        $this->port = $port;
    }

    function setPasswd($passwd)
    {
        $this->passwd = $passwd;
    }

    function setDbtype($dbtype)
    {
        $this->dbtype = $dbtype;
    }

    function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }

    function setUser($user)
    {
        $this->user = $user;
    }
    
    function toString(string $delimitor="\n") : string
    {
        $string="";
        $vars=get_object_vars($this);
        foreach ($vars as $var_key => $var_value)
        {
            $string.="".$var_key."=>".$var_value.$delimitor;
        }
        return $string;
    }
}