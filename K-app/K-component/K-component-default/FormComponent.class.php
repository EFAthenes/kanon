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
class FormComponent extends KComponent
{
    private string $action="";
    //private $type="";
    private ?ArrayList $listParams=null;
    private string $autoComplete="";
    
    /* 
    UTF-8 - Character encoding for Unicode
    ISO-8859-1 - Character encoding for the Latin alphabet
    */ 
//    private $accept_charset="UTF-8";
//    private $autocomplete="";
//    private $enctype="";
    private string $method="";
//    private $nameForm="";
//    private $novalidate="";
    private string $target="";  
    private string $id_form="";
    private string $onSubmit = "";
    //private static $STRING_SUBMITED_POST="string_submited_post";
    
    public static string $TARGET_BLANK="_blank";
    public static string $TARGET_SELF="_self";
    public static string $TARGET_PARENT="_parent";
    public static string $TARGET_TOP="_top";
    
    public static string $AUTOCOMPLETE_ON="on";
    public static string $AUTOCOMPLETE_OFF="on";
    
    public static string $ENCTYPE_APPLICATION="application/x-www-form-urlencoded";
    public static string $ENCTYPE_MULTIPART="multipart/form-data";
    public static string $ENCTYPE_TEXT_PLAIN="text/plain";
    
    public static string $METHOD_GET="get";
    public static string $METHOD_POST="post";

//    private static string $NOVALIDATE_STRING="novalidate";    
//    
    
    function __construct(string $actionURL,string $id_form="")
    {        
        parent::__construct();
        $this->setNone(); 
        $this->id_form=$id_form;
        $this->action=$actionURL;
        $this->method=self::$METHOD_POST;
        $this->target=self::$TARGET_SELF;
    }   
    public function setGet() : void
    {
        $this->method=self::$METHOD_GET;
    }
    public function setPost() : void
    {
        $this->method=self::$METHOD_POST;
    }
    
    public function getTarget(): string
    {
        return $this->target;
    }
   
    function getId_form() : string
    {
        return $this->id_form;
    }

    function setId_form(string $id_form) : void
    {
        $this->id_form=$id_form;
    }
    
    public function getAction() : string
    {
        return $this->action;
    }

    public function setAction(string $action) : void
    {
        $this->action = $action;
    }
     
    public function addHiddenParameters(string $field,mixed $value) : void
    {
        if($this->listParams==null)
        {
            $this->listParams=new ArrayList();
        }
        $this->listParams->add('<input type="hidden" id="'.$field.'" name="'.$field.'" value="'.strval($value).'" >');
    }
    
    public function draw() : string
    {      
        $hidden="";
        if($this->listParams!=null)
        {
            foreach($this->listParams as $hiddenParams)
            {
                $hidden.=$hiddenParams;
            }
        }
        $auto_string="";
        if($this->autoComplete==self::$AUTOCOMPLETE_OFF)
        {
            $auto_string='autocomplete="off"';
        }
        
        $on_submit="";
        if($this->onSubmit!="")
        {
            $on_submit=' onsubmit="'.$this->onSubmit.'" ';
        }
        
        $id_form="";
        if($this->id_form!="")
        {
            $id_form=' id="'.$this->id_form.'" ';
        }
        
        $kurl=new KURL($this->action);
        $kurl->addOrReplace(HistoryPage::$STRING_SUBMITED_POST,"1");
        $the_url=$kurl->printURLWithoutAmp();
        
        return '<form method="'.$this->method.'" '.$id_form.' '.$auto_string.' '.$on_submit.'  action="'.$the_url.'" class="'.$this->getClassName().'">'.
        parent::draw().
        $hidden.'
        </form>
';
    }
    
    public function disableEnterOnInput() : void
    {
        $js='
$(document).on("keydown", "form", function(event) 
{ 
    return event.key != "Enter";
});             
';
        $this->addJSText($js);
    }
    
    
    public function setAutoCompleteOn() : void
    {
        $this->autoComplete=self::$AUTOCOMPLETE_ON;
    }  
    public function setAutoCompleteOff() : void
    {
        $this->autoComplete=self::$AUTOCOMPLETE_OFF;
    }    
    
//    public function setAutoComplete($value)
//    {
//        $this->autoComplete=$value;
//    }
    
    public static function inputString(mixed $string) : string
    {
        if(is_null($string))
        {
            return "";
        }
        else
        {
            //return str_replace('"', '&quot;', $string);
            return htmlspecialchars("".$string, ENT_QUOTES, 'UTF-8');
        }
    }

    public static function textareaString(string $string) : string
    {
        return htmlentities($string);
    }
    
    public function setSubmit(string $string) : void
    {
        $this->onSubmit = $string;
    }
    
    public static function isAlreadyPost() : bool
    {
        $posted=0;
        if(KInput::checkInputGet(HistoryPage::$STRING_SUBMITED_POST, KInput::$VARIABLE_INT, $posted))
        {
            if($posted==1)
            {
                return true;
            }
        }
        return false;
    }
    
    public static function getSubmitedGetArg() : string
    {
        return HistoryPage::$STRING_SUBMITED_POST;
    }
}