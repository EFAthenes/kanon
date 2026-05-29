<?php

declare(strict_types=1);

class KGeoStyle
{
    public $name = "";
    public $format = "";
    public $filename = "";
    public $version = "";
    public $worskpace_name = "";

    function __construct($worskpace_name)
    {
        $this->worskpace_name = $worskpace_name;
    }

    function getName()
    {
        return $this->name;
    }

    function getFormat()
    {
        return $this->format;
    }

    function getFilename()
    {
        return $this->filename;
    }

    function getVersion()
    {
        return $this->version;
    }

    function getWorskpace_name()
    {
        return $this->worskpace_name;
    }

    function setName($name)
    {
        $this->name = $name;
    }

    function setFormat($format)
    {
        $this->format = $format;
    }

    function setFilename($filename)
    {
        $this->filename = $filename;
    }

    function setVersion($version)
    {
        $this->version = $version;
    }

    function setWorskpace_name($worskpace_name)
    {
        $this->worskpace_name = $worskpace_name;
    }
    
    function retrieveFile() : bool
    {
        return false;
    }

    public function toString(string $delimitor = ""): string
    {
        $string = "KGeoStyle::toString()" . $delimitor;
        $vars = get_object_vars($this);
        foreach ($vars as $var_key => $var_value)
        {
            $string .= "" . $var_key . "=>" . $var_value . $delimitor;
        }
        return $string;
    }

}