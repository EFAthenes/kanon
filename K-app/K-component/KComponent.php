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
/**
 * Description of Component
 *
 * classe abstraite des composants utilise dans une page
 *
 * @author Mulot Louis
 */
class KComponent 
{
    protected bool $id=false;
    protected bool $class=true;
    protected bool $none=false;
    protected string $html_code="";
    protected string $javascript_code="";
    protected ?ArrayList $javascript_file=null;
    protected string $css_code="";
    protected ?ArrayList $css_file=null;
    protected string $css_properties="";
    protected string $name="";
    //private string $class_name="";
    protected ?ArrayList $list=null;
    protected bool $makeCssDiv=true;
    protected bool $visible=true;  
    protected string $comments="";
    protected string $style_code="";
    private string $endDiv="</div>";
    public bool $DEBUG=false;
    private string $abstract="";
    private string $abstract_title="";
    private string $abstract_url="";
    
    /**
     * 
     * @var array<int,string>|null
     */
    private ?array $class_names=null;
    
    /**
     * 
     * @var array<string,string>
     */
    protected array $events=[];
    //public static $EURO="&#8364;";

    //Abstract class
    protected function  __construct()
    {
        $this->list=new ArrayList();
        $this->css_file=new ArrayList();
        $this->javascript_file=new ArrayList();
    }
    public function  __destruct()
    {
    }
    public function setName(string $name) : void
    {
        $this->name=$name;
    }
    public function getName() : string
    {
        return $this->name;
    }
    public function setIdName(mixed $id_name) : void
    {
        $this->setName("".$id_name);
    }
    public function setIdAndName(mixed $id_name) : void
    {
        $this->setName("".$id_name);
        $this->setId();
    }    
    public function setClassName(string $class_name) : void
    {
        $this->class_names=[];
        $this->addClass_Name($class_name);
    }
    public function addClassName(string $class_name) : void
    {
        $this->addClass_Name($class_name);
    }
    
    /**
     * 
     * @param array<int,string>|null $class_names
     * @return self
     */
    public function setClass_names(?array $class_names) : self
    {
        if(!is_null($class_names))
        {
            $this->class_names = $class_names;
        }
        return $this;
    }
    
    public function addClass_Name(string $class_name) : self
    {
        if(!is_array($this->class_names))
        {
            $this->class_names=[];
        }
        $this->class_names[]=$class_name;
        return $this;
    }   
     
