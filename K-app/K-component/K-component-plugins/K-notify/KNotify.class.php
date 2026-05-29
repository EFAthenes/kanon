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
 * Description of KNotify
 *
 * @author Mulot Louis
 */
class KNotify extends KComponent
{
    public static string $TYPE_INFO="info";
    public static string $TYPE_SUCCESS="success";
    public static string $TYPE_WARNING="warning";
    public static string $TYPE_DANGER="danger";

    private string $the_icon="fa fa-check";
    private string $js_code='';
    
    public function __construct(string $title,string $message,string $type="info",string $icon="")
    {        
        parent::__construct();
        $this->setNone();    
        self::addJsLib();
        
        $the_type=$this->testType($type);
        
        if($icon!="")
        {
            $this->the_icon=$icon;
        }
        
        $this->js_code='
$.notify(
{
    title: "<b>'.FormComponent::inputString($title).' :</b> <br />",
    message: "'.FormComponent::inputString($message).'",
    icon: "'.FormComponent::inputString($this->the_icon).'"
},
{
    type: "'.FormComponent::inputString($the_type).'",
    delay: 5000,
});     
';
        $this->addHtml('<script>'.$this->js_code.'</script>');
    }
    
    public function getJsCode() : string
    {
        $this->setHTML('');
        return $this->js_code;
    }
    
    private function testType(string $type) : string
    { 
        if($type==self::$TYPE_INFO)
        {
            $this->the_icon="fa fa-exclamation";
            return $type;
        }
        else if($type==self::$TYPE_SUCCESS)
        {
            $this->the_icon="fa fa-check-square-o";
            return $type;
        }
        else if($type==self::$TYPE_WARNING)
        {
            $this->the_icon="fa fa-exclamation-triangle";
            return $type;
        }
        else if($type==self::$TYPE_DANGER)
        {
            $this->the_icon="fa fa-times";  
            return $type;
        }
        return self::$TYPE_INFO;
    }
    
    public static function addJsLib() : void
    {
        KApp::getInstance()->getLayout()->addJsFileToBuffer(__DIR__."/js/bootstrap-notify.min.js");
    }
}