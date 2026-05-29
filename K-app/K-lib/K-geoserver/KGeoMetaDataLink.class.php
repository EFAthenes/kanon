<?php declare(strict_types=1);

class KGeoMetaDataLink
{
    protected $type ="";
    protected $metaDataType="";
    protected $content="";
    
    function getType()
    {
        return $this->type;
    }

    function getMetaDataType()
    {
        return $this->metaDataType;
    }

    function getContent()
    {
        return $this->content;
    }

    function setType($type)
    {
        $this->type = $type;
    }

    function setMetaDataType($metaDataType)
    {
        $this->metaDataType = $metaDataType;
    }

    function setContent($content)
    {
        $this->content = $content;
    }
    
    function toString(string $delimitor = "\n"): string
    {
        $string = "";
        $vars = get_object_vars($this);
        foreach ($vars as $var_key => $var_value)
        {
            $string .= "" . $var_key . "=>" . $var_value . $delimitor;            
        }
        return $string;
    }

}
