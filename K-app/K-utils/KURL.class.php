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
class KURL
{
    private ArrayList $listNames;
    private ArrayList $listValues;
    private string $url="";
    private string $http_type="";
    private string $initialUrl="";
    private bool $isServerEnable=true;

    public function __construct(string $url="")
    {
        $this->initialUrl=$url;
        $checkURL="";
        
        $this->listNames= new ArrayList();
        $this->listValues= new ArrayList();    
        $this->http_type="http://";
        /** @phpstan-ignore-next-line */
        if(!isset($_SERVER)|| !isset($_SERVER['HTTP_HOST']))
        {            
            //KDebugger::getInstance()->dump("2");
            $this->isServerEnable=false;
            return;
        }
        else if($url!="")
        {
            if(substr( $url, 0, 5 ) === "https" )
            {
                $this->http_type="https://";
                $checkURL=str_replace("https://".$this->getServerParam('HTTP_HOST'),"", $url);
            }
            else if(substr( $url, 0,4  ) != "http" &&$this->getServerParam('HTTPS')=="on" )
            {
                $this->http_type="https://";
                $checkURL=str_replace("https://".$this->getServerParam('HTTP_HOST'),"", $url);
            }
            else
            {
                $this->http_type="http://";
                $checkURL=str_replace("http://".$this->getServerParam('HTTP_HOST'),"", $url);
            }
        }
        else
        {
            $checkURL=$this->getServerParam('REQUEST_URI');
            if($this->getServerParam('HTTPS')=="on"
                    || (substr( $this->getServerParam('REQUEST_URI'), 0, 5 ) === "https")
                            ||  $this->getServerParam('REQUEST_SCHEME')=="https"
                            )
            {
                $this->http_type="https://";
            }
            else
            {
                //KDebugger::getInstance()->dump("4");
                $this->http_type="http://";
            }            
        }

        $array= explode ("?",$checkURL);
        
        if(count($array)==2)
        {
            $array2=explode("&",$array[1]);

            for($i=0; $i< count($array2) ;$i++)
            {
                $array3=explode("=",$array2[$i]);
                if($array3!=null && count($array3)>1)
                {
                    $this->listNames->add($array3[0]);
                    $this->listValues->add($array3[1]);
                }
            }
            $this->url = $this->http_type.$this->getServerParam('HTTP_HOST').$array[0];
        }
        else if($url!="")
        {
            $this->url = $checkURL;
        }
        else
        {
            $this->url = $this->http_type.$this->getServerParam('HTTP_HOST').$this->getServerParam('REQUEST_URI');
        }
    }

    public function  __destruct()
    {
    }
    

    private function getServerParam(string $param) : string
    {
        if($this->isServerEnable)
        {
            if(isset($_SERVER[$param]))
            {
                return "".strval($_SERVER[$param]);
            }
        }
        return "";
    }
    
    public function getPath() : string
    {
        $path="";
        $array= explode ("/",$this->url);
        if(count($array)>1)
        {
            for($i=0; $i< count($array)-1 ;$i++)
            {
                $path.=$array[$i]."/";
            }
        }
        else
        {
            $path=$this->url;
        }
        return $path;
    }
    
    public function getLastNameInPath() : string
    {
        $path="";
        $array= explode ("/",$this->url);
        if(count($array)>1)
        {
            $path=$array[count($array)-1];
        }
        else
        {
            $path=$this->url;
        }
        return $path;        
    }

    public function removeAllArgs() : void
    { 
        if(!empty($this->url))
        {
            $array=explode("?",$this->url);
            $this->url=$array[0];
        } 
        $this->listNames=new ArrayList();
        $this->listValues=new ArrayList();
    }
    
    public function getCountOfArgs() : int
    {
        if($this->listNames!=NULL)
        {
            return $this->listNames->getSize();
        }
        return 0;
    }

    /**
     * 
     * @return array<mixed,mixed>
     */
    public function getArgListNames() : array
    {
        return $this->listNames->getArray();
    }
    /**
     * 
     * @return array<mixed,mixed>
     */   
    public function getArgListValues() : array
    {
        return $this->listValues->getArray();
    }    
    
