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
abstract class KController
{
    private bool $doNotTest=false;
    private bool $isATest=false;
    private bool $debugParam=false;
    private ?ArrayList $listComponent=null;
    public final function __construct() 
    {
        $this->listComponent=new ArrayList();
    }
    public function init() : void
    {
        
    }
    public function after() : void
    {
        
    }    
    public function addComponent(KComponent $component) : void
    {
        $this->listComponent->add($component);
    }
    public function addString(string $string) : void
    {
        $component=new EmptyComponent();
        $component->addHTML($string);
        $this->listComponent->add($component);
    }  
    public function addStringToConsole(mixed $var) : void
    {
        $component=new EmptyComponent();
        $component->addHTML("<script>"."console.log('".addslashes("".$var)."')"."</script>");
        $this->listComponent->add($component);
    } 
    public function addStringPrint_r(mixed $var) : void
    {
        $component=new EmptyComponent();
        $component->addHTML("<div><pre>".print_r($var,true)."</pre></div>");
        $this->listComponent->add($component);
    }     
    public function drawComponents() : KComponent
    {
        $componentController= new EmptyComponent();//DivClassComponent("KController");
        /* @var $component KComponent */
        foreach($this->listComponent as $component)
        {
            //echo "in the foreach".$component->toString();
            $componentController->addComponent($component);
        }
        return $componentController;
    }
    public function doNotTest() : void
    {
        $this->doNotTest=true;
    }
    public function canTest() : bool
    {
        if($this->isATest)
        {
            $this->setDebugParam();
        }
        return !$this->doNotTest;
    }
    public function activateTestParam() : void
    {
        $this->isATest=true;
    }
    public function setDebugParam() : void
    {
        $this->debugParam=true;
    }
    public function getDebugParam() : bool
    {
        return $this->debugParam;
    }
    public function isVarPosted() : bool
    {

//        if(isset($_POST) )
//        {
            if(count($_POST))
            {
                return true;
            }
//        }
        return false;
    }
    public function __destruct() 
    {
        
    }
    abstract function execute() : bool;
}