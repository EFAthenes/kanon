<?php declare(strict_types=1);

class KGeoKeyWord
{
    protected $value= "";
    protected $lang= "";
    protected $vocabulary= "";
    
    function __construct()
    {
        
    }

    // test_mot_clé\@language=fr\;\@vocabulary=test_vocabulary\;
    public function initByString(?string $fromXML) : bool
    {
        if(!is_null($fromXML))
        {
            $array=explode("\@", $fromXML);
            if(!is_null($array) && is_array($array))
            {
                $i=0;
                foreach ($array as $value)
                {
                    if($i==0)
                    {
                        $this->value=$array[$i];
                    }
                    elseif ($i==1)
                    {
                        $this->lang=str_replace("\;", "",$array[$i]);
                    }
                    elseif ($i==2)
                    {
                        $this->vocabulary=str_replace("\;", "",$array[$i]);
                    }                    
                    $i++;    
                }
                return true;
            }
        }
        return false;
    }
    
    public function makeStringForXML() :?string
    {
        $string=null;
        if(!is_null($this->value))
        {
            $string.=''.$this->value;
            if(!is_null($this->lang))
            {
                $string.='\@'.$this->lang.'\;';
            }
            if(!is_null($this->vocabulary))
            {
                 $string.='\@'.$this->vocabulary.'\;';
            }            
        }
        return $string;
    }
    
    function getValue()
    {
        return $this->value;
    }

    function getLang()
    {
        return $this->lang;
    }

    function getVocabulary()
    {
        return $this->vocabulary;
    }

    function setValue($value)
    {
        $this->value = $value;
    }

    function setLang($lang)
    {
        $this->lang = $lang;
    }

    function setVocabulary($vocabulary)
    {
        $this->vocabulary = $vocabulary;
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