    /**
     * 
     * @param array <mixed,mixed> $listNames
     * @param array <mixed,mixed> $listValues
     * @return bool
     */
    public function replaceArrays(array $listNames,array $listValues) : bool
    {
        if(count($listNames) && count($listValues) && count($listNames)==count($listValues) )
        {
            $this->listNames->replaceArray($listNames);
            $this->listValues->replaceArray($listValues);
            return true;
        }
        return false;
    }

    public function add(mixed $name,mixed $value) : void
    {
        $name="".strval($name);
        $value="".strval($value);        
        $this->listNames->add(urlencode($name));
        $this->listValues->add(urlencode($value));
    }
    
    public function addNoEncode(mixed $name,mixed $value) : void
    {
        $name="".strval($name);
        $value="".strval($value);
        $this->listNames->add(($name));
        $this->listValues->add(($value));
    }
    /**
     * 
     * @param mixed $name
     * @param array<mixed,mixed> $array
     * @return void
     */
    public function addArray(mixed $name,array $array) : void
    {
        $name="".strval($name);
        
        if(empty(array_values($array))) 
        {
            $this->listNames->add(urlencode($name)."[]");
            $this->listValues->add(urlencode(""));    
        }
        else 
        {
            foreach($array as $key=> $value)
            {
                $this->listNames->add(urlencode($name)."[]");
                $this->listValues->add(urlencode($value));           
            }
        }
    }    
    

    public function addOrReplace(mixed $name,mixed $value,bool $encode=true) : void
    {
        $name="".strval($name);
        $value="".strval($value);
        $status=false;
        for( $i=0; $i<$this->listNames->getSize(); $i++ )
        {
            if($encode)
            {
                if($this->listNames->get($i)==urlencode($name))
                {  
                    $this->listValues->replace($i,urlencode($value)); 
                    $status=true;
                    break;
                }              
            }
            else
            {
                if($this->listNames->get($i)==($name))
                {  
                    $this->listValues->replace($i,($value)); 
                    $status=true;
                    break;                    
                }                   
            }
        }
        if(!$status)
        {
            //echo "|NEW(".$name.")=>".$value."|<br />";
            $this->add($name,$value);
        }
    }
    
    public function addOrReplaceNoEncode(mixed $name,mixed $value) : void
    {
        $name="".strval($name);
        $value="".strval($value);        
        $status=false;
        for( $i=0; $i<$this->listNames->getSize(); $i++ )
        {
            if($this->listNames->get($i)==$name)
            {
                //echo "|OLD(".$name.")=>".$value."|<br />";
                $this->listValues->replace($i,$value);
                $status=true;
                break;
            }
        }

        if(!$status)
        {
            //echo "|NEW(".$name.")=>".$value."|<br />";
            $this->addNoEncode($name,$value);
        }
    }    

    public function addOrReplaceIfFound(mixed $name,mixed $value1, mixed$value2) : void
    {
        $name="".strval($name);
        $value1="".strval($value1); 
        $value2="".strval($value2);        
        $status=false;
        for( $i=0; $i<$this->listNames->getSize(); $i++ )
        {
            if($this->listNames->get($i)==$name)
            {
                if($this->listValues->get($i)==$value2)
                {
                    $this->listValues->replace($i,$value1);
                }
                else
                {
                    $this->listValues->replace($i,$value2);
                }
                $status=true;
                break;
            }
        }
        if(!$status)
        {
            $this->add($name,$value1);
        }
    }

    public function printURLWithHost() : string
    {
        return $this->http_type.$this->getServerParam('HTTP_HOST').$this->printURL(); 
    }
    
    public function printURLWithHostWithoutAmp() : string
    {
        return $this->http_type.$this->getServerParam('HTTP_HOST').$this->printURLWithoutAmp(); 
    }    
    
    public function printJustURL() : string
    {
        return $this->url;
    }
    
    public function printURL() : string
    {
        $url=$this->url;
        for($i=0; $i < $this->listNames->getSize() ; $i++)
        {
            if(!string_contains("?",$url))
            {
                $url.="?".$this->listNames->get($i)."=".$this->listValues->get($i);
            }
            else
            {
                $url.="&amp;".$this->listNames->get($i)."=".$this->listValues->get($i);
            }
        }
        return $url;
    }
    
    public function printURLWithoutAmp() : string
    {
        $url=$this->url;
        for($i=0; $i < $this->listNames->getSize() ; $i++)
        {
            if(!string_contains("?",$url))
            {
                $url.="?".$this->listNames->get($i)."=".$this->listValues->get($i);
            }
            else
            {
                $url.="&".$this->listNames->get($i)."=".$this->listValues->get($i);
            }
        }
        return $url;
    }  
    
