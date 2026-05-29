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
class PostGetComponent extends KComponent
{
    private bool $debug=true;
    function __construct(string $name="postget")
    {
        parent::__construct();
        $this->setName($name);
        $this->setClass();
        $this->debug=ParamManager::getInstance()->debug;
        if($this->debug)
        {
            $this->addHTML("<p> POST :: <br /> ".$this->print_ar($_POST)."</p>");
            $this->addHTML("<p> GET :: <br /> ".$this->print_ar($_GET)."</p>");
        }
        //$this->addHTML("<p> SESSION :: ".print_r($_SESSION,true)."</p>");
        //$this->addHTML("<p> SESSION :: ".$_SESSION['page']->getName()."</p>");
    }


    /**
     * 
     * @param array<mixed,mixed> $array
     * @param int $count
     * @return string
     */
    function print_ar(array $array,int $count=0) : string
    {
        $print="";
        $i=0;
        $tab ='';
        while($i != $count)
        {
            $i++;
            $tab .= "&nbsp;&nbsp;|&nbsp;&nbsp;";
        }
        foreach($array as $key=>$value)
        {
            if(is_array($value))
            {
                $print.= $tab."[<strong><u>".strval($key)."</u></strong>]<br />";
                $count++;                
                $print.=$this->print_ar($value, $count);
                $count--;
            }
            else
            {
                $tab2 = substr($tab, 0, -12);
                if(!empty($value))
                {
                    $print.= "$tab2~ $key : <strong>$value</strong><br />";
                }
                else
                {
                    $print.= "$tab2~ $key : <br />";
                }
            }
        }
        $count--;
        return $print;
    }
}