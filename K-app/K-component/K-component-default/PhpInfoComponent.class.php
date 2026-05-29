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
class PhpInfoComponent extends KComponent
{
    function __construct()
    {
        parent::__construct();
        $this->setName("PHP_INFO");
        $this->setId();
        
        ob_start ();
        ob_start ();                              // Capturing
        phpinfo ();                               // phpinfo ()
        $info = trim (ob_get_clean ());           // output
        //$info=strip_tags($info,'style');
        //$info=preg_replace('/<style>(.*)</style>/i', '$1', $info);
        //$info=strip_tags($info,'style');
        
//        $strings=explode("</style>", $info);
//        $i=0;
//        $phpinfo='';
//        foreach ($strings as $string)
//        {
//            if($i!=0)
//            {
//                $phpinfo.=$string;
//            }
//            $i++;
//        }
        
        $info=$this->removeToTag("</style>",$info);
        $info=$this->removeToTag("</head>",$info);
        $info=$this->removeJustTag("body",$info);
        $info=$this->addClassToTag("table","table",$info);
        //$info=$this->removeJustTag("body",$info);
        
        $component=new DivIdComponent("system");
  
        
        //body {background-color: #fff; color: #222; font-family: sans-serif;}
        $css='

#system .pre {margin: 0; font-family: monospace;}
#system .a:link {color: #009; text-decoration: none; background-color: #fff;}
#system .a:hover {text-decoration: underline;}
#system .table {border-collapse: collapse; border: 0; width: 934px; box-shadow: 1px 2px 3px #ccc;}
#system .center {text-align: center;}
#system .center table {margin: 1em auto; text-align: left;}
#system .center th {text-align: center !important;}
#system .td, #system.th {border: 1px solid #666; font-size: 75%; vertical-align: baseline; padding: 4px 5px;}
#system .th {position: sticky; top: 0; background: inherit;}
#system .h1 {font-size: 150%;}
#system .h2 {font-size: 125%;}
#system .p {text-align: left;}
#system .e {background-color: #ccf;  font-weight: bold;}
#system .h {background-color: #99c; font-weight: bold;}
#system .v {background-color: #ddd; overflow-x: auto; word-wrap: break-word;}
#system .v i {color: #999;}
#system .img {float: right; border: 0;}
#system .hr {width: 934px; background-color: #ccc; border: 0; height: 1px;}
            
';
        //$this->addCssText($css);
        $component->addHTML($info);
        $this->addComponent($component);
        //$this->setHTML($info);
    }
    
    private function removeToTag(string $tagName,string $input) : string
    {
        $strings=explode($tagName, $input);
        $i=0;
        $output='';
        foreach ($strings as $string)
        {
            if($i!=0)
            {
                $output.=$string;
            }
            $i++;
        } 
        return $output;
    }
    
    private function removeJustTag(string $tagName,string $input) : string
    {
        $output=str_replace("<".$tagName.">","", $input);
        $output=str_replace("</".$tagName.">","", $output);
        return $output;
    }
    
    private function addClassToTag(string $className,string $tagName,string $input) : string
    {
        $output=str_replace("<".$tagName.">","<".$tagName." class=\"".$className."\" >", $input);
        return $output;
    }    
}