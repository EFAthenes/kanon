<?php
/**
 * Description of DoiContext
 *
 * @author bruno.morandiere
 */
namespace src\Helper;

class DoiContext
{
    private string $datacitePrefix="";
    private string $dataciteUrl="";
    private string $dataciteUser="";
    private string $datacitePasswd="";
    private string $dataciteBaseUrl="";

    /**
     * Constructs the context.
     */
    public function __construct()
    {
        
    }

    public function getDataciteUrl() : string
    {
        return $this->dataciteUrl;
    }

    public function getDataciteUser() : string
    {
        return $this->dataciteUser;
    }

    public function getDatacitePasswd() : string
    {
        return $this->datacitePasswd;
    }

    public function setDataciteUrl(string $dataciteUrl) : void
    {
        $this->dataciteUrl = $dataciteUrl;
    }

    public function setDataciteUser(string $dataciteUser) : void
    {
        $this->dataciteUser = $dataciteUser;
    }

    public function setDatacitePasswd(string $datacitePasswd) : void
    {
        $this->datacitePasswd = $datacitePasswd;
    }

    public function getDatacitePrefix() : string
    {
        return $this->datacitePrefix;
    }

    public function getDataciteBaseUrl() : string
    {
        return $this->dataciteBaseUrl;
    }


    public function setDatacitePrefix(string $datacitePrefix) : void
    {
        $this->datacitePrefix = $datacitePrefix;
    }

    public function setDataciteBaseUrl(string $dataciteBaseUrl) : void
    {
        $this->dataciteBaseUrl = $dataciteBaseUrl;
    }

}