    public function getArgsInString() : string
    {
        $url="";
        for($i=0; $i < $this->listNames->getSize() ; $i++)
        {
            if(!string_contains("?",$url))
            {
                $url.="?".$this->listNames->get($i)."=".$this->listValues->get($i);
            }
            else
            {
                $url.="&".$this->listNames->get($i)."=".$this->listValues->get($i);
            }
        }
        return $url;
    }

    public function printAdd(mixed $name,mixed $value) : string
    {
        $name="".strval($name);
        $value="".strval($value);          
        $this->add($name, $value);
        return $this->printURL();
    } 
    public function printAddReplace(mixed $name,mixed $value) : string
    {
        $name="".strval($name);
        $value="".strval($value);         
        $this->addOrReplace($name, $value);
        $html=$this->printURL();
        //$this->removeLastAdd();
        return $html;
    } 
    public function printAddReplaceWithoutAmp(mixed $name, mixed $value) : string
    {
        $name="".strval($name);
        $value="".strval($value);         
        $this->addOrReplace($name, $value);
        $html=$this->printURLWithoutAmp();
//        $this->removeLastAdd();
        return $html;  
    }
    
    public function printAddReplaceWithoutAmpNoEncode(mixed $name, mixed $value) : string
    {
        $name="".strval($name);
        $value="".strval($value);         
        $this->addOrReplaceNoEncode($name, $value);
        $html=$this->printURLWithoutAmp();
        return $html;
    }   
    
    public function removeLastAdd() : void
    {
        $this->listNames->remove($this->listNames->getSize()-1);
        $this->listValues->remove($this->listValues->getSize()-1);       
    }
    /**
     * 
     * @param array<int,mixed> $arrayOfArgs
     * @return void
     */
    public function removeArgs(array $arrayOfArgs) : void
    {
        foreach ($arrayOfArgs as $arg)
        {
            if(is_string($arg))
            {
                $this->removeArg($arg);
            }
        }
        //echo $this->listName->getSize()." removeArg<br />";
    }    
    public function removeArg(mixed $name) : KURL
    {
        $name="".strval($name);
        for( $i=0; $i<$this->listNames->getSize(); $i++ )
        {
            if($this->listNames->get($i)==$name)
            {
                $this->listNames->remove($i);
                $this->listValues->remove($i);
                return $this;
            }
        }        
        return $this;
    }
    public function getArgValue(mixed $name) :?string
    {
        $name="".strval($name);
        for( $i=0; $i<$this->listNames->getSize(); $i++ )
        {
            if($this->listNames->get($i)==$name)
            {
                return $this->listValues->get($i);
            }
        }
        return null;
    }
    
    public function toWebString() : string
    {
        return $this->toString("<br />");
    }    
    
    public function toString(string $delimiter="\n") : string
    {
        $string="";
        $string.="URL = ".$this->url.$delimiter;
        $string.=$this->listNames->toString($delimiter);
        $string.=$this->listValues->toString($delimiter);
        return $string;
    }
    public function exists() : bool
    {
        $exists=true;
        //$file_headers = @get_headers($this->printURLWithoutAmp());
        $headers = @get_headers($this->printURLWithoutAmp());
        if(is_array($headers)&&count($headers))
        {
            //echo $headers[0];
            if(strpos($headers[0],'200')===false)
            //if($file_headers[0] == 'HTTP/1.1 404 Not Found')
            {
                $exists = false;
            }
        }
        return $exists;
    }
    public function getPathWithoutIndexDotPhp() : string
    {
        $path="";
        $array= explode("/",$this->url);
        if(count($array)>1)
        {
            for($i=0; $i< count($array)-1 ;$i++)
            {
                $path.=$array[$i]."/";
            }
        }
        else
        {
            $path=$this->url;
        }

        $array=explode("index.php", $path);
        $path=$array[0];
        
        return $path;
    }  
    
    public function mergePartFromInitialUrl(string $url_part) : string
    {
        $oldArray =  explode('/',$this->initialUrl);
        $newArray = explode('/',$url_part);
        $result =  array_unique(array_merge($oldArray, $newArray));
        $resultUrl=implode("/", $result);
        return $resultUrl;
    }
}