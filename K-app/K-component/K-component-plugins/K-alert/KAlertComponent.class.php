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
 * Description of KAlertComponent
 *
 * @author Mulot Louis
 */
class KAlertComponent extends KComponent
{
    public static int $TYPE_SUCCESS=1;
    public static int $TYPE_INFO=2;
    public static int $TYPE_WARNING=3;
    public static int $TYPE_ERROR=4;
    public static int $TYPE_SECONDARY=5;
    public static int $TYPE_LIGHT=6;
    public static int $TYPE_DARK=7;
    
    private string $type_class="";
    private string $title_html="";
    private string $js="";
    private string $size="";
    private ?KComponent $component=null;
    private bool $close =true;
    
    final function __construct(string $title, KComponent|string $content,int $type=1,string $size="",bool $close=true)
    {
        parent::__construct();       
        $this->setNone();
        $name="alert_".KRandom::makeRandom();
        $this->setClassName($name);
        $this->type_class=$this->getClassAlert($type);
        $this->title_html=$this->getTitle($title);
        $this->js=$this->getClose($close);
        $this->size=$size;
        $this->close=$close;
        if($content instanceof KComponent)
        {
            $this->component=$content;
        }
        else
        {
            $this->component=new HTMLComponent($content);
        }   
        $this->addComponent($this->component);
    }
    
    public function draw(): string
    {
        $html='
<div class="alert'.$this->type_class.' '.$this->getClassName().' '.$this->size.' alert-dismissible" role="alert">
';
        if($this->close)
        {
            $html.='
            <button class="close" type="button" data-bs-dismiss="alert">×</button>';
        }
        $html.='
'.$this->title_html.'  
'.$this->component->draw().' 
</div>            
'.$this->js;        
        return $html;
    }

    private function getTitle(string $title) : string
    {
        if($title!="")
        {
            $title="<h4>".$title."</h4>";
        }
        return $title;
    }
    private function getClassAlert(int $type) : string
    {
        $type_class=" alert-success";
        if($type==self::$TYPE_SUCCESS)
        {
            $type_class=" alert-success";
        }
        else if($type==self::$TYPE_INFO)
        {
            $type_class=" alert-info";
        }
        else if($type==self::$TYPE_WARNING)
        {
            $type_class=" alert-warning";
        }  
        else if($type==self::$TYPE_ERROR)
        {
            $type_class=" alert-danger";
        } 
        else if($type==self::$TYPE_SECONDARY)
        {
            $type_class=" alert-secondary";
        } 
        else if($type==self::$TYPE_LIGHT)
        {
            $type_class=" alert-light";
        }
        else if($type==self::$TYPE_DARK)
        {
            $type_class=" alert-dark";
        }
        return $type_class;
    }
    private function getClose(bool $close) : string
    {
        $js='
<script>
$(document).ready(function()
{            
';
        if($close)
        {
            $js.='
    $(".'.$this->getClassNameTrimed().'").alert();             
';
            
    }
        $js.='
    $(".'.$this->getClassNameTrimed().' a").addClass("alert-link");   
});
</script>
';
        return $js;   
    }   
    
    public function setHidden() : void
    {
        $js='
$(document).ready(function()
{            
    $(".'.$this->getClassNameTrimed().'").hide();   
});
';
        $this->addJSText($js);
    }  
    
        
    #[\Override]
    public static function testMe(): static
    {
        //string $title,string $content,int $type=1,string $size="",bool $close=true
        $class=new static("Title","Success",self::$TYPE_SUCCESS);
        return $class;
    }  
}