    public function getIdName() : string
    {
        return $this->getName();
    }
    public function getClassName() : string
    {
        $classname="";
        if(is_array($this->class_names))
        {
            foreach ($this->class_names as $class_name)
            {
                $classname .= " " . $class_name;
            }
        }
        return $classname;
    } 
    public function getClassNameTrimed() : string
    {
        return trim($this->getClassName());
    }
    public function isId() : bool
    {
        return $this->id;
    }
    public function isClass() : bool
    {
        return $this->class;
    }
    public function setClass() : void
    {
        $this->id=false;
        $this->class=true;
        $this->none=false;
    }
    public function setId() : void
    {
        $this->id=true;
        $this->class=false;
        $this->none=false;
    }
    public function setNone() : void
    {
        $this->id=false;
        $this->class=false; 
        $this->none=true;
    }
    public function setIdAndClass() : void
    {
        $this->id=true;
        $this->class=true;
        $this->none=false;        
    }
    public function addCssFile(string $file) : void
    {
        $this->css_file->add($file);
    }
    public function addCssListFile(ArrayList $list) : void
    {
        $this->css_file->addList($list);
    }
    public function setCssText(string $text) : void
    {
        if($text!="")
        {
        $this->css_code= '
';
	$this->css_code.=$text;
        $this->css_code.='
';
        }
    }
    public function addCssText(string $text) : void
    {
        if($text!="")
        {
            $this->css_code.=$text;
        }
    }
    public function addCssProperties(string $text) : void
    {
        $this->css_properties.="
$text
";
    }
    public function clearCssProperties() : void
    {
        $this->css_properties="";
    }
    public function getCss() : string
    {
        $css="";
        $div="";
        foreach($this->list as $component)    
        {    
            $css.=$component->getCss();
        }
        
        if($this->css_properties == "")
        {
            return $css.$this->css_code;
        }

        if($this->id)
        {
            $div="#".$this->name;
        }
        else if($this->class)
        {
            $div=".".$this->name;
        }

        if($this->makeCssDiv)
        {

            $css.=$div.'
{
'.$this->css_properties.'
}';
        }
        return $css.$this->css_code;
    }
    public function getCompleteCss() : string
    {
        $list=$this->getCssFile();
        $css="";
        if($list->getSize()==0)
        {
            return "";
        }

        foreach($list as $url)    
        {             
            $css.="<link rel=\"stylesheet\" type=\"text/css\" href=\"".$url."\" >
";
        }
        return $css."<style type=\"text/css\">".$this->getCss()."</style>";
    }
    public function getCssFile() : ArrayList
    {
        foreach($this->list as $component)    
        {    
            $this->css_file->addList($component->getCssFile());
        }
        return $this->css_file;
    }
    public function setJSFile(string $file) : void
    {
        $this->javascript_file->add($file);
    }
    public function addJSFile(string  $file) : void
    {
        $this->javascript_file->add($file);
    }
    public function addJSFileList(ArrayList $list) : void
    {
        if($list!=null && $list->getSize()>0)
        {
            $this->javascript_file->addList($list);
        }
    }
    public function setJSText(string $text) : void
    {
        $this->javascript_code= '
<script type="text/javascript">
'.$text.'
</script>';
    }
    public function addJSText(string $js) : void
    {
        $this->javascript_code.= '
<script>
'.$js.'
</script>';
    }
    public function addJSTextOnDocumentReady(string $js): void
    {
        $this->javascript_code.= '
<script>
$(document).ready(function()
{
'.$js.'
});
</script>';        
    }
    public function clearJSText() : void
    {
        $this->javascript_code="";
    }
    public function addJS(string $js) : void
    {
        if($js!="")
        {
        $this->javascript_code.= '
';
		$this->javascript_code.= $js;
        $this->javascript_code.='
';
        }
    }
    public function getJS() : string
    {
        $print="";
//        for($i=0; $i < $this->list->getSize() ;$i++)
//        {
        foreach($this->list as $component)    
        {             
            $print.=$component->getJS();
        }
        $print.=$this->javascript_code;
        return $print;
    }
    public function getJsFile() : ArrayList
    {
//        for($i=0; $i < $this->list->getSize() ;$i++)
//        {
        foreach($this->list as $component)    
        {             
            $this->javascript_file->addList($component->getJsFile());
        }
        return $this->javascript_file;
    }
    public function getCompleteJs() : string
    {
        $scriptFile="";
        foreach($this->javascript_file as $url)    
        {
//        for($i=0; $i < $this->javascript_file->getSize() ; $i++)
//        {
                $scriptFile.='
<script type="text/javascript" src="'.$url.'"></script>';
        }
        return $scriptFile.$this->getJS();
    }
    
    public function addComment(string $text) : void
    {
        $this->comments.="<!-- ".$text." -->
";
    }
    
    public function setStyleCode(string $styleCode) : void
    {
        $this->style_code=$styleCode;
    }
    public function addStyleCode(string $styleCode) : void
    {
        $this->style_code.=$styleCode;
    } 
    
    public function getStyleCode() : string
    {
        return $this->style_code;
    }    
    
    
    public function addHTML(string $html) : void
    {
        $this->html_code.=$html;
    }
    public function setHTML(string $html) : void
    {
        $this->html_code=$html;
    }
    public function makeOwnDiv(bool $status) : void
    {
        $this->makeCssDiv=$status;
    }
    
    public function getAbstract(): string
    {
        return $this->abstract;
    }

    public function getAbstract_url(): string
    {
        return $this->abstract_url;
    }

    public function setAbstract(string $abstract): void
    {
        $this->abstract = $abstract;
    }

    public function setAbstract_url(string $abstracr_url): void
    {
        $this->abstract_url = $abstracr_url;
    }
    
    public function getAbstract_title(): string
    {
        return $this->abstract_title;
    }

    public function setAbstract_title(string $abstract_title): void
    {
        $this->abstract_title = $abstract_title;
    }

    
        
    public function getListHtmlComponents() : string
    {
        $print="";
        foreach($this->javascript_file as $component)    
        {            
            $print.=$component->draw();
        }
        return $print;
    }
    public function completeDraw() : string
    {
        return $this->getCompleteJs().$this->getCompleteCss().$this->draw();
    }
    public function completeBodyDraw() : string
    {
        $text="";
        $text.='<!DOCTYPE html>            
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
		<title> completeBodyDraw </title>';
        $text.=$this->getCompleteJs().$this->getCompleteCss();
        $text.='
    </head>
    <body>';
        $text.=$this->draw();
        $text.="
    </body>
</html>";
        return $text;
    }
    public function draw() : string
    {
        if(!$this->visible)
        {
            return "";
        }
        $print="";
        $print.=$this->comments;
        $print.=$this->drawOnlyThisComponent(false);
        foreach($this->list as $component)    
        {
            $print.=$component->draw();
        }
        if(!$this->none)
        {
        $print.="
$this->endDiv
";
        }
        return $print;     
    }
    public function drawOnlyThisComponent(bool $printEndDiv=true) : string
    {
        if(!$this->visible)
        {
            return "";
        }
        $print="";
        $div="";
        $style_code="";
        if(!empty($this->style_code))
        {
            $style_code.=' style="'.$this->style_code.'" ';
        }
        
        if($this->none)
        {
            $printEndDiv=false;
        }  
        else if($this->id && $this->class)
        {
            $div="<div id=\"".$this->name."\" class=\"".$this->name." ".$this->getClassName()."\" ".$style_code." >";
        }
        else
        {
            if($this->id)
            {              
                $div="<div id=\"".$this->name."\" class=\"".$this->getClassName()."\" ".$style_code.">";
            }
            else if($this->class)
            {
                $div="<div class=\"".$this->name." ".$this->getClassName()."\" ".$style_code.">";
            }
            else
            {
                $div="<div id=\"".$this->name."\" class=\"".$this->getClassName()."\" ".$style_code.">";
                //die("Problem with the component ".$this->name." about the id or class tag !!");
            }
        }
        if($printEndDiv)
        {
            $print=$div.$this->comments.$this->html_code.$this->endDiv;
        }
        else
        {
        $print=$div.$this->html_code;
        }
        return $print;
    }
    public function addHtmlComponent(string $html) : KComponent
    {
        return $this->addComponent(new HTMLComponent($html));
    } 
    public function addVarDumpComponent(mixed $variable,bool $raw=false) : KComponent
    {
        $string=var_export($variable, true);
        if(!$raw)
        {
            $string="<pre>".$string."</pre>";
        }
        return $this->addComponent(new HTMLComponent($string));
    } 
    public function addComponent(KComponent $component) : KComponent
    {
        if($this!=$component)
        {
            $this->list->add($component);
        }
        return $this;
    }
    public function getListComponent() : ArrayList
    {
        return $this->list;
    }
    
    public function clearComponentList() : void
    {
        $this->list->clear();
    }
    public function removeComponent(KComponent $component) : bool
    {
        return $this->list->removeObject($component);
    }
    public function detectIE() : bool
    {
        if (isset($_SERVER['HTTP_USER_AGENT']) &&
            (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    public function detectIE6() : bool
    {
        $string=$_SERVER['HTTP_USER_AGENT'];
        if( isset($string) && ((strpos($string, '3M/MSIE 6')>0) || (strpos($string, '3M/IE 6.0')>0) || (strpos($string, 'Trident/4')>0)) )
        {
            return false;
        }
        else if ( isset($string) && ((strpos($string, 'MSIE 6')>0) && (strpos($string, 'MSIE 8') == false) && (strpos($string, 'MSIE 7') == false) ))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    public function detectOpera() : bool
    {
        if (isset($_SERVER['HTTP_USER_AGENT']) &&
            (strpos($_SERVER['HTTP_USER_AGENT'], 'Opera') !== false))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    public function detectChrome() : bool
    {
        if (isset($_SERVER['HTTP_USER_AGENT']) &&
            (strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome') !== false))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    public function detectEpiphany() : bool
    {
        if (isset($_SERVER['HTTP_USER_AGENT']) &&
            (strpos($_SERVER['HTTP_USER_AGENT'], 'Epiphany') !== false))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    public function setVisible(bool $status) : void
    {
        $this->visible=$status;
    }
    public function isVisible() : bool
    {
        return $this->visible;
    }

    public function goToHomePage() : void
    {
        $array= explode("/", $_SERVER['REQUEST_URI']);
        $link="";
        for($i=0; $i < (count($array)-2) ; $i++)
        {
            $link.=$array[$i]."/";
        }
        $url="http://".$_SERVER['SERVER_NAME'].$link."/";

        echo"
<script type=\"text/javascript\">
// <![CDATA[
    document.location=\"".$url."\";
// ]]>
</script>";

        exit;
    }   
    public function toString(string $delimitor ="<br />") : string
    {
        $string="";
        $string.="Component Name == ".$this->getName().$delimitor;
        $string.="ID == ".$this->isId()." // CLASS == ".$this->isClass().$delimitor;
        $string.="Visible == ".$this->isVisible().$delimitor;
        $string.="List Of Comp Size == ".$this->list->getSize().$delimitor;
        return $string;
    }
    
    public function addEvent(string $eventType,string $eventContent) : void
    {
        $this->events[]=[$eventType,$eventContent];
    }
    
    public function getStringEvents() :string
    {
        $event_string="";
        foreach($this->events as $event_array)
        {
            $event_string.=$event_array[0]."=\"".$event_array[1]."\"";
        }
        return $event_string;
    }

    public function getHtml() : string
    {
        return $this->html_code;
    }
    
    public static function testMe() : ?static
    {
        return null;
    }

    //public function editlist(array $actions) : string 